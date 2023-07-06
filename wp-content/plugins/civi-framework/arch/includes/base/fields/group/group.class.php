<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
if (!class_exists('GLF_Field_Group')) {
	class GLF_Field_Group extends GLF_Field
	{
		function html_start()
		{
			$field_id = $this->get_id();
			$this->params['clone'] = false;
			$isToggle = isset($this->params['toggle']) ? $this->params['toggle'] : true;
			?>
			<div <?php echo (!empty($field_id) ? 'id="' . esc_attr($field_id) . '"' : ''); ?> class="glf-group glf-field" <?php $this->the_required(); ?>>
				<h4>
					<?php esc_html_e($this->params['title']); ?>
					<?php if ($isToggle): ?>
						<?php if ($isToggle && !(isset($this->params['toggle_default']) ? $this->params['toggle_default'] : true)): ?>
                            <span class="glf-group-toggle dashicons dashicons-arrow-up"></span>
						<?php else: ?>
                            <span class="glf-group-toggle dashicons dashicons-arrow-down"></span>
						<?php endif;?>
					<?php endif;?>
				</h4>
				<div class="glf-group-inner"
                <?php if ($isToggle && !(isset($this->params['toggle_default']) ? $this->params['toggle_default'] : true)): ?>
                 style="display: none"
                <?php endif; ?>
                >
			<?php
		}
		function html_end() {
			?>
				</div><!-- /.glf-group-inner -->
			</div><!-- /.glf-group -->
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
				$meta = new $field_cls($field, 'group', $col, $this->panel_id, $this->panel_index);
				$meta->panel_default = $this->panel_default;
				$meta->render();
			}
		}
	}
}
