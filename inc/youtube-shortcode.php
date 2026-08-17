<?php

/**
 * YouTube video lightbox shortcode.
 *
 * Usage:
 * [youtube_lightbox id="VIDEO_ID"]
 */
function aboyuma_youtube_lightbox_shortcode(array $atts) {

    $atts = shortcode_atts(
        [
            'id'  => '',
            'alt' => 'Product video',
        ],
        $atts,
        'youtube_lightbox'
    );

    $video_id = trim($atts['id']);

    if (!$video_id) {
        return '';
    }

    $video_url     = 'https://www.youtube.com/watch?v=' . rawurlencode($video_id);
    $thumbnail_url = 'https://img.youtube.com/vi/' . rawurlencode($video_id) . '/maxresdefault.jpg';

    ob_start();
?>
    <div class="youtube-lightbox-container">
        <a
            href="<?php echo esc_url($video_url); ?>"
            data-fancybox
            class="text-decoration-none">
            <div class="position-relative">

                <img
                    src="<?php echo esc_url($thumbnail_url); ?>"
                    class="youtube-lightbox-image rounded"
                    loading="lazy"
                    alt="<?php echo esc_attr($atts['alt']); ?>">

                <img
                    class="video-play-icon"
                    src="https://api.iconify.design/logos/youtube-icon.svg"
                    alt="Play">

            </div>
        </a>
    </div>
<?php

    return ob_get_clean();
}

add_shortcode(
    'youtube_lightbox',
    'aboyuma_youtube_lightbox_shortcode'
);
