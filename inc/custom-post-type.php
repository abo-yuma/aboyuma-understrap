<?php

/**
 * Custom Post Type
 *
 * @package Understrap
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Register News Custom Post Type
 */
if (! function_exists('aboyuma_register_news_post_type')) {
    function aboyuma_register_news_post_type() {

        $labels = array(
            'name'                  => 'News',
            'singular_name'         => 'News',
            'menu_name'             => 'News',
            'name_admin_bar'        => 'News',
            'add_new'               => 'Add News',
            'add_new_item'          => 'Add New News',
            'new_item'              => 'New News',
            'edit_item'             => 'Edit News',
            'view_item'             => 'View News',
            'all_items'             => 'All News',
            'search_items'          => 'Search News',
            'not_found'             => 'No news found',
            'not_found_in_trash'    => 'No news found in Trash',
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'show_in_rest'       => true,

            'has_archive'        => true,
            'rewrite'            => array(
                'slug' => 'news',
            ),

            'supports'           => array(
                'title',
                'editor',
                'thumbnail',
                'excerpt',
                'author',
                'revisions',
            ),

            'menu_icon'          => 'dashicons-megaphone',

            'publicly_queryable' => true,
            'query_var'          => true,
        );

        register_post_type(
            'news',
            $args
        );
    }
}

add_action(
    'init',
    'aboyuma_register_news_post_type'
);
