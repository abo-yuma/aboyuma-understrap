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

?>

<main class="bg-body text-body" data-bs-theme="">

	<div class="<?php echo esc_attr($container); ?>" id="content" tabindex="-1">

		<div class="row">
			<div class="sidebar-left-column-width" id="sidebar-left">
				<?php
				get_template_part('sidebar-templates/sidebar', 'left');
				?>
			</div>
			<div class="main-content-column-width">
				<?php
				while (have_posts()) {
					the_post();
					get_template_part('loop-templates/content', 'page');

					// If comments are open or we have at least one comment, load up the comment template.
					if (comments_open() || get_comments_number()) {
						comments_template();
					}
				}
				?>

				<!-- Another front page templates-->
				<?php if (is_front_page()) : ?>
					<!-- Templates only for front page(Home)-->
					<?php get_template_part('homepage-templates/news'); ?>
					<?php get_template_part('homepage-templates/recommend-product'); ?>
					<?php get_template_part('homepage-templates/latest-post'); ?>
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
