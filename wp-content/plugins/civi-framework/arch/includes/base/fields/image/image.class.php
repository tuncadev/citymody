<?php

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

if (!class_exists('GLF_Field_Image')) {
	class GLF_Field_Image extends GLF_Field
	{
		function field_map()
		{
			return 'id,url';
		}
		function enqueue()
		{
			wp_enqueue_script(GLF_BASE_RESOURCE_PREFIX . 'image', GLF_BASE_URL . 'fields/image/assets/image.js', array(), GLF_VER, true);
		}

		function render_content($content_args = '')
		{
			$field_value = $this->get_value();
			if (!is_array($field_value)) {
				$field_value = array();
			}
			$default = array(
				'id'  => 0,
				'url' => ''
			);

			if (isset($this->params['default'])) {
				if (is_numeric($this->params['default'])) {
					$default['id'] = $this->params['default'];
					$default['url'] = wp_get_attachment_url($default['id']);
				} else {
					$default['url'] = $this->params['default'];
					$default['id'] = glf_get_attachment_id($default['url']);
				}
			}
			$field_value = wp_parse_args($field_value, $default);

			$thumb_url = $field_value['url'];
			$image_attributes = wp_get_attachment_image_src($field_value['id']);
			if (!empty($image_attributes) && is_array($image_attributes)) {
				$thumb_url = $image_attributes[0];
			}
?>
			<div class="glf-field-image-inner glf-clearfix">
				<input data-field-control="" type="hidden" class="glf-image-id" name="<?php echo esc_attr($this->get_name()) ?>[id]" value="<?php echo esc_attr($field_value['id']); ?>" />
				<div class="glf-image-preview">
					<div class="centered">
						<img src="<?php echo esc_url($thumb_url); ?>" style="<?php echo esc_attr(empty($thumb_url) ? 'display:none' : '') ?>" />
					</div>
				</div>
				<div class="glf-image-info">
					<input data-field-control="" type="text" class="glf-image-url" placeholder="<?php esc_attr_e('No image', 'civi-framework'); ?>" name="<?php echo esc_attr($this->get_name()) ?>[url]" value="<?php echo esc_url($field_value['url']); ?>" />
					<button type="button" class="button glf-image-choose-image"><?php esc_html_e('Choose Image', 'civi-framework'); ?></button>
					<button type="button" class="button glf-image-remove"><?php esc_html_e('Remove', 'civi-framework'); ?></button>
					<?php if (isset($this->params['images_select_text']) && !empty($this->params['images_select_text'])) : ?>
						<button type="button" class="button glf-image-choose-image-dir"><?php esc_html_e($this->params['images_select_text']); ?></button>
					<?php endif; ?>
				</div>
			</div>
<?php
		}

		/**
		 * Get default value
		 *
		 * @return array
		 */
		function get_default()
		{
			$default = array(
				'id'  => 0,
				'url' => ''
			);

			if (isset($this->params['default'])) {
				if (is_numeric($this->params['default'])) {
					$default['id'] = $this->params['default'];
					$default['url'] = wp_get_attachment_url($default['id']);
				} else {
					$default['url'] = $this->params['default'];
					$default['id'] = glf_get_attachment_id($default['url']);
				}
			}

			return $this->is_clone() ? array($default) : $default;
		}
	}
}
