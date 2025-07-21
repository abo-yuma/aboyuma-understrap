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
    <span style="font-weight: bold;">🛍️สินค้าแนะนำอื่นๆ:</span>
    <hr>
    <?php
    if ($latest_posts->have_posts()) : ?>
        <div id="other-recommend-product-section" class="row">
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
                                        if (!empty($categories)) {
                                            $category_links = array();
                                            foreach ($categories as $category) {
                                                $category_links[] = '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . esc_html($category->name) . '</a>';
                                            }
                                            echo 'หมวดหมู่: ' . implode(', ', $category_links);
                                        }
                                        ?>
                                    </div>
                                    <div class="post-thumbnail-readmore">
                                        <a style="float: right;" href="<?php the_permalink(); ?>">อ่านเพิ่มเติม...</a>
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
</div><!-- Other Recommend Product Section -->