<section id="home-cta" class="mb-4">

    <div class="row g-3">

        <?php for ($i = 1; $i <= 4; $i++) :

            $image = get_theme_mod("home_cta_image_$i");
            $title = get_theme_mod("home_cta_title_$i");
            $link  = get_theme_mod("home_cta_link_$i");

            if (!$title && !$image) continue;
        ?>

            <div class="col-6">

                <div class="card h-100 text-center">

                    <?php if ($image) : ?>

                        <img
                            src="<?php echo esc_url($image); ?>"
                            class="card-img-top object-fit-cover"
                            style="height:140px;"
                            alt="<?php echo esc_attr($title); ?>">

                    <?php endif; ?>

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

</section>