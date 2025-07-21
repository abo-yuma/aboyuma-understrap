<?php

/**
 * Sidebar Template.
 */
?>
<div id="sidebar-left" class="sidebar-left-column-width order-md-1 order-2 col-sm-12">
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
						<span class="edit-link"><a href="<?php echo esc_url(admin_url('widgets.php')); ?>"
								class="badge bg-secondary"><?php esc_html_e('Edit', 'aboyuma'); ?></a></span><!-- Show Edit Widget link -->
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
</div><!-- /#sidebar -->