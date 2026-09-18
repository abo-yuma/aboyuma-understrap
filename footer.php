<footer class="text-white text-center text-lg-start footer-bg-background g-0">
	<!-- Grid container -->
	<!-- Copyright -->
	<div id="copyright-content" class="text-center p-2 copyright-content">
		<div>
			<?php
			$start_year = 2026;
			$current_year = date('Y');

			if ($start_year == $current_year) {
				echo "&copy; Copyright {$current_year} aboyuma.com";
			} else {
				echo "&copy; Copyright {$start_year}&ndash;{$current_year} aboyuma.com";
			}
			?>
		</div>
	</div>
	<!-- Copyright -->
</footer>
</div><!-- /#content-wrapper -->
</div><!-- /#wrapper -->

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