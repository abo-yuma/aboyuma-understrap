<?php
// Planned for removal.
// Since the edit page already contains the updated data, a separate detail page
function aboyuma_amway_product_detail() {
  global $wpdb;
  // Check if product ID is provided
  $product_id = isset($_GET['id']) ? absint($_GET['id']) : 0;
  if (!$product_id) {
    echo '<div class="notice notice-error is-dismissible"><p>Invalid product ID.</p></div>';
    return;
  }
  if (isset($_GET['updated-success']) && $_GET['updated-success'] === 'true') {
    echo '<div class="notice notice-success is-dismissible">
            <p>Product updated successfully!</p>
          </div>';
  }
  // Get existing data for the product
  $product_table_name = $wpdb->prefix . 'aboyuma_amway_product';
  $productBrandTable = $wpdb->prefix . 'aboyuma_amway_product_brand';
  $productCategoryTable = $wpdb->prefix . 'aboyuma_amway_product_category';
  $productTagTable = $wpdb->prefix . 'aboyuma_amway_product_tag';
  $sql = $wpdb->prepare("
    SELECT 
      p.*, 
      b.brand_name, 
      c.category_name,
      GROUP_CONCAT(t.tag_name SEPARATOR ',') AS tags
    FROM 
      $product_table_name AS p
    LEFT JOIN 
      $productBrandTable AS b ON p.product_brand_id = b.id
    LEFT JOIN 
      $productCategoryTable AS c ON p.product_category_id = c.id
    LEFT JOIN 
      $productTagTable AS t ON p.id = t.product_id
    WHERE
      p.id = %d
    GROUP BY p.id
  ", $product_id);
  $product = $wpdb->get_row($sql);

  if (!$product) {
    echo '<div class="notice notice-error is-dismissible"><p>Product not found.</p></div>';
    return;
  }
  $productImageTable = $wpdb->prefix . 'aboyuma_amway_product_image';
  $productDetailImages = $wpdb->get_results("SELECT * FROM $productImageTable WHERE product_id = $product_id");
  $productInfographicTable = $wpdb->prefix . 'aboyuma_amway_product_infographic';
  $productInfographics = $wpdb->get_results("SELECT * FROM $productInfographicTable WHERE product_id = $product_id");

  // Display the detail
?>
  <div class="wrap">
    <h1 class="wp-heading-inline">Product Detail</h1>
    <hr class="wp-header-end">

    <form method="post">
      <table class="form-table">
        <!-- Product Image -->
        <tr>
          <th scope="row"><label for="product_image">Product Image</label></th>
          <td>
            <img src="data:image/jpeg;base64,<?php echo base64_encode($product->product_image); ?>" width="200" height="auto">
          </td>
        </tr>
        <!-- Product Brand -->
        <tr>
          <th scope="row"><label for="product_brand">Product Brand</label></th>
          <td>
            <?php echo esc_html($product->brand_name); ?>
          </td>
        </tr>
        <!-- Product Category -->
        <tr>
          <th scope="row"><label for="product_category">Product Category</label></th>
          <td>
            <?php echo esc_html($product->category_name); ?>
          </td>
        </tr>
        <!-- Product Name -->
        <tr>
          <th scope="row"><label for="product_name">Product Name</label></th>
          <td>
            <?php echo esc_attr($product->product_name); ?>
          </td>
        </tr>
        <!-- Product Unit Count -->
        <tr>
          <th scope="row"><label>Unit Count</label></th>
          <td>
            <?php echo esc_html($product->product_unit_count); ?>
          </td>
        </tr>
        <!-- Product Unit Type -->
        <tr>
          <th scope="row"><label>Unit Type</label></th>
          <td>
            <?php echo esc_html($product->product_unit_type); ?>
          </td>
        </tr>
        <!-- Product Short Description -->
        <tr>
          <th scope="row"><label for="product_short_description">Product Short Description</label></th>
          <td>
            <?php
            /*
            apply_filters() is a WordPress function 
            that applies all hooks (or filters) to a given piece of data.
            the_content filter is one of WordPress's built-in filters 
            specifically for post content (or any similar content).
            */
            echo apply_filters('the_content', $product->product_short_description);
            ?>
          </td>
        </tr>
        <!-- Product Description -->
        <tr>
          <th scope="row"><label for="product_description">Product Description</label></th>
          <td>
            <?php
            /*
            apply_filters() is a WordPress function 
            that applies all hooks (or filters) to a given piece of data.
            the_content filter is one of WordPress's built-in filters 
            specifically for post content (or any similar content).
            */
            echo apply_filters('the_content', $product->product_description);
            ?>
          </td>
        </tr>
        <!-- Product Benefit -->
        <tr>
          <th scope="row"><label for="product_benefit">Product Benefit</label></th>
          <td>
            <?php
            echo apply_filters('the_content', $product->product_benefit);
            ?>
          </td>
        </tr>
        <!-- Product Detail Images -->
        <tr>
          <th scope="row"><label for="product_image">Product Detail Images</label></th>
          <td>
            <div id="image-upload-container">
              <!-- Existing Images -->
              <?php foreach ($productDetailImages as $image) : ?>
                <div class="image-preview-container" data-id="<?= $image->id; ?>">
                  <img src="<?= esc_url(wp_upload_dir()['baseurl'] . '/' . $image->product_image_path); ?>" class="image-preview">
                  <input type="number" name="existing_image_orders[]" value="<?= $image->product_image_order; ?>" class="order-input" disabled>
                </div>
              <?php endforeach; ?>
            </div>
          </td>
        </tr>
        <!-- Product Detail (WYSIWYG editor) -->
        <tr>
          <th scope="row"><label for="product_detail">Product Detail</label></th>
          <td>
            <?php
            echo apply_filters('the_content', $product->product_detail);
            ?>
          </td>
        </tr>
        <!-- Product Specification (WYSIWYG editor) -->
        <tr>
          <th scope="row"><label for="product_specification">Product Specification</label></th>
          <td>
            <?php
            echo apply_filters('the_content', $product->product_specification);
            ?>
          </td>
        </tr>
        <!-- Product Comment (WYSIWYG editor) -->
        <tr>
          <th scope="row"><label for="product_comment">Product Comment</label></th>
          <td>
            <?php
            echo apply_filters('the_content', $product->product_comment);
            ?>
          </td>
        </tr>
        <!-- Product Additive Properties -->
        <?php
        $saved_additive_props_obj = json_decode($product->product_additive_properties ?? '{}', true);
        if (!is_array($saved_additive_props_obj)) {
          $saved_additive_props_obj = [];
        }
        ?>
        <tr>
          <th scope="row">Product Additive Properties</th>
          <td>
            <label>
              <input type="checkbox" disabled <?php echo (!empty($saved_additive_props_obj['no_color'])) ? 'checked' : ''; ?>>
              ไม่เจือสี
            </label><br>
            <label>
              <input type="checkbox" disabled <?php echo (!empty($saved_additive_props_obj['no_flavoring'])) ? 'checked' : ''; ?>>
              ไม่ใช้วัตถุแต่งกลิ่นรส
            </label><br>
            <label>
              <input type="checkbox" disabled <?php echo (!empty($saved_additive_props_obj['no_preservative'])) ? 'checked' : ''; ?>>
              ไม่ใช้วัตถุกันเสีย
            </label>
          </td>
        </tr>
        <!-- Product Diet Properties -->
        <?php
        $saved_diet_props_obj = json_decode($product->product_diet_properties ?? '{}', true);
        if (!is_array($saved_diet_props_obj)) {
          $saved_diet_props_obj = [];
        }
        ?>
        <tr>
          <th scope="row">Product Diet Properties</th>
          <td>
            <label>
              <input type="checkbox" disabled <?php echo (!empty($saved_diet_props_obj['vegetarian'])) ? 'checked' : ''; ?>>
              Vegetarian
            </label>
            <label>
              <input type="checkbox" disabled <?php echo (!empty($saved_diet_props_obj['flexitarian'])) ? 'checked' : ''; ?>>
              Flexitarian
            </label>
            <label>
              <input type="checkbox" disabled <?php echo (!empty($saved_diet_props_obj['vegan'])) ? 'checked' : ''; ?>>
              Vegan
            </label>
            <label>
              <input type="checkbox" disabled <?php echo (!empty($saved_diet_props_obj['mostly vegan'])) ? 'checked' : ''; ?>>
              Mostly Vegan
            </label>
          </td>
        </tr>
        <!-- Product Review (WYSIWYG editor) -->
        <tr>
          <th scope="row"><label for="product_review">Product Review</label></th>
          <td>
            <?php
            echo apply_filters('the_content', $product->product_review);
            ?>
          </td>
        </tr>
        <!-- Product Price -->
        <tr>
          <th scope="row"><label for="product_price">Product Price</label></th>
          <td>
            <?php echo esc_attr($product->product_price); ?>
          </td>
        </tr>
        <!-- Product Member Price -->
        <tr>
          <th scope="row"><label for="product_member_price">Product Member Price</label></th>
          <td>
            <?php echo esc_attr($product->product_member_price); ?>
          </td>
        </tr>
        <!-- Product is SOP -->
        <tr>
          <th scope="row"><label for="product_is_sop">Product is SOP</label></th>
          <td>
            <label><input type="radio" name="product_is_sop" value="1" <?php echo ($product->product_is_sop == 1) ? 'checked' : ''; ?> disabled>Yes</label>
            <label><input type="radio" name="product_is_sop" value="0" <?php echo ($product->product_is_sop == 0) ? 'checked' : ''; ?> disabled>No</label>
          </td>
        </tr>
        <!-- Product Sop Link -->
        <tr>
          <th scope="row"><label for="product_sop_link">Product Sop Link</label></th>
          <td>
            <a href="<?php echo esc_attr($product->product_sop_link); ?>" target="_blank"><?php echo esc_attr($product->product_sop_link); ?></a>
          </td>
        </tr>
        <!-- Product Shop Link -->
        <tr>
          <th scope="row"><label for="product_shop_link">Product Shop Link</label></th>
          <td>
            <a href="<?php echo esc_attr($product->product_shop_link); ?>" target="_blank"><?php echo esc_attr($product->product_shop_link); ?></a>
          </td>
        </tr>
        <!-- Product Sale Article Link -->
        <tr>
          <th scope="row"><label for="product_sale_article_link">Product Sale Article Link</label></th>
          <td>
            <?php echo $product->product_sale_article_link; ?>
          </td>
        </tr>
        <!-- Product is pinned -->
        <tr>
          <th scope="row"><label for="product_is_pinned">Product is pinned</label></th>
          <td>
            <label><input type="radio" name="product_is_pinned" value="1" <?php echo ($product->product_is_pinned == 1) ? 'checked' : ''; ?> disabled>Yes</label>
            <label><input type="radio" name="product_is_pinned" value="0" <?php echo ($product->product_is_pinned == 0) ? 'checked' : ''; ?> disabled>No</label>
          </td>
        </tr>
        <!-- Product Infographic -->
        <tr>
          <th scope="row"><label for="product_infographic">Product Infographic</label></th>
          <td>
            <div id="infographic-upload-container">
              <!-- Existing Infographics -->
              <?php foreach ($productInfographics as $infographic) : ?>
                <div class="infographic-preview-container" data-id="<?= $infographic->id; ?>">
                  <img src="<?= esc_url(wp_upload_dir()['baseurl'] . '/' . $infographic->product_infographic_path); ?>" class="infographic-preview">
                  <input type="number" name="existing_infographic_orders[]" value="<?= $infographic->product_infographic_order; ?>" class="order-input" disabled>
                </div>
              <?php endforeach; ?>
            </div>
          </td>
        </tr>
        <!-- Related product -->
        <?php
        $related_products = $wpdb->get_results($wpdb->prepare(
          "
        SELECT p.id, p.product_name
        FROM {$wpdb->prefix}aboyuma_related_amway_product rp
        JOIN {$wpdb->prefix}aboyuma_amway_product p ON rp.related_product_id = p.id
        WHERE rp.product_id = %d
        ORDER BY p.product_name ASC
        ",
          $product_id
        ));
        ?>
        <tr>
          <th scope="row">Related Products</th>
          <td>
            <?php if (!empty($related_products)) : ?>
              <ul style="margin:0; padding-left: 1.2em;">
                <?php foreach ($related_products as $product): ?>
                  <li><?= esc_html($product->product_name); ?></li>
                <?php endforeach; ?>
              </ul>
            <?php else: ?>
              <em>No related products.</em>
            <?php endif; ?>
          </td>
        </tr>
        <!-- Product Tag -->
        <tr>
          <th scope="row"><label for="product_is_pinned">Product Tags</label></th>
          <td>
            <?php
            if (!empty($product->tags)) {
              // Split tags by comma and trim whitespace
              $tags = array_map('trim', explode(',', $product->tags));
              foreach ($tags as $tag) {
                // Output each tag with a # prefix, styled as a badge or simple span
                echo '<span style="margin-right:8px;">#' . esc_html($tag) . '</span>';
              }
            } else {
              echo '<em>No tags</em>';
            }
            ?>
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
              <tbody>
                <?php
                if (!empty($videos)) {
                  foreach ($videos as $i => $video) {
                    $video_url = $video->video_url;
                    $video_comment = $video->video_comment;

                    // Extract YouTube video ID
                    preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\s]+)/', $video_url, $matches);
                    $video_id = $matches[1] ?? null;
                ?>
                    <tr>
                      <td colspan="3">
                        <?php if ($video_id): ?>
                          <div style="margin-bottom: 10px;">
                            <iframe width="400" height="225" src="https://www.youtube.com/embed/<?= esc_attr($video_id) ?>" frameborder="0" allowfullscreen></iframe>
                            <?php if (!empty($video_comment)): ?>
                              <p style="margin: 5px 0 15px; font-style: italic;"><?= esc_html($video_comment) ?></p>
                            <?php endif; ?>
                          </div>
                        <?php else: ?>
                          <p>Invalid video URL: <?= esc_html($video_url) ?></p>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php
                  }
                } else {
                  ?>
                  <tr>
                    <td colspan="3">
                      <p>No related product videos.</p>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>
            </table>
            <p style="margin-top: 10px;">
              <button type="button" class="button" id="add-video-row">+ Add Row</button>
            </p>
          </td>
        </tr>
      </table>
      <button type="button" onclick="window.location.href='<?php echo admin_url('admin.php?page=aboyuma-product-edit&id=' . $product_id); ?>';" class="button button-primary">Edit</button>
      <button type="button" onclick="window.location.href='<?php echo admin_url('admin.php?page=aboyuma-product-list'); ?>'" class="button button-secondary">Back</button>
    </form>
  </div>
  <style>
    .image-preview,
    .infographic-preview {
      width: 100px;
      height: 100px;
      object-fit: cover;
      margin-right: 10px;
    }
  </style>
<?php
}
