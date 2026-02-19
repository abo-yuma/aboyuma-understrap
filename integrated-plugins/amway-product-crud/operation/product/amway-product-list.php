<?php
defined('ABSPATH') || exit;

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
        pageLength: 10,
        searching: true,
        responsive: true,
        columnDefs: [
          {
            width: "20%",
            targets: 0,
          },
          {
            width: "35%",
            targets: 1,
          },
          {
            width: "15%",
            targets: 2,
          },
          {
            width: "15%",
            targets: 3,
          },
          {
            width: "15%",
            targets: 4,
          },
        ],
      });
    });
  ');

  wp_add_inline_style('datatables-css', '
        .product-list-image-wrapper {
            aspect-ratio: 1 / 1;
            overflow: hidden;
        }
        .product-list-image-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
    ');
}
add_action('admin_enqueue_scripts', 'enqueue_datatables_assets');

function aboyuma_amway_product_list() {
  global $wpdb;
  $product_table_name = $wpdb->prefix . 'aboyuma_amway_product';
  $products = $wpdb->get_results("
    SELECT
      id,
      main_image_id,
      product_description,
      created,
      modified
    FROM 
      $product_table_name
    ORDER BY created DESC;
  ");
  if (isset($_GET['updated-success']) && $_GET['updated-success'] === 'true') {
    echo '<div class="notice notice-success is-dismissible">
            <p>Product updated successfully!</p>
          </div>';
  }
  // Product display page template
  echo '<div class="wrap">';
  echo '<h1 class="wp-heading-inline">ABO YUMA Amway Product</h1>';
  echo '<a href="' . admin_url('admin.php?page=aboyuma-product-add') . '" class="page-title-action">Add New</a>';
  echo '<hr class="wp-header-end">';
  // Add ID to the table for DataTables
  echo '<table id="aboyuma-product-table" class="wp-list-table widefat fixed striped">';
  echo '<thead>
					<tr>
							<th>Image</th>
              <th>Description</th>
							<th>Created</th>
							<th>Modified</th>
							<th>Actions</th>
					</tr>
				</thead>';
  echo '<tbody>';
  foreach ($products as $product) {
    echo '<tr>';
    echo '
    <td>
      image
    </td>';
    echo '<td>' . esc_html($product->product_description) . '</td>';
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
