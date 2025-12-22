<?php
function aboyuma_amway_shop_edit() {
    global $wpdb;

    // Check if amway_shop ID is provided
    $shop_id = isset($_GET['id']) ? absint($_GET['id']) : 0;
    if (!$shop_id) {
        echo '<div class="notice notice-error is-dismissible"><p>Invalid shop ID.</p></div>';
    }

    // Fetch shop
    $amwayShopTable = $wpdb->prefix . 'aboyuma_amway_shop';
    $shop = $wpdb->get_row($wpdb->prepare("SELECT * FROM $amwayShopTable WHERE id = %d", $shop_id));
    if (!$shop) {
        echo '<div class="notice notice-error"><p>Shop not found.</p></div>';
        return;
    }

    // Decode opening hours
    $opening_hours = json_decode($shop->shop_opening_hours, true);
    if (!is_array($opening_hours)) {
        $opening_hours = [
            'mon' => '',
            'tue' => '',
            'wed' => '',
            'thu' => '',
            'fri' => '',
            'sat' => '',
            'sun' => ''
        ];
    }

    $amwayShopImageTable = $wpdb->prefix . 'aboyuma_amway_shop_image';
    $amwayShopImages = $wpdb->get_results("
        SELECT id, image_path, image_order 
        FROM $amwayShopImageTable 
        WHERE shop_id = $shop_id
        ORDER BY image_order ASC
    ");

    // Handle form submission
    // Handle form submission (UPDATE SHOP TABLE ONLY)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aboyuma_shop_nonce'])) {

        // Verify nonce
        if (!wp_verify_nonce($_POST['aboyuma_shop_nonce'], 'aboyuma_edit_shop_action')) {
            echo '<div class="notice notice-error"><p>Security check failed.</p></div>';
            return;
        }

        // Sanitize form fields
        $shop_code        = sanitize_text_field($_POST['shop_code']);
        $shop_name        = sanitize_text_field($_POST['shop_name']);
        $shop_address     = sanitize_textarea_field($_POST['shop_address']);
        $shop_phone       = sanitize_text_field($_POST['shop_phone']);
        $shop_description = wp_kses_post($_POST['shop_description']);
        $shop_slug        = sanitize_title($_POST['shop_slug']);
        $shop_map_url     = esc_url_raw($_POST['shop_map_url']);

        // Opening hours
        $opening_hours = isset($_POST['opening_hours'])
            ? array_map('sanitize_text_field', $_POST['opening_hours'])
            : [];

        $opening_hours_json = json_encode($opening_hours);

        // UPDATE shop table
        $updated = $wpdb->update(
            $amwayShopTable,
            [
                'shop_code'          => $shop_code,
                'shop_name'          => $shop_name,
                'shop_address'       => $shop_address,
                'shop_phone'         => $shop_phone,
                'shop_description'   => $shop_description,
                'shop_slug'          => $shop_slug,
                'shop_map_url'       => $shop_map_url,
                'shop_opening_hours' => $opening_hours_json,
            ],
            ['id' => $shop_id],
            ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'],
            ['%d']
        );
        // ===== DB_UPDATE: Update amway_shop images =====
        if ($updated !== false) {
            if (!empty($_POST['existing_image_ids'])) {
                $existingImageIds = $_POST['existing_image_ids'] ?? [];
                $imageOrders = $_POST['existing_image_orders'] ?? [];

                // Update existing images
                foreach ($existingImageIds as $index => $imageId) {
                    $order = intval($imageOrders[$index]);

                    // Update image order
                    $wpdb->update(
                        $amwayShopImageTable,
                        [
                            'image_order' => $order,
                            'modified' => current_time('mysql'),
                        ],
                        ['id' => intval($imageId)], // Identify the row by its ID
                        ['%d', '%s'],
                        ['%d']
                    );
                }
            }

            // Handle new image uploads
            // DB_UPDATE: Insert into aboyuma_amway_shop_image table
            // Note: Large files might fail silently if PHP or server upload size limits are exceeded,
            // so ensure 'upload_max_filesize', 'post_max_size', and server settings allow the file size.
            if (
                isset($_FILES['amway_shop_images']['name']) && is_array($_FILES['amway_shop_images']['name'])
                && !empty(array_filter($_FILES['amway_shop_images']['name']))
            ) {
                $files = $_FILES['amway_shop_images'];

                $orders = $_POST['amway_shop_image_orders'];

                // ✅ STEP 1: Get WordPress upload directory path
                $upload_dir = wp_upload_dir();
                $base_upload_path = trailingslashit($upload_dir['basedir']) . 'amway-shop-images/';

                // ✅ STEP 2: Use amway_shop code to create subfolder
                $safe_folder_name = sanitize_title($shop_code);
                $image_subdir = trailingslashit($base_upload_path . $safe_folder_name);

                // ✅ STEP 3: Create subfolder if it doesn't exist
                if (!file_exists($image_subdir)) {
                    if (!wp_mkdir_p($image_subdir)) {
                        wp_die('Unable to create amway_shop image folder: ' . esc_html($image_subdir));
                    }
                }

                $now = current_time('mysql');
                // NOTE: using => $name is important.
                foreach ($files['name'] as $index => $name) {
                    if ($files['error'][$index] == UPLOAD_ERR_OK) {
                        $tmp_name = $files['tmp_name'][$index];

                        $order = intval($orders[$index]);

                        // ✅ STEP 4: Generate a safe, unique filename
                        $extension = pathinfo($name, PATHINFO_EXTENSION);
                        $new_filename = uniqid('amway_shop_', true) . '.' . $extension;
                        $destinationFolder = $image_subdir . $new_filename;

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

                            // ✅ STEP 5: Store relative path in DB
                            $image_path = 'amway-shop-images/' . $safe_folder_name . '/' . $new_filename;

                            // ✅ STEP 6: Insert into database
                            // Insert the image into the database
                            $wpdb->insert(
                                $amwayShopImageTable,
                                [
                                    'image_path' => $image_path,
                                    'shop_id' => $shop_id, // Use the inserted amway_shop ID
                                    'image_order' => $order,
                                    'created' => $now,
                                    'modified' => $now,
                                ],
                                [
                                    '%s', // image_path
                                    '%d', // shop_id
                                    '%d', // image_order
                                    '%s', // created
                                    '%s', // modified
                                ]
                            );
                        }
                    } else {
                        echo "File uploaded failed: " . $files['name'][$index];
                        echo " Error Code: " . $files['error'][$index];
                        var_dump($files['error'][$index]);
                        var_dump(UPLOAD_ERR_OK);
                        die();
                    }
                }
            }

            // Optional: Delete images if necessary
            if (!empty($_POST['deleted_image_ids'])) {
                $deletedImageIds = $_POST['deleted_image_ids'];
                foreach ($deletedImageIds as $imageId) {
                    // ✅ Step 1: Get the image path
                    $image = $wpdb->get_row(
                        $wpdb->prepare("SELECT image_path FROM $amwayShopImageTable WHERE id = %d", $imageId)
                    );
                    if ($image) {
                        $upload_dir = wp_upload_dir();
                        $full_path = trailingslashit($upload_dir['basedir']) . $image->image_path;
                        $upload_basedir = wp_normalize_path($upload_dir['basedir']);
                        $full_path_normalized = wp_normalize_path($full_path);

                        // ✅ Step 2: Securely delete the file if it exists and is inside uploads dir
                        if (strpos($full_path_normalized, $upload_basedir) !== 0 || !is_file($full_path)) {
                            error_log("Attempt to delete file outside upload directory or invalid file: {$full_path}");
                        } elseif (!unlink($full_path)) {
                            error_log("Failed to delete amway_shop image file: {$full_path}");
                        }
                    }
                    // ✅ Step 3: Delete the DB record
                    $wpdb->delete(
                        $amwayShopImageTable,
                        ['id' => intval($imageId)],
                        ['%d']
                    );
                }
            }

            echo '<script>window.location.href="' . admin_url('admin.php?page=aboyuma-amway-shop-edit&updated-success=true&id=' . $shop_id) . '";</script>';
            exit;
        }
    }

?>
    <div class="wrap">
        <h1>Edit Amway Shop — <?php echo esc_html($shop->shop_name); ?></h1>

        <form method="post" enctype="multipart/form-data" id="aboyuma-edit-shop-form">
            <table class="form-table">

                <!-- Shop Code -->
                <tr>
                    <th><label for="shop_code">Shop Code</label></th>
                    <td><input type="text" name="shop_code" id="shop_code" class="regular-text" required value="<?php echo esc_attr($shop->shop_code); ?>"></td>
                </tr>

                <!-- Shop Name -->
                <tr>
                    <th><label for="shop_name">Shop Name</label></th>
                    <td><input type="text" name="shop_name" id="shop_name" class="regular-text" required value="<?php echo esc_attr($shop->shop_name); ?>"></td>
                </tr>

                <!-- Address -->
                <tr>
                    <th><label for="shop_address">Address</label></th>
                    <td>
                        <textarea name="shop_address" id="shop_address" class="large-text" rows="3" required><?php echo esc_textarea($shop->shop_address); ?></textarea>
                    </td>
                </tr>

                <!-- Phone -->
                <tr>
                    <th><label for="shop_phone">Phone</label></th>
                    <td><input type="text" name="shop_phone" id="shop_phone" class="regular-text" required value="<?php echo esc_attr($shop->shop_phone); ?>"></td>
                </tr>

                <!-- Opening Hours -->
                <tr>
                    <th><label>Opening Hours</label></th>
                    <td>
                        <div>
                            <input type="text" name="opening_hours[mon]" placeholder="10.30 - 21.00 (จันทร์)" class="regular-text" required value="<?php echo esc_attr($opening_hours['mon'] ?? ''); ?>"><br>
                            <input type="text" name="opening_hours[tue]" placeholder="10.30 - 21.00 (อังคาร)" class="regular-text" required value="<?php echo esc_attr($opening_hours['tue'] ?? ''); ?>"><br>
                            <input type="text" name="opening_hours[wed]" placeholder="10.30 - 21.00 (พุธ)" class="regular-text" required value="<?php echo esc_attr($opening_hours['wed'] ?? ''); ?>"><br>
                            <input type="text" name="opening_hours[thu]" placeholder="10.30 - 21.00 (พฤหัส)" class="regular-text" required value="<?php echo esc_attr($opening_hours['thu'] ?? ''); ?>"><br>
                            <input type="text" name="opening_hours[fri]" placeholder="10.30 - 21.00 (ศุกร์)" class="regular-text" required value="<?php echo esc_attr($opening_hours['fri'] ?? ''); ?>"><br>
                            <input type="text" name="opening_hours[sat]" placeholder="10.30 - 18.00 (เสาร์)" class="regular-text" required value="<?php echo esc_attr($opening_hours['sat'] ?? ''); ?>"><br>
                            <input type="text" name="opening_hours[sun]" placeholder="10.30 - 18.00 (อาทิตย์)" class="regular-text" required value="<?php echo esc_attr($opening_hours['sun'] ?? ''); ?>">
                        </div>
                    </td>
                </tr>

                <!-- Description -->
                <tr>
                    <th><label for="shop_description">Description</label></th>
                    <td>
                        <?php
                        wp_editor(
                            $shop->shop_description,
                            'shop_description',
                            array(
                                'textarea_name' => 'shop_description',
                                'textarea_rows' => 8,
                                'media_buttons' => false,
                                'teeny' => false,
                            )
                        );
                        ?>
                    </td>
                </tr>

                <!-- Slug -->
                <tr>
                    <th><label for="shop_slug">Slug</label></th>
                    <td><input type="text" name="shop_slug" id="shop_slug" class="regular-text" required value="<?php echo esc_attr($shop->shop_slug); ?>"></td>
                </tr>

                <!-- Google Map URL -->
                <tr>
                    <th><label for="shop_map_url">Google Map URL</label></th>
                    <td>
                        <input type="text" id="shop_map_url" name="shop_map_url" value="<?php echo esc_attr($shop->shop_map_url); ?>" class="regular-text" placeholder="https://maps.app.goo.gl/xxxxxx" />
                        <p class="description">Paste the Google Maps link for this shop.</p>
                    </td>
                </tr>

                <!-- Shop Images -->
                <tr>
                    <th scope="row"><label for="_benefit">Shop Images</label></th>
                    <td>
                        <div id="image-upload-container">
                            <!-- Existing Images -->
                            <?php
                            $upload_dir = wp_upload_dir(); // ✅ Add before the loop
                            $base_url = trailingslashit($upload_dir['baseurl']);
                            foreach ($amwayShopImages as $image) :
                                $image_url = $base_url . $image->image_path;
                                echo '<pre>' . $image_url . '</pre>';
                            ?>
                                <div class="image-preview-container" data-id="<?= $image->id; ?>">
                                    <img src="<?= esc_url($image_url); ?>" class="image-preview">
                                    <input type="number" name="existing_image_orders[]" value="<?= $image->image_order; ?>" class="order-input">
                                    <input type="hidden" name="existing_image_ids[]" value="<?= $image->id; ?>">
                                    <label><input type="checkbox" name="deleted_image_ids[]" value="<?= $image->id; ?>"> Delete</label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <!-- New Image Uploads -->
                        <button type="button" id="add-image-field">Add Image</button>
                        <small>** detail images with order number to display in detail page</small>
                    </td>
                </tr>

            </table>

            <?php wp_nonce_field('aboyuma_edit_shop_action', 'aboyuma_shop_nonce'); ?>
            <?php submit_button('Update Shop'); ?>
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
                    <input type="file" name="amway_shop_images[]" class="shop-image-input">
                    <input type="number" name="amway_shop_image_orders[]" class="order-input" placeholder="">
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
