<?php

/**
 * Table of content functions
 *
 * @package Understrap
 */

// Exit if accessed directly.
defined('ABSPATH') || exit;

if (! function_exists('aboyuma_toc')) {
    function aboyuma_toc($content) {

        if (!is_singular('post')) return $content;

        preg_match_all('/<h([2-3])(.*?)>(.*?)<\/h[2-3]>/', $content, $matches, PREG_OFFSET_CAPTURE);

        if (!$matches[0]) return $content;

        $toc = '<nav class="toc"><strong>Table of Contents</strong><ul>';

        $current_h2_open = false;

        foreach ($matches[0] as $i => $match) {

            $level = $matches[1][$i][0];
            $title = strip_tags($matches[3][$i][0]);
            $id = 'toc-' . $i;

            // add id to heading
            $original_heading = $match[0];
            $new_heading = '<h' . $level . ' id="' . $id . '">' . $matches[3][$i][0] . '</h' . $level . '>';

            $content = str_replace($original_heading, $new_heading, $content);

            // build toc
            if ($level == 2) {

                if ($current_h2_open) {
                    $toc .= '</ul></li>';
                }

                $toc .= '<li><a href="#' . $id . '">' . $title . '</a><ul>';
                $current_h2_open = true;
            } elseif ($level == 3 && $current_h2_open) {

                $toc .= '<li><a href="#' . $id . '">' . $title . '</a></li>';
            }
        }

        if ($current_h2_open) {
            $toc .= '</ul></li>';
        }

        $toc .= '</ul></nav>';

        return $toc . $content;
    }
}
add_filter('the_content', 'aboyuma_toc');

if (! function_exists('aboyuma_toc_inline_css')) {

    function aboyuma_toc_inline_css() {

        $css = "

        .toc {
            font-size: 15px;
            line-height: 1.6;
            border: 1px solid var(--bs-border-color);
            padding: 16px;
            border-radius: 8px;
            background: var(--bs-body-bg);
            margin-bottom: 1.5rem;
        }

        .toc strong {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
        }

        .toc ul {
            margin: 0;
            padding-left: 18px;
            list-style-type: square;
        }

        .toc ul ul {
            margin-top: 4px;
        }

        .toc li {
            margin: 4px 0;
        }

        .toc a {
        text-decoration: none;
        }

        .toc a:hover {
        text-decoration: underline;
        }

        ";

        wp_add_inline_style(
            'understrap-styles',
            $css
        );
    }
}
add_action('wp_enqueue_scripts', 'aboyuma_toc_inline_css');
