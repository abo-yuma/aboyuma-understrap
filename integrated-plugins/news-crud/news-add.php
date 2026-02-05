<?php
function aboyuma_news_add_page() {
    global $wpdb;
    $table = $wpdb->prefix . 'aboyuma_news';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Verify nonce
        if (
            !isset($_POST['aboyuma_news_nonce']) ||
            !wp_verify_nonce($_POST['aboyuma_news_nonce'], 'aboyuma_add_news_action')
        ) {
            wp_die('Security check failed.');
        }

        $text = sanitize_text_field($_POST['news_text']);
        $date = sanitize_text_field($_POST['display_date']);
        $wpdb->insert($table, [
            'news_text' => $text,
            'display_date' => $date ?: null,
        ]);
        echo '<div class="notice notice-success"><p>News added successfully.</p></div>';
    }
?>
    <div class="wrap">
        <h1>Add News</h1>
        <form method="post">
            <table class="form-table">
                <tr>
                    <th><label for="news_text">News Text</label></th>
                    <td><input type="text" name="news_text" id="news_text" class="regular-text" required></td>
                </tr>
                <tr>
                    <th><label for="display_date">Display Date</label></th>
                    <td><input type="date" name="display_date" id="display_date"></td>
                </tr>
            </table>
            <?php
            // Nonce field
            wp_nonce_field('aboyuma_add_news_action', 'aboyuma_news_nonce');
            submit_button('Add News');
            ?>
        </form>
    </div>
<?php
}
