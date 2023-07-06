<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
if (!class_exists('GLF_Field_Sortable')) {
	class GLF_Field_Sortable extends GLF_Field
	{
		function field_map() {
			if (!is_array($this->params['options'])) {
				$this->params['options'] = array();
			}
			return join(',', array_keys($this->params['options'])) . ',sort_order';
		}
		/**
		 * Enqueue field resources
		 */
		function enqueue() {
			wp_enqueue_script(GLF_BASE_RESOURCE_PREFIX . 'sortable', GLF_BASE_URL . 'fields/sortable/assets/sortable.js', array(), GLF_VER, true);
		}

		/**
		 * Render field
		 *
		 * @param string $content_args
		 */
		function render_content($content_args = '')
		{
			$field_value = $this->get_value();

			if (!is_array($field_value)) {
				$field_value = array();
			}

			$sort = array();
			if (isset($field_value['sort_order'])) {
				$sort = explode('|', $field_value['sort_order']);
			}

			if (is_array($this->params['options'])) {
				foreach ($this->params['options'] as $key => $value) {
					if (!in_array($key, $sort)) {
						$sort[] = $key;
					}
				}

				foreach ($sort as $key => $value) {
					if (!isset($this->params['options'][$value])) {
						unset($field_value[$key]);
					}
				}
			}

			?>
			<div class="glf-field-sortable-inner glf-clearfix">
				<?php foreach ($sort as $sortValue): ?>
					<div class="glf-field-sortable-item">
						<i class="dashicons dashicons-menu"></i>
						<label>
							<input class="glf-field-sortable-checkbox" type="checkbox"
								   data-field-control=""
								   data-uncheck-novalue="true"
								   name="<?php echo esc_attr($this->get_name()) ?>[<?php echo esc_attr($sortValue) ?>]"
								   value="<?php echo esc_attr($sortValue) ?>"
								<?php echo in_array($sortValue, $field_value) ? 'checked="checked"' : ''; ?>/>
							<span><?php esc_html_e($this->params['options'][$sortValue]); ?></span>
						</label>
					</div>
					<input class="glf-field-sortable-sort" data-field-control="" type="hidden" name="<?php echo esc_attr($this->get_name()) ?>[sort_order]" value="<?php echo join('|', $sort) ?>"/>
				<?php endforeach;?>
			</div>
			<?php
		}

		/**
		 * Get default value
		 *
		 * @return array
		 */
		function get_default() {
			$field_default = isset($this->params['default']) ? $this->params['default'] : array();

			return $this->is_clone() ? array($field_default) : $field_default;
		}
	}
}
