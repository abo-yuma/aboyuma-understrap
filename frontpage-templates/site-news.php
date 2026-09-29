<section class="site-news mb-4">

    <h2 class="h5 mb-3 pb-2 border-bottom">
        📰 ข่าวสาร
    </h2>

    <?php
    $news_posts = new WP_Query([
        'post_type'           => 'news',
        'posts_per_page'      => 5,
        'post_status'         => 'publish',
        'ignore_sticky_posts' => true,
    ]);
    ?>

    <?php if ($news_posts->have_posts()) : ?>

        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">

                <tbody>

                    <?php
                    while ($news_posts->have_posts()) :
                        $news_posts->the_post();
                    ?>

                        <tr>

                            <td class="text-muted small text-nowrap">
                                🗓️ <?php echo esc_html(get_the_date()) . ':'; ?>
                            </td>

                            <td>
                                <a
                                    href="<?php the_permalink(); ?>"
                                    class="text-decoration-none">

                                    <?php the_title(); ?>

                                </a>
                            </td>

                        </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>
        </div>

    <?php else : ?>

        <div class="alert alert-secondary">
            ยังไม่มีข่าวสาร
        </div>

    <?php endif; ?>

    <?php wp_reset_postdata(); ?>

</section>