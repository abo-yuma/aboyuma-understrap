<?php
class Aboyuma_Recommend_Video_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'aboyuma_recommend_video_widget',
            'ABOYUMA Recommend Video',
            ['description' => __('Displays two recommended YouTube videos with comments.', 'text_domain')]
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        $video_1 = !empty($instance['video_1']) ? esc_url($instance['video_1']) : '';
        $video_2 = !empty($instance['video_2']) ? esc_url($instance['video_2']) : '';
        $comment_1 = !empty($instance['comment_1']) ? esc_html($instance['comment_1']) : '';
        $comment_2 = !empty($instance['comment_2']) ? esc_html($instance['comment_2']) : '';

        // Convert to embed URLs
        $video_1 = $this->convert_to_embed_url($video_1);
        $video_2 = $this->convert_to_embed_url($video_2);

        // Prepare data for template
        $data = [
            'video_1' => $video_1,
            'video_2' => $video_2,
            'comment_1' => $comment_1,
            'comment_2' => $comment_2,
        ];

        $template_path = get_template_directory() . '/integrated-widgets/recommend-video-widget/recommend-video-widget-template.php';
        if (file_exists($template_path)) {
            include $template_path;
        }

        echo $args['after_widget'];
    }

    public function form($instance) {
        $video_1 = !empty($instance['video_1']) ? $instance['video_1'] : '';
        $video_2 = !empty($instance['video_2']) ? $instance['video_2'] : '';
        $comment_1 = !empty($instance['comment_1']) ? $instance['comment_1'] : '';
        $comment_2 = !empty($instance['comment_2']) ? $instance['comment_2'] : '';
?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('video_1')); ?>">YouTube Video 1 URL:</label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('video_1')); ?>"
                name="<?php echo esc_attr($this->get_field_name('video_1')); ?>" type="text"
                value="<?php echo esc_url($video_1); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('comment_1')); ?>">Comment for Video 1:</label>
            <textarea class="widefat" id="<?php echo esc_attr($this->get_field_id('comment_1')); ?>"
                name="<?php echo esc_attr($this->get_field_name('comment_1')); ?>"><?php echo esc_textarea($comment_1); ?></textarea>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('video_2')); ?>">YouTube Video 2 URL:</label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('video_2')); ?>"
                name="<?php echo esc_attr($this->get_field_name('video_2')); ?>" type="text"
                value="<?php echo esc_url($video_2); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('comment_2')); ?>">Comment for Video 2:</label>
            <textarea class="widefat" id="<?php echo esc_attr($this->get_field_id('comment_2')); ?>"
                name="<?php echo esc_attr($this->get_field_name('comment_2')); ?>"><?php echo esc_textarea($comment_2); ?></textarea>
        </p>
<?php
    }

    public function update($new_instance, $old_instance) {
        $instance = [];
        $instance['video_1'] = !empty($new_instance['video_1']) ? esc_url_raw($new_instance['video_1']) : '';
        $instance['comment_1'] = !empty($new_instance['comment_1']) ? sanitize_textarea_field($new_instance['comment_1']) : '';
        $instance['video_2'] = !empty($new_instance['video_2']) ? esc_url_raw($new_instance['video_2']) : '';
        $instance['comment_2'] = !empty($new_instance['comment_2']) ? sanitize_textarea_field($new_instance['comment_2']) : '';
        return $instance;
    }

    // Convert normal YouTube URL or shortlink to embed format
    private function convert_to_embed_url($url) {
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&?]+)/', $url, $matches)) {
            return 'https://www.youtube.com/embed/' . esc_attr($matches[1]);
        }
        return '';
    }
}

// Register the widget
function register_aboyuma_recommend_video_widget() {
    register_widget('Aboyuma_Recommend_Video_Widget');
}
add_action('widgets_init', 'register_aboyuma_recommend_video_widget');

// Register the widget area
function register_recommend_video_widget_area() {
    register_sidebar([
        'name'          => __('Home Recommend Video Widget Area'),
        'id'            => 'recommend-video-widget-area',
        'before_widget' => '<div class="widget recommend-video-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ]);
}
add_action('widgets_init', 'register_recommend_video_widget_area');
