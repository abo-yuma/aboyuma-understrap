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
$container_classes = $container . ' px-3 px-md-4 py-0';
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
