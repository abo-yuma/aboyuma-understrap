<?php
function enqueue_category_datatables_assets() {
  // aboyuma script to initialize DataTables
  wp_add_inline_script('datatables-js', '
   jQuery(document).ready(function($) {
      var table = $("#aboyuma-product-category-table").DataTable({
         "pageLength": 10,
         "searching": true,
         "responsive": true
      });
   });
');
}
add_action('admin_enqueue_scripts', 'enqueue_category_datatables_assets');

function aboyuma_amway_product_category_list() {
  global $wpdb;
  $table_name = $wpdb->prefix . 'aboyuma_amway_product_category';
  $results = $wpdb->get_results("
    SELECT c.*, p.category_name AS parent_name
    FROM $table_name c
    LEFT JOIN $table_name p ON c.parent_id = p.id
    ORDER BY c.created DESC
  ");

  if ($results < 0) {
    return;
  }
  if (isset($_GET['updated-success']) && $_GET['updated-success'] === 'true') {
    echo '<div class="notice notice-success is-dismissible">
            <p>Category updated successfully!</p>
          </div>';
  }
  // Product display page template
  echo '<div class="wrap">';
  echo '<h1 class="wp-heading-inline">ABO YUMA Amway Product Category</h1>';
  echo '<a href="' . admin_url('admin.php?page=aboyuma-product-category-add') . '" class="page-title-action">Add New</a>';
  echo '<hr class="wp-header-end">';
  // Add ID to the table for DataTables
  echo '<table id="aboyuma-product-category-table" class="wp-list-table widefat fixed striped">';
  echo '<thead>
					<tr>
              <th>Name</th>
              <th>Parent</th>
              <th>Created</th>
              <th>Modified</th>
              <th>Actions</th>
					</tr>
				</thead>';
  echo '<tbody>';
  foreach ($results as $row) {
    echo '<tr>';
    echo '<td>' . esc_html($row->category_name) . '</td>';
    echo '<td>' . esc_html($row->parent_name ?? '—') . '</td>';
    echo '<td>' . esc_html($row->created) . '</td>';
    echo '<td>' . esc_html($row->modified) . '</td>';
    echo '<td>
						<a href="' . admin_url('admin.php?page=aboyuma-product-category-edit&id=' . $row->id) . '">Edit</a> | 
						<a href="#" class="delete-category-link" data-id="' . $row->id . '">Delete</a>
					</td>';
    echo '</tr>';
  }
  echo '</tbody>';
  echo '</table>';
  echo '</div>';
}
