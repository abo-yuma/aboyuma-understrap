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
                            <div class="gallery-cover-wrapper">
                                <?php
                                if (!empty($row->image_path)) {
                                    $uploads = wp_get_upload_dir();
                                    // Prepend the uploads URL to your relative path
                                    $image_url = esc_url($uploads['baseurl'] . '/' . $row->image_path);
                                    echo '<img src="' . $image_url . '" alt="" class="gallery-cover" width="150" height="150">';
                                } else {
                                    echo '<span style="color:#999;">No image</span>';
                                }
                                ?>
                            </div>
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
    <style>
        .gallery-cover-wrapper {
            width: 150px;
            /* fixed box size */
            height: 150px;
            overflow: hidden;
            /* hide any overflow if image is larger */
            display: flex;
            align-items: center;
            /* vertical centering */
            justify-content: center;
            /* horizontal centering */
            background-color: #f0f0f0;
            /* optional: placeholder color */
        }

        .gallery-cover {
            width: 100%;
            height: 100%;
            object-fit: cover;
            /* fill box, crop if necessary */
            object-position: center;
        }
    </style>
<?php
}
