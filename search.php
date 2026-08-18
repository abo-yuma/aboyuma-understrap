<?php

/**
 * The template for displaying search results pages
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

					<?php if (have_posts()) : ?>

						<header class="page-header">

							<h1 class="page-title">
								<?php
								printf(
									/* translators: %s: query term */
									esc_html__('Search Results for: %s', 'understrap'),
									'<span>' . get_search_query() . '</span>'
								);
								?>
							</h1>


							<?php /* Start the Loop */ ?>
							<?php
							while (have_posts()) :
								the_post();

								/*
						 * Run the loop for the search to output the results.
						 * If you want to overload this in a child theme then include a file
						 * called content-search.php and that will be used instead.
						 */
								get_template_part('loop-templates/content', 'search');
							endwhile;
							?>

						<?php else : ?>

							<?php get_template_part('loop-templates/content', 'none'); ?>

						<?php endif; ?>



						<?php
						// Display the pagination component.
						understrap_pagination();

						// Do the right sidebar check and close div#primary.
						//get_template_part('global-templates/right-sidebar-check');
						?>
				</div>
			</div>
			<div class="sidebar-left-column border-start px-3" id="sidebar-left">
				<?php
				get_template_part('sidebar-templates/sidebar', 'right');
				?>
			</div>
		</div><!-- .row -->

	</div><!-- #content -->

</main><!-- #search-wrapper -->

<?php
get_footer();
