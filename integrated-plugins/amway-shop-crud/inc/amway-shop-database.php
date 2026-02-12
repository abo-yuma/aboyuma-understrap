<?php
defined('ABSPATH') || exit;

function create_aboyuma_amway_shop_table() {

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    global $wpdb;
    $shopTable = $wpdb->prefix . 'aboyuma_amway_shop';
    $charset_collate = $wpdb->get_charset_collate();

    $queries = array();

    // Main shop table
    array_push($queries, "CREATE TABLE $shopTable (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        shop_image_id BIGINT UNSIGNED NULL,
        shop_code VARCHAR(20) NOT NULL DEFAULT '',
        shop_name VARCHAR(255) NOT NULL DEFAULT '',
        shop_address TEXT NOT NULL,
        shop_nearby_transport JSON NULL,
        shop_phone VARCHAR(50) NULL,
        shop_opening_hours JSON NULL,
        shop_description TEXT NULL,
        shop_slug VARCHAR(255) NOT NULL DEFAULT '',
        shop_map_url VARCHAR(500) NOT NULL DEFAULT '',
        created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        modified DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY unique_shop_code (shop_code)
    )  ENGINE=InnoDB $charset_collate;");

    // Create database tables
    foreach ($queries as $key => $sql) {
        dbDelta($sql);
    }
}
add_action('after_switch_theme', 'create_aboyuma_amway_shop_table');

// Define new schema version when there's db update
function aboyuma_update_amway_shop_schema() {

    $new_schema_version = '1.0'; // Define new version
    $current_schema_version = get_option('aboyuma_amway_shop_schema_version');

    if (version_compare($current_schema_version, $new_schema_version, '<')) {
        create_aboyuma_amway_shop_table();
        update_option('aboyuma_amway_shop_schema_version', $new_schema_version);
    }
}
add_action('after_setup_theme', 'aboyuma_update_amway_shop_schema');
