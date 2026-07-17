<!-- Featured Post Section -->
<section id="home-featured-post" class="mb-3">

    <?php
    $featured_category = get_category_by_slug('featured');
    $featured_id = $featured_category ? $featured_category->term_id : 0;

    $args = array(
        'posts_per_page' => 4,
        'post_status'    => 'publish',
        'cat'            => $featured_id,
    );

    $featured_posts = new WP_Query($args);
    ?>

    <h2 class="section-header mb-3">
        📌 บทความแนะนำ
    </h2>

    <?php if ($featured_posts->have_posts()) : ?>

        <div class="featured-post-feed carousel slide" id="featured-post-carousel"
            data-bs-ride="carousel"
            data-bs-interval="4000">

            <div class="carousel-inner">

                <?php
                $i = 0;
                while ($featured_posts->have_posts()) : $featured_posts->the_post();
                    $active = ($i === 0) ? 'active' : '';
                ?>

                    <div class="carousel-item <?php echo $active; ?>">

                        <article id="post-<?php the_ID(); ?>" <?php post_class('col-12'); ?>>

                            <div class="card border-0 h-100">

                                <div class="card-img-top featured-image">
                                    <div class="post-thumbnail">

                                        <?php if (has_post_thumbnail()) : ?>
                                            <a href="<?php the_permalink(); ?>">
                                                <?php the_post_thumbnail('large', ['class' => 'img-fluid w-100']); ?>
                                            </a>
                                        <?php else : ?>
                                            <img src="https://placehold.co/800x400?text=No+Image"
                                                alt="No image available"
                                                class="img-fluid w-100">
                                        <?php endif; ?>

                                        <button class="carousel-control-prev"
                                            type="button"
                                            data-bs-target="#featured-post-carousel"
                                            data-bs-slide="prev">

                                            <span class="carousel-control-prev-icon"></span>

                                        </button>

                                        <button class="carousel-control-next"
                                            type="button"
                                            data-bs-target="#featured-post-carousel"
                                            data-bs-slide="next">

                                            <span class="carousel-control-next-icon"></span>

                                        </button>

                                    </div>
                                </div>

                                <div class="card-body p-0 pt-2">
                                    <h3 class="entry-title">
                                        <a class="d-block"
                                            href="<?php the_permalink(); ?>">
                                            <?php echo get_the_title(); ?>
                                        </a>
                                    </h3>

                                    <div class="entry-meta text-muted d-flex flex-wrap gap-2">
                                        <span>
                                            <?php
                                            $categories = get_the_category();

                                            if (!empty($categories)) {

                                                $category_links = array();

                                                foreach ($categories as $category) {

                                                    if ($category->slug === 'featured') {
                                                        continue;
                                                    }

                                                    $category_links[] =
                                                        '<a href="' . esc_url(get_category_link($category->term_id)) . '">' .
                                                        esc_html($category->name) .
                                                        '</a>';
                                                }

                                                if (!empty($category_links)) {
                                                    echo '🗂️ ' . implode(', ', $category_links);
                                                }
                                            }
                                            ?>
                                        </span>
                                        <span>
                                            <?php echo '🗓 ' . get_the_date('M j, Y'); ?>
                                        </span>
                                        <span>
                                            <?php
                                            if (function_exists('aboyuma_get_post_views')) {
                                                echo aboyuma_get_post_views(get_the_ID());
                                            }
                                            ?>
                                        </span>
                                    </div>
                                    <div class="small mt-auto pt-3 d-flex justify-content-end">
                                        <?php echo '<a class="btn btn-sm btn-outline-primary" href="' . get_permalink() . '"> อ่านบทความ →</a>'; ?>
                                    </div>

                                </div>

                            </div>

                        </article>

                    </div>

                <?php
                    $i++;
                endwhile;
                wp_reset_postdata();
                ?>

            </div>

        </div>

    <?php else : ?>

        <div class="alert alert-secondary text-center">
            ยังไม่มีบทความแนะนำ
        </div>

    <?php endif; ?>

</section>