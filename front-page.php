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
                <div class="main-content-wrapper mb-2">
                    <!-- Marquee Content -->
                    <?php
                    $marquee_text  = get_theme_mod('home_marquee_text');
                    $marquee_duration = get_theme_mod('home_marquee_duration', 15000);
                    $marquee_color = get_theme_mod('home_marquee_color', '#ff0000');

                    if (! empty($marquee_text)) :
                        // Get today's date
                        $today_date = date_i18n('D, j F Y');
                    ?>
                        <div class="home-marquee-wrapper mb-2">
                            <div class="home-marquee-content"
                                data-duration="<?php echo esc_attr($marquee_duration); ?>"
                                style="color: <?php echo esc_attr($marquee_color); ?>;">
                                <?php echo "🗓 " . esc_html($today_date); ?> • <?php echo wp_kses_post($marquee_text); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php
                    while (have_posts()) {
                        the_post();

                        $content = get_the_content();

                        if (! empty(trim(wp_strip_all_tags($content)))) :
                    ?>

                            <?php the_content(); ?>

                    <?php
                        endif;
                    }
                    ?>
                    <?php get_template_part('frontpage-templates/featured-post'); ?>
                    <?php get_template_part('frontpage-templates/latest-post'); ?>
                </div>
            </div>
        </div><!-- .row -->

    </div><!-- #content -->

</main><!-- #main-wrapper -->

<?php
get_footer();
