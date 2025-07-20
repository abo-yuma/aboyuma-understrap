<?php
function delete_product() {
  check_ajax_referer('delete_product_nonce', 'nonce');
  global $wpdb;
  $table_name = $wpdb->prefix . 'aboyuma_product';
  $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
  // ✅ 1. Get product_code by ID
  $product = $wpdb->get_row($wpdb->prepare("SELECT product_code FROM {$table_name} WHERE id = %d", $id));

  if (!$product) {
    wp_send_json_error('Product not found.');
    return;
  }

  $product_code = $product->product_code;

  // ✅ 2. Remove image folder
  $upload_dir = wp_upload_dir();
  $base_upload_path = trailingslashit($upload_dir['basedir']) . 'product-details/';
  $safe_folder_name = sanitize_title($product_code);
  $product_subdir = trailingslashit($base_upload_path . $safe_folder_name);

  if (file_exists($product_subdir)) {
    $files = glob($product_subdir . '*');
    foreach ($files as $file) {
      if (is_file($file)) {
        unlink($file);
      }
    }
    rmdir($product_subdir); // Remove the empty directory
  }
  
  $result = $wpdb->delete($table_name, array('id' => $id));
  wp_send_json_success($result);
}
add_action('wp_ajax_delete_product', 'delete_product');

function delete_product_script() {
  wp_enqueue_script('delete-product', get_template_directory_uri() . '/theme-plugins/product-crud/assets/delete-product.js', array('jquery'), false, true);
  wp_localize_script('delete-product', 'deleteProduct', array(
    'ajaxurl' => admin_url('admin-ajax.php'),
    'delete_product_nonce' => wp_create_nonce('delete_product_nonce')
  ));
}
add_action('admin_enqueue_scripts', 'delete_product_script');
