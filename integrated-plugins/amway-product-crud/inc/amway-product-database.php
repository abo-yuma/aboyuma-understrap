<?php
function create_aboyuma_amway_product_table() {
  /*
  Create multiple tables reference: 
  - https://wordpress.stackexchange.com/a/341755/193936
  */
  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

  global $wpdb;
  $productTable = $wpdb->prefix . 'aboyuma_amway_product';
  $productPropertyTable = $wpdb->prefix . 'aboyuma_amway_product_property';
  $productPropertyPivotTable = $wpdb->prefix . 'aboyuma_amway_product_property_pivot';
  $productBrandTable = $wpdb->prefix . 'aboyuma_amway_product_brand';
  $productCategoryTable = $wpdb->prefix . 'aboyuma_amway_product_category';
  $productTagTable = $wpdb->prefix . 'aboyuma_amway_product_tag';
  $productImageTable = $wpdb->prefix . 'aboyuma_amway_product_image';
  $productVideoTable = $wpdb->prefix . 'aboyuma_amway_product_video';

  $charset_collate = $wpdb->get_charset_collate();
  // Prepare query statements
  $queries = array();

  /** =========================================
   * Product Brand
   * Stores brand information and external logo URL
   * ========================================= */
  array_push($queries, "CREATE TABLE $productBrandTable (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,

    brand_image_url VARCHAR(255) NOT NULL,
    brand_name VARCHAR(255) NOT NULL,

    created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
    modified DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    KEY idx_brand_name (brand_name)

  ) $charset_collate;");

  /*********************************************
   * Product Category
   * Supports hierarchical structure via parent_id
   *********************************************/
  array_push($queries, "CREATE TABLE $productCategoryTable (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,

    category_name VARCHAR(255) NOT NULL,
    parent_id INT UNSIGNED DEFAULT NULL,

    created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    modified DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    -- For hierarchy queries
    KEY idx_parent_id (parent_id),
    -- For filtering/search
    KEY idx_category_name (category_name)

  ) $charset_collate;");

  /**
   * =========================================
   * Product
   * Core product entity table
   * =========================================
   */
  array_push($queries, "CREATE TABLE $productTable (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,

    main_image_id BIGINT UNSIGNED NULL,

    product_code VARCHAR(50) NOT NULL,
    product_brand_id INT UNSIGNED NULL,
    product_category_id INT UNSIGNED NULL,

    product_name VARCHAR(255) NOT NULL,
    product_unit_count INT UNSIGNED NULL,

    product_unit_type ENUM('tablet','capsule','sachet','g','ml') NOT NULL,
    product_unit_usage ENUM('content','capacity') NOT NULL DEFAULT 'content',

    product_short_description LONGTEXT NOT NULL,
    product_description LONGTEXT NOT NULL,
    product_benefit LONGTEXT NOT NULL,
    product_detail LONGTEXT NOT NULL,

    product_specification LONGTEXT NULL,
    product_comment TEXT NULL,
    product_review LONGTEXT NULL,

    product_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    product_member_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,

    product_sop_link VARCHAR(255) NULL,
    product_shop_link VARCHAR(255) NOT NULL,
    product_sale_article_link VARCHAR(255) NULL,

    product_is_pinned TINYINT(1) NOT NULL DEFAULT 0,
    product_slug VARCHAR(255) NOT NULL,

    created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    modified DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    UNIQUE KEY uq_product_slug (product_slug),
    UNIQUE KEY uq_product_code (product_code),

    KEY idx_brand_id (product_brand_id),
    KEY idx_category_id (product_category_id),
    KEY idx_main_image_id (main_image_id),
    KEY idx_price (product_price),
    KEY idx_is_pinned (product_is_pinned)

  ) ENGINE=InnoDB $charset_collate;");

  /**
   * =========================================
   * Product Property Definition
   * =========================================
   */
  array_push($queries, "CREATE TABLE $productPropertyTable (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,

    property_key VARCHAR(50) NOT NULL,
    property_name VARCHAR(100) NOT NULL,

    property_type ENUM('additive','diet','program','feature') NOT NULL,

    property_description LONGTEXT NULL,

    created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    modified DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    UNIQUE KEY uq_property_key (property_key),
    KEY idx_property_type (property_type)

  ) ENGINE=InnoDB $charset_collate;");


  /**
   * =========================================
   * Product Property Pivot (Many-to-Many)
   * =========================================
   */
  array_push($queries, "CREATE TABLE $productPropertyPivotTable (
    product_id INT UNSIGNED NOT NULL,
    property_id INT UNSIGNED NOT NULL,

    PRIMARY KEY (product_id, property_id),

    KEY idx_property_id (property_id),

    CONSTRAINT fk_product_property_product
      FOREIGN KEY (product_id)
      REFERENCES $productTable(id)
      ON DELETE CASCADE,

    CONSTRAINT fk_product_property_definition
      FOREIGN KEY (property_id)
      REFERENCES $productPropertyTable(id)
      ON DELETE CASCADE

  ) ENGINE=InnoDB $charset_collate;");
  /** =========================================
   * Product Tag
   * ========================================= */
  array_push($queries, "CREATE TABLE $productTagTable (
    product_id INT UNSIGNED NOT NULL,
    tag_name VARCHAR(255) NOT NULL,
    created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    modified DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (product_id, tag_name),
    
    KEY idx_tag_name (tag_name)
  ) $charset_collate;");

  /** =========================================
   * Product Gallery Image
   * Stores additional product images
   * Uses WordPress attachment IDs
   * ========================================= */
  array_push($queries, "CREATE TABLE $productImageTable (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,

    product_id INT UNSIGNED NOT NULL,
    attachment_id BIGINT UNSIGNED NOT NULL,
    image_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,

    created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    modified DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE KEY uq_product_attachment (product_id, attachment_id),

    -- Composite index for gallery loading + sorting
    KEY idx_product_order (product_id, image_order),

    -- For joining with wp_posts
    KEY idx_attachment_id (attachment_id)

  ) $charset_collate;");

  /** =========================================
   * Product Video
   * Stores external video links related to product
   * ========================================= */
  array_push($queries, "CREATE TABLE $productVideoTable (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,

    product_id INT UNSIGNED NOT NULL,
    video_url VARCHAR(255) NOT NULL,
    video_comment VARCHAR(255) NOT NULL,

    created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    modified DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    -- FK performance
    KEY idx_product_id (product_id)

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
  // ===============================
  // FOREIGN KEY DEFINITIONS
  // Safer & Production Ready
  // ===============================

  // Make sure ALL tables use InnoDB before this runs.

  $foreign_keys = [
    /*
    |--------------------------------------------------------------------------
    | CATEGORY - SELF PARENT
    |--------------------------------------------------------------------------
    */
    [$productCategoryTable, 'fk_aboyuma_category_parent', "
      ALTER TABLE $productCategoryTable
      ADD INDEX idx_category_parent_id (parent_id),
      ADD CONSTRAINT fk_aboyuma_category_parent
      FOREIGN KEY (parent_id)
      REFERENCES $productCategoryTable(id)
      ON DELETE SET NULL
    "],
    /*
    |--------------------------------------------------------------------------
    | PRODUCT PROPERTY PIVOT → PRODUCT
    |--------------------------------------------------------------------------
    */
    [$productPropertyPivotTable, 'fk_aboyuma_product_property_product_id', "
      ALTER TABLE $productPropertyPivotTable
      ADD CONSTRAINT fk_aboyuma_product_property_product_id
      FOREIGN KEY (product_id)
      REFERENCES $productTable(id)
      ON DELETE CASCADE
    "],

    /*
    |--------------------------------------------------------------------------
    | PRODUCT PROPERTY PIVOT → PROPERTY DEFINITION
    |--------------------------------------------------------------------------
    */
    [$productPropertyPivotTable, 'fk_aboyuma_product_property_definition_id', "
      ALTER TABLE $productPropertyPivotTable
      ADD CONSTRAINT fk_aboyuma_product_property_definition_id
      FOREIGN KEY (property_id)
      REFERENCES $productPropertyTable(id)
      ON DELETE CASCADE
    "],

    /*
    |--------------------------------------------------------------------------
    | PRODUCT → MAIN IMAGE (wp_posts)
    |--------------------------------------------------------------------------
    */
    [$productTable, 'fk_aboyuma_product_main_image', "
      ALTER TABLE $productTable
      ADD INDEX idx_main_image_id (main_image_id),
      ADD CONSTRAINT fk_aboyuma_product_main_image
      FOREIGN KEY (main_image_id)
      REFERENCES {$wpdb->posts}(ID)
      ON DELETE SET NULL
    "],

    /*
    |--------------------------------------------------------------------------
    | PRODUCT IMAGE → ATTACHMENT (wp_posts)
    |--------------------------------------------------------------------------
    */
    [$productImageTable, 'fk_aboyuma_product_image_attachment', "
      ALTER TABLE $productImageTable
      ADD INDEX idx_attachment_id (attachment_id),
      ADD CONSTRAINT fk_aboyuma_product_image_attachment
      FOREIGN KEY (attachment_id)
      REFERENCES {$wpdb->posts}(ID)
      ON DELETE CASCADE
    "],

    /*
    |--------------------------------------------------------------------------
    | PRODUCT → BRAND (SAFE DELETE)
    |--------------------------------------------------------------------------
    */
    [$productTable, 'fk_aboyuma_product_brand_id', "
      ALTER TABLE $productTable
      ADD INDEX idx_product_brand_id (product_brand_id),
      ADD CONSTRAINT fk_aboyuma_product_brand_id
      FOREIGN KEY (product_brand_id)
      REFERENCES $productBrandTable(id)
      ON DELETE SET NULL
    "],

    /*
    |--------------------------------------------------------------------------
    | PRODUCT → CATEGORY (SAFE DELETE)
    |--------------------------------------------------------------------------
    */
    [$productTable, 'fk_aboyuma_product_category_id', "
      ALTER TABLE $productTable
      ADD INDEX idx_product_category_id (product_category_id),
      ADD CONSTRAINT fk_aboyuma_product_category_id
      FOREIGN KEY (product_category_id)
      REFERENCES $productCategoryTable(id)
      ON DELETE SET NULL
    "],

    /*
    |--------------------------------------------------------------------------
    | PRODUCT TAG → PRODUCT
    |--------------------------------------------------------------------------
    */
    [$productTagTable, 'fk_aboyuma_product_tag_product_id', "
      ALTER TABLE $productTagTable
      ADD INDEX idx_tag_product_id (product_id),
      ADD CONSTRAINT fk_aboyuma_product_tag_product_id
      FOREIGN KEY (product_id)
      REFERENCES $productTable(id)
      ON DELETE CASCADE
    "],

    /*
    |--------------------------------------------------------------------------
    | PRODUCT IMAGE → PRODUCT
    |--------------------------------------------------------------------------
    */
    [$productImageTable, 'fk_aboyuma_product_image_product_id', "
      ALTER TABLE $productImageTable
      ADD INDEX idx_product_image_product_id (product_id),
      ADD CONSTRAINT fk_aboyuma_product_image_product_id
      FOREIGN KEY (product_id)
      REFERENCES $productTable(id)
      ON DELETE CASCADE
    "],

    /*
    |--------------------------------------------------------------------------
    | PRODUCT VIDEO → PRODUCT
    |--------------------------------------------------------------------------
    */
    [$productVideoTable, 'fk_aboyuma_product_video_product_id', "
      ALTER TABLE $productVideoTable
      ADD INDEX idx_product_video_product_id (product_id),
      ADD CONSTRAINT fk_aboyuma_product_video_product_id
      FOREIGN KEY (product_id)
      REFERENCES $productTable(id)
      ON DELETE CASCADE
    "],
  ];

  // ========================================
  // Execute FK creation safely
  // ========================================
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
