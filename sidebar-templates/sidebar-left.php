<?php

/**
 * Sidebar Template.
 */
?>

<div id="sidebar-left-widget-wrapper" class="sidebar-left-widget-wrapper">

	<div class="authorbox">
		<?php
		$author_id = 1;

		$first_name = get_the_author_meta('first_name', $author_id);
		$last_name  = get_the_author_meta('last_name', $author_id);
		$bio        = get_the_author_meta('description', $author_id);

		$name = trim($first_name . ' ' . $last_name);
		if (empty($name)) {
			$name = get_the_author_meta('display_name', $author_id);
		}

		$facebook = 'https://www.facebook.com/profile.php?id=100093053005799';
		?>

		<div class="card mb-3 border-0">

			<div class="card-body p-2">

				<?php echo get_avatar($author_id, 400, '', '', [
					'class' => 'rounded-0 img-fluid w-100 mb-2'
				]); ?>

				<p class="card-title fw-semibold mb-2">
					🪪 <?php echo esc_html($name); ?>
				</p>

				<?php if (!empty($bio)) : ?>
					<p class="card-text small mb-2">
						<?php echo esc_html($bio); ?>
					</p>
				<?php endif; ?>

				<div class="d-flex justify-content-center gap-2">

					<a href="<?php echo esc_url(home_url('/about-me/')); ?>"
						class="btn btn-outline-secondary btn-sm">
						<i class="bi bi-person-fill"></i>
						เกี่ยวกับเรา
					</a>

					<a href="<?php echo esc_url($facebook); ?>"
						target="_blank"
						rel="noopener"
						class="btn btn-primary btn-sm">

						<i class="bi bi-facebook me-1"></i>
						ดูโปรไฟล์
					</a>

				</div>

			</div>

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