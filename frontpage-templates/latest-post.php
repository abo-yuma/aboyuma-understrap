<section id="home-latest-post" class="mb-3">

    <h1 class="section-header mb-3">
        📝 บทความล่าสุด
    </h1>

    <?php

    $latest_posts = new WP_Query([
        'posts_per_page' => 4,
        'post_status'    => 'publish',
    ]);

    ?>

    <?php if ($latest_posts->have_posts()) : ?>

        <div class="latest-post-feed row">

            <?php
            while ($latest_posts->have_posts()) :

                $latest_posts->the_post();

                get_template_part(
                    'frontpage-templates/latest-post-card'
                );

            endwhile;
            ?>

        </div>

    <?php else : ?>

        <div class="alert alert-secondary text-center">
            ยังไม่มีบทความ
        </div>

    <?php endif; ?>

    <?php wp_reset_postdata(); ?>

</section>