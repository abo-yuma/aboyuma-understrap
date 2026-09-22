<?php
$current_post_id = get_the_ID();
$categories = wp_get_post_categories($current_post_id);

$related_posts = new WP_Query([
    'post_type'           => 'post',
    'posts_per_page'      => 2,
    'post__not_in'        => [$current_post_id],
    'category__in'        => $categories,
    'orderby'             => 'date',
    'order'               => 'DESC',
    'ignore_sticky_posts' => true,
    'post_status'         => 'publish',
]);
?>
<section class="related-post mt-4">

    <header class="related-post-header mb-3 pb-2 border-bottom">
        <h2 class="h5 mb-0">
            📝 บทความที่เกี่ยวข้อง
        </h2>
    </header>

    <div class="related-post-wrapper">

        <?php while ($related_posts->have_posts()) : $related_posts->the_post(); ?>

            <article
                <?php post_class('related-post-item mb-4'); ?>
                id="post-<?php the_ID(); ?>">

                <div class="row g-3">

                    <div class="col-5 col-sm-4">

                        <?php if (has_post_thumbnail()) : ?>

                            <a href="<?php the_permalink(); ?>">

                                <?php
                                the_post_thumbnail(
                                    'thumbnail',
                                    [
                                        'class' => 'img-fluid rounded',
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
                                    '<h3 class="entry-title h6 mb-1">',
                                    '</h3>'
                                );
                                ?>

                            </a>

                            <div class="entry-meta">
                                <?php understrap_posted_on(); ?>
                            </div>

                        </header>

                        <footer class="entry-footer">

                            <?php understrap_entry_footer(); ?>

                        </footer>

                    </div>

                </div>

            </article>

        <?php endwhile; ?>

    </div>

</section>