<?php
function aboyuma_amway_shop_edit() {
    global $wpdb;

    $table = $wpdb->prefix . 'aboyuma_amway_shop';

    /* =========================================
       1. DETECT SHOP ID (GET + POST SAFE)
    ========================================== */
    $shop_id = 0;

    if (isset($_GET['id'])) {
        $shop_id = absint($_GET['id']);
    } elseif (isset($_POST['shop_id'])) {
        $shop_id = absint($_POST['shop_id']);
    }

    if (!$shop_id) {

        // If POST but no ID, something wrong with form
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            echo '<div class="notice notice-error"><p>Missing shop ID during submission.</p></div>';
        } else {
            echo '<div class="notice notice-error"><p>Invalid shop ID.</p></div>';
        }

        return;
    }

    /* =========================================
       2. HANDLE FORM SUBMISSION FIRST
    ========================================== */
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aboyuma_shop_nonce'])) {

        if (!wp_verify_nonce($_POST['aboyuma_shop_nonce'], 'aboyuma_edit_shop_action')) {
            echo '<div class="notice notice-error"><p>Security check failed.</p></div>';
            return;
        }

        $errors = new WP_Error();

        /* ===== Required Fields ===== */
        $required_fields = [
            'shop_code'     => 'Shop Code',
            'shop_name'     => 'Shop Name',
            'shop_address'  => 'Address',
            'shop_phone'    => 'Phone',
            'shop_slug'     => 'Slug',
            'shop_map_url'  => 'Google Map URL',
            'shop_image_id' => 'Shop Image',
        ];

        foreach ($required_fields as $field => $label) {
            if (empty($_POST[$field])) {
                $errors->add($field, "$label is required.");
            }
        }

        /* ===== Image Validation ===== */
        $image_id = intval($_POST['shop_image_id'] ?? 0);

        if ($image_id && get_post_type($image_id) !== 'attachment') {
            $errors->add('shop_image_id', 'Invalid shop image.');
        }

        /* ===== Opening Hours ===== */
        $days = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];

        foreach ($days as $day) {
            if (empty($_POST['opening_hours'][$day])) {
                $errors->add("opening_$day", ucfirst($day) . ' opening hours required.');
            }
        }

        /* ===== Nearby Transport ===== */
        $nearby_transport = [];

        if (!empty($_POST['transport']['station'])) {

            foreach ($_POST['transport']['station'] as $index => $station) {

                $type    = trim($_POST['transport']['type'][$index] ?? '');
                $line    = trim($_POST['transport']['line'][$index] ?? '');
                $station = trim($station);

                if ($type === '' && $line === '' && $station === '') {
                    continue;
                }

                if ($type === '' || $line === '' || $station === '') {
                    $errors->add('transport_required', 'Transport Type, Line and Station are required.');
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

        /* ===== Map URL ===== */
        $map_url = esc_url_raw($_POST['shop_map_url']);

        if (!wp_http_validate_url($map_url)) {
            $errors->add('shop_map_url', 'Invalid Google Map URL.');
        }

        /* ===== Stop if Errors ===== */
        if ($errors->has_errors()) {
            echo '<div class="notice notice-error"><ul>';
            foreach ($errors->get_error_messages() as $message) {
                echo '<li>' . esc_html($message) . '</li>';
            }
            echo '</ul></div>';
        } else {

            /* =========================================
               3. UPDATE DATABASE
            ========================================== */

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
                '%d',
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

            $updated = $wpdb->update(
                $table,
                $data,
                ['id' => $shop_id],
                $format,
                ['%d']
            );

            if ($updated === false) {
                echo '<div class="notice notice-error"><p>' .
                    esc_html($wpdb->last_error) .
                    '</p></div>';
                return;
            }

            $redirect_url = add_query_arg(
                [
                    'page'    => 'aboyuma-amway-shop-edit',
                    'id'      => $shop_id,
                    'updated' => 'true'
                ],
                admin_url('admin.php')
            );

            echo '<script type="text/javascript">
                    window.location.href = "' . esc_url_raw($redirect_url) . '";
                  </script>';
            exit;
        }
    }

    /* =========================================
       4. FETCH SHOP AFTER UPDATE OR LOAD
    ========================================== */
    $shop = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM $table WHERE id = %d", $shop_id)
    );

    if (!$shop) {
        echo '<div class="notice notice-error"><p>Shop not found.</p></div>';
        return;
    }

    $nearby_transport = !empty($shop->shop_nearby_transport)
        ? json_decode($shop->shop_nearby_transport, true)
        : [];

    $opening_hours = !empty($shop->shop_opening_hours)
        ? json_decode($shop->shop_opening_hours, true)
        : [];

    if (isset($_GET['updated'])) {
        echo '<div class="notice notice-success is-dismissible"><p>Shop updated successfully.</p></div>';
    }
?>

    <div class="wrap">
        <h1>Edit Amway Shop — <?php echo esc_html($shop->shop_name); ?></h1>

        <form method="post" enctype="multipart/form-data" id="aboyuma-edit-shop-form">
            <table class="form-table">
                <!-- Shop Image -->
                <tr>
                    <th scope="row"><label>Shop Image</label></th>
                    <td>
                        <input type="hidden"
                            name="shop_image_id"
                            id="shop_image_id"
                            value="<?php echo esc_attr($shop->shop_image_id); ?>">

                        <button type="button" class="button" id="upload_shop_image">
                            Select Image
                        </button>

                        <div id="shop_image_preview" style="margin-top:10px;">
                            <?php if (!empty($shop->shop_image_id)) : ?>
                                <?php echo wp_get_attachment_image(
                                    $shop->shop_image_id,
                                    'medium',
                                    false,
                                    ['style' => 'max-width:200px;height:auto;']
                                ); ?>
                            <?php endif; ?>
                        </div>
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

            <input type="hidden" name="shop_id" value="<?php echo esc_attr($shop_id); ?>">
            <?php wp_nonce_field('aboyuma_edit_shop_action', 'aboyuma_shop_nonce'); ?>
            <button type="submit" name="submit" id="submit" class="button button-primary">Update Shop</button>
            <button type="button" onclick="window.location.href='<?php echo admin_url('admin.php?page=aboyuma-amway-shop-list'); ?>'"
                class="button button-secondary">Cancel</button>
        </form>
    </div>
<?php
}
