<?php

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

if (!class_exists('GLF_Field_Gallery')) {
	class GLF_Field_Gallery extends GLF_Field
	{
		function enqueue()
		{
			wp_enqueue_script(GLF_BASE_RESOURCE_PREFIX . 'gallery', GLF_BASE_URL . 'assets/js/gallery.js', array(), GLF_VER, true);
			wp_enqueue_script(GLF_BASE_RESOURCE_PREFIX . 'field-gallery', GLF_BASE_URL . 'fields/gallery/assets/gallery.js', array(), GLF_VER, true);
		}
		function render_content($content_args = '')
		{
			$field_value = $this->get_value();
			$field_value_arr = explode('|', $field_value);
?>
			<div class="glf-field-gallery-inner">
				<input data-field-control="" type="hidden" name="<?php echo esc_attr($this->get_name()) ?>" value="<?php echo esc_attr($field_value); ?>" />
				<?php foreach ($field_value_arr as $image_id) : ?>
					<?php
					if (empty($image_id)) {
						continue;
					}
					$image_url = '';
					$image_attributes = wp_get_attachment_image_src($image_id);
					if (!empty($image_attributes) && is_array($image_attributes)) {
						$image_url = $image_attributes[0];
					}
					?>
					<div class="glf-image-preview" data-id="<?php echo esc_attr($image_id); ?>">
						<div class="centered">
							<img src="<?php echo esc_url($image_url); ?>" />
						</div>
						<span class="glf-gallery-remove dashicons dashicons dashicons-no-alt"></span>
					</div>
				<?php endforeach; ?>
				<div class="glf-gallery-add">
					<?php esc_html_e('+ Add Images', 'civi-framework'); ?>
				</div>
			</div>
<?php
		}
	}
}
