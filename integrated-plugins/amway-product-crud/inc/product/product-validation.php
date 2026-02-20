<?php
defined('ABSPATH') || exit;
function aboyuma_validate_product($data, $table_name, $is_edit = false, $current_id = 0) {

    global $wpdb;

    $errors = [];

    extract($data);

    $required = [
        'Main image'   => $main_image_id,
        'Product code' => $product_code,
        'Brand'        => $product_brand_id,
        'Category'     => $product_category_id,
        'Product name' => $product_name,
        'Slug'         => $product_slug,
        'Unit type'    => $product_unit_type,
        'Shop link'    => $product_shop_link,
    ];

    foreach ($required as $label => $value) {
        if (empty($value)) {
            $errors[] = "$label required.";
        }
    }

    $rich_required = [
        'Short description' => $product_short_description,
        'Description'       => $product_description,
        'Benefit'           => $product_benefit,
        'Detail'            => $product_detail,
        'Specification'     => $product_specification,
        'Comment'           => $product_comment,
        'Review'            => $product_review,
    ];

    foreach ($rich_required as $label => $value) {
        if (empty(wp_strip_all_tags($value))) {
            $errors[] = "$label required.";
        }
    }

    if (!$product_unit_count || $product_unit_count <= 0) {
        $errors[] = 'Unit count must be greater than 0.';
    }

    if ($product_price < 0 || $product_member_price < 0) {
        $errors[] = 'Invalid price.';
    }

    if ($product_member_price > $product_price) {
        $errors[] = 'Member price cannot exceed normal price.';
    }

    // Unique check
    if ($is_edit) {
        $exists = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM $table_name
                 WHERE (product_slug = %s OR product_code = %s)
                 AND id != %d",
                $product_slug,
                $product_code,
                $current_id
            )
        );
    } else {
        $exists = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM $table_name
                 WHERE product_slug = %s OR product_code = %s",
                $product_slug,
                $product_code
            )
        );
    }

    if ($exists > 0) {
        $errors[] = 'Slug or product code already exists.';
    }

    return $errors;
}
