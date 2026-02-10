<?php
defined('ABSPATH') || exit;

function aboyuma_amway_product_brand_add() {
  // Add new product data page content
  global $wpdb;
  $table_name = $wpdb->prefix . 'aboyuma_amway_product_brand';

  // Initialize variables
  $brand_image_url = '';
  $brand_name = '';
  $errors = [];

  if (
    $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aboyuma_amway_product_brand_add_nonce'])
    && wp_verify_nonce($_POST['aboyuma_amway_product_brand_add_nonce'], 'aboyuma_amway_product_brand_add')
  ) {
    // Sanitize and assign form data
    $brand_image_url = esc_url_raw(trim($_POST['brand_image_url']));
    $brand_name = sanitize_text_field(trim($_POST['brand_name']));
    $created = current_time('mysql');
    $modified = current_time('mysql');

    // Validate fields
    // Validate Product Image URL
    if (empty($brand_image_url)) {
      $errors[] = 'Brand image URL is required.';
    } elseif (!filter_var($brand_image_url, FILTER_VALIDATE_URL)) {
      $errors[] = 'Brand image URL must be a valid URL.';
    }
    // Validate Product Name
    if (empty($brand_name)) {
      $errors[] = 'Brand name is required.';
    }
    // If no errors, insert data into the database
    if (empty($errors)) {
      $inserted = $wpdb->insert(
        $table_name,
        [
          'brand_image_url'   => $brand_image_url,
          'brand_name'        => $brand_name,
          'created'           => $created,
          'modified'           => $modified,
        ],
        [
          '%s',
          '%s',
          '%s',
          '%s'
        ]
      );

      if ($inserted) {
        echo '<div class="notice notice-success is-dismissible"><p>Product added successfully!</p></div>';
        echo '<script>window.location.href="' . admin_url('admin.php?page=aboyuma-product-brand-list') . '";</script>';
        exit;
      } else {
        // Capture and display the error
        $error_message = $wpdb->last_error; // Get the last error message
        if (!empty($error_message)) {
          error_log('Database Insert Error: ' . $error_message); // Log the error for debugging
        }
        $errors[] = 'Failed to add brand. Please try again.';
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
    <h1 class="wp-heading-inline">Add New Brand</h1>
    <hr class="wp-header-end">

    <form method="post">
      <table class="form-table">
        <!-- Product Image URL -->
        <tr>
          <th scope="row"><label for="brand_image_url">Brand Image URL</label></th>
          <td>
            <input type="url" name="brand_image_url" id="brand_image_url" class="regular-text" required>
            <p class="detail">Enter a valid URL pointing to the product image (e.g., https://example.com/image.jpg).</p>
          </td>
        </tr>
        </tr>
        <!-- Product Name -->
        <tr>
          <th scope="row"><label for="brand_name">Brand Name</label></th>
          <td>
            <input type="text" name="brand_name" id="brand_name" class="regular-text" required>
          </td>
        </tr>
      </table>
      <?php wp_nonce_field('aboyuma_amway_product_brand_add', 'aboyuma_amway_product_brand_add_nonce'); ?>
      <button type="submit" name="submit" id="submit" class="button button-primary">Add Brand</button>
      <button type="button" onclick="window.history.back();" class="button button-secondary">Cancel</button>
    </form>
  </div>
<?php
}
