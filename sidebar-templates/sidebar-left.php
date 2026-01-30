<?php

/**
 * Sidebar Template.
 */
?>

<div id="sidebar-left-widget-wrapper">

	<?php if (function_exists('aboyuma_author_box_render')) : ?>
		<div class="author-box-wrapper">
			<?php aboyuma_author_box_render(); ?>
		</div>
	<?php endif; ?>

	<?php
	if (is_active_sidebar('left-sidebar') || is_archive() || is_single()):
	?>
		<div class="sticky-widget-wrapper">
			<div id="widget-area" class="widget-area" role="complementary">
				<?php
				dynamic_sidebar('left-sidebar');

				if (current_user_can('manage_options')):
				?>
					<a href="<?php echo esc_url(admin_url('widgets.php')); ?>">
						<?php esc_html_e('Edit widget'); ?></a>
				<?php
				endif;
				?>
			</div><!-- /.widget-area -->
		</div>
	<?php
	endif;
	?>

</div><!-- /#widget-wrapper -->