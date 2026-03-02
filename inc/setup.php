<?php

/**
 * Theme basic setup
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

// Set the content width based on the theme's design and stylesheet.
if (! isset($content_width)) {
	$content_width = 640; /* pixels */
}

add_action('after_setup_theme', 'understrap_setup');

if (! function_exists('understrap_setup')) {
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function understrap_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on understrap, use a find and replace
		 * to change 'understrap' to the name of your theme in all the template files
		 */
		load_theme_textdomain('understrap', get_template_directory() . '/languages');

		// Add default posts and comments RSS feed links to head.
		add_theme_support('automatic-feed-links');

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support('title-tag');

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus(
			array(
				'primary' => __('Primary Menu', 'understrap'),
			)
		);

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'script',
				'style',
			)
		);

		/*
		 * Adding Thumbnail basic support
		 */
		add_theme_support('post-thumbnails');

		/*
		 * Adding support for Widget edit icons in customizer
		 */
		add_theme_support('customize-selective-refresh-widgets');

		/*
		 * Enable support for Post Formats.
		 * See https://wordpress.org/support/article/post-formats/
		 */
		add_theme_support(
			'post-formats',
			array(
				'aside',
				'image',
				'video',
				'quote',
				'link',
			)
		);

		// Set up the WordPress core custom background feature.
		add_theme_support(
			'custom-background',
			apply_filters(
				'understrap_custom_background_args',
				array(
					'default-color' => 'ffffff',
					'default-image' => '',
				)
			)
		);

		// Set up the WordPress Theme logo feature.
		add_theme_support('custom-logo');

		// Add support for responsive embedded content.
		add_theme_support('responsive-embeds');

		// Check and setup theme default settings.
		understrap_setup_theme_default_settings();
	}
}

// Auto register menu on theme activation
function aboyuma_register_menu() {
	// Register a menu location
	register_nav_menu('primary', __('Primary', 'aboyuma'));

	// Define the pages to create
	$pages = [
		'home'                  => 'หน้าหลัก', // Parent menu
		'about'                 => 'เกี่ยวกับ', // Parent menu
		'about-aboyuma'         => 'เกี่ยวกับ ABO YUMA', // Submenu under "about"
		'about-amway'           => 'เกี่ยวกับแอมเวย์',  // Submenu under "about"
		'product'               => 'สินค้า', // Parent menu
		'shop'                  => 'ช็อป', // Parent menu
		'amway-shop'            => 'แอมเวย์ช็อป', // Submenu under "shop"
		'contact'               => 'ติดต่อ', // Parent menu
	];

	$created_page_ids = [];
	// Pages to create but not add to menu
	$excluded_from_menu = ['about-aboyuma, about-amway, amway-shop'];

	foreach ($pages as $slug => $title) {
		// Check if page already exists
		$existing_page = get_page_by_path($slug);
		if (!$existing_page) {
			// Create the page
			$page_id = wp_insert_post([
				'post_title'   => $title,
				'post_name'    => $slug,
				'post_status'  => 'publish',
				'post_type'    => 'page',
			]);
			if ($page_id) {
				$created_page_ids[$slug] = $page_id;
			}
		} else {
			$created_page_ids[$slug] = $existing_page->ID;
		}
	}

	// Create the menu if it doesn’t exist
	$menu_name = 'Primary';
	$menu_exists = wp_get_nav_menu_object($menu_name);
	if (!$menu_exists) {
		$menu_id = wp_create_nav_menu($menu_name);
	} else {
		$menu_id = $menu_exists->term_id;
	}

	// Add menu items (EXCEPT excluded pages)
	$menu_item_ids = []; // Store menu item IDs to use for submenus
	foreach ($created_page_ids as $slug => $page_id) {
		if (in_array($slug, $excluded_from_menu)) {
			continue; // Skip 'amway-product-detail'
		}

		// Specify parent id สำหรับเมนูที่เป็น sub-menu
		$parent_id = 0;
		if (in_array($slug, ['about-aboyuma', 'about-amway']) && !empty($menu_item_ids['about'])) {
			$parent_id = $menu_item_ids['about']; // ทำให้เป็น submenu ใต้ "เกี่ยวกับ"
		}

		// Check if page is already in the menu
		$menu_items = wp_get_nav_menu_items($menu_id);
		$page_in_menu = false;
		if ($menu_items) {
			foreach ($menu_items as $item) {
				if ((int) $item->object_id === (int) $page_id) {
					$page_in_menu = true;
					$menu_item_ids[$slug] = $item->ID;
					break;
				}
			}
		}

		if (!$page_in_menu) {
			$menu_item_id = wp_update_nav_menu_item($menu_id, 0, [
				'menu-item-title'      => get_the_title($page_id),
				'menu-item-object'     => 'page',
				'menu-item-object-id'  => $page_id,
				'menu-item-type'       => 'post_type',
				'menu-item-status'     => 'publish',
				'menu-item-parent-id'  => $parent_id, // Specify parent if it is a submenu
			]);

			$menu_item_ids[$slug] = $menu_item_id;
		}
	}

	// Set menu location
	set_theme_mod('nav_menu_locations', ['primary' => $menu_id]);
}
add_action('after_switch_theme', 'aboyuma_register_menu');
