<?php
/*
Plugin Name: ABO YUMA Author Box
Plugin URI:  https://yourwebsite.com
Description: Theme integrated plugin for managing the ABO YUMA Author Box, including author info, register links, and modal content.
Version:     1.0.0
*/

function create_aboyuma_author_box_table() {
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    global $wpdb;
    $authorBoxTable = $wpdb->prefix . 'aboyuma_author_box';
    $authorBoxTable = $wpdb->prefix . 'aboyuma_author_box';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $authorBoxTable (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        title VARCHAR(255) NOT NULL,
        bio TEXT NOT NULL,
        image VARCHAR(500) DEFAULT NULL,
        aboyumaQualifiedTitle VARCHAR(255) DEFAULT NULL,
        memberRegisterLinkUrl VARCHAR(500) DEFAULT NULL,
        memberRegisterLink VARCHAR(255) DEFAULT NULL,
        memberRegisterLinkInfoModalTitle VARCHAR(255) DEFAULT NULL,
        memberRegisterLinkInfoModalContent LONGTEXT DEFAULT NULL,
        aboRegisterLinkUrl VARCHAR(500) DEFAULT NULL,
        aboRegisterLink VARCHAR(255) DEFAULT NULL,
        aboRegisterLinkInfoModalTitle VARCHAR(255) DEFAULT NULL,
        aboRegisterLinkInfoModalContent LONGTEXT DEFAULT NULL,
        created DATETIME DEFAULT CURRENT_TIMESTAMP,
        modified DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) ENGINE=InnoDB $charset_collate;";

    dbDelta($sql);
}
add_action('after_switch_theme', 'create_aboyuma_author_box_table');

function aboyuma_author_box_menu() {
    add_menu_page(
        'ABO YUMA Author Box',
        'ABO YUMA Author Box',
        'manage_options',
        'aboyuma-author-box',
        'aboyuma_author_box_page',
        'dashicons-megaphone',
        25
    );
}
add_action('admin_menu', 'aboyuma_author_box_menu');

// require_once get_template_directory() . '/integrated-plugins/aboyuma-author-box/author-box-edit.php';

