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
    <div class="card">
        <div class="card-header">🛍️ สินค้าแนะนำอื่นๆ</div>
        <div class="card-body">
            <?php
            if ($latest_posts->have_posts()) : ?>
                <div id="other-recommend-product-section" class="row">
                    <?php while ($latest_posts->have_posts()) : $latest_posts->the_post(); ?>
                        <article id="post-<?php the_ID(); ?>" <?php post_class('col-6 mb-2'); ?>>
                            <div class="card h-100">
                                <div class="card-body" style="padding: 5px;">
                                    <div class="row h-100">
                                        <div class="post-thumbnail col-md-5">
                                            <?php if (has_post_thumbnail()) : ?>
                                                <?php the_post_thumbnail(); ?>
                                            <?php else : ?>
                                                <img src="https://placehold.co/300x200?text=No+Image"
                                                    alt="No image available"
                                                    class="img-fluid">
                                            <?php endif; ?>
                                        </div>
                                        <div class="post-content col-md-7 d-flex flex-column">
                                            <span><?php echo mb_strimwidth(get_the_title(), 0, 50, '...'); ?></span>
                                            <div class="post-category" style="font-size: 0.75rem;">
                                                <?php
                                                $categories = get_the_category();
                                                if (!empty($categories)) {
                                                    $category_links = array();
                                                    foreach ($categories as $category) {
                                                        $category_links[] = '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . esc_html($category->name) . '</a>';
                                                    }
                                                    echo 'หมวดหมู่: ' . implode(', ', $category_links);
                                                }
                                                ?>
                                            </div>
                                            <div class="post-thumbnail-readmore mt-auto d-flex justify-content-end">
                                                <a href="<?php the_permalink(); ?>">อ่านเพิ่มเติม...</a>
                                            </div>
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
    </div>
</div><!-- Other Recommend Product Section -->