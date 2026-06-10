<!-- Ad  -->
<div class="px-3 py-4 border-top">
	<div class="d-flex justify-content-center align-items-center 
                border border-2 
                bg-body-secondary rounded mx-auto"
		style="max-width:970px;height:90px;">
		<small class="text-muted">Advertisement placement</small>
	</div>
</div>

<footer class="text-white text-center text-lg-start footer-bg-background g-0">
	<div class="px-3 py-3">
		<div class="row">
			<div class="col-md-2 mb-4 mb-md-0 text-center d-flex align-items-center justify-content-center">
				<?php
				$image_url = get_theme_mod('footer_column_1_image');
				if ($image_url) {
					echo '<img src="' . esc_url($image_url) . '" alt="' . __('Footer Column 1 Image', 'aboyuma') . '" style="width: 80px; height: 80px;">';
				}
				?>
			</div>
			<div class="col-md-8 mb-4 mb-md-0">
				<div class="aboyuma-about-title" style="font-weight:bold; margin-bottom:0.3em;">เกี่ยวกับเรา :</div>
				<p><?php echo nl2br(esc_html(get_theme_mod('footer_column_2_content'))); ?></p>
			</div>
			<div class="col-md-2 mb-4 mb-md-0">
				<div class="social-contact-wrapper">
					<div class="aboyuma-social-contact-title" style="font-weight:bold; margin-bottom:0.3em;">ติดตามเรา :</div>
					<div id="social-contact">
						<ul style="display: flex; flex-direction: row; flex-wrap: wrap; list-style: none; padding: 0; margin: 0; gap: 8px;">
							<?php
							$socials = [
								'facebook' => [
									'link' => get_theme_mod('footer_facebook_link'),
									'icon' => 'https://img.icons8.com/?size=100&id=13912&format=png&color=000000'
								],
								'instagram' => [
									'link' => get_theme_mod('footer_instagram_link'),
									'icon' => 'https://img.icons8.com/?size=100&id=32323&format=png&color=000000'
								],
								'youtube' => [
									'link' => get_theme_mod('footer_youtube_link'),
									'icon' => 'https://img.icons8.com/?size=100&id=19318&format=png&color=000000'
								]
							];

							foreach ($socials as $key => $data) {
								if (!empty($data['link'])) {
									echo '<li>';
									echo '<a href="' . esc_url($data['link']) . '" target="_blank" rel="noopener noreferrer" style="display:inline-block;">';
									echo '<img src="' . esc_url($data['icon']) . '" width="24" height="24" alt="' . esc_attr(ucfirst($key)) . '">';
									echo '</a>';
									echo '</li>';
								}
							}
							?>
						</ul>
					</div>
				</div>
			</div>

		</div>
	</div>
	<!-- Grid container -->
	<!-- Copyright -->
	<div id="copyright-content" class="text-center p-2 copyright-content">
		<div>
			<?php
			$start_year = 2026;
			$current_year = date('Y');

			if ($start_year == $current_year) {
				echo "&copy; {$current_year} aboyuma.com";
			} else {
				echo "&copy; {$start_year}&ndash;{$current_year} aboyuma.com";
			}
			?>
		</div>
	</div>
	<!-- Copyright -->
</footer>
</div><!-- /#content-wrapper -->
</div><!-- /#wrapper -->

<!-- Floating Messenger Chat Button -->
<?php
$messenger_url  = get_theme_mod('messenger_chat_url', 'https://m.me/YOUR_FACEBOOK_PAGE');
$tooltip_text  = get_theme_mod(
	'messenger_tooltip_text',
	"สนใจสมัครสมาชิก สั่งซื้อสินค้า <br>หรือสอบถามข้อมูลใช่ไหม ทักเลย!"
);
?>

<?php if (! empty($messenger_url)) : ?>
	<a href="<?php echo esc_url($messenger_url); ?>"
		target="_blank"
		class="messenger-chat-btn"
		aria-label="Chat with us on Messenger">

		<?php if (! empty($tooltip_text)) : ?>
			<span class="messenger-tooltip">
				<?php echo wp_kses_post($tooltip_text); ?>
			</span>
		<?php endif; ?>

		<img src="<?php echo get_template_directory_uri(); ?>/src/images/messenger.png" alt="Messenger">
	</a>
<?php endif; ?>
<button class="scroll-to-top-btn badge bg-dark bg-opacity-75">
	↑ Go to top
</button>
<?php
wp_footer();
?>
</body>

</html>