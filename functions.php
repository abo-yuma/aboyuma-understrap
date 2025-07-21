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
