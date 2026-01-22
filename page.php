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

		<div class="row py-3">
			<div class="sidebar-left-column-width order-2 order-md-1" id="sidebar-left">
				<?php
				get_template_part('sidebar-templates/sidebar', 'left');
				?>
			</div>
			<div class="main-content-column-width order-1 order-md-2">
				<?php
				while (have_posts()) {
					the_post();
					get_template_part('loop-templates/content', 'page');
				}
				?>
				<!-- Another front page templates-->
				<?php if (is_front_page()) : ?>
					<!-- Templates only for front page(Home)-->
					<?php get_template_part('homepage-templates/news'); ?>
					<?php get_template_part('homepage-templates/latest-post'); ?>
					<?php get_template_part('homepage-templates/latest-product'); ?>
					<?php get_template_part('homepage-templates/other-recommend-product'); ?>
					<!-- Widgets templates only for front page(Home)-->
					<?php if (is_active_sidebar('recommend-video-widget-area')) : ?>
						<div id="recommend-video-widget-area">
							<?php dynamic_sidebar('recommend-video-widget-area'); ?>
						</div>
					<?php endif; ?>
				<?php endif; ?>
			</div>

		</div><!-- .row -->

	</div><!-- #content -->

</main><!-- #main-wrapper -->

<?php
get_footer();
