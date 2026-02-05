<?php
function aboyuma_amway_product_category_add() {
  // Add new product data page content
  global $wpdb;
  $table_name = $wpdb->prefix . 'aboyuma_amway_product_category';

  // Initialize variables
  $category_name = '';
  $errors = [];

  if (
    $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aboyuma_amway_product_category_add_nonce'])
    && wp_verify_nonce($_POST['aboyuma_amway_product_category_add_nonce'], 'aboyuma_amway_product_category_add')
  ) {
    // Sanitize and assign form data
    $category_name = sanitize_text_field(trim($_POST['category_name']));
    $parent_id = isset($_POST['parent_id']) && $_POST['parent_id'] !== '' ? intval($_POST['parent_id']) : 0;
    $created = current_time('mysql');
    $modified = current_time('mysql');

    // Validate fields
    // Validate Product Name
    if (empty($category_name)) {
      $errors[] = 'Category name is required.';
    }
    // If no errors, insert data into the database
    if (empty($errors)) {
      $inserted = $wpdb->insert(
        $table_name,
        [
          'category_name'     => $category_name,
          'parent_id'         => $parent_id,
          'created'           => $created,
          'modified'          => $modified,
        ],
        [
          '%s',
          '%d',
          '%s',
          '%s'
        ]
      );

      if ($inserted) {
        echo '<div class="notice notice-success is-dismissible"><p>Category added successfully!</p></div>';
        echo '<script>window.location.href="' . admin_url('admin.php?page=aboyuma-product-category-list') . '";</script>';
        exit;
      } else {
        // Capture and display the error
        $error_message = $wpdb->last_error; // Get the last error message
        if (!empty($error_message)) {
          error_log('Database Insert Error: ' . $error_message); // Log the error for debugging
        }
        $errors[] = 'Failed to add category. Please try again.';
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
    <h1 class="wp-heading-inline">Add New Product Category</h1>
    <hr class="wp-header-end">

    <form method="post">
      <table class="form-table">
        </tr>
        <!-- Category Name -->
        <tr>
          <th scope="row"><label for="category_name">Category Name</label></th>
          <td>
            <input type="text" name="category_name" id="category_name" class="regular-text" required>
          </td>
        </tr>
        <?php
        // Fetch categories for parent select
        $categories = $wpdb->get_results("SELECT id, category_name FROM $table_name ORDER BY category_name ASC", ARRAY_A);
        ?>
        <tr>
          <th scope="row"><label for="parent_id">Parent Category</label></th>
          <td>
            <select name="parent_id" id="parent_id" class="regular-text">
              <option value="">-- None (Top-level category) --</option>
              <?php foreach ($categories as $cat): ?>
                <option value="<?php echo esc_attr($cat['id']); ?>" <?php selected($_POST['parent_id'] ?? '', $cat['id']); ?>>
                  <?php echo esc_html($cat['category_name']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </td>
        </tr>
      </table>
      <?php wp_nonce_field('aboyuma_amway_product_category_add', 'aboyuma_amway_product_category_add_nonce'); ?>
      <button type="submit" name="submit" id="submit" class="button button-primary">Add Category</button>
      <button type="button" onclick="window.history.back();" class="button button-secondary">Cancel</button>
    </form>
  </div>
<?php
}
