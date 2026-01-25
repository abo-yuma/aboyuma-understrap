<?php

/**
 * Image settings for the theme.
 * - Enable Featured Images
 * - Register custom image sizes
 */

function aboyuma_image_setup() {

    // Enable Featured Images (post thumbnails)
    add_theme_support('post-thumbnails');

    // Custom image size for home page post feed cards (300x150, hard crop)
    add_image_size('card-300x150', 300, 150, true);
}

add_action('after_setup_theme', 'aboyuma_image_setup');
