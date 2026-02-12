<?php
defined('ABSPATH') || exit;

function amway_product_admin_enqueue($hook) {

    $base_path = get_template_directory() . '/integrated-plugins/amway-product-crud';
    $base_uri  = get_template_directory_uri() . '/integrated-plugins/amway-product-crud';

    // Product Add
    wp_enqueue_style(
        'amway-product-add',
        $base_uri . '/assets/admin/amway-product-add.min.css',
        [],
        filemtime($base_path . '/assets/admin/amway-product-add.min.css')
    );
    wp_enqueue_script(
        'amway-product-add',
        $base_uri . '/assets/admin/amway-product-add.min.js',
        ['jquery'],
        filemtime($base_path . '/assets/admin/amway-product-add.min.js'),
        true
    );

    // Product Edit
    wp_enqueue_style(
        'amway-product-edit',
        $base_uri . '/assets/admin/amway-product-edit.min.css',
        [],
        filemtime($base_path . '/assets/admin/amway-product-edit.min.css')
    );
    wp_enqueue_script(
        'amway-product-edit',
        $base_uri . '/assets/admin/amway-product-edit.min.js',
        ['jquery'],
        filemtime($base_path . '/assets/admin/amway-product-edit.min.js'),
        true
    );

    // Product List / Delete
    wp_enqueue_script(
        'amway-delete-product',
        $base_uri . '/assets/admin/amway-product-delete.min.js',
        ['jquery'],
        filemtime($base_path . '/assets/admin/amway-product-delete.min.js'),
        true
    );
    wp_localize_script('amway-product-delete', 'deleteProduct', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('delete_product_nonce'),
    ]);
}
add_action('admin_enqueue_scripts', 'amway_product_admin_enqueue');
