<?php

/**
 * Understrap Theme Customizer
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

if (! function_exists('understrap_customize_register')) {
	/**
	 * Register basic support (site title, header text color) for the Theme Customizer.
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer reference.
	 */
	function understrap_customize_register($wp_customize) {
		$settings = array('blogname', 'header_textcolor');
		foreach ($settings as $setting) {
			$get_setting = $wp_customize->get_setting($setting);
			if ($get_setting instanceof WP_Customize_Setting) {
				$get_setting->transport = 'postMessage';
			}
		}

		// Override default partial for custom logo.
		$wp_customize->selective_refresh->add_partial(
			'custom_logo',
			array(
				'settings'            => array('custom_logo'),
				'selector'            => '.custom-logo-link',
				'render_callback'     => 'understrap_customize_partial_custom_logo',
				'container_inclusive' => false,
			)
		);
	}
}
add_action('customize_register', 'understrap_customize_register');

if (! function_exists('understrap_customize_partial_custom_logo')) {
	/**
	 * Callback for rendering the custom logo, used in the custom_logo partial.
	 *
	 * @return string The custom logo markup or the site title.
	 */
	function understrap_customize_partial_custom_logo() {
		if (has_custom_logo()) {
			return get_custom_logo();
		} else {
			return get_bloginfo('name');
		}
	}
}

if (! function_exists('understrap_theme_customize_register')) {
	/**
	 * Register individual settings through customizer's API.
	 *
	 * @param WP_Customize_Manager $wp_customize Customizer reference.
	 */
	function understrap_theme_customize_register($wp_customize) {

		// Theme layout settings.
		$wp_customize->add_section(
			'understrap_theme_layout_options',
			array(
				'title'       => __('Theme Layout Settings', 'understrap'),
				'capability'  => 'edit_theme_options',
				'description' => __('Container width and sidebar defaults', 'understrap'),
				'priority'    => apply_filters('understrap_theme_layout_options_priority', 160),
			)
		);

		$wp_customize->add_setting(
			'understrap_bootstrap_version',
			array(
				'default'           => 'bootstrap4',
				'type'              => 'theme_mod',
				'sanitize_callback' => 'sanitize_text_field',
				'capability'        => 'edit_theme_options',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'understrap_bootstrap_version',
				array(
					'label'       => __('Bootstrap Version', 'understrap'),
					'description' => __('Choose between Bootstrap 4 and Bootstrap 5', 'understrap'),
					'section'     => 'understrap_theme_layout_options',
					'type'        => 'select',
					'choices'     => array(
						'bootstrap4' => __('Bootstrap 4', 'understrap'),
						'bootstrap5' => __('Bootstrap 5', 'understrap'),
					),
					'priority'    => apply_filters('understrap_bootstrap_version_priority', 10),
				)
			)
		);

		$wp_customize->add_setting(
			'understrap_container_type',
			array(
				'default'           => 'container',
				'type'              => 'theme_mod',
				'sanitize_callback' => 'understrap_customize_sanitize_select',
				'capability'        => 'edit_theme_options',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'understrap_container_type',
				array(
					'label'       => __('Container Width', 'understrap'),
					'description' => __('Choose between Bootstrap\'s container and container-fluid', 'understrap'),
					'section'     => 'understrap_theme_layout_options',
					'type'        => 'select',
					'choices'     => array(
						'container'       => __('Fixed width container', 'understrap'),
						'container-fluid' => __('Full width container', 'understrap'),
					),
					'priority'    => apply_filters('understrap_container_type_priority', 10),
				)
			)
		);

		$wp_customize->add_setting(
			'understrap_navbar_type',
			array(
				'default'           => 'collapse',
				'type'              => 'theme_mod',
				'sanitize_callback' => 'understrap_customize_sanitize_select',
				'capability'        => 'edit_theme_options',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'understrap_navbar_type',
				array(
					'label'       => __('Responsive Navigation Type', 'understrap'),
					'description' => __(
						'Choose between an expanding and collapsing navbar or an offcanvas drawer.',
						'understrap'
					),
					'section'     => 'understrap_theme_layout_options',
					'type'        => 'select',
					'choices'     => array(
						'collapse'  => __('Collapse', 'understrap'),
						'offcanvas' => __('Offcanvas', 'understrap'),
					),
					'priority'    => apply_filters('understrap_navbar_type_priority', 20),
				)
			)
		);

		$wp_customize->add_setting(
			'understrap_sidebar_position',
			array(
				'default'           => 'right',
				'type'              => 'theme_mod',
				'sanitize_callback' => 'understrap_customize_sanitize_select',
				'capability'        => 'edit_theme_options',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'understrap_sidebar_position',
				array(
					'label'       => __('Sidebar Positioning', 'understrap'),
					'description' => __(
						'Set sidebar\'s default position. Can either be: right, left, both or none. Note: this can be overridden on individual pages.',
						'understrap'
					),
					'section'     => 'understrap_theme_layout_options',
					'type'        => 'select',
					'choices'     => array(
						'right' => __('Right sidebar', 'understrap'),
						'left'  => __('Left sidebar', 'understrap'),
						'both'  => __('Left & Right sidebars', 'understrap'),
						'none'  => __('No sidebar', 'understrap'),
					),
					'priority'    => apply_filters('understrap_sidebar_position_priority', 20),
				)
			)
		);

		$wp_customize->add_setting(
			'understrap_site_info_override',
			array(
				'default'           => '',
				'type'              => 'theme_mod',
				'sanitize_callback' => 'wp_kses_post',
				'capability'        => 'edit_theme_options',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Control(
				$wp_customize,
				'understrap_site_info_override',
				array(
					'label'       => __('Footer Site Info', 'understrap'),
					'description' => __('Override Understrap\'s site info located at the footer of the page.', 'understrap'),
					'section'     => 'understrap_theme_layout_options',
					'type'        => 'textarea',
					'priority'    => 20,
				)
			)
		);

		$understrap_site_info = $wp_customize->get_setting('understrap_site_info_override');
		if ($understrap_site_info instanceof WP_Customize_Setting) {
			$understrap_site_info->transport = 'postMessage';
		}
	}
} // End of if function_exists( 'understrap_theme_customize_register' ).
add_action('customize_register', 'understrap_theme_customize_register');

if (! function_exists('understrap_customize_sanitize_select')) {
	/**
	 * Sanitize select.
	 *
	 * @since 1.2.0 Renamed from understrap_theme_slug_sanitize_select()
	 *
	 * @param string               $input   Slug to sanitize.
	 * @param WP_Customize_Setting $setting Setting instance.
	 * @return string|bool Sanitized slug if it is a valid choice; the setting default for
	 *                     invalid choices and false in all other cases.
	 */
	function understrap_customize_sanitize_select($input, $setting) {

		// Ensure input is a slug (lowercase alphanumeric characters, dashes and underscores are allowed only).
		$input = sanitize_key($input);

		$control = $setting->manager->get_control($setting->id);
		if (! $control instanceof WP_Customize_Control) {
			return false;
		}

		// Get the list of possible select options.
		$choices = $control->choices;

		// If the input is a valid key, return it; otherwise, return the default.
		return (array_key_exists($input, $choices) ? $input : $setting->default);
	}
}

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
if (! function_exists('understrap_customize_preview_js')) {
	/**
	 * Setup JS integration for live previewing.
	 */
	function understrap_customize_preview_js() {
		$file    = '/js/customizer.js';
		$version = filemtime(get_template_directory() . $file);
		if (false === $version) {
			$version = time();
		}

		wp_enqueue_script(
			'understrap_customizer',
			get_template_directory_uri() . $file,
			array('customize-preview'),
			(string) $version,
			true
		);
	}
}
// add_action('customize_preview_init', 'understrap_customize_preview_js');

/**
 * Loads javascript for conditionally showing customizer controls.
 */
if (! function_exists('understrap_customize_controls_js')) {
	/**
	 * Setup JS integration for live previewing.
	 *
	 * @since 1.1.0
	 */
	function understrap_customize_controls_js() {
		$file    = '/js/customizer-controls.js';
		$version = filemtime(get_template_directory() . $file);
		if (false === $version) {
			$version = time();
		}

		wp_enqueue_script(
			'understrap_customizer',
			get_template_directory_uri() . $file,
			array('customize-preview'),
			(string) $version,
			true
		);
	}
}
// add_action('customize_controls_enqueue_scripts', 'understrap_customize_controls_js');

if (! function_exists('understrap_default_navbar_type')) {
	/**
	 * Overrides the responsive navbar type for Bootstrap 4.
	 *
	 * @since 1.1.0
	 *
	 * @param string $current_mod Current navbar type.
	 * @return string Maybe filtered navbar type.
	 */
	function understrap_default_navbar_type($current_mod) {

		if ('bootstrap5' !== get_theme_mod('understrap_bootstrap_version')) {
			$current_mod = 'collapse';
		}

		return $current_mod;
	}
}
// add_filter('theme_mod_understrap_navbar_type', 'understrap_default_navbar_type', 20);

// ===== ABO YUMA Custom Customizer ===== //

// Messenger Button Customzier
if (! function_exists('aboyuma_messenger_button_customizer')) {

	function aboyuma_messenger_button_customizer($wp_customize) {

		// Section
		$wp_customize->add_section('messenger_settings', array(
			'title'    => __('Messenger Button', 'aboyuma'),
			'priority' => 160,
		));

		// URL setting
		$wp_customize->add_setting('messenger_chat_url', array(
			'default'           => 'https://m.me/YOUR_FACEBOOK_PAGE',
			'sanitize_callback' => 'esc_url_raw',
		));

		$wp_customize->add_control('messenger_chat_url_control', array(
			'label'    => __('Messenger Chat URL', 'aboyuma'),
			'section'  => 'messenger_settings',
			'settings' => 'messenger_chat_url',
			'type'     => 'url',
		));

		// Tooltip text setting
		$wp_customize->add_setting('messenger_tooltip_text', array(
			'default'           => "สนใจสมัครสมาชิก สั่งซื้อสินค้า <br>หรือสอบถามข้อมูลใช่ไหม ทักเลย!",
			'sanitize_callback' => 'wp_kses_post',
		));

		$wp_customize->add_control('messenger_tooltip_text_control', array(
			'label'       => __('Messenger Tooltip Text', 'aboyuma'),
			'description' => __('You can use <br> for new line', 'aboyuma'),
			'section'     => 'messenger_settings',
			'settings'    => 'messenger_tooltip_text',
			'type'        => 'textarea',
		));
	}
}

add_action('customize_register', 'aboyuma_messenger_button_customizer');

if (! function_exists('aboyuma_marquee_customizer')) {
	function aboyuma_marquee_customizer($wp_customize) {

		$wp_customize->add_section('home_marquee_section', array(
			'title'    => __('Home Marquee', 'aboyuma'),
			'priority' => 30,
		));

		// ===== Text =====
		$wp_customize->add_setting('home_marquee_text', array(
			'default' => 'Home | About | Privacy Policy',
			'sanitize_callback' => 'wp_kses_post',
		));

		$wp_customize->add_control('home_marquee_text', array(
			'label'       => __('Marquee Text', 'aboyuma'),
			'description' => __('You can use text, links, or spans. Example: Home | <a href="/about">About</a>', 'aboyuma'),
			'section'     => 'home_marquee_section',
			'type'        => 'textarea',
		));

		// ===== Speed =====
		$wp_customize->add_setting('home_marquee_duration', array(
			'default' => 15000,
			'sanitize_callback' => 'absint',
		));

		$wp_customize->add_control('home_marquee_duration', array(
			'label'       => __('Marquee Speed (ms)', 'aboyuma'),
			'description' => __('Lower = faster. Example: 3000, 6000, 10000', 'aboyuma'),
			'section'     => 'home_marquee_section',
			'type'        => 'number',
		));

		// ===== Color =====
		$wp_customize->add_setting('home_marquee_color', array(
			'default' => '#ff0000',
			'sanitize_callback' => 'sanitize_hex_color',
		));

		$wp_customize->add_control(new WP_Customize_Color_Control(
			$wp_customize,
			'home_marquee_color',
			array(
				'label'   => __('Marquee Text Color', 'aboyuma'),
				'section' => 'home_marquee_section',
			)
		));
	}
}
add_action('customize_register', 'aboyuma_marquee_customizer');

// === Header Image Customizer ===
function header_customizer_register($wp_customize) {

	// Add a section for Header Image
	$wp_customize->add_section('aboyuma_header_image_section', array(
		'title'    => __('Header Image', 'aboyuma'),
		'priority' => 30,
	));

	// Add a setting for the image
	$wp_customize->add_setting('aboyuma_header_image', array(
		'default'           => '',
		'sanitize_callback' => 'absint', // cropped image returns attachment ID
	));

	// Add cropped image control
	$wp_customize->add_control(
		new WP_Customize_Cropped_Image_Control(
			$wp_customize,
			'aboyuma_header_image',
			array(
				'label'       => __('Upload Header Image', 'aboyuma'),
				'description' => __('Recommended size: 820x160px', 'aboyuma'),
				'section'     => 'aboyuma_header_image_section',
				'settings'    => 'aboyuma_header_image',

				'width'       => 820,
				'height'      => 160,
				'flex_width'  => false,
				'flex_height' => false,
			)
		)
	);

	// Header Title
	$wp_customize->add_setting('aboyuma_header_title', array(
		'default'           => 'Yuma\'s Personal Blog',
		'sanitize_callback' => 'sanitize_text_field',
	));

	$wp_customize->add_control('aboyuma_header_title', array(
		'label'    => __('Header Title', 'aboyuma'),
		'section'  => 'aboyuma_header_image_section',
		'type'     => 'text',
	));


	// Header Subtitle
	$wp_customize->add_setting('aboyuma_header_subtitle', array(
		'default'           => 'Sharing life experiences',
		'sanitize_callback' => 'sanitize_text_field',
	));

	$wp_customize->add_control('aboyuma_header_subtitle', array(
		'label'    => __('Header Subtitle', 'aboyuma'),
		'section'  => 'aboyuma_header_image_section',
		'type'     => 'text',
	));
}
add_action('customize_register', 'header_customizer_register');

// Footer customizer
function aboyuma_customize_register($wp_customize) {
	// Add a section for Footer Columns
	$wp_customize->add_section('footer_columns', array(
		'title' => __('Footer Columns', 'aboyuma'),
		'priority' => 30,
	));

	// Column 1: About Us
	$wp_customize->add_setting('footer_column_1_image', array(
		'default' => '', // Default image URL if any.
		'sanitize_callback' => 'esc_url_raw', // Sanitize the URL of the image.
	));

	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'footer_column_1_image',
			array(
				'label'    => __('Column 1 Image', 'aboyuma'),
				'section'  => 'footer_columns',
				'settings' => 'footer_column_1_image',
			)
		)
	);

	// Column 2: Our Services
	$wp_customize->add_setting('footer_column_2_content', array(
		'default' => __('เป็นตัวแทนจำหน่ายสินค้าแอมเวย์...', 'aboyuma'),
		'sanitize_callback' => 'sanitize_textarea_field',
	));
	$wp_customize->add_control('footer_column_2_content', array(
		'label' => __('Column 2 Content', 'aboyuma'),
		'section' => 'footer_columns',
		'type' => 'textarea',
	));

	// Column 3: Contact Us
	// ===============================
	// Column 3: Contact Us (Social Links)
	// ===============================
	$wp_customize->add_section('footer_columns', array(
		'title' => __('Footer Columns', 'aboyuma'),
		'priority' => 30,
	));

	// --- Facebook ---
	$wp_customize->add_setting('footer_facebook_name', array(
		'default' => '',
		'sanitize_callback' => 'sanitize_text_field',
	));
	$wp_customize->add_control('footer_facebook_name', array(
		'label' => __('Facebook Name', 'aboyuma'),
		'section' => 'footer_columns',
		'type' => 'text',
	));

	$wp_customize->add_setting('footer_facebook_link', array(
		'default' => '',
		'sanitize_callback' => 'esc_url_raw',
	));
	$wp_customize->add_control('footer_facebook_link', array(
		'label' => __('Facebook Link', 'aboyuma'),
		'section' => 'footer_columns',
		'type' => 'url',
	));

	// --- Instagram ---
	$wp_customize->add_setting('footer_instagram_name', array(
		'default' => '',
		'sanitize_callback' => 'sanitize_text_field',
	));
	$wp_customize->add_control('footer_instagram_name', array(
		'label' => __('Instagram Name', 'aboyuma'),
		'section' => 'footer_columns',
		'type' => 'text',
	));

	$wp_customize->add_setting('footer_instagram_link', array(
		'default' => '',
		'sanitize_callback' => 'esc_url_raw',
	));
	$wp_customize->add_control('footer_instagram_link', array(
		'label' => __('Instagram Link', 'aboyuma'),
		'section' => 'footer_columns',
		'type' => 'url',
	));

	// --- YouTube ---
	$wp_customize->add_setting('footer_youtube_name', array(
		'default' => '',
		'sanitize_callback' => 'sanitize_text_field',
	));
	$wp_customize->add_control('footer_youtube_name', array(
		'label' => __('YouTube Name', 'aboyuma'),
		'section' => 'footer_columns',
		'type' => 'text',
	));

	$wp_customize->add_setting('footer_youtube_link', array(
		'default' => '',
		'sanitize_callback' => 'esc_url_raw',
	));
	$wp_customize->add_control('footer_youtube_link', array(
		'label' => __('YouTube Link', 'aboyuma'),
		'section' => 'footer_columns',
		'type' => 'url',
	));

	// --- LINE QR Code ---
	$wp_customize->add_setting('footer_line_qrcode', array(
		'default' => '',
		'sanitize_callback' => 'esc_url_raw', // sanitize image URL
	));

	$wp_customize->add_control(new WP_Customize_Image_Control(
		$wp_customize,
		'footer_line_qrcode',
		array(
			'label'    => __('Line QR Code', 'aboyuma'),
			'section'  => 'footer_columns',
			'settings' => 'footer_line_qrcode',
			'description' => __('Upload your LINE QR Code image for display in the footer.', 'aboyuma'),
		)
	));
}
add_action('customize_register', 'aboyuma_customize_register');


function aboyuma_customize_tinymce_toolbar($init) {
	// Add toolbar options clearly separated
	$init['toolbar1'] = 'formatselect fontsizeselect | bold italic underline | alignleft aligncenter alignright | bullist numlist outdent indent | link unlink';
	$init['toolbar2'] = 'blockquote code removeformat';

	// Add available font sizes (pt is optional, WordPress TinyMCE understands both)
	$init['fontsize_formats'] = '8pt 10pt 12pt 14pt 18pt 24pt 36pt';

	return $init;
}
add_filter('tiny_mce_before_init', 'aboyuma_customize_tinymce_toolbar');

function aboyuma_home_cta_customizer($wp_customize) {

	$wp_customize->add_section('home_cta_section', array(
		'title'    => 'Homepage CTA Cards',
		'priority' => 25,
	));

	for ($i = 1; $i <= 4; $i++) {

		// Image
		$wp_customize->add_setting("home_cta_image_$i");

		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				"home_cta_image_$i",
				array(
					'label'   => "CTA $i Image",
					'section' => 'home_cta_section',
				)
			)
		);

		// Title
		$wp_customize->add_setting("home_cta_title_$i");

		$wp_customize->add_control(
			"home_cta_title_$i",
			array(
				'label'   => "CTA $i Title",
				'section' => 'home_cta_section',
				'type'    => 'text',
			)
		);

		// Link
		$wp_customize->add_setting("home_cta_link_$i");

		$wp_customize->add_control(
			"home_cta_link_$i",
			array(
				'label'   => "CTA $i Link URL",
				'section' => 'home_cta_section',
				'type'    => 'url',
			)
		);
	}
}

add_action('customize_register', 'aboyuma_home_cta_customizer');

function disable_customizer_refresh($wp_customize) {

	foreach ($wp_customize->settings() as $setting) {
		$setting->transport = 'postMessage';
	}
}
add_action('customize_register', 'disable_customizer_refresh', 100);
