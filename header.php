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
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?> <?php understrap_body_attributes(); ?>>
	<?php do_action('wp_body_open'); ?>
	<div class="site" id="page">
		<!-- ******************* The Main Wrapper Holding Header, Main and Footer ******************* -->
		<div id="main-wrapper" class="main-wrapper container p-0">
			<header id="wrapper-header">

				<a class="skip-link <?php echo understrap_get_screen_reader_class(true); ?>" href="#content">
					<?php esc_html_e('Skip to content', 'understrap'); ?>
				</a>
				<div id="main-image" class="container g-0">
					<div class="row">
						<img src="<?php echo esc_url(get_theme_mod('aboyuma_header_image', '')); ?>"
							style="height: 250px;">
					</div>
				</div>
				<?php get_template_part('global-templates/navbar-custom-bootstrap5'); ?>

			</header><!-- #wrapper-header -->