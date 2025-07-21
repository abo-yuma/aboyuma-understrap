<!-- News Section -->
<div id="home-news" class="mb-2">
    <?php
    global $wpdb;
    $table_name = $wpdb->prefix . 'aboyuma_news';
    $news = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM {$table_name} 
             ORDER BY created DESC LIMIT %d",
            5
        )
    );
    if ($wpdb->last_error) {
        echo '<div style="color:red;">MySQL Error: ' . $wpdb->last_error . '</div>';
    }
    ?>
    <div class="card">
        <div class="card-header">📢 ข่าวประกาศ</div>
        <div class="card-body">
            <ul class="mb-0" style="list-style: none; padding-left: 0; margin-left: 0;">
                <?php foreach ($news as $item): ?>
                    <li style="margin-bottom: 0.5em;">
                        📰<?php echo esc_html($item->display_date).":"; ?>
                        <?php echo esc_html($item->news_text); ?>
                    </li>
                <?php endforeach; ?>
            </ul>

        </div>
    </div>
</div><!-- News Section -->