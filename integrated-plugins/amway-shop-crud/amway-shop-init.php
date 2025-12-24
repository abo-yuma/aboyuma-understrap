<?php
/*
Theme Integrated Plugin
ABO YUMA Amway Shop Crud
Reference: 
https://github.com/eduardoarandah/wordpress-crud-example
https://accreditly.io/articles/how-to-create-a-crud-in-wordpress-without-using-plugins#content-section-6-deleting-aboyuma-data
*/
// Create multiple tables reference: https://wordpress.stackexchange.com/a/341755/193936

function create_aboyuma_amway_shop_table() {


    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    global $wpdb;
    $shopTable = $wpdb->prefix . 'aboyuma_amway_shop';
    $shopImageTable = $wpdb->prefix . 'aboyuma_amway_shop_image';
    $charset_collate = $wpdb->get_charset_collate();

    $queries = array();

    // Main shop table
    array_push($queries, "CREATE TABLE $shopTable (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    shop_code VARCHAR(20) NOT NULL DEFAULT '',                           -- รหัสร้านค้า
    shop_name VARCHAR(255) NOT NULL DEFAULT '',                          -- ชื่อร้านค้า
    shop_address TEXT NOT NULL,                                          -- ที่อยู่ร้านค้า
    shop_phone VARCHAR(50) NULL,                                         -- โทรศัพท์
    shop_opening_hours JSON NULL,                                        -- เวลาทำการ (เก็บเป็น JSON)
    shop_description TEXT NULL,                                          -- รายละเอียดเพิ่มเติม
    shop_slug VARCHAR(255) NOT NULL DEFAULT '',                          -- slug สำหรับหน้า shop-detail
    shop_map_url VARCHAR(500) NOT NULL DEFAULT '',                       -- Google Map link
    created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,                 -- วันที่สร้าง
    modified DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP -- วันที่แก้ไข
    UNIQUE KEY unique_shop_code (shop_code)                              -- ✅ PREVENT DUPLICATE
  ) $charset_collate;");

    // Shop image table
    array_push($queries, "CREATE TABLE $shopImageTable (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    shop_id INT UNSIGNED NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    image_order TINYINT(1) NOT NULL DEFAULT 0,
    created DATETIME NOT NULL,
    modified DATETIME NOT NULL,
    FOREIGN KEY (shop_id) REFERENCES $shopTable(id) ON DELETE CASCADE
  ) $charset_collate;");

    foreach ($queries as $sql) {
        dbDelta($sql);
    }

    update_option('aboyuma_amway_shop_schema_version', $schema_version);
}
add_action('after_switch_theme', 'create_aboyuma_amway_shop_table');


// Register admin menu
function aboyuma_amway_shop_menu() {
    $pageTitle = 'Amway Shops';
    $menuTitle = 'Amway Shops';
    $capability = 'manage_options';
    $menuSlug = 'aboyuma-amway-shop-list';
    $callback = 'aboyuma_amway_shop_list';
    $iconUrl = 'dashicons-store';
    $position = 26;

    add_menu_page(
        $pageTitle,
        $menuTitle,
        $capability,
        $menuSlug,
        $callback,
        $iconUrl,
        $position
    );

    add_submenu_page(
        $menuSlug,
        'All Shops',
        'All Shops',
        $capability,
        $menuSlug,
        $callback
    );

    add_submenu_page(
        null,
        'Add New Shop',
        null,
        $capability,
        'aboyuma-amway-shop-add',
        'aboyuma_amway_shop_add'
    );

    add_submenu_page(
        null,
        'Edit Shop',
        null,
        $capability,
        'aboyuma-amway-shop-edit',
        'aboyuma_amway_shop_edit'
    );

    add_submenu_page(
        null,
        'Shop Detail',
        null,
        $capability,
        'aboyuma-amway-shop-detail',
        'aboyuma_amway_shop_detail'
    );

    add_submenu_page(
        null,
        'Delete Shop',
        null,
        $capability,
        'aboyuma-amway-shop-delete',
        'aboyuma_amway_shop_delete'
    );
}
add_action('admin_menu', 'aboyuma_amway_shop_menu');


// Include CRUD subfiles
require_once get_template_directory() . '/integrated-plugins/amway-shop-crud/amway-shop-list.php';
require_once get_template_directory() . '/integrated-plugins/amway-shop-crud/amway-shop-add.php';
require_once get_template_directory() . '/integrated-plugins/amway-shop-crud/amway-shop-edit.php';
// require_once get_template_directory() . '/integrated-plugins/amway-shop-crud/shop-detail.php';
require_once get_template_directory() . '/integrated-plugins/amway-shop-crud/amway-shop-delete.php';
