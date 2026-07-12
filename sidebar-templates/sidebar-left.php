<?php

/**
 * Sidebar Template.
 */
?>

<div id="sidebar-left-widget-wrapper" class="sidebar-left-widget-wrapper mb-2">

	<div class="authorbox mb-3">

		<div class="author-box border p-3 bg-body-secondary">

			<div class="row g-4 align-items-start">

				<!-- Avatar -->
<div class="col-md-auto text-center">

    <?php
	 $author_id = 1;
    echo get_avatar(
        $author_id,
        200,
        '',
        get_the_author_meta('display_name', $author_id),
        array(
            'class' => 'img-fluid border',
        )
    );
    ?>

</div>

				<!-- Content -->
				<div class="col m-0">

					<div class="d-flex flex-wrap align-items-center gap-2">

						<h3 class="h6 mb-0">
							Paranyu Chantarakka
						</h3>


					</div>

					<div class="d-flex flex-wrap gap-2 mb-2">

						<span class="badge text-bg-info border">
							Indie Developer
						</span>

						<span class="badge text-bg-info border">
							ABO ID: 6672883
						</span>

					</div>

						<div class="small">

						<div>
							📝 <strong>58</strong> บทความ
						</div>

						<div>
							📂 <strong>8</strong> หมวดหมู่
						</div>

					</div>



				
					<hr>


					<div class="d-flex flex-wrap gap-2">

						<a href="#" >
							<i class="bi bi-github"></i>
						</a>

						<a href="#" >
							<i class="bi bi-facebook"></i>
						</a>

						<a href="#" >
							<i class="bi bi-instagram"></i>
						</a>

						<a href="#" >
							<i class="bi bi-youtube"></i>
						</a>

					</div>

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