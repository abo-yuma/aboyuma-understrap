<?php

/**
 * Partial template for content in page.php
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;
?>

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">

	<?php
	if (!is_page_template('page-templates/no-title.php') && !is_front_page()) :
	?>

		<header class="entry-header mb-4">
			<nav class="breadcrumb" aria-label="breadcrumb">
				<?php
				get_breadcrumb();
				?>
			</nav>
			<h1 class="entry-title">
				<?php the_title(); ?>
			</h1>
		</header>

	<?php endif; ?>

	<?php
	echo get_the_post_thumbnail(get_the_ID(), 'large');
	?>

	<div class="entry-content">

		<?php
		the_content();
		understrap_link_pages();
		?>

	</div><!-- .entry-content -->

	<footer class="entry-footer">

		<?php understrap_edit_post_link(); ?>

	</footer><!-- .entry-footer -->

</article><!-- #post-<?php the_ID(); ?> -->