<?php
// Admin and Front-end handle
function aboyuma_get_subcategories() {
    global $wpdb;
    $parent_id = isset($_GET['parent_id']) ? intval($_GET['parent_id']) : 0;
    $results = $wpdb->get_results(
        $wpdb->prepare("SELECT id, category_name FROM {$wpdb->prefix}aboyuma_amway_product_category WHERE parent_id = %d", $parent_id)
    );
    wp_send_json($results);
}
add_action('wp_ajax_get_subcategories', 'aboyuma_get_subcategories');
add_action('wp_ajax_nopriv_get_subcategories', 'aboyuma_get_subcategories');


// Front-end handle
function enqueue_page_product_script() {
    if (is_page('amway-product')) { // or use is_page(ID)
        wp_enqueue_script(
            'page-product-js',
            get_template_directory_uri() . '/integrated-plugins/amway-product-crud/assets/front-page-product.js',
            array('jquery'),
            null,
            true
        );

        // Pass dynamic data to JS
        wp_localize_script('page-product-js', 'productPageData', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('filter_products_nonce'),
        ));
    }
}
add_action('wp_enqueue_scripts', 'enqueue_page_product_script');
