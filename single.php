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

					<?php //get_template_part('post-templates/related-post'); 
					?>

					<?php wp_reset_postdata(); ?>
					<div class="text-end mt-4">

						<?php
						$categories = get_the_category();

						if (! empty($categories)) :
							$category = $categories[0];
						?>
							<a
								href="<?php echo esc_url(get_category_link($category->term_id)); ?>"
								class="btn btn-sm btn-outline-primary">

								← ดูบทความอื่น

							</a>
						<?php endif; ?>

					</div>
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
