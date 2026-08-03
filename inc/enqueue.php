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
		/**
		 * Theme JavaScript dependencies.
		 *
		 * lightbox is a built-in theme feature implemented 
		 * The library must be loaded before theme.min.js
		 * because the theme initializes it during startup.
		 */
		$deps = array('jquery');

		if (is_singular('post')) {
			$deps[] = 'aboyuma-simple-lightbox';
			$deps[] = 'aboyuma-fancybox';
		}

		wp_enqueue_script(
			'understrap-scripts',
			get_template_directory_uri() . $theme_scripts,
			$deps,
			$js_version,
			true
		);
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

if (! function_exists('aboyuma_enqueue_styles')) {

	function aboyuma_enqueue_styles() {

		// Font Awesome 4.7.0
		wp_enqueue_style(
			'fontawesomeicon',
			'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css',
			array(),
			'4.7.0',
			'all'
		);

		// Bootstrap Icons 1.10.5
		wp_enqueue_style(
			'bootstrap-icons',
			'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css',
			array(),
			'1.10.5'
		);

		// Lightbox (Single Post Only)
		if (is_singular('post')) {


			/**
			 * SimpleLightbox (Images)
			 * The theme automatically detects supported image links and
			 * converts them into a lightbox. No additional HTML attributes
			 * are required in post content.
			 */
			wp_enqueue_style(
				'aboyuma-simple-lightbox',
				'https://cdn.jsdelivr.net/npm/simplelightbox@2.14.3/dist/simple-lightbox.min.css',
				array(),
				'2.14.3'
			);

			/**
			 * Fancybox (Videos)
			 * Video lightboxes are added manually by the content author
			 * using the `data-fancybox` attribute.
			 */
			wp_enqueue_style(
				'aboyuma-fancybox',
				'https://cdn.jsdelivr.net/npm/@fancyapps/ui@6.1/dist/fancybox/fancybox.css',
				array(),
				'6.1.0'
			);
		}
	}
}
add_action('wp_enqueue_scripts', 'aboyuma_enqueue_styles');

if (! function_exists('aboyuma_enqueue_scripts')) {

	function aboyuma_enqueue_scripts() {

		// jQuery Marquee 1.6.0
		wp_enqueue_script(
			'jquery-marquee',
			'https://cdn.jsdelivr.net/npm/jquery.marquee@1.6.0/jquery.marquee.min.js',
			array('jquery'),
			'1.6.0',
			false
		);

		// Clipboard.js 2.0.6
		wp_enqueue_script(
			'clipboard-js',
			'https://cdn.jsdelivr.net/npm/clipboard@2.0.6/dist/clipboard.min.js',
			array(),
			'2.0.6',
			true
		);

		// Lightbox (Single Post Only)
		if (is_singular('post')) {

			/**
			 * SimpleLightbox (Images)
			 * The theme automatically detects supported image links and
			 * converts them into a lightbox. No additional HTML attributes
			 * are required in post content.
			 */
			wp_enqueue_script(
				'aboyuma-simple-lightbox',
				'https://cdn.jsdelivr.net/npm/simplelightbox@2.14.3/dist/simple-lightbox.min.js',
				array('jquery'),
				'2.14.3',
				true
			);
		}

		/**
		 * Fancybox (Videos)
		 * Video lightboxes are added manually by the content author
		 * using the `data-fancybox` attribute.
		 */
		wp_enqueue_script(
			'aboyuma-fancybox',
			'https://cdn.jsdelivr.net/npm/@fancyapps/ui@6.1/dist/fancybox/fancybox.umd.js',
			array(),
			'6.1.0',
			true
		);
	}
}
add_action('wp_enqueue_scripts', 'aboyuma_enqueue_scripts');
