<?php
class WP_Widget_Aboyuma_Quick_Links extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'aboyuma_quick_links', // widget ID
            'Aboyuma Quick Links',  // widget name
            array('description' => 'Add a list of quick links by Aboyuma')
        );
    }

    // Front-end display
    public function widget($args, $instance) {
        $title = apply_filters('widget_title', $instance['title'] ?? '');
        $links = isset($instance['links']) ? json_decode($instance['links'], true) : [];
        
        echo $args['before_widget'];
        
        if (!empty($title)) {
            echo $args['before_title'].esc_html($title) . $args['after_title'];
        }
       
        if (!empty($links)) {
            echo '<ul class="aboyuma-quick-links-list">';
            foreach ($links as $link) {
                $url = esc_url($link['url'] ?? '#');
                $label = trim($link['label'] ?? '');

                if (empty($label)) {
                    $label = $url;
                }
                echo "<li><a href=\"{$url}\" target=\"_blank\" rel=\"noopener\"><i class='fa fa-external-link' aria-hidden='true'></i> {$label}</a></li>";
            }
            echo '</ul>';
        }
        
        echo $args['after_widget'];
    }

    // Backend form
    public function form($instance) {
        $title = $instance['title'] ?? '';
        $links = isset($instance['links']) ? json_decode($instance['links'], true) : [];
?>
        <p>
            <label>Title:</label>
            <input class="widefat" type="text" name="<?php echo esc_attr($this->get_field_name('title')); ?>" value="<?php echo esc_attr($title); ?>">
        </p>

        <div class="aboyuma-quick-links-wrapper">
            <?php for ($i = 0; $i < 5; $i++) :
                $label = $links[$i]['label'] ?? '';
                $url = $links[$i]['url'] ?? '';
            ?>
                <p>
                    <label>Label <?php echo $i + 1; ?>:</label>
                    <input class="widefat" type="text" name="<?php echo esc_attr($this->get_field_name("link_label_{$i}")); ?>" value="<?php echo esc_attr($label); ?>">
                    <label>URL <?php echo $i + 1; ?>:</label>
                    <input class="widefat" type="url" name="<?php echo esc_attr($this->get_field_name("link_url_{$i}")); ?>" value="<?php echo esc_attr($url); ?>">
                </p>
            <?php endfor; ?>
        </div>
<?php
    }

    // Save widget settings
    public function update($new_instance, $old_instance) {
        $instance = [];
        $instance['title'] = sanitize_text_field($new_instance['title']);

        $links = [];
        for ($i = 0; $i < 5; $i++) {
            $label = sanitize_text_field($new_instance["link_label_{$i}"] ?? '');
            $url = esc_url_raw($new_instance["link_url_{$i}"] ?? '');

            if (!empty($url)) {
                $links[] = [
                    'label' => $label,
                    'url' => $url,
                ];
            }
        }

        $instance['links'] = json_encode($links);

        return $instance;
    }
}

// Register the widget
function register_aboyuma_quick_links_widget() {
    register_widget('WP_Widget_Aboyuma_Quick_Links');
}
add_action('widgets_init', 'register_aboyuma_quick_links_widget');