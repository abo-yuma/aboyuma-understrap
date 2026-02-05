<?php
// AJAX handler
function delete_amway_shop() {
    check_ajax_referer('delete_amway_shop_action', 'nonce');
    global $wpdb;
    $id = intval($_POST['id']);
    $id = absint($id);
    if (!$id) {
        wp_send_json_error('Invalid shop ID.');
        return;
    }

    $amway_shop_table = $wpdb->prefix . 'aboyuma_amway_shop';
    // ✅ 1. Get shop_code by ID
    $shop_code = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT shop_code FROM {$amway_shop_table} WHERE id = %d",
            $id
        )
    );

    if (empty($shop_code)) {
        wp_send_json_error('Amway shop not found.');
        return;
    }

    // ✅ 2. Remove image folder
    $upload_dir = wp_upload_dir();
    $base_upload_path = trailingslashit($upload_dir['basedir']) . 'amway-shop-images/';
    $safe_folder_name = sanitize_title($shop_code);
    $shop_subdir = trailingslashit($base_upload_path . $safe_folder_name);

    // Normalize paths for security check
    $upload_basedir = wp_normalize_path($upload_dir['basedir']);
    $shop_subdir_normalized = wp_normalize_path($shop_subdir);

    // Delete files & remove shop folder
    delete_files_in_folder($shop_subdir, $shop_subdir_normalized, $upload_basedir);

    $result = $wpdb->delete($wpdb->prefix . 'aboyuma_amway_shop', ['id' => $id], ['%d']);
    if ($result === false) {
        wp_send_json_error('Delete failed');
    } else {
        wp_send_json_success();
    }
}
add_action('wp_ajax_delete_amway_shop', 'delete_amway_shop');

// Script enqueue & localize
function delete_amway_shop_script() {
    wp_enqueue_script('delete-amway-shop', get_template_directory_uri() . '/integrated-plugins/amway-shop-crud/assets/amway-shop-delete.js', ['jquery'], null, true);
    wp_localize_script('delete-amway-shop', 'deleteAmwayShop', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'delete_amway_shop_nonce' => wp_create_nonce('delete_amway_shop_action'),
    ]);
}
add_action('admin_enqueue_scripts', 'delete_amway_shop_script');
