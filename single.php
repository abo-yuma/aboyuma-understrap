<?php

/**
 * The template for displaying all single posts
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
			<div class="sidebar-left-column order-2" id="sidebar-left">
				<?php
				get_template_part('sidebar-templates/sidebar', 'left');
				?>
			</div>
			<div class="main-content-column order-1">
				<div class="main-content-wrapper">
					<?php
					while (have_posts()) {
						the_post();

						// Track post views
						if (function_exists('setPostViews')) {
							setPostViews(get_the_ID());
						}

						get_template_part('loop-templates/content', 'single');

						// If comments are open or we have at least one comment, load up the comment template.
						// if (comments_open() || get_comments_number()) {
						// 	comments_template();
						// }
					}
					?>

					<!-- Another front page templates-->
				</div>
			</div>
		</div><!-- .row -->

	</div><!-- #content -->

</main><!-- #main-wrapper -->

<?php
get_footer();
