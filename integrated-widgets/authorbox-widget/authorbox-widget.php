<?php
// Widget: Aboyuma Author Box
class My_Custom_Author_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'my_custom_author_widget', // Base ID
			esc_html__('ABOYUMA Author Widget', 'text_domain'), // Name
			array('description' => esc_html__('ABOYUMA Author Widget.', 'text_domain'))
		);
	}

	// Front-end display of the widget
	public function widget($args, $instance) {
		echo $args['before_widget'];

		// Get the widget data
		$title = isset($instance['title']) ? $instance['title'] : esc_html__('Lorem ipsum title', 'text_domain');
		$bio = isset($instance['bio']) ? $instance['bio'] : esc_html__('Lorem ipsum bio', 'text_domain');
		$aboyumaQualifiedTitle = isset($instance['aboyumaQualifiedTitle']) ? $instance['aboyumaQualifiedTitle'] : esc_html__('Lorem ipsum aboyumaQualifiedTitle', 'text_domain');
		$memberRegisterLinkUrl = isset($instance['memberRegisterLinkUrl']) ? $instance['memberRegisterLinkUrl'] : esc_html__('Lorem ipsum memberRegisterLinkUrl', 'text_domain');
		$memberRegisterLink = isset($instance['memberRegisterLink']) ? $instance['memberRegisterLink'] : esc_html__('Lorem ipsum memberRegisterLink', 'text_domain');
		$memberRegisterLinkInfoModalTitle = isset($instance['memberRegisterLinkInfoModalTitle']) ? $instance['memberRegisterLinkInfoModalTitle'] : esc_html__('Lorem ipsum memberRegisterLinkInfoModalTitle', 'text_domain');
		$memberRegisterLinkInfoModalContent = isset($instance['memberRegisterLinkInfoModalContent']) ? $instance['memberRegisterLinkInfoModalContent'] : esc_html__('Lorem ipsum memberRegisterLinkInfoModalContent', 'text_domain');
		$aboRegisterLinkUrl = isset($instance['aboRegisterLinkUrl']) ? $instance['aboRegisterLinkUrl'] : esc_html__('Lorem ipsum aboRegisterLinkUrl', 'text_domain');
		$aboRegisterLink = isset($instance['aboRegisterLink']) ? $instance['aboRegisterLink'] : esc_html__('Lorem ipsum aboRegisterLink', 'text_domain');
		$aboRegisterLinkInfoModalTitle = isset($instance['aboRegisterLinkInfoModalTitle']) ? $instance['aboRegisterLinkInfoModalTitle'] : esc_html__('Lorem ipsum aboRegisterLinkInfoModalTitle', 'text_domain');
		$aboRegisterLinkInfoModalContent = isset($instance['aboRegisterLinkInfoModalContent']) ? $instance['aboRegisterLinkInfoModalContent'] : esc_html__('Lorem ipsum aboRegisterLinkInfoModalContent', 'text_domain');
		$image = isset($instance['image']) ? $instance['image'] : esc_html__('Lorem ipsum image', 'text_domain');

		// Pass variables to the template
		require get_template_directory() . '/integrated-widgets/authorbox-widget/author-box-widget-template.php';

		// Modals
		global $aboyuma_member_modal_data;
		$aboyuma_member_modal_data = array(
			'title' => $memberRegisterLinkInfoModalTitle,
			'content' => $memberRegisterLinkInfoModalContent,
		);
		if (!has_action('wp_footer', 'aboyuma_member_modal')) {
			add_action('wp_footer', 'aboyuma_member_modal');
		}

		global $aboyuma_abo_modal_data;
		$aboyuma_abo_modal_data = array(
			'title' => $aboRegisterLinkInfoModalTitle,
			'content' => $aboRegisterLinkInfoModalContent,
		);
		if (!has_action('wp_footer', 'aboyuma_abo_modal')) {
			add_action('wp_footer', 'aboyuma_abo_modal');
		}

		echo $args['after_widget'];
	}

	// Widget form in the admin dashboard
	public function form($instance) {
		$title = !empty($instance['title']) ? $instance['title'] : esc_html__('Lorem ipsum title', 'text_domain');
		$bio = !empty($instance['bio']) ? $instance['bio'] : esc_html__('Lorem ipsum bio', 'text_domain');
		$image = !empty($instance['image']) ? $instance['image'] : esc_html__('Lorem ipsum image', 'text_domain');
		$aboyumaQualifiedTitle = !empty($instance['aboyumaQualifiedTitle']) ? $instance['aboyumaQualifiedTitle'] : esc_html__('Lorem ipsum aboyumaQualifiedTitle', 'text_domain');
		$memberRegisterLinkUrl = !empty($instance['memberRegisterLinkUrl']) ? $instance['memberRegisterLinkUrl'] : esc_html__('Lorem ipsum memberRegisterLinkUrl', 'text_domain');
		$memberRegisterLink = !empty($instance['memberRegisterLink']) ? $instance['memberRegisterLink'] : esc_html__('Lorem ipsum memberRegisterLink', 'text_domain');
		$memberRegisterLinkInfoModalTitle = !empty($instance['memberRegisterLinkInfoModalTitle']) ? $instance['memberRegisterLinkInfoModalTitle'] : esc_html__('Lorem ipsum memberRegisterLinkInfoModalTitle', 'text_domain');
		$memberRegisterLinkInfoModalContent = !empty($instance['memberRegisterLinkInfoModalContent']) ? $instance['memberRegisterLinkInfoModalContent'] : esc_html__('Lorem ipsum memberRegisterLinkInfoModalContent', 'text_domain');
		$aboRegisterLinkUrl = !empty($instance['aboRegisterLinkUrl']) ? $instance['aboRegisterLinkUrl'] : esc_html__('Lorem ipsum aboRegisterLinkUrl', 'text_domain');
		$aboRegisterLink = !empty($instance['aboRegisterLink']) ? $instance['aboRegisterLink'] : esc_html__('Lorem ipsum aboRegisterLink', 'text_domain');
		$aboRegisterLinkInfoModalTitle = !empty($instance['aboRegisterLinkInfoModalTitle']) ? $instance['aboRegisterLinkInfoModalTitle'] : esc_html__('Lorem ipsum aboRegisterLinkInfoModalTitle', 'text_domain');
		$aboRegisterLinkInfoModalContent = !empty($instance['aboRegisterLinkInfoModalContent']) ? $instance['aboRegisterLinkInfoModalContent'] : esc_html__('Lorem ipsum aboRegisterLinkInfoModalContent', 'text_domain');
?>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('title')); ?>">Title:</label>
			<input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('bio')); ?>">Bio:</label>
			<textarea class="widefat" id="<?php echo esc_attr($this->get_field_id('bio')); ?>" name="<?php echo esc_attr($this->get_field_name('bio')); ?>"><?php echo esc_attr($bio); ?></textarea>
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('image')); ?>">Image:</label>
			<textarea class="widefat" id="<?php echo esc_attr($this->get_field_id('image')); ?>" name="<?php echo esc_attr($this->get_field_name('image')); ?>"><?php echo esc_attr($image); ?></textarea>
			<small>Paste media URL</small>
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('aboyumaQualifiedTitle')); ?>">aboyumaQualifiedTitle:</label>
			<input class="widefat" id="<?php echo esc_attr($this->get_field_id('aboyumaQualifiedTitle')); ?>" name="<?php echo esc_attr($this->get_field_name('aboyumaQualifiedTitle')); ?>" type="text" value="<?php echo esc_attr($aboyumaQualifiedTitle); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('memberRegisterLinkUrl')); ?>">memberRegisterLinkUrl:</label>
			<textarea class="widefat" id="<?php echo esc_attr($this->get_field_id('memberRegisterLinkUrl')); ?>" name="<?php echo esc_attr($this->get_field_name('memberRegisterLinkUrl')); ?>"><?php echo esc_attr($memberRegisterLinkUrl); ?></textarea>
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('memberRegisterLink')); ?>">memberRegisterLink:</label>
			<textarea class="widefat" id="<?php echo esc_attr($this->get_field_id('memberRegisterLink')); ?>" name="<?php echo esc_attr($this->get_field_name('memberRegisterLink')); ?>"><?php echo esc_attr($memberRegisterLink); ?></textarea>
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('memberRegisterLinkInfoModalTitle')); ?>">memberRegisterLinkInfoModalTitle:</label>
			<input class="widefat" id="<?php echo esc_attr($this->get_field_id('memberRegisterLinkInfoModalTitle')); ?>" name="<?php echo esc_attr($this->get_field_name('memberRegisterLinkInfoModalTitle')); ?>" type="text" value="<?php echo esc_attr($memberRegisterLinkInfoModalTitle); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('memberRegisterLinkInfoModalContent')); ?>">memberRegisterLinkInfoModalContent:</label>
			<textarea class="widefat" id="<?php echo esc_attr($this->get_field_id('memberRegisterLinkInfoModalContent')); ?>" name="<?php echo esc_attr($this->get_field_name('memberRegisterLinkInfoModalContent')); ?>" rows="5"><?php echo wp_kses_post($memberRegisterLinkInfoModalContent); ?></textarea>
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('aboRegisterLinkUrl')); ?>">aboRegisterLinkUrl:</label>
			<textarea class="widefat" id="<?php echo esc_attr($this->get_field_id('aboRegisterLinkUrl')); ?>" name="<?php echo esc_attr($this->get_field_name('aboRegisterLinkUrl')); ?>"><?php echo esc_attr($aboRegisterLinkUrl); ?></textarea>
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('aboRegisterLink')); ?>">aboRegisterLink:</label>
			<textarea class="widefat" id="<?php echo esc_attr($this->get_field_id('aboRegisterLink')); ?>" name="<?php echo esc_attr($this->get_field_name('aboRegisterLink')); ?>"><?php echo esc_attr($aboRegisterLink); ?></textarea>
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('aboRegisterLinkInfoModalTitle')); ?>">aboRegisterLinkInfoModalTitle:</label>
			<input class="widefat" id="<?php echo esc_attr($this->get_field_id('aboRegisterLinkInfoModalTitle')); ?>" name="<?php echo esc_attr($this->get_field_name('aboRegisterLinkInfoModalTitle')); ?>" type="text" value="<?php echo esc_attr($aboRegisterLinkInfoModalTitle); ?>">
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('aboRegisterLinkInfoModalContent')); ?>">aboRegisterLinkInfoModalContent:</label>
			<textarea class="widefat" id="<?php echo esc_attr($this->get_field_id('aboRegisterLinkInfoModalContent')); ?>" name="<?php echo esc_attr($this->get_field_name('aboRegisterLinkInfoModalContent')); ?>" rows="5"><?php echo wp_kses_post($aboRegisterLinkInfoModalContent); ?></textarea>
		</p>
	<?php
	}


	// Update widget settings
	public function update($new_instance, $old_instance) {
		$instance = array();
		$instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
		$instance['bio'] = (!empty($new_instance['bio'])) ? sanitize_textarea_field($new_instance['bio']) : '';
		$instance['aboyumaQualifiedTitle'] = (!empty($new_instance['aboyumaQualifiedTitle'])) ? sanitize_text_field($new_instance['aboyumaQualifiedTitle']) : '';
		$instance['image'] = (!empty($new_instance['image'])) ? esc_url($new_instance['image']) : '';
		$instance['memberRegisterLinkUrl'] = (!empty($new_instance['memberRegisterLinkUrl'])) ? esc_url($new_instance['memberRegisterLinkUrl']) : '';
		$instance['memberRegisterLink'] = (!empty($new_instance['memberRegisterLink'])) ? sanitize_textarea_field($new_instance['memberRegisterLink']) : '';
		$instance['memberRegisterLinkInfoModalTitle'] = (!empty($new_instance['memberRegisterLinkInfoModalTitle'])) ? sanitize_textarea_field($new_instance['memberRegisterLinkInfoModalTitle']) : '';

		$allowed_tags = array(
			'p' => array('class' => true, 'style' => true),
			'span' => array('style' => true),
			'strong' => array(),
			'em' => array(),
			'br' => array(),
			'a' => array('href' => true, 'target' => true, 'rel' => true),
			'img' => array('src' => true, 'alt' => true, 'style' => true),
			'div' => array('class' => true, 'style' => true),
			'ul' => array('class' => true, 'style' => true),
			'ol' => array('class' => true, 'style' => true),
			'li' => array('class' => true, 'style' => true),
		);
		$instance['memberRegisterLinkInfoModalContent'] = (!empty($new_instance['memberRegisterLinkInfoModalContent']))
			? wp_kses($new_instance['memberRegisterLinkInfoModalContent'], $allowed_tags)
			: '';


		$instance['aboRegisterLinkUrl'] = (!empty($new_instance['aboRegisterLinkUrl'])) ? esc_url($new_instance['aboRegisterLinkUrl']) : '';
		$instance['aboRegisterLink'] = (!empty($new_instance['aboRegisterLink'])) ? sanitize_textarea_field($new_instance['aboRegisterLink']) : '';
		$instance['aboRegisterLinkInfoModalTitle'] = (!empty($new_instance['aboRegisterLinkInfoModalTitle'])) ? sanitize_textarea_field($new_instance['aboRegisterLinkInfoModalTitle']) : '';

		$instance['aboRegisterLinkInfoModalContent'] = (!empty($new_instance['aboRegisterLinkInfoModalContent']))
			? wp_kses($new_instance['aboRegisterLinkInfoModalContent'], $allowed_tags)
			: '';

		return $instance;
	}
}
function my_theme_register_aboyuma_author_box_widget() {
	register_widget('My_Custom_Author_Widget');
}
add_action('widgets_init', 'my_theme_register_aboyuma_author_box_widget');

// Add the modal HTML at the end of the page (footer)
// We use wp_footer hook so the modal is outside any sticky or positioned containers
// Reason: Modals use position: fixed and rely on being at top-level of the DOM
// If the modal is nested inside a sticky, relative, or overflow:hidden container (like the author box widget),
// it can get clipped or appear behind other elements due to stacking context / z-index issues

function aboyuma_member_modal() {
	global $aboyuma_member_modal_data;

	// Stop if there is no data
	if (empty($aboyuma_member_modal_data)) {
		return;
	}

	$title   = $aboyuma_member_modal_data['title'];
	$content = $aboyuma_member_modal_data['content'];
	?>
	<div class="modal fade" id="memberRegisterModal" tabindex="-1" aria-labelledby="memberRegisterModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="memberRegisterModalLabel"><?php echo esc_html($title); ?></h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<?php echo wp_kses_post($content); ?>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
				</div>
			</div>
		</div>
	</div>
<?php
}


function aboyuma_abo_modal() {
	global $aboyuma_abo_modal_data;

	// Stop if there is no data
	if (empty($aboyuma_abo_modal_data)) {
		return;
	}

	$title   = $aboyuma_abo_modal_data['title'];
	$content = $aboyuma_abo_modal_data['content'];
?>
	<div class="modal fade" id="aboRegisterModal" tabindex="-1" aria-labelledby="aboRegisterModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="aboRegisterModalLabel"><?php echo esc_html($title); ?></h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<?php echo wp_kses_post($content); ?>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
				</div>
			</div>
		</div>
	</div>
<?php
}
