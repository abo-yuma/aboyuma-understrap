<?php
function aboyuma_gallery_edit_page() {
    global $wpdb;
    $gallery_id = intval($_GET['gallery_id'] ?? 0);
    $gallery_table = $wpdb->prefix . 'aboyuma_gallery';
    $gallery_image_table = $wpdb->prefix . 'aboyuma_gallery_image';

    // Get gallery info
    $gallery = $wpdb->get_row($wpdb->prepare("SELECT * FROM $gallery_table WHERE id = %d", $gallery_id));
    if (!$gallery) {
        echo '<div class="notice notice-error"><p>Gallery not found.</p></div>';
        return;
    }

    $upload_dir = wp_upload_dir();
    $base_dir = trailingslashit($upload_dir['basedir']) . 'aboyuma-gallery/' . $gallery_id . '/';

    // Handle form submit
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Nonce check for security
        if (
            !isset($_POST['aboyuma_edit_gallery_nonce']) ||
            !wp_verify_nonce($_POST['aboyuma_edit_gallery_nonce'], 'aboyuma_edit_gallery_action')
        ) {
            wp_die('Security check failed. Please try again.');
        }

        // echo '<pre>';
        // echo var_dump($_FILES['gallery_images']);
        // echo var_dump($_POST['image_order_json']);
        // echo '</pre>';
        // exit;

        // Update gallery description
        $gallery_description = sanitize_textarea_field($_POST['gallery_description'] ?? '');

        $wpdb->update(
            $gallery_table,
            ['description' => $gallery_description],
            ['id' => $gallery_id],
            ['%s'], // format for description (string)
            ['%d']  // format for id (integer)
        );


        // 1. Delete checked images
        if (!empty($_POST['deleted_image_ids'])) {
            $deleted_ids = array_map('intval', $_POST['deleted_image_ids']);
            foreach ($deleted_ids as $id) {
                $image_path = $wpdb->get_var($wpdb->prepare("SELECT image_path 
                FROM $gallery_image_table
                WHERE id = %d", $id));
                if ($image_path) {
                    $full_path = wp_normalize_path($upload_dir['basedir'] . '/' . $image_path);
                    // ✅ SAFETY CHECK BEFORE DELETING FILE
                    // Ensure the file path is inside the WordPress uploads directory to prevent accidental or malicious deletion
                    // 1. wp_normalize_path() makes paths consistent across OS (Windows vs Linux slashes)
                    // 2. strpos() === 0 ensures the file starts with uploads base dir (no directory traversal outside allowed folder)
                    // 3. file_exists() confirms the file is actually there before unlink()
                    // This prevents dangerous cases like deleting ../../wp-config.php if someone injects a path into the DB
                    if (strpos($full_path, wp_normalize_path($upload_dir['basedir'])) === 0 && file_exists($full_path)) {
                        unlink($full_path);
                    }
                }
                $wpdb->delete($gallery_image_table, ['id' => $id]);
            }
        }

        // 2. Create folder if not exist
        if (!file_exists($base_dir)) {
            wp_mkdir_p($base_dir);
        }

        // 3. Save new uploaded images, map tempIndex => new DB ID
        $new_images_map = [];
        $captions_temp = $_POST['gallery_image_captions'] ?? [];
        $cover_temp_index = $_POST['cover_image_temp_index'] ?? '';
        if (
            !empty($_FILES['gallery_images']['name'][0])
            && isset($_POST['gallery_image_indexes']) && is_array($_POST['gallery_image_indexes'])
        ) {
            $indexes = $_POST['gallery_image_indexes'];
            $filenames = $_POST['gallery_image_filenames'];
            for ($i = 0; $i < count($indexes); $i++) {
                $tempIndex = $indexes[$i];
                $filename = sanitize_file_name($filenames[$i]);
                $tmp_name = $_FILES['gallery_images']['tmp_name'][$i];
                $new_filename = wp_unique_filename($base_dir, $filename);
                $new_filepath = $base_dir . $new_filename;

                if (move_uploaded_file($tmp_name, $new_filepath)) {

                    // === FIX IMAGE ORIENTATION ISSUE ===
                    if (function_exists('exif_read_data')) {
                        $exif = @exif_read_data($new_filepath);
                        if (!empty($exif['Orientation'])) {
                            $image = imagecreatefromstring(file_get_contents($new_filepath));
                            switch ($exif['Orientation']) {
                                case 3:
                                    $image = imagerotate($image, 180, 0);
                                    break;
                                case 6:
                                    $image = imagerotate($image, -90, 0);
                                    break;
                                case 8:
                                    $image = imagerotate($image, 90, 0);
                                    break;
                            }
                            // Save back to file (preserving type)
                            imagejpeg($image, $new_filepath, 100);
                            imagedestroy($image);
                        }
                    }
                    // === END FIX ===

                    /**
                     * Resize the image after upload
                     * - Change $max_width and $max_height to match WordPress recommended size or your own
                     */
                    $max_width  = 1920; // example: Full HD max width
                    $max_height = 1080; // example: Full HD max height

                    $image_editor = wp_get_image_editor($new_filepath);
                    if (!is_wp_error($image_editor)) {
                        $image_editor->set_quality(82); // WordPress default is 82, but setting it explicitly gives you control.
                        $image_editor->resize($max_width, $max_height, false); // false = keep aspect ratio
                        $image_editor->save($new_filepath);
                    }

                    $relative_path = 'aboyuma-gallery/' . $gallery_id . '/' . $new_filename;
                    $caption = sanitize_text_field($captions_temp[$i] ?? '');

                    $wpdb->insert(
                        $gallery_image_table,
                        [
                            'gallery_id'  => $gallery_id,
                            'image_path'  => $relative_path,
                            'image_order' => 0,
                            'caption'     => $caption
                        ],
                        [
                            '%d', // gallery_id
                            '%s', // image_path
                            '%d', // image_order
                            '%s'  // caption
                        ]
                    );

                    $inserted_image_id = $wpdb->insert_id;
                    $new_images_map[$tempIndex] = $inserted_image_id;

                    // Update cover image for the gallery
                    if ($tempIndex == $cover_temp_index) {
                        $wpdb->update(
                            $gallery_table,
                            ['cover_image_id' => $inserted_image_id],
                            ['id' => $gallery_id],
                            ['%d'], // format for cover_image_id
                            ['%d']  // format for id
                        );
                    }
                }
            }
        }

        // 4. Update captions and cover for existing images
        $cover_image_id = isset($_POST['cover_image_id']) ? intval($_POST['cover_image_id']) : 0;
        $captions = $_POST['captions'] ?? [];

        foreach ($captions as $img_id => $caption) {
            $img_id = intval($img_id);
            $wpdb->update(
                $gallery_image_table,
                ['caption' => sanitize_text_field($caption)],
                ['id' => $img_id],
                ['%s'], // caption
                ['%d']  // id
            );
        }

        // Update cover image
        if ($cover_image_id > 0) {
            $wpdb->update(
                $gallery_table,
                ['cover_image_id' => $cover_image_id],
                ['id' => $gallery_id],
                ['%d'], // cover_image_id
                ['%d']  // id
            );
        }

        // 5. Update image_order for existing and new images in the order given
        if (!empty($_POST['image_order_json'])) {
            $image_order = json_decode(stripslashes($_POST['image_order_json']), true);
            if (is_array($image_order)) {
                $order = 0;
                foreach ($image_order as $imgRef) {
                    if (strpos($imgRef, 'existing-') === 0) {
                        $imgId = intval(str_replace('existing-', '', $imgRef));
                        $wpdb->update($gallery_image_table, ['image_order' => $order], ['id' => $imgId]);
                    } elseif (strpos($imgRef, 'new-') === 0) {
                        $tempIndex = str_replace('new-', '', $imgRef);
                        if (isset($new_images_map[$tempIndex])) {
                            $wpdb->update($gallery_image_table, ['image_order' => $order], ['id' => $new_images_map[$tempIndex]]);
                        }
                    }
                    $order++;
                }
            }
        }

        echo '<script>window.location.href = "' . admin_url("admin.php?page=aboyuma-gallery-edit&gallery_id=$gallery_id") . '";</script>';
        exit;
    }

    // Fetch all images ordered by image_order
    $galleryImages = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $gallery_image_table 
        WHERE gallery_id = %d 
        ORDER BY image_order ASC",
        $gallery_id
    ));
?>
    <div class="wrap">
        <h1>Edit Gallery: <?= esc_html($gallery->title); ?></h1>
        <form id="aboyuma-edit-gallery-form" enctype="multipart/form-data">
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="gallery_images">Gallery Description</label></th>
                    <td>
                        <textarea name="gallery_description" id="gallery_description" rows="4" cols="50"><?php echo esc_textarea($gallery->description ?? ''); ?></textarea>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="gallery_images">Gallery Images</label></th>
                    <td>
                        <input type="file" id="gallery-multi-upload" accept="image/*" multiple>
                        <ul id="sortable-gallery" class="sortable-gallery-list" style="margin-top:10px;">
                            <?php foreach ($galleryImages as $galleryImage):
                                $image_url = esc_url(trailingslashit($upload_dir['baseurl']) . $galleryImage->image_path);
                            ?>
                                <li class="image-preview-container existing-image" data-id="<?= $galleryImage->id; ?>">
                                    <img src="<?= $image_url; ?>" width="150" alt="">
                                    <input type="hidden" name="existing_image_ids[]" value="<?= $galleryImage->id; ?>">
                                    <label><input type="checkbox" name="deleted_image_ids[]" value="<?= $galleryImage->id; ?>"> Delete</label><br>
                                    <label><input type="radio" name="cover_image_id" value="<?= $galleryImage->id; ?>" <?= ($gallery->cover_image_id == $galleryImage->id) ? 'checked' : ''; ?>> Cover</label><br>
                                    <input type="text" name="captions[<?= $galleryImage->id; ?>]" value="<?= esc_attr($galleryImage->caption); ?>" placeholder="Caption" style="width: 100%;">
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <input type="hidden" name="image_order_json" id="image_order_json" value="">
                        <small>** Drag to reorder images</small>
                    </td>
                </tr>
            </table>
            <?php wp_nonce_field('aboyuma_edit_gallery_action', 'aboyuma_edit_gallery_nonce'); ?>
            <?php submit_button('Save Changes', 'primary', '', false); ?>
            <button type="button"
                onclick="window.location.href='<?php echo admin_url('admin.php?page=aboyuma-gallery'); ?>'"
                class="button button-secondary">
                Cancel
            </button>

        </form>
    </div>

    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script>
        jQuery(function($) {
            let imageIndex = 1000;
            const accumulatedFiles = [];

            $("#sortable-gallery").sortable({
                update: updateImageOrder
            });

            function updateImageOrder() {
                const order = [];
                $('#sortable-gallery .image-preview-container').each(function() {
                    const existingId = $(this).data('id');
                    const tempIndex = $(this).data('temp-index');
                    if (existingId !== undefined) {
                        order.push('existing-' + existingId);
                    } else if (tempIndex !== undefined) {
                        order.push('new-' + tempIndex);
                    }
                });
                $('#image_order_json').val(JSON.stringify(order));
            }

            updateImageOrder();

            $('#gallery-multi-upload').on('change', function(e) {
                const files = e.target.files;
                if (!files.length) return;

                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    const reader = new FileReader();
                    const thisIndex = imageIndex;

                    accumulatedFiles.push({
                        file: file,
                        index: thisIndex
                    });

                    reader.onload = function(event) {
                        const $li = $(`
                        <li class="image-preview-container" data-temp-index="${thisIndex}">
                            <img src="${event.target.result}" width="150" alt="">
                            <input type="hidden" name="gallery_image_indexes[]" value="${thisIndex}">
                            <input type="hidden" name="gallery_image_filenames[]" value="${file.name}">
                            <input type="text" name="gallery_image_captions[]" placeholder="Caption" style="width: 100%;">
                            <label><input type="radio" name="cover_image_temp_index" value="${thisIndex}"> Cover</label><br>
                            <span class="remove-image-button" style="color:red;cursor:pointer;">Remove</span>
                        </li>
                        `);
                        $('#sortable-gallery').append($li);
                        updateImageOrder();
                    };

                    reader.readAsDataURL(file);
                    imageIndex++;
                }

                // Clear input so re-choosing the same file triggers change
                this.value = '';
            });

            $(document).on('click', '.remove-image-button', function() {
                const container = $(this).closest('.image-preview-container');
                const tempIndex = container.data('temp-index');

                if (tempIndex !== undefined) {
                    const indexToRemove = accumulatedFiles.findIndex(f => f.index === tempIndex);
                    if (indexToRemove !== -1) {
                        accumulatedFiles.splice(indexToRemove, 1);
                    }
                }

                container.remove();
                updateImageOrder();
            });

            $('#aboyuma-edit-gallery-form').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                accumulatedFiles.forEach(({
                    file,
                    index
                }) => {
                    formData.append('gallery_images[]', file);
                });

                fetch(window.location.href, {
                        method: 'POST',
                        body: formData,
                        credentials: 'same-origin',
                    })
                    .then(response => response.text())
                    .then(html => {
                        document.open();
                        document.write(html);
                        document.close();
                    })
                    .catch(error => {
                        alert('Error uploading: ' + error.message);
                    });
            });
        });
    </script>


    <style>
        .sortable-gallery-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .image-preview-container {
            width: 150px;
            border: 1px solid #ccc;
            background: #fafafa;
            padding: 5px;
            text-align: center;
            cursor: grab;
            position: relative;
        }

        .image-preview-container img {
            max-width: 100%;
            height: auto;
            display: block;
        }

        .remove-image-button {
            margin-top: 5px;
            font-size: 13px;
        }
    </style>
<?php
}
