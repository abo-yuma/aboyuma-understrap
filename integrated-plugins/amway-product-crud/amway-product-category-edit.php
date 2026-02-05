<?php
function aboyuma_amway_product_category_edit() {
  global $wpdb;
  $table_name = $wpdb->prefix . 'aboyuma_amway_product_category';

  // Check if product ID is provided
  $category_id = isset($_GET['id']) ? absint($_GET['id']) : 0;
  if (!$category_id) {
    echo '<div class="notice notice-error is-dismissible"><p>Invalid category ID.</p></div>';
    return;
  }
  // Get existing data for the category
  $category = $wpdb->get_row($wpdb->prepare("SELECT category_name, parent_id FROM $table_name WHERE id = %d", $category_id));
  if (!$category) {
    echo '<div class="notice notice-error is-dismissible"><p>Category not found.</p></div>';
    return;
  }
  $errors = [];

  // Handle form submission
  if (
    $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aboyuma_amway_product_category_edit_nonce'])
    && wp_verify_nonce($_POST['aboyuma_amway_product_category_edit_nonce'], 'aboyuma_amway_product_category_edit')
  ) {

    // Sanitize and assign form data
    $category_name = sanitize_text_field(trim($_POST['category_name']));
    $parent_id = isset($_POST['parent_id']) && $_POST['parent_id'] !== '' ? intval($_POST['parent_id']) : 0;
    $modified = current_time('mysql');

    // Validate fields
    if (empty($category_name)) {
      $errors[] = 'Category name is required.';
    }
    // If no errors, update the database
    if (empty($errors)) {
      $result = $wpdb->update(
        $table_name,
        [
          'category_name'     => $category_name,
          'parent_id'         => $parent_id,
          'modified'          => $modified
        ],
        ['id' => $category_id],
        ['%s', '%d', '%s'], // Correct formats for: string, int, string
        ['%d']
      );
      if ($result !== false) {
        //echo '<div class="notice notice-success is-dismissible"><p>Category updated successfully!</p></div>';
        echo '<script>window.location.href="' . admin_url('admin.php?page=aboyuma-product-category-list&updated-success=true') . '";</script>';
        exit;
      } else {
        $errors[] = 'Failed to update category. Please try again.';
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
    <h1 class="wp-heading-inline">Edit Category</h1>
    <hr class="wp-header-end">
    <form method="post">
      <table class="form-table">
        <!-- Category Name -->
        <tr>
          <th scope="row"><label for="category_name">Category Name</label></th>
          <td>
            <input type="text" name="category_name" id="category_name" class="regular-text"
              value="<?php echo esc_attr(
                        isset($_POST['category_name']) && $_POST['category_name'] !== ''
                          ? $_POST['category_name']
                          : $category->category_name
                      ); ?>" required>
          </td>
        </tr>
        <?php
        // Fetch categories for parent select
        $categories = $wpdb->get_results($wpdb->prepare(
          "SELECT id, category_name FROM $table_name WHERE id != %d ORDER BY category_name ASC",
          $category_id
        ));
        $submitted_parent_id = isset($_POST['parent_id']) && $_POST['parent_id'] !== ''
          ? intval($_POST['parent_id'])
          : $category->parent_id;
        ?>
        <tr>
          <th scope="row"><label for="parent_id">Parent Category</label></th>
          <td>
            <select name="parent_id" id="parent_id" class="regular-text">
              <option value="">-- None (Top-level category) --</option>
              <?php foreach ($categories as $cat): ?>
                <option value="<?php echo esc_attr($cat->id); ?>"
                  <?php selected($submitted_parent_id, $cat->id); ?>>
                  <?php echo esc_html($cat->category_name); ?>
                </option>
              <?php endforeach; ?>
            </select>
            <br>
            <small>This category will be subcategory of the selected category.</small>
          </td>
        </tr>
      </table>
      <?php wp_nonce_field('aboyuma_amway_product_category_edit', 'aboyuma_amway_product_category_edit_nonce'); ?>
      <button type="submit" name="submit" id="submit" class="button button-primary">Update Category</button>
      <button type="button" onclick="window.history.back();" class="button button-secondary">Cancel</button>
    </form>
  </div>
<?php
}
