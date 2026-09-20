<article id="post-<?php the_ID(); ?>"
    <?php post_class('col-md-6 mb-3 align-self-stretch'); ?>>

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

            <div class="entry-title mb-1">

                <a href="<?php the_permalink(); ?>">

                    <?php echo mb_strimwidth(
                        get_the_title(),
                        0,
                        50,
                        '...'
                    ); ?>

                </a>

            </div>

            <div class="entry-meta text-muted d-flex flex-wrap gap-2">

                <span>

                    <?php
                    $categories = get_the_category();

                    if (!empty($categories)) {

                        $category_links = [];

                        foreach ($categories as $category) {

                            $category_links[] =
                                '<a href="' .
                                esc_url(
                                    get_category_link(
                                        $category->term_id
                                    )
                                ) .
                                '">' .
                                esc_html($category->name) .
                                '</a>';
                        }

                        echo '🗂️ ' .
                            implode(', ', $category_links);
                    }
                    ?>

                </span>

                <span>
                    🗓 <?php echo get_the_date('M j, Y'); ?>
                </span>

            </div>

        </div>

    </div>

</article>