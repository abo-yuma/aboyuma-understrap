<?php
$product_image = ''; // LONGBLOB — leave empty or use base64 if testing image
$product_code = 'TEST123';
$product_brand_id = 1;
$product_category_id = 2;
$product_name = 'Test Product';
$product_short_description = 'Short desc';
$product_description = 'Full description here';
$product_benefit = 'Benefits...';
$product_detail = 'Detailed info...';
$product_specification = 'Specs...';
$product_comment = 'Comment...';
$product_additive_properties_json = json_encode([
    'no_color' => false,
    'no_flavoring' => false,
    'no_preservative' => false,
]);
$product_diet_properties_json = json_encode([
    'vegetarian' => false,
    'vegan' => true,
    'mostly vegan' => true,
]);
$product_review = 'Good product!';
$product_price = 99.99;
$product_member_price = 89.99;
$product_is_sop = 0;
$product_sop_link = '';
$product_shop_link = 'https://example.com';
$product_sale_article_link = '';
$product_is_pinned = 0;
$product_slug = 'product-slug';
$created = current_time('mysql');
$modified = current_time('mysql');

// // // // Insert into DB
$inserted = $wpdb->insert(
    $table_name,
    [
        'product_image' => $product_image,
        'product_code' => $product_code,
        'product_brand_id' => $product_brand_id,
        'product_category_id' => $product_category_id,
        'product_name' => $product_name,
        'product_short_description' => $product_short_description,
        'product_description' => $product_description,
        'product_benefit' => $product_benefit,
        'product_detail' => $product_detail,
        'product_specification' => $product_specification,
        'product_comment' => $product_comment,
        'product_additive_properties' => $product_additive_properties_json,
        'product_diet_properties' => $product_diet_properties_json,
        'product_review' => $product_review,
        'product_price' => $product_price,
        'product_member_price' => $product_member_price,
        'product_is_sop' => $product_is_sop,
        'product_sop_link' => $product_sop_link,
        'product_shop_link' => $product_shop_link,
        'product_sale_article_link' => $product_sale_article_link,
        'product_is_pinned' => $product_is_pinned,
        'product_slug' => $product_slug,
        'created' => $created,
        'modified' => $modified
    ],
    [
        '%s', // product_image
        '%s', // product_code
        '%d', // product_brand_id
        '%d', // product_category_id
        '%s', // product_name
        '%s', // product_short_description
        '%s', // product_description
        '%s', // product_benefit
        '%s', // product_detail
        '%s', // product_specification
        '%s', // product_comment
        '%s', // product_additive_properties (JSON)
        '%s', // product_diet_properties (JSON)
        '%s', // product_review
        '%f', // product_price
        '%f', // product_member_price
        '%d', // product_is_sop
        '%s', // product_sop_link
        '%s', // product_shop_link
        '%s', // product_sale_article_link
        '%d', // product_is_pinned
        '%s', // product_slug
        '%s', // created
        '%s'  // modified
    ]
);

if ($inserted) {
    // Get the ID of the inserted product
    $inserted_product_id = $wpdb->insert_id;
    // DB_INSERT: Insert into aboyuma_product_ingraphic table
    if (
        isset($_FILES['product_infographics']['name']) && is_array($_FILES['product_infographics']['name'])
        && !empty(array_filter($_FILES['product_infographics']['name']))
    ) {

        $infographic_files = $_FILES['product_infographics'];
        $inforaphic_orders = $_POST['product_infographic_orders'];

        // Get WordPress upload directory path
        $infographic_upload_dir = wp_upload_dir();
        $infographic_base_upload_path = trailingslashit($infographic_upload_dir['basedir']) . 'product-details/';

        // ✅ Use product code to create subfolder
        $infographic_safe_folder_name = sanitize_title($product_code);
        $infographic_subdir = trailingslashit($infographic_base_upload_path . $infographic_safe_folder_name . '/infographic');

        // ✅ If folder exists, delete all images inside
        if (file_exists($infographic_subdir)) {
            $existing_infographic_files = glob($infographic_subdir . '*');
            foreach ($existing_infographic_files as $existing_infographic_file) {
                if (is_file($existing_infographic_file)) {
                    unlink($existing_infographic_file);
                }
            }
        } else {
            // ✅ Create subfolder if it doesn’t exist
            wp_mkdir_p($infographic_subdir);
        }

        $now = current_time('mysql');
        // NOTE: using => $name is important.
        foreach ($infographic_files['name'] as $index => $infographic_name) {
            if ($infographic_files['error'][$index] == UPLOAD_ERR_OK) {

                $infographic_tmp_name = $infographic_files['tmp_name'][$index];
                $infographic_order = intval($inforaphic_orders[$index]);

                // Generate unique filename to prevent collision
                $extension = pathinfo($infographic_name, PATHINFO_EXTENSION);
                $infographic_new_filename = uniqid('product_', true) . '.' . $extension;

                // Full path to save the image
                $infographic_destination = $infographic_subdir . $infographic_new_filename;

                // Move uploaded file to custom uploads folder
                if (move_uploaded_file($infographic_tmp_name, $infographic_destination)) {

                    // ✅ Store relative URL path in DB
                    $infographic_path = 'product-details/' . $infographic_safe_folder_name . '/infographic/' . $infographic_new_filename;

                    // Insert the image into the database
                    $wpdb->insert(
                        $productInfographicTable,
                        [
                            'product_infographic_path' => $infographic_path,
                            'product_id' => $inserted_product_id, // Use the inserted product ID
                            'product_infographic_order' => $infographic_order,
                            'created' => $now,
                            'modified' => $now,
                        ],
                        [
                            '%s',
                            '%d',
                            '%d',
                            '%s',
                            '%s'
                        ]
                    );
                }
            } else {
                echo "File uploaded failed: " . $infographic_files['name'][$index];
                echo " Error Code: " . $infographic_files['error'][$index]; // This should give you the error code
                var_dump($infographic_files['error'][$index]); // Check the actual value and type
                var_dump(UPLOAD_ERR_OK);          // Verify UPLOAD_ERR_OK is an integer 0
                die();
            }
        }
    }

    // DB_INSERT: Add related product
    // On submit, filter only checked items
    $relatedProductTable = $wpdb->prefix . 'aboyuma_related_product';
    if (!empty($_POST['relatedproduct']) && is_array($_POST['relatedproduct'])) {
        // Insert each related product
        foreach ($_POST['relatedproduct'] as $item) {
            if (isset($item['id']) && $item['id'] !== '') {
                $related_id = intval($item['id']);
                // Skip self insert
                if ($related_id === intval($inserted_product_id)) {
                    continue;
                }
                $wpdb->insert(
                    $relatedProductTable,
                    [
                        'product_id'             => $inserted_product_id,
                        'related_product_id'     => intval($item['id']),
                        'related_product_comment' => sanitize_text_field($item['comment']),
                    ],
                    ['%d', '%d', '%s']
                );
            }
        }
    }
    echo '✅ Dummy product inserted successfully.';
} else {
    echo '❌ Failed to insert dummy product.<br>';
    echo 'MySQL Error: ' . $wpdb->last_error . '<br>';
    echo '<pre>';
    print_r($wpdb->last_query);
    echo '</pre>';
}
