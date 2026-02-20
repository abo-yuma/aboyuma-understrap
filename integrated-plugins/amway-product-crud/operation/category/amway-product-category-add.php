<?php
defined('ABSPATH') || exit;

function aboyuma_amway_product_category_add() {

  global $wpdb;
  $table_name = $wpdb->prefix . 'aboyuma_amway_product_category';

  $category_name = '';
  $parent_id = null;
  $errors = [];

  if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['aboyuma_amway_product_category_add_nonce'])
    && wp_verify_nonce($_POST['aboyuma_amway_product_category_add_nonce'], 'aboyuma_amway_product_category_add')
  ) {

    // =========================
    // SANITIZE
    // =========================
    $category_name = sanitize_text_field(trim($_POST['category_name']));
    $parent_id = !empty($_POST['parent_id']) ? absint($_POST['parent_id']) : null;

    // =========================
    // VALIDATION
    // =========================
    if (empty($category_name)) {
      $errors[] = 'Category name is required.';
    }

    // Validate parent exists (if selected)
    if ($parent_id !== null) {
      $parent_exists = $wpdb->get_var(
        $wpdb->prepare(
          "SELECT COUNT(*) FROM $table_name WHERE id = %d",
          $parent_id
        )
      );

      if (!$parent_exists) {
        $errors[] = 'Selected parent category does not exist.';
      }
    }

    // =========================
    // INSERT
    // =========================
    if (empty($errors)) {

      $inserted = $wpdb->insert(
        $table_name,
        [
          'category_name' => $category_name,
          'parent_id'     => $parent_id, // NULL if top-level
        ],
        [
          '%s',
          '%d'
        ]
      );

      if ($inserted !== false) {

        $redirect_url = admin_url('admin.php?page=aboyuma-product-category-list');

        echo '<script type="text/javascript">
        window.location.replace("' . esc_url($redirect_url) . '");
        </script>';
        exit;
      } else {

        $error_message = $wpdb->last_error;
        error_log('Category Insert Error: ' . $error_message);

        echo '<div class="notice notice-error is-dismissible"><p>'
          . esc_html($error_message)
          . '</p></div>';
      }
    }
  }

  // =========================
  // DISPLAY ERRORS
  // =========================
  if (!empty($errors)) {
    echo '<div class="notice notice-error is-dismissible"><ul>';
    foreach ($errors as $error) {
      echo '<li>' . esc_html($error) . '</li>';
    }
    echo '</ul></div>';
  }

  // =========================
  // FETCH EXISTING CATEGORIES
  // =========================
  $categories = $wpdb->get_results(
    "SELECT id, category_name FROM $table_name ORDER BY category_name ASC",
    ARRAY_A
  );
?>

  <div class="wrap">
    <h1 class="wp-heading-inline">Add New Product Category</h1>
    <hr class="wp-header-end">

    <form method="post">

      <table class="form-table">

        <!-- Category Name -->
        <tr>
          <th scope="row">
            <label for="category_name">Category Name</label>
          </th>
          <td>
            <input
              type="text"
              name="category_name"
              id="category_name"
              class="regular-text"
              value="<?php echo esc_attr($category_name); ?>"
              required>
          </td>
        </tr>

        <!-- Parent Category -->
        <tr>
          <th scope="row">
            <label for="parent_id">Parent Category</label>
          </th>
          <td>
            <select name="parent_id" id="parent_id" class="regular-text">
              <option value="">-- None (Top-level category) --</option>

              <?php foreach ($categories as $cat): ?>
                <option
                  value="<?php echo esc_attr($cat['id']); ?>"
                  <?php selected($_POST['parent_id'] ?? '', $cat['id']); ?>>
                  <?php echo esc_html($cat['category_name']); ?>
                </option>
              <?php endforeach; ?>

            </select>
          </td>
        </tr>

      </table>

      <?php wp_nonce_field('aboyuma_amway_product_category_add', 'aboyuma_amway_product_category_add_nonce'); ?>

      <button type="submit" class="button button-primary">
        Add Category
      </button>

      <button type="button" onclick="window.history.back();" class="button button-secondary">
        Cancel
      </button>

    </form>
  </div>

<?php
}
