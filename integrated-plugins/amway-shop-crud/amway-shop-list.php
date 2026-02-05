<?php
function aboyuma_amway_shop_list() {
    global $wpdb;
    $table = $wpdb->prefix . 'aboyuma_amway_shop';
    $rows = $wpdb->get_results("SELECT * FROM $table ORDER BY id DESC");

    // Enqueue DataTables CSS/JS
    wp_enqueue_style('datatables-css', 'https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css');
    wp_enqueue_script('datatables-js', 'https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js', ['jquery'], null, true);

    // DataTables init
    wp_add_inline_script('datatables-js', "
      jQuery(document).ready(function($) {
        $('.widefat').DataTable({
          order: [[0, 'desc']],
          pageLength: 10,
          lengthMenu: [5, 10, 25, 50]
        });
      });
    ");
?>
    <div class="wrap">
        <h1>Amway Shop List</h1>

        <a href="admin.php?page=aboyuma-amway-shop-add" class="button button-primary" style="margin-bottom:15px;">
            Add New Shop
        </a>

        <table class="widefat striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Shop Code</th>
                    <th>Shop Name</th>
                    <th>Phone</th>
                    <th>Map</th>
                    <th>Created</th>
                    <th>Modified</th>
                    <th width="130">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td><?= esc_html($row->id) ?></td>
                        <td><?= esc_html($row->shop_code) ?></td>
                        <td><?= esc_html($row->shop_name) ?></td>
                        <td><?= esc_html($row->shop_phone) ?></td>
                        <td>
                            <?php if (!empty($row->shop_map_url)): ?>
                                <a href="<?= esc_url($row->shop_map_url) ?>" target="_blank">View Map</a>
                            <?php endif; ?>
                        </td>
                        <td><?= esc_html($row->created) ?></td>
                        <td><?= esc_html($row->modified) ?></td>

                        <td>
                            <a href="admin.php?page=aboyuma-amway-shop-edit&id=<?= $row->id ?>" class="button">Edit</a>

                            <a href="#"
                                class="button amway-shop-delete-button"
                                data-id="<?= $row->id ?>"
                                style="color:#a00;">
                                Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
<?php
}
