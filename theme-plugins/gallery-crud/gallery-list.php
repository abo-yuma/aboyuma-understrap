<?php
function aboyuma_gallery_list_page() {
    global $wpdb;
    $table = $wpdb->prefix . 'aboyuma_gallery';
    $rows = $wpdb->get_results("SELECT * FROM $table ORDER BY id DESC");

    // Enqueue DataTables CSS/JS
    wp_enqueue_style('datatables-css', 'https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css');
    wp_enqueue_script('datatables-js', 'https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js', ['jquery'], null, true);

    // Add inline script to init DataTable
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
        <h1>Gallery List</h1>
        <a href="admin.php?page=aboyuma-gallery-add" class="button button-primary">Add New</a>
        <table class="widefat striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Created</th>
                    <th>Modified</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td><?= esc_html($row->id) ?></td>
                        <td><?= esc_html($row->title) ?></td>
                        <td><?= esc_html($row->description) ?></td>
                        <td><?= esc_html($row->created) ?></td>
                        <td><?= esc_html($row->modified) ?></td>
                        <td>
                            <a href="admin.php?page=aboyuma-gallery-edit&gallery_id=<?= $row->id ?>" class="button">Edit</a>
                            <a href="#" class="button gallery-delete-button" data-id="<?= $row->id ?>">Delete</a>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
<?php
}
