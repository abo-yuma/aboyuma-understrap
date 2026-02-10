<?php
defined('ABSPATH') || exit;

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

// AJAX Handler registration
// Fetch product data for page-product
function ajax_filter_products() {
    // Verify nonce
    if (!isset($_GET['_ajax_nonce']) || !wp_verify_nonce($_GET['_ajax_nonce'], 'filter_products_nonce')) {
        wp_send_json_error(['message' => 'Invalid or expired nonce'], 403);
        wp_die();
    }

    global $wpdb;
    $product_table = $wpdb->prefix . 'aboyuma_amway_product';
    $product_brand_table = $wpdb->prefix . 'aboyuma_amway_product_brand';
    $product_category_table = $wpdb->prefix . 'aboyuma_amway_product_category';

    // Retrieve category filter value
    $parent_category = isset($_GET['parent_category']) ? intval($_GET['parent_category']) : 0;
    $sub_category = isset($_GET['sub_category']) ? intval($_GET['sub_category']) : 0;

    // Retrieve price filter values
    $price_min = isset($_GET['price_min']) ? floatval($_GET['price_min']) : 0;
    $price_max = isset($_GET['price_max']) ? floatval($_GET['price_max']) : 0;

    // Base query
    $query = "SELECT p.id,
              p.product_image,
              p.product_name,
              p.product_short_description,
              p.product_benefit, b.brand_name, c.category_name
              FROM $product_table p
              LEFT JOIN $product_brand_table b ON p.product_brand_id = b.id
              LEFT JOIN $product_category_table c ON p.product_category_id = c.id
              WHERE 1=1";

    // Apply price filters if provided
    if ($price_min > 0) {
        $query .= $wpdb->prepare(" AND p.product_member_price >= %f", $price_min);
    }
    if ($price_max > 0) {
        $query .= $wpdb->prepare(" AND p.product_price <= %f", $price_max);
    }
    // Category filtering
    if ($sub_category > 0) {
        // Filter by selected subcategory only
        $query .= $wpdb->prepare(" AND c.id = %d", $sub_category);
    } elseif ($parent_category > 0) {
        // Get subcategory IDs + parent category ID
        $subcategory_ids = $wpdb->get_col(
            $wpdb->prepare(
                "SELECT id FROM $product_category_table WHERE parent_id = %d OR id = %d",
                $parent_category,
                $parent_category
            )
        );

        if (!empty($subcategory_ids)) {
            $placeholders = implode(',', array_fill(0, count($subcategory_ids), '%d'));
            $query .= $wpdb->prepare(" AND c.id IN ($placeholders)", ...$subcategory_ids);
        } else {
            // No matching categories — return nothing
            $query .= " AND 0=1";
        }
    }

    // Fetch the results from the database
    $products = $wpdb->get_results($query);
    $data = [];
    // Prepare data for DataTable
    foreach ($products as $product) {
        $image_url = $product->product_image ? '<img src="data:image/jpeg;base64,' . base64_encode($product->product_image) . '" width="100%" height="auto">' : '';
        $detail_url = esc_url(add_query_arg('id', $product->id, get_permalink(get_page_by_path('amway-product-detail')->ID)));
        // Combine image and name in one column
        $image_and_name = '
        <div style="text-align:center;">
            ' . $image_url . '
            <span>' . esc_html($product->product_name) . '</span>
        </div>';
        $data[] = [
            $image_and_name,
            htmlspecialchars_decode($product->product_short_description),
            '<a class="btn btn-sm btn-outline-primary" href="' . $detail_url . '">ดูรายละเอียด</a>' // Only show the text as a link
        ];
    }
    // Return plain array dataSrc should set to ''
    wp_send_json($data);
    wp_die();
}
// 🔄 Register AJAX for both guests and logged-in users
add_action('wp_ajax_filter_products', 'ajax_filter_products');
add_action('wp_ajax_nopriv_filter_products', 'ajax_filter_products');

// Fetch related product for page-amway-product-detail
function aboyuma_get_related_products() {
    check_ajax_referer('aboyuma_related_nonce', '_ajax_nonce');

    $product_id = intval($_GET['product_id'] ?? 0);
    $related_products = isset($_GET['related_products']) ? array_map('intval', (array) $_GET['related_products']) : [];

    global $wpdb;
    $placeholders = implode(',', array_fill(0, count($related_products), '%d'));
    $product_table = $wpdb->prefix . 'aboyuma_amway_product';
    $related_table = $wpdb->prefix . 'aboyuma_related_amway_product';

    $sql = "
        SELECT 
        p.id,
        p.product_image,
        p.product_name,
        r.related_product_comment
        FROM $product_table p
        INNER JOIN $related_table r
        ON p.id = r.related_product_id
        WHERE r.product_id = %d
        AND r.related_product_id IN ($placeholders)
        ORDER BY p.product_name ASC
    ";
    $prepared_sql = $wpdb->prepare($sql, $product_id, ...$related_products);
    $results = $wpdb->get_results($prepared_sql);

    $data = [];

    foreach ($results as $product) {
        $image_url = $product->product_image ? '<img src="data:image/jpeg;base64,' . base64_encode($product->product_image) . '" width="100%" height="auto">' : '';
        $name = esc_html($product->product_name);
        // Combine image and name in one column
        $image_and_name = '
        <div style="text-align:center;">
            ' . $image_url . '
            <span>' . $name . '</span>
        </div>';
        $comment = wp_kses_post($product->related_product_comment);
        $link = esc_url(add_query_arg('id', $product->id, get_permalink(get_page_by_path('amway-product-detail')->ID)));
        $data[] = [
            $image_and_name,
            $comment,
            "<a class='btn btn-sm btn-outline-primary' href='{$link}'>ดูรายละเอียด</a>"
        ];
    }

    wp_send_json([
        'data' => $data
    ]);
}
add_action('wp_ajax_get_related_products', 'aboyuma_get_related_products');
add_action('wp_ajax_nopriv_get_related_products', 'aboyuma_get_related_products');
