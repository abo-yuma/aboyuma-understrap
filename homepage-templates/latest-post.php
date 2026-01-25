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


    <div class="section-header mb-2 fw-bold">
        📰 บทความล่าสุด
    </div>
    <?php
    if ($latest_posts->have_posts()) : ?>
        <div id="latest-posts-section" class="content-feed row">
            <?php while ($latest_posts->have_posts()) : $latest_posts->the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('col-md-6 mb-2 align-self-stretch'); ?>>
                    <div class="card h-100">
                        <div class="card-img-top">
                            <div class="post-thumbnail">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail(); ?>
                                <?php else : ?>
                                    <img src="https://placehold.co/100x130?text=No+Image"
                                        alt="No image available"
                                        class="img-fluid w-100">
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <div class="post-content d-flex flex-column">
                                <span>
                                    <a class="post-title-link" href="<?php echo get_permalink(); ?>">
                                        📝 <?php echo mb_strimwidth(get_the_title(), 0, 50, '...'); ?>
                                    </a>
                                </span>
                                <div class="post-category" style="font-size: 0.75rem;">
                                    <span class="text-muted">📂 </span>
                                    <?php
                                    $categories = get_the_category();
                                    if (! empty($categories)) {
                                        $category_links = array();
                                        foreach ($categories as $category) {
                                            $category_links[] = '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . esc_html($category->name) . '</a>';
                                        }
                                        echo implode(', ', $category_links);
                                    }
                                    ?>
                                </div>
                                <div class="post-meta text-muted" style="font-size: 0.75rem;">
                                    <span class="me-2">
                                        🗓 
                                        <?php echo get_the_date(); ?>
                                    </span>

                                    <span>
                                        📊
                                        <?php echo number_format((int) get_post_meta(get_the_ID(), 'post_views_count', true)); ?>
                                    </span>
                                </div>
                                <div class="small mt-auto d-flex justify-content-end link-primary text-decoration-none">
                                    <?php echo '<a href="' . get_permalink() . '"> อ่านบทความ →</a>'; ?>
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