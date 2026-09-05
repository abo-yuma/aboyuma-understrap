<?php

/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

get_header();

$container = get_theme_mod('understrap_container_type');
$container_classes = $container . ' py-0';
?>

<main>

	<div class="<?php echo esc_attr($container_classes); ?>" id="content" tabindex="-1">

		<div class="row">
			<div class="main-content-column px-3">
				<div class="main-content-wrapper mb-2">

					<?php
					if (have_posts()) {
						// Start the Loop.
						while (have_posts()) {
							the_post();

							/*
						 * Include the Post-Format-specific template for the content.
						 * If you want to override this in a child theme, then include a file
						 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
						 */
							get_template_part('loop-templates/content', get_post_format());
						}
					} else {
						get_template_part('loop-templates/content', 'none');
					}
					?>



					<?php
					// Display the pagination component.
					understrap_pagination();


					?>

					<!-- Another front page templates-->
				</div>
			</div>
			<div class="sidebar-right-column border-start px-3" id="sidebar-right">
				<?php
				get_template_part('sidebar-templates/sidebar', 'right');
				?>
			</div>
		</div><!-- .row -->

	</div><!-- #content -->

</main><!-- #main-wrapper -->

<?php
get_footer();
