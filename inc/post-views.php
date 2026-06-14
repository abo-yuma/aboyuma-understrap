<?php

// =========================
// Get Real IP (Cloudflare Safe)
// =========================
function aboyuma_get_real_ip(): string {
    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    }

    return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '0.0.0.0';
}

// =========================
// Bot Detection
// =========================
function aboyuma_visitor_is_bot(): bool {
    if (empty($_SERVER['HTTP_USER_AGENT'])) {
        return true;
    }

    $bots = [
        'bot',
        'crawl',
        'slurp',
        'spider',
        'curl',
        'wget',
        'python',
        'scrapy',
        'facebookexternalhit',
        'discordbot',
        'whatsapp',
        'telegrambot',
        'twitterbot',
        'apache-httpclient',
        'java',
        'go-http-client',
        'axios'
    ];

    $ua = strtolower($_SERVER['HTTP_USER_AGENT']);

    foreach ($bots as $bot) {
        if (strpos($ua, $bot) !== false) {
            return true;
        }
    }

    return false;
}

// =========================
// Set Post Views
// =========================
function aboyuma_set_post_views(int $postID): void {
    if (aboyuma_visitor_is_bot()) {
        return;
    }

    // Ignore administrators
    if (is_user_logged_in() && current_user_can('manage_options')) {
        return;
    }

    $ip_key = 'post_viewed_' . $postID . '_' . md5(aboyuma_get_real_ip());

    // Already counted within 24 hours
    if (get_transient($ip_key) !== false) {
        return;
    }

    $count = (int) get_post_meta($postID, 'post_views_count', true);

    update_post_meta(
        $postID,
        'post_views_count',
        $count + 1
    );

    // Count once per IP every 24 hours
    set_transient($ip_key, true, 24 * 60 * 60);
}

// =========================
// Get Post Views
// =========================
function aboyuma_get_post_views(int $postID): string {
    $count = (int) get_post_meta($postID, 'post_views_count', true);

    if ($count === 0) {
        return '<span>🆕</span>';
    }

    return '<span>📊 ' . number_format($count) . '</span>';
}

// =========================
// Auto Track Views
// =========================
function aboyuma_track_post_views(): void {
    if (!is_singular('post')) {
        return;
    }

    $postID = get_queried_object_id();

    if ($postID > 0) {
        aboyuma_set_post_views($postID);
    }
}

add_action('wp_head', 'aboyuma_track_post_views');
