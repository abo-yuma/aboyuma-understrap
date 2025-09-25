<?php
/*
aboyuma Gallery Crud
Crud without plugin, implement in theme.
Reference: 
https://github.com/eduardoarandah/wordpress-crud-example
https://accreditly.io/articles/how-to-create-a-crud-in-wordpress-without-using-plugins#content-section-6-deleting-aboyuma-data
*/
// Create multiple tables reference: https://wordpress.stackexchange.com/a/341755/193936
function create_aboyuma_gallery_table() {
  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
  global $wpdb;

  $galleryTable = $wpdb->prefix . 'aboyuma_gallery';
  $galleryImageTable = $wpdb->prefix . 'aboyuma_gallery_image';
  $charset_collate = $wpdb->get_charset_collate();

  $queries = [];

  // Create aboyuma_gallery table
  array_push($queries, "CREATE TABLE $galleryTable (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    gallery_date DATE DEFAULT NULL,
    cover_image_id INT UNSIGNED DEFAULT NULL,
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    modified DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
  ) $charset_collate;");

  // Create aboyuma_gallery_image table
  array_push($queries, "CREATE TABLE $galleryImageTable (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    gallery_id INT UNSIGNED NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    caption MEDIUMTEXT DEFAULT NULL,
    image_order INT UNSIGNED DEFAULT 0,
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    modified DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
  ) $charset_collate;");

  // Run table creation queries
  foreach ($queries as $sql) {
    dbDelta($sql);
  }

  // 🔁 Add foreign keys if not exists
  $foreign_keys = [
    // Delete images if gallery is deleted
    [
      $galleryImageTable,
      'fk_aboyuma_gallery_image_gallery_id',
      "ALTER TABLE $galleryImageTable ADD CONSTRAINT fk_aboyuma_gallery_image_gallery_id 
       FOREIGN KEY (gallery_id) REFERENCES $galleryTable(id) ON DELETE CASCADE;"
    ],
    // Set NULL if the cover image is deleted
    [
      $galleryTable,
      'fk_aboyuma_gallery_cover_image_id',
      "ALTER TABLE $galleryTable ADD CONSTRAINT fk_aboyuma_gallery_cover_image_id 
       FOREIGN KEY (cover_image_id) REFERENCES $galleryImageTable(id) ON DELETE SET NULL;"
    ],
  ];

  foreach ($foreign_keys as [$table, $fk_name, $sql]) {
    if (!fk_exists($table, $fk_name)) {
      $wpdb->query($sql);
    }
  }
}
add_action('after_switch_theme', 'create_aboyuma_gallery_table');

// Load page templates
function aboyuma_gallery_menu() {
  add_menu_page(
    'Gallery',
    'Gallery',
    'manage_options',
    'aboyuma-gallery',
    'aboyuma_gallery_list_page',
    'dashicons-format-image',
    25
  );
  add_submenu_page(
    'aboyuma-gallery',
    'Add Gallery',
    'Add Gallery',
    'manage_options',
    'aboyuma-gallery-add',
    'aboyuma_gallery_add_page'
  );
  add_submenu_page(
    null,
    'Edit Gallery',
    'Edit Gallery',
    'manage_options',
    'aboyuma-gallery-edit',
    'aboyuma_gallery_edit_page'
  );
}
// ✅ Register admin menus (List, Add, Edit - hidden)
add_action('admin_menu', 'aboyuma_gallery_menu');

// Load page templates
require_once get_template_directory() . '/integrated-plugins/gallery-crud/gallery-list.php';
require_once get_template_directory() . '/integrated-plugins/gallery-crud/gallery-add.php';
require_once get_template_directory() . '/integrated-plugins/gallery-crud/gallery-edit.php';
require_once get_template_directory() . '/integrated-plugins/gallery-crud/gallery-delete.php';
