<?php

if ( !defined('ABSPATH') ) {
	exit; // Exit if accessed directly
}

if ( !class_exists('GLF_Field_Select') ) {
	class GLF_Field_Select extends GLF_Field
	{
		function enqueue() {
			wp_enqueue_script(GLF_BASE_RESOURCE_PREFIX . 'select', GLF_BASE_URL . 'fields/select/assets/select.js', array(), GLF_VER, true);
		}
		function render_content($content_args = '')
		{
			if (isset($this->params['data'])) {
				switch ($this->params['data']) {
					case 'sidebar':
						$this->params['options'] = glf_get_sidebars();
						break;
					case 'menu':
						$this->params['options'] = glf_get_menus();
						break;
					case 'taxonomy':
						$this->params['options'] = glf_get_taxonomies(isset($this->params['data_args']) ? $this->params['data_args'] : array());
						break;
					default:
						if (isset($this->params['data_args']) && !isset($this->params['data_args']['post_type'])) {
							$this->params['data_args']['post_type'] = $this->params['data'];
						}
						$this->params['options'] = glf_get_posts(isset($this->params['data_args']) ? $this->params['data_args'] : array('post_type' => $this->params['data']));
						break;
				}
			}

			if (!isset($this->params['options']) || !is_array($this->params['options'])) {
				return;
			}
			$field_value = $this->get_value();
			$multiple = isset($this->params['multiple']) ? $this->params['multiple'] : false;
			?>
			<div class="glf-field-select-inner">
				<select data-field-control="" class="glf-select"
                        <?php if ($multiple): ?>
                            name="<?php echo esc_attr($this->get_name()) ?>[]"
                            multiple="multiple"
                        <?php else: ?>
                            name="<?php echo esc_attr($this->get_name()) ?>"
                        <?php endif; ?>
                    >
					<?php foreach ($this->params['options'] as $key => $value): ?>
						<?php if (is_array($value)): ?>
							<optgroup label="<?php echo esc_attr($key); ?>">
								<?php foreach ($value as $opt_key => $opt_value): ?>
									<option <?php glf_the_selected($opt_key, $field_value) ?>
										value="<?php echo esc_attr($opt_key); ?>"><?php esc_html_e($opt_value); ?></option>
								<?php endforeach; ?>
							</optgroup>
						<?php else:; ?>
							<option value="<?php echo esc_attr($key); ?>" <?php glf_the_selected($key, $field_value) ?>><?php esc_html_e($value); ?></option>
						<?php endif; ?>
					<?php endforeach; ?>
				</select>
			</div>
		<?php
		}

		/**
		 * Get default value
		 *
		 * @return array | string
		 */
		function get_default() {
			$default = '';
			if (isset($this->params['multiple']) && $this->params['multiple']) {
				$default = array();
			}
			$field_default = isset($this->params['default']) ? $this->params['default'] : $default;
			return $this->is_clone() ? array($field_default) : $field_default;
		}
	}
}
