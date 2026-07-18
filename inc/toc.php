<?php

/**
 * Generate TOC and inject into content
 */
function aboyuma_toc_generate($content) {

    if (!is_singular('post')) return $content;

    preg_match_all('/<h([2-3])(.*?)>(.*?)<\/h[2-3]>/', $content, $matches, PREG_OFFSET_CAPTURE);

    if (empty($matches[0])) return $content;

    $toc = <<<HTML
    <nav class="toc bg-body-tertiary">

        <button
            class="toc-header btn w-100 text-start"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#tocCollapse"
            aria-expanded="true"
            aria-controls="tocCollapse"
        >
            <span>
                <i class="bi bi-list"></i>
                Table of Contents
            </span>

            <i class="bi bi-chevron-down toc-icon"></i>
        </button>

        <div id="tocCollapse" class="collapse show">
            <div class="toc-body">
                <ul>
    HTML;
    $current_h2_open = false;

    foreach ($matches[0] as $i => $match) {

        $level = $matches[1][$i][0];
        $title = strip_tags($matches[3][$i][0]);
        $id = 'toc-' . $i;

        // Replace heading with ID
        $original_heading = $match[0];
        $new_heading = '<h' . $level . ' id="' . esc_attr($id) . '">' . $matches[3][$i][0] . '</h' . $level . '>';

        $content = str_replace($original_heading, $new_heading, $content);

        // Build TOC
        if ($level == 2) {

            if ($current_h2_open) {
                $toc .= '</ul></li>';
            }

            $toc .= '<li><a href="#' . esc_attr($id) . '">' . esc_html($title) . '</a><ul>';
            $current_h2_open = true;
        } elseif ($level == 3 && $current_h2_open) {

            $toc .= '<li><a href="#' . esc_attr($id) . '">' . esc_html($title) . '</a></li>';
        }
    }

    if ($current_h2_open) {
        $toc .= '</ul></li>';
    }

    $toc .= <<<HTML
                </ul>
            </div>
        </div>

    </nav>
    HTML;

    return $toc . $content;
}
add_filter('the_content', 'aboyuma_toc_generate');


/**
 * Add inline CSS
 */
function aboyuma_toc_styles() {

    $css = <<<CSS
    .toc {
        background: var(--bs-secondary-bg);
        border: 1px solid var(--bs-border-color);
        border-radius: 5px;
        overflow: hidden;
        margin-bottom: 1.5rem;
        font-size: 0.9375rem; /* 15px */
    }

    .toc-header {
        padding: 0.875rem 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .5rem;
        cursor: pointer;
        font-weight: 600;
        text-decoration: none;
        /*
        * TOC header is styled as a card header, not a traditional button.
        * Remove Bootstrap button border to keep the UI clean and avoid
        * visual border flashes during interaction.
        */
        border: 0;
        color: inherit;
        background: transparent;
    }

    .toc-icon {
        transition: transform .2s ease;
    }

    .toc-header[aria-expanded="true"] .toc-icon {
        transform: rotate(180deg);
    }

    .toc-body {
        padding: 0 1rem 1rem;
    }

    .toc ul {
        margin: 0;
        padding-left: 1.25rem;
    }

    .toc ul ul {
        margin-top: .25rem;
    }

    .toc li {
        margin: .4rem 0;
    }

    .toc a {
        text-decoration: none;
    }

    .toc a:hover {
        text-decoration: underline;
    }
    CSS;

    wp_register_style('aboyuma-toc-style', false);
    wp_enqueue_style('aboyuma-toc-style');
    wp_add_inline_style('aboyuma-toc-style', $css);
}
add_action('wp_enqueue_scripts', 'aboyuma_toc_styles');
