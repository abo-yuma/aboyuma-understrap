<?php
function aboyuma_gallery_list_page() {
    global $wpdb;
    $gallery_table  = $wpdb->prefix . 'aboyuma_gallery';
    $image_table   = $wpdb->prefix . 'aboyuma_gallery_image';

    // Fetch rows with cover image path
    $rows = $wpdb->get_results("
        SELECT g.*, gi.image_path
        FROM {$gallery_table} g
        LEFT JOIN {$image_table} gi 
            ON g.cover_image_id = gi.id
        ORDER BY g.id DESC
    ");

?>
    <div class="wrap">
        <h1>Gallery List</h1>
        <a href="admin.php?page=aboyuma-gallery-add" class="button button-primary">Add New</a>
        <table id="aboyuma-gallery-list" class="widefat striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cover</th>
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
                        <td>
                            <?php
                            if (!empty($row->image_path)) {
                                $uploads = wp_get_upload_dir();
                                // Prepend the uploads URL to your relative path
                                $image_url = esc_url($uploads['baseurl'] . '/' . $row->image_path);
                                echo '<img src="' . $image_url . '" alt="" style="max-width:150px; max-height:150px;">';
                            } else {
                                echo '<span style="color:#999;">No image</span>';
                            }
                            ?>
                        </td>
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
    <script>
        jQuery(document).ready(function($) {
            $('#aboyuma-gallery-list').DataTable({
                order: [
                    [0, 'desc']
                ],
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],
                columnDefs: [{
                        orderable: false,
                        targets: 1
                    } // disable sorting on Cover column
                ]
            });
        });
    </script>
<?php
}
