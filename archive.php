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
$container_classes = $container . ' py-0';
?>

<div class="<?php echo esc_attr($container_classes); ?>" id="content" tabindex="-1">

	<div class="row">
		<div class="sidebar-column px-4 order-2 order-md-1" id="sidebar">
			<?php
			get_template_part('sidebar-templates/sidebar');
			?>
		</div>
		<div class="main-content-column px-4 order-1 order-md-2" id="main-content">
			<div class="main-content-wrapper mb-2">
				<main class="site-main" id="main">
					<?php
					if (have_posts()) {
					?>
						<header class="page-header mb-4">

							<nav class="breadcrumb" aria-label="breadcrumb">
								<?php
								get_breadcrumb();
								?>
							</nav>

							<?php
							the_archive_title('<h1 class="page-title">', '</h1>');
							the_archive_description('<div class="taxonomy-description small">', '</div>');
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
						?>
						<div class="my-2">
							<?php understrap_pagination(); ?>
						</div>
					<?php
					} else {
						get_template_part('loop-templates/content', 'none');
					}
					?>

					<!-- Another front page templates-->
				</main>
			</div>
		</div>

	</div><!-- .row -->

</div><!-- #content -->

<?php
get_footer();
