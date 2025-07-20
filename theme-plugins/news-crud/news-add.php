<?php
function aboyuma_news_add_page() {
    global $wpdb;
    $table = $wpdb->prefix . 'aboyuma_news';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
            <?php submit_button('Add News'); ?>
        </form>
    </div>
<?php
}
