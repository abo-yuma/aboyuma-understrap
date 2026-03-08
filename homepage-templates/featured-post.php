<!-- Featured Post Section -->
<section id="home-featured-post" class="mb-2">

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

    <h1 class="section-header">
        ⭐ บทความแนะนำ
    </h1>

    <?php if ($featured_posts->have_posts()) : ?>

        <div id="featured-post-carousel"
            class="carousel slide"
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

                            <div class="card h-100">

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

                                    </div>
                                </div>

                                <div class="card-body p-2">

                                    <a class="post-title-link d-block"
                                        href="<?php the_permalink(); ?>">

                                        <div class="post-title-text">
                                            ⭐ <?php echo mb_strimwidth(get_the_title(), 0, 50, '...'); ?>
                                        </div>

                                    </a>

                                    <div class="post-meta text-muted small">

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

                                            echo '📂 ' . implode(', ', $category_links);
                                        }
                                        ?>

                                        <span class="mx-2">|</span>

                                        🗓 <?php echo get_the_date(); ?>

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

    <?php else : ?>

        <div class="alert alert-secondary text-center">
            ⭐ ยังไม่มีบทความแนะนำ
        </div>

    <?php endif; ?>

</section>