<?php
/*
Theme Integreated Plugin
ABO YUMA AMWAY Product Crud

Reference: 
- https://github.com/eduardoarandah/wordpress-crud-example
- https://accreditly.io/articles/how-to-create-a-crud-in-wordpress-without-using-plugins

Related issues:
- https://stackoverflow.com/questions/18287291/create-a-file-for-crud-functions-with-no-plugin
- https://wordpress.stackexchange.com/questions/27525/how-to-add-plugins-to-wordpress-theme

TODO :
Future Refactor
- Refactor using custom OOP or WordPress Plugin Boilerplate (WPPB)
  https://wppb.me/
- Remain theme-integrated
*/
// Create multiple tables reference: https://wordpress.stackexchange.com/a/341755/193936

function create_aboyuma_amway_product_table() {

  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

  global $wpdb;
  $productTable = $wpdb->prefix . 'aboyuma_amway_product';
  $productBrandTable = $wpdb->prefix . 'aboyuma_amway_product_brand';
  $productCategoryTable = $wpdb->prefix . 'aboyuma_amway_product_category';
  $productTagTable = $wpdb->prefix . 'aboyuma_amway_product_tag';
  $productImageTable = $wpdb->prefix . 'aboyuma_amway_product_image';
  $productInfographicTable = $wpdb->prefix . 'aboyuma_amway_product_infographic';
  $relatedProductTable = $wpdb->prefix . 'aboyuma_related_amway_product';
  $relatedProductVideoTable = $wpdb->prefix . 'aboyuma_related_amway_product_video';

  $charset_collate = $wpdb->get_charset_collate();
  // Prepare query statements
  $queries = array();
  array_push($queries, "CREATE TABLE $productBrandTable (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    PRIMARY KEY (id),
    brand_image_url VARCHAR(255) NOT NULL,
    brand_name VARCHAR(255) NOT NULL,
    created DATETIME NOT NULL,
    modified DATETIME NOT NULL
  ) $charset_collate;");
  array_push($queries, "CREATE TABLE $productCategoryTable (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    PRIMARY KEY (id),
    category_name VARCHAR(255) NOT NULL,
    parent_id INT UNSIGNED DEFAULT NULL,
    created DATETIME NOT NULL,
    modified DATETIME NOT NULL
  ) $charset_collate;");
  // Constraint query should be last
  array_push($queries, "CREATE TABLE $productTable (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    PRIMARY KEY (id),
    product_image LONGBLOB NOT NULL,
    product_code VARCHAR(10) NOT NULL DEFAULT '',                 
    product_brand_id INT UNSIGNED NOT NULL DEFAULT 0,             
    product_category_id INT UNSIGNED NOT NULL DEFAULT 0,          
    product_name VARCHAR(255) NOT NULL DEFAULT '',                
    product_unit_count INT UNSIGNED NULL,                         
    product_unit_type ENUM('tablet','capsule','sachet','g','ml') NOT NULL,
    product_unit_usage ENUM('content','capacity') NOT NULL DEFAULT 'content',
    product_short_description LONGTEXT NOT NULL DEFAULT '',       
    product_description LONGTEXT NOT NULL DEFAULT '',             
    product_benefit LONGTEXT NOT NULL DEFAULT '',                 
    product_detail LONGTEXT NOT NULL DEFAULT '',                  
    product_specification LONGTEXT NULL,                           
    product_comment TEXT NULL,                                     
    product_additive_properties JSON NULL,                         
    product_diet_properties JSON NULL,                              
    product_review LONGTEXT NULL,                                    
    product_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,             
    product_member_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,      
    product_is_sop TINYINT(1) NOT NULL DEFAULT 0,                  
    product_sop_link VARCHAR(255) NULL,                             
    product_shop_link VARCHAR(255) NOT NULL DEFAULT '',             
    product_sale_article_link VARCHAR(255) NULL,                    
    product_is_pinned TINYINT(1) NOT NULL DEFAULT 0,               
    product_slug VARCHAR(255) NOT NULL DEFAULT '',                 
    created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,           
    modified DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
  ) $charset_collate;");

  array_push($queries, "CREATE TABLE $productTagTable (
    product_id INT UNSIGNED NOT NULL,
    tag_name VARCHAR(255) NOT NULL,
    created DATETIME NOT NULL,
    modified DATETIME NOT NULL,
    PRIMARY KEY (product_id, tag_name),
    KEY idx_tag_name (tag_name)
  ) $charset_collate;");
  array_push($queries, "CREATE TABLE $productImageTable (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    PRIMARY KEY (id),
    product_id INT UNSIGNED NOT NULL,
    product_image_path VARCHAR(255) NOT NULL,
    product_image_order TINYINT(1) NOT NULL,
    created DATETIME NOT NULL,
    modified DATETIME NOT NULL
  ) $charset_collate;");
  array_push($queries, "CREATE TABLE $productInfographicTable (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    PRIMARY KEY (id),
    product_id INT UNSIGNED NOT NULL,
    product_infographic_path VARCHAR(255) NOT NULL,
    product_infographic_order TINYINT(1) NOT NULL,
    created DATETIME NOT NULL,
    modified DATETIME NOT NULL
  ) $charset_collate;");
  array_push($queries, "CREATE TABLE $relatedProductTable (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    PRIMARY KEY (id),
    product_id INT UNSIGNED NOT NULL,
    related_product_id INT UNSIGNED NOT NULL,
    created DATETIME NOT NULL,
    modified DATETIME NOT NULL,
    UNIQUE KEY unique_related_pair (product_id, related_product_id)
  ) $charset_collate;");
  array_push($queries, "CREATE TABLE $relatedProductVideoTable (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    PRIMARY KEY (id),
    product_id INT UNSIGNED NOT NULL,
    video_url VARCHAR(255) NOT NULL,
    video_comment VARCHAR (255) NOT NULL,
    created DATETIME NOT NULL,
    modified DATETIME NOT NULL
  ) $charset_collate;");

  /*
  * IMPORTANT – WordPress dbDelta compatibility
  * Issue date: 2026-01-03
  *
  * Do NOT define PRIMARY KEY inline, for example:
  *   id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY
  *
  * This old-style MySQL syntax is NOT dbDelta-safe.
  * WordPress dbDelta() may fail to detect inline PRIMARY KEY definitions
  * and will attempt to add another PRIMARY KEY using ALTER TABLE.
  *
  * This results in the MySQL error:
  *   "Multiple primary key defined"
  *
  * Always define primary keys using a separate statement:
  *   id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  *   PRIMARY KEY (id)
  */

  // Create database tables
  foreach ($queries as $key => $sql) {
    dbDelta($sql);
  }

  // List foreign keys: [table, fk_name, SQL]
  $foreign_keys = [
    [$productTable, 'fk_aboyuma_amway_product_brand_id', "ALTER TABLE $productTable 
      ADD CONSTRAINT fk_aboyuma_amway_product_brand_id FOREIGN KEY (product_brand_id) 
      REFERENCES $productBrandTable(id) ON DELETE CASCADE"],
    [$productTable, 'fk_aboyuma_amway_product_category_id', "ALTER TABLE $productTable 
      ADD CONSTRAINT fk_aboyuma_amway_product_category_id FOREIGN KEY (product_category_id) 
      REFERENCES $productCategoryTable(id) ON DELETE CASCADE"],
    [$productTagTable, 'fk_aboyuma_amway_product_tag_product_id', "ALTER TABLE $productTagTable 
      ADD CONSTRAINT fk_aboyuma_amway_product_tag_product_id FOREIGN KEY (product_id) 
      REFERENCES $productTable(id) ON DELETE CASCADE"],
    [$productImageTable, 'fk_aboyuma_amway_product_image_product_id', "ALTER TABLE $productImageTable 
      ADD CONSTRAINT fk_aboyuma_amway_product_image_product_id FOREIGN KEY (product_id) 
      REFERENCES $productTable(id) ON DELETE CASCADE"],
    [$productInfographicTable, 'fk_aboyuma_amway_product_infographic_product_id', "ALTER TABLE $productInfographicTable 
      ADD CONSTRAINT fk_aboyuma_amway_product_infographic_product_id FOREIGN KEY (product_id) 
      REFERENCES $productTable(id) ON DELETE CASCADE"],
    [$relatedProductTable, 'fk_aboyuma_related_amway_product_product_id', "ALTER TABLE $relatedProductTable 
      ADD CONSTRAINT fk_aboyuma_related_amway_product_product_id FOREIGN KEY (product_id) 
      REFERENCES $productTable(id) ON DELETE CASCADE"],
    [$relatedProductTable, 'fk_aboyuma_related_amway_product_related_id', "ALTER TABLE $relatedProductTable 
      ADD CONSTRAINT fk_aboyuma_related_amway_product_related_id FOREIGN KEY (related_product_id) 
      REFERENCES $productTable(id) ON DELETE CASCADE"],
    [$relatedProductVideoTable, 'fk_aboyuma_amway_product_video_product_id', "ALTER TABLE $relatedProductVideoTable 
      ADD CONSTRAINT fk_aboyuma_amway_product_video_product_id FOREIGN KEY (product_id) 
      REFERENCES $productTable(id) ON DELETE CASCADE"],
  ];

  foreach ($foreign_keys as [$table, $fk_name, $sql]) {
    if (!fk_exists($table, $fk_name)) {
      $wpdb->query($sql);
    }
  }
}
add_action('after_switch_theme', 'create_aboyuma_amway_product_table');

// Define new schema version when there's db update
function aboyuma_update_amway_product_schema() {

  $new_schema_version = '1.0'; // Define new version
  $current_schema_version = get_option('aboyuma_amway_product_schema_version');

  if (version_compare($current_schema_version, $new_schema_version, '<')) {
    create_aboyuma_amway_product_table();
    update_option('aboyuma_amway_product_schema_version', $new_schema_version);
  }
}
add_action('after_setup_theme', 'aboyuma_update_amway_product_schema');


function aboyuma_amway_product_menu() {
  $productPageTitle = 'Amway Products';
  $productPageMenuTitle = 'Amway Products';
  $productPageCapability = 'manage_options';
  $productPageMenuSlug = 'aboyuma-product-list';
  $productPageFunction = 'aboyuma_amway_product_list';
  $productPageIconUrl = 'dashicons-products';
  $productPagePosition = 25;

  add_menu_page(
    $productPageTitle,
    $productPageMenuTitle,
    $productPageCapability,
    $productPageMenuSlug,
    $productPageFunction,
    $productPageIconUrl,
    $productPagePosition
  );
  /*
  if you do not add the add_submenu_page for "All Products," WordPress will automatically 
  create a submenu item for the main menu. This default submenu item will have the same title 
  as the main menu ("aboyuma Product" in your case) and will point to the same slug and callback function.
  Behavior Without the Submenu Code:
  WordPress automatically mirrors the main menu as the first submenu item.
  The submenu item will have:
  Menu title: "aboyuma Product" (same as the main menu title).
  Slug: The same slug as the main menu (aboyuma-product).
  Callback function: The same callback function as the main menu.
  */
  add_submenu_page(
    $productPageMenuSlug,
    'Products', // Page title
    'Products', // Menu title
    $productPageCapability,
    $productPageMenuSlug, // Use the same slug as the main menu
    $productPageFunction
  );
  add_submenu_page(
    null,
    'Add New Product',
    null,
    $productPageCapability,
    'aboyuma-product-add',
    'aboyuma_amway_product_add'
  );
  // Add submenu page without adding menu 
  // Ref:https://stackoverflow.com/a/12758400/8527193
  add_submenu_page(
    null,
    'Product Detail',
    null,
    $productPageCapability,
    'aboyuma-product-detail',
    'aboyuma_amway_product_detail'
  );
  add_submenu_page(
    null,
    'Edit Product',
    null,
    $productPageCapability,
    'aboyuma-product-edit',
    'aboyuma_amway_product_edit'
  );
  add_submenu_page(
    $productPageMenuSlug,
    'Brands',
    'Brands',
    $productPageCapability,
    'aboyuma-product-brand-list',
    'aboyuma_amway_product_brand_list'
  );
  add_submenu_page(
    null,
    'Add New Brand',
    null,
    $productPageCapability,
    'aboyuma-product-brand-add',
    'aboyuma_amway_product_brand_add'
  );
  add_submenu_page(
    null,
    'Edit Brand',
    null,
    $productPageCapability,
    'aboyuma-product-brand-edit',
    'aboyuma_amway_product_brand_edit'
  );
  add_submenu_page(
    $productPageMenuSlug,
    'Categories',
    'Categories',
    $productPageCapability,
    'aboyuma-product-category-list',
    'aboyuma_amway_product_category_list'
  );
  add_submenu_page(
    null,
    'Add New Category',
    null,
    $productPageCapability,
    'aboyuma-product-category-add',
    'aboyuma_amway_product_category_add'
  );
  add_submenu_page(
    null,
    'Edit Category',
    null,
    $productPageCapability,
    'aboyuma-product-category-edit',
    'aboyuma_amway_product_category_edit'
  );
}
add_action('admin_menu', 'aboyuma_amway_product_menu');

// AJAX Handler registration
// Fetch product data for page-product
function ajax_filter_products() {
  // Verify nonce
  if (!isset($_GET['_ajax_nonce']) || !wp_verify_nonce($_GET['_ajax_nonce'], 'filter_products_nonce')) {
    wp_send_json_error(['message' => 'Invalid or expired nonce'], 403);
    wp_die();
  }

  global $wpdb;
  $product_table = $wpdb->prefix . 'aboyuma_amway_product';
  $product_brand_table = $wpdb->prefix . 'aboyuma_amway_product_brand';
  $product_category_table = $wpdb->prefix . 'aboyuma_amway_product_category';

  // Retrieve category filter value
  $parent_category = isset($_GET['parent_category']) ? intval($_GET['parent_category']) : 0;
  $sub_category = isset($_GET['sub_category']) ? intval($_GET['sub_category']) : 0;

  // Retrieve price filter values
  $price_min = isset($_GET['price_min']) ? floatval($_GET['price_min']) : 0;
  $price_max = isset($_GET['price_max']) ? floatval($_GET['price_max']) : 0;

  // Base query
  $query = "SELECT p.id,
              p.product_image,
              p.product_name,
              p.product_short_description,
              p.product_benefit, b.brand_name, c.category_name
              FROM $product_table p
              LEFT JOIN $product_brand_table b ON p.product_brand_id = b.id
              LEFT JOIN $product_category_table c ON p.product_category_id = c.id
              WHERE 1=1";

  // Apply price filters if provided
  if ($price_min > 0) {
    $query .= $wpdb->prepare(" AND p.product_member_price >= %f", $price_min);
  }
  if ($price_max > 0) {
    $query .= $wpdb->prepare(" AND p.product_price <= %f", $price_max);
  }
  // Category filtering
  if ($sub_category > 0) {
    // Filter by selected subcategory only
    $query .= $wpdb->prepare(" AND c.id = %d", $sub_category);
  } elseif ($parent_category > 0) {
    // Get subcategory IDs + parent category ID
    $subcategory_ids = $wpdb->get_col(
      $wpdb->prepare(
        "SELECT id FROM $product_category_table WHERE parent_id = %d OR id = %d",
        $parent_category,
        $parent_category
      )
    );

    if (!empty($subcategory_ids)) {
      $placeholders = implode(',', array_fill(0, count($subcategory_ids), '%d'));
      $query .= $wpdb->prepare(" AND c.id IN ($placeholders)", ...$subcategory_ids);
    } else {
      // No matching categories — return nothing
      $query .= " AND 0=1";
    }
  }

  // Fetch the results from the database
  $products = $wpdb->get_results($query);
  $data = [];
  // Prepare data for DataTable
  foreach ($products as $product) {
    $image_url = $product->product_image ? '<img src="data:image/jpeg;base64,' . base64_encode($product->product_image) . '" width="100%" height="auto">' : '';
    $detail_url = esc_url(add_query_arg('id', $product->id, get_permalink(get_page_by_path('amway-product-detail')->ID)));
    // Combine image and name in one column
    $image_and_name = '
        <div style="text-align:center;">
            ' . $image_url . '
            <span>' . esc_html($product->product_name) . '</span>
        </div>';
    $data[] = [
      $image_and_name,
      htmlspecialchars_decode($product->product_short_description),
      htmlspecialchars_decode($product->product_benefit),
      '<a href="' . $detail_url . '">รายละเอียด</a>' // Only show the text as a link
    ];
  }
  // Return plain array dataSrc should set to ''
  wp_send_json($data);
  wp_die();
}
// 🔄 Register AJAX for both guests and logged-in users
add_action('wp_ajax_filter_products', 'ajax_filter_products');
add_action('wp_ajax_nopriv_filter_products', 'ajax_filter_products');

// Add page-amway-product-detail meta data for Facebook share
add_action('wp_head', function () {
  if (is_page('amway-product-detail')) {
    global $wpdb;

    $product_id = isset($_GET['id']) ? absint($_GET['id']) : 0;
    if (!$product_id) {
      return;
    }

    // --- Define table names ---
    $product_table = $wpdb->prefix . 'aboyuma_amway_product';
    $product_image_table = $wpdb->prefix . 'aboyuma_amway_product_image';

    // --- Fetch product info + first image path ---
    $sql = $wpdb->prepare("
			SELECT 
				p.product_name,
				p.product_short_description,
				pi.product_image_path
			FROM 
				{$product_table} AS p
			LEFT JOIN 
				{$product_image_table} AS pi
					ON pi.product_id = p.id
			WHERE 
				p.id = %d
			ORDER BY 
				pi.id ASC
			LIMIT 1
		", $product_id);

    $product = $wpdb->get_row($sql);
    if (!$product) {
      return;
    }

    // --- Prepare meta values ---
    $metaTitle = esc_attr($product->product_name);
    $metaDescription = esc_attr(wp_trim_words(strip_tags($product->product_short_description), 30, '...'));

    // --- Build full image URL (simple version) ---
    $metaImage = '';
    if (!empty($product->product_image_path)) {
      // Same logic as your front-end: base URL + image path
      $uploads = wp_get_upload_dir();
      $baseurl = trailingslashit($uploads['baseurl']); // e.g. http://localhost/devwp/wp-content/uploads/
      $metaImage = esc_url($baseurl . ltrim($product->product_image_path, '/'));
    }

    // --- Product page URL ---
    $metaUrl = esc_url(home_url("/amway-product-detail/?id={$product_id}"));
?>
    <!-- Open Graph Meta Tags -->
    <meta property="og:type" content="product" />
    <meta property="og:title" content="<?php echo $metaTitle; ?>" />
    <meta property="og:description" content="<?php echo $metaDescription; ?>" />
    <?php if ($metaImage): ?>
      <meta property="og:image" content="<?php echo $metaImage; ?>" />
    <?php endif; ?>
    <meta property="og:url" content="<?php echo $metaUrl; ?>" />
    <meta property="og:site_name" content="<?php bloginfo('name'); ?>" />
<?php
  }
});

// Fetch related product for page-amway-product-detail
function aboyuma_get_related_products() {
  check_ajax_referer('aboyuma_related_nonce', '_ajax_nonce');

  $product_id = intval($_GET['product_id'] ?? 0);
  $related_products = isset($_GET['related_products']) ? array_map('intval', (array) $_GET['related_products']) : [];

  global $wpdb;
  $placeholders = implode(',', array_fill(0, count($related_products), '%d'));
  $product_table = $wpdb->prefix . 'aboyuma_amway_product';
  $related_table = $wpdb->prefix . 'aboyuma_related_amway_product';

  $sql = "
        SELECT 
        p.id,
        p.product_image,
        p.product_name,
		    p.product_short_description,
        r.related_product_comment
        FROM $product_table p
        INNER JOIN $related_table r
        ON p.id = r.related_product_id
        WHERE r.product_id = %d
        AND r.related_product_id IN ($placeholders)
        ORDER BY p.product_name ASC
    ";
  $prepared_sql = $wpdb->prepare($sql, $product_id, ...$related_products);
  $results = $wpdb->get_results($prepared_sql);

  $data = [];

  foreach ($results as $product) {
    $image_url = $product->product_image ? '<img src="data:image/jpeg;base64,' . base64_encode($product->product_image) . '" width="100%" height="auto">' : '';
    $name = esc_html($product->product_name);
    // Combine image and name in one column
    $image_and_name = '
        <div style="text-align:center;">
            ' . $image_url . '
            <span>' . $name . '</span>
        </div>';
    $comment = wp_kses_post($product->related_product_comment);
    $link = esc_url(add_query_arg('id', $product->id, get_permalink(get_page_by_path('amway-product-detail')->ID)));
    $data[] = [
      $image_and_name,
      htmlspecialchars_decode($product->product_short_description),
      $comment,
      "<a href='{$link}'>[รายละเอียด]</a>"
    ];
  }

  wp_send_json([
    'data' => $data
  ]);
}
add_action('wp_ajax_get_related_products', 'aboyuma_get_related_products');
add_action('wp_ajax_nopriv_get_related_products', 'aboyuma_get_related_products');

require_once get_template_directory() . '/integrated-plugins/amway-product-crud/amway-product-functions.php';
require_once get_template_directory() . '/integrated-plugins/amway-product-crud/amway-product-list.php';
require_once get_template_directory() . '/integrated-plugins/amway-product-crud/amway-product-add.php';
require_once get_template_directory() . '/integrated-plugins/amway-product-crud/amway-product-detail.php';
require_once get_template_directory() . '/integrated-plugins/amway-product-crud/amway-product-edit.php';
require_once get_template_directory() . '/integrated-plugins/amway-product-crud/amway-product-delete.php';
require_once get_template_directory() . '/integrated-plugins/amway-product-crud/amway-product-brand-list.php';
require_once get_template_directory() . '/integrated-plugins/amway-product-crud/amway-product-brand-add.php';
require_once get_template_directory() . '/integrated-plugins/amway-product-crud/amway-product-brand-edit.php';
require_once get_template_directory() . '/integrated-plugins/amway-product-crud/amway-product-brand-delete.php';
require_once get_template_directory() . '/integrated-plugins/amway-product-crud/amway-product-category-list.php';
require_once get_template_directory() . '/integrated-plugins/amway-product-crud/amway-product-category-add.php';
require_once get_template_directory() . '/integrated-plugins/amway-product-crud/amway-product-category-edit.php';
require_once get_template_directory() . '/integrated-plugins/amway-product-crud/amway-product-category-delete.php';
require_once get_template_directory() . '/integrated-plugins/amway-product-crud/config/amway-product-menu-registration.php';
