<!-- Recommend Video Widget Section -->
<div id="home-other-recommend-product" class="mb-2">
    <span style="font-weight: bold;">▶️ วิดีโอแนะนำ:</span>
    <hr>
    <?php if (!empty($data['video_1']) || !empty($data['video_2'])): ?>
        <div class="container aboyuma-video-wrapper">
            <div class="row">
                <?php if (!empty($data['video_1'])): ?>
                    <div class="col-md-6 mb-3">
                        <div class="aboyuma-video ratio ratio-16x9">
                            <iframe src="<?php echo esc_url($data['video_1']); ?>" title="YouTube video 1" allowfullscreen></iframe>
                        </div>
                        <?php if (!empty($data['comment_1'])): ?>
                            <small class="video-comment"><?php echo esc_html($data['comment_1']); ?></small>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($data['video_2'])): ?>
                    <div class="col-md-6 mb-3">
                        <div class="aboyuma-video ratio ratio-16x9">
                            <iframe src="<?php echo esc_url($data['video_2']); ?>" title="YouTube video 2" allowfullscreen></iframe>
                        </div>
                        <?php if (!empty($data['comment_2'])): ?>
                            <small class="video-comment"><?php echo esc_html($data['comment_2']); ?></small>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div><!-- Recommend Video Widget Section -->