<?php
function aboyuma_product_edit() {
  global $wpdb;
  $table_name = $wpdb->prefix . 'aboyuma_product';

  // Check if product ID is provided
  $product_id = isset($_GET['id']) ? absint($_GET['id']) : 0;
  if (!$product_id) {
    echo '<div class="notice notice-error is-dismissible"><p>Invalid product ID.</p></div>';
  }
  // Define table names
  $productTable      = $wpdb->prefix . 'aboyuma_product';
  $brandTable        = $wpdb->prefix . 'aboyuma_product_brand';
  $categoryTable     = $wpdb->prefix . 'aboyuma_product_category';
  $tagTable          = $wpdb->prefix . 'aboyuma_product_tag';
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

  $productImageTable = $wpdb->prefix . 'aboyuma_product_image';
  $productDetailImages = $wpdb->get_results("
    SELECT id, product_image_path, product_image_order 
    FROM $productImageTable 
    WHERE product_id = $product_id
    ORDER BY product_image_order ASC
  ");

  $errors = [];

  // Handle form submission
  if (
    $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aboyuma_product_nonce'])
    && wp_verify_nonce($_POST['aboyuma_product_nonce'], 'aboyuma_product_edit')
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
    $product_name = sanitize_text_field(trim($_POST['product_name']));
    $product_short_description = wp_kses_post(trim($_POST['product_short_description']));
    $product_description = wp_kses_post(trim($_POST['product_description']));
    $product_benefit = wp_kses_post(trim($_POST['product_benefit']));
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
    $product_is_sop = intval($_POST['product_is_sop']);
    $product_shop_link = esc_url_raw(trim($_POST['product_shop_link']));
    $product_sop_link = esc_url_raw(trim($_POST['product_sop_link']));
    $product_sale_article_link = esc_url_raw(trim($_POST['product_sale_article_link']));
    $product_is_pinned = intval($_POST['product_is_pinned']);
    $product_slug = sanitize_text_field(trim($_POST['product_slug']));
    $modified = current_time('mysql');

    // Check if a file was uploaded
    if (empty($_FILES['product_image']['tmp_name'])) {
      $product_image = $product->product_image;
    } else {
      $image_tmp = $_FILES['product_image']['tmp_name'];
      $product_image = file_get_contents($image_tmp); // Convert the image to binary
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
          'modified' => $modified
        ],
        ['id' => $product_id],
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

        // Handle new image uploads
        // DB_UPDATE: Insert into aboyuma_product_images table
        // Note: Large files might fail silently if PHP or server upload size limits are exceeded,
        // so ensure 'upload_max_filesize', 'post_max_size', and server settings allow the file size.
        if (
          isset($_FILES['product_images']['name']) && is_array($_FILES['product_images']['name'])
          && !empty(array_filter($_FILES['product_images']['name']))
        ) {
          $files = $_FILES['product_images'];
          $orders = $_POST['product_image_orders'];

          // ✅ STEP 1: Get WordPress upload directory path
          $upload_dir = wp_upload_dir();
          $base_upload_path = trailingslashit($upload_dir['basedir']) . 'product-details/';

          // ✅ STEP 2: Use product code to create subfolder
          $safe_folder_name = sanitize_title($product_code);
          $product_subdir = trailingslashit($base_upload_path . $safe_folder_name);

          // ✅ STEP 3: Create subfolder if it doesn't exist
          if (!file_exists($product_subdir)) {
            error_log("ERROR: Product image folder missing");
          }

          $now = current_time('mysql');
          // NOTE: using => $name is important.
          foreach ($files['name'] as $index => $name) {
            if ($files['error'][$index] == UPLOAD_ERR_OK) {
              $tmp_name = $files['tmp_name'][$index];

              $order = intval($orders[$index]);

              // ✅ STEP 4: Generate a safe, unique filename
              $extension = pathinfo($name, PATHINFO_EXTENSION);
              $new_filename = uniqid('product_', true) . '.' . $extension;
              $destination = $product_subdir . $new_filename;

              if (move_uploaded_file($tmp_name, $destination)) {
                // ✅ STEP 5: Store relative path in DB
                $image_path = 'product-details/' . $safe_folder_name . '/' . $new_filename;

                // ✅ STEP 6: Insert into database
                // Insert the image into the database
                $wpdb->insert(
                  $productImageTable,
                  [
                    'product_image_path' => $image_path,
                    'product_id' => $product_id, // Use the inserted product ID
                    'product_image_order' => $order,
                    'created' => $now,
                    'modified' => $now,
                  ],
                  [
                    '%s',
                    '%d',
                    '%d',
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

        // Optional: Delete images if necessary
        if (!empty($_POST['deleted_image_ids'])) {
          $deletedImageIds = $_POST['deleted_image_ids'];
          foreach ($deletedImageIds as $imageId) {
            // ✅ Step 1: Get the image path
            $image = $wpdb->get_row(
              $wpdb->prepare("SELECT product_image_path FROM $productImageTable WHERE id = %d", $imageId)
            );
            if ($image) {
              $upload_dir = wp_upload_dir();
              $full_path = trailingslashit($upload_dir['basedir']) . $image->product_image_path;
              // ✅ Step 2: Delete the file if it exists
              if (file_exists($full_path)) {
                unlink($full_path);
              }
            }
            // ✅ Step 3: Delete the DB record
            $wpdb->delete(
              $productImageTable,
              ['id' => intval($imageId)],
              ['%d']
            );
          }
        }

        // DB_UPDATE: Add related product
        // On submit, filter only checked items
        // Clear old related products for this product_id
        $relatedProductTable = $wpdb->prefix . 'aboyuma_related_product';
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

        // DB_UPDATE: aboyuma_product_tag
        if (isset($_POST['product_tags']) && !empty($_POST['product_tags'])) {

          $tag_string = sanitize_text_field($_POST['product_tags']); // "tag1,tag2,tag3"

          // Split tags into array, trim spaces, remove empty entries
          $tags = array_filter(array_map('trim', explode(',', $tag_string)));

          $table = $wpdb->prefix . 'aboyuma_product_tag';

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

        // DB_UPDATE: aboyuma_related_product_video
        $relatedVideoTable = $wpdb->prefix . 'aboyuma_related_product_video';
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

        echo '<script>window.location.href="' . admin_url('admin.php?page=aboyuma-product-detail&updated-success=true&id=' . $product_id) . '";</script>';
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
          <th scope="row"><label for="product_image">Product Image</label></th>
          <td>
            <input type="file" name="product_image" id="product_image">
            <small style="display:block;">Main image of product.</small>
            <img id="product_image_preview" src="data:image/jpeg;base64,<?php echo base64_encode($product->product_image); ?>" alt="Product Image Preview" style="max-width: 200px; margin-top: 10px;">
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
        <!-- Product Short Description -->
        <tr>
          <th scope="row"><label for="product_short_description">Product Short Description</label></th>
          <td>
            <?php
            wp_editor($product->product_short_description, 'product_short_description', [
              'textarea_name' => 'product_short_description',
              'textarea_rows' => 5,
              'media_buttons' => true,
              'tinymce' => [
                'toolbar1' => 'bold italic underline | alignleft aligncenter alignright | bullist numlist outdent indent',
                'toolbar2' => 'link unlink | formatselect | blockquote code',
              ],
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
              'media_buttons' => true,
              'tinymce' => [
                'toolbar1' => 'bold italic underline | alignleft aligncenter alignright | bullist numlist outdent indent',
                'toolbar2' => 'link unlink | formatselect | blockquote code',
              ],
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
              'media_buttons' => true,
              'tinymce' => [
                'toolbar1' => 'bold italic underline | alignleft aligncenter alignright | bullist numlist outdent indent',
                'toolbar2' => 'link unlink | formatselect | blockquote code',
              ],
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
              <!-- Existing Images -->
              <?php
              $upload_dir = wp_upload_dir(); // ✅ Add before the loop
              $base_url = trailingslashit($upload_dir['baseurl']);
              foreach ($productDetailImages as $image) :
                $image_url = $base_url . $image->product_image_path;
                echo '<pre>' . $image_url . '</pre>';
              ?>
                <div class="image-preview-container" data-id="<?= $image->id; ?>">
                  <img src="<?= esc_url($image_url); ?>" class="image-preview">
                  <input type="number" name="existing_image_orders[]" value="<?= $image->product_image_order; ?>" class="order-input">
                  <input type="hidden" name="existing_image_ids[]" value="<?= $image->id; ?>">
                  <label><input type="checkbox" name="deleted_image_ids[]" value="<?= $image->id; ?>"> Delete</label>
                </div>
              <?php endforeach; ?>
            </div>
            <!-- New Image Uploads -->
            <button type="button" id="add-image-field">Add Image</button>
            <small>**Product detail images with order number to display in product detail page</small>
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
              'media_buttons' => true,
              'tinymce' => [
                'toolbar1' => 'bold italic underline | alignleft aligncenter alignright | bullist numlist outdent indent',
                'toolbar2' => 'link unlink | formatselect | blockquote code',
              ],
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
              'tinymce' => [
                'toolbar1' => 'bold italic underline | alignleft aligncenter alignright | bullist numlist outdent indent',
                'toolbar2' => 'link unlink | formatselect | blockquote code',
              ],
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
              'media_buttons' => true,
              'tinymce' => [
                'toolbar1' => 'bold italic underline | alignleft aligncenter alignright | bullist numlist outdent indent',
                'toolbar2' => 'link unlink | formatselect | blockquote code',
              ],
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
              <input type="checkbox" name="product_diet_properties[]" value="vegan" <?php echo in_array('vegan', $diet_props, true) ? 'checked' : ''; ?>>
              Vegan
            </label>
            <label>
              <input type="checkbox" name="product_diet_properties[]" value="practical vegan" <?php echo in_array('practical vegan', $diet_props, true) ? 'checked' : ''; ?>>
              Practical Vegan
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
              'media_buttons' => true,
              'tinymce' => [
                'toolbar1' => 'bold italic underline | alignleft aligncenter alignright | bullist numlist outdent indent',
                'toolbar2' => 'link unlink | formatselect | blockquote code',
              ],
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
        FROM {$wpdb->prefix}aboyuma_product 
        ORDER BY product_name ASC");

        // Get saved related products from DB
        $relatedProductTable = $wpdb->prefix . 'aboyuma_related_product';
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
        $relatedProductVideotable = $wpdb->prefix . 'aboyuma_related_product_video';
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
                } else {
                  ?>
                  <tr>
                    <td><input type="url" name="video_datas[video_1][video_url]" placeholder="https://www.youtube.com/watch?v=..." style="width: 300px;"></td>
                    <td><input type="text" name="video_datas[video_1][video_comment]" maxlength="255" style="width: 200px;" placeholder="Video comment.."></td>
                    <td><button type="button" class="button remove-row">✕</button></td>
                  </tr>
                <?php
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
      <?php wp_nonce_field('aboyuma_product_edit', 'aboyuma_product_nonce'); ?>
      <button type="submit" name="submit" id="submit" class="button button-primary">Update Product</button>
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
              options = '<option value="">-- No Subcategories Found --</option>';
            }
            $subSelect.html(options);
          },
          error: function() {
            $subSelect.html('<option value="">Failed to load subcategories</option>');
          }
        });
      });

      // Related Product Video
      let videoIndex = 1;

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
      });

    });
  </script>
  <style>
    .image-preview-container {
      display: flex;
      align-items: center;
      margin-bottom: 10px;
    }

    .image-preview {
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
