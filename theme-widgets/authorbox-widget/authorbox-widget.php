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
			$title = isset($instance['title']) ? $instance['title'] : esc_html__('ภรัณยู (ยูมะ)', 'text_domain');
			$position = isset($instance['position']) ? $instance['position'] : esc_html__('', 'text_domain');
			$bio = isset($instance['bio']) ? $instance['bio'] : esc_html__('💻 ปัจจุบันทำงาน IT Application Support ที่บริษัทแมป มาร์เกตติ้ง ควบคู่กับการทำธุรกิจแอมเวย์เพื่อหาทุนต่อยอดทำพันธกิจคริสเตียนเเละสร้างมูลนิธิช่วยเหลือผู้คน ✝️', 'text_domain');
			$aboyumaQualifiedTitle = isset($instance['aboyumaQualifiedTitle']) ? $instance['aboyumaQualifiedTitle'] : esc_html__('นักธุรกิจแอมเวย์ รหัส 6672883', 'text_domain');
			$memberRegisterLinkIcon = isset($instance['memberRegisterLinkIcon']) ? $instance['memberRegisterLinkIcon'] : esc_html__('fa fa-address-card', 'text_domain');
			$memberRegisterLinkUrl = isset($instance['memberRegisterLinkUrl']) ? $instance['memberRegisterLinkUrl'] : esc_html__('https://th.ashare.me/25ATQlcb', 'text_domain');
			$memberRegisterLink = isset($instance['memberRegisterLink']) ? $instance['memberRegisterLink'] : esc_html__('สมัครสมาชิกแอมเวย์', 'text_domain');
			$memberRegisterLinkInfoPopoverTitle = isset($instance['memberRegisterLinkInfoPopoverTitle']) ? $instance['memberRegisterLinkInfoPopoverTitle'] : esc_html__('เกี่ยวกับลิงก์สมัครสมาชิก', 'text_domain');
			$memberRegisterLinkInfoPopoverContent = isset($instance['memberRegisterLinkInfoPopoverContent']) ? $instance['memberRegisterLinkInfoPopoverContent'] : esc_html__('ลิงก์นี้เป็นลิงก์สมัครสมาชิกแอมเวย์ของ ABO YUMA เมื่อเปิดลิงก์สมัครเเล้วใน url จะระบุว่า referringABO=6672883', 'text_domain');
			// Get the author's image dynamically
			$image = isset($instance['image']) ? $instance['image'] : esc_html__('Image.', 'text_domain');

			// Pass these variables to the template
			require get_template_directory() . '/theme-widgets/authorbox-widget/author-box-widget-template.php';

			echo $args['after_widget'];
	}

	// Widget form in the admin dashboard
	public function form($instance) {
			$title = !empty($instance['title']) ? $instance['title'] : esc_html__('ภรัณยู (ยูมะ)', 'text_domain');
			$position = !empty($instance['position']) ? $instance['position'] : esc_html__('', 'text_domain');
			$bio = !empty($instance['bio']) ? $instance['bio'] : esc_html__('💻 ปัจจุบันทำงาน IT Application Support ที่บริษัทแมป มาร์เกตติ้ง ควบคู่กับการทำธุรกิจแอมเวย์เพื่อหาทุนต่อยอดทำพันธกิจคริสเตียนเเละสร้างมูลนิธิช่วยเหลือผู้คน ✝️', 'text_domain');
			$image = !empty($instance['image']) ? $instance['image'] : '';
			$aboyumaQualifiedTitle = !empty($instance['aboyumaQualifiedTitle']) ? $instance['aboyumaQualifiedTitle'] : esc_html__('นักธุรกิจแอมเวย์ รหัส 6672883', 'text_domain');
			$memberRegisterLinkIcon = !empty($instance['memberRegisterLinkIcon']) ? $instance['memberRegisterLinkIcon'] : esc_html__('fa fa-address-card', 'text_domain');
			$memberRegisterLinkUrl = !empty($instance['memberRegisterLinkUrl']) ? $instance['memberRegisterLinkUrl'] : esc_html__('https://th.ashare.me/25ATQlcb', 'text_domain');
			$memberRegisterLink = !empty($instance['memberRegisterLink']) ? $instance['memberRegisterLink'] : esc_html__('สมัครสมาชิกแอมเวย์', 'text_domain');
			$memberRegisterLinkInfoPopoverTitle = !empty($instance['memberRegisterLinkInfoPopoverTitle']) ? $instance['memberRegisterLinkInfoPopoverTitle'] : esc_html__('เกี่ยวกับลิงก์สมัครสมาชิก', 'text_domain');
			$memberRegisterLinkInfoPopoverContent = !empty($instance['memberRegisterLinkInfoPopoverContent']) ? $instance['memberRegisterLinkInfoPopoverContent'] : esc_html__('ลิงก์นี้เป็นลิงก์สมัครสมาชิกแอมเวย์ของ ABO YUMA เมื่อเปิดลิงก์สมัครเเล้วใน url จะระบุว่า referringABO=6672883', 'text_domain');
			?>
			<p>
					<label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'text_domain'); ?></label>
					<input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
			</p>
			<p>
					<label for="<?php echo esc_attr($this->get_field_id('position')); ?>"><?php esc_html_e('Position:', 'text_domain'); ?></label>
					<input class="widefat" id="<?php echo esc_attr($this->get_field_id('position')); ?>" name="<?php echo esc_attr($this->get_field_name('position')); ?>" type="text" value="<?php echo esc_attr($position); ?>">
			</p>
			<p>
					<label for="<?php echo esc_attr($this->get_field_id('bio')); ?>"><?php esc_html_e('Bio:', 'text_domain'); ?></label>
					<textarea class="widefat" id="<?php echo esc_attr($this->get_field_id('bio')); ?>" name="<?php echo esc_attr($this->get_field_name('bio')); ?>"><?php echo esc_attr($bio); ?></textarea>
			</p>
			<p>
					<label for="<?php echo esc_attr($this->get_field_id('image')); ?>"><?php esc_html_e('Image:', 'text_domain'); ?></label>
					<textarea class="widefat" id="<?php echo esc_attr($this->get_field_id('image')); ?>" name="<?php echo esc_attr($this->get_field_name('image')); ?>"><?php echo esc_attr($image); ?></textarea>
					<small>Paste media url</small>
			</p>
			<p>
					<label for="<?php echo esc_attr($this->get_field_id('aboyumaQualifiedTitle')); ?>"><?php esc_html_e('aboyumaQualifiedTitle:', 'text_domain'); ?></label>
					<input class="widefat" id="<?php echo esc_attr($this->get_field_id('aboyumaQualifiedTitle')); ?>" name="<?php echo esc_attr($this->get_field_name('aboyumaQualifiedTitle')); ?>" type="text" value="<?php echo esc_attr($aboyumaQualifiedTitle); ?>">
			</p>
			<p>
					<label for="<?php echo esc_attr($this->get_field_id('memberRegisterLinkIcon')); ?>"><?php esc_html_e('memberRegisterLinkIcon:', 'text_domain'); ?></label>
					<textarea class="widefat" id="<?php echo esc_attr($this->get_field_id('memberRegisterLinkIcon')); ?>" name="<?php echo esc_attr($this->get_field_name('memberRegisterLinkIcon')); ?>"><?php echo esc_attr($memberRegisterLinkIcon); ?></textarea>
					<small>*Use font awesome icon 4.7.0</small>
					<a href="https://fontawesome.com/v4/icons/" target="_blank">Fontawesome icon 4.7.0</a>
			</p>
			<p>
					<label for="<?php echo esc_attr($this->get_field_id('memberRegisterLinkUrl')); ?>"><?php esc_html_e('memberRegisterLinkUrl:', 'text_domain'); ?></label>
					<textarea class="widefat" id="<?php echo esc_attr($this->get_field_id('memberRegisterLinkUrl')); ?>" name="<?php echo esc_attr($this->get_field_name('memberRegisterLinkUrl')); ?>"><?php echo esc_attr($memberRegisterLinkUrl); ?></textarea>
			</p>
			<p>
					<label for="<?php echo esc_attr($this->get_field_id('memberRegisterLink')); ?>"><?php esc_html_e('memberRegisterLink:', 'text_domain'); ?></label>
					<textarea class="widefat" id="<?php echo esc_attr($this->get_field_id('memberRegisterLink')); ?>" name="<?php echo esc_attr($this->get_field_name('memberRegisterLink')); ?>"><?php echo esc_attr($memberRegisterLink); ?></textarea>
			</p>
			<p>
					<label for="<?php echo esc_attr($this->get_field_id('memberRegisterLinkInfoPopoverTitle')); ?>"><?php esc_html_e('memberRegisterLinkInfoPopoverTitle:', 'text_domain'); ?></label>
					<input class="widefat" id="<?php echo esc_attr($this->get_field_id('memberRegisterLinkInfoPopoverTitle')); ?>" name="<?php echo esc_attr($this->get_field_name('memberRegisterLinkInfoPopoverTitle')); ?>" type="text" value="<?php echo esc_attr($memberRegisterLinkInfoPopoverTitle); ?>">
			</p>
			<p>
					<label for="<?php echo esc_attr($this->get_field_id('memberRegisterLinkInfoPopoverContent')); ?>"><?php esc_html_e('memberRegisterLinkInfoPopoverContent:', 'text_domain'); ?></label>
					<textarea class="widefat" id="<?php echo esc_attr($this->get_field_id('memberRegisterLinkInfoPopoverContent')); ?>" name="<?php echo esc_attr($this->get_field_name('memberRegisterLinkInfoPopoverContent')); ?>"><?php echo esc_attr($memberRegisterLinkInfoPopoverContent); ?></textarea>
			</p>
			<?php
	}

	// Update widget settings
	public function update($new_instance, $old_instance) {
			$instance = array();
			$instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
			$instance['position'] = (!empty($new_instance['position'])) ? sanitize_text_field($new_instance['position']) : '';
			$instance['bio'] = (!empty($new_instance['bio'])) ? sanitize_textarea_field($new_instance['bio']) : '';
			$instance['aboyumaQualifiedTitle'] = (!empty($new_instance['aboyumaQualifiedTitle'])) ? sanitize_text_field($new_instance['aboyumaQualifiedTitle']) : '';
			$instance['image'] = (!empty($new_instance['image'])) ? esc_url($new_instance['image']) : '';
			$instance['memberRegisterLinkIcon'] = (!empty($new_instance['memberRegisterLinkIcon'])) ? sanitize_textarea_field($new_instance['memberRegisterLinkIcon']) : '';
			$instance['memberRegisterLinkUrl'] = (!empty($new_instance['memberRegisterLinkUrl'])) ? esc_url($new_instance['memberRegisterLinkUrl']) : '';
			$instance['memberRegisterLink'] = (!empty($new_instance['memberRegisterLink'])) ? sanitize_textarea_field($new_instance['memberRegisterLink']) : '';
			$instance['memberRegisterLinkInfoPopoverTitle'] = (!empty($new_instance['memberRegisterLinkInfoPopoverTitle'])) ? sanitize_textarea_field($new_instance['memberRegisterLinkInfoPopoverTitle']) : '';
			$instance['memberRegisterLinkInfoPopoverContent'] = (!empty($new_instance['memberRegisterLinkInfoPopoverContent'])) ? sanitize_textarea_field($new_instance['memberRegisterLinkInfoPopoverContent']) : '';
			return $instance;
	}
}
function my_theme_register_aboyuma_author_box_widget() {
	register_widget('My_Custom_Author_Widget');
}
add_action('widgets_init', 'my_theme_register_aboyuma_author_box_widget');