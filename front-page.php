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
$container_classes = $container . ' py-0';
?>

<div class="<?php echo esc_attr($container_classes); ?>" id="content" tabindex="-1">

    <div class="row">
        <div class="sidebar-column px-4" id="sidebar">
            <?php
            get_template_part('sidebar-templates/sidebar', 'right');
            ?>
        </div>
        <div class="main-content-column px-4">
            <div class="main-content-wrapper mb-2">
                <main class="site-main" id="main">
                    <!-- Marquee Content -->
                    <?php
                    $marquee_text  = get_theme_mod('home_marquee_text');
                    $marquee_duration = get_theme_mod('home_marquee_duration', 15000);
                    $marquee_color = get_theme_mod('home_marquee_color', '#ff0000');

                    if (! empty($marquee_text)) :
                        // Get today's date
                        $today_date = date_i18n('D, j F Y');
                    ?>
                        <div class="home-marquee-wrapper mb-4">
                            <div class="home-marquee-content"
                                data-duration="<?php echo esc_attr($marquee_duration); ?>">
                                <?php echo "🗓️ " . esc_html($today_date); ?> • <?php echo wp_kses_post($marquee_text); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php
                    while (have_posts()) {
                        the_post();

                        the_content();
                    }
                    ?>
                    <!-- Site News -->
                    <?php get_template_part('frontpage-templates/site-news'); ?>
                    <!-- Latest Posts -->
                    <?php get_template_part('frontpage-templates/latest-post');
                    ?>
                    <section class="support-and-promote mb-4">

                        <h2 class="h5 mb-3 pb-2 border-bottom">
                            📣 สนับสนุนและโปรโมท
                            <button type="button"
                                class="p-0 ms-1 border-0 bg-transparent"
                                style="font-size: 0.8rem;"
                                data-bs-toggle="modal"
                                data-bs-target="#supportPromoteModal"
                                aria-label="ข้อมูลเกี่ยวกับการโปรโมท">
                                <i class="fa fa-info-circle" aria-hidden="true"></i>
                            </button>
                        </h2>

                        <div class="d-flex justify-content-center align-items-center
                                border border-2
                                bg-body-secondary rounded mx-auto"
                            style="height:150px;">
                            <small class="text-muted">Support and promote placement</small>
                        </div>

                    </section>

                    <!-- Support & Promote Modal -->
                    <div class="modal fade"
                        id="supportPromoteModal"
                        tabindex="-1"
                        aria-labelledby="supportPromoteModalLabel"
                        aria-hidden="true">

                        <div class="modal-dialog modal-dialog-centered">

                            <div class="modal-content">

                                <div class="modal-header">
                                    <h5 class="modal-title" id="supportPromoteModalLabel">
                                        📣 สนับสนุนและโปรโมท
                                    </h5>

                                    <button type="button"
                                        class="btn-close"
                                        data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>

                                <div class="modal-body">

                                    <p>
                                        พื้นที่นี้จัดไว้สำหรับช่วยสนับสนุนและโปรโมทช่องทางของ ครอบครัว เพื่อน และคนรู้จัก
                                    </p>

                                    <p>
                                        หากมีช่องทางออนไลน์ที่ต้องการให้คนรู้จักมากขึ้น
                                        สามารถฝากโปรโมทที่นี่ได้เลยครับ
                                    </p>

                                </div>

                            </div>
                        </div>
                    </div>

                </main>
            </div>
        </div>

    </div><!-- .row -->

</div><!-- #content -->

<?php
get_footer();
