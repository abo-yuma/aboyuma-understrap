<?php
/*
Theme Integreated Plugin
ABO YUMA Product Crud
Reference: 
https://github.com/eduardoarandah/wordpress-crud-example
https://accreditly.io/articles/how-to-create-a-crud-in-wordpress-without-using-plugins#content-section-6-deleting-aboyuma-data
*/
// Create multiple tables reference: https://wordpress.stackexchange.com/a/341755/193936
function create_aboyuma_table() {

  $schema_version = '1.0';
  $current_version = get_option('aboyuma_schema_version');

  if ($current_version === $schema_version) {
    return; // Already up-to-date
  }

  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
  global $wpdb;
  $productTable = $wpdb->prefix . 'aboyuma_product';
  $productBrandTable = $wpdb->prefix . 'aboyuma_product_brand';
  $productCategoryTable = $wpdb->prefix . 'aboyuma_product_category';
  $productTagTable = $wpdb->prefix . 'aboyuma_product_tag';
  $productImageTable = $wpdb->prefix . 'aboyuma_product_image';
  $relatedProductTable = $wpdb->prefix . 'aboyuma_related_product';
  $relatedProductVideoTable = $wpdb->prefix . 'aboyuma_related_product_video';

  $charset_collate = $wpdb->get_charset_collate();
  // Prepare query statements
  $queries = array();
  array_push($queries, "CREATE TABLE $productBrandTable (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    brand_image_url VARCHAR(255) NOT NULL,
    brand_name VARCHAR(255) NOT NULL,
    created DATETIME NOT NULL,
    modified DATETIME NOT NULL
  ) $charset_collate;");
  array_push($queries, "CREATE TABLE $productCategoryTable (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(255) NOT NULL,
    parent_id INT UNSIGNED DEFAULT NULL,
    created DATETIME NOT NULL,
    modified DATETIME NOT NULL
  ) $charset_collate;");
  // Constraint query should be last
  array_push($queries, "CREATE TABLE $productTable (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    product_image LONGBLOB NOT NULL,
    product_code VARCHAR(10) NOT NULL,
    product_brand_id INT UNSIGNED NOT NULL,
    product_category_id INT UNSIGNED NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    product_short_description LONGTEXT NOT NULL,
    product_description LONGTEXT NOT NULL,
    product_benefit LONGTEXT NOT NULL,
    product_detail LONGTEXT NOT NULL,
    product_specification LONGTEXT NULL,
    product_comment TEXT NULL,
    product_additive_properties JSON NULL,
    product_diet_properties JSON NULL,
    product_review LONGTEXT NULL,
    product_price DECIMAL(10,2) NOT NULL, -- Precise monetary value
    product_member_price DECIMAL(10,2) NOT NULL, -- Precise monetary value
    product_is_sop TINYINT(1) NOT NULL DEFAULT 0,
    product_sop_link VARCHAR(255) NULL,
    product_shop_link VARCHAR(255) NOT NULL,
    product_sale_article_link VARCHAR(255) NULL,
    product_is_pinned TINYINT(1) NOT NULL DEFAULT 0,
    created DATETIME NOT NULL,
    modified DATETIME NOT NULL
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
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNSIGNED NOT NULL,
    product_image_path VARCHAR(255) NOT NULL,
    product_image_order TINYINT(1) NOT NULL,
    created DATETIME NOT NULL,
    modified DATETIME NOT NULL
  ) $charset_collate;");
  array_push($queries, "CREATE TABLE $relatedProductTable (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNSIGNED NOT NULL,
    related_product_id INT UNSIGNED NOT NULL,
    created DATETIME NOT NULL,
    modified DATETIME NOT NULL,
    UNIQUE KEY unique_related_pair (product_id, related_product_id)
  ) $charset_collate;");
  array_push($queries, "CREATE TABLE $relatedProductVideoTable (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNSIGNED NOT NULL,
    video_url VARCHAR(255) NOT NULL,
    video_comment VARCHAR (255) NOT NULL,
    created DATETIME NOT NULL,
    modified DATETIME NOT NULL
  ) $charset_collate;");
  // Create database tables
  foreach ($queries as $key => $sql) {
    dbDelta($sql);
  }

  // Helper function to check if FK exists
  function fk_exists($table, $fk_name) {
    global $wpdb;
    $db = DB_NAME;
    $sql = "SELECT CONSTRAINT_NAME 
            FROM information_schema.TABLE_CONSTRAINTS
            WHERE TABLE_SCHEMA = %s 
            AND TABLE_NAME = %s
            AND CONSTRAINT_NAME = %s";
    return (bool) $wpdb->get_var($wpdb->prepare($sql, $db, $table, $fk_name));
  }

  // List foreign keys: [table, fk_name, SQL]
  $foreign_keys = [
    [$productTable, 'fk_aboyuma_product_brand_id', "ALTER TABLE $productTable 
      ADD CONSTRAINT fk_aboyuma_product_brand_id FOREIGN KEY (product_brand_id) 
      REFERENCES $productBrandTable(id) ON DELETE CASCADE"],
    [$productTable, 'fk_aboyuma_product_category_id', "ALTER TABLE $productTable 
      ADD CONSTRAINT fk_aboyuma_product_category_id FOREIGN KEY (product_category_id) 
      REFERENCES $productCategoryTable(id) ON DELETE CASCADE"],
    [$productTagTable, 'fk_aboyuma_product_tag_product_id', "ALTER TABLE $productTagTable 
      ADD CONSTRAINT fk_aboyuma_product_tag_product_id FOREIGN KEY (product_id) 
      REFERENCES $productTable(id) ON DELETE CASCADE"],
    [$productImageTable, 'fk_aboyuma_product_image_product_id', "ALTER TABLE $productImageTable 
      ADD CONSTRAINT fk_aboyuma_product_image_product_id FOREIGN KEY (product_id) 
      REFERENCES $productTable(id) ON DELETE CASCADE"],
    [$relatedProductTable, 'fk_aboyuma_related_product_product_id', "ALTER TABLE $relatedProductTable 
      ADD CONSTRAINT fk_aboyuma_related_product_product_id FOREIGN KEY (product_id) 
      REFERENCES $productTable(id) ON DELETE CASCADE"],
    [$relatedProductTable, 'fk_aboyuma_related_product_related_id', "ALTER TABLE $relatedProductTable 
      ADD CONSTRAINT fk_aboyuma_related_product_related_id FOREIGN KEY (related_product_id) 
      REFERENCES $productTable(id) ON DELETE CASCADE"],
    [$relatedProductVideoTable, 'fk_aboyuma_product_video_product_id', "ALTER TABLE $relatedProductVideoTable 
      ADD CONSTRAINT fk_aboyuma_product_video_product_id FOREIGN KEY (product_id) 
      REFERENCES $productTable(id) ON DELETE CASCADE"],
  ];

  foreach ($foreign_keys as [$table, $fk_name, $sql]) {
    if (!fk_exists($table, $fk_name)) {
      $wpdb->query($sql);
    }
  }

  // Save current schema version
  update_option('aboyuma_schema_version', $schema_version);
}
add_action('after_switch_theme', 'create_aboyuma_table');

function aboyuma_product_menu() {
  $productPageTitle = 'Amway Products';
  $productPageMenuTitle = 'Amway Products';
  $productPageCapability = 'manage_options';
  $productPageMenuSlug = 'aboyuma-product-list';
  $productPageFunction = 'aboyuma_product_list';
  $productPageIconUrl = 'dashicons-admin-generic';
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
    'aboyuma_product_add'
  );
  // Add submenu page without adding menu 
  // Ref:https://stackoverflow.com/a/12758400/8527193
  add_submenu_page(
    null,
    'Product Detail',
    null,
    $productPageCapability,
    'aboyuma-product-detail',
    'aboyuma_product_detail'
  );
  add_submenu_page(
    null,
    'Edit Product',
    null,
    $productPageCapability,
    'aboyuma-product-edit',
    'aboyuma_product_edit'
  );
  add_submenu_page(
    $productPageMenuSlug,
    'Brands',
    'Brands',
    $productPageCapability,
    'aboyuma-product-brand-list',
    'aboyuma_product_brand_list'
  );
  add_submenu_page(
    null,
    'Add New Brand',
    null,
    $productPageCapability,
    'aboyuma-product-brand-add',
    'aboyuma_product_brand_add'
  );
  add_submenu_page(
    null,
    'Edit Brand',
    null,
    $productPageCapability,
    'aboyuma-product-brand-edit',
    'aboyuma_product_brand_edit'
  );
  add_submenu_page(
    $productPageMenuSlug,
    'Categories',
    'Categories',
    $productPageCapability,
    'aboyuma-product-category-list',
    'aboyuma_product_category_list'
  );
  add_submenu_page(
    null,
    'Add New Category',
    null,
    $productPageCapability,
    'aboyuma-product-category-add',
    'aboyuma_product_category_add'
  );
  add_submenu_page(
    null,
    'Edit Category',
    null,
    $productPageCapability,
    'aboyuma-product-category-edit',
    'aboyuma_product_category_edit'
  );
}
add_action('admin_menu', 'aboyuma_product_menu');

require_once get_template_directory() . '/theme-plugins/product-crud/product-functions.php';
require_once get_template_directory() . '/theme-plugins/product-crud/product-list.php';
require_once get_template_directory() . '/theme-plugins/product-crud/product-add.php';
require_once get_template_directory() . '/theme-plugins/product-crud/product-detail.php';
require_once get_template_directory() . '/theme-plugins/product-crud/product-edit.php';
require_once get_template_directory() . '/theme-plugins/product-crud/product-delete.php';
require_once get_template_directory() . '/theme-plugins/product-crud/product-brand-list.php';
require_once get_template_directory() . '/theme-plugins/product-crud/product-brand-add.php';
require_once get_template_directory() . '/theme-plugins/product-crud/product-brand-edit.php';
require_once get_template_directory() . '/theme-plugins/product-crud/product-brand-delete.php';
require_once get_template_directory() . '/theme-plugins/product-crud/product-category-list.php';
require_once get_template_directory() . '/theme-plugins/product-crud/product-category-add.php';
require_once get_template_directory() . '/theme-plugins/product-crud/product-category-edit.php';
require_once get_template_directory() . '/theme-plugins/product-crud/product-category-delete.php';
require_once get_template_directory() . '/theme-plugins/product-crud/config/product-menu-registration.php';
