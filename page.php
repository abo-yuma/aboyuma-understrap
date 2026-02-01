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
			<div class="sidebar-left-column-width  order-2" id="sidebar-left">
				<?php
				get_template_part('sidebar-templates/sidebar', 'left');
				?>
			</div>
			<div class="main-content-column-width order-1">
				<?php
				while (have_posts()) {
					the_post();
					get_template_part('loop-templates/content', 'page');
				}
				?>
			</div>

		</div><!-- .row -->

	</div><!-- #content -->

</main><!-- #main-wrapper -->

<?php
get_footer();
