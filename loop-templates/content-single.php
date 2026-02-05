<?php

/**
 * Single post partial template
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;
?>

<article <?php post_class('content-single'); ?> id="post-<?php the_ID(); ?>">

	<header class="entry-header">

		<?php the_title('<h1 class="entry-title">', '</h1>'); ?>

		<div class="entry-meta">

			<?php
			understrap_posted_on();
			// --- Add post views ---
			$views = getPostViews($post->ID);
			// $view_output = '<span class="post-views"> | ' .
			// 	sprintf(_n('%d view', '%d views', $views, 'understrap'), $views) . '</span>';
			// 	echo $view_output;
			echo '<span class="post-views"> | <i class="fa fa-eye" aria-hidden="true"></i> ' . $views . '</span>';
			?>

		</div><!-- .entry-meta -->

	</header><!-- .entry-header -->

	<div class="entry-content mt-2">

		<?php
		the_content();
		understrap_link_pages();
		?>

	</div><!-- .entry-content -->

	<footer class="entry-footer">

		<?php understrap_entry_footer(); ?>
		<?php aboyuma_facebook_share_button(); ?>

	</footer><!-- .entry-footer -->

</article><!-- #post-<?php the_ID(); ?> -->