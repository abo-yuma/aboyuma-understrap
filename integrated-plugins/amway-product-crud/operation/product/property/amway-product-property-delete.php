<?php
defined('ABSPATH') || exit;

function delete_amway_product_property() {

    check_ajax_referer('delete_property_nonce', 'nonce');

    global $wpdb;

    $id = isset($_POST['id']) ? absint($_POST['id']) : 0;

    if (!$id) {
        wp_send_json_error('Invalid property ID.');
    }

    $table = $wpdb->prefix . 'aboyuma_amway_product_property';

    $exists = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT id FROM {$table} WHERE id = %d",
            $id
        )
    );

    if (!$exists) {
        wp_send_json_error('Property not found.');
    }

    $result = $wpdb->delete(
        $table,
        ['id' => $id],
        ['%d']
    );

    if ($result === false) {
        wp_send_json_error('Delete failed.');
    }

    wp_send_json_success();
}
add_action('wp_ajax_delete_amway_product_property', 'delete_amway_product_property');
