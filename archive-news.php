<?php

/**
 * The template for displaying News archive pages
 *
 * @package Understrap
 */

defined('ABSPATH') || exit;

get_header();

$container = get_theme_mod('understrap_container_type');
$container_classes = $container . ' py-0';
?>

<div class="<?php echo esc_attr($container_classes); ?>" id="content" tabindex="-1">

    <div class="row">

        <div class="main-content-column px-4" id="main-content">

            <div class="main-content-wrapper mb-2">

                <main class="site-main" id="main">

                    <?php if (have_posts()) : ?>

                        <header class="page-header mb-4">

                            <nav class="breadcrumb" aria-label="breadcrumb">
                                <?php get_breadcrumb(); ?>
                            </nav>

                            <h1 class="page-title">
                                <?php post_type_archive_title(); ?>
                            </h1>

                        </header>

                        <div class="table-responsive">

                            <table class="table table-striped table-hover mb-0">

                                <tbody>

                                    <?php while (have_posts()) : the_post(); ?>

                                        <tr>

                                            <td class="text-muted small text-nowrap">
                                                🗓️ <?php echo esc_html(get_the_date()); ?>:
                                            </td>

                                            <td>
                                                <a
                                                    href="<?php the_permalink(); ?>"
                                                    class="text-decoration-none">
                                                    <?php the_title(); ?>
                                                </a>
                                            </td>

                                        </tr>

                                    <?php endwhile; ?>

                                </tbody>

                            </table>

                        </div>

                        <div class="my-2">
                            <?php understrap_pagination(); ?>
                        </div>

                    <?php else : ?>

                        <?php get_template_part('loop-templates/content', 'none'); ?>

                    <?php endif; ?>

                </main>

            </div>

        </div>
        <div class="sidebar-column px-4" id="sidebar">
            <?php
            get_template_part('sidebar-templates/sidebar');
            ?>
        </div>
    </div>

</div>

<?php
get_footer();
