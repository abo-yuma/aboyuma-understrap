<?php
/*
Plugin Name: News Management System
Description: Theme integrated plugin for managing news content with full CRUD functionality.
Version:     1.0.0
*/

function create_aboyuma_news_table() {
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    global $wpdb;
    $newsTable = $wpdb->prefix . 'aboyuma_news';

    $charset_collate = $wpdb->get_charset_collate();
    // Prepare query statements
    $queries = array();
    array_push($queries, "CREATE TABLE $newsTable (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
		news_text TEXT NOT NULL,
		display_date DATE DEFAULT NULL,
		created DATETIME DEFAULT CURRENT_TIMESTAMP,
		modified DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY (id)
  ) ENGINE=InnoDB $charset_collate;");

    // Create database tables
    foreach ($queries as $key => $sql) {
        dbDelta($sql);
    }
}
add_action('after_switch_theme', 'create_aboyuma_news_table');

function aboyuma_news_menu() {
    add_menu_page(
        'News',
        'News',
        'manage_options',
        'aboyuma-news',
        'aboyuma_news_list_page',
        'dashicons-megaphone',
        25
    );
    add_submenu_page(
        'aboyuma-news',
        'Add News',
        'Add News',
        'manage_options',
        'aboyuma-news-add',
        'aboyuma_news_add_page'
    );
    add_submenu_page(
        null,
        'Edit News',
        'Edit News',
        'manage_options',
        'aboyuma-news-edit',
        'aboyuma_news_edit_page'
    );
}

add_action('admin_menu', 'aboyuma_news_menu');

require_once get_template_directory() . '/integrated-plugins/news-crud/news-list.php';
require_once get_template_directory() . '/integrated-plugins/news-crud/news-add.php';
require_once get_template_directory() . '/integrated-plugins/news-crud/news-edit.php';
require_once get_template_directory() . '/integrated-plugins/news-crud/news-delete.php';
