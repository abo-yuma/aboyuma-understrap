<?php

function delete_product_brand()
{
  check_ajax_referer('delete_product_brand_nonce', 'nonce');
  global $wpdb;
  $table_name = $wpdb->prefix . 'aboyuma_product_brand';
  $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

  $result = $wpdb->delete($table_name, array('id' => $id));
  wp_send_json_success($result);
}
add_action('wp_ajax_delete_product_brand', 'delete_product_brand');

function delete_product_brand_script()
{
  wp_enqueue_script('delete-product-brand', get_template_directory_uri() . '/integrated-plugins/product-crud/assets/delete-product-brand.js', array('jquery'), false, true);
  wp_localize_script('delete-product-brand', 'deleteProductBrand', array(
    'ajaxurl' => admin_url('admin-ajax.php'),
    'delete_product_brand_nonce' => wp_create_nonce('delete_product_brand_nonce')
  ));
}
add_action('admin_enqueue_scripts', 'delete_product_brand_script');