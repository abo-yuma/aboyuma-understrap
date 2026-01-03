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

    // Decode nearby transport
    $nearby_transport = [];

    if (!empty($shop->shop_nearby_transport)) {
        $decoded_transport = json_decode($shop->shop_nearby_transport, true);
        if (is_array($decoded_transport)) {
            $nearby_transport = $decoded_transport;
        }
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aboyuma_shop_nonce'])) {

        // Verify nonce
        if (!wp_verify_nonce($_POST['aboyuma_shop_nonce'], 'aboyuma_edit_shop_action')) {
            echo '<div class="notice notice-error"><p>Security check failed.</p></div>';
            return;
        }

        /* ---------- ERROR CONTAINER ---------- */
        $errors = new WP_Error();

        $required_fields = [
            'shop_code'        => ['label' => 'Shop Code',      'type' => 'text'],
            'shop_name'        => ['label' => 'Shop Name',      'type' => 'text'],
            'shop_address'     => ['label' => 'Address',        'type' => 'text'],
            'shop_phone'       => ['label' => 'Phone',          'type' => 'text'],
            'shop_slug'        => ['label' => 'Slug',           'type' => 'text'],
            'shop_map_url'     => ['label' => 'Google Map URL', 'type' => 'text'],
            // ❌ shop_image is NOT required on edit
        ];

        foreach ($required_fields as $field => $config) {
            if (empty($_POST[$field])) {
                $errors->add($field, "{$config['label']} is required.");
            }
        }

        /* ---------- SHOP IMAGE (OPTIONAL on EDIT) ---------- */
        $shop_image = null;

        if (
            ! empty($_FILES['shop_image']) &&
            $_FILES['shop_image']['error'] !== UPLOAD_ERR_NO_FILE
        ) {

            if ($_FILES['shop_image']['error'] !== UPLOAD_ERR_OK) {
                $errors->add('shop_image', 'Image upload failed.');
            } else {

                $mime = mime_content_type($_FILES['shop_image']['tmp_name']);
                $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp'];

                if (! in_array($mime, $allowedMimeTypes, true)) {
                    $errors->add('shop_image', 'Only JPG, PNG, WEBP allowed.');
                } else {

                    $shop_image = file_get_contents($_FILES['shop_image']['tmp_name']);

                    if ($shop_image === false) {
                        $errors->add('shop_image', 'Cannot read image file.');
                    }
                }
            }
        }

        /* ---------- NEARBY TRANSPORT validation ---------- */
        $nearby_transport = [];

        if (!empty($_POST['transport']) && is_array($_POST['transport']['station'])) {

            foreach ($_POST['transport']['station'] as $index => $station) {

                $type    = trim($_POST['transport']['type'][$index] ?? '');
                $line    = trim($_POST['transport']['line'][$index] ?? '');
                $station = trim($station);

                // ignore fully empty rows
                if ($type === '' && $line === '' && $station === '') {
                    continue;
                }

                // ALL fields required
                if ($type === '' || $line === '' || $station === '') {
                    $errors->add(
                        'transport_required',
                        'Nearby transport requires Type, Line, and Station name.'
                    );
                    continue;
                }

                // validate type
                if (!in_array($type, ['BTS', 'MRT'], true)) {
                    $errors->add(
                        'transport_type',
                        'Transport type must be BTS or MRT.'
                    );
                    continue;
                }

                $nearby_transport[] = [
                    'type'    => sanitize_text_field($type),
                    'line'    => sanitize_text_field($line),
                    'station' => sanitize_text_field($station),
                ];
            }
        }

        /* ---------- OPENING HOURS validation ---------- */
        $days = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];
        foreach ($days as $day) {
            if (! isset($_POST['opening_hours'][$day])) {
                $errors->add(
                    'opening_hours_' . $day,
                    'Opening Hours for ' . ucfirst($day) . ' is missing.'
                );
            }
        }

        /* ---------- GOOGLE MAP URL validation ---------- */
        if (! empty($_POST['shop_map_url'])) {

            $map_url = esc_url_raw($_POST['shop_map_url']);

            if (! wp_http_validate_url($map_url)) {
                $errors->add('shop_map_url', 'Invalid Google Map URL.');
            }

            $_POST['shop_map_url'] = $map_url;
        }

        // If validation failed
        if ($errors->has_errors()) {
            echo '<div class="notice notice-error"><ul>';
            foreach ($errors->get_error_messages() as $message) {
                echo '<li>' . esc_html($message) . '</li>';
            }
            echo '</ul></div>';
            return;
        }

        /* ---------- UPDATE ---------- */
        $data = [
            'shop_code'          => sanitize_text_field($_POST['shop_code']),
            'shop_name'          => sanitize_text_field($_POST['shop_name']),
            'shop_address'       => sanitize_textarea_field($_POST['shop_address']),
            'shop_nearby_transport' => !empty($nearby_transport)
                ? wp_json_encode($nearby_transport)
                : null,
            'shop_phone'         => sanitize_text_field($_POST['shop_phone']),
            'shop_opening_hours' => wp_json_encode(
                array_map('sanitize_text_field', $_POST['opening_hours'])
            ),
            'shop_description'   => wp_kses_post($_POST['shop_description']),
            'shop_slug'          => sanitize_title($_POST['shop_slug']),
            'shop_map_url'       => esc_url_raw($_POST['shop_map_url']),
        ];

        $format = [
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s'
        ];

        // Update image only if uploaded
        if ($shop_image !== null) {
            $data['shop_image'] = $shop_image;
            array_unshift($format, '%s');
        }

        $result = $wpdb->update(
            $amwayShopTable,
            $data,
            ['id' => $shop_id],
            $format,
            ['%d']
        );

        if ($result === false) {
            echo '<div class="notice notice-error"><p>' .
                esc_html($wpdb->last_error) .
                '</p></div>';
            return;
        }

        echo '<div class="notice notice-success is-dismissible"><p>Amway shop updated successfully!</p></div>';
        echo '<script>window.location.href="' .
            admin_url('admin.php?page=aboyuma-amway-shop-edit&id=' . $shop_id . '&updated=true') .
            '";</script>';
        exit;
    }

?>
    <div class="wrap">
        <h1>Edit Amway Shop — <?php echo esc_html($shop->shop_name); ?></h1>

        <form method="post" enctype="multipart/form-data" id="aboyuma-edit-shop-form">
            <table class="form-table">
                <!-- Shop Image -->
                <tr>
                    <th scope="row"><label for="shop_image">Shop Image</label></th>
                    <td>
                        <input type="file" name="shop_image" id="shop_image">
                        <small style="display:block;">Main image of shop.</small>
                        <img id="shop_image_preview" src="data:image/jpeg;base64,<?php echo base64_encode($shop->shop_image); ?>" alt="shop Image Preview" style="max-width: 200px; margin-top: 10px;">
                    </td>
                </tr>
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

                <!-- Nearby Transport -->
                <tr>
                    <th><label>Nearby Transport</label></th>
                    <td>
                        <div id="transport-wrapper">

                            <?php if (!empty($nearby_transport)) : ?>
                                <?php foreach ($nearby_transport as $t) : ?>
                                    <div class="transport-row">

                                        <select name="transport[type][]">
                                            <option value="">-- Type --</option>
                                            <option value="BTS" <?php selected($t['type'], 'BTS'); ?>>BTS</option>
                                            <option value="MRT" <?php selected($t['type'], 'MRT'); ?>>MRT</option>
                                        </select>

                                        <input
                                            type="text"
                                            name="transport[line][]"
                                            placeholder="สาย (เช่น สีน้ำเงิน)"
                                            value="<?php echo esc_attr($t['line'] ?? ''); ?>">

                                        <input
                                            type="text"
                                            name="transport[station][]"
                                            placeholder="ชื่อสถานี"
                                            value="<?php echo esc_attr($t['station'] ?? ''); ?>">

                                        <button type="button" class="button remove-transport">ลบ</button>
                                    </div>
                                <?php endforeach; ?>

                            <?php else : ?>
                                <!-- ADD MODE: empty row -->
                                <div class="transport-row">
                                    <select name="transport[type][]">
                                        <option value="">-- Type --</option>
                                        <option value="BTS">BTS</option>
                                        <option value="MRT">MRT</option>
                                    </select>

                                    <input type="text" name="transport[line][]" placeholder="สาย (เช่น สีน้ำเงิน)">
                                    <input type="text" name="transport[station][]" placeholder="ชื่อสถานี">

                                    <button type="button" class="button remove-transport">ลบ</button>
                                </div>
                            <?php endif; ?>

                        </div>

                        <button type="button" class="button button-primary" id="add-transport">
                            + เพิ่มสถานี
                        </button>
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

            </table>

            <?php wp_nonce_field('aboyuma_edit_shop_action', 'aboyuma_shop_nonce'); ?>
            <?php submit_button('Update Shop'); ?>
        </form>
    </div>

    <script>
        jQuery(document).ready(function($) {
            // Preview for the main shop image
            $('#shop_image').on('change', function() {
                const file = this.files[0];
                const preview = $('#shop_image_preview');
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.attr('src', e.target.result); // Set the image preview source
                        preview.show(); // Show the image
                    };
                    reader.readAsDataURL(file); // Read the file as a data URL
                } else {
                    preview.attr('src', ''); // Clear the image preview
                    preview.hide(); // Hide the image
                }
            });

            $('#add-transport').on('click', function() {

                const rowHtml = `
                    <div class="transport-row">
                        <select name="transport[type][]">
                            <option value="">-- Type --</option>
                            <option value="BTS">BTS</option>
                            <option value="MRT">MRT</option>
                        </select>

                        <input type="text" name="transport[line][]" placeholder="สาย (เช่น สีน้ำเงิน)">
                        <input type="text" name="transport[station][]" placeholder="ชื่อสถานี">

                        <button type="button" class="button remove-transport">ลบ</button>
                    </div>
                `;
                $('#transport-wrapper').append(rowHtml);
            });

            $(document).on('click', '.remove-transport', function() {
                if ($('.transport-row').length > 1) {
                    $(this).closest('.transport-row').remove();
                }
            });

        });
    </script>

<?php
}
