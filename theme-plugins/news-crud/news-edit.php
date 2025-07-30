<?php
function aboyuma_news_edit_page() {
    global $wpdb;
    $table = $wpdb->prefix . 'aboyuma_news';

    $id = intval($_GET['id'] ?? 0);
    $news = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id));

    if (!$news) {
        echo '<div class="notice notice-error"><p>News not found.</p></div>';
        return;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Verify nonce
        if (
            !isset($_POST['aboyuma_news_nonce']) ||
            !wp_verify_nonce($_POST['aboyuma_news_nonce'], 'aboyuma_edit_news_action')
        ) {
            wp_die('Security check failed.');
        }
        $text = sanitize_text_field($_POST['news_text']);
        $date = sanitize_text_field($_POST['display_date']);
        $wpdb->update($table, [
            'news_text' => $text,
            'display_date' => $date ?: null,
        ], ['id' => $id]);
        echo '<div class="notice notice-success"><p>News updated successfully.</p></div>';

        // Reload updated data
        $news = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $id));
    }
?>
    <div class="wrap">
        <h1>Edit News</h1>
        <form method="post">
            <table class="form-table">
                <tr>
                    <th><label for="news_text">News Text</label></th>
                    <td><input type="text" name="news_text" id="news_text" class="regular-text" required value="<?= esc_attr($news->news_text) ?>"></td>
                </tr>
                <tr>
                    <th><label for="display_date">Display Date</label></th>
                    <td><input type="date" name="display_date" id="display_date" value="<?= esc_attr($news->display_date) ?>"></td>
                </tr>
            </table>
            <?php 
             wp_nonce_field('aboyuma_edit_news_action', 'aboyuma_news_nonce');
            submit_button('Update News'); 
            ?>
        </form>
    </div>
<?php
}
