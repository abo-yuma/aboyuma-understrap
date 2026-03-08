<?php

/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

get_header();

$container = get_theme_mod('understrap_container_type');
$container_classes = $container . ' px-4 py-0';
?>

<main>

    <div class="<?php echo esc_attr($container_classes); ?>" id="content" tabindex="-1">

        <div class="row">
            <div class="sidebar-left-column border-start order-2" id="sidebar-left">
                <?php
                get_template_part('sidebar-templates/sidebar', 'left');
                ?>
            </div>
            <div class="main-content-column order-1">
                <div class="main-content-wrapper">
                    <!-- Marquee Content -->
                    <?php
                    $marquee_text  = get_theme_mod('home_marquee_text');
                    $marquee_duration = get_theme_mod('home_marquee_duration', 15000);
                    $marquee_color = get_theme_mod('home_marquee_color', '#ff0000');

                    if (! empty($marquee_text)) :
                    ?>
                        <div class="home-marquee-wrapper mb-2">
                            <div class="home-marquee-content"
                                data-duration="<?php echo esc_attr($marquee_duration); ?>"
                                style="color: <?php echo esc_attr($marquee_color); ?>;">
                                <?php echo wp_kses_post($marquee_text); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php
                    while (have_posts()) {
                        the_post();
                        get_template_part('loop-templates/content', 'page');
                    }
                    ?>
                    <!-- Templates only for front page(Home)-->
                    <section id="home-cta" class="mb-4">

                        <div class="row g-3">

                            <!-- CTA 1 -->
                            <div class="col-6">
                                <div class="card h-100 text-center">

                                    <img
                                        src="https://images.unsplash.com/photo-1492724441997-5dc865305da7?q=80&w=600&auto=format&fit=crop"
                                        class="card-img-top object-fit-cover"
                                        style="height:140px;"
                                        alt="หารายได้เพิ่ม">

                                    <div class="card-body p-2">
                                        <div class="small fw-semibold">💼 อยากหาอะไรทำเพิ่ม</div>
                                        <a href="/about-aboyuma/" class="stretched-link"></a>
                                    </div>

                                </div>
                            </div>

                            <!-- CTA 2 -->
                            <div class="col-6">
                                <div class="card h-100 text-center">

                                    <img
                                        src="https://images.unsplash.com/photo-1505751172876-fa1923c5c528?q=80&w=600&auto=format&fit=crop"
                                        class="card-img-top object-fit-cover"
                                        style="height:140px;"
                                        alt="สินค้าสุขภาพ">

                                    <div class="card-body p-2">
                                        <div class="small fw-semibold">🧡 มองหาสินค้าดูแลสุขภาพ</div>
                                        <a href="/amway-product/" class="stretched-link"></a>
                                    </div>

                                </div>
                            </div>

                            <!-- CTA 3 -->
                            <div class="col-6">
                                <div class="card h-100 text-center">

                                    <img
                                        src="https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?q=80&w=600&auto=format&fit=crop"
                                        class="card-img-top object-fit-cover"
                                        style="height:140px;"
                                        alt="บทความความรู้">

                                    <div class="card-body p-2">
                                        <div class="small fw-semibold">📖 บทความความรู้</div>
                                        <a href="/blog/" class="stretched-link"></a>
                                    </div>

                                </div>
                            </div>

                            <!-- CTA 4 -->
                            <div class="col-6">
                                <div class="card h-100 text-center">

                                    <img
                                        src="https://images.unsplash.com/photo-1576091160550-2173dba999ef?q=80&w=600&auto=format&fit=crop"
                                        class="card-img-top object-fit-cover"
                                        style="height:140px;"
                                        alt="ประสบการณ์ภูมิแพ้">

                                    <div class="card-body p-2">
                                        <div class="small fw-semibold">🧴 ประสบการณ์ภูมิแพ้ผิวหนัง</div>
                                        <a href="/tag/skin-allergy/" class="stretched-link"></a>
                                    </div>

                                </div>
                            </div>

                        </div>

                    </section>
                    <?php get_template_part('homepage-templates/featured-post'); ?>
                    <?php get_template_part('homepage-templates/latest-post'); ?>
                    <?php get_template_part('homepage-templates/latest-product'); ?>
                    <?php get_template_part('homepage-templates/other-recommend-product'); ?>
                    <!-- Widgets templates only for front page(Home)-->
                    <?php if (is_active_sidebar('recommend-video-widget-area')) : ?>
                        <div id="recommend-video-widget-area">
                            <?php dynamic_sidebar('recommend-video-widget-area'); ?>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div><!-- .row -->

    </div><!-- #content -->

</main><!-- #main-wrapper -->

<?php
get_footer();
