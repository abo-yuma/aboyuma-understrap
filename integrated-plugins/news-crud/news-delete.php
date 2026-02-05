<?php
// AJAX handler
function delete_news() {
    check_ajax_referer('delete_news_action', 'nonce');
    global $wpdb;
    $id = intval($_POST['id']);
    $result = $wpdb->delete($wpdb->prefix . 'aboyuma_news', ['id' => $id], ['%d']);
    if ($result === false) {
        wp_send_json_error('Delete failed');
    } else {
        wp_send_json_success();
    }
}
add_action('wp_ajax_delete_news', 'delete_news');

// Script enqueue & localize
function delete_news_script() {
    wp_enqueue_script('delete-news', get_template_directory_uri() . '/integrated-plugins/news-crud/assets/news-delete.js', ['jquery'], null, true);
    wp_localize_script('delete-news', 'deleteNews', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'delete_news_nonce' => wp_create_nonce('delete_news_action'),
    ]);
}
add_action('admin_enqueue_scripts', 'delete_news_script');

