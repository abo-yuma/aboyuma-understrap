<?php
function aboyuma_gallery_add_page() {
    global $wpdb;
    $table = $wpdb->prefix . 'aboyuma_gallery';

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = sanitize_text_field($_POST['title'] ?? '');
        $description = sanitize_textarea_field($_POST['description'] ?? '');

        // Insert gallery
        $wpdb->insert($table, [
            'title'       => $title,
            'description' => $description,
            'cover_image_id' => null
        ]);
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
            </table>
            <?php submit_button('Add Gallery'); ?>
        </form>
    </div>
<?php
}
