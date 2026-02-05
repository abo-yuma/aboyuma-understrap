<!-- Recommend Video Widget Section -->
<div id="reccommend-video-widget" class="mb-2">

    <h1 class="section-header">
        ▶️ วิดีโอแนะนำ
    </h1>

    <?php if (!empty($data['video_1']) || !empty($data['video_2'])): ?>
        <div class="container aboyuma-video-wrapper">
            <div class="row">
                <?php
                // Helper function to extract YouTube ID
                function aboyuma_get_youtube_id($url) {
                    preg_match('/(?:youtu\.be\/|youtube\.com(?:\/embed\/|\/watch\?v=|\/v\/|\/.+\?v=))([^&\n?#]+)/', $url, $matches);
                    return $matches[1] ?? '';
                }

                // Loop through up to 2 videos
                for ($i = 1; $i <= 2; $i++):
                    $video_url = $data["video_$i"] ?? '';
                    $comment = $data["comment_$i"] ?? '';
                    $youtube_id = aboyuma_get_youtube_id($video_url);
                    if (!$youtube_id) continue; // skip invalid video
                ?>
                    <div class="col-md-6 mb-3">
                        <div class="h-100">

                            <!-- Card image top (video thumbnail) -->
                            <div class="video-thumb-wrapper position-relative overflow-hidden" style="aspect-ratio: 16 / 9;">
                                <a data-fancybox
                                    href="https://www.youtube.com/watch?v=<?php echo esc_attr($youtube_id); ?>"
                                    data-caption="<?php echo esc_attr($comment ?: 'Recommended Video'); ?>">

                                    <img src="https://img.youtube.com/vi/<?php echo esc_attr($youtube_id); ?>/hqdefault.jpg"
                                        alt="YouTube thumbnail"
                                        class="card-img-top w-100 h-100 object-fit-cover">

                                    <span class="play-btn position-absolute top-50 start-50 translate-middle"></span>
                                </a>
                            </div>

                            <!-- Card body -->
                            <?php if (!empty($comment)): ?>
                                <div class="card-body py-2">
                                    <small class="card-text d-block">
                                        ⏯️ <?php echo esc_html($comment); ?>
                                    </small>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>

                <?php endfor; ?>
            </div>
        </div>
    <?php endif; ?>
    <?php

    if (current_user_can('manage_options')):
    ?>
        <a href="<?php echo esc_url(admin_url('widgets.php')); ?>">
            <?php esc_html_e('Edit widget'); ?></a>
    <?php
    endif;
    ?>

</div><!-- Recommend Video Widget Section -->