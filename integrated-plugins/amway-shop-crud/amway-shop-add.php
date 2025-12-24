<?php
function aboyuma_amway_shop_add() {
    global $wpdb;
    $table = $wpdb->prefix . 'aboyuma_amway_shop';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Verify nonce
        if (
            !isset($_POST['aboyuma_shop_nonce']) ||
            !wp_verify_nonce($_POST['aboyuma_shop_nonce'], 'aboyuma_add_shop_action')
        ) {
            wp_die('Security check failed.');
        }

        /**
         * REQUIRED FIELDS (all fields)
         */
        $required_fields = [
            'shop_code'        => 'Shop Code',
            'shop_name'        => 'Shop Name',
            'shop_address'     => 'Address',
            'shop_phone'       => 'Phone',
            'shop_description' => 'Description',
            'shop_slug'        => 'Slug',
            'shop_map_url'     => 'Google Map URL'
        ];

        $errors = [];

        // Check normal fields
        foreach ($required_fields as $field => $label) {
            if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
                $errors[] = "{$label} is required.";
            }
        }

        /**
         * GOOGLE MAP URL VALIDATION
         * (only validate if field is not empty)
         */
        if (!empty($_POST['shop_map_url'])) {

            $map_url = esc_url_raw(trim($_POST['shop_map_url']));

            $google_map_pattern = '/^https?:\/\/(www\.)?(google\.com\/maps|maps\.google\.com|goo\.gl\/maps|maps\.app\.goo\.gl)\//i';

            if (!preg_match($google_map_pattern, $map_url)) {
                $errors[] = 'Google Map URL must be a valid Google Maps link.';
            }

            // overwrite sanitized value for later DB use
            $_POST['shop_map_url'] = $map_url;
        }

        // Check opening hours (each day required)
        $days = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];
        foreach ($days as $day) {
            if (!isset($_POST['opening_hours'][$day]) || trim($_POST['opening_hours'][$day]) === '') {
                $errors[] = "Opening Hours for " . ucfirst($day) . " is required.";
            }
        }

        /**
         * DISPLAY ERRORS
         */
        if (!empty($errors)) {
            echo '<div class="notice notice-error"><p><strong>Error:</strong></p><ul>';
            foreach ($errors as $error) {
                echo '<li>' . esc_html($error) . '</li>';
            }
            echo '</ul></div>';
            return;
        }

        /**
         * SANITIZE & INSERT
         */
        $data = [
            'shop_code'        => sanitize_text_field($_POST['shop_code']),
            'shop_name'        => sanitize_text_field($_POST['shop_name']),
            'shop_address'     => sanitize_textarea_field($_POST['shop_address']),
            'shop_phone'       => sanitize_text_field($_POST['shop_phone']),
            'shop_opening_hours' =>
            wp_json_encode(array_map('sanitize_text_field', $_POST['opening_hours'])),
            'shop_description' => sanitize_textarea_field($_POST['shop_description']),
            'shop_slug'        => sanitize_title($_POST['shop_slug']),
            'shop_map_url'     => esc_url_raw($_POST['shop_map_url'])
        ];

        $format = ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'];

        // === PREVENT DUPLICATE SHOP CODE ===


        $insert_result = $wpdb->insert($table, $data, $format);

        if ($insert_result === false) {

            // Duplicate entry (UNIQUE shop_code)
            if ($wpdb->last_error && stripos($wpdb->last_error, 'Duplicate entry') !== false) {
                echo '<div class="notice notice-error"><p><strong>Error:</strong> This Shop Code already exists.</p></div>';
            } else {
                echo '<div class="notice notice-error"><p>Failed to insert shop.</p></div>';
            }

            return;
        }

        $inserted_shop_id = $wpdb->insert_id;

        /**
         * ===== IMAGE UPLOAD PROCESS =====
         */
        if (
            isset($_FILES['shop_images']['name']) && is_array($_FILES['shop_images']['name'])
            && !empty(array_filter($_FILES['shop_images']['name']))
        ) {

            $files = $_FILES['shop_images'];
            $orders = $_POST['shop_image_orders'];

            // Get WordPress upload directory path
            $upload_dir = wp_upload_dir();
            $base_upload_path = trailingslashit($upload_dir['basedir']) . 'amway-shop-images/';

            // ✅ Use shop code to create subfolder
            $safe_folder_name = sanitize_file_name($_POST['shop_code']);
            $shop_subdir = trailingslashit($base_upload_path . $safe_folder_name);

            // =======================================================
            // DELETE EXISTING SHOP IMAGE FOLDER (RECURSIVE)
            // =======================================================
            // ✔ Deletes all files AND subfolders inside the directory
            // ✔ Safer than manual unlink() loops
            // ✔ Works across different hosting environments
            // ✔ WordPress-approved method using WP_Filesystem
            // =======================================================

            if (file_exists($shop_subdir)) {

                // Load WordPress filesystem API
                require_once ABSPATH . 'wp-admin/includes/file.php';

                // Initialize filesystem
                WP_Filesystem();

                global $wp_filesystem;

                // true = recursive delete (folder + all contents)
                $wp_filesystem->delete($shop_subdir, true);
            }

            // =======================================================
            // RECREATE CLEAN SHOP IMAGE FOLDER
            // =======================================================
            // Ensures the directory exists for new image uploads
            // =======================================================

            wp_mkdir_p($shop_subdir);


            $now = current_time('mysql');
            // NOTE: using as $index => $name is important.
            foreach ($files['name'] as $index => $name) {
                if ($files['error'][$index] == UPLOAD_ERR_OK) {

                    $tmp_name = $files['tmp_name'][$index];
                    $order = intval($orders[$index]);

                    // Generate unique filename to prevent collision
                    $extension = pathinfo($name, PATHINFO_EXTENSION);
                    $new_filename = uniqid('shop_', true) . '.' . $extension;

                    // Full path to save the image
                    $destinationFolder = $shop_subdir . $new_filename;

                    // Move uploaded file to custom uploads folder
                    if (move_uploaded_file($tmp_name, $destinationFolder)) {

                        /**
                         * ==== FIX IMAGE ORIENTATION (EXIF) ====
                         * Must run BEFORE resizing.
                         * Only applies to JPG images (EXIF works with JPEG only)
                         */
                        $extension_lower = strtolower($extension);
                        if (in_array($extension_lower, ['jpg', 'jpeg']) && function_exists('exif_read_data')) {

                            $exif = @exif_read_data($destinationFolder);

                            if (!empty($exif['Orientation'])) {

                                $image = imagecreatefromjpeg($destinationFolder);

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

                                // Save the rotated JPEG (100% quality before resizing)
                                imagejpeg($image, $destinationFolder, 100);
                                unset($image);
                            }
                        }
                        /** ==== END EXIF FIX ==== */

                        /**
                         * ===== IMAGE RESIZE PROCESS =====
                         * Resize the image AFTER it has been moved to $destinationFolder
                         */
                        $max_width  = 1920; // adjust as you need
                        $max_height = 1080;

                        $image_editor = wp_get_image_editor($destinationFolder);

                        if (!is_wp_error($image_editor)) {

                            // Set the final saved JPEG/PNG quality
                            $image_editor->set_quality(82);

                            // Resize (false = maintain aspect ratio)
                            $image_editor->resize($max_width, $max_height, false);

                            // Save back to same file
                            $image_editor->save($destinationFolder);
                        }

                        // ✅ Store relative URL path in DB
                        $image_path = 'amway-shop-images/' . $safe_folder_name . '/' . $new_filename;

                        $shopImageTable = $wpdb->prefix . 'aboyuma_amway_shop_image';

                        // Insert the image into the database
                        $wpdb->insert(
                            $shopImageTable,
                            [
                                'shop_id'    => $inserted_shop_id,
                                'image_path' => $image_path,
                                'image_order' => $order,
                                'created'    => $now,
                                'modified'   => $now,
                            ],
                            [
                                '%d',
                                '%s',
                                '%d',
                                '%s',
                                '%s'
                            ]
                        );
                    }
                } else {
                    echo "File uploaded failed: " . $files['name'][$index];
                    echo " Error Code: " . $files['error'][$index]; // This should give you the error code
                    var_dump($files['error'][$index]); // Check the actual value and type
                    var_dump(UPLOAD_ERR_OK);          // Verify UPLOAD_ERR_OK is an integer 0
                    die();
                }
            }
        }

        echo '<div class="notice notice-success is-dismissible"><p>Amway shop added successfully!</p></div>';
        echo '<script>window.location.href="' . admin_url('admin.php?page=aboyuma-amway-shop-list') . '";</script>';
        exit; // Extra code from running unnecessarily.Additional HTML from being rendered.Unexpected side effects (like duplicate inserts, echoes, or further processing).
    }
?>

    <div class="wrap">
        <h1>Add Amway Shop</h1>

        <form method="post" enctype="multipart/form-data">
            <table class="form-table">

                <!-- Shop Code -->
                <tr>
                    <th><label for="shop_code">Shop Code</label></th>
                    <td><input type="text" name="shop_code" id="shop_code" class="regular-text" required></td>
                </tr>

                <!-- Shop Name -->
                <tr>
                    <th><label for="shop_name">Shop Name</label></th>
                    <td><input type="text" name="shop_name" id="shop_name" class="regular-text" required></td>
                </tr>

                <!-- Address -->
                <tr>
                    <th><label for="shop_address">Address</label></th>
                    <td>
                        <textarea name="shop_address" id="shop_address" class="large-text" rows="3" required></textarea>
                    </td>
                </tr>

                <!-- Phone -->
                <tr>
                    <th><label for="shop_phone">Phone</label></th>
                    <td><input type="text" name="shop_phone" id="shop_phone" class="regular-text" required></td>
                </tr>

                <!-- Opening Hours -->
                <tr>
                    <th><label>Opening Hours</label></th>
                    <td>
                        <div>
                            <input type="text" name="opening_hours[mon]" placeholder="10.30 - 21.00 (จันทร์)" class="regular-text" required><br>
                            <input type="text" name="opening_hours[tue]" placeholder="10.30 - 21.00 (อังคาร)" class="regular-text" required><br>
                            <input type="text" name="opening_hours[wed]" placeholder="10.30 - 21.00 (พุธ)" class="regular-text" required><br>
                            <input type="text" name="opening_hours[thu]" placeholder="10.30 - 21.00 (พฤหัส)" class="regular-text" required><br>
                            <input type="text" name="opening_hours[fri]" placeholder="10.30 - 21.00 (ศุกร์)" class="regular-text" required><br>
                            <input type="text" name="opening_hours[sat]" placeholder="10.30 - 18.00 (เสาร์)" class="regular-text" required><br>
                            <input type="text" name="opening_hours[sun]" placeholder="10.30 - 18.00 (อาทิตย์)" class="regular-text" required>
                        </div>
                    </td>
                </tr>

                <?php
                /* 
                === fRONT-END DISPLAY NOTE ===
                $opening = json_decode($shop->shop_opening_hours, true);
                $days = [
                    'mon' => 'จันทร์',
                    'tue' => 'อังคาร',
                    'wed' => 'พุธ',
                    'thu' => 'พฤหัส',
                    'fri' => 'ศุกร์',
                    'sat' => 'เสาร์',
                    'sun' => 'อาทิตย์',
                ];
                ?>

                <h3>เวลาทำการ</h3>
                <ul>
                <?php foreach ($days as $key => $label): ?>
                    <li><?php echo $label . ':' . $opening[$key]; ?></li>
                <?php endforeach; ?>
                </ul> 
                */
                ?>

                <!-- Description -->
                <tr>
                    <th><label for="shop_description">Description</label></th>
                    <td>
                        <?php
                        wp_editor(
                            '',                       // default content (empty for Add page)
                            'shop_description',       // Unique editor ID
                            array(
                                'textarea_name' => 'shop_description',
                                'textarea_rows' => 8,
                                'media_buttons' => false, // true if you want media upload button
                                'teeny' => false,         // small simplified toolbar (set true if needed)
                            )
                        );
                        ?>
                    </td>
                </tr>

                <!-- Slug -->
                <tr>
                    <th><label for="shop_slug">Slug</label></th>
                    <td><input type="text" name="shop_slug" id="shop_slug" class="regular-text" required></td>
                </tr>

                <!-- Google Map URL -->
                <tr>
                    <th scope="row">
                        <label for="shop_map_url">Google Map URL</label>
                    </th>
                    <td>
                        <input type="text"
                            id="shop_map_url"
                            name="shop_map_url"
                            value="<?php echo esc_attr($shop->shop_map_url ?? ''); ?>"
                            class="regular-text"
                            placeholder="https://maps.app.goo.gl/xxxxxx" />
                        <p class="description">Paste the Google Maps link for this shop.</p>
                    </td>
                </tr>


                <!-- Shop Images -->
                <tr>
                    <th scope="row"><label for="shop_images">Shop Images</label></th>
                    <td>
                        <div id="image-upload-container">
                            <!-- Dynamic image fields will appear here -->
                        </div>
                        <button type="button" id="add-image-field">Add Image</button>
                        <small>**Shop detail images with order number to display in shop detail page</small>
                    </td>
                </tr>

            </table>

            <?php
            wp_nonce_field('aboyuma_add_shop_action', 'aboyuma_shop_nonce');
            submit_button('Add Shop');
            ?>
        </form>
    </div>
    <script>
        jQuery(document).ready(function($) {
            // ===== Shop Images Script ===== //
            let imageCount = 0;

            // Add a new image input field for shop detail images
            $('#add-image-field').on('click', function() {
                imageCount++;
                const newField = `
                <div class="image-preview-container" data-index="${imageCount}">
                    <img src="" class="image-preview hidden" alt="Preview">
                    <input type="file" name="shop_images[]" class="shop-image-input">
                    <input type="number" name="shop_image_orders[]" class="order-input" placeholder="">
                    <span class="remove-button">Remove</span>
                </div>`;
                $('#image-upload-container').append(newField);
            });

            // Preview the selected detail images
            $(document).on('change', '.shop-image-input', function() {
                const file = this.files[0];
                const preview = $(this).siblings('.image-preview');
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.attr('src', e.target.result); // Set the image preview source
                        preview.removeClass('hidden'); // Show the image
                    };
                    reader.readAsDataURL(file); // Read the file as a data URL
                } else {
                    preview.attr('src', ''); // Clear the image preview
                    preview.addClass('hidden'); // Hide the image
                }
            });
            // Remove an image input field
            $(document).on('click', '.remove-button', function() {
                $(this).closest('.image-preview-container').remove();
            });
            // ===== End Shop Detail Images Script ===== //
        });
    </script>
    <style>
        .image-preview-container {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .image-preview {
            width: 100px;
            height: 100px;
            object-fit: cover;
            margin-right: 10px;
        }

        .order-input {
            width: 50px;
            margin-right: 10px;
        }

        .remove-button {
            margin-left: 10px;
            cursor: pointer;
        }
    </style>
<?php
}
