<?php

function delete_product_category() {
  check_ajax_referer('delete_product_category_nonce', 'nonce');
  global $wpdb;

  $category_id = isset($_POST['id']) ? intval($_POST['id']) : 0;
  $product_table = $wpdb->prefix . 'aboyuma_product';
  $category_table = $wpdb->prefix . 'aboyuma_product_category';

  // Check if any product is using this category
  $count = $wpdb->get_var($wpdb->prepare(
    "SELECT COUNT(*) FROM $product_table WHERE product_category_id = %d",
    $category_id
  ));

  if ($count > 0) {
    wp_send_json_error('Cannot delete: This category is currently used by one or more products.');
  }

  $result = $wpdb->delete($category_table, ['id' => $category_id]);

  if ($result !== false) {
    wp_send_json_success();
  } else {
    wp_send_json_error('Failed to delete the category. Please try again.');
  }
}
add_action('wp_ajax_delete_product_category', 'delete_product_category');

function delete_product_category_script() {
  wp_enqueue_script('delete-product-category', get_template_directory_uri() . '/theme-plugins/product-crud/assets/delete-product-category.js', array('jquery'), false, true);
  wp_localize_script('delete-product-category', 'deleteProductCategory', array(
    'ajaxurl' => admin_url('admin-ajax.php'),
    'delete_product_category_nonce' => wp_create_nonce('delete_product_category_nonce')
  ));
}
add_action('admin_enqueue_scripts', 'delete_product_category_script');
