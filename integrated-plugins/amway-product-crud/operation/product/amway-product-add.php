<?php
defined('ABSPATH') || exit;

require_once get_template_directory() . '/inc/product/product-sanitize.php';
require_once get_template_directory() . '/inc/product/product-validate.php';

function aboyuma_amway_product_add() {
  // Add new product data page content
  global $wpdb;
  $table_name = $wpdb->prefix . 'aboyuma_amway_product';
  // Fetch brands and categories
  $productBrandTable = $wpdb->prefix . 'aboyuma_amway_product_brand';
  $productCategoryTable = $wpdb->prefix . 'aboyuma_amway_product_category';
  // Fetch brands
  $brands = $wpdb->get_results("SELECT id, brand_name FROM $productBrandTable");
  // Fetch categories
  $categories = $wpdb->get_results("SELECT id, category_name, parent_id FROM $productCategoryTable");

  if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['aboyuma_amway_product_add_nonce'])
    && wp_verify_nonce($_POST['aboyuma_amway_product_add_nonce'], 'aboyuma_amway_product_add')
  ) {

    $data = aboyuma_sanitize_product($_POST);

    $errors = aboyuma_validate_product($data, $table_name);

    if (!empty($errors)) {
      return;
    }

    $wpdb->query('START TRANSACTION');

    // =========================
    // INSERT PRODUCT
    // =========================
    $inserted = $wpdb->insert(
      $table_name,
      [
        'main_image_id'            => $data['main_image_id'],
        'product_code'             => $data['product_code'],
        'product_brand_id'         => $data['product_brand_id'] ?: null,
        'product_category_id'      => $data['product_category_id'] ?: null,
        'product_name'             => $data['product_name'],
        'product_unit_count'       => $data['product_unit_count'],
        'product_unit_type'        => $data['product_unit_type'],
        'product_unit_usage'       => $data['product_unit_usage'],
        'product_short_description' => $data['product_short_description'],
        'product_description'      => $data['product_description'],
        'product_benefit'          => $data['product_benefit'],
        'product_detail'           => $data['product_detail'],
        'product_specification'    => $data['product_specification'],
        'product_comment'          => $data['product_comment'],
        'product_review'           => $data['product_review'],
        'product_price'            => number_format($data['product_price'], 2, '.', ''),
        'product_member_price'     => number_format($data['product_member_price'], 2, '.', ''),
        'product_shop_link'        => $data['product_shop_link'],
        'product_sop_link'         => $data['product_sop_link'],
        'product_sale_article_link' => $data['product_sale_article_link'],
        'product_is_pinned'        => $data['product_is_pinned'],
        'product_slug'             => $data['product_slug'],
      ],
      [
        '%d',
        '%s',
        '%d',
        '%d',
        '%s',
        '%d',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%f',
        '%f',
        '%s',
        '%s',
        '%s',
        '%d',
        '%s'
      ]
    );

    if (!$inserted) {
      $wpdb->query('ROLLBACK');
      wp_die($wpdb->last_error);
    }

    $product_id = $wpdb->insert_id;

    // =========================
    // INSERT PROPERTIES
    // =========================
    if (!empty($_POST['product_properties'])) {

      $pivotTable = $wpdb->prefix . 'aboyuma_amway_product_property_pivot';
      $defTable = $wpdb->prefix . 'aboyuma_amway_product_property';

      foreach ($_POST['product_properties'] as $key) {

        $property = $wpdb->get_row(
          $wpdb->prepare("SELECT id FROM $defTable WHERE property_key=%s", $key)
        );

        if ($property) {
          $wpdb->query(
            $wpdb->prepare(
              "INSERT IGNORE INTO $pivotTable (product_id, property_id) VALUES (%d,%d)",
              $product_id,
              $property->id
            )
          );
        }
      }
    }

    // =========================
    // INSERT GALLERY
    // =========================
    if (!empty($_POST['gallery_image_ids'])) {

      $galleryTable = $wpdb->prefix . 'aboyuma_amway_product_image';

      foreach ($_POST['gallery_image_ids'] as $index => $attachment_id) {

        $wpdb->query(
          $wpdb->prepare(
            "INSERT IGNORE INTO $galleryTable (product_id, attachment_id, image_order)
           VALUES (%d,%d,%d)",
            $product_id,
            absint($attachment_id),
            intval($index)
          )
        );
      }
    }

    // =========================
    // INSERT TAGS
    // =========================
    if (!empty($_POST['product_tags'])) {

      $tagTable = $wpdb->prefix . 'aboyuma_amway_product_tag';
      $tags = array_filter(array_map('trim', explode(',', $_POST['product_tags'])));

      foreach ($tags as $tag) {
        $wpdb->query(
          $wpdb->prepare(
            "INSERT IGNORE INTO $tagTable (product_id, tag_name)
           VALUES (%d,%s)",
            $product_id,
            sanitize_text_field($tag)
          )
        );
      }
    }

    // =========================
    // INSERT VIDEOS
    // =========================
    if (!empty($_POST['video_datas'])) {

      $videoTable = $wpdb->prefix . 'aboyuma_amway_product_video';

      foreach ($_POST['video_datas'] as $video) {

        $video_url = esc_url_raw($video['video_url']);
        $video_comment = sanitize_text_field($video['video_comment']);

        if ($video_url) {
          $wpdb->insert(
            $videoTable,
            [
              'product_id' => $product_id,
              'video_url' => $video_url,
              'video_comment' => $video_comment
            ],
            ['%d', '%s', '%s']
          );
        }
      }
    }

    $wpdb->query('COMMIT');

    wp_redirect(admin_url('admin.php?page=aboyuma-product-list'));
    exit;
  }

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
