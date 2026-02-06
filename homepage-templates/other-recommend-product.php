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

    <h1 class="section-header">
        🛍️ สินค้าแนะนำล่าสุด
    </h1>

    <?php if ($latest_posts->have_posts()) : ?>
        <div id="other-recommend-product-section" class="content-feed row">
            <?php while ($latest_posts->have_posts()) : $latest_posts->the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('col-md-6 mb-2 align-self-stretch'); ?>>
                    <div class="card h-100 d-flex flex-column border-0">
                        <!-- Thumbnail -->
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

                        <!-- Content -->
                        <div class="card-body p-0 pt-2">
                            <div class="post-content d-flex flex-column h-100">

                                <!-- Title -->
                                <span>
                                    <a class="post-title-link" href="<?php echo get_permalink(); ?>">
                                        🛍️ <?php echo mb_strimwidth(get_the_title(), 0, 50, '...'); ?>
                                    </a>
                                </span>

                                <!-- Category -->
                                <div class="post-category" style="font-size: 0.75rem;">
                                    <span class="text-muted">📂 </span>
                                    <?php
                                    $categories = get_the_category();
                                    if (!empty($categories)) {
                                        $category_links = array();
                                        foreach ($categories as $category) {
                                            $category_links[] =
                                                '<a href="' . esc_url(get_category_link($category->term_id)) . '">' .
                                                esc_html($category->name) .
                                                '</a>';
                                        }
                                        echo implode(', ', $category_links);
                                    }
                                    ?>
                                </div>

                                <!-- Meta -->
                                <div class="post-meta text-muted" style="font-size: 0.75rem;">
                                    <span class="me-2">
                                        🗓 <?php echo get_the_date(); ?>
                                    </span>
                                    <span>
                                        📊 <?php echo number_format((int) get_post_meta(get_the_ID(), 'post_views_count', true)); ?>
                                    </span>
                                </div>

                                <!-- CTA -->
                                <div class="small mt-auto d-flex justify-content-end link-primary text-decoration-none">
                                    <a href="<?php echo get_permalink(); ?>">
                                        ดูรายละเอียด →
                                    </a>
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
</div>