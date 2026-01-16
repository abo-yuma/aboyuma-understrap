<?php

class Aboyuma_Recommend_Posts_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'aboyuma_recommend_posts',
            'ABOYUMA Recommended Posts',
            ['description' => 'Insert up to 5 post IDs to recommend']
        );
    }

    // FRONTEND
    public function widget($args, $instance) {

        echo $args['before_widget'];

        $title = !empty($instance['title']) ? esc_html($instance['title']) : '';

        // Collect post IDs
        $post_ids = [];
        for ($i = 1; $i <= 5; $i++) {
            if (!empty($instance["post_$i"])) {
                $post_ids[] = intval($instance["post_$i"]);
            }
        }

        if (empty($post_ids)) {
            echo $args['after_widget'];
            return;
        }

        // Query posts
        $q = new WP_Query([
            'post_type'      => 'post',
            'post__in'       => $post_ids,
            'orderby'        => 'post__in',
            'posts_per_page' => 5
        ]);

        $posts_data = [];

        if ($q->have_posts()) {
            while ($q->have_posts()) {
                $q->the_post();

                $categories = get_the_category();
                $category_links = [];

                if (!empty($categories)) {
                    foreach ($categories as $cat) {
                        $category_links[] = [
                            'name' => esc_html($cat->name),
                            'url'  => esc_url(get_category_link($cat->term_id)),
                        ];
                    }
                }

                $posts_data[] = [
                    'id'        => get_the_ID(),
                    'title'     => esc_html(mb_strimwidth(get_the_title(), 0, 50, '...')),
                    'permalink' => esc_url(get_permalink()),
                    'thumbnail' => get_the_post_thumbnail(get_the_ID(), 'medium', ['class' => 'img-fluid']),
                    'categories' => $category_links,
                ];
            }
            wp_reset_postdata();
        }

        // Prepare data for template
        $data = [
            'title' => $title,
            'posts' => $posts_data
        ];

        // Load template
        $template_path = get_template_directory() . '/integrated-widgets/recommend-posts-widget/recommend-posts-widget-template.php';

        if (file_exists($template_path)) {
            include $template_path;
        }

        echo $args['after_widget'];
    }

    // BACKEND FORM
    public function form($instance) {

        $title = $instance['title'] ?? 'Recommended Posts';
?>

        <p>
            <label>Title</label>
            <input class="widefat"
                name="<?php echo $this->get_field_name('title'); ?>"
                type="text"
                value="<?php echo esc_attr($title); ?>">
        </p>

        <p><strong>Recommended Post IDs (max 5)</strong></p>

        <?php for ($i = 1; $i <= 5; $i++):
            $val = $instance["post_$i"] ?? '';
        ?>
            <p>
                <label>Post <?php echo $i; ?> ID</label>
                <input class="widefat"
                    type="number"
                    placeholder="e.g. 128"
                    name="<?php echo $this->get_field_name("post_$i"); ?>"
                    value="<?php echo esc_attr($val); ?>">
            </p>
        <?php endfor; ?>

        <small>Tip: Edit a post and check the URL → post=123</small>

<?php
    }

    // SAVE
    public function update($new, $old) {

        $instance = [];
        $instance['title'] = sanitize_text_field($new['title'] ?? '');

        for ($i = 1; $i <= 5; $i++) {
            $instance["post_$i"] = !empty($new["post_$i"]) ? intval($new["post_$i"]) : '';
        }

        return $instance;
    }
}

function register_aboyuma_recommend_posts_widget() {
    register_widget('Aboyuma_Recommend_Posts_Widget');
}
add_action('widgets_init', 'register_aboyuma_recommend_posts_widget');

function register_recommend_posts_widget_area() {
    register_sidebar([
        'name'          => __('Recommend Posts Widget Area'),
        'id'            => 'recommend-posts-widget-area',
        'before_widget' => '<div class="widget recommend-posts-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '',
        'after_title'   => '',
    ]);
}
add_action('widgets_init', 'register_recommend_posts_widget_area');
