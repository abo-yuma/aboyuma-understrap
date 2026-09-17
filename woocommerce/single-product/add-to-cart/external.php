<?php

/**
 * External product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/external.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.0
 */
if (! defined('ABSPATH')) {
    exit;
}

do_action('woocommerce_before_add_to_cart_button'); ?>

<p class="cart">
    <!-- Affiliate/external link attributes:
     nofollow  = Prevent normal SEO ranking signals from passing through.
     sponsored = Identify this as an affiliate/sponsored link.
     noopener  = Prevent the new tab from accessing window.opener.
     noreferrer = Prevent the browser from sending the current page URL as the referrer. -->
    <a
        href="<?php echo esc_url($product_url); ?>"
        target="_blank"
        rel="nofollow sponsored noopener noreferrer"
        class="single_add_to_cart_button btn btn-primary btn-sm mt-3">
        <?php echo esc_html($button_text); ?>
    </a>
</p>

<?php do_action('woocommerce_after_add_to_cart_button'); ?>