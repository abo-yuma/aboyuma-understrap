<?php

/**
 * Sidebar Template.
 */
?>

<div id="sidebar-left-widget-wrapper" class="sidebar-left-widget-wrapper mb-2">

	<div class="author-box mb-3">

		<?php
		$author_id = 1;

		$first_name = get_the_author_meta('first_name', $author_id);
		$last_name  = get_the_author_meta('last_name', $author_id);

		$display_name = trim($first_name . ' ' . $last_name);
		$bio = get_the_author_meta('description', $author_id);

		$about_me_page_url = get_user_meta($author_id, 'about_me_page_url', true);
		$support_me_page_url = get_user_meta($author_id, 'support_me_page_url', true);
		?>

		<div class="card border-0">
			<!-- Avatar -->
			<?php
			echo get_avatar(
				$author_id,
				600,
				'',
				$display_name,
				array(
					'class' => 'card-img-top',
				)
			);
			?>

			<div class="card-body p-0 pt-3">

				<!-- Name -->
				<h3 class="mb-1 text-uppercase fs-6">
					<?php echo esc_html($display_name); ?>
				</h3>

				<!-- Bio -->
				<?php if (! empty($bio)) : ?>

					<p class="card-text text-body-secondary">
						<?php echo esc_html(wp_trim_words($bio, 18, '...')); ?>
					</p>

				<?php endif; ?>

			</div>

			<?php if (! empty($about_me_page_url) || ! empty($support_me_page_url)) : ?>

				<div class="card-footer profile-actions bg-transparent d-flex">

					<?php if (! empty($about_me_page_url)) : ?>
						<a
							href="<?php echo esc_url($about_me_page_url); ?>"
							class="btn btn-light">
							🙋 เกี่ยวกับเรา
						</a>
					<?php endif; ?>

					<?php if (! empty($support_me_page_url)) : ?>
						<a
							href="<?php echo esc_url($support_me_page_url); ?>"
							class="btn btn-light">
							❤️ สนับสนุนเรา
						</a>
					<?php endif; ?>

				</div>

			<?php endif; ?>

		</div>

	</div>

	<?php
	if (is_active_sidebar('right-sidebar') || is_archive() || is_single()):
	?>
		<div class="sticky-widget-wrapper">

			<div id="widget-area" class="widget-area" role="complementary">
				<?php
				dynamic_sidebar('right-sidebar');

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