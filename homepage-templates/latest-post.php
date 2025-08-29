<!-- Latest Post Section -->
<div id="home-latest-post" class="mb-2">
    <?php
    // Get the category ID of 'other-recommend-product' to exclude it
    $excluded_category = get_category_by_slug('other-recommend-product');
    $exclude_id = $excluded_category ? $excluded_category->term_id : 0;

    $args = array(
        'posts_per_page' => 4,
        'post_status'    => 'publish',
        'category__not_in'  => array($exclude_id),
    );

    $latest_posts = new WP_Query($args);
    ?>
    <span style="font-weight: bold;">📰 บทความล่าสุด:</span>
    <hr>
    <?php
    if ($latest_posts->have_posts()) : ?>
        <div id="latest-posts-section" class="row">
            <?php while ($latest_posts->have_posts()) : $latest_posts->the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('col-6 mb-2'); ?>>
                    <div class="card h-100">
                        <div class="card-body" style="padding: 5px;">
                            <div class="row">
                                <div class="post-thumbnail col-md-5">
                                    <?php the_post_thumbnail(); ?>
                                </div>
                                <div class="col-md-7">
                                    <span><?php echo mb_strimwidth(get_the_title(), 0, 50, '...'); ?></span>
                                    <div class="post-category" style="font-size: 0.75rem;">
                                        <?php
                                        $categories = get_the_category();
                                        if (! empty($categories)) {
                                            $category_links = array();
                                            foreach ($categories as $category) {
                                                $category_links[] = '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . esc_html($category->name) . '</a>';
                                            }
                                            echo 'หมวดหมู่: ' . implode(', ', $category_links);
                                        }
                                        ?>
                                    </div>
                                    <div class="post-thumbnail-readmore">
                                        <?php echo '<a style="float: right;" href="' . get_permalink() . '"> อ่านบทความ...</a>'; ?>
                                    </div>
                                </div>
                            </div>
                        </div><!-- .entry-content -->
                    </div>
                </article><!-- /#post-<?php the_ID(); ?> -->
            <?php endwhile; ?>
            <?php wp_reset_postdata(); ?>
        </div>
    <?php
    endif;
    ?>
</div><!-- Latest Post Section -->