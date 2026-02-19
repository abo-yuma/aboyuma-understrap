<?php
defined('ABSPATH') || exit;

function aboyuma_amway_product_add() {
  // Add new product data page content
  global $wpdb;
  $table_name = $wpdb->prefix . 'aboyuma_amway_product';
  // Fetch brands and categories
  $productBrandTable = $wpdb->prefix . 'aboyuma_amway_product_brand';
  $productCategoryTable = $wpdb->prefix . 'aboyuma_amway_product_category';
  $productImageTable = $wpdb->prefix . 'aboyuma_amway_product_image';
  // Fetch brands
  $brands = $wpdb->get_results("SELECT id, brand_name FROM $productBrandTable");
  // Fetch categories
  $categories = $wpdb->get_results("SELECT id, category_name, parent_id FROM $productCategoryTable");

  if (
    $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aboyuma_amway_product_add_nonce'])
    && wp_verify_nonce($_POST['aboyuma_amway_product_add_nonce'], 'aboyuma_amway_product_add')
  ) {

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

    $allowed_usage = ['content', 'capacity'];
    $product_unit_usage = isset($_POST['product_unit_usage']) && in_array($_POST['product_unit_usage'], $allowed_usage)
      ? sanitize_text_field($_POST['product_unit_usage'])
      : 'content'; // default

    $product_short_description = sanitize_textarea_field(trim($_POST['product_short_description']));
    $product_description = sanitize_textarea_field(trim($_POST['product_description']));
    $product_benefit = sanitize_textarea_field(trim($_POST['product_benefit']));
    $product_detail = wp_kses_post(trim($_POST['product_detail']));
    $product_specification = wp_kses_post(trim($_POST['product_specification']));
    $product_comment = wp_kses_post(trim($_POST['product_comment']));

    // =============================
    // Product Additive Properties
    // =============================

    $all_additive_options = ['no_color', 'no_flavoring', 'no_preservative'];

    $selected_additive_props = isset($_POST['product_additive_properties'])
      && is_array($_POST['product_additive_properties'])
      ? array_map('sanitize_text_field', $_POST['product_additive_properties'])
      : [];

    $product_additive_properties = [];

    foreach ($all_additive_options as $option) {
      $product_additive_properties[$option] = in_array($option, $selected_additive_props, true);
    }

    $product_additive_properties_json = wp_json_encode($product_additive_properties);


    // =============================
    // Product Diet Properties
    // =============================

    $all_diet_options = ['vegetarian', 'flexitarian', 'vegan', 'mostly_vegan'];

    $selected_diet_props = isset($_POST['product_diet_properties'])
      && is_array($_POST['product_diet_properties'])
      ? array_map('sanitize_text_field', $_POST['product_diet_properties'])
      : [];

    $product_diet_properties = [];

    foreach ($all_diet_options as $diet) {
      $product_diet_properties[$diet] = in_array($diet, $selected_diet_props, true);
    }

    $product_diet_properties_json = wp_json_encode($product_diet_properties);

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
    $main_image_id = isset($_POST['main_image_id']) ? absint($_POST['main_image_id']) : 0;

    if (empty($main_image_id)) {
      $errors[] = 'Main image is required.';
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
    // Validate Product Unit Usage
    $allowed_usage = ['content', 'capacity'];
    if (empty($product_unit_usage) || !in_array($product_unit_usage, $allowed_usage)) {
      $errors[] = 'Unit usage is required and must be content or capacity.';
    }
    if ($product_unit_usage === 'capacity' && $product_unit_type !== 'ml') {
      $errors[] = 'Unit usage "capacity" can only be used when unit type is ml.';
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
      // DB_INSERT: Insert into aboyuma_amway_product table
      $inserted = $wpdb->insert(
        $table_name,
        [
          'main_image_id' => $main_image_id,
          'product_code' => $product_code,
          'product_brand_id' => $product_brand_id,
          'product_category_id' => $product_category_id,
          'product_name' => $product_name,
          'product_unit_count' => $product_unit_count,
          'product_unit_type' => $product_unit_type,
          'product_unit_usage' => $product_unit_usage,
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
          '%d', // main_image_id
          '%s', // product_code
          '%d', // product_brand_id
          '%d', // product_category_id
          '%s', // product_name
          '%d', // product_unit_count
          '%s', // product_unit_type
          '%s', // product_unit_usage
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
      // Insert product success
      if ($inserted) {
        // Get the ID of the inserted product
        $inserted_product_id = $wpdb->insert_id;

        // =====================================
        // INSERT PRODUCT PROPERTIES (Pivot)
        // =====================================
        $productPropertyTable = $wpdb->prefix . 'aboyuma_amway_product_property';
        $productPropertyPivotTable = $wpdb->prefix . 'aboyuma_amway_product_property_pivot';

        // Collect selected property keys from form
        $selected_properties = isset($_POST['product_properties']) && is_array($_POST['product_properties'])
          ? array_map('sanitize_text_field', $_POST['product_properties'])
          : [];

        if (!empty($selected_properties)) {

          foreach ($selected_properties as $property_key) {

            // Get property id from definition table
            $property = $wpdb->get_row(
              $wpdb->prepare(
                "SELECT id FROM $productPropertyTable WHERE property_key = %s",
                $property_key
              )
            );

            if ($property) {
              $wpdb->insert(
                $productPropertyPivotTable,
                [
                  'product_id'  => $inserted_product_id,
                  'property_id' => $property->id
                ],
                ['%d', '%d']
              );
            }
          }
        }


        if (!empty($_POST['gallery_image_ids']) && is_array($_POST['gallery_image_ids'])) {

          $now = current_time('mysql');

          foreach ($_POST['gallery_image_ids'] as $index => $attachment_id) {

            $wpdb->insert(
              $productImageTable,
              [
                'product_id'    => $inserted_product_id,
                'attachment_id' => absint($attachment_id),
                'image_order'   => intval($index),
                'created'       => $now,
                'modified'      => $now,
              ],
              ['%d', '%d', '%d', '%s', '%s']
            );
          }
        }

        // DB_INSERT: aboyuma_amway_product_tag
        if (isset($_POST['product_tags']) && !empty($_POST['product_tags'])) {

          $tag_string = sanitize_text_field($_POST['product_tags']); // "tag1,tag2,tag3"

          // Split tags into array, trim spaces, remove empty entries
          $tags = array_filter(array_map('trim', explode(',', $tag_string)));

          $table = $wpdb->prefix . 'aboyuma_amway_product_tag';

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

        // DB_INSERT: aboyuma_amway_product_video
        if (isset($_POST['video_datas']) && !empty($_POST['video_datas'])) {

          $table = $wpdb->prefix . 'aboyuma_amway_product_video';
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
        <!-- Product Image -->
        <tr>
          <th>Main Image</th>
          <td>

            <input type="hidden" name="main_image_id" id="main_image_id" value="">

            <button type="button" class="button" id="select-main-image">
              Select / Replace Image
            </button>

            <div id="main-image-wrapper" class="amway-main-image" style="display:none;">
              <img id="main-image-preview" src="">
            </div>

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
        <!-- Product Unit Usage -->
        <tr id="unit-usage-row">
          <th scope="row"><label for="product_unit_usage">Unit Usage</label></th>
          <td>
            <select name="product_unit_usage" id="product_unit_usage">
              <?php
              $usages = [
                'content'  => 'บรรจุ / ปริมาณสินค้า',
                'capacity' => 'ความจุ (ภาชนะ)'
              ];
              $selected_usage = isset($_POST['product_unit_usage']) ? $_POST['product_unit_usage'] : 'content';
              foreach ($usages as $value => $label) {
                echo '<option value="' . esc_attr($value) . '" ' . selected($selected_usage, $value, false) . '>' . esc_html($label) . '</option>';
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
        <!-- Product Gallery Images -->
        <tr>
          <th>Product Gallery</th>
          <td>

            <button type="button" class="button" id="add-gallery-images">
              Add Gallery Images
            </button>

            <p class="description">
              Drag images to reorder. Click × to remove.
            </p>

            <div id="gallery-container" class="amway-gallery"></div>

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
        <!-- Product Properties -->
        <?php
        $productPropertyTable = $wpdb->prefix . 'aboyuma_amway_product_property';
        $properties = $wpdb->get_results("SELECT * FROM $productPropertyTable ORDER BY property_type, property_name");
        ?>
        <tr>
          <th scope="row">Product Properties</th>
          <td>
            <?php foreach ($properties as $prop): ?>
              <label style="display:block;">
                <input type="checkbox"
                  name="product_properties[]"
                  value="<?php echo esc_attr($prop->property_key); ?>">
                <?php echo esc_html($prop->property_name); ?>
                <small>(<?php echo esc_html($prop->property_type); ?>)</small>
              </label>
            <?php endforeach; ?>
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
      <?php wp_nonce_field('aboyuma_amway_product_add', 'aboyuma_amway_product_add_nonce'); ?>
      <button type="submit" name="submit" id="submit" class="button button-primary">Add Product</button>
      <button type="button" onclick="window.location.href='<?php echo admin_url('admin.php?page=aboyuma-product-list'); ?>'" class="button button-secondary">Cancel</button>
    </form>
  </div>
<?php
}
