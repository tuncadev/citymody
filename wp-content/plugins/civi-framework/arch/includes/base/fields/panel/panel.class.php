<?php

if ( !defined('ABSPATH') ) {
	exit; // Exit if accessed directly
}

if ( !class_exists('GLF_Field_Panel') ) {
	class GLF_Field_Panel extends GLF_Field
	{
		function enqueue()
		{
			wp_enqueue_script(GLF_BASE_RESOURCE_PREFIX . 'panel', GLF_BASE_URL . 'fields/panel/assets/panel.js', array(), GLF_VER, true);
		}

		function is_clone() {
			return true;
		}
		function html_start()
		{
			$field_id = $this->get_id();
			?>
			<div id="<?php echo esc_attr($field_id); ?>" class="glf-field-panel glf-field" <?php $this->the_required(); ?>>
				<div class="glf-field-panel-inner">
			<?php
		}
		function html_end() {
			?>
				</div><!-- /.glf-field-panel-inner -->
			</div><!-- /.glf-field-panel -->
			<?php
		}

		function html_content()
		{
			$count = 0;
			if ($this->is_clone()) {
				$count = $count = apply_filters('glf_'. glf_get_config_type() . '_get_panel_count', $count, $this);
			}
			$isToggle = isset($this->params['toggle']) ? $this->params['toggle'] : true;
			?>
			<div class="glf-field-content-wrap <?php echo esc_attr($this->is_sort() ? 'glf-field-panel-sortable' : ''); ?>">
				<?php
				$content_wrap_class = 'glf-field-content-inner glf-field-content-inner-clone';
				if ($this->is_sort()) {
					$content_wrap_class .= ' glf-field-sortable';
				}

				if (!$count) {
					$count = 1;
				}
				?>
				<div class="<?php echo esc_attr($content_wrap_class); ?>">
					<?php for ($i = 0; $i < $count; $i++): ?>
						<div class="glf-field-content glf-clone-field glf-clone-field-panel"
						     data-panel-index="<?php echo esc_attr($i); ?>" data-panel-id="<?php echo esc_attr($this->get_id()); ?>">
							<?php if (!empty($this->params['title'])): ?>
								<h4 class="glf-field-panel-title">
									<span class="glf-panel-title" data-label="<?php echo esc_attr($this->params['title']); ?>"><?php esc_html_e($this->params['title']); ?></span>
									<?php if ($isToggle): ?>
										<?php if ($isToggle && !(isset($this->params['toggle_default']) ? $this->params['toggle_default'] : true)): ?>
                                            <span class="glf-group-toggle dashicons dashicons-arrow-up"></span>
										<?php else: ?>
                                            <span class="glf-group-toggle dashicons dashicons-arrow-down"></span>
										<?php endif;?>
									<?php endif;?>
								</h4>
							<?php endif;?>
							<div class="glf-clone-field-panel-inner"
                                <?php if ($isToggle && !(isset($this->params['toggle_default']) ? $this->params['toggle_default'] : true)): ?>
                                style="display: none"
                                <?php endif; ?>
                                >
								<?php $this->render_content($i); ?>
							</div>
							<?php $this->html_clone_button_remove(); ?>
						</div><!-- /.glf-field-content -->
					<?php endfor; ?>
				</div>
				<?php $this->html_desc(); ?>
				<?php $this->html_clone_button_add(); ?>
			</div><!-- /.glf-field-content-wrap -->
		<?php
		}

		/**
		 * Render content for panel field
		 * *******************************************************
		 */
		function render_content($index = 0)
		{
			if (!isset($this->params['fields']) || !is_array($this->params['fields'])) {
				return;
			}
			$col = isset($this->params['col']) ? $this->params['col'] : 12;
			foreach ($this->params['fields'] as $field) {
				if (!isset($field['type'])) {
					continue;
				}
				$field_cls = glf_get_field_class_name($field['type']);
				$meta = new $field_cls($field, 'panel', $col, $this->get_id());
				$meta->panel_index = $index;
				$meta->panel_default = $this->get_default();
				$meta->render();
			}
		}

		/**
		 * Get default value
		 *
		 * @return array
		 */
		function get_default() {
			$default  = array(array());
			$field_default = isset($this->params['default']) ? $this->params['default'] : $default;
			return $field_default;
		}
	}
}
