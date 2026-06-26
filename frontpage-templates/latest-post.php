<section id="home-latest-post" class="mb-3">

    <h1 class="section-header mb-3">
        📝 บทความล่าสุด
    </h1>

    <?php

    $tabs = [
        'skin-allergy' => 'ภูมิแพ้ผิวหนัง',
        'knee-health'  => 'สุขภาพข้อเข่า',
        'development-tips' => 'Dev Tips',
        'recommend-products' => 'สินค้า Shopee',
    ];

    ?>

    <ul class="nav nav-tabs mb-3" id="latestPostTabs" role="tablist">

        <?php
        $first = true;

        foreach ($tabs as $slug => $label) :
        ?>

            <li class="nav-item" role="presentation">

                <button
                    class="nav-link <?php echo $first ? 'active' : ''; ?>"
                    data-bs-toggle="tab"
                    data-bs-target="#tab-<?php echo esc_attr($slug); ?>"
                    type="button">

                    <?php echo esc_html($label); ?>

                </button>

            </li>

        <?php

            $first = false;

        endforeach;
        ?>

    </ul>

    <div class="latest-post-section tab-content">

        <?php

        $first = true;

        foreach ($tabs as $slug => $label) :

            $args = [
                'posts_per_page' => 4,
                'post_status'    => 'publish',
            ];

            if ($slug !== 'all') {
                $args['category_name'] = $slug;
            }

            $query = new WP_Query($args);

        ?>

            <div
                class="tab-pane fade <?php echo $first ? 'show active' : ''; ?>"
                id="tab-<?php echo esc_attr($slug); ?>">

                <?php if ($query->have_posts()) : ?>

                    <div class="latest-post-feed row">

                        <?php
                        while ($query->have_posts()) :

                            $query->the_post();

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

            </div>

        <?php

            wp_reset_postdata();

            $first = false;

        endforeach;
        ?>

    </div>

</section>