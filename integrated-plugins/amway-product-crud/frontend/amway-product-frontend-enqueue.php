<?php
defined('ABSPATH') || exit;

/**
 * Enqueue frontend assets for Amway Product CRUD
 */
function amway_product_frontend_enqueue() {

    $base_path = get_template_directory() . '/integrated-plugins/amway-product-crud';
    $base_uri  = get_template_directory_uri() . '/integrated-plugins/amway-product-crud';

    /**
     * Product listing page
     */
    if (is_page('amway-product')) {

        wp_enqueue_style(
            'amway-product-page',
            $base_uri . '/assets/frontend/amway-product-page.min.css',
            [],
            filemtime($base_path . '/assets/frontend/amway-product-page.min.css')
        );

        wp_enqueue_script(
            'amway-product-page',
            $base_uri . '/assets/frontend/amway-product-page.min.js',
            ['jquery'],
            filemtime($base_path . '/assets/frontend/amway-product-page.min.js'),
            true
        );

        wp_localize_script(
            'amway-product-page',
            'productPageData',
            [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce'    => wp_create_nonce('filter_products_nonce'),
            ]
        );
    }

    /**
     * Product detail page
     */
    if (is_page('amway-product-detail')) {

        wp_enqueue_style(
            'amway-product-detail-page',
            $base_uri . '/assets/frontend/amway-product-detail-page.min.css',
            [],
            filemtime($base_path . '/assets/frontend/amway-product-detail-page.min.css')
        );

        wp_enqueue_script(
            'amway-product-detail-page',
            $base_uri . '/assets/frontend/amway-product-detail-page.min.js',
            ['jquery'],
            filemtime($base_path . '/assets/frontend/amway-product-detail-page.min.js'),
            true
        );
    }

    wp_localize_script(
        'amway-product-page',
        'productPageData',
        [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('filter_products_nonce'),
        ]
    );
}
add_action('wp_enqueue_scripts', 'amway_product_frontend_enqueue');
