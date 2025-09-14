<?php
function aboyuma_product_add() {
  // Add new product data page content
  global $wpdb;
  $table_name = $wpdb->prefix . 'aboyuma_product';
  // Fetch brands and categories
  $productBrandTable = $wpdb->prefix . 'aboyuma_product_brand';
  $productCategoryTable = $wpdb->prefix . 'aboyuma_product_category';
  $productImageTable = $wpdb->prefix . 'aboyuma_product_image';
  $productInfographicTable = $wpdb->prefix . 'aboyuma_product_infographic';
  // Fetch brands
  $brands = $wpdb->get_results("SELECT id, brand_name FROM $productBrandTable");
  // Fetch categories
  $categories = $wpdb->get_results("SELECT id, category_name, parent_id FROM $productCategoryTable");

  if (
    $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aboyuma_product_add_nonce'])
    && wp_verify_nonce($_POST['aboyuma_product_add_nonce'], 'aboyuma_product_add')
  ) {

    // ===== DEBUG SPACE =====
    // 🔧 USAGE GUIDE FOR DEBUGGING $_POST AND DB INSERTS
    //
    // This section is for development use only.
    // It helps you inspect any specific input submitted via $_POST,
    // test how the value behaves (e.g. split/trim),
    // and attempt a manual insert into the database.
    //
    // ✅ Step-by-step:
    // 1. Set the `$field` to the name of the input field you want to test.
    // 2. Check if it exists and dump the raw value.
    // 3. (Optional) Process the value as needed (e.g., explode, trim, validate).
    // 4. Prepare a mock insert with a known valid table and reference ID.
    // 5. Use `$wpdb->last_error` to debug SQL issues.
    // 6. Always `exit;` at the end to prevent further code execution.
    // $product_slug = sanitize_text_field(trim($_POST['product_slug']));
    // // echo '<pre>';
    // // echo var_dump();
    // // echo '</pre>';
    // Get the posted values

    // // Dummy values
    // include(get_template_directory() . '/theme-plugins/product-crud/inc/test-product-add.php');
    // exit;
    // ===== DEBUG SPACE END =====

    // ===== Product Data Insert =====
    // Sanitize and assign form data
    $product_code = sanitize_text_field(trim($_POST['product_code']));
    $product_brand_id = sanitize_text_field(trim($_POST['product_brand']));
    $parent_category = isset($_POST['parent_category']) ? (int)$_POST['parent_category'] : 0;
    $product_subcategory = isset($_POST['product_subcategory']) ? (int)$_POST['product_subcategory'] : 0;

    // Determine which value to use for $product_category_id
    if (!empty($product_subcategory)) {
      // If subcategory is selected, use subcategory
      $product_category_id = $product_subcategory;
    } else {
      // If subcategory is empty, use parent category
      $product_category_id = $parent_category;
    }
    $product_category_id = absint(trim($product_category_id));
    $product_name = sanitize_text_field(trim($_POST['product_name']));

    // Product Unit Count (integer, nullable)
    $product_unit_count = isset($_POST['product_unit_count']) ? intval($_POST['product_unit_count']) : null;

    // Product Unit Type (enum, sanitize and validate)
    $allowed_types = ['tablet', 'capsule', 'sachet', 'g', 'ml'];
    $product_unit_type = isset($_POST['product_unit_type']) && in_array($_POST['product_unit_type'], $allowed_types)
      ? sanitize_text_field($_POST['product_unit_type'])
      : null;

    $product_short_description = sanitize_textarea_field(trim($_POST['product_short_description']));
    $product_description = sanitize_textarea_field(trim($_POST['product_description']));
    $product_benefit = sanitize_textarea_field(trim($_POST['product_benefit']));
    $product_detail = wp_kses_post(trim($_POST['product_detail']));
    $product_specification = wp_kses_post(trim($_POST['product_specification']));
    $product_comment = wp_kses_post(trim($_POST['product_comment']));
    // --- Product Additive Properties ---
    $all_additive_options = ['no_color', 'no_flavoring', 'no_preservative'];
    $selected_additive_props = isset($_POST['product_additive_properties']) && is_array($_POST['product_additive_properties'])
      ? array_map('sanitize_text_field', $_POST['product_additive_properties'])
      : [];

    $product_additive_properties = [];
    foreach ($all_additive_options as $option) {
      $product_additive_properties[$option] = in_array($option, $selected_additive_props, true);
    }
    $product_additive_properties_json = json_encode($product_additive_properties);
    // --- Product Diet Properties ---
    $all_diet_options = ['vegetarian', 'vegan', 'practical vegan'];
    $selected_diet_props = isset($_POST['product_diet_properties']) && is_array($_POST['product_diet_properties'])
      ? array_map('sanitize_text_field', $_POST['product_diet_properties'])
      : [];
    // Build consistent boolean object
    $product_diet_properties = [];
    foreach ($all_diet_options as $diet) {
      $product_diet_properties[$diet] = in_array($diet, $selected_diet_props);
    }
    // Encode as JSON before inserting to DB
    $product_diet_properties_json = json_encode($product_diet_properties);
    $product_review = wp_kses_post(trim($_POST['product_review']));
    $product_price = floatval($_POST['product_price']);
    $product_member_price = floatval($_POST['product_member_price']);
    $product_is_sop =  intval($_POST['product_is_sop']);
    $product_shop_link = esc_url_raw(trim($_POST['product_shop_link']));
    $product_sop_link = esc_url_raw(trim($_POST['product_sop_link']));
    $product_sale_article_link = esc_url_raw(trim($_POST['product_sale_article_link']));
    $product_is_pinned = intval($_POST['product_is_pinned']);
    $product_slug = sanitize_text_field(trim($_POST['product_slug']));
    $created = current_time('mysql');
    $modified = current_time('mysql');

    // Validate fields
    // Validate product image url
    // Check if a file was uploaded

    if (!empty($_FILES['product_image']['tmp_name'])) {
      $product_image = file_get_contents($_FILES['product_image']['tmp_name']);
    } else {
      $errors[] = 'Product image is required.';
    }
    // Validate product code
    if (empty($product_code)) {
      $errors[] = 'Product code is required.';
    }
    // Validate product brand
    if (empty($product_brand_id)) {
      $errors[] = 'Product brand is required.';
    }
    // Validate product category
    if (empty($product_category_id)) {
      $errors[] = 'Product category is required.';
    }
    // Validate product name
    if (empty($product_name)) {
      $errors[] = 'Product name is required.';
    }
    // Validate Product Unit Count
    if ($product_unit_count === null || $product_unit_count < 0) {
      $errors[] = 'Unit count is required and must be 0 or greater.';
    }
    // Validate Product Unit Type
    $allowed_types = ['tablet', 'capsule', 'sachet', 'g', 'ml'];
    if (empty($product_unit_type) || !in_array($product_unit_type, $allowed_types)) {
      $errors[] = 'Unit type is required and must be one of: tablet, capsule, sachet, g, ml.';
    }
    // Validate product description
    if (empty($product_short_description)) {
      $errors[] = 'Product short description is required.';
    }
    // Validate product description
    if (empty($product_description)) {
      $errors[] = 'Product description is required.';
    }
    // Validate product benefit
    if (empty($product_benefit)) {
      $errors[] = 'Product benefit is required.';
    }
    // Validate product detail
    if (empty($product_detail)) {
      $errors[] = 'Product detail is required.';
    }
    // Validate product price
    if (empty($product_price)) {
      $errors[] = 'Product price is required.';
    }
    // Validate product member price
    if (empty($product_member_price)) {
      $errors[] = 'Product member price is required.';
    }
    // Validate product price
    if (is_null($product_is_sop)) {
      $errors[] = 'Product SOP flag is required.';
    }
    // Validate Product Sop Link
    if (!empty($product_sop_link)) {
      if (!filter_var($product_sop_link, FILTER_VALIDATE_URL)) {
        $errors[] = 'Product Shop link must be a valid URL.';
      }
    }
    // Validate Product Shop Link
    // Validate Product Shop Link
    if (empty($product_shop_link)) {
      $errors[] = 'Product Shop link is required.';
    } elseif (!empty($product_shop_link) && !filter_var($product_shop_link, FILTER_VALIDATE_URL)) {
      $errors[] = 'Product Shop link must be a valid URL.';
    }
    // Validate product sale article link
    if (!empty($product_sale_article_link)) {
      if (!filter_var($product_sale_article_link, FILTER_VALIDATE_URL)) {
        $errors[] = 'Product sale article link must be a valid URL.';
      }
    }
    // Validate product is pinned
    if (is_null($product_is_pinned)) {
      $errors[] = 'Product is pinned flag is required.';
    }
    // Validate product slug
    if (empty($product_slug)) {
      $errors[] = 'Product slug is required.';
    }
    // If no errors, insert data into the database
    if (empty($errors)) {
      // DB_INSERT: Insert into aboyuma_product table
      $inserted = $wpdb->insert(
        $table_name,
        [
          'product_image' => $product_image,
          'product_code' => $product_code,
          'product_brand_id' => $product_brand_id,
          'product_category_id' => $product_category_id,
          'product_name' => $product_name,
          'product_unit_count' => $product_unit_count,
          'product_unit_type' => $product_unit_type,
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
          'product_shop_link' => $product_shop_link,
          'product_sop_link' => $product_sop_link,
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
          '%d', // product_unit_count
          '%s', // product_unit_type
          '%d', // product_tablet_count
          '%s', // product_serving_size
          '%d', // product_total_servings
          '%s', // product_short_description
          '%s', // product_description
          '%s', // product_benefit
          '%s', // product_detail
          '%s', // product_specification
          '%s', // product_comment
          '%s', // product_additive_properties
          '%s', // product_diet_properties
          '%s', // product_review
          '%f', // product_price
          '%f', // product_member_price
          '%d', // product_is_sop
          '%s', // product_shop_link
          '%s', // product_sop_link
          '%s', // product_sale_article_link
          '%d', // product_is_pinned
          '%s', // product_slug
          '%s', // created
          '%s' // modified
        ]
      );
      // Insert product
      if ($inserted) {
        // Get the ID of the inserted product
        $inserted_product_id = $wpdb->insert_id;

        // DB_INSERT: Insert into aboyuma_product_images table
        if (
          isset($_FILES['product_images']['name']) && is_array($_FILES['product_images']['name'])
          && !empty(array_filter($_FILES['product_images']['name']))
        ) {

          $files = $_FILES['product_images'];
          $orders = $_POST['product_image_orders'];

          // Get WordPress upload directory path
          $upload_dir = wp_upload_dir();
          $base_upload_path = trailingslashit($upload_dir['basedir']) . 'product-details/';

          // ✅ Use product code to create subfolder
          $safe_folder_name = sanitize_title($product_code);
          $product_subdir = trailingslashit($base_upload_path . $safe_folder_name);

          // ✅ If folder exists, delete all images inside
          if (file_exists($product_subdir)) {
            $existing_files = glob($product_subdir . '*');
            foreach ($existing_files as $existing_file) {
              if (is_file($existing_file)) {
                unlink($existing_file);
              }
            }
          } else {
            // ✅ Create subfolder if it doesn’t exist
            wp_mkdir_p($product_subdir);
          }

          $now = current_time('mysql');
          // NOTE: using as $index => $name is important.
          foreach ($files['name'] as $index => $name) {
            if ($files['error'][$index] == UPLOAD_ERR_OK) {

              $tmp_name = $files['tmp_name'][$index];
              $order = intval($orders[$index]);

              // Generate unique filename to prevent collision
              $extension = pathinfo($name, PATHINFO_EXTENSION);
              $new_filename = uniqid('product_', true) . '.' . $extension;

              // Full path to save the image
              $destination = $product_subdir . $new_filename;

              // Move uploaded file to custom uploads folder
              if (move_uploaded_file($tmp_name, $destination)) {

                // ✅ Store relative URL path in DB
                $image_path = 'product-details/' . $safe_folder_name . '/' . $new_filename;

                // Insert the image into the database
                $wpdb->insert(
                  $productImageTable,
                  [
                    'product_image_path' => $image_path,
                    'product_id' => $inserted_product_id, // Use the inserted product ID
                    'product_image_order' => $order,
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
              echo "File uploaded failed: " . $files['name'][$index];
              echo " Error Code: " . $files['error'][$index]; // This should give you the error code
              var_dump($files['error'][$index]); // Check the actual value and type
              var_dump(UPLOAD_ERR_OK);          // Verify UPLOAD_ERR_OK is an integer 0
              die();
            }
          }
        }

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

        // DB_INSERT: aboyuma_product_tag
        if (isset($_POST['product_tags']) && !empty($_POST['product_tags'])) {

          $tag_string = sanitize_text_field($_POST['product_tags']); // "tag1,tag2,tag3"

          // Split tags into array, trim spaces, remove empty entries
          $tags = array_filter(array_map('trim', explode(',', $tag_string)));

          $table = $wpdb->prefix . 'aboyuma_product_tag';

          //$wpdb->delete($table, ['product_id' => $inserted_product_id], ['%d']);

          // Insert each tag individually
          foreach ($tags as $tag_name) {
            $wpdb->insert(
              $table,
              [
                'product_id' => $inserted_product_id,
                'tag_name' => $tag_name,
                'created' => current_time('mysql'),
                'modified' => current_time('mysql')
              ],
              ['%d', '%s', '%s', '%s']
            );
          }
        }

        // DB_INSERT: aboyuma_related_product_video
        if (isset($_POST['video_datas']) && !empty($_POST['video_datas'])) {

          $table = $wpdb->prefix . 'aboyuma_related_product_video';
          $now = current_time('mysql');

          foreach ($_POST['video_datas'] as $video_data) {
            $video_url = esc_url_raw($video_data['video_url']);
            $video_comment = sanitize_text_field($video_data['video_comment']);

            $wpdb->insert($table, [
              'product_id'     => $inserted_product_id, // ← use your variable here
              'video_url'      => $video_url,
              'video_comment'  => $video_comment,
              'created'        => $now,
              'modified'       => $now
            ]);
          }
        }

        echo '<div class="notice notice-success is-dismissible"><p>Product added successfully!</p></div>';
        echo '<script>window.location.href="' . admin_url('admin.php?page=aboyuma-product-list') . '";</script>';
        exit; // Extra code from running unnecessarily.Additional HTML from being rendered.Unexpected side effects (like duplicate inserts, echoes, or further processing).
      } else {
        // Capture and display the error
        $error_message = $wpdb->last_error; // Get the last error message
        if (!empty($error_message)) {
          error_log('Database Insert Error: ' . $error_message); // Log the error for debugging
        }
        $errors[] = 'Failed to add product. Please try again.';
        echo '<div class="notice notice-error is-dismissible"><p>' . esc_html($error_message) . '</p></div>';
      }
    }
  }
  // Display errors if any
  if (!empty($errors)) {
    echo '<div class="notice notice-error is-dismissible"><ul>';
    foreach ($errors as $error) {
      echo '<li>' . esc_html($error) . '</li>';
    }
    echo '</ul></div>';
  }
  // Display the form
?>
  <div class="wrap">
    <h1 class="wp-heading-inline">Add New Product</h1>
    <hr class="wp-header-end">
    <form method="post" enctype="multipart/form-data">
      <table class="form-table">
        <!-- Product Image URL -->
        <tr>
          <th scope="row"><label for="product_image">Product Image</label></th>
          <td>
            <input type="file" name="product_image" id="product_image">
            <small style="display:block;">Main image of product.</small>
            <img id="product_image_preview" src="" alt="Product Image Preview" style="max-width: 200px; margin-top: 10px; display: none;">
          </td>
        </tr>
        <!-- Product Code -->
        <tr>
          <th scope="row"><label for="product_code">Product Code</label></th>
          <td>
            <input type="text" name="product_code" id="product_code" class="regular-text">
          </td>
        </tr>
        <!-- Product Brand -->
        <tr>
          <th scope="row"><label for="product_brand">Product Brand</label></th>
          <td>
            <select name="product_brand" id="product_brand" class="regular-text">
              <option value="">-- Select Brand --</option>
              <?php foreach ($brands as $brand) : ?>
                <option value="<?php echo esc_attr($brand->id); ?>"
                  <?php echo (isset($_POST['product_brand']) && $_POST['product_brand'] == $brand->id) ? 'selected' : ''; ?>>
                  <?php echo esc_html($brand->brand_name); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </td>
        </tr>
        <!-- Product Category -->
        <tr>
          <th scope="row"><label for="parent_category">Parent Category</label></th>
          <td>
            <select name="parent_category" id="parent_category" class="regular-text">
              <option value="">-- Select Parent Category --</option>
              <?php foreach ($categories as $category) :
                if ($category->parent_id == 0): ?>
                  <option value="<?php echo esc_attr($category->id); ?>">
                    <?php echo esc_html($category->category_name); ?>
                  </option>
              <?php endif;
              endforeach; ?>
            </select>
          </td>
        </tr>
        <!-- Product Sub Category -->
        <tr>
          <th scope="row"><label for="product_subcategory">Subcategory</label></th>
          <td>
            <select name="product_subcategory" id="product_subcategory" class="regular-text">
              <option value="">-- Select Subcategory --</option>
              <!-- AJAX-loaded options will go here -->
            </select>
          </td>
        </tr>
        <!-- Product Name -->
        <tr>
          <th scope="row"><label for="product_name">Product Name</label></th>
          <td>
            <input type="text" name="product_name" id="product_name" class="regular-text"
              value="<?php echo isset($_POST['product_name']) ? esc_attr($_POST['product_name']) : ''; ?>">
          </td>
        </tr>
        <!-- Product Unit Count -->
        <tr>
          <th scope="row"><label for="product_unit_count">Unit Count</label></th>
          <td>
            <input type="number" name="product_unit_count" id="product_unit_count" min="0"
              value="<?php echo isset($_POST['product_unit_count']) ? esc_attr($_POST['product_unit_count']) : ''; ?>"
              placeholder="e.g., 120, 30, 450">
          </td>
        </tr>
        <!-- Product Unit Type -->
        <tr>
          <th scope="row"><label for="product_unit_type">Unit Type</label></th>
          <td>
            <select name="product_unit_type" id="product_unit_type">
              <?php
              $types = ['tablet', 'capsule', 'sachet', 'g', 'ml'];
              $selected_type = isset($_POST['product_unit_type']) ? $_POST['product_unit_type'] : '';
              foreach ($types as $type) {
                echo '<option value="' . esc_attr($type) . '" ' . selected($selected_type, $type, false) . '>' . ucfirst($type) . '</option>';
              }
              ?>
            </select>
          </td>
        </tr>
        <!-- Product Short Description -->
        <tr>
          <th scope="row"><label for="product_short_description">Product Short Description</label></th>
          <td>
            <?php
            $product_short_description_value = isset($_POST['product_short_description'])
              ? $_POST['product_short_description'] : '';
            wp_editor($product_short_description_value, 'product_short_description', [
              'textarea_name' => 'product_short_description',
              'textarea_rows' => 5,
              'media_buttons' => true
            ]);
            ?>
          </td>
        </tr>
        <!-- Product Description -->
        <tr>
          <th scope="row"><label for="product_description">Product Description</label></th>
          <td>
            <?php
            $product_description = isset($_POST['product_description'])
              ? $_POST['product_description'] : '';
            wp_editor($product_description, 'product_description', [
              'textarea_name' => 'product_description',
              'textarea_rows' => 5,
              'media_buttons' => true
            ]);
            ?>
          </td>
        </tr>
        <!-- Product Benefit -->
        <tr>
          <th scope="row"><label for="product_benefit">Product Benefit</label></th>
          <td>
            <?php
            $product_benefit = isset($_POST['product_benefit'])
              ? $_POST['product_benefit'] : '';
            wp_editor($product_benefit, 'product_benefit', [
              'textarea_name' => 'product_benefit',
              'textarea_rows' => 5,
              'media_buttons' => true
            ]);
            ?>
          </td>
        </tr>
        <!-- Product Detail Images -->
        <tr>
          <th scope="row"><label for="product_images">Product Detail Images</label></th>
          <td>
            <div id="image-upload-container">
              <!-- Dynamic image fields will appear here -->
            </div>
            <button type="button" id="add-image-field">Add Image</button>
            <small>**Product detail images with order number to display in product detail page</small>
          </td>
        </tr>
        <!-- Product Detail (WYSIWYG editor) -->
        <tr>
          <th scope="row"><label for="product_detail">Product Detail</label></th>
          <td>
            <?php
            $product_detail = isset($_POST['product_detail'])
              ? $_POST['product_detail'] : '';
            wp_editor($product_detail, 'product_detail', [
              'textarea_name' => 'product_detail',
              'textarea_rows' => 20,
              'media_buttons' => true
            ]);
            ?>
          </td>
        </tr>
        <!-- Product Specification (WYSIWYG editor) -->
        <tr>
          <th scope="row"><label for="product_specification">Product Specification</label></th>
          <td>
            <?php
            $product_specification = isset($_POST['product_specification'])
              ? $_POST['product_specification'] : '';
            wp_editor($product_specification, 'product_specification', [
              'textarea_name' => 'product_specification',
              'textarea_rows' => 20,
              'media_buttons' => true
            ]);
            ?>
          </td>
        </tr>
        <!-- Product Comment (WYSIWYG editor) -->
        <tr>
          <th scope="row"><label for="product_comment">Product Comment</label></th>
          <td>
            <?php
            $product_comment = isset($_POST['product_comment'])
              ? $_POST['product_comment'] : '';
            wp_editor($product_comment, 'product_comment', [
              'textarea_name' => 'product_comment',
              'textarea_rows' => 10,
              'media_buttons' => true
            ]);
            ?>
          </td>
        </tr>
        <!-- Product additive properties -->
        <tr>
          <th scope="row">Product Additive Properties</th>
          <td>
            <label>
              <input type="checkbox" name="no_color" value="1" <?php checked(isset($_POST['no_color']) && $_POST['no_color'] == '1'); ?>>
              ไม่เจือสี
            </label><br>
            <label>
              <input type="checkbox" name="no_flavoring" value="1" <?php checked(isset($_POST['no_flavoring']) && $_POST['no_flavoring'] == '1'); ?>>
              ไม่ใช้วัตถุแต่งกลิ่นรส
            </label><br>
            <label>
              <input type="checkbox" name="no_preservative" value="1" <?php checked(isset($_POST['no_preservative']) && $_POST['no_preservative'] == '1'); ?>>
              ไม่ใช้วัตถุกันเสีย
            </label>
          </td>
        </tr>
        <!-- Product Diet Properties -->
        <tr>
          <th scope="row"><label for="product_diet_properties">Product Diet Properties</label></th>
          <td>
            <?php
            $diet_props = isset($_POST['product_diet_properties']) ? $_POST['product_diet_properties'] : [];
            ?>
            <label>
              <input type="checkbox" name="product_diet_properties[]" value="vegetarian"
                <?php echo in_array('vegetarian', $diet_props) ? 'checked' : ''; ?>> Vegetarian
            </label>
            <label>
              <input type="checkbox" name="product_diet_properties[]" value="vegan"
                <?php echo in_array('vegan', $diet_props) ? 'checked' : ''; ?>> Vegan
            </label>
            <label>
              <input type="checkbox" name="product_diet_properties[]" value="practical vegan"
                <?php echo in_array('practical vegan', $diet_props) ? 'checked' : ''; ?>> Practical Vegan
            </label>
          </td>
        </tr>
        <!-- Product Review (WYSIWYG editor) -->
        <tr>
          <th scope="row"><label for="product_review">Product Review</label></th>
          <td>
            <?php
            $product_review = isset($_POST['product_review'])
              ? $_POST['product_review'] : '';
            wp_editor($product_review, 'product_review', [
              'textarea_name' => 'product_review',
              'textarea_rows' => 20,
              'media_buttons' => true
            ]);
            ?>
          </td>
        </tr>
        <!-- Product Price -->
        <tr>
          <th scope="row"><label for="product_price">Product Price</label></th>
          <td>
            <input type="number" name="product_price" id="product_price"
              value="<?php echo isset($_POST['product_price']) ? esc_attr($_POST['product_price']) : ''; ?>">
          </td>
        </tr>
        <!-- Product Member Price -->
        <tr>
          <th scope="row"><label for="product_member_price">Product Member Price</label></th>
          <td>
            <input type="number" name="product_member_price" id="product_member_price"
              value="<?php echo isset($_POST['product_member_price']) ? esc_attr($_POST['product_member_price']) : ''; ?>">
          </td>
        </tr>
        <!-- Product is SOP -->
        <tr>
          <th scope="row"><label for="product_is_sop">Product is SOP</label></th>
          <td>
            <label>
              <input type="radio" name="product_is_sop" value="1"
                <?php echo (isset($_POST['product_is_sop']) && $_POST['product_is_sop'] == '1') ? 'checked' : ''; ?>>
              Yes
            </label>
            <label>
              <input type="radio" name="product_is_sop" value="0"
                <?php echo (isset($_POST['product_is_sop']) && $_POST['product_is_sop'] == '0') ? 'checked' : ''; ?>>
              No
            </label>
            <small style="display:block;">The product has SOP promotion.</small>
          </td>
        </tr>
        <!-- Product Shop Link -->
        <tr>
          <th scope="row"><label for="product_shop_link">Product Shop Link</label></th>
          <td>
            <input type="url" name="product_shop_link" id="product_shop_link" class="regular-text"
              value="<?php echo isset($_POST['product_shop_link']) ? esc_url($_POST['product_shop_link']) : ''; ?>">
            <small style="display:block;">Enter the URL where the product can be purchased (e.g., https://example.com/buy-product).</small>
          </td>
        </tr>
        <!-- Product SOP Link -->
        <tr>
          <th scope="row"><label for="product_sop_link">Product Sop Link</label></th>
          <td>
            <input type="url" name="product_sop_link" id="product_sop_link" class="regular-text"
              value="<?php echo isset($_POST['product_sop_link']) ? esc_url($_POST['product_sop_link']) : ''; ?>">
            <small style="display:block;">Enter the URL where the product SOP link.</small>
          </td>
        </tr>
        <!-- Product Sale Article Link -->
        <tr>
          <th scope="row"><label for="product_sale_article_link">Product Sale Article Link</label></th>
          <td>
            <input type="url" name="product_sale_article_link" id="product_sale_article_link" class="regular-text"
              value="<?php echo isset($_POST['product_sale_article_link']) ? esc_url($_POST['product_sale_article_link']) : ''; ?>">
            <small style="display:block;">Enter the URL of the product sale article.</small>
          </td>
        </tr>
        <!-- Product is pinned -->
        <tr>
          <th scope="row"><label for="product_is_pinned">Product is pinned</label></th>
          <td>
            <label>
              <input type="radio" name="product_is_pinned" value="1"
                <?php echo (isset($_POST['product_is_pinned']) && $_POST['product_is_pinned'] == '1') ? 'checked' : ''; ?>>
              Yes
            </label>
            <label>
              <input type="radio" name="product_is_pinned" value="0"
                <?php echo (isset($_POST['product_is_pinned']) && $_POST['product_is_pinned'] == '0') ? 'checked' : ''; ?>>
              No
            </label>
            <small style="display:block;">For display the product at the top of dataTable.</small>
          </td>
        </tr>
        <!-- Product Slug -->
        <tr>
          <th scope="row"><label for="product_slug">Product Slug</label></th>
          <td>
            <input type="text" name="product_slug" id="product_slug" class="regular-text">
          </td>
        </tr>
        <!-- Product Infographics -->
        <tr>
          <th scope="row"><label for="product_infographics">Product Infographic</label></th>
          <td>
            <div id="infographic-upload-container">
              <!-- Dynamic Infographic fields will appear here -->
            </div>
            <button type="button" id="add-infographic-field">Add Infographic</button>
            <small>**Product infographic with order number to display in product detail page</small>
          </td>
        </tr>
        <!-- Related Products -->
        <?php
        // Fetch all products
        $products = $wpdb->get_results("SELECT id, product_name 
        FROM {$wpdb->prefix}aboyuma_product 
        ORDER BY product_name ASC");
        // If form was submitted and failed, use submitted values instead
        $submitted_related = $_POST['relatedproduct'] ?? [];
        ?>
        <tr>
          <th><label for="related-products">Related Products</label></th>
          <td>
            <!-- Search Input -->
            <input
              type="text"
              id="related-product-search"
              placeholder="Search products..."
              style="margin-bottom: 10px; width: 100%; max-width: 400px; padding: 5px;">

            <!-- Fieldset for grouping -->
            <fieldset id="related-product-fieldset" style="border: none; padding: 0;">
              <div
                id="related-product-list"
                style="max-height: 300px; overflow-y: auto; border: 1px solid #ccc; padding: 10px;">

                <?php foreach ($products as $index => $product):
                  // Priority: submitted data > saved data
                  $submitted = $submitted_related[$index] ?? [];
                  $is_checked = isset($submitted['id']);
                  $comment_value = $submitted['comment'] ?? '';
                ?>
                  <div class="related-product-item"
                    data-name="<?= esc_attr(strtolower($product->product_name)); ?>"
                    style="margin-bottom: 15px;">
                    <label style="display:block; font-weight: bold;">
                      <input
                        type="checkbox"
                        class="related-checkbox"
                        name="relatedproduct[<?= $index ?>][id]"
                        value="<?= esc_attr($product->id); ?>" <?= $is_checked ? 'checked' : '' ?>>
                      <?= esc_html($product->product_name); ?>
                    </label>
                    <textarea
                      class="related-comment"
                      name="relatedproduct[<?= $index ?>][comment]"
                      rows="2"
                      style="width: 100%; max-width: 400px;"
                      <?= $is_checked ? '' : 'disabled' ?>><?= esc_textarea($comment_value); ?></textarea>
                  </div>
                <?php endforeach; ?>
              </div>
            </fieldset>
          </td>
        </tr>
        <!-- Product Tag -->
        <tr>
          <th scope="row"><label for="product_is_pinned">Product Tags</label></th>
          <td>
            <input type="text" id="product-tags" name="product_tags" class="regular-text" placeholder="Fill tag separate with comma.." />
          </td>
        </tr>
        <!-- Related Product Videos -->
        <tr>
          <th>Related Product Videos</th>
          <td style="padding: 0;">
            <table id="video-form-table">
              <tbody id="video-form-rows">
                <!-- No default row -->
              </tbody>
            </table>
            <p style="margin-top: 10px;">
              <button type="button" class="button" id="add-video-row">+ Add Row</button>
            </p>
          </td>
        </tr>
      </table>
      <?php wp_nonce_field('aboyuma_product_add', 'aboyuma_product_add_nonce'); ?>
      <button type="submit" name="submit" id="submit" class="button button-primary">Add Product</button>
      <button type="button" onclick="window.location.href='<?php echo admin_url('admin.php?page=aboyuma-product-list'); ?>'" class="button button-secondary">Cancel</button>
    </form>
  </div>

  <script type="text/javascript">
    jQuery(document).ready(function($) {
      // Preview for the main product image
      $('#product_image').on('change', function() {
        const file = this.files[0];
        const preview = $('#product_image_preview');
        if (file) {
          const reader = new FileReader();
          reader.onload = function(e) {
            preview.attr('src', e.target.result); // Set the image preview source
            preview.show(); // Show the image
          };
          reader.readAsDataURL(file); // Read the file as a data URL
        } else {
          preview.attr('src', ''); // Clear the image preview
          preview.hide(); // Hide the image
        }
      });

      // ===== Product Detail Images Script ===== //
      let imageCount = 0;

      // Add a new image input field for product detail images
      $('#add-image-field').on('click', function() {
        imageCount++;
        const newField = `
      <div class="image-preview-container" data-index="${imageCount}">
        <img src="" class="image-preview hidden" alt="Preview">
        <input type="file" name="product_images[]" class="product-image-input">
        <input type="number" name="product_image_orders[]" class="order-input" placeholder="">
        <span class="remove-button">Remove</span>
      </div>`;
        $('#image-upload-container').append(newField);
      });

      // Preview the selected detail images
      $(document).on('change', '.product-image-input', function() {
        const file = this.files[0];
        const preview = $(this).siblings('.image-preview');
        if (file) {
          const reader = new FileReader();
          reader.onload = function(e) {
            preview.attr('src', e.target.result); // Set the image preview source
            preview.removeClass('hidden'); // Show the image
          };
          reader.readAsDataURL(file); // Read the file as a data URL
        } else {
          preview.attr('src', ''); // Clear the image preview
          preview.addClass('hidden'); // Hide the image
        }
      });
      // Remove an image input field
      $(document).on('click', '.remove-button', function() {
        $(this).closest('.image-preview-container').remove();
      });
      // ===== End Product Detail Images Script ===== //

      // ===== Product Infographics Script ===== //
      let infographicCount = 0;

      // Add a new image input field for product infographics
      $('#add-infographic-field').on('click', function() {
        infographicCount++;
        const newField = `
      <div class="infographic-preview-container" data-index="${infographicCount}">
        <img src="" class="infographic-preview hidden" alt="Preview">
        <input type="file" name="product_infographics[]" class="product-infographic-input">
        <input type="number" name="product_infographic_orders[]" class="order-input" placeholder="">
        <span class="remove-button">Remove</span>
      </div>`;
        $('#infographic-upload-container').append(newField);
      });

      // Preview the selected infographics
      $(document).on('change', '.product-infographic-input', function() {
        const file = this.files[0];
        const preview = $(this).siblings('.infographic-preview');
        if (file) {
          const reader = new FileReader();
          reader.onload = function(e) {
            preview.attr('src', e.target.result); // Set the infographic preview source
            preview.removeClass('hidden'); // Show the infographic
          };
          reader.readAsDataURL(file); // Read the file as a data URL
        } else {
          preview.attr('src', ''); // Clear the infographic preview
          preview.addClass('hidden'); // Hide the infographic
        }
      });
      // Remove an infographic input field
      $(document).on('click', '.remove-button', function() {
        $(this).closest('.infographic-preview-container').remove();
      });
      // ===== End Product Infographic Script ===== //

      // HACK: remove selection of select option multiple selection
      $('.related-product-select-multiple option').on('mousedown', function(e) {
        this.selected = !this.selected;
        e.preventDefault();
      });

      // Script for related product field
      // Enable/disable textarea based on checkbox state
      $('.related-checkbox').on('change', function() {
        const $item = $(this).closest('.related-product-item');
        const $textarea = $item.find('.related-comment');
        $textarea.prop('disabled', !this.checked);
      });

      // On page load, set textarea state based on checkbox
      $('.related-product-item').each(function() {
        const $checkbox = $(this).find('.related-checkbox');
        const $textarea = $(this).find('.related-comment');
        $textarea.prop('disabled', !$checkbox.prop('checked'));
      });

      // Search filter
      $('#related-product-search').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        $('.related-product-item').each(function() {
          const name = $(this).data('name');
          $(this).toggle(name.includes(searchTerm));
        });
      });

      // Category selection
      $('#parent_category').on('change', function() {
        var parentId = $(this).val();
        var $subSelect = $('#product_subcategory');
        $subSelect.html('<option value="">Loading...</option>');

        $.ajax({
          url: ajaxurl,
          method: 'GET',
          data: {
            action: 'get_subcategories',
            parent_id: parentId
          },
          dataType: 'json',
          success: function(data) {
            var options = '<option value="">-- Select Subcategory --</option>';
            if (data.length > 0) {
              $.each(data, function(index, subcat) {
                options += '<option value="' + subcat.id + '">' + subcat.category_name + '</option>';
              });
            } else {
              options = '<option value="">No Subcategories Found</option>';
            }
            $subSelect.html(options);
          },
          error: function() {
            $subSelect.html('<option value="">Failed to load subcategories</option>');
          }
        });
      });

      // Related Product Video
      let videoIndex = $('#video-form-rows tr').length;

      $('#add-video-row').on('click', function() {
        const videoForm = `
        <tr>
          <td><input type="url" name="video_datas[${videoIndex}][video_url]" style="width: 300px;"></td>
          <td><input type="text" name="video_datas[${videoIndex}][video_comment]" maxlength="255" style="width: 200px;" placeholder="Video comment.."></td>
          <td><button type="button" class="button remove-row">✕</button></td>
        </tr>
        `;
        $('#video-form-rows').append(videoForm);
        videoIndex++;
      });

      $('#video-form-table').on('click', '.remove-row', function() {
        $(this).closest('tr').remove();
        // Re-number all remaining rows
        $('#video-form-rows tr').each(function(index) {
          $(this).find('input[name*="[video_url]"]').attr('name', `video_datas[${index}][video_url]`);
          $(this).find('input[name*="[video_comment]"]').attr('name', `video_datas[${index}][video_comment]`);
        });

        // Reset videoIndex
        videoIndex = $('#video-form-rows tr').length;
      });
    });
  </script>
  <style>
    .image-preview-container,
    .infographic-preview-container {
      display: flex;
      align-items: center;
      margin-bottom: 10px;
    }

    .image-preview,
    .infographic-preview {
      width: 100px;
      height: 100px;
      object-fit: cover;
      margin-right: 10px;
    }

    .order-input {
      width: 50px;
      margin-right: 10px;
    }

    .remove-button {
      margin-left: 10px;
      cursor: pointer;
    }
  </style>
<?php
}
