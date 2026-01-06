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

/*
Post and Page Views Counts
Refactor using transient with ChatGPT
Wordpress Transient API : https://developer.wordpress.org/apis/transients/
Main referece : https://www.themexpert.com/blog/track-display-post-views-in-wordpress
- Track unique post views using IP address and transients
- Function to get the view count for a page
- Counting one view per unique IP per 24 hours
- Ignoring logged-in admins
- Ignoring bots
- Separate functions to set and get views for posts and pages
- A handy unified getter for either post or page
--- Set Page Views ---
*/
function setPageViews($pageID) {
	if (function_exists('visitor_is_bot') && visitor_is_bot()) return;
	if (is_user_logged_in() && current_user_can('manage_options')) return;

	$ip = get_real_ip();
	$ip_transient_key = 'page_viewed_' . $pageID . '_' . md5($ip);

	if (false === get_transient($ip_transient_key)) {
		$count_key = 'page_views_count';
		$count = get_transient('page_views_' . $pageID);

		if ($count === false) {
			$count = get_post_meta($pageID, $count_key, true);
			if ($count === '') {
				$count = 0;
				delete_post_meta($pageID, $count_key);
				add_post_meta($pageID, $count_key, '0');
			}
		}

		$count++;
		update_post_meta($pageID, $count_key, $count);
		set_transient('page_views_' . $pageID, $count, 12 * HOUR_IN_SECONDS);

		set_transient($ip_transient_key, true, 24 * HOUR_IN_SECONDS);
	}
}

// --- Get Page Views ---
function getPageViews($pageID) {
	$count_key = 'page_views_count';
	$count = get_transient('page_views_' . $pageID);

	if ($count === false) {
		$count = get_post_meta($pageID, $count_key, true);
		if ($count == '') {
			$count = 0;
			update_post_meta($pageID, $count_key, $count);
		}
		set_transient('page_views_' . $pageID, $count, 12 * HOUR_IN_SECONDS);
	}

	return $count . ' Views';
}

// --- Set Post Views ---
function setPostViews($postID) {
	if (function_exists('visitor_is_bot') && visitor_is_bot()) return;

	$ip = get_real_ip();
	$ip_transient_key = 'post_viewed_' . $postID . '_' . md5($ip);

	if (false === get_transient($ip_transient_key)) {
		$count_key = 'post_views_count';
		$count = get_post_meta($postID, $count_key, true);

		if ($count == '') {
			$count = 0;
		}

		$count++;
		update_post_meta($postID, $count_key, $count);
		set_transient('post_views_' . $postID, $count, 12 * HOUR_IN_SECONDS);

		set_transient($ip_transient_key, true, 24 * HOUR_IN_SECONDS);
	}
}

// --- Get Post Views ---
function getPostViews($postID) {
	$count_key = 'post_views_count';
	$count = get_transient('post_views_' . $postID);

	if ($count === false) {
		$count = get_post_meta($postID, $count_key, true);
		if ($count == '') {
			$count = 0;
			update_post_meta($postID, $count_key, $count);
		}
		set_transient('post_views_' . $postID, $count, 12 * HOUR_IN_SECONDS);
	}

	return $count . ' Views';
}

// --- Unified Getter for any post type (post or page) ---
function getViews($postID) {
	$post_type = get_post_type($postID);
	if ($post_type === 'page') {
		return getPageViews($postID);
	} else {
		return getPostViews($postID);
	}
}

// Visitor Counter Snippet
// Create custom table on theme activation
function aboyuma_create_visitor_table() {
	global $wpdb;
	$table = $wpdb->prefix . 'aboyuma_visitor';
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE IF NOT EXISTS $table (
		id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		ip_address VARCHAR(100),
		visit_date DATE
	) $charset_collate;";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta($sql);
}
add_action('after_setup_theme', 'aboyuma_create_visitor_table');

// Get real IP address (Cloudflare-safe)
function get_real_ip() {
	if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
		return $_SERVER['HTTP_CF_CONNECTING_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ip_list = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
		return trim($ip_list[0]);
	}
	return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'; // ✅ fallback เป็น IP จริง
}

// Log visitor counter
function visitor_is_bot() {
	if (empty($_SERVER['HTTP_USER_AGENT'])) return true;

	$bots = [
		'bot',
		'crawl',
		'slurp',
		'spider',
		'curl',
		'wget',
		'python',
		'scrapy',
		'facebookexternalhit',
		'discordbot',
		'WhatsApp',
		'TelegramBot',
		'Twitterbot',
		'Apache-HttpClient',
		'Java',
		'Go-http-client',
		'axios'
	];
	$user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);

	foreach ($bots as $bot) {
		if (strpos($user_agent, $bot) !== false) {
			return true;
		}
	}
	return false;
}

function log_unique_visitor() {
	// Prevent logging bots (assumes you have this helper defined elsewhere)
	if (function_exists('visitor_is_bot') && visitor_is_bot()) {
		return;
	}

	global $wpdb;
	$table = $wpdb->prefix . 'aboyuma_visitor';

	// Get real IP
	$ip = get_real_ip();

	// Get current datetime
	$now = current_time('mysql');     // e.g. "2025-06-30 10:32:00"

	// Check if this IP has already been logged ever
	$query = $wpdb->prepare(
		"SELECT COUNT(*) FROM $table WHERE ip_address = %s",
		$ip
	);
	$exists = $wpdb->get_var($query);

	// Insert only if IP not logged before
	if (!$exists) {
		$wpdb->insert($table, [
			'ip_address' => $ip,
			'visit_time' => $now
		]);
	}
}
add_action('init', 'log_unique_visitor');

// Display visitor info
function display_visitor_stats() {
	global $wpdb;
	$table = $wpdb->prefix . 'aboyuma_visitor';

	$today = current_time('Y-m-d'); // still keep this for DATE() filtering
	$yesterday  = date('Y-m-d', strtotime('-1 day', strtotime($today)));
	$this_month = date('Y-m', strtotime($today));
	$this_year  = date('Y', strtotime($today));

	// Today's visitors
	$today_visitors = $wpdb->get_var($wpdb->prepare(
		"SELECT COUNT(DISTINCT ip_address) FROM $table WHERE DATE(visit_time) = %s",
		$today
	));

	// Yesterday's visitors
	$yesterday_visitors = $wpdb->get_var($wpdb->prepare(
		"SELECT COUNT(DISTINCT ip_address) FROM $table WHERE DATE(visit_time) = %s",
		$yesterday
	));

	// This month's visitors
	$month_visitors = $wpdb->get_var($wpdb->prepare(
		"SELECT COUNT(DISTINCT ip_address) FROM $table WHERE DATE_FORMAT(visit_time, '%%Y-%%m') = %s",
		$this_month
	));

	// This year's visitors
	$year_visitors = $wpdb->get_var($wpdb->prepare(
		"SELECT COUNT(DISTINCT ip_address) FROM $table WHERE YEAR(visit_time) = %d",
		$this_year
	));

	// Total visitors
	$total_visitors = $wpdb->get_var("SELECT COUNT(DISTINCT ip_address) FROM $table");
	// Shop info
	$launch_date_file = __DIR__ . '/launch_date.txt';

	if (!file_exists($launch_date_file)) {
		file_put_contents($launch_date_file, current_time('Y-m-d'));
	}

	$shop_open_date = trim(file_get_contents($launch_date_file));
	// Get max modified dates from posts, comments, visitors, and products
	$max_post_date    = $wpdb->get_var("SELECT MAX(post_modified) FROM {$wpdb->prefix}posts WHERE post_status = 'publish'");
	$max_product_date = $wpdb->get_var("SELECT MAX(modified) FROM {$wpdb->prefix}aboyuma_amway_product");

	// Combine dates safely
	$dates = array_filter([$max_post_date, $max_product_date]); // remove nulls
	if (!empty($dates)) {
		$last_update = max($dates);
	} else {
		$last_update = null;
	}

	echo '<div class="aboyuma-visitor-wrapper">';
	echo '<div class="aboyuma-visitor-title" style="font-weight:bold; margin-bottom:0.3em;">สถิติ :</div>';
	echo '<ul class="aboyuma-visitor-info" style="list-style:none; padding-left:0; margin:0;">';
	echo '<li style="margin-bottom:0.2em;">📊 ผู้ชมวันนี้ ' . number_format($today_visitors) . ' คน</li>';
	// echo '<li style="margin-bottom:0.2em;">👁️‍🗨️ ผู้ชมเมื่อวาน ' . number_format($yesterday_visitors) . ' คน</li>';
	echo '<li style="margin-bottom:0.2em;">📊 ผู้ชมเดือนนี้ ' . number_format($month_visitors) . ' คน</li>';
	// echo '<li style="margin-bottom:0.2em;">📅 ผู้ชมปีนี้ ' . number_format($year_visitors) . ' คน</li>';
	echo '<li style="margin-bottom:0.2em;">📊 ผู้ชมทั้งหมด ' . number_format($total_visitors) . ' คน</li>';
	echo '<li style="margin-bottom:0.2em;">🌐 เปิดบล็อก ' . date('j M Y', strtotime($shop_open_date)) . '</li>';
	echo '<li>🔄 บล็อกอัพเดต ' . esc_html(date('j M Y', strtotime($last_update))) . '</li>';
	echo '</ul>';
	echo '</div>';
}

function aboyuma_customize_tinymce_toolbar($init) {
	// Add toolbar options clearly separated
	$init['toolbar1'] = 'formatselect fontsizeselect | bold italic underline | alignleft aligncenter alignright | bullist numlist outdent indent | link unlink';
	$init['toolbar2'] = 'blockquote code removeformat';

	// Add available font sizes (pt is optional, WordPress TinyMCE understands both)
	$init['fontsize_formats'] = '8pt 10pt 12pt 14pt 18pt 24pt 36pt';

	return $init;
}
add_filter('tiny_mce_before_init', 'aboyuma_customize_tinymce_toolbar');

// Auto register menu on theme activation
function aboyuma_register_menu() {
	// 1️⃣ Register a menu location
	register_nav_menu('primary', __('Primary', 'aboyuma'));

	// 2️⃣ Define the pages to create
	$pages = [
		'home'                  => 'หน้าหลัก', // ✅ Added to Menu
		'about'                 => 'เกี่ยวกับ', // ✅ Parent Menu
		'about-aboyuma'         => 'เกี่ยวกับ ABO YUMA', // 🔹 Submenu under "About"
		'about-amway'           => 'เกี่ยวกับแอมเวย์',  // 🔹 Submenu under "About"
		'contact'               => 'ติดต่อ', // ✅ Added to Menu
		'amway-product'         => 'สินค้าแนะนำ', // ✅ Added to Menu
		'amway-product-detail'  => 'รายละเอียดสินค้า', // ❌ Not Added to Menu
		'shop'                  => 'ช็อป', // ✅ Added to Menu
		'amway-shop'            => 'แอมเวย์ช็อป', // ❌ Not Added to Menu
		'gallery'               => 'แกลอรี่', // ✅ Added to Menu
		'gallery-image'         => 'รูปแกลอรี่', // ❌ Not Added to Menu
	];

	$created_page_ids = [];
	$excluded_from_menu = ['amway-product-detail', 'amway-shop', 'gallery-image']; // Pages to create but not add to menu

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

	// 3️⃣ Create the menu if it doesn’t exist
	$menu_name = 'Primary';
	$menu_exists = wp_get_nav_menu_object($menu_name);
	if (!$menu_exists) {
		$menu_id = wp_create_nav_menu($menu_name);
	} else {
		$menu_id = $menu_exists->term_id;
	}

	// 4️⃣ Add menu items (EXCEPT excluded pages)
	$menu_item_ids = []; // Store menu item IDs to use for submenus
	foreach ($created_page_ids as $slug => $page_id) {
		if (in_array($slug, $excluded_from_menu)) {
			continue; // Skip 'amway-product-detail'
		}

		// กำหนด parent id สำหรับเมนูที่เป็น sub-menu
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
				'menu-item-parent-id'  => $parent_id, // กำหนด parent ถ้าเป็น submenu
			]);

			$menu_item_ids[$slug] = $menu_item_id;
		}
	}

	// 5️⃣ Set menu location
	set_theme_mod('nav_menu_locations', ['primary' => $menu_id]);
}
add_action('after_switch_theme', 'aboyuma_register_menu');

// Add Facebook share button at the end of all posts
// function add_facebook_share_button($content) {
// 	if (is_single()) { // Only for single posts
// 		$share_url = urlencode(get_permalink());
// 		$button = '<span class="share-text">Share this:</span> 
//                    <a href="https://www.facebook.com/sharer/sharer.php?u=' . $share_url . '" 
//                       target="_blank" 
//                       class="facebook-share-btn">
//                       Facebook
//                    </a>';
// 		return $content . '<div class="facebook-share-wrapper" style="margin-top:10px;">' . $button . '</div>';
// 	}
// 	return $content;
// }
// add_filter('the_content', 'add_facebook_share_button');

// Facebook Share Button Snippet
function aboyuma_facebook_share_button() {
	if (is_single() || is_page('amway-product-detail')) {

		$share_url = urlencode(get_permalink());

		echo '<div class="facebook-share-wrapper" style="margin-top:10px;">
				<div class="mb-2">
					<span class="share-text">Share this:</span>
					<a href="https://www.facebook.com/sharer/sharer.php?u=' . $share_url . '"
						onclick="window.open(this.href, \'fbshare\', \'width=600,height=400\'); return false;"
						class="facebook-share-btn">
						Facebook
					</a>
				</div>
			</div>';
	}
}

// Array of custom integrated plugin files
$integrated_plugins = [
	'/integrated-plugins/news-crud/news-init.php',
	'/integrated-plugins/amway-product-crud/amway-product-init.php',
	// '/integrated-plugins/gallery-crud/gallery-init.php',
	'/integrated-plugins/amway-shop-crud/amway-shop-init.php'
];

// Array of custom integrated widget files
$integrated_widgets = [
	'/integrated-widgets/authorbox-widget/authorbox-widget.php',
	'/integrated-widgets/recommend-video-widget/recommend-video-widget.php',
	'/integrated-widgets/quick-links-widget/quick-links-widget.php',
];

// Require all plugins
foreach ($integrated_plugins as $plugin) {
	require get_template_directory() . $plugin;
}

// Require all widgets
foreach ($integrated_widgets as $widget) {
	require get_template_directory() . $widget;
}

require_once get_template_directory() . '/inc/db-helpers.php';
