<?php

/**
 * The template for displaying archive pages
 *
 * Learn more: https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

get_header();

$container = get_theme_mod('understrap_container_type');
$container_classes = $container . ' px-4 py-0';
?>

<main class="bg-body text-body" data-bs-theme="">

	<div class="<?php echo esc_attr($container_classes); ?>" id="content" tabindex="-1">

		<div class="row">
			<div class="sidebar-left-column border-start order-2" id="sidebar-left">
				<?php
				get_template_part('sidebar-templates/sidebar', 'left');
				?>
			</div>
			<div class="main-content-column order-1">
				<div class="main-content-wrapper">

					<?php
					if (have_posts()) {
					?>
						<header class="page-header">
							<?php
							the_archive_title('<h1 class="page-title">', '</h1>');
							the_archive_description('<div class="taxonomy-description">', '</div>');
							?>
						</header><!-- .page-header -->
					<?php
						// Start the loop.
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

					<!-- Another front page templates-->

				</div>
			</div>
		</div><!-- .row -->

	</div><!-- #content -->

</main><!-- #main-wrapper -->

<?php
get_footer();
