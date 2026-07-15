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

		<div class="card bg-body-secondary">

			<div class="card-body">

				<!-- Avatar -->
				<div class="text-center mb-3">

					<?php
					echo get_avatar(
						$author_id,
						180,
						'',
						$display_name,
						array(
							'class' => 'img-fluid border rounded-3',
						)
					);
					?>

				</div>

				<!-- Name -->
				<h3 class="mb-1 text-uppercase fs-6">
					🪪 <?php echo esc_html($display_name); ?>
				</h3>

				<span class="badge text-bg-primary mb-3">
					Author
				</span>
				

				<!-- Bio -->
				<?php if (! empty($bio)) : ?>

					<p class="card-text text-body-secondary">
						<?php echo esc_html(wp_trim_words($bio, 18, '...')); ?>
					</p>

				<?php endif; ?>

			</div>

			<?php if (! empty($about_me_page_url) || ! empty($support_me_page_url)) : ?>

				<div class="card-footer bg-transparent d-flex justify-content-between small">

					<?php if (! empty($about_me_page_url)) : ?>
						<a
							href="<?php echo esc_url($about_me_page_url); ?>"
							class="text-decoration-none">
							🙋 About Me
						</a>
					<?php endif; ?>

					<?php if (! empty($support_me_page_url)) : ?>
						<a
							href="<?php echo esc_url($support_me_page_url); ?>"
							class="text-decoration-none">
							❤️ Support Me
						</a>
					<?php endif; ?>

				</div>

			<?php endif; ?>

		</div>

	</div>

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