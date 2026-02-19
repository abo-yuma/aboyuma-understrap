<?php
defined('ABSPATH') || exit;

function aboyuma_amway_product_property_edit() {

    global $wpdb;
    $table = $wpdb->prefix . 'aboyuma_amway_product_property';

    if (!isset($_GET['id']) || empty($_GET['id'])) {
        echo '<div class="notice notice-error"><p>No property ID</p></div>';
        return;
    }

    $id = intval($_GET['id']);

    $property = $wpdb->get_row(
        $wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id)
    );

    if (!$property) {
        echo '<div class="notice notice-error"><p>Property not found</p></div>';
        return;
    }

    // ===============================
    // HANDLE UPDATE
    // ===============================
    if (
        isset($_POST['submit_property']) &&
        check_admin_referer('aboyuma_property_edit_nonce')
    ) {

        $property_name = sanitize_text_field($_POST['property_name']);
        $property_type = sanitize_text_field($_POST['property_type']);
        $property_description = wp_kses_post($_POST['property_description']);

        if (empty($property_name) || empty($property_type)) {
            echo '<div class="notice notice-error"><p>Please fill all fields</p></div>';
            return;
        }

        $wpdb->update(
            $table,
            [
                'property_name' => $property_name,
                'property_type' => $property_type,
                'property_description' => $property_description,
            ],
            ['id' => $id],
            ['%s', '%s', '%s'],
            ['%d']
        );

        $redirect_url = admin_url('admin.php?page=aboyuma-product-property-list&message=updated');

        printf(
            '<script>window.location.href="%s";</script>',
            esc_url_raw($redirect_url)
        );
        exit;
    }
?>

    <div class="wrap">
        <h1>Edit Property</h1>

        <form method="post">
            <?php wp_nonce_field('aboyuma_property_edit_nonce'); ?>

            <table class="form-table">

                <!-- Property Key (Readonly) -->
                <tr>
                    <th>Property Key</th>
                    <td>
                        <input type="text"
                            value="<?php echo esc_attr($property->property_key); ?>"
                            class="regular-text"
                            readonly>
                    </td>
                </tr>

                <!-- Property Name -->
                <tr>
                    <th><label for="property_name">Property Name *</label></th>
                    <td>
                        <input type="text"
                            name="property_name"
                            id="property_name"
                            value="<?php echo esc_attr($property->property_name); ?>"
                            class="regular-text"
                            required>
                    </td>
                </tr>

                <!-- Property Type -->
                <tr>
                    <th><label for="property_type">Property Type *</label></th>
                    <td>
                        <select name="property_type" required>
                            <option value="additive" <?php selected($property->property_type, 'additive'); ?>>Additive</option>
                            <option value="diet" <?php selected($property->property_type, 'diet'); ?>>Diet</option>
                            <option value="program" <?php selected($property->property_type, 'program'); ?>>Program</option>
                            <option value="feature" <?php selected($property->property_type, 'feature'); ?>>Feature</option>
                        </select>
                    </td>
                </tr>

                <!-- Description -->
                <tr>
                    <th>Description</th>
                    <td>
                        <?php
                        wp_editor(
                            $property->property_description,
                            'property_description',
                            [
                                'textarea_name' => 'property_description',
                                'textarea_rows' => 8,
                                'media_buttons' => false
                            ]
                        );
                        ?>
                    </td>
                </tr>

            </table>

            <p>
                <button type="submit"
                    name="submit_property"
                    class="button button-primary">
                    Save
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
