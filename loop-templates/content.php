<?php

/**
 * Post rendering content according to caller of get_template_part
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;
?>

<article <?php post_class('archive-loop-item mt-2'); ?> id="post-<?php the_ID(); ?>">

	<header class="entry-header">

		<?php
		the_title(
			sprintf('<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url(get_permalink())),
			'</a></h2>'
		);
		?>

		<?php if ('post' === get_post_type()) : ?>

			<div class="entry-meta">
				<?php
				understrap_posted_on();
				// $view_output = '<span class="post-views"> | ' .
				// 	sprintf(_n('%d view', '%d views', $views, 'understrap'), $views) . '</span>';
				?>
			</div><!-- .entry-meta -->

		<?php endif; ?>

	</header><!-- .entry-header -->

	<div class="post-thumbnail mt-2">
		<a href="<?php the_permalink(); ?>">
			<?php echo get_the_post_thumbnail($post->ID, 'large', [
				'class' => 'img-fluid archive-post-thumbnail'
			]); ?>
		</a>
	</div>

	<div class="entry-content mt-2">

		<?php
		the_excerpt();
		understrap_link_pages();
		?>

	</div><!-- .entry-content -->

	<footer class="entry-footer">

		<?php understrap_entry_footer(); ?>

	</footer><!-- .entry-footer -->

</article><!-- #post-<?php the_ID(); ?> -->

<style>
	.archive-post-thumbnail {
		width: 100%;
		height: 350px;
		object-fit: cover;
		display: block;
		border-radius: 0.5rem;
	}
</style>