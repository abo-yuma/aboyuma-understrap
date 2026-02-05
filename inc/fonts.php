<?php
function mytheme_register_fonts() {
    wp_enqueue_style(
        'mytheme-google-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Sarabun:wght@300;400;500;600&display=swap',
        array(),
        null
    );
}
add_action('wp_enqueue_scripts', 'mytheme_register_fonts');
