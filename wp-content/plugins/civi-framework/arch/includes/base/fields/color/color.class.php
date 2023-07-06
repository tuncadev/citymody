<?php

if ( !defined('ABSPATH') ) {
	exit; // Exit if accessed directly
}

if ( !class_exists('GLF_Field_Color') ) {
	class GLF_Field_Color extends GLF_Field
	{
		public function enqueue()
		{
			wp_enqueue_style('wp-color-picker');
			wp_enqueue_script('wp-color-picker');
			//wp_enqueue_script('wp_color_picker_alpha', GLF_BASE_URL . 'assets/vendors/wp-color-picker-alpha.js', array(), '1.2', true);
			wp_enqueue_script(GLF_BASE_RESOURCE_PREFIX . 'color', GLF_BASE_URL . 'fields/color/assets/color.js', array(), GLF_VER, true);
		}

		function render_content($content_args = '')
		{
			$field_value = $this->get_value();
			$alpha = isset($this->params['alpha']) ? $this->params['alpha'] : false;
			$validate = array(
				'maxlength' => 7,
				'pattern'   => '^(#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3}))$'
			);
			if ($alpha) {
				$validate = array(
					'maxlength' => 22,
					'pattern'   => '^((#(([a-fA-F0-9]{6})|([a-fA-F0-9]{3})))|(rgba\(\d+,\d+,\d+,\d?(\.\d+)*\)))$'
				);
			}
			?>
			<div class="glf-field-color-inner">
				<input data-field-control=""
				       class="glf-color" type="text"
				       maxlength="<?php echo esc_attr($validate['maxlength']); ?>"
				       pattern="<?php echo esc_attr($validate['pattern']); ?>"
                      <?php if ($alpha): ?>
                          data-alpha="true"
                      <?php endif; ?>
				       name="<?php echo esc_attr($this->get_name()) ?>"
				       value="<?php echo esc_attr($field_value); ?>"/>
			</div>
		<?php
		}
	}
}