<?php if (! in_category('news')) : ?>
    <section class="author-box mt-4 mb-4">

        <?php
        $author_id = 1;

        $first_name = get_the_author_meta('first_name', $author_id);
        $last_name  = get_the_author_meta('last_name', $author_id);

        $display_name = trim($first_name . ' ' . $last_name);
        $bio = get_the_author_meta('description', $author_id);

        $about_me_page_url   = get_user_meta($author_id, 'about_me_page_url', true);
        $support_me_page_url = get_user_meta($author_id, 'support_me_page_url', true);
        ?>

        <div class="card border-0 bg-secondary-subtle">

            <div class="card-body p-3">

                <div class="row g-3 align-items-start">

                    <!-- Avatar -->
                    <div class="col-4 col-md-3">
                        <?php
                        echo get_avatar(
                            $author_id,
                            600,
                            '',
                            $display_name,
                            array(
                                'class' => 'img-fluid w-100',
                            )
                        );
                        ?>
                    </div>

                    <!-- Author Info -->
                    <div class="col-8 col-md-9">

                        <!-- Name -->
                        <h3 class="mb-3 text-uppercase fs-6">
                            <?php echo esc_html($display_name); ?>
                        </h3>

                        <!-- Bio -->
                        <?php if (! empty($bio)) : ?>

                            <p class="card-text text-muted small fst-italic mb-3">
                                <?php echo esc_html(wp_trim_words($bio, 30, '...')); ?>
                            </p>

                        <?php endif; ?>

                        <!-- Buttons -->
                        <?php if (! empty($about_me_page_url) || ! empty($support_me_page_url)) : ?>

                            <div class="d-flex gap-2 flex-wrap">

                                <?php if (! empty($about_me_page_url)) : ?>

                                    <a
                                        href="<?php echo esc_url($about_me_page_url); ?>"
                                        class="btn btn-outline-primary btn-sm">
                                        เกี่ยวกับเรา
                                    </a>

                                <?php endif; ?>

                                <?php if (! empty($support_me_page_url)) : ?>

                                    <a
                                        href="<?php echo esc_url($support_me_page_url); ?>"
                                        class="btn btn-outline-primary btn-sm">
                                        สนับสนุนเรา
                                    </a>

                                <?php endif; ?>

                            </div>

                        <?php endif; ?>

                    </div>

                </div>

            </div>

        </div>

    </section>
<?php endif; ?>