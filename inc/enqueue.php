<?php

/**
 * Understrap enqueue scripts
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

if (! function_exists('understrap_scripts')) {
	/**
	 * Load theme's JavaScript and CSS sources.
	 */
	function understrap_scripts() {
		// Get the theme data.
		$the_theme         = wp_get_theme();
		$theme_version     = $the_theme->get('Version');
		$bootstrap_version = get_theme_mod('understrap_bootstrap_version', 'bootstrap4');
		$suffix            = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

		// Grab asset urls.
		$theme_styles  = "/css/theme{$suffix}.css";
		$theme_scripts = "/js/theme{$suffix}.js";

		$css_version = $theme_version . '.' . filemtime(get_template_directory() . $theme_styles);
		wp_enqueue_style('understrap-styles', get_template_directory_uri() . $theme_styles, array(), $css_version);

		// Fix that the offcanvas close icon is hidden behind the admin bar.
		if ('bootstrap4' !== $bootstrap_version && is_admin_bar_showing()) {
			understrap_offcanvas_admin_bar_inline_styles();
		}

		wp_enqueue_script('jquery');

		$js_version = $theme_version . '.' . filemtime(get_template_directory() . $theme_scripts);
		wp_enqueue_script('understrap-scripts', get_template_directory_uri() . $theme_scripts, array(), $js_version, true);
		if (is_singular() && comments_open() && get_option('thread_comments')) {
			wp_enqueue_script('comment-reply');
		}
	}
} // End of if function_exists( 'understrap_scripts' ).

add_action('wp_enqueue_scripts', 'understrap_scripts');

if (! function_exists('understrap_offcanvas_admin_bar_inline_styles')) {
	/**
	 * Add inline styles for the offcanvas component if the admin bar is visible.
	 *
	 * Fixes that the offcanvas close icon is hidden behind the admin bar.
	 *
	 * @since 1.2.0
	 */
	function understrap_offcanvas_admin_bar_inline_styles() {
		$navbar_type = get_theme_mod('understrap_navbar_type', 'collapse');
		if ('offcanvas' !== $navbar_type) {
			return;
		}

		$css = '
		body.admin-bar .offcanvas.show  {
			margin-top: 32px;
		}
		@media screen and ( max-width: 782px ) {
			body.admin-bar .offcanvas.show {
				margin-top: 46px;
			}
		}';
		wp_add_inline_style('understrap-styles', $css);
	}
}

if (! function_exists('aboyuma_enqueue_scripts')) {
	function aboyuma_enqueue_scripts() {
		$theme_version = wp_get_theme()->get('Version');

		// Enqueue your custom styles and scripts here
		// Addition scripts
		// Fontawesome icon
		wp_enqueue_style(
			'fontawesomeicon',
			'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css',
			array(),
			$theme_version,
			'all'
		);

		// Enqueue Lightbox2 CSS and JS from a CDN
		wp_enqueue_style('lightbox2-css', 'https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css');
		wp_enqueue_script('lightbox2-js', 'https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js', array('jquery'), null, true);
		// Initialize Lightbox2 options
		wp_add_inline_script('lightbox2-js', "
		lightbox.option({
			'fadeDuration': 0,
			'imageFadeDuration': 0
		});");

		// FancyBox4
		wp_enqueue_style(
			'fancybox-css',
			'https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css',
			array(),
			null
		);
		wp_enqueue_script(
			'fancybox-js',
			'https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.umd.js',
			array(),
			null,
			true
		);

		// jQuery Marquee
		wp_enqueue_script('jquery-marquee', 'https://cdnjs.cloudflare.com/ajax/libs/jQuery.Marquee/1.5.0/jquery.marquee.min.js', array('jquery'), null, false);

		// Enqueue jQueryDataTables JS from a CDN
		// Ref: https://cdn.datatables.net/
		// Enqueue DataTables Bootstrap 5 CSS
		wp_enqueue_style(
			'datatables-bootstrap-style',
			'https://cdn.datatables.net/2.2.2/css/dataTables.bootstrap5.css',
			array(),
			$theme_version
		);

		// Enqueue FixedColumns Bootstrap 5 CSS
		wp_enqueue_style(
			'datatables-fixedcolumns-style',
			'https://cdn.datatables.net/fixedcolumns/5.0.4/css/fixedColumns.bootstrap5.css',
			array('datatables-bootstrap-style'),
			$theme_version
		);
		// Enqueue DataTables core JS
		wp_enqueue_script(
			'datatables-js',
			'https://cdn.datatables.net/2.2.2/js/dataTables.js',
			array('jquery'),
			$theme_version,
			true
		);
		// Enqueue DataTables Bootstrap 5 integration JS
		wp_enqueue_script(
			'datatables-bootstrap-js',
			'https://cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.js',
			array('datatables-js'),
			$theme_version,
			true
		);
		// Enqueue FixedColumns extension JS
		wp_enqueue_script(
			'datatables-fixedcolumns-js',
			'https://cdn.datatables.net/fixedcolumns/5.0.4/js/dataTables.fixedColumns.js',
			array('datatables-js'),
			$theme_version,
			true
		);
		// Enqueue Bootstrap 5 integration for FixedColumns J
		wp_enqueue_script(
			'datatables-fixedcolumns-bootstrap-js',
			'https://cdn.datatables.net/fixedcolumns/5.0.4/js/fixedColumns.bootstrap5.js',
			array('datatables-fixedcolumns-js', 'datatables-bootstrap-js'),
			$theme_version,
			true
		);

		wp_enqueue_script(
			'clipboard-js',
			'https://cdn.jsdelivr.net/npm/clipboard@2.0.6/dist/clipboard.min.js',
			array(), // no dependencies
			$theme_version,
			true // load in footer
		);

		// Bootstrap icon
		wp_enqueue_style(
			'bootstrap-icons',
			'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css'
		);

	}
}
add_action('wp_enqueue_scripts', 'aboyuma_enqueue_scripts');
