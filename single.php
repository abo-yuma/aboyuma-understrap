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

<main>

	<div class="<?php echo esc_attr($container_classes); ?>" id="content" tabindex="-1">

		<div class="row">
			<div class="main-content-column px-3">
				<div class="main-content-wrapper mb-2">
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
					<?php
					$current_post_id = get_the_ID();
					$categories = wp_get_post_categories($current_post_id);

					$related_posts = new WP_Query([
						'post_type'           => 'post',
						'posts_per_page'      => 2,
						'post__not_in'        => [$current_post_id],
						'category__in'        => $categories,
						'orderby'             => 'date',
						'order'               => 'DESC',
						'ignore_sticky_posts' => true,
						'post_status'         => 'publish',
					]);

					if ($related_posts->have_posts()) : ?>

						<section class="related-posts mt-3 pt-4 border-top">

							<h2 class="fs-6 mb-4">
								📝 บทความที่เกี่ยวข้อง
							</h2>

							<?php while ($related_posts->have_posts()) : $related_posts->the_post(); ?>

								<article class="mb-3">

									<div class="row g-3 align-items-center">

										<div class="col-4">

											<a href="<?php the_permalink(); ?>" class="related-post-thumbnail">

												<?php
												if (has_post_thumbnail()) {
													the_post_thumbnail('medium', [
														'class' => 'img-fluid rounded',
													]);
												}
												?>

											</a>

										</div>

										<div class="col-8">

											<a
												href="<?php the_permalink(); ?>">

												<h3 class="h6 mb-1">
													<?php the_title(); ?>
												</h3>

											</a>

											<small class="text-body-secondary">
												<?php echo get_the_date(); ?>
											</small>

										</div>

									</div>

								</article>

							<?php endwhile; ?>

						</section>
					<?php
					endif;

					wp_reset_postdata();
					?>
					<div class="text-end mt-4">

						<a
							href="<?php echo esc_url(home_url('/')); ?>"
							class="btn btn-sm btn-outline-primary">

							← กลับหน้าหลัก

						</a>

					</div>
				</div>
			</div>
			<div class="sidebar-left-column border-start px-3" id="sidebar-left">
				<?php
				get_template_part('sidebar-templates/sidebar', 'right');
				?>
			</div>
		</div><!-- .row -->

	</div><!-- #content -->

</main><!-- #main-wrapper -->

<?php
get_footer();
