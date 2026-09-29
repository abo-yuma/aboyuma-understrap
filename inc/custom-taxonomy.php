<?php

/**
 * Custom Taxonomy
 *
 * @package Understrap
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Register News Taxonomy
 */
if (! function_exists('aboyuma_register_news_taxonomy')) {
    function aboyuma_register_news_taxonomy() {

        $labels = array(
            'name'              => 'News Categories',
            'singular_name'     => 'News Category',
            'search_items'      => 'Search News Categories',
            'all_items'         => 'All News Categories',
            'edit_item'         => 'Edit News Category',
            'update_item'       => 'Update News Category',
            'add_new_item'      => 'Add New News Category',
            'new_item_name'     => 'New News Category',
            'menu_name'         => 'Categories',
        );

        $args = array(
            'labels'            => $labels,
            'public'            => true,
            'hierarchical'      => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_rest'      => true,
            'default_term'      => array(
                'name' => 'Uncategorized',
                'slug' => 'uncategorized',
            ),
            'rewrite'           => array(
                'slug' => 'news-category',
            ),
        );

        register_taxonomy(
            'news_category',
            array('news'),
            $args
        );
    }
}

add_action(
    'init',
    'aboyuma_register_news_taxonomy'
);
