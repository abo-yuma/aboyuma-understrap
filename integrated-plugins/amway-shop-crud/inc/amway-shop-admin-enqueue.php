<?php
defined('ABSPATH') || exit;

function amway_shop_admin_enqueue($hook) {

    $base_path = get_template_directory() . '/integrated-plugins/amway-shop-crud';
    $base_uri  = get_template_directory_uri() . '/integrated-plugins/amway-shop-crud';

    // Shop Add
    wp_enqueue_style(
        'amway-shop-add',
        $base_uri . '/assets/admin/amway-shop-add.min.css',
        [],
        filemtime($base_path . '/assets/admin/amway-shop-add.min.css')
    );
    wp_enqueue_script(
        'amway-shop-add',
        $base_uri . '/assets/admin/amway-shop-add.min.js',
        ['jquery'],
        filemtime($base_path . '/assets/admin/amway-shop-add.min.js'),
        true
    );

    // Shop Edit
    wp_enqueue_style(
        'amway-shop-edit',
        $base_uri . '/assets/admin/amway-shop-edit.min.css',
        [],
        filemtime($base_path . '/assets/admin/amway-shop-edit.min.css')
    );
    wp_enqueue_script(
        'amway-shop-edit',
        $base_uri . '/assets/admin/amway-shop-edit.min.js',
        ['jquery'],
        filemtime($base_path . '/assets/admin/amway-shop-edit.min.js'),
        true
    );

    // Shop List / Delete
    wp_enqueue_script(
        'amway-shop-delete',
        $base_uri . '/assets/admin/amway-shop-delete.min.js',
        ['jquery'],
        filemtime($base_path . '/assets/admin/amway-shop-delete.min.js'),
        true
    );
    wp_localize_script('amway-shop-delete', 'deleteShop', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('delete_shop_nonce'),
    ]);

    if (strpos($hook, 'aboyuma-amway-shop') !== false) {
        wp_enqueue_media();

        wp_enqueue_script(
            'aboyuma-shop-media',
            $base_uri . '/assets/admin/shop-media.min.js',
            ['jquery'],
            filemtime($base_path . '/assets/admin/shop-media.min.js'),
            true
        );
    }
}
add_action('admin_enqueue_scripts', 'amway_shop_admin_enqueue');
