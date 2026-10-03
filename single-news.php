<?php

/**
 * The template for displaying single News posts
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
            get_template_part('sidebar-templates/sidebar');
            ?>
        </div>

        <div class="main-content-column px-4">

            <div class="main-content-wrapper mb-2">

                <main class="site-main" id="main">

                    <?php
                    while (have_posts()) {
                        the_post();

                        // Track post views.
                        if (function_exists('setPostViews')) {
                            setPostViews(get_the_ID());
                        }

                        get_template_part('loop-templates/content', 'news');
                    }
                    ?>

                    <?php wp_reset_postdata(); ?>

                    <div class="text-end mt-4">

                        <a
                            href="<?php echo esc_url(get_post_type_archive_link('news')); ?>"
                            class="btn btn-sm btn-outline-primary">

                            ← ดูข่าวสารอื่น

                        </a>

                    </div>

                </main>

            </div>

        </div>

    </div><!-- .row -->

</div><!-- #content -->

<?php
get_footer();
