<section id="latest-post">

    <h2 class="h5 mb-3 pb-2 border-bottom">
        📝 บทความล่าสุด
    </h2>

    <?php

    $news_category = get_category_by_slug('news');

    $latest_posts = new WP_Query([
        'posts_per_page'   => 2,
        'post_status'      => 'publish',
        'category__not_in' => $news_category ? [$news_category->term_id] : [],
    ]);

    ?>

    <?php if ($latest_posts->have_posts()) : ?>

        <div class="latest-post-wrapper">

            <?php
            while ($latest_posts->have_posts()) :

                $latest_posts->the_post();
            ?>

                <article
                    <?php post_class('latest-post-item mb-4'); ?>
                    id="post-<?php the_ID(); ?>">

                    <div class="row g-3">

                        <div class="col-5 col-sm-4">

                            <?php if (has_post_thumbnail()) : ?>

                                <a
                                    href="<?php the_permalink(); ?>"
                                    class="latest-post-thumbnail">

                                    <?php
                                    the_post_thumbnail(
                                        'thumbnail',
                                        [
                                            'class' => 'img-fluid img-thumbnail rounded',
                                        ]
                                    );
                                    ?>

                                </a>

                            <?php else : ?>

                                <a href="<?php the_permalink(); ?>">

                                    <img
                                        src="https://placehold.co/150x150"
                                        alt=""
                                        width="150"
                                        height="150"
                                        class="img-fluid rounded">

                                </a>

                            <?php endif; ?>

                        </div>

                        <div class="col">

                            <header class="entry-header">

                                <a href="<?php the_permalink(); ?>"
                                    class="text-decoration-none">

                                    <?php
                                    the_title(
                                        '<h2 class="entry-title h6 mb-1">',
                                        '</h2>'
                                    );
                                    ?>

                                </a>

                                <?php if ('post' === get_post_type()) : ?>

                                    <div class="entry-meta">
                                        <?php understrap_posted_on(); ?>
                                    </div>

                                <?php endif; ?>

                            </header>

                            <div class="entry-content text-muted small">
                                <?php the_excerpt(); ?>
                                <div class="text-end mt-2">
                                    <a
                                        href="<?php the_permalink(); ?>"
                                        class="btn btn-outline-primary btn-sm">
                                        อ่านบทความ →
                                    </a>
                                </div>
                            </div>

                            <footer class="entry-footer">

                                <?php understrap_entry_footer(); ?>

                            </footer>

                        </div>

                    </div>

                </article>

            <?php endwhile; ?>

        </div>

    <?php else : ?>

        <div class="alert alert-secondary text-center">
            ยังไม่มีบทความ
        </div>

    <?php endif; ?>

    <?php wp_reset_postdata(); ?>

</section>