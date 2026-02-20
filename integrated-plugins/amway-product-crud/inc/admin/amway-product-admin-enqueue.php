<?php
defined('ABSPATH') || exit;

function amway_product_admin_enqueue($hook) {

    /**
     * -------------------------------------------------
     * Base Paths
     * -------------------------------------------------
     */
    $base_path = get_template_directory() . '/integrated-plugins/amway-product-crud';
    $base_uri  = get_template_directory_uri() . '/integrated-plugins/amway-product-crud';

    /**
     * -------------------------------------------------
     * Only load on your product pages
     * -------------------------------------------------
     */
    if (strpos($hook, 'aboyuma-product') === false) {
        return;
    }

    /**
     * -------------------------------------------------
     * Always load WordPress media (needed for images)
     * -------------------------------------------------
     */
    wp_enqueue_media();

    /**
     * -------------------------------------------------
     * Enable Drag & Drop Sorting (Gallery)
     * -------------------------------------------------
     */
    wp_enqueue_script('jquery-ui-sortable');

    /**
     * -------------------------------------------------
     * Shared Media Script (Add + Edit)
     * -------------------------------------------------
     */
    $media_js_path = $base_path . '/assets/admin/amway-product-media.min.js';

    wp_enqueue_script(
        'amway-product-media',
        $base_uri . '/assets/admin/amway-product-media.min.js',
        ['jquery'],
        file_exists($media_js_path) ? filemtime($media_js_path) : '1.0.0',
        true
    );

    /**
     * Optional: Pass data to JS (very useful later)
     */
    wp_localize_script(
        'amway-product-media',
        'amwayProduct',
        [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('amway_product_nonce'),
        ]
    );

    if ($hook === 'amway-products_page_aboyuma-product-category-list') {

        $script_path = $base_path . '/assets/admin/amway-product-category-delete.min.js';
        $script_uri  = $base_uri  . '/assets/admin/amway-product-category-delete.min.js';

        wp_enqueue_script(
            'amway-category-delete',
            $script_uri,
            ['jquery'],
            file_exists($script_path) ? filemtime($script_path) : '1.0.0',
            true
        );

        wp_localize_script(
            'amway-category-delete',
            'deleteProductCategory',
            [
                'ajaxurl' => admin_url('admin-ajax.php'),
                'delete_product_category_nonce' => wp_create_nonce('delete_product_category_nonce')
            ]
        );
    }

    /**
     * =====================================================
     * ADD & EDIT PAGE (Shared Assets)
     * =====================================================
     */
    if (
        $hook === 'admin_page_aboyuma-product-add' ||
        $hook === 'admin_page_aboyuma-product-edit'
    ) {

        $admin_css = $base_path . '/assets/admin/amway-product-admin.min.css';
        $admin_js  = $base_path . '/assets/admin/amway-product-admin.min.js';

        wp_enqueue_style(
            'amway-product-admin',
            $base_uri . '/assets/admin/amway-product-admin.min.css',
            [],
            file_exists($admin_css) ? filemtime($admin_css) : '1.0.0'
        );

        wp_enqueue_script(
            'amway-product-admin',
            $base_uri . '/assets/admin/amway-product-admin.min.js',
            ['jquery', 'amway-product-media'],
            file_exists($admin_js) ? filemtime($admin_js) : '1.0.0',
            true
        );
    }

    /**
     * -------------------------------------------------
     * PRODUCT PROPERTY LIST PAGE
     * -------------------------------------------------
     *
     * Because this submenu was added with:
     * add_submenu_page( 'aboyuma-product-list', ... )
     *
     * WordPress generates the hook in this format:
     * {parent_slug}_page_{menu_slug}
     *
     */
    if ($hook === 'amway-products_page_aboyuma-product-property-list') {

        wp_enqueue_script(
            'amway-property-delete',
            $base_uri . '/assets/admin/amway-product-property-delete.min.js',
            ['jquery'],
            filemtime($base_path . '/assets/admin/amway-product-property-delete.min.js'),
            true
        );

        wp_localize_script('amway-property-delete', 'deleteProperty', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('delete_property_nonce'),
        ]);
    }
}

add_action('admin_enqueue_scripts', 'amway_product_admin_enqueue');
