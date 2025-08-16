<?php
function aboyuma_gallery_add_page() {
    global $wpdb;
    $gallery_table = $wpdb->prefix . 'aboyuma_gallery';

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = sanitize_text_field($_POST['title'] ?? '');
        $description = sanitize_textarea_field($_POST['description'] ?? '');
        $gallery_date = !empty($_POST['gallery_date']) ? $_POST['gallery_date'] : NULL; // 'YYYY-MM-DD'

        // Insert gallery
        $wpdb->insert(
            $gallery_table,
            [
                'title' => $title,
                'description' => $description,
                'gallery_date' => $gallery_date
            ],
            ['%s', '%s', '%s']
        );
        $gallery_id = $wpdb->insert_id;

        // Redirect to edit images page
        echo '<script>window.location.href="' . admin_url('admin.php?page=aboyuma-gallery-edit&gallery_id=' . $gallery_id) . '";</script>';
        exit;
    }
?>
    <div class="wrap">
        <h1>Add Gallery</h1>
        <form method="post">
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="title">Title</label></th>
                    <td><input name="title" type="text" id="title" class="regular-text" required></td>
                </tr>
                <tr>
                    <th scope="row"><label for="description">Description</label></th>
                    <td><textarea name="description" id="description" class="large-text" rows="5"></textarea></td>
                </tr>
                <tr>
                    <th><label for="gallery_date">Gallery Date</label></th>
                    <td>
                        <input type="date" id="gallery_date" name="gallery_date">
                        <small>วันที่ที่เกี่ยวข้องกับแกลอรี่</small>
                    </td>
                </tr>
            </table>
            <?php submit_button('Add Gallery'); ?>
        </form>
    </div>
<?php
}
