<section id="home-cta" class="mb-3">

    <h1 class="section-header mb-3">
        ⭐ รายการแนะนำ
    </h1>

    <?php
    $has_items = false;

    for ($i = 1; $i <= 4; $i++) {
        $image = get_theme_mod("home_cta_image_$i");
        $title = get_theme_mod("home_cta_title_$i");
        $link  = get_theme_mod("home_cta_link_$i");

        if ($image && $title && $link) {
            $has_items = true;
            break;
        }
    }
    ?>

    <?php if ($has_items) : ?>

        <div class="row g-3">

            <?php for ($i = 1; $i <= 4; $i++) :

                $image = get_theme_mod("home_cta_image_$i");
                $title = get_theme_mod("home_cta_title_$i");
                $link  = get_theme_mod("home_cta_link_$i");

                if (!$image || !$title || !$link) continue;
            ?>

                <div class="col-6">

                    <div class="card h-100 text-center">

                        <img
                            src="<?php echo esc_url($image); ?>"
                            class="card-img-top object-fit-cover"
                            style="height:140px;"
                            alt="<?php echo esc_attr($title); ?>">

                        <div class="card-body p-2">

                            <div class="small fw-semibold">
                                <?php echo esc_html($title); ?>
                            </div>

                            <a href="<?php echo esc_url($link); ?>" class="stretched-link"></a>

                        </div>

                    </div>

                </div>

            <?php endfor; ?>

        </div>

    <?php else : ?>

        <div class="alert alert-secondary text-center">
            ยังไม่มีรายการแนะนำ
        </div>

    <?php endif; ?>

</section>