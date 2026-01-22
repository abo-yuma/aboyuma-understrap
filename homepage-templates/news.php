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

    <div class="section-header mb-2 fw-bold">
        📢 ข่าวประกาศ
    </div>

    <div class="card mb-2">
        <div class="card-body py-2 px-3">
            <ul class="news-list mb-0">
                <?php foreach ($news as $item): ?>
                    <li class="news-item">
                        <div class="news-date">
                            🗓 <?php echo esc_html($item->display_date); ?>
                        </div>
                        <div class="news-text">
                            <?php echo esc_html($item->news_text); ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

</div><!-- News Section -->
<style>
    .news-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .news-item:not(:last-child) {
        padding-bottom: 8px;
        margin-bottom: 8px;
        border-bottom: 1px dashed var(--bs-border-color);
    }

    .news-date {
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--bs-secondary-color);
        line-height: 1.2;
    }

    .news-text {
        font-size: 0.95rem;
        color: var(--bs-body-color);
        line-height: 1.4;
    }

    .news-item:nth-child(even) {
        background-color: var(--bs-secondary-bg);
        border-radius: 6px;
        padding: 6px 8px;
    }
</style>