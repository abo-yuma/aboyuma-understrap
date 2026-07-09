<?php

/**
 * The template for displaying search forms
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

$bootstrap_version = get_theme_mod('understrap_bootstrap_version', 'bootstrap4');
$uid               = wp_unique_id('s-'); // The search form specific unique ID for the input.

$aria_label = '';
if (isset($args['aria_label']) && ! empty($args['aria_label'])) {
	$aria_label = 'aria-label="' . esc_attr($args['aria_label']) . '"';
}
?>

<form role="search" class="search-form" method="get" action="<?php echo esc_url(home_url('/')); ?>" <?php echo $aria_label; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped above. 
																									?>>
	<label class="screen-reader-text" for="<?php echo $uid; ?>"><?php echo esc_html_x('Search for:', 'label', 'understrap'); ?></label>
	<div class="search-wrap">
		<div class="input-group">
			<input
				type="search"
				class="search-field form-control"
				id="<?php echo esc_attr($uid); ?>"
				name="s"
				value="<?php the_search_query(); ?>"
				placeholder="<?php echo esc_attr_x('Search…', 'placeholder', 'understrap'); ?>"
				required>

			<button
				type="submit"
				class="search-submit btn btn-primary"
				name="submit">
				<?php esc_attr_e('ค้นหา', 'submit button', 'understrap'); ?>
			</button>

		</div>
	</div>
</form>
<?php
