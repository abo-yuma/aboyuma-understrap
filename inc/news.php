<?php

/**
 * News functions
 *
 * @package Understrap
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Set posts per page for News archive.
 */
function aboyuma_news_archive_posts_per_page($query) {

    if (
        ! is_admin() &&
        $query->is_main_query() &&
        $query->is_post_type_archive('news')
    ) {
        $query->set('posts_per_page', 20);
    }
}

add_action(
    'pre_get_posts',
    'aboyuma_news_archive_posts_per_page'
);
