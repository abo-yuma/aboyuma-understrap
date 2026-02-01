<?php

/**
 * The header for our theme
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> data-bs-theme="light">

<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?> <?php understrap_body_attributes(); ?>>
	<header id="wrapper-header" class="wrapper-header">
		<div id="main-image" class="container g-0">
			<div class="row">
				<?php
				$header_image = get_theme_mod('aboyuma_header_image');

				if (! empty($header_image)) {
					$img_src = esc_url($header_image);
				} else {
					$img_src = 'https://placehold.co/980x250?text=Header+Image';
				}
				?>
				<img
					src="<?php echo $img_src; ?>"
					alt="Header image"
					style="height:180px; width:100%; object-fit:cover;">
			</div>
		</div>
		<?php get_template_part('global-templates/navbar-custom-bootstrap5'); ?>
	</header>
	<?php do_action('wp_body_open'); ?>
	<div class="site" id="page">

		<!-- ******************* The Main Wrapper Holding Header, Main and Footer ******************* -->
		<div id="main-wrapper" class="main-wrapper container p-0 bg-body text-body">
			<a class="skip-link <?php echo understrap_get_screen_reader_class(true); ?>" href="#content">
				<?php esc_html_e('Skip to content', 'understrap'); ?>
			</a>