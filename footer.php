<footer class="text-white text-center text-lg-start footer-bg-background g-0">
	<!-- Grid container -->
	<div class="container p-4">
		<div class="row">
			<div class="col-md-2 mb-4 mb-md-0 text-center">
				<?php
				$image_url = get_theme_mod('footer_column_1_image');
				if ($image_url) {
					echo '<img src="' . esc_url($image_url) . '" alt="' . __('Footer Column 1 Image', 'aboyuma') . '" style="width: 80px; height: 80px;">';
				}
				?>
			</div>
			<div class="col-md-5 mb-4 mb-md-0">
				<div class="aboyuma-about-title" style="font-weight:bold; margin-bottom:0.3em;">เกี่ยวกับเรา :</div>
				<p><?php echo nl2br(esc_html(get_theme_mod('footer_column_2_content'))); ?></p>
			</div>
			<div class="col-md-3 mb-4 mb-md-0">
				<div id="visitor-counter">
					<?php
					if (function_exists('display_visitor_stats')) {
						display_visitor_stats();
					}
					?>
				</div>
			</div>
			<div class="col-md-2 mb-4 mb-md-0">
				<div id="social-contact">
					<ul style="display: flex; list-style: none; padding: 0; margin: 0;">
						<?php echo get_theme_mod('footer_column_3_content'); ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<!-- Grid container -->
	<!-- Copyright -->
	<div id="copyright-content" class="text-center p-3 copyright-content">
		© <?php echo date("Y"); ?> Copyright:
		<a class="text-white" href="https://aboyuma.com/">aboyuma.com</a>
	</div>
	<!-- Copyright -->
</footer>
</div><!-- /#content-wrapper -->
</div><!-- /#wrapper -->
<button class="scroll-to-top-btn">^</button>
<?php
wp_footer();
?>
</body>

</html>