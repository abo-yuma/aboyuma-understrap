<?php

/**
 * Sidebar Template.
 */
?>

<div id="sidebar-left-widget-wrapper">
	<?php
	if (is_active_sidebar('left-sidebar') || is_archive() || is_single()):
	?>
		<?php
		if (is_active_sidebar('left-sidebar')):
		?>
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
		<?php
		endif;
		?>
	<?php
	endif;
	?>
</div><!-- /#widget-wrapper -->