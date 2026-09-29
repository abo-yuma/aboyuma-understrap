<?php

/**
 * Sidebar Template.
 */
?>

<div id="sidebar-left-widget-wrapper" class="sidebar-left-widget-wrapper mb-2">

	<?php
	if (is_active_sidebar('primary-sidebar') || is_archive() || is_single()):
	?>
		<div class="sticky-widget-wrapper">

			<div id="widget-area" class="widget-area" role="complementary">
				<?php
				dynamic_sidebar('primary-sidebar');

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