<?php

/**
 * Add WooCommerce support
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

add_action('after_setup_theme', 'understrap_woocommerce_support');
if (! function_exists('understrap_woocommerce_support')) {
	/**
	 * Declares WooCommerce theme support.
	 */
	function understrap_woocommerce_support() {
		add_theme_support('woocommerce');

		// Add Product Gallery support.
		add_theme_support('wc-product-gallery-lightbox');
		add_theme_support('wc-product-gallery-zoom');
		add_theme_support('wc-product-gallery-slider');

		// Add Bootstrap classes to form fields.
		add_filter('woocommerce_form_field_args', 'understrap_wc_form_field_args', 10, 3);
		add_filter('woocommerce_form_field_radio', 'understrap_wc_form_field_radio', 10, 4);
		add_filter('woocommerce_quantity_input_classes', 'understrap_quantity_input_classes');
		add_filter('woocommerce_loop_add_to_cart_args', 'understrap_loop_add_to_cart_args');

		// Wrap the add-to-cart link in `div.add-to-cart-container`.
		add_filter('woocommerce_loop_add_to_cart_link', 'understrap_loop_add_to_cart_link');

		// Add Bootstrap classes to account navigation.
		add_filter('woocommerce_account_menu_item_classes', 'understrap_account_menu_item_classes');
	}
}

// First unhook the WooCommerce content wrappers.
remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);

// Then hook in your own functions to display the wrappers your theme requires.
add_action('woocommerce_before_main_content', 'understrap_woocommerce_wrapper_start', 10);
add_action('woocommerce_after_main_content', 'understrap_woocommerce_wrapper_end', 10);

// Remove the default product sorting dropdown.
remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);
// Remove the default product sorting and result count.
remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
// Remove the default product archive pagination.
remove_action('woocommerce_after_shop_loop', 'woocommerce_pagination', 10);

add_action('woocommerce_after_shop_loop', 'aboyuma_bootstrap_pagination', 10);

/**
 * Remove WooCommerce smallscreen styles.
 */
// add_filter('woocommerce_enqueue_styles', 'aboyuma_remove_smallscreen_styles');

// if (! function_exists('aboyuma_remove_smallscreen_styles')) {
// 	function aboyuma_remove_smallscreen_styles($enqueue_styles) {
// 		unset($enqueue_styles['woocommerce-smallscreen']);

// 		return $enqueue_styles;
// 	}
// }

if (! function_exists('understrap_woocommerce_wrapper_start')) {
	/**
	 * Display the theme specific start of the page wrapper.
	 */
	function understrap_woocommerce_wrapper_start() {
		$container = get_theme_mod('understrap_container_type');
		$container_classes = $container . ' py-0';

		echo '<div id="woocommerce-wrapper">';
		echo '<div class="' . esc_attr($container_classes) . '" id="content" tabindex="-1">';

		echo '<div class="row">';

		echo '<div class="sidebar-column px-4" id="sidebar">';
		echo  get_template_part('sidebar-templates/sidebar', 'right');
		echo '</div><!-- .sidebar-column -->';
		echo '<div class="main-content-column px-4">';
		echo '<div class="main-content-wrapper mb-2">';
		echo '<main class="site-main" id="main">';
	}
}

if (! function_exists('understrap_woocommerce_wrapper_end')) {
	/**
	 * Display the theme specific end of the page wrapper.
	 */
	function understrap_woocommerce_wrapper_end() {
		echo '</main><!-- .site-main -->';
		echo '</div><!-- .main-content-wrapper -->';
		echo '</div><!-- .main-content-column -->';

		echo '</div><!-- .row -->';

		echo '</div><!-- .container(-fluid) -->';
		echo '</div><!-- #woocommerce-wrapper -->';
	}
}

if (! function_exists('understrap_wc_form_field_args')) {
	/**
	 * Modifies the form field's arguments by input type. The arguments are used
	 * in `woocommerce_form_field()` to build the form fields.
	 *
	 * @see https://woocommerce.github.io/code-reference/namespaces/default.html#function_woocommerce_form_field
	 *
	 * @param array       $args  Form field arguments.
	 * @param string      $key   Value of the fields name attribute.
	 * @param string|null $value Value of <select> option.
	 * @return array Filtered form field arguments.
	 *
	 * @phpstan-template T of array{
	 *     'type': string,
	 *     'label': string,
	 *     'description': string,
	 *     'placeholder': string,
	 *     'maxlength': false|int,
	 *     'required': bool,
	 *     'autocomplete': false|string,
	 *     'id': string,
	 *     'class': list<string>,
	 *     'label_class': list<string>,
	 *     'input_class': list<string>,
	 *     'return': bool,
	 *     'options': array<string,string>,
	 *     'custom_attributes': array<string,int|string>,
	 *     'validate': list<string>,
	 *     'default': string,
	 *     'autofocus': ?(string|bool),
	 *     'priority': ?string,
	 * }
	 * @phpstan-param T $args
	 * @phpstan-return T | array{'class': non-empty-list<string>}
	 */
	function understrap_wc_form_field_args($args, $key, $value) {
		$bootstrap4 = 'bootstrap4' === get_theme_mod('understrap_bootstrap_version', 'bootstrap4');

		// Add margin to each form field's html element wrapper (<p></p>).
		if ($bootstrap4) {
			$args['class'][] = 'form-group';
		}
		$args['class'][] = 'mb-3';

		// Start field type switch case.
		switch ($args['type']) {
			case 'country':
				/*
				 * WooCommerce will populate a <select> element of type 'country'
				 * with the country names.
				 */

				// Add class to the form field's html element wrapper.
				$args['class'][] = 'single-country';
				break;
			case 'state':
				/*
				 * WooCommerce will populate a <select> element of type 'state'
				 * with the state names.
				 */

				// Add custom data attributes to the form input itself.
				$args['custom_attributes']['data-plugin']      = 'select2';
				$args['custom_attributes']['data-allow-clear'] = 'true';
				$args['custom_attributes']['aria-hidden']      = 'true';

				// If state is text input.
				$args['input_class'][] = 'form-control';
				break;
			case 'checkbox':
				/*
				 * WooCommerce checkbox markup differs from Bootstrap checkbox
				 * markup. We apply Bootstrap classes such that the WooCommerce
				 * checkbox look matches the Bootstrap checkbox look.
				 */

				// Get Bootstrap version specific CSS class base.
				$base = $bootstrap4 ? 'custom-control' : 'form-check';

				if ('' !== $args['label'] || $bootstrap4) {
					// Wrap the label in <span> tag.
					$args['label'] = "<span class=\"{$base}-label\">{$args['label']}</span>";
				}

				// Add a class to the form input's <label> tag.
				$args['label_class'][] = $base;
				if ($bootstrap4) {
					$args['label_class'][] = 'custom-checkbox';
				}

				// Add a class to the form input itself.
				$args['input_class'][] = $base . '-input';
				break;
			case 'select':
				// Targets all <select> elements, except the country and state <select>.

				// Add a class to the form input itself.
				$args['input_class'][] = $bootstrap4 ? 'form-control' : 'form-select';

				// Add custom data attributes to the form input itself.
				$args['custom_attributes']['data-plugin']      = 'select2';
				$args['custom_attributes']['data-allow-clear'] = 'true';
				break;
			case 'radio':
				// Get Bootstrap version specific CSS class base.
				$base = $bootstrap4 ? 'custom-control' : 'form-check';

				$args['label_class'][] = $base . '-label';
				$args['input_class'][] = $base . '-input';
				break;
			default:
				$args['input_class'][] = 'form-control';
		} // End of switch ( $args ).
		return $args;
	}
}

if (! function_exists('understrap_wc_form_field_radio')) {
	/**
	 * Adjust the WooCommerce checkout/address radio fields to match the look of
	 * Bootstrap radio fields.
	 *
	 * Wraps each radio field (`<input>`+`<label>`) in a `.from-check`.
	 *
	 * If `$args['label']` is set a `<label>` tag is prepended to the radio
	 * fields. `$args['label_class']` is used for the class attribute of this
	 * tag and the class attribute of the actual input labels. Hence, we must
	 * remove the first occurrence of the label class added via
	 * `understrap_wc_form_field_args()` that is meant for input labels only.
	 *
	 * @param string              $field The field's HTML incl. the wrapper element.
	 * @param string              $key   The wrapper element's id attribute value.
	 * @param array<string|mixed> $args  An array of field arguments.
	 * @param string|null         $value The field's value.
	 * @return string The field's filtered HTML.
	 *
	 * @phpstan-template T of array{
	 *     'type': string,
	 *     'label': string,
	 *     'description': string,
	 *     'placeholder': string,
	 *     'maxlength': false|int,
	 *     'required': bool,
	 *     'autocomplete': false|string,
	 *     'id': string,
	 *     'class': list<string>,
	 *     'label_class': list<string>,
	 *     'input_class': list<string>,
	 *     'return': bool,
	 *     'options': array<string,string>,
	 *     'custom_attributes': array<string,int|string>,
	 *     'validate': list<string>,
	 *     'default': string,
	 *     'autofocus': ?(string|bool),
	 *     'priority': ?string,
	 * }
	 * @phpstan-param T $args
	 */
	function understrap_wc_form_field_radio($field, $key, $args, $value) {
		// Set up Bootstrap version specific variables.
		if ('bootstrap4' === get_theme_mod('understrap_bootstrap_version', 'bootstrap4')) {
			$wrapper_classes = 'custom-control custom-radio';
			$label_class     = 'custom-control-label';
		} else {
			$wrapper_classes = 'form-check';
			$label_class     = 'form-check-label';
		}

		// Remove the first occurrence of the label class if necessary.
		if ('' !== $args['label'] && ! empty($args['label_class'])) {
			$strpos = strpos($field, $label_class);
			if (false !== $strpos) {
				$field = substr_replace($field, '', $strpos, strlen($label_class));

				/*
				 * If $label_class was the only class in $args['label_class']
				 * then there is an empty class attribute now. Let's remove it.
				 */
				$field = str_replace('class=""', '', $field);
			}
		}

		// Wrap each radio in a <span.from-check>.
		$field = str_replace('<input', "<span class=\"{$wrapper_classes}\"><input", $field);
		$field = str_replace('</label>', '</label></span>', $field);
		if ('' !== $args['label']) {
			// Remove the closing span tag from the first <label> element.
			$strpos = strpos($field, '</label>') + strlen('</label>');
			$field  = substr_replace($field, '', $strpos, strlen('</span>'));
		}

		return $field;
	}
}

if (! is_admin() && ! function_exists('wc_review_ratings_enabled')) {
	/**
	 * Check if reviews are enabled.
	 *
	 * Function introduced in WooCommerce 3.6.0., include it for backward compatibility.
	 *
	 * @return bool
	 */
	function wc_reviews_enabled() {
		return 'yes' === get_option('woocommerce_enable_reviews');
	}

	/**
	 * Check if reviews ratings are enabled.
	 *
	 * Function introduced in WooCommerce 3.6.0., include it for backward compatibility.
	 *
	 * @return bool
	 */
	function wc_review_ratings_enabled() {
		return wc_reviews_enabled() && 'yes' === get_option('woocommerce_enable_review_rating');
	}
}

if (! function_exists('understrap_quantity_input_classes')) {
	/**
	 * Add Bootstrap class to quantity input field.
	 *
	 * @param array<int,string> $classes Array of quantity input classes.
	 * @return array<int,string>
	 */
	function understrap_quantity_input_classes($classes) {
		$classes[] = 'form-control';
		return $classes;
	}
}

if (! function_exists('understrap_loop_add_to_cart_link')) {
	/**
	 * Wrap add to cart link in container.
	 *
	 * @param string $html Add to cart link HTML.
	 * @return string Add to cart link HTML.
	 */
	function understrap_loop_add_to_cart_link($html) {
		return '<div class="add-to-cart-container">' . $html . '</div>';
	}
}

/*
 * Product loop add to cart button.
 *
 * WooCommerce uses `woocommerce_loop_add_to_cart_args` for add to cart
 * links in product loops, such as shop and product archive pages.
 *
 * Single product add to cart buttons use a different mechanism and are
 * styled separately via `.single_add_to_cart_button` in the theme CSS.
 */
if (! function_exists('understrap_loop_add_to_cart_args')) {
	/**
	 * Add Bootstrap button classes to add to cart link.
	 *
	 * @param array<string,mixed> $args Array of add to cart link arguments.
	 * @return array<string,mixed> Array of add to cart link arguments.
	 */
	function understrap_loop_add_to_cart_args($args) {
		if (isset($args['class']) && ! empty($args['class'])) {
			if (! is_string($args['class'])) {
				return $args;
			}

			// Remove the `button` class if it exists.
			if (false !== strpos($args['class'], 'button')) {
				$args['class'] = explode(' ', $args['class']);
				$args['class'] = array_diff($args['class'], array('button'));
				$args['class'] = implode(' ', $args['class']);
			}

			$args['class'] .= ' btn btn-primary btn-sm';
		} else {
			$args['class'] = 'btn btn-primary btn-sm';
		}

		if ('bootstrap4' === get_theme_mod('understrap_bootstrap_version', 'bootstrap4')) {
			$args['class'] .= ' btn-block';
		}

		return $args;
	}
}

if (! function_exists('understrap_account_menu_item_classes')) {
	/**
	 * Add Bootstrap classes to the account navigation.
	 *
	 * @param string[] $classes Array of classes added to the account menu items.
	 * @return string[] Array of classes added to the account menu items.
	 */
	function understrap_account_menu_item_classes($classes) {
		$classes[] = 'list-group-item';
		$classes[] = 'list-group-item-action';
		if (in_array('is-active', $classes, true)) {
			$classes[] = 'active';
		}
		return $classes;
	}
}

/**
 * Set External/Affiliate as the default product type.
 */
add_action('admin_footer-post-new.php', 'aboyuma_default_product_type');

if (! function_exists('aboyuma_default_product_type')) {
	function aboyuma_default_product_type() {
		global $post_type;

		// Only apply to new products.
		if ('product' !== $post_type) {
			return;
		}
?>
		<script>
			jQuery(function($) {
				// Set External/Affiliate as the default product type
				// and notify WooCommerce of the change.
				$('#product-type').val('external').trigger('change');
			});
		</script>
	<?php
	}
}

/**
 * Add Member Price field to External/Affiliate products.
 */
add_action('woocommerce_product_options_pricing', 'aboyuma_add_member_price_field');

if (! function_exists('aboyuma_add_member_price_field')) {
	/**
	 * Display Member Price field.
	 */
	function aboyuma_add_member_price_field() {
		woocommerce_wp_text_input(
			array(
				'id'            => '_member_price',
				'label' 		=> sprintf(
					__('Member price (%s)', 'aboyuma'),
					get_woocommerce_currency_symbol()
				),
				'description'   => __('Price for members.', 'aboyuma'),
				'desc_tip'      => true,
				'type'          => 'text',
				'data_type'     => 'price',
				'wrapper_class' => 'show_if_external',
			)
		);
	}
}


/**
 * Save Member Price.
 */
add_action('woocommerce_admin_process_product_object', 'aboyuma_save_member_price');

if (! function_exists('aboyuma_save_member_price')) {

	/**
	 * Save Member Price as product meta.
	 *
	 * @param WC_Product $product Product object.
	 */
	function aboyuma_save_member_price($product) {
		if ('external' !== $product->get_type()) {
			return;
		}

		if (isset($_POST['_member_price'])) {
			$product->update_meta_data(
				'_member_price',
				wc_clean(wp_unslash($_POST['_member_price']))
			);
		}
	}
}

/*
 * Product Editor flow:
 *
 * add_meta_boxes_product
 *     → Register custom meta boxes
 *     → wp_editor() renders the editors
 *
 * Update / Publish
 *     → woocommerce_admin_process_product_object
 *     → Receive values from $_POST
 *     → Sanitize with wp_kses_post()
 *     → Save with update_meta_data()
 *     → Product is saved
 */

/**
 * Add Product Specification and Product Note meta boxes.
 *
 * The `add_meta_boxes_product` hook runs when WordPress is
 * registering meta boxes for the Product post type.
 *
 * Priority 40 makes these meta boxes load after WooCommerce's
 * own product meta boxes.
 */
add_action('add_meta_boxes_product', 'aboyuma_add_product_content_meta_boxes', 40);

if (! function_exists('aboyuma_add_product_content_meta_boxes')) {
	/**
	 * Register custom meta boxes for product content.
	 */
	function aboyuma_add_product_content_meta_boxes() {

		// Add the Product Specification meta box.
		add_meta_box(
			'aboyuma-product-specification',

			// Meta box title + WooCommerce help tip (?).
			__('Product Specification', 'aboyuma') . ' ' . wc_help_tip(
				__('Detailed product specifications and information.', 'aboyuma')
			),
			// Callback function that renders the editor inside the meta box.
			'aboyuma_product_specification_meta_box',
			// Only show this meta box on the Product post type.
			'product',
			// Display in the main content area.
			'normal',
			// Low priority places it below higher-priority meta boxes.
			'low'
		);

		// Add the Product Note meta box.
		add_meta_box(
			'aboyuma-product-note',

			// Meta box title + WooCommerce help tip (?).
			__('Product Note', 'aboyuma') . ' ' . wc_help_tip(
				__('Add your own notes or additional information about this product.', 'aboyuma')
			),
			// Callback function that renders the editor inside the meta box.
			'aboyuma_product_note_meta_box',
			// Only show this meta box on the Product post type.
			'product',
			// Display in the main content area.
			'normal',
			// Low priority places it below higher-priority meta boxes.
			'low'
		);
	}
}


/**
 * Display the Product Specification editor.
 *
 * This callback is used by the Product Specification meta box.
 */
if (! function_exists('aboyuma_product_specification_meta_box')) {
	function aboyuma_product_specification_meta_box($post) {

		// Load the saved Product Specification from post meta
		// and display it in a WordPress Rich Text Editor.
		wp_editor(
			get_post_meta($post->ID, '_product_specification', true),

			// Unique ID for this editor instance.
			'aboyuma_product_specification',

			array(
				// Name of the textarea submitted with the product form.
				'textarea_name' => '_product_specification',
				// Show the Add Media button.
				'media_buttons' => true,
				// Use the full WordPress editor instead of the simplified editor.
				'teeny'         => false,
				// Set the editor height.
				'textarea_rows' => 8,
			)
		);
	}
}

/**
 * Display the Product Note editor.
 *
 * This callback is used by the Product Note meta box.
 */
if (! function_exists('aboyuma_product_note_meta_box')) {
	function aboyuma_product_note_meta_box($post) {

		// Load the saved Product Note from post meta
		// and display it in a WordPress Rich Text Editor.
		wp_editor(
			get_post_meta($post->ID, '_product_note', true),

			// Unique ID for this editor instance.
			'aboyuma_product_note',

			array(
				// Name of the textarea submitted with the product form.
				'textarea_name' => '_product_note',
				// Show the Add Media button.
				'media_buttons' => true,
				// Use the full WordPress editor instead of the simplified editor.
				'teeny'         => false,
				// Set the editor height.
				'textarea_rows' => 8,
			)
		);
	}
}

/**
 * Save Product Specification and Product Note.
 *
 * WooCommerce passes the WC_Product object to this hook
 * before the product is saved.
 */
add_action('woocommerce_admin_process_product_object', 'aboyuma_save_product_content_meta_boxes');

if (! function_exists('aboyuma_save_product_content_meta_boxes')) {
	/**
	 * Save custom product content to product meta.
	 *
	 * @param WC_Product $product Product object.
	 */
	function aboyuma_save_product_content_meta_boxes($product) {

		// List of custom meta fields that should be saved.
		$fields = array(
			'_product_specification',
			'_product_note',
		);

		// Process each custom field.
		foreach ($fields as $field_id) {

			// Make sure the field was submitted.
			if (isset($_POST[$field_id])) {

				$product->update_meta_data(
					$field_id,

					// Remove unwanted HTML while preserving
					// allowed post content such as formatting and links.
					wp_kses_post(
						wp_unslash($_POST[$field_id])
					)
				);
			}
		}
	}
}

/**
 * Add Product Video meta box.
 */
add_action('add_meta_boxes_product', 'aboyuma_add_product_video_meta_box', 40);

if (! function_exists('aboyuma_add_product_video_meta_box')) {
	/**
	 * Register Product Video meta box.
	 */
	function aboyuma_add_product_video_meta_box() {
		add_meta_box(
			'aboyuma-product-video',
			__('Product Video', 'aboyuma') . ' ' . wc_help_tip(
				__('Add up to two YouTube videos for this product.', 'aboyuma')
			),
			'aboyuma_product_video_meta_box',
			'product',
			'normal',
			'low'
		);
	}
}

/**
 * Display Product Video fields.
 */
if (! function_exists('aboyuma_product_video_meta_box')) {
	/**
	 * Display Product Video fields.
	 *
	 * @param WP_Post $post Current product post.
	 */
	function aboyuma_product_video_meta_box($post) {
		$video_1 = get_post_meta($post->ID, '_product_video_1', true);
		$video_2 = get_post_meta($post->ID, '_product_video_2', true);
	?>

		<p>
			<label for="aboyuma-product-video-1">
				<strong><?php esc_html_e('Video 1', 'aboyuma'); ?></strong>
			</label>
		</p>

		<input
			type="url"
			id="aboyuma-product-video-1"
			name="_product_video_1"
			value="<?php echo esc_attr($video_1); ?>"
			class="widefat"
			placeholder="https://www.youtube.com/watch?v=...">

		<p>
			<label for="aboyuma-product-video-2">
				<strong><?php esc_html_e('Video 2', 'aboyuma'); ?></strong>
			</label>
		</p>

		<input
			type="url"
			id="aboyuma-product-video-2"
			name="_product_video_2"
			value="<?php echo esc_attr($video_2); ?>"
			class="widefat"
			placeholder="https://www.youtube.com/watch?v=...">

		<?php
	}
}

/**
 * Save Product Videos.
 */
add_action('woocommerce_admin_process_product_object', 'aboyuma_save_product_video');

if (! function_exists('aboyuma_save_product_video')) {
	/**
	 * Save Product Video URLs as product meta.
	 *
	 * @param WC_Product $product Product object.
	 */
	function aboyuma_save_product_video($product) {
		$fields = array(
			'_product_video_1',
			'_product_video_2',
		);

		foreach ($fields as $field_id) {
			if (isset($_POST[$field_id])) {
				$value = esc_url_raw(wp_unslash($_POST[$field_id]));

				if ('' !== $value) {
					$product->update_meta_data($field_id, $value);
				} else {
					$product->delete_meta_data($field_id);
				}
			}
		}
	}
}

/**
 * Load product subcategories via AJAX.
 */
add_action('wp_ajax_aboyuma_load_subcategories', 'aboyuma_load_subcategories');
add_action('wp_ajax_nopriv_aboyuma_load_subcategories', 'aboyuma_load_subcategories');

if (! function_exists('aboyuma_load_subcategories')) {
	/**
	 * Load subcategories for selected product category.
	 */
	function aboyuma_load_subcategories() {

		check_ajax_referer('aboyuma_product_filter', 'nonce');

		$category_id = isset($_POST['category_id'])
			? absint($_POST['category_id'])
			: 0;

		$subcategories = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'parent'     => $category_id,
				'hide_empty' => true,
			)
		);

		if (is_wp_error($subcategories)) {
			wp_send_json_error();
		}

		ob_start();

		foreach ($subcategories as $subcategory) {
		?>
			<option value="<?php echo esc_attr($subcategory->term_id); ?>">
				<?php echo esc_html($subcategory->name); ?>
			</option>
		<?php
		}

		wp_send_json_success(
			array(
				'html' => ob_get_clean(),
			)
		);
	}
}

/**
 * Load products via AJAX.
 */
add_action('wp_ajax_aboyuma_load_products', 'aboyuma_load_products');
add_action('wp_ajax_nopriv_aboyuma_load_products', 'aboyuma_load_products');

if (! function_exists('aboyuma_load_products')) {
	/**
	 * Load filtered products.
	 */
	function aboyuma_load_products() {

		check_ajax_referer('aboyuma_product_filter', 'nonce');

		$category_id = isset($_POST['category_id'])
			? absint($_POST['category_id'])
			: 0;

		$subcategory_id = isset($_POST['subcategory_id'])
			? absint($_POST['subcategory_id'])
			: 0;

		$page = isset($_POST['page'])
			? max(1, absint($_POST['page']))
			: 1;

		$tax_query = array();

		if ($subcategory_id) {
			$tax_query[] = array(
				'taxonomy'         => 'product_cat',
				'field'            => 'term_id',
				'terms'            => $subcategory_id,
				'include_children' => false,
			);
		} elseif ($category_id) {
			$tax_query[] = array(
				'taxonomy'         => 'product_cat',
				'field'            => 'term_id',
				'terms'            => $category_id,
				'include_children' => true,
			);
		}

		$query_args = array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => apply_filters(
				'loop_shop_per_page',
				wc_get_default_products_per_row() * wc_get_default_product_rows_per_page()
			),
			'orderby'        => 'date',
			'order'          => 'DESC',
			'paged'          => $page,
		);

		$visibility_terms = wc_get_product_visibility_term_ids();

		$exclude_visibility = array(
			$visibility_terms['exclude-from-catalog'],
		);

		if ('yes' === get_option('woocommerce_hide_out_of_stock_items')) {
			$exclude_visibility[] = $visibility_terms['outofstock'];
		}

		$query_args['tax_query'] = array(
			array(
				'taxonomy' => 'product_visibility',
				'field'    => 'term_taxonomy_id',
				'terms'    => $exclude_visibility,
				'operator' => 'NOT IN',
			),
		);

		if ($tax_query) {
			$query_args['tax_query'] = array_merge(
				$query_args['tax_query'],
				$tax_query
			);
		}

		$query = new WP_Query($query_args);

		ob_start();

		if ($query->have_posts()) {

			woocommerce_product_loop_start();

			while ($query->have_posts()) {
				$query->the_post();

				// echo '<div style="border: 2px solid red; padding: 5px;">';
				// echo esc_html(get_the_title());
				// echo '</div>';

				wc_get_template_part('content', 'product');
			}

			woocommerce_product_loop_end();
		} else {
			do_action('woocommerce_no_products_found');
		}

		$product_html = ob_get_clean();
		$total = $query->found_posts;

		wp_reset_postdata();

		ob_start();

		aboyuma_bootstrap_pagination($query);

		$pagination_html = ob_get_clean();

		wp_send_json_success(
			array(
				'html'  => $product_html,
				'total' => $total,
				'current_page' => $page,
				'max_pages'    => $query->max_num_pages,
				'pagination' => $pagination_html,
				'per_page'      => $query->get('posts_per_page'),
			)
		);
	}
}

/**
 * Display WooCommerce pagination with Bootstrap styles.
 */
add_action('woocommerce_after_shop_loop', 'aboyuma_bootstrap_pagination', 10);

if (! function_exists('aboyuma_bootstrap_pagination')) {
	function aboyuma_bootstrap_pagination($query = null) {

		global $wp_query;

		if (! $query) {
			$query = $wp_query;
		}

		$total_pages = $query->max_num_pages;

		if ($total_pages <= 1) {
			return;
		}

		$current_page = max(1, $query->get('paged'));

		$pagination = paginate_links(
			array(
				'base'   => esc_url(wc_get_page_permalink('shop')) . '?page=%#%',
				'format' => '',
				'current'   => $current_page,
				'total'     => $total_pages,
				'type'      => 'array',
				'prev_text' => '&laquo;',
				'next_text' => '&raquo;',
			)
		);

		if (! $pagination) {
			return;
		}

		echo '<nav id="product-pagination" aria-label="Product pagination">';
		echo '<ul class="pagination">';

		foreach ($pagination as $page) {

			$class = 'page-item';

			if (strpos($page, 'current') !== false) {
				$class .= ' active';
			}

			if (strpos($page, 'dots') !== false) {
				$class .= ' disabled';
			}

			$page = str_replace(
				'<a',
				'<a class="page-link"',
				$page
			);

			$page = str_replace(
				'<span',
				'<span class="page-link"',
				$page
			);

			echo '<li class="' . esc_attr($class) . '">';
			echo $page;
			echo '</li>';
		}

		echo '</ul>';
		echo '</nav>';
	}
}

/**
 * Set default product archive sorting and filtering.
 *
 * Sort products by publish date, newest first.
 * Apply product category from the URL on the initial page load.
 */
add_action('pre_get_posts', 'aboyuma_product_archive_order');

if (! function_exists('aboyuma_product_archive_order')) {
	function aboyuma_product_archive_order($query) {

		if (
			is_admin()
			|| ! $query->is_main_query()
			|| ! is_shop()
		) {
			return;
		}

		// Sort products by newest first.
		$query->set('orderby', 'date');
		$query->set('order', 'DESC');

		// Get page from URL.
		$page = isset($_GET['page'])
			? max(1, absint($_GET['page']))
			: 1;

		$query->set('paged', $page);

		// Get filters from URL.
		$category_id = isset($_GET['category'])
			? absint($_GET['category'])
			: 0;

		$subcategory_id = isset($_GET['subcategory'])
			? absint($_GET['subcategory'])
			: 0;

		if ($subcategory_id) {
			$query->set(
				'tax_query',
				array(
					array(
						'taxonomy'         => 'product_cat',
						'field'            => 'term_id',
						'terms'            => $subcategory_id,
						'include_children' => false,
					),
				)
			);
		} elseif ($category_id) {
			$query->set(
				'tax_query',
				array(
					array(
						'taxonomy'         => 'product_cat',
						'field'            => 'term_id',
						'terms'            => $category_id,
						'include_children' => true,
					),
				)
			);
		}
	}
}

/**
 * Display member price in product loop.
 */
add_filter(
	'woocommerce_get_price_html',
	'aboyuma_member_price_html',
	10,
	2
);

if (! function_exists('aboyuma_member_price_html')) {
	function aboyuma_member_price_html($price_html, $product) {

		$member_price = $product->get_meta('_member_price');

		if ('' === $member_price) {
			return $price_html;
		}

		$regular_price = $product->get_regular_price();

		if ('' === $regular_price) {
			return $price_html;
		}

		return sprintf(
			'<del>%s</del><br>
			<span class="member-price">🪪 %s</span>
			<br>
			<small class="text-primary">*เฉพาะสมาชิกแอมเวย์</small>',
			wc_price($regular_price),
			wc_price($member_price)
		);
	}
}

/*
 * External product links in product loops.
 *
 * This filter applies to WooCommerce product loop/archive links only.
 * Single product external buttons use a different mechanism because
 * WooCommerce renders them through the single product add-to-cart template.
 */
add_filter(
	'woocommerce_loop_add_to_cart_args',
	'aboyuma_external_product_add_to_cart_args',
	20,
	2
);

if (! function_exists('aboyuma_external_product_add_to_cart_args')) {
	function aboyuma_external_product_add_to_cart_args($args, $product) {

		if (! $product || ! $product->is_type('external')) {
			return $args;
		}

		// Open external product in a new tab.
		$args['attributes']['target'] = '_blank';
		/*
		 * Link attributes:
		 *
		 * nofollow  — Tells search engines not to pass normal ranking signals
		 *             through this affiliate link.
		 * sponsored — Identifies the link as a paid/sponsored or affiliate link.
		 * noopener  — Prevents the newly opened page from accessing the
		 *             original window through window.opener.
		 * noreferrer — Prevents the browser from sending the current page URL
		 * as the referrer to the destination.
		 */
		$args['attributes']['rel']    = 'nofollow sponsored noopener noreferrer';

		return $args;
	}
}

/*
 * Remove pagination from WooCommerce breadcrumbs.
 *
 * AJAX pagination does not represent a new page in the
 * breadcrumb hierarchy, so the WooCommerce pagination crumb
 * is not needed.
 */
add_filter('woocommerce_get_breadcrumb', function ($crumbs) {

	foreach ($crumbs as $key => $crumb) {

		if (
			isset($crumb[0]) &&
			preg_match('/^Page \d+$/', $crumb[0])
		) {
			unset($crumbs[$key]);
		}
	}

	return array_values($crumbs);
});

// if (! function_exists('aboyuma_remove_product_tabs')) {
// 	/**
// 	 * Removes product tabs.
// 	 *
// 	 * @param array $tabs Product tabs.
// 	 * @return array Product tabs.
// 	 */
// 	function aboyuma_remove_product_tabs($tabs) {
// 		unset($tabs['reviews']);
// 		return $tabs;
// 	}
// }
// add_filter('woocommerce_product_tabs', 'aboyuma_remove_product_tabs', 98);


// Remove the Description tab heading.
add_filter('woocommerce_product_description_heading', '__return_null');
// Remove the Additional Information tab heading.
add_filter('woocommerce_product_additional_information_heading', '__return_null');

/**
 * Add Product Specification tab.
 */
add_filter('woocommerce_product_tabs', 'aboyuma_add_specification_tab');

if (! function_exists('aboyuma_add_specification_tab')) {
	function aboyuma_add_specification_tab($tabs) {

		$specification = get_post_meta(
			get_the_ID(),
			'_product_specification',
			true
		);

		if (empty($specification)) {
			return $tabs;
		}

		$tabs['aboyuma_specification'] = array(
			'title'    => __('Specification', 'aboyuma'),
			'priority' => 50,
			'callback' => 'aboyuma_specification_tab_content',
		);

		return $tabs;
	}
}


/**
 * Display Product Specification tab content.
 */
if (! function_exists('aboyuma_specification_tab_content')) {
	function aboyuma_specification_tab_content() {

		$specification = get_post_meta(
			get_the_ID(),
			'_product_specification',
			true
		);

		// Do not display the tab content if there is no specification.
		if (empty($specification)) {
			return;
		}

		echo '<div class="product-specification">';
		echo wp_kses_post(wpautop($specification));
		echo '</div>';
	}
}

/**
 * Add Product Note tab.
 */
add_filter('woocommerce_product_tabs', 'aboyuma_add_note_tab');

if (! function_exists('aboyuma_add_note_tab')) {
	function aboyuma_add_note_tab($tabs) {

		$note = get_post_meta(
			get_the_ID(),
			'_product_note',
			true
		);

		// Do not display the tab if there is no note.
		if (empty($note)) {
			return $tabs;
		}

		$tabs['aboyuma_note'] = array(
			'title'    => __('Note', 'aboyuma'),
			'priority' => 60,
			'callback' => 'aboyuma_note_tab_content',
		);

		return $tabs;
	}
}


/**
 * Display Product Note tab content.
 */
if (! function_exists('aboyuma_note_tab_content')) {
	function aboyuma_note_tab_content() {

		$note = get_post_meta(
			get_the_ID(),
			'_product_note',
			true
		);

		// Do not display empty content.
		if (empty($note)) {
			return;
		}

		echo '<div class="product-note">';
		echo wp_kses_post(wpautop($note));
		echo '</div>';
	}
}

/**
 * Remove add to cart button from product archive.
 */
remove_action(
	'woocommerce_after_shop_loop_item',
	'woocommerce_template_loop_add_to_cart',
	10
);

/**
 * Add a link to return to the product listing.
 */
if (! function_exists('aboyuma_back_to_products_link')) {

	function aboyuma_back_to_products_link() {
		?>
		<div class="mt-3 text-end">
			<a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>"
				class="btn btn-sm btn-outline-primary">
				&larr; ดูสินค้าอื่น
			</a>
		</div>
	<?php
	}

	add_action('woocommerce_single_product_summary', 'aboyuma_back_to_products_link', 55);
}

/**
 * Add a link to return to the product listing below related products.
 */
if (! function_exists('aboyuma_back_to_products_link_below_related')) {

	function aboyuma_back_to_products_link_below_related() {
	?>
		<div class="mt-3 text-end">
			<a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>"
				class="btn btn-sm btn-outline-primary">
				&larr; ดูสินค้าอื่น
			</a>
		</div>
<?php
	}

	add_action(
		'woocommerce_after_single_product_summary',
		'aboyuma_back_to_products_link_below_related',
		25
	);
}
