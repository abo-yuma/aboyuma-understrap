<?php
defined('ABSPATH') || exit;

function aboyuma_amway_product_edit() {
  global $wpdb;
  $table_name = $wpdb->prefix . 'aboyuma_amway_product';

  // Check if product ID is provided
  $product_id = isset($_GET['id']) ? absint($_GET['id']) : 0;
  if (!$product_id) {
    echo '<div class="notice notice-error is-dismissible"><p>Invalid product ID.</p></div>';
  }
  // Define table names
  $productTable      = $wpdb->prefix . 'aboyuma_amway_product';
  $brandTable        = $wpdb->prefix . 'aboyuma_amway_product_brand';
  $categoryTable     = $wpdb->prefix . 'aboyuma_amway_product_category';
  $tagTable          = $wpdb->prefix . 'aboyuma_amway_product_tag';
  // Get existing data for the product
  // Get product data including brand, category, and tags
  $product = $wpdb->get_row(
    $wpdb->prepare("
    SELECT 
      p.*, 
      b.brand_name, 
      c.category_name,
      GROUP_CONCAT(t.tag_name SEPARATOR ',') AS tags
    FROM 
      $productTable AS p
    LEFT JOIN 
      $brandTable AS b ON p.product_brand_id = b.id
    LEFT JOIN 
      $categoryTable AS c ON p.product_category_id = c.id
    LEFT JOIN 
      $tagTable AS t ON p.id = t.product_id
    WHERE 
      p.id = %d
    GROUP BY 
      p.id
  ", $product_id)
  );
  if (!$product) {
    echo '<div class="notice notice-error is-dismissible"><p>Product not found.</p></div>';
    return;
  }
  // Fetch brands and categories
  // Fetch brands
  $brands = $wpdb->get_results("SELECT id, brand_name FROM $brandTable");
  // Fetch all categories
  $categories = $wpdb->get_results("SELECT id, category_name, parent_id FROM $categoryTable");

  $selected_category_id = (int) $product->product_category_id;
  $parent_category_id = 0; // default

  // Find parent category of selected category
  foreach ($categories as $category) {
    if ((int) $category->id === $selected_category_id) {
      $parent_category_id = (int) $category->parent_id;
      break;
    }
  }

  // If the selected category is itself a parent (parent_id == 0),
  // then we consider that as the parent category, and subcategory is empty
  if ($parent_category_id === 0) {
    $parent_category_id = $selected_category_id;
    $subcategory_id = 0; // No subcategory selected
  } else {
    // Otherwise, selected category is a subcategory
    $subcategory_id = $selected_category_id;
  }

  $parentCategories = [];
  $subCategories = [];

  foreach ($categories as $category) {
    if ((int) $category->parent_id === 0) {
      $parentCategories[] = $category;
    }
  }

  if ($parent_category_id !== 0) {
    foreach ($categories as $category) {
      if ((int) $category->parent_id === $parent_category_id) {
        $subCategories[] = $category;
      }
    }
  }

  $productImageTable = $wpdb->prefix . 'aboyuma_amway_product_image';
  $productDetailImages = $wpdb->get_results(
    $wpdb->prepare("
    SELECT id, attachment_id, image_order
    FROM $productImageTable
    WHERE product_id = %d
    ORDER BY image_order ASC
  ", $product_id)
  );

  $errors = [];

  // Handle form submission
  if (
    $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aboyuma_amway_product_nonce'])
    && wp_verify_nonce($_POST['aboyuma_amway_product_nonce'], 'aboyuma_amway_product_edit')
  ) {
    // Sanitize and assign form data
    $product_code = sanitize_text_field(trim($_POST['product_code']));
    $product_brand_id = sanitize_text_field(trim($_POST['product_brand_id']));
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
    // Product Unit Usage (enum, sanitize and validate)
    $allowed_usage = ['content', 'capacity'];
    $product_unit_usage = isset($_POST['product_unit_usage']) && in_array($_POST['product_unit_usage'], $allowed_usage)
      ? sanitize_text_field($_POST['product_unit_usage'])
      : 'content'; // default

    $product_short_description = wp_kses_post(trim($_POST['product_short_description']));
    $product_description = wp_kses_post(trim($_POST['product_description']));
    $product_benefit = wp_kses_post(trim($_POST['product_benefit']));
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
    $product_is_sop = intval($_POST['product_is_sop']);
    $product_shop_link = esc_url_raw(trim($_POST['product_shop_link']));
    $product_sop_link = esc_url_raw(trim($_POST['product_sop_link']));
    $product_sale_article_link = esc_url_raw(trim($_POST['product_sale_article_link']));
    $product_is_pinned = intval($_POST['product_is_pinned']);
    $product_slug = sanitize_text_field(trim($_POST['product_slug']));
    $modified = current_time('mysql');

    $main_image_id = isset($_POST['main_image_id'])
      ? absint($_POST['main_image_id'])
      : null;
    // Validate main image
    if (empty($main_image_id)) {
      $errors[] = 'Main product image is required.';
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

    // Validate product short description
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
    // Validate product is SOP
    /*
    Validation for checkbox caution:
    empty() considers values like 0, false, '', null, and [] as empty.
    isset() checks whether the variable exists and is not null, 
    which is better for radio buttons when one of the possible values is 0.
    */
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
    if (empty($product_shop_link)) {
      $errors[] = 'Product Shop link is required.';
    } elseif (!filter_var($product_shop_link, FILTER_VALIDATE_URL)) {
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
    // If no errors, update the database
    if (empty($errors)) {
      $updated = $wpdb->update(
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
          'product_sop_link' => $product_sop_link,
          'product_shop_link' => $product_shop_link,
          'product_sale_article_link' => $product_sale_article_link,
          'product_is_pinned' => $product_is_pinned,
          'product_slug' => $product_slug,
          'modified' => $modified
        ],
        ['id' => $product_id],
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
          '%s', // product_sop_link
          '%s', // product_shop_link
          '%s', // product_sale_article_link
          '%d', // product_is_pinned
          '%s', // product_slug
          '%s' // modified
        ],
        ['%d']
      );
      // ===== DB_UPDATE: Update product detail images =====
      if ($updated !== false) {
        if (!empty($_POST['existing_image_ids'])) {
          $existingImageIds = $_POST['existing_image_ids'] ?? [];
          $imageOrders = $_POST['existing_image_orders'] ?? [];

          // Update existing images
          foreach ($existingImageIds as $index => $imageId) {
            $order = intval($imageOrders[$index]);

            // Update image order
            $wpdb->update(
              $productImageTable,
              [
                'product_image_order' => $order,
                'modified' => current_time('mysql'),
              ],
              ['id' => intval($imageId)], // Identify the row by its ID
              ['%d', '%s'],
              ['%d']
            );
          }
        }

        // DB_UPDATE: Add related product
        // On submit, filter only checked items
        // Clear old related products for this product_id
        $relatedProductTable = $wpdb->prefix . 'aboyuma_related_amway_product';
        $wpdb->delete($relatedProductTable, ['product_id' => $product_id]);
        if (!empty($_POST['relatedproduct']) && is_array($_POST['relatedproduct'])) {
          // Insert each related product
          foreach ($_POST['relatedproduct'] as $item) {
            if (isset($item['id']) && $item['id'] !== '') {
              $related_id = intval($item['id']);
              // Skip self insert
              if ($related_id === intval($product_id)) {
                continue;
              }
              $wpdb->insert(
                $relatedProductTable,
                [
                  'product_id'             => $product_id,
                  'related_product_id'     => intval($item['id']),
                  'related_product_comment' => sanitize_text_field($item['comment']),
                ],
                ['%d', '%d', '%s']
              );
            }
          }
        }

        // DB_UPDATE: aboyuma_amway_product_tag
        if (isset($_POST['product_tags']) && !empty($_POST['product_tags'])) {

          $tag_string = sanitize_text_field($_POST['product_tags']); // "tag1,tag2,tag3"

          // Split tags into array, trim spaces, remove empty entries
          $tags = array_filter(array_map('trim', explode(',', $tag_string)));

          $table = $wpdb->prefix . 'aboyuma_amway_product_tag';

          $wpdb->delete($table, ['product_id' => $product_id], ['%d']);

          // Insert each tag individually
          foreach ($tags as $tag_name) {
            $wpdb->insert(
              $table,
              [
                'product_id' => $product_id,
                'tag_name' => $tag_name,
                'created' => current_time('mysql'),
                'modified' => current_time('mysql')
              ],
              ['%d', '%s', '%s', '%s']
            );
          }
        }

        // DB_UPDATE: aboyuma_related_amway_product_video
        $relatedVideoTable = $wpdb->prefix . 'aboyuma_related_amway_product_video';
        $wpdb->delete($relatedVideoTable, ['product_id' => $product_id]);

        if (isset($_POST['video_datas']) && !empty($_POST['video_datas'])) {

          $now = current_time('mysql');

          foreach ($_POST['video_datas'] as $video_data) {
            $video_url = esc_url_raw($video_data['video_url']);
            $video_comment = sanitize_text_field($video_data['video_comment']);

            $wpdb->insert($relatedVideoTable, [
              'product_id'     => $product_id,
              'video_url'      => $video_url,
              'video_comment'  => $video_comment,
              'created'        => $now,
              'modified'       => $now
            ]);
          }
        }

        echo '<script>window.location.href="' . admin_url('admin.php?page=aboyuma-product-edit&updated-success=true&id=' . $product_id) . '";</script>';
        exit; // Extra code from running unnecessarily.Additional HTML from being rendered.Unexpected side effects (like duplicate inserts, echoes, or further processing).
      } else {
        $errors[] = 'Failed to update product. Please try again.';
        if (!empty($wpdb->last_error)) {
          exit(var_dump($wpdb->last_query));
        }
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
    <h1 class="wp-heading-inline">Edit Product</h1>
    <hr class="wp-header-end">

    <form method="post" enctype="multipart/form-data">
      <table class="form-table">
        <!-- Product Image -->
        <tr>
          <th scope="row"><label>Main Product Image</label></th>
          <td>
            <div id="main-image-wrapper">
              <?php if (!empty($product->main_image_id)) : ?>
                <?php echo wp_get_attachment_image($product->main_image_id, 'medium'); ?>
              <?php endif; ?>
            </div>

            <input type="hidden" name="main_image_id" id="main_image_id"
              value="<?php echo esc_attr($product->main_image_id); ?>">

            <button type="button" class="button" id="select-main-image">
              Select Image
            </button>

            <button type="button" class="button" id="remove-main-image">
              Remove
            </button>

            <p class="description">Use WordPress media library.</p>
          </td>
        </tr>

        <!-- Product Code -->
        <tr>
          <th scope="row"><label for="product_code">Product Code</label></th>
          <td>
            <input type="text" name="product_code" id="product_code" class="regular-text" value="<?php echo esc_attr($product->product_code); ?>" required>
          </td>
        </tr>
        <!-- Product Brand -->
        <tr>
          <th scope="row"><label for="product_brand">Product Brand</label></th>
          <td>
            <select name="product_brand_id" id="product_brand" class="regular-text" required>
              <option value="">-- Select Brand --</option>
              <?php foreach ($brands as $brand) : ?>
                <option value="<?php echo esc_attr($brand->id); ?>"
                  <?php selected($brand->id, $product->product_brand_id); ?>>
                  <?php echo esc_html($brand->brand_name); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </td>
        </tr>
        <!-- Parent Category -->
        <tr>
          <th scope="row"><label for="parent_category">Parent Category</label></th>
          <td>
            <select name="parent_category" id="parent_category" class="regular-text" required>
              <option value="">-- Select Parent Category --</option>
              <?php foreach ($parentCategories as $category): ?>
                <option value="<?php echo esc_attr($category->id); ?>"
                  <?php echo selected((int) $category->id, (int) $parent_category_id, false); ?>>
                  <?php echo esc_html($category->category_name); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </td>
        </tr>
        <!-- Subcategory -->
        <tr>
          <th scope="row"><label for="product_subcategory">Subcategory</label></th>
          <td>
            <select name="product_subcategory" id="product_subcategory" class="regular-text">
              <?php if (empty($subCategories)): ?>
                <option value="">-- No Subcategories Found --</option>
              <?php else: ?>
                <option value="">-- Select Subcategory --</option>
                <?php foreach ($subCategories as $category): ?>
                  <option value="<?php echo esc_attr($category->id); ?>"
                    <?php echo selected((int)$category->id, (int)$subcategory_id, false); ?>>
                    <?php echo esc_html($category->category_name); ?>
                  </option>
                <?php endforeach; ?>
              <?php endif; ?>
            </select>

          </td>
        </tr>
        <!-- Product Name -->
        <tr>
          <th scope="row"><label for="product_name">Product Name</label></th>
          <td>
            <input type="text" name="product_name" id="product_name" class="regular-text" value="<?php echo esc_attr($product->product_name); ?>" required>
          </td>
        </tr>
        <?php
        // Fetch current values from the database for the product
        $current_unit_count = isset($product->product_unit_count) ? $product->product_unit_count : '';
        $current_unit_type  = isset($product->product_unit_type) ? $product->product_unit_type : '';
        $current_unit_usage = isset($product->product_unit_usage) ? $product->product_unit_usage : 'content';
        ?>
        <!-- Product Unit Count -->
        <tr>
          <th scope="row"><label for="product_unit_count">Unit Count</label></th>
          <td>
            <input type="number" name="product_unit_count" id="product_unit_count" min="0"
              value="<?php echo esc_attr($current_unit_count); ?>"
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
              foreach ($types as $type) {
                echo '<option value="' . esc_attr($type) . '" ' . selected($current_unit_type, $type, false) . '>' . ucfirst($type) . '</option>';
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
              foreach ($usages as $value => $label) {
                echo '<option value="' . esc_attr($value) . '" ' . selected($current_unit_usage, $value, false) . '>' . esc_html($label) . '</option>';
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
            wp_editor($product->product_short_description, 'product_short_description', [
              'textarea_name' => 'product_short_description',
              'textarea_rows' => 5,
              'media_buttons' => true
            ]);
            ?>
            <small>**Paste in Text Mode: Paste the content in the Text mode of the editor, which will allow it to retain HTML tags and formatting.</small>
          </td>
        </tr>
        <!-- Product Description -->
        <tr>
          <th scope="row"><label for="product_description">Product Description</label></th>
          <td>
            <?php
            wp_editor($product->product_description, 'product_description', [
              'textarea_name' => 'product_description',
              'textarea_rows' => 5,
              'media_buttons' => true
            ]);
            ?>
            <small>**Paste in Text Mode: Paste the content in the Text mode of the editor, which will allow it to retain HTML tags and formatting.</small>
          </td>
        </tr>
        <!-- Product Benefit -->
        <tr>
          <th scope="row"><label for="product_benefit">Product Benefit</label></th>
          <td>
            <?php
            wp_editor($product->product_benefit, 'product_benefit', [
              'textarea_name' => 'product_benefit',
              'textarea_rows' => 5,
              'media_buttons' => true
            ]);
            ?>
            <small>**Paste in Text Mode: Paste the content in the Text mode of the editor, which will allow it to retain HTML tags and formatting.</small>
          </td>
        </tr>
        <!-- Product Detail Images -->
        <tr>
          <th scope="row"><label for="product_benefit">Product Detail Images</label></th>
          <td>
            <div id="image-upload-container">

              <?php foreach ($productDetailImages as $image): ?>

                <div class="image-preview-container" data-id="<?= esc_attr($image->id); ?>">

                  <div class="image-preview">
                    <?= wp_get_attachment_image($image->attachment_id, 'medium'); ?>
                  </div>

                  <input type="hidden"
                    name="existing_image_ids[]"
                    value="<?= esc_attr($image->id); ?>">

                  <input type="hidden"
                    name="existing_attachment_ids[]"
                    value="<?= esc_attr($image->attachment_id); ?>">

                  <label>Order:</label>
                  <input type="number"
                    name="existing_image_orders[]"
                    value="<?= esc_attr($image->image_order); ?>"
                    min="0">

                  <label>
                    <input type="checkbox"
                      name="deleted_image_ids[]"
                      value="<?= esc_attr($image->id); ?>">
                    Delete
                  </label>

                </div>
              <?php endforeach; ?>
            </div>
            <button type="button" class="button" id="add-image-field">
              + Add Image
            </button>
          </td>
        </tr>
        <!-- Product Detail (WYSIWYG editor) -->
        <tr>

          <th scope="row"><label for="product_detail">Product Detail</label></th>
          <td>
            <?php
            wp_editor($product->product_detail, 'product_detail', [
              'textarea_name' => 'product_detail',
              'textarea_rows' => 20,
              'media_buttons' => true
            ]);
            ?>
            <small>**Paste in Text Mode: Paste the content in the Text mode of the editor, which will allow it to retain HTML tags and formatting.</small>
          </td>
        </tr>
        <!-- Product Specification (WYSIWYG editor) -->
        <tr>
          <th scope="row"><label for="product_specification">Product Specification</label></th>
          <td>
            <?php
            wp_editor($product->product_specification, 'product_specification', [
              'textarea_name' => 'product_specification',
              'textarea_rows' => 20,
              'media_buttons' => true,
            ]);
            ?>
            <small>**Paste in Text Mode: Paste the content in the Text mode of the editor, which will allow it to retain HTML tags and formatting.</small>
          </td>
        </tr>
        <!-- Product Comment (WYSIWYG editor) -->
        <tr>
          <th scope="row"><label for="product_comment">Product Comment</label></th>
          <td>
            <?php
            $product_comment = isset($_POST['product_comment'])
              ? $_POST['product_comment'] : '';
            wp_editor($product->product_comment, 'product_comment', [
              'textarea_name' => 'product_comment',
              'textarea_rows' => 10,
              'media_buttons' => true
            ]);
            ?>
          </td>
        </tr>
        <!-- Product Additive Properties -->
        <?php
        // Decode diet properties JSON object from DB (associative array)
        $saved_additive_props_obj = json_decode($product->product_additive_properties ?? '{}', true);
        if (!is_array($saved_additive_props_obj)) {
          $saved_additive_props_obj = [];
        }

        // Prepare array of selected diet properties from POST or DB keys with true value
        if (isset($_POST['product_additive_properties']) && is_array($_POST['product_additive_properties'])) {
          $additive_props = $_POST['product_additive_properties'];
        } else {
          $additive_props = [];
          foreach ($saved_additive_props_obj as $key => $val) {
            if ($val) {
              $additive_props[] = $key;
            }
          }
        }
        ?>
        <tr>
          <th scope="row">Product Additive Properties</th>
          <td>
            <label>
              <input type="checkbox" name="product_additive_properties[]" value="no_color" <?php echo in_array('no_color', $additive_props, true) ? 'checked' : ''; ?>>
              ไม่เจือสี
            </label><br>
            <label>
              <input type="checkbox" name="product_additive_properties[]" value="no_flavoring" <?php echo in_array('no_flavoring', $additive_props, true) ? 'checked' : ''; ?>>
              ไม่ใช้วัตถุแต่งกลิ่นรส
            </label><br>
            <label>
              <input type="checkbox" name="product_additive_properties[]" value="no_preservative" <?php echo in_array('no_preservative', $additive_props, true) ? 'checked' : ''; ?>>
              ไม่ใช้วัตถุกันเสีย
            </label>
          </td>
        </tr>
        <!-- Product Diet Properties -->
        <?php
        // Decode diet properties JSON object from DB (associative array)
        $saved_diet_props_obj = json_decode($product->product_diet_properties ?? '{}', true);
        if (!is_array($saved_diet_props_obj)) {
          $saved_diet_props_obj = [];
        }

        // Prepare array of selected diet properties from POST or DB keys with true value
        if (isset($_POST['product_diet_properties']) && is_array($_POST['product_diet_properties'])) {
          $diet_props = $_POST['product_diet_properties'];
        } else {
          $diet_props = [];
          foreach ($saved_diet_props_obj as $key => $val) {
            if ($val) {
              $diet_props[] = $key;
            }
          }
        }
        ?>
        <tr>
          <th scope="row"><label for="product_diet_properties">Product Diet Properties</label></th>
          <td>
            <label>
              <input type="checkbox" name="product_diet_properties[]" value="vegetarian" <?php echo in_array('vegetarian', $diet_props, true) ? 'checked' : ''; ?>>
              Vegetarian
            </label>
            <label>
              <input type="checkbox" name="product_diet_properties[]" value="flexitarian" <?php echo in_array('flexitarian', $diet_props, true) ? 'checked' : ''; ?>>
              Flexitarian
            </label>
            <label>
              <input type="checkbox" name="product_diet_properties[]" value="vegan" <?php echo in_array('vegan', $diet_props, true) ? 'checked' : ''; ?>>
              Vegan
            </label>
            <label>
              <input type="checkbox" name="product_diet_properties[]" value="mostly_vegan" <?php echo in_array('mostly vegan', $diet_props, true) ? 'checked' : ''; ?>>
              Mostly Vegan
            </label>
          </td>
        </tr>
        <!-- Product Review (WYSIWYG editor) -->
        <tr>
          <th scope="row"><label for="product_review">Product Review</label></th>
          <td>
            <?php
            wp_editor($product->product_review, 'product_review', [
              'textarea_name' => 'product_review',
              'textarea_rows' => 20,
              'media_buttons' => true
            ]);
            ?>
            <small>**Paste in Text Mode: Paste the content in the Text mode of the editor, which will allow it to retain HTML tags and formatting.</small>
          </td>
        </tr>
        <!-- Product Price -->
        <tr>
          <th scope="row"><label for="product_price">Product Price</label></th>
          <td>
            <input type="number" name="product_price" id="product_price" value="<?php echo esc_attr($product->product_price); ?>" required>
          </td>
        </tr>
        <!-- Product Member Price -->
        <tr>
          <th scope="row"><label for="product_member_price">Product Member Price</label></th>
          <td>
            <input type="number" name="product_member_price" id="product_member_price" value="<?php echo esc_attr($product->product_member_price); ?>" required>
          </td>
        </tr>
        <!-- Product is SOP -->
        <tr>
          <th scope="row"><label for="product_is_sop">Product is SOP</label></th>
          <td>
            <label><input type="radio" name="product_is_sop" value="1" <?php echo ($product->product_is_sop == 1) ? 'checked' : ''; ?>>Yes</label>
            <label><input type="radio" name="product_is_sop" value="0" <?php echo ($product->product_is_sop == 0) ? 'checked' : ''; ?>>No</label>
            <small style="display:block;">The product has SOP promotion.</small>
          </td>
        </tr>
        <!-- Product Sop Link -->
        <tr>
          <th scope="row"><label for="product_sop_link">Product Sop Link</label></th>
          <td>
            <input type="url" name="product_sop_link" id="product_sop_link" class="regular-text" value="<?php echo esc_attr($product->product_sop_link); ?>">
          </td>
        </tr>
        <!-- Product Shop Link -->
        <tr>
          <th scope="row"><label for="product_shop_link">Product Shop Link</label></th>
          <td>
            <input type="url" name="product_shop_link" id="product_shop_link" class="regular-text" value="<?php echo esc_attr($product->product_shop_link); ?>" required>
            <p class="detail">Enter the URL where the product can be purchased (e.g., https://example.com/buy-product).</p>
          </td>
        </tr>
        <!-- Product Sale Article Link -->
        <tr>
          <th scope="row"><label for="product_sale_article_link">Product Sale Article Link</label></th>
          <td>
            <input type="url" name="product_sale_article_link" id="product_sale_article_link" class="regular-text" value="<?php echo esc_attr($product->product_sale_article_link); ?>">
            <p class="detail">Enter the URL of the product sale article.</p>
          </td>
        </tr>
        <!-- Product is pinned -->
        <tr>
          <th scope="row"><label for="product_is_pinned">Product is pinned</label></th>
          <td>
            <label><input type="radio" name="product_is_pinned" value="1" <?php echo ($product->product_is_pinned == 1) ? 'checked' : ''; ?>>Yes</label>
            <label><input type="radio" name="product_is_pinned" value="0" <?php echo ($product->product_is_pinned == 0) ? 'checked' : ''; ?>>No</label>
            <small style="display:block;">The product is pinned.</small>
          </td>
        </tr>
        <!-- Product Slug -->
        <tr>
          <th scope="row"><label for="product_slug">Product Slug</label></th>
          <td>
            <input type="text" name="product_slug" id="product_slug" class="regular-text" value="<?php echo esc_attr($product->product_slug); ?>" required>
          </td>
        </tr>
        <!-- Related dproduct -->
        <?php
        // Fetch all products
        $products = $wpdb->get_results("SELECT id, product_name 
        FROM {$wpdb->prefix}aboyuma_amway_product 
        ORDER BY product_name ASC");

        // Get saved related products from DB
        $relatedProductTable = $wpdb->prefix . 'aboyuma_related_amway_product';
        $related_products = $wpdb->get_results($wpdb->prepare(
          "SELECT related_product_id, related_product_comment 
          FROM $relatedProductTable WHERE product_id = %d",
          $product_id
        ));

        // Prepare saved related products for quick lookup
        $saved_related = [];
        foreach ($related_products as $item) {
          $saved_related[$item->related_product_id] = $item->related_product_comment;
        }

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

                <?php foreach ($products as $index => $candidate_product):
                  // Priority: submitted data > saved data
                  $submitted = $submitted_related[$index] ?? [];
                  $is_checked = isset($submitted['id']) || array_key_exists($candidate_product->id, $saved_related);
                  $comment_value = $submitted['comment'] ?? ($saved_related[$candidate_product->id] ?? '');
                ?>
                  <div class="related-product-item"
                    data-name="<?= esc_attr(strtolower($candidate_product->product_name)); ?>"
                    style="margin-bottom: 15px;">

                    <label style="display:block; font-weight: bold;">
                      <input
                        type="checkbox"
                        class="related-checkbox"
                        name="relatedproduct[<?= $index ?>][id]"
                        value="<?= esc_attr($candidate_product->id); ?>"
                        <?= $is_checked ? 'checked' : '' ?>>
                      <?= esc_html($candidate_product->product_name); ?>
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
          <th scope="row"><label for="product_tags">Product Tags</label></th>
          <td>
            <input
              type="text"
              id="product-tags"
              name="product_tags"
              class="regular-text"
              placeholder="tag1,tag2,tag3"
              value="<?php echo esc_attr($product->tags); ?>" />
          </td>
        </tr>
        <?php
        $relatedProductVideotable = $wpdb->prefix . 'aboyuma_related_amway_product_video';
        $videos = $wpdb->get_results(
          $wpdb->prepare("SELECT video_url, video_comment 
          FROM $relatedProductVideotable 
          WHERE product_id = %d", $product_id)
        );
        ?>
        <!-- Related Product Videos -->
        <tr>
          <th>Related Product Videos</th>
          <td style="padding: 0;">
            <table id="video-form-table">
              <tbody id="video-form-rows">
                <?php
                if (!empty($videos)) {
                  foreach ($videos as $i => $video) {
                    $key = 'video_' . ($i + 1);
                ?>
                    <tr>
                      <td>
                        <input type="url" name="video_datas[<?= $key ?>][video_url]" value="<?= esc_url($video->video_url) ?>" style="width: 300px;">
                      </td>
                      <td>
                        <input type="text" name="video_datas[<?= $key ?>][video_comment]" value="<?= esc_attr($video->video_comment) ?>" maxlength="255" style="width: 200px;" placeholder="Video comment..">
                      </td>
                      <td>
                        <button type="button" class="button remove-row">✕</button>
                      </td>
                    </tr>
                <?php
                  }
                }
                ?>
              </tbody>

            </table>
            <p style="margin-top: 10px;">
              <button type="button" class="button" id="add-video-row">+ Add Row</button>
            </p>
          </td>
        </tr>
      </table>
      <?php wp_nonce_field('aboyuma_amway_product_edit', 'aboyuma_amway_product_nonce'); ?>
      <button type="submit" name="submit" id="submit" class="button button-primary">Update Product</button>
      <button type="button" onclick="window.location.href='<?php echo admin_url('admin.php?page=aboyuma-product-list'); ?>'" class="button button-secondary">Cancel</button>
    </form>
  </div>
<?php
}
