<?php
function enqueue_brand_datatables_assets()
{
  // aboyuma script to initialize DataTables
  wp_add_inline_script('datatables-js', '
   jQuery(document).ready(function($) {
      var table = $("#aboyuma-product-brand-table").DataTable({
         "pageLength": 10,
         "searching": true,
         "responsive": true
      });
   });
');
}
add_action('admin_enqueue_scripts', 'enqueue_brand_datatables_assets');

function aboyuma_amway_product_brand_list()
{
  global $wpdb;
  $table_name = $wpdb->prefix . 'aboyuma_amway_product_brand';
  $results = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created DESC");
  if ($results < 0) {
    return;
  }
  if (isset($_GET['updated-success']) && $_GET['updated-success'] === 'true') {
    echo '<div class="notice notice-success is-dismissible">
            <p>Brand updated successfully!</p>
          </div>';
  }
  // Product display page template
  echo '<div class="wrap">';
  echo '<h1 class="wp-heading-inline">aboyuma Product</h1>';
  echo '<a href="' . admin_url('admin.php?page=aboyuma-product-brand-add') . '" class="page-title-action">Add New</a>';
  echo '<hr class="wp-header-end">';
  // Add ID to the table for DataTables
  echo '<table id="aboyuma-product-brand-table" class="wp-list-table widefat fixed striped">';
  echo '<thead>
					<tr>
              <th>Image</th>
              <th>Name</th>
              <th>Created</th>
              <th>Modified</th>
              <th>Actions</th>
					</tr>
				</thead>';
  echo '<tbody>';
  foreach ($results as $row) {
    echo '<tr>';
    echo '<td>' . '<img src="' . esc_url($row->brand_image_url) . '" width="100%" height="auto">' . '</td>';
    echo '<td>' . esc_html($row->brand_name) . '</td>';
    echo '<td>' . esc_html($row->created) . '</td>';
    echo '<td>' . esc_html($row->modified) . '</td>';
    echo '<td>
						<a href="' . admin_url('admin.php?page=aboyuma-product-brand-edit&id=' . $row->id) . '">Edit</a> | 
						<a href="#" id="delete-brand-link" data-id="' . $row->id . '">Delete</a>
					</td>';
    echo '</tr>';
  }
  echo '</tbody>';
  echo '</table>';
  echo '</div>';
}
