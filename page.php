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
		
		<div class="main-content-column px-4" id="main-content">
			<div class="main-content-wrapper mb-2">
				<main class="site-main" id="main">
					<?php
					while (have_posts()) {
						the_post();
						get_template_part('loop-templates/content', 'page');
					}
					?>
				</main>
			</div>
		</div>
		<div class="sidebar-column px-4" id="sidebar">
			<?php
			get_template_part('sidebar-templates/sidebar');
			?>
		</div>
	</div><!-- .row -->

</div><!-- #content -->

<?php
get_footer();
