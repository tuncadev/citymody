<?php

if ( !defined('ABSPATH') ) {
	exit; // Exit if accessed directly
}

if ( !class_exists('GLF_Field_Row') ) {
	class GLF_Field_Row extends GLF_Field
	{
		function html_start()
		{
			$field_id = $this->get_id();
			$this->params['clone'] = false;
			?>
			<div <?php echo (!empty($field_id) ? 'id="' . esc_attr($field_id) . '"' : ''); ?> class="glf-row-outer glf-field <?php echo esc_attr($this->get_layout()); ?>" <?php $this->the_required(); ?>>
				<div class="glf-row">
			<?php
		}
		function html_end() {
			?>
				</div><!-- /.glf-row -->
			</div><!-- /.glf-row-outer -->
			<?php
		}

		function render_content($content_args = '')
		{
			if (!isset($this->params['fields']) || !is_array($this->params['fields'])) {
				return;
			}
			$col = isset($this->params['col']) ? $this->params['col'] : 12;
			foreach ($this->params['fields'] as $field) {
				if (!isset($field['type'])) {
					continue;
				}
				if (!empty($this->panel_id) && ($field['type'] === 'panel')) {
					continue;
				}
				$field_cls = glf_get_field_class_name($field['type']);

				$meta = new $field_cls($field, 'row', $col, $this->panel_id, $this->panel_index);
				$meta->panel_default = $this->panel_default;
				$meta->render();
			}
		}
	}
}