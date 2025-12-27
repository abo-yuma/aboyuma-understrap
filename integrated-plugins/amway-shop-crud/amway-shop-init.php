<?php
/*
Theme Integrated Plugin
ABO YUMA Amway Shop Crud

Reference: 
- https://github.com/eduardoarandah/wordpress-crud-example
- https://accreditly.io/articles/how-to-create-a-crud-in-wordpress-without-using-plugins

Related issues:
- https://stackoverflow.com/questions/18287291/create-a-file-for-crud-functions-with-no-plugin

TODO :
Future Refactor
- Migrate from theme-integrated implementation
- Refactor using WordPress Plugin Boilerplate (WPPB)
  https://wppb.me/
- Remain theme-integrated
*/
// Create multiple tables reference: https://wordpress.stackexchange.com/a/341755/193936

function create_aboyuma_amway_shop_table() {

    // $schema_version = '1.0';
    // $current_version = get_option('aboyuma_amway_shop_schema_version');

    // if ($current_version === $schema_version) {
    //     return; // Already up-to-date
    // }

    // // Save current schema version
    // update_option('aboyuma_amway_shop_schema_version', $schema_version);

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    global $wpdb;
    $shopTable = $wpdb->prefix . 'aboyuma_amway_shop';
    $shopImageTable = $wpdb->prefix . 'aboyuma_amway_shop_image';
    $charset_collate = $wpdb->get_charset_collate();

    $queries = array();

    // Main shop table
    array_push($queries, "CREATE TABLE $shopTable (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        shop_code VARCHAR(20) NOT NULL DEFAULT '',
        shop_name VARCHAR(255) NOT NULL DEFAULT '',
        shop_address TEXT NOT NULL,
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


    // Shop image table
    array_push($queries, "CREATE TABLE $shopImageTable (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        shop_id INT UNSIGNED NOT NULL,
        image_path VARCHAR(255) NOT NULL,
        image_order TINYINT(1) NOT NULL DEFAULT 0,
        created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        modified DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) ENGINE=InnoDB $charset_collate;");


    // Create database tables
    foreach ($queries as $key => $sql) {
        dbDelta($sql);
    }

    // [table, fk_name, SQL]
    $foreign_keys = [
        [$shopImageTable, 'fk_aboyuma_shop_image_shop_id', "ALTER TABLE $shopImageTable
            ADD CONSTRAINT fk_aboyuma_shop_image_shop_id FOREIGN KEY (shop_id)
            REFERENCES $shopTable(id) ON DELETE CASCADE"]
    ];

    // --- Foreign keys ---
    // dbDelta does not support registering foreign keys.
    // Therefore, we use $wpdb->query manually to add the FK.
    // ⚠ Note: $wpdb->query will fail if FK already exists, so we check manually with fk_exists()
    foreach ($foreign_keys as [$table, $fk_name, $sql]) {
        if (!fk_exists($table, $fk_name)) {
            $wpdb->query($sql);
        }
    }
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
