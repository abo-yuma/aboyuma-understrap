<?php

/**
 * Custom hooks
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

if (! function_exists('understrap_site_info')) {
	/**
	 * Add site info hook to WP hook library.
	 */
	function understrap_site_info() {
		do_action('understrap_site_info');
	}
}

add_action('understrap_site_info', 'understrap_add_site_info');
if (! function_exists('understrap_add_site_info')) {
	/**
	 * Add site info content.
	 */
	function understrap_add_site_info() {
		$the_theme = wp_get_theme();

		$site_info = sprintf(
			'<a href="%1$s">%2$s</a><span class="sep"> | </span>%3$s(%4$s)',
			esc_url(__('https://wordpress.org/', 'understrap')),
			sprintf(
				/* translators: WordPress */
				esc_html__('Proudly powered by %s', 'understrap'),
				'WordPress'
			),
			sprintf(
				/* translators: 1: Theme name, 2: Theme author */
				esc_html__('Theme: %1$s by %2$s.', 'understrap'),
				$the_theme->get('Name'),
				'<a href="' . esc_url(__('https://understrap.com', 'understrap')) . '">understrap.com</a>'
			),
			sprintf(
				/* translators: Theme version */
				esc_html__('Version: %s', 'understrap'),
				$the_theme->get('Version')
			)
		);

		// Check if customizer site info has value.
		if (get_theme_mod('understrap_site_info_override')) {
			$site_info = get_theme_mod('understrap_site_info_override');
		}

		echo apply_filters('understrap_site_info_content', $site_info); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	}
}


if (! function_exists('aboyuma_exclude_news_from_date_archive')) {
	/**
	 * Exclude news posts from date archives.
	 */
	function aboyuma_exclude_news_from_date_archive($query) {

		if (
			! is_admin()
			&& $query->is_main_query()
			&& $query->is_date()
		) {
			$news_category = get_category_by_slug('news');

			if ($news_category) {
				$query->set(
					'category__not_in',
					[$news_category->term_id]
				);
			}
		}
	}
}

add_action('pre_get_posts', 'aboyuma_exclude_news_from_date_archive');

if (! function_exists('aboyuma_exclude_news_from_archives_widget')) {
	/**
	 * Exclude news posts from the archives widget.
	 */
	function aboyuma_exclude_news_from_archives_widget($where, $parsed_args) {

		$news_category = get_category_by_slug('news');

		if (! $news_category) {
			return $where;
		}

		global $wpdb;

		$where .= $wpdb->prepare(
			" AND ID NOT IN (
				SELECT tr.object_id
				FROM {$wpdb->term_relationships} AS tr
				INNER JOIN {$wpdb->term_taxonomy} AS tt
					ON tr.term_taxonomy_id = tt.term_taxonomy_id
				WHERE tt.taxonomy = 'category'
				AND tt.term_id = %d
			)",
			$news_category->term_id
		);

		return $where;
	}
}

add_filter(
	'getarchives_where',
	'aboyuma_exclude_news_from_archives_widget',
	10,
	2
);
