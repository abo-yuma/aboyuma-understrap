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
	<!-- ******************* The Main Wrapper Holding Header, Main and Footer ******************* -->
	<div id="main-wrapper" class="main-wrapper bg-body text-body">
		<a class="skip-link <?php echo understrap_get_screen_reader_class(true); ?>" href="#content">
			<?php esc_html_e('Skip to content', 'understrap'); ?>
		</a>

		<header id="site-header" class="site-header">
			<div class="container p-3">
				<div class="site-header-inner">

					<div class="site-identity">

						<div class="site-logo">
							<?php
							if (has_custom_logo()) {
								the_custom_logo();
							}
							?>
						</div>

						<div class="site-info">

							<div class="site-title">
								<?php bloginfo('name'); ?>
							</div>

							<div class="site-tagline">
								<?php bloginfo('description'); ?>
							</div>

						</div>

					</div>

					<div class="header-banner">
						<span>
							Advertisement Placement<br>
							500 × 100
						</span>
					</div>

				</div>
			</div>
		</header>

		<?php get_template_part('global-templates/navbar-custom-bootstrap5'); ?>

		<?php do_action('wp_body_open'); ?>
		<div class="site" id="page">