<!-- Other Recommend Product Section -->
<div id="home-other-recommend-product" class="mb-2">
    <?php
    $args = array(
        'posts_per_page' => 4,
        'post_status'    => 'publish',
        'category_name'  => 'other-recommend-product',
    );
    $latest_posts = new WP_Query($args);
    ?>

    <div class="section-header mb-2 fw-bold">
        🛍️ สินค้าแนะนำอื่น
    </div>
    <?php
    if ($latest_posts->have_posts()) : ?>
        <div id="other-recommend-product-section" class="content-feed row">
            <?php while ($latest_posts->have_posts()) : $latest_posts->the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('col-md-6 mb-2'); ?>>
                    <div class="card h-100 overflow-hidden">

                        <div class="row g-0 h-100">
                            <div class="col-md-5">

                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('medium', ['class' => 'img-fluid h-100 w-100 object-fit-cover']); ?>
                                <?php else : ?>
                                    <img src="https://placehold.co/300x400?text=No+Image" class="img-fluid h-100 w-100 object-fit-cover" alt="">
                                <?php endif; ?>

                            </div>
                            <div class="col-md-7">
                                <div class="post-content card-body d-flex flex-column h-100 p-3">
                                    <span>
                                        <a href="<?php echo get_permalink(); ?>">
                                            📝 <?php echo mb_strimwidth(get_the_title(), 0, 50, '...'); ?>
                                        </a></span>
                                    <div class="post-category" style="font-size: 0.75rem;">
                                        <span class="text-muted">📂 </span>
                                        <?php
                                        $categories = get_the_category();
                                        if (!empty($categories)) {
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
                            </div>
                        </div>

                </article>
            <?php endwhile; ?>
            <?php wp_reset_postdata(); ?>
        </div>
    <?php else : ?>
        <p>ยังไม่มีสินค้าแนะนำอื่นๆ</p>
    <?php endif; ?>
</div><!-- Other Recommend Product Section -->