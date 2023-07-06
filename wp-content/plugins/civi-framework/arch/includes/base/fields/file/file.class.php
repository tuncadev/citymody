<?php

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

if (!class_exists('GLF_Field_File')) {
	class GLF_Field_File extends GLF_Field
	{
		function enqueue()
		{
			wp_enqueue_script(GLF_BASE_RESOURCE_PREFIX . 'file', GLF_BASE_URL . 'fields/file/assets/file.js', array(), GLF_VER, true);
			wp_localize_script(GLF_BASE_RESOURCE_PREFIX . 'file', 'sfFileFieldMeta', array(
				'title'   => esc_html__('Select File', 'civi-framework'),
				'button'  => esc_html__('Use these files', 'civi-framework')
			));
		}

		/*
		 * Render field content
		 */
		function render_content($content_args = '')
		{
			$field_value = $this->get_value();
			$field_value_arr = explode('|', $field_value);
			$remove_text = esc_html__('Remove', 'civi-framework');
?>
			<div class="glf-field-file-inner" data-remove-text="<?php echo esc_attr($remove_text); ?>" <?php if (isset($this->params['lib_filter']) && !empty($this->params['lib_filter'])) : ?> data-lib-filter="<?php echo esc_attr($this->params['lib_filter']) ?>" <?php endif; ?>>
				<input data-field-control="" type="hidden" name="<?php echo esc_attr($this->get_name()) ?>" value="<?php echo esc_attr($field_value); ?>" />
				<?php foreach ($field_value_arr as $file_id) : ?>
					<?php
					if (empty($file_id)) {
						continue;
					}
					$file_meta = get_post($file_id);
					if ($file_meta == null) {
						continue;
					}
					?>
					<div class="glf-file-item" data-file-id="<?php echo esc_attr($file_id); ?>">
						<span class="dashicons dashicons-media-document"></span>
						<div class="glf-file-info">
							<a class="glf-file-title" href="<?php echo esc_url(get_edit_post_link($file_id)); ?>" target="_blank"><?php esc_html_e($file_meta->post_title); ?></a>
							<div class="glf-file-name"><?php esc_html_e(wp_basename($file_meta->guid)); ?></div>
							<div class="glf-file-action">
								<span class="glf-file-remove"><span class="dashicons dashicons-no-alt"></span> <?php esc_html_e($remove_text) ?></span>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
				<div class="glf-file-add">
					<button class="button" type="button"><?php esc_html_e('+ Add File', 'civi-framework'); ?></button>
				</div>
			</div>
<?php
		}
	}
}
