<!-- Latest Post Section -->
<section id="home-latest-post" class="mb-3">
    <?php
    // Get the category ID of 'recommend-products' to exclude it
    $excluded_category = get_category_by_slug('recommend-products');
    $exclude_id = $excluded_category ? $excluded_category->term_id : 0;

    $args = array(
        'posts_per_page' => 4,
        'post_status'    => 'publish',
        'category__not_in'  => array($exclude_id),
    );

    $latest_posts = new WP_Query($args);
    ?>

    <h1 class="section-header mb-3">
        📝 บทความล่าสุด
    </h1>
    <?php
    if ($latest_posts->have_posts()) : ?>
        <div id="latest-posts-section" class="latest-post-feed row">
            <?php while ($latest_posts->have_posts()) : $latest_posts->the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('col-md-6 mb-3 align-self-stretch'); ?>>
                    <div class="card border-0 h-100 d-flex flex-column">
                        <div class="card-img-top">
                            <div class="post-thumbnail">
                                <a href="<?php the_permalink(); ?>">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <?php the_post_thumbnail(); ?>
                                    <?php else : ?>
                                        <img src="https://placehold.co/100x130?text=No+Image"
                                            alt="No image available"
                                            class="img-fluid w-100">
                                    <?php endif; ?>
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-0 pt-2 d-flex flex-column h-100">
                            <div class="d-flex flex-column h-100">
                                <div class="entry-title mb-1">
                                    <span>
                                        <a href="<?php echo get_permalink(); ?>">
                                            <?php echo mb_strimwidth(get_the_title(), 0, 50, '...'); ?>
                                        </a>
                                    </span>
                                </div>
                                <div class="entry-meta text-muted">
                                    <span>🗂️
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
                                    <span class="me-2">
                                        🗓
                                        <?php
                                        echo get_the_date();
                                        if (function_exists('aboyuma_get_post_views')) {
                                            echo ' ' . aboyuma_get_post_views(get_the_ID());
                                        }
                                        ?>
                                    </span>
                                </div>
                                <div class="small mt-auto pt-3 d-flex justify-content-end">
                                    <?php echo '<a class="btn btn-sm btn-outline-primary" href="' . get_permalink() . '"> อ่านบทความ →</a>'; ?>
                                </div>
                            </div>

                        </div><!-- .entry-content -->
                    </div>
                </article><!-- /#post-<?php the_ID(); ?> -->
            <?php endwhile; ?>
            <?php wp_reset_postdata(); ?>
        </div>
    <?php else : ?>
        <div class="alert alert-secondary text-center">
            ยังไม่มีบทความ
        </div>
    <?php endif; ?>

</section><!-- Latest Post Section -->