<section class="site-news mb-4">

    <h2 class="h5 mb-3 pb-2 border-bottom">
        📰 ข่าวสาร
    </h2>

    <?php
    $news_posts = new WP_Query([
        'post_type'           => 'post',
        'posts_per_page'      => 5,
        'category_name'       => 'news',
        'post_status'         => 'publish',
        'ignore_sticky_posts' => true,
    ]);
    ?>

    <?php if ($news_posts->have_posts()) : ?>

        <div class="site-news-wrapper border bg-body-secondary rounded p-3">

            <?php
            while ($news_posts->have_posts()) :
                $news_posts->the_post();
            ?>

                <article class="site-news-entry pb-3 mb-3 border-bottom">

                    <header class="site-news-entry-header mb-1">

                        <div class="small text-muted">
                            🗓 <?php echo esc_html(get_the_date()); ?>
                        </div>

                    </header>

                    <div class="site-news-entry-content">

                        <a
                            href="<?php the_permalink(); ?>"
                            class="text-decoration-none">

                            <?php the_title(); ?>

                        </a>

                    </div>

                </article>

            <?php endwhile; ?>

        </div>

    <?php else : ?>

        <div class="alert alert-secondary">
            ยังไม่มีข่าวสาร
        </div>

    <?php endif; ?>

    <?php wp_reset_postdata(); ?>

</section>