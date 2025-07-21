<?php

/**
 * UnderStrap functions and definitions
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

// UnderStrap's includes directory.
$understrap_inc_dir = 'inc';

// Array of files to include.
$understrap_includes = array(
	'/theme-settings.php',                  // Initialize theme default settings.
	'/setup.php',                           // Theme setup and custom theme supports.
	'/widgets.php',                         // Register widget area.
	'/enqueue.php',                         // Enqueue scripts and styles.
	'/template-tags.php',                   // Custom template tags for this theme.
	'/pagination.php',                      // Custom pagination for this theme.
	'/hooks.php',                           // Custom hooks.
	'/extras.php',                          // Custom functions that act independently of the theme templates.
	'/customizer.php',                      // Customizer additions.
	'/custom-comments.php',                 // Custom Comments file.
	'/bootstrap_5_wp_nav_menu_walker.php',  // Load custom WordPress nav walker. Trying to get deeper navigation? Check out: https://github.com/understrap/understrap/issues/567.
	'/editor.php',                          // Load Editor functions.
	'/block-editor.php',                    // Load Block Editor functions.
	'/deprecated.php',                      // Load deprecated functions.
);

// Load WooCommerce functions if WooCommerce is activated.
if (class_exists('WooCommerce')) {
	$understrap_includes[] = '/woocommerce.php';
}

// Load Jetpack compatibility file if Jetpack is activiated.
if (class_exists('Jetpack')) {
	$understrap_includes[] = '/jetpack.php';
}

// Include files.
foreach ($understrap_includes as $file) {
	require_once get_theme_file_path($understrap_inc_dir . $file);
}

// ===== ABO YUMA custom add-on ===== //

// Array of custom theme plugin files
$theme_plugins = [
	'/theme-plugins/news-crud/init.php',
	'/theme-plugins/product-crud/product-init.php',
];

// Array of custom theme widget files
$theme_widgets = [
	'/theme-widgets/authorbox-widget/authorbox-widget.php',
	'/theme-widgets/recommend-video-widget/recommend-video-widget.php',
	'/theme-widgets/quick-links-widget/quick-links-widget.php',
];

// Require all plugins
foreach ($theme_plugins as $plugin) {
	require get_template_directory() . $plugin;
}

// Require all widgets
foreach ($theme_widgets as $widget) {
	require get_template_directory() . $widget;
}

// === Header Image Customizer ===
function header_customizer_register($wp_customize) {
	// Add a section for Header Image
	$wp_customize->add_section('aboyuma_header_image_section', array(
		'title'       => __('Header Image', 'aboyuma'),
		'priority'    => 30,
	));

	// Add a setting for the image
	$wp_customize->add_setting('aboyuma_header_image', array(
		'default'           => '',
		'sanitize_callback' => 'esc_url_raw',
	));

	// Add control for the image uploader
	$wp_customize->add_control(new WP_Customize_Image_Control(
		$wp_customize,
		'aboyuma_header_image',
		array(
			'label'    => __('Upload Header Image', 'aboyuma'),
			'description' => __('Height: >80px', 'aboyuma'),
			'section'  => 'aboyuma_header_image_section',
			'settings' => 'aboyuma_header_image',
		)
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
	$wp_customize->add_setting('footer_column_3_content', array(
		'default' => '
					<li class="mb-3"><img src="https://img.icons8.com/?size=100&id=13912&format=png&color=000000" width="30px"> 佑馬高橋</li>
					<li class="mb-3"><img src="https://img.icons8.com/?size=100&id=21746&format=png&color=000000" width="30px"> line@</li>',
		'sanitize_callback' => 'wp_kses_post',
	));
	$wp_customize->add_control('footer_column_3_content', array(
		'label' => __('Column 3 Content (HTML allowed)', 'aboyuma'),
		'section' => 'footer_columns',
		'type' => 'textarea',
	));
}
add_action('customize_register', 'aboyuma_customize_register');
