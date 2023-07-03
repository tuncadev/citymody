<?php

if ( !defined('ABSPATH') ) {
	exit; // Exit if accessed directly
}

if ( !class_exists('GLF_Field_Textarea') ) {
	class GLF_Field_Textarea extends GLF_Field
	{
		function enqueue()
		{
			wp_enqueue_script(GLF_BASE_RESOURCE_PREFIX . 'textarea', GLF_BASE_URL . 'fields/textarea/assets/textarea.js', array(), GLF_VER, true);
		}

		function render_content($content_args = '')
		{

			$field_value = $this->get_value();
			?>
			<div class="glf-field-textarea-inner">
					<textarea data-field-control="" name="<?php echo esc_attr($this->get_name()) ?>" class="glf-textarea"
			      	<?php if (isset($this->params['args']) && isset($this->params['args']['col'])): ?>
			            cols="<?php echo esc_attr($this->params['args']['col']); ?>"
				  	<?php endif;?>
		          	rows="<?php echo ((isset($this->params['args']) && isset($this->params['args']['row'])) ? esc_attr($this->params['args']['row']) : '5'); ?>"><?php echo esc_textarea($field_value); ?></textarea>
			</div>
			<?php
		}
	}
}