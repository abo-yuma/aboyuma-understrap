<?php

/**
 * Fancybox Lightbox
 *
 * @package Understrap
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Process post content.
 */
function aboyuma_lightbox_content($content) {
    if (!is_singular('post')) {
        return $content;
    }

    if (trim($content) === '') {
        return $content;
    }

    libxml_use_internal_errors(true);

    $dom = new DOMDocument();

    $dom->loadHTML(
        '<?xml encoding="utf-8" ?>' . $content,
        LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
    );

    aboyuma_lightbox_process_images($dom);

    return $dom->saveHTML();
}
add_filter('the_content', 'aboyuma_lightbox_content', 20);

/**
 * Convert post images to lightbox links.
 */
function aboyuma_lightbox_process_images(DOMDocument $dom) {
    $images = [];

    foreach ($dom->getElementsByTagName('img') as $img) {
        $images[] = $img;
    }

    foreach ($images as $img) {

        $parent = $img->parentNode;

        /*
		 * Already wrapped with <a>.
		 */
        if (
            $parent instanceof DOMElement &&
            $parent->tagName === 'a'
        ) {
            $class = trim($parent->getAttribute('class'));

            if (!str_contains($class, 'lightbox')) {
                $parent->setAttribute(
                    'class',
                    trim($class . ' lightbox')
                );
            }

            continue;
        }

        $src = $img->getAttribute('src');

        if (empty($src)) {
            continue;
        }

        $link = $dom->createElement('a');

        $link->setAttribute('href', $src);
        $link->setAttribute('class', 'lightbox');

        $parent->replaceChild($link, $img);
        $link->appendChild($img);
    }
}
