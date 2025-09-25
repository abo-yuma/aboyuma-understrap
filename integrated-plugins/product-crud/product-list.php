<?php
function enqueue_datatables_assets() {
  // Enqueue DataTables CSS and JS
  wp_enqueue_style('datatables-css', 'https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css');
  wp_enqueue_script(
    'datatables-js',
    'https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js',
    array('jquery'),
    null,
    true
  );
  // aboyuma script to initialize DataTables
  wp_add_inline_script('datatables-js', '
   jQuery(document).ready(function($) {
      var table = $("#aboyuma-product-table").DataTable({
         "pageLength": 10,
         "searching": true,
         "responsive": true,
         "columnDefs": [
            { "orderable": false, "targets": [2, 3, 4] },
            { "searchable": true, "targets": [2, 3, 4] }
         ]
      });
   });
');
}
add_action('admin_enqueue_scripts', 'enqueue_datatables_assets');

function aboyuma_product_list() {
  global $wpdb;
  $product_table_name = $wpdb->prefix . 'aboyuma_product';
  $productBrandTable = $wpdb->prefix . 'aboyuma_product_brand';
  $productCategoryTable = $wpdb->prefix . 'aboyuma_product_category';
  $products = $wpdb->get_results("
    SELECT
      p.id,
      p.product_image,
      p.product_name,
      p.product_short_description,
      p.created,
      p.modified,
      b.brand_name, 
      c.category_name
    FROM 
      $product_table_name AS p
    LEFT JOIN 
      $productBrandTable AS b ON p.product_brand_id = b.id
    LEFT JOIN 
      $productCategoryTable AS c ON p.product_category_id = c.id
    ORDER BY p.created DESC;
  ");
  if (isset($_GET['updated-success']) && $_GET['updated-success'] === 'true') {
    echo '<div class="notice notice-success is-dismissible">
            <p>Product updated successfully!</p>
          </div>';
  }
  // Product display page template
  echo '<div class="wrap">';
  echo '<h1 class="wp-heading-inline">aboyuma Product</h1>';
  echo '<a href="' . admin_url('admin.php?page=aboyuma-product-add') . '" class="page-title-action">Add New</a>';
  echo '<hr class="wp-header-end">';
  // Add ID to the table for DataTables
  echo '<table id="aboyuma-product-table" class="wp-list-table widefat fixed striped">';
  echo '<thead>
					<tr>
							<th>Image</th>
              <th>Brand</th>
              <th>Category</th>
							<th>Name</th>
              <th>Short Description</th>
							<th>Created</th>
							<th>Modified</th>
							<th>Actions</th>
					</tr>
				</thead>';
  echo '<tbody>';
  foreach ($products as $product) {
    echo '<tr>';
    echo '<td><img src="data:image/jpeg;base64,' . base64_encode($product->product_image) . '" width="100%" height="auto"></td>';
    echo '<td>' . esc_html($product->brand_name) . '</td>';
    echo '<td>' . esc_html($product->category_name) . '</td>';
    echo '<td>' . esc_html($product->product_name) . '</td>';
    echo '<td>' . esc_html($product->product_short_description) . '</td>';
    echo '<td>' . esc_html($product->created) . '</td>';
    echo '<td>' . esc_html($product->modified) . '</td>';
    echo '<td>
            <a href="' . admin_url('admin.php?page=aboyuma-product-detail&id=' . $product->id) . '">Detail</a> | 
						<a href="' . admin_url('admin.php?page=aboyuma-product-edit&id=' . $product->id) . '">Edit</a> | 
						<a href="#" class="delete-link" data-id="' . $product->id . '">Delete</a>
					</td>';
    echo '</tr>';
  }
  echo '</tbody>';
  echo '</table>';
  echo '</div>';
}
