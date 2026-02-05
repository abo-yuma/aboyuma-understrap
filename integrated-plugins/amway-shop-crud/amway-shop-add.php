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

        /* ---------- ERROR CONTAINER ---------- */
        $errors = new WP_Error();

        $required_fields = [
            'shop_code'        => ['label' => 'Shop Code',        'type' => 'text'],
            'shop_name'        => ['label' => 'Shop Name',        'type' => 'text'],
            'shop_address'     => ['label' => 'Address',          'type' => 'text'],
            'shop_phone'       => ['label' => 'Phone',            'type' => 'text'],
            'shop_slug'        => ['label' => 'Slug',             'type' => 'text'],
            'shop_map_url'     => ['label' => 'Google Map URL',   'type' => 'text'],
            'shop_image'       => ['label' => 'Shop Image',       'type' => 'file'],
        ];

        foreach ($required_fields as $field => $config) {

            /* TEXT FIELDS */
            if ($config['type'] === 'text') {
                if (empty($_POST[$field])) {
                    $errors->add($field, "{$config['label']} is required.");
                }
            }

            /* FILE FIELDS */
            if ($config['type'] === 'file') {
                if (empty($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
                    $errors->add($field, "{$config['label']} is required.");
                }
            }
        }

        /* ---------- SHOP IMAGE validation ---------- */
        if (
            empty($_FILES['shop_image'])
            || $_FILES['shop_image']['error'] === UPLOAD_ERR_NO_FILE
        ) {
            $errors->add('shop_image', 'Shop Image is required.');
        }

        /* ---------- IMAGE VALIDATION ---------- */
        if (! empty($_FILES['shop_image']['tmp_name'])) {

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

        /* ---------- INSERT ---------- */
        $data = [
            'shop_image'         => $shop_image,
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
            '%s', // shop_image (BLOB)
            '%s', // shop_code
            '%s', // shop_name
            '%s', // shop_address
            '%s', // shop_nearby_transport
            '%s', // shop_phone
            '%s', // shop_opening_hours
            '%s', // shop_description
            '%s', // shop_slug
            '%s', // shop_map_url
        ];

        $insert = $wpdb->insert($table, $data, $format);

        if ($insert === false) {
            echo '<div class="notice notice-error"><p>' .
                esc_html($wpdb->last_error) .
                '</p></div>';
            return;
        }

        echo '<div class="notice notice-success is-dismissible"><p>Amway shop added successfully!</p></div>';
        echo '<script>window.location.href="' . admin_url('admin.php?page=aboyuma-amway-shop-list') . '";</script>';
        exit;
    }
?>

    <div class="wrap">
        <h1>Add Amway Shop</h1>

        <form method="post" enctype="multipart/form-data">
            <table class="form-table">
                <!-- Shop Image -->
                <tr>
                    <th scope="row"><label for="shop_image">Shop Image</label></th>
                    <td>
                        <input type="file" name="shop_image" id="shop_image">
                        <small style="display:block;">Main image of shop.</small>
                        <img id="shop_image_preview" src="" alt="shop Image Preview" style="max-width: 200px; margin-top: 10px; display: none;">
                    </td>
                </tr>
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

                <!-- Nearby Transport -->
                <tr>
                    <th><label for="shop_nearby_transport">Nearby Transport</label></th>
                    <td>
                        <div id="transport-wrapper">
                            <div class="transport-row">
                                <select name="transport[type][]">
                                    <option value="BTS">BTS</option>
                                    <option value="MRT">MRT</option>
                                </select>

                                <input type="text" name="transport[line][]" placeholder="สาย (เช่น สีน้ำเงิน)">
                                <input type="text" name="transport[station][]" placeholder="ชื่อสถานี">

                                <button type="button" class="button remove-transport">ลบ</button>
                            </div>
                        </div>
                        <button type="button" class="button button-primary" id="add-transport">
                            + เพิ่มสถานี
                        </button>
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

            </table>

            <?php
            wp_nonce_field('aboyuma_add_shop_action', 'aboyuma_shop_nonce');
            submit_button('Add Shop');
            ?>
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
