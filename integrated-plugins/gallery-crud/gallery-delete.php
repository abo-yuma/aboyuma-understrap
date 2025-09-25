<?php
// AJAX handler
function delete_gallery() {
    check_ajax_referer('delete_gallery_action', 'nonce');
    global $wpdb;

    $gallery_table = $wpdb->prefix . 'aboyuma_gallery';
    $image_table   = $wpdb->prefix . 'aboyuma_gallery_image';

    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    if ($id <= 0) {
        wp_send_json_error('Invalid gallery ID.');
    }

    // ✅ 1. Get gallery folder name (if you store it, e.g., slug or id)
    // For example, if images are stored in uploads/gallery/{id}/
    $upload_dir = wp_upload_dir();
    $gallery_subdir = trailingslashit($upload_dir['basedir']) . 'aboyuma-gallery/' . $id . '/';
    $gallery_subdir_normalized = wp_normalize_path($gallery_subdir);
    $upload_basedir = wp_normalize_path($upload_dir['basedir']);

    // ✅ Helper function to safely delete all files in a folder
    $delete_files_in_folder = function ($folder_path, $folder_path_normalized, $upload_basedir) {
        if (
            !file_exists($folder_path) || !is_dir($folder_path)
            || strpos($folder_path_normalized, $upload_basedir) !== 0
        ) {
            error_log("Folder missing or invalid: {$folder_path}");
            return;
        }

        $files = glob($folder_path . '*');
        if ($files !== false) {
            foreach ($files as $file) {
                if (is_file($file) && !unlink($file)) {
                    error_log("Failed to delete file: {$file}");
                }
            }
        }

        if (!rmdir($folder_path)) {
            error_log("Failed to remove directory: {$folder_path}");
        }
    };

    // ✅ 2. Delete gallery images folder
    $delete_files_in_folder($gallery_subdir, $gallery_subdir_normalized, $upload_basedir);

    // ✅ 3. Delete images from DB
    $wpdb->delete($image_table, ['gallery_id' => $id], ['%d']);

    // ✅ 4. Delete gallery record
    $result = $wpdb->delete($gallery_table, ['id' => $id], ['%d']);

    if ($result === false) {
        wp_send_json_error('Delete failed');
    } else {
        wp_send_json_success();
    }
}
add_action('wp_ajax_delete_gallery', 'delete_gallery');

// Script enqueue & localize
function delete_gallery_script() {
    wp_enqueue_script(
        'delete-gallery',
        get_template_directory_uri() . '/theme-plugins/gallery-crud/assets/gallery-delete.js',
        ['jquery'],
        null,
        true
    );
    wp_localize_script('delete-gallery', 'deleteGallery', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'delete_gallery_nonce' => wp_create_nonce('delete_gallery_action'),
    ]);
}
add_action('admin_enqueue_scripts', 'delete_gallery_script');
