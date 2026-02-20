<?php
defined('ABSPATH') || exit;
function aboyuma_sanitize_product($post) {

    $allowed_types  = ['tablet', 'capsule', 'sachet', 'g', 'ml'];
    $allowed_usage  = ['content', 'capacity'];

    $parent_category      = isset($post['parent_category']) ? absint($post['parent_category']) : 0;
    $product_subcategory  = isset($post['product_subcategory']) ? absint($post['product_subcategory']) : 0;

    return [
        'main_image_id' => isset($post['main_image_id']) ? absint($post['main_image_id']) : 0,
        'product_code'  => sanitize_text_field(trim($post['product_code'] ?? '')),
        'product_brand_id' => isset($post['product_brand']) ? absint($post['product_brand']) : null,
        'product_category_id' => $product_subcategory ?: $parent_category,
        'product_name' => sanitize_text_field(trim($post['product_name'] ?? '')),
        'product_unit_count' => isset($post['product_unit_count']) ? absint($post['product_unit_count']) : null,
        'product_unit_type' => in_array($post['product_unit_type'] ?? '', $allowed_types)
            ? sanitize_text_field($post['product_unit_type'])
            : null,
        'product_unit_usage' => in_array($post['product_unit_usage'] ?? '', $allowed_usage)
            ? sanitize_text_field($post['product_unit_usage'])
            : 'content',
        'product_short_description' => wp_kses_post($post['product_short_description'] ?? ''),
        'product_description' => wp_kses_post($post['product_description'] ?? ''),
        'product_benefit' => wp_kses_post($post['product_benefit'] ?? ''),
        'product_detail' => wp_kses_post($post['product_detail'] ?? ''),
        'product_specification' => wp_kses_post($post['product_specification'] ?? ''),
        'product_comment' => wp_kses_post($post['product_comment'] ?? ''),
        'product_review' => wp_kses_post($post['product_review'] ?? ''),
        'product_price' => isset($post['product_price']) ? floatval($post['product_price']) : 0,
        'product_member_price' => isset($post['product_member_price']) ? floatval($post['product_member_price']) : 0,
        'product_shop_link' => esc_url_raw($post['product_shop_link'] ?? ''),
        'product_sop_link' => !empty($post['product_sop_link']) ? esc_url_raw($post['product_sop_link']) : null,
        'product_sale_article_link' => !empty($post['product_sale_article_link']) ? esc_url_raw($post['product_sale_article_link']) : null,
        'product_is_pinned' => isset($post['product_is_pinned']) ? intval($post['product_is_pinned']) : 0,
        'product_slug' => sanitize_title($post['product_slug'] ?? ''),
    ];
}
