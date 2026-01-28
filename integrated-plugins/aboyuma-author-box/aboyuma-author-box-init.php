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

    $authorBoxTable   = $wpdb->prefix . 'aboyuma_author_box';
    $charset_collate  = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $authorBoxTable (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,

        title VARCHAR(255) NOT NULL,
        bio TEXT NOT NULL,
        image VARCHAR(500) DEFAULT NULL,
        aboyuma_qualified_title VARCHAR(255) DEFAULT NULL,

        member_register_link_url VARCHAR(500) DEFAULT NULL,
        member_register_link VARCHAR(255) DEFAULT NULL,
        member_register_link_info_modal_title VARCHAR(255) DEFAULT NULL,
        member_register_link_info_modal_content LONGTEXT DEFAULT NULL,

        abo_register_link_url VARCHAR(500) DEFAULT NULL,
        abo_register_link VARCHAR(255) DEFAULT NULL,
        abo_register_link_info_modal_title VARCHAR(255) DEFAULT NULL,
        abo_register_link_info_modal_content LONGTEXT DEFAULT NULL,

        created DATETIME DEFAULT CURRENT_TIMESTAMP,
        modified DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

        PRIMARY KEY (id)
    ) ENGINE=InnoDB $charset_collate;";

    dbDelta($sql);

    // ✅ Insert default row if table is empty
    $author_exists = $wpdb->get_var("SELECT 1 FROM $authorBoxTable LIMIT 1");

    if ($author_exists === null) {
        $wpdb->insert(
            $authorBoxTable,
            array(
                'title' => 'ABO YUMA',
                'bio'   => 'This is default author bio.',
                'image' => '',
                'aboyuma_qualified_title' => 'ABO Qualified',

                'member_register_link_url' => '',
                'member_register_link' => 'สมัครสมาชิกแอมเวย์',
                'member_register_link_info_modal_title' => 'Member Registration',
                'member_register_link_info_modal_content' => '<p>Default member registration information.</p>',

                'abo_register_link_url' => '',
                'abo_register_link' => 'สมัครนักธุรกิจแอมเวย์',
                'abo_register_link_info_modal_title' => 'ABO Registration',
                'abo_register_link_info_modal_content' => '<p>Default ABO registration information.</p>',
            ),
            array(
                '%s', // title
                '%s', // bio
                '%s', // image
                '%s', // aboyuma_qualified_title

                '%s', // member_register_link_url
                '%s', // member_register_link
                '%s', // member_register_link_info_modal_title
                '%s', // member_register_link_info_modal_content

                '%s', // abo_register_link_url
                '%s', // abo_register_link
                '%s', // abo_register_link_info_modal_title
                '%s', // abo_register_link_info_modal_content
            )
        );
    }
}
add_action('after_switch_theme', 'create_aboyuma_author_box_table');

function aboyuma_author_box_menu() {
    add_menu_page(
        'ABO YUMA Author Box',
        'ABO YUMA Author Box',
        'manage_options',
        'aboyuma-author-box-edit',
        'aboyuma_author_box_edit_page',
        'dashicons-id',
        25
    );
}
add_action('admin_menu', 'aboyuma_author_box_menu');

require_once get_template_directory() . '/integrated-plugins/aboyuma-author-box/aboyuma-author-box-edit.php';
require_once get_template_directory() . '/integrated-plugins/aboyuma-author-box/aboyuma-author-box-render.php';
