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

		<a href="<?php the_permalink(); ?>" class="text-decoration-none">

			<?php the_title(
				'<h2 class="entry-title">',
				'</h2>'
			); ?>

		</a>

		<?php if ('post' === get_post_type()) : ?>

			<div class="entry-meta d-flex flex-wrap gap-2">
				<span>
					<?php understrap_posted_on(); ?>
				</span>
			</div>

		<?php endif; ?>

	</header>

	<div class="post-thumbnail mt-2">
		<a href="<?php the_permalink(); ?>">
			<?php echo get_the_post_thumbnail(
				$post->ID,
				'large',
				[
					'class' => 'img-fluid archive-post-thumbnail rounded-3'
				]
			); ?>
		</a>
	</div>

	<div class="entry-content mt-2">

		<?php
		the_excerpt();
		understrap_link_pages();
		?>

	</div>

	<footer class="entry-footer">

		<?php understrap_entry_footer(); ?>

	</footer>

</article><!-- #post-<?php the_ID(); ?> -->