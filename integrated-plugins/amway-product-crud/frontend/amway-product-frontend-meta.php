<?php
defined('ABSPATH') || exit;

// Add page-amway-product-detail meta data for Facebook share
add_action('wp_head', function () {
    if (is_page('amway-product-detail')) {
        global $wpdb;

        $product_id = isset($_GET['id']) ? absint($_GET['id']) : 0;
        if (!$product_id) {
            return;
        }

        // --- Define table names ---
        $product_table = $wpdb->prefix . 'aboyuma_amway_product';
        $product_image_table = $wpdb->prefix . 'aboyuma_amway_product_image';

        // --- Fetch product info + first image path ---
        $sql = $wpdb->prepare("
			SELECT 
				p.product_name,
				p.product_short_description,
				pi.product_image_path
			FROM 
				{$product_table} AS p
			LEFT JOIN 
				{$product_image_table} AS pi
					ON pi.product_id = p.id
			WHERE 
				p.id = %d
			ORDER BY 
				pi.id ASC
			LIMIT 1
		", $product_id);

        $product = $wpdb->get_row($sql);
        if (!$product) {
            return;
        }

        // --- Prepare meta values ---
        $metaTitle = esc_attr($product->product_name);
        $metaDescription = esc_attr(wp_trim_words(strip_tags($product->product_short_description), 30, '...'));

        // --- Build full image URL (simple version) ---
        $metaImage = '';
        if (!empty($product->product_image_path)) {
            // Same logic as your front-end: base URL + image path
            $uploads = wp_get_upload_dir();
            $baseurl = trailingslashit($uploads['baseurl']); // e.g. http://localhost/devwp/wp-content/uploads/
            $metaImage = esc_url($baseurl . ltrim($product->product_image_path, '/'));
        }

        // --- Product page URL ---
        $metaUrl = esc_url(home_url("/amway-product-detail/?id={$product_id}"));
?>
        <!-- Open Graph Meta Tags -->
        <meta property="og:type" content="product" />
        <meta property="og:title" content="<?php echo $metaTitle; ?>" />
        <meta property="og:description" content="<?php echo $metaDescription; ?>" />
        <?php if ($metaImage): ?>
            <meta property="og:image" content="<?php echo $metaImage; ?>" />
        <?php endif; ?>
        <meta property="og:url" content="<?php echo $metaUrl; ?>" />
        <meta property="og:site_name" content="<?php bloginfo('name'); ?>" />
<?php
    }
});
