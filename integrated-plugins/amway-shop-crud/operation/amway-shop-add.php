<?php
defined('ABSPATH') || exit;

function aboyuma_amway_shop_add() {
    global $wpdb;
    $table = $wpdb->prefix . 'aboyuma_amway_shop';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        /* =========================
           SECURITY
        ========================== */
        if (
            !isset($_POST['aboyuma_shop_nonce']) ||
            !wp_verify_nonce($_POST['aboyuma_shop_nonce'], 'aboyuma_add_shop_action')
        ) {
            wp_die('Security check failed.');
        }

        $errors = new WP_Error();

        /* =========================
           REQUIRED FIELDS
        ========================== */
        $required_fields = [
            'shop_code'      => 'Shop Code',
            'shop_name'      => 'Shop Name',
            'shop_address'   => 'Address',
            'shop_phone'     => 'Phone',
            'shop_slug'      => 'Slug',
            'shop_map_url'   => 'Google Map URL',
            'shop_image_id'  => 'Shop Image',
        ];

        foreach ($required_fields as $field => $label) {
            if (empty($_POST[$field])) {
                $errors->add($field, "$label is required.");
            }
        }

        /* =========================
           VALIDATE IMAGE
        ========================== */
        $image_id = intval($_POST['shop_image_id'] ?? 0);

        if ($image_id && get_post_type($image_id) !== 'attachment') {
            $errors->add('shop_image_id', 'Invalid shop image.');
        }

        /* =========================
           OPENING HOURS
        ========================== */
        $days = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];

        foreach ($days as $day) {
            if (empty($_POST['opening_hours'][$day])) {
                $errors->add(
                    'opening_hours_' . $day,
                    'Opening Hours for ' . ucfirst($day) . ' is required.'
                );
            }
        }

        /* =========================
           NEARBY TRANSPORT
        ========================== */
        $nearby_transport = [];

        if (!empty($_POST['transport']['station']) && is_array($_POST['transport']['station'])) {

            foreach ($_POST['transport']['station'] as $index => $station) {

                $type    = trim($_POST['transport']['type'][$index] ?? '');
                $line    = trim($_POST['transport']['line'][$index] ?? '');
                $station = trim($station);

                if ($type === '' && $line === '' && $station === '') {
                    continue;
                }

                if ($type === '' || $line === '' || $station === '') {
                    $errors->add(
                        'transport_required',
                        'If adding transport, Type, Line and Station are required.'
                    );
                    continue;
                }

                if (!in_array($type, ['BTS', 'MRT'], true)) {
                    $errors->add('transport_type', 'Transport type must be BTS or MRT.');
                    continue;
                }

                $nearby_transport[] = [
                    'type'    => sanitize_text_field($type),
                    'line'    => sanitize_text_field($line),
                    'station' => sanitize_text_field($station),
                ];
            }
        }

        /* =========================
           GOOGLE MAP VALIDATION
        ========================== */
        $map_url = esc_url_raw($_POST['shop_map_url']);

        if (!wp_http_validate_url($map_url)) {
            $errors->add('shop_map_url', 'Invalid Google Map URL.');
        }

        /* =========================
           IF ERROR → STOP
        ========================== */
        if ($errors->has_errors()) {

            echo '<div class="notice notice-error"><ul>';
            foreach ($errors->get_error_messages() as $message) {
                echo '<li>' . esc_html($message) . '</li>';
            }
            echo '</ul></div>';

            return;
        }

        /* =========================
           PREPARE DATA
        ========================== */
        $data = [
            'shop_image_id' => $image_id,
            'shop_code'     => sanitize_text_field($_POST['shop_code']),
            'shop_name'     => sanitize_text_field($_POST['shop_name']),
            'shop_address'  => sanitize_textarea_field($_POST['shop_address']),
            'shop_nearby_transport' => !empty($nearby_transport)
                ? wp_json_encode($nearby_transport)
                : null,
            'shop_phone'    => sanitize_text_field($_POST['shop_phone']),
            'shop_opening_hours' => wp_json_encode(
                array_map('sanitize_text_field', $_POST['opening_hours'])
            ),
            'shop_description' => wp_kses_post($_POST['shop_description']),
            'shop_slug'     => sanitize_title($_POST['shop_slug']),
            'shop_map_url'  => $map_url,
        ];

        $format = [
            '%d', // shop_image_id
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

        echo '<script>window.location.href="' .
            admin_url('admin.php?page=aboyuma-amway-shop-list') .
            '";</script>';
        exit;
    }
?>

    <div class="wrap">
        <h1>Add Amway Shop</h1>

        <form method="post" enctype="multipart/form-data">
            <table class="form-table">
                <!-- Shop Image -->
                <tr>
                    <th scope="row"><label>Shop Image</label></th>
                    <td>
                        <input type="hidden" name="shop_image_id" id="shop_image_id" value="">

                        <button type="button" class="button" id="upload_shop_image">
                            Select Image
                        </button>

                        <div id="shop_image_preview" style="margin-top:10px;"></div>
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
                                    <option value="">เลือกประเภท</option>
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
<?php
}
