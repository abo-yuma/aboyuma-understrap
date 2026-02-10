<?php
defined('ABSPATH') || exit;

function aboyuma_amway_product_brand_edit() {
  global $wpdb;
  $table_name = $wpdb->prefix . 'aboyuma_amway_product_brand';

  // Check if product ID is provided
  $brand_id = isset($_GET['id']) ? absint($_GET['id']) : 0;
  if (!$brand_id) {
    echo '<div class="notice notice-error is-dismissible"><p>Invalid brand ID.</p></div>';
    return;
  }
  // Get existing data for the brand
  $brand = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $brand_id));
  if (!$brand) {
    echo '<div class="notice notice-error is-dismissible"><p>Brand not found.</p></div>';
    return;
  }

  // Initialize variables
  $brand_image_url = $brand->brand_image_url;
  $brand_name = $brand->brand_name;
  $errors = [];

  // Handle form submission
  if (
    $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aboyuma_amway_product_brand_edit_nonce'])
    && wp_verify_nonce($_POST['aboyuma_amway_product_brand_edit_nonce'], 'aboyuma_amway_product_brand_edit')
  ) {

    // Sanitize and assign form data
    $brand_image_url = esc_url_raw(trim($_POST['brand_image_url']));
    $brand_name = sanitize_text_field(trim($_POST['brand_name']));
    $modified = current_time('mysql');

    // Validate fields
    if (empty($brand_image_url)) {
      $errors[] = 'Brand image URL is required.';
    } elseif (!filter_var($brand_image_url, FILTER_VALIDATE_URL)) {
      $errors[] = 'Brand image URL must be a valid URL.';
    }
    if (empty($brand_name)) {
      $errors[] = 'Brand name is required.';
    }
    // If no errors, update the database
    if (empty($errors)) {
      $result = $wpdb->update(
        $table_name,
        [
          'brand_image_url'   => $brand_image_url,
          'brand_name'        => $brand_name,
          'modified'          => $modified
        ],
        ['id' => $brand_id],
        ['%s', '%s', '%s'],
        ['%d']
      );
      if ($result !== false) {
        echo '<script>window.location.href="' . admin_url('admin.php?page=aboyuma-product-brand-list&updated-success=true') . '";</script>';
        exit;
      } else {
        $errors[] = 'Failed to update brand. Please try again.';
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
    <h1 class="wp-heading-inline">Edit Brand</h1>
    <hr class="wp-header-end">
    <form method="post">
      <table class="form-table">
        <!-- Brand Image -->
        <tr>
          <th scope="row"><label for="brand_image">Brand Image</label></th>
          <td>
            <img src="<?php echo esc_url($brand_image_url); ?>" width="250px">
          </td>
        </tr>
        <!-- Brand Image URL -->
        <tr>
          <th scope="row"><label for="brand_image_url">Brand Image URL</label></th>
          <td>
            <input type="url" name="brand_image_url" id="brand_image_url" class="regular-text" value="<?php echo esc_attr($brand_image_url); ?>" required>
            <p class="detail">Enter a valid URL pointing to the brand image (e.g., https://example.com/image.jpg).</p>
          </td>
        </tr>
        <!-- Brand Name -->
        <tr>
          <th scope="row"><label for="brand_name">Brand Name</label></th>
          <td>
            <input type="text" name="brand_name" id="brand_name" class="regular-text" value="<?php echo esc_attr($brand_name); ?>" required>
          </td>
        </tr>
      </table>
      <?php wp_nonce_field('aboyuma_amway_product_brand_edit', 'aboyuma_amway_product_brand_edit_nonce'); ?>
      <button type="submit" name="submit" id="submit" class="button button-primary">Update Brand</button>
      <button type="button" onclick="window.history.back();" class="button button-secondary">Cancel</button>
    </form>
  </div>
<?php
}
