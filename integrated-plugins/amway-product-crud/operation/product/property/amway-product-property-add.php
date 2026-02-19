<?php
defined('ABSPATH') || exit;

function aboyuma_amway_product_property_add() {

    global $wpdb;
    $table = $wpdb->prefix . 'aboyuma_amway_product_property';

    // ===============================
    // HANDLE FORM SUBMIT
    // ===============================
    if (
        isset($_POST['submit_property']) &&
        check_admin_referer('aboyuma_amway_product_property_add_nonce')
    ) {

        $property_key = sanitize_title($_POST['property_key']);
        $property_name = sanitize_text_field($_POST['property_name']);
        $property_type = sanitize_text_field($_POST['property_type']);
        $property_description = wp_kses_post($_POST['property_description']);

        if (empty($property_key) || empty($property_name) || empty($property_type)) {

            echo '<div class="notice notice-error"><p>Please fill all required fields.</p></div>';
        } else {

            $exists = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT id FROM $table WHERE property_key = %s",
                    $property_key
                )
            );

            if ($exists) {

                echo '<div class="notice notice-error"><p>Property key already exists.</p></div>';
            } else {

                $wpdb->insert(
                    $table,
                    [
                        'property_key' => $property_key,
                        'property_name' => $property_name,
                        'property_type' => $property_type,
                        'property_description' => $property_description,
                        'created' => current_time('mysql')
                    ],
                    ['%s', '%s', '%s', '%s', '%s']
                );

                // JS Redirect (since you prefer it)
                $redirect_url = admin_url('admin.php?page=aboyuma-product-property-list&message=added');

                printf(
                    '<script>window.location.href="%s";</script>',
                    esc_url_raw($redirect_url)
                );
                exit;
            }
        }
    }
?>

    <div class="wrap">
        <h1>Add New Property</h1>

        <form method="post">
            <?php wp_nonce_field('aboyuma_amway_product_property_add_nonce'); ?>

            <table class="form-table">

                <!-- Property Key -->
                <tr>
                    <th>
                        <label for="property_key">Property Key *</label>
                    </th>
                    <td>
                        <input type="text"
                            name="property_key"
                            id="property_key"
                            class="regular-text"
                            required>

                        <p class="description">
                            รหัสเฉพาะของคุณสมบัติ (ห้ามซ้ำ) ใช้ตัวอักษรภาษาอังกฤษพิมพ์เล็ก และเครื่องหมายขีดกลาง (-) เท่านั้น
                            ตัวอย่าง: <code>vegan</code>, <code>halal-certified</code>
                            *ไม่สามารถแก้ไขภายหลังได้*
                        </p>
                    </td>
                </tr>

                <!-- Property Name -->
                <tr>
                    <th>
                        <label for="property_name">Property Name *</label>
                    </th>
                    <td>
                        <input type="text"
                            name="property_name"
                            id="property_name"
                            class="regular-text"
                            required>

                        <p class="description">
                            ชื่อที่จะแสดงผลในระบบและหน้าสินค้า
                            ตัวอย่าง: <strong>สินค้าเหมาะสำหรับผู้ทานมังสวิรัติ</strong>
                        </p>
                    </td>
                </tr>

                <!-- Property Type -->
                <tr>
                    <th>
                        <label for="property_type">Property Type *</label>
                    </th>
                    <td>
                        <select name="property_type" id="property_type" required>
                            <option value="">-- กรุณาเลือกประเภท --</option>
                            <option value="additive">Additive (สารประกอบ)</option>
                            <option value="diet">Diet (รูปแบบการรับประทาน)</option>
                            <option value="program">Program (โปรแกรม)</option>
                            <option value="feature">Feature (คุณสมบัติพิเศษ)</option>
                        </select>

                        <p class="description">
                            ใช้สำหรับจัดกลุ่มคุณสมบัติ เพื่อการแสดงผลและการกรองข้อมูลสินค้า
                        </p>
                    </td>
                </tr>

                <!-- Description -->
                <tr>
                    <th>
                        <label for="property_description">Description</label>
                    </th>
                    <td>
                        <?php
                        wp_editor(
                            '',
                            'property_description',
                            [
                                'textarea_name' => 'property_description',
                                'textarea_rows' => 8,
                                'media_buttons' => false
                            ]
                        );
                        ?>

                        <p class="description">
                            รายละเอียดเพิ่มเติมของคุณสมบัติ (ไม่บังคับกรอก)
                            ข้อมูลนี้สามารถนำไปแสดงในหน้ารายละเอียดสินค้าได้
                        </p>
                    </td>
                </tr>

            </table>

            <p>
                <button type="submit"
                    name="submit_property"
                    class="button button-primary">
                    Save Property
                </button>

                <a href="<?php echo admin_url('admin.php?page=aboyuma-product-property-list'); ?>"
                    class="button">
                    Cancel
                </a>
            </p>
        </form>
    </div>

<?php
}
