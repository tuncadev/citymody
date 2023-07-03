<?php

if ( !defined('ABSPATH') ) {
	exit; // Exit if accessed directly
}

if ( !class_exists('GLF_Field_URL') ) {
	class GLF_Field_URL extends GLF_Field
	{
		function enqueue() {
			wp_enqueue_script(GLF_BASE_RESOURCE_PREFIX . 'url', GLF_BASE_URL . 'fields/url/assets/url.js', array(), GLF_VER, true);
		}
		function render_content($content_args = '')
		{
			$field_value = $this->get_value();

			?>
			<div class="glf-field-url-inner">
				<input  data-field-control="" class="glf-url" type="url"
				       name="<?php echo esc_attr($this->get_name()) ?>"
				       <?php if (!empty($this->params['placeholder'])): ?>
					       placeholder="<?php echo esc_attr($this->params['placeholder']); ?>"
				       <?php endif;?>
				       value="<?php echo esc_attr($field_value); ?>"/>
			</div>
		<?php
		}
	}
}