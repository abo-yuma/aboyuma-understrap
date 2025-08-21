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
?>

<main>

	<div class="<?php echo esc_attr($container); ?>" id="content" tabindex="-1">

		<div class="row py-3">
			<div class="sidebar-left-column-width" id="sidebar-left">
				<?php
				get_template_part('sidebar-templates/sidebar', 'left');
				?>
			</div>
			<div class="main-content-column-width">
				<?php
				while (have_posts()) {
					the_post();
					get_template_part('loop-templates/content', 'single');

					// If comments are open or we have at least one comment, load up the comment template.
					if (comments_open() || get_comments_number()) {
						comments_template();
					}
				}
				?>

				<!-- Another front page templates-->

			</div>

		</div><!-- .row -->

	</div><!-- #content -->

</main><!-- #main-wrapper -->

<?php
get_footer();
