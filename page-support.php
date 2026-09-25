<?php

/**
 * The template for displaying contact page
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
		
		<div class="col-12 px-3">
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

	</div><!-- .row -->

</div><!-- #content -->

<?php
get_footer();
