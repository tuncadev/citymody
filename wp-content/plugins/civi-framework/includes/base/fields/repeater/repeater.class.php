<?php

if ( !defined('ABSPATH') ) {
	exit; // Exit if accessed directly
}

if ( !class_exists('GLF_Field_Repeater') ) {
	class GLF_Field_Repeater extends GLF_Field
	{
		function html_start()
		{
			$wrapper_class = 'glf-field glf-repeater';
			if (isset($this->params['sort']) && $this->params['sort']) {
				$wrapper_class .= ' glf-repeater-sort';
			}
			$wrapper_class .= ' ' . esc_attr($this->get_layout());
			$field_id = $this->get_id();
			?>
			<div <?php echo (!empty($field_id) ? 'id="' . $field_id . '"' : ''); ?> class="<?php echo esc_attr($wrapper_class); ?>" <?php $this->the_required(); ?>>
				<?php $this->html_label(); ?>
			<?php
		}
		function html_end() {
			?>
			</div><!-- /.glf-repeater -->
			<?php
		}

		function html_repeater_desc() {
			$has_footer = false;
			foreach ($this->params['fields'] as $field) {
				if (isset($field['desc']) && !empty($field['desc'])) {
					$has_footer = true;
					break;
				}
			}
			if (!$has_footer) {
				return;
			}
			?>
			<div class="glf-row glf-repeater-footer">
				<?php foreach ($this->params['fields'] as $field): ?>
					<?php
					$col = isset($this->params['col']) ? $this->params['col'] : 12;
					$col = isset($field['col']) ? $field['col'] : $col;
					?>
					<div class="glf-col glf-col-<?php echo esc_attr($col); ?>">
						<p class="glf-desc"><?php echo (isset($field['desc']) ? $field['desc'] : ''); ?></p>
					</div>
				<?php endforeach;?>
			</div><!-- /.glf-row -->
			<?php
		}

		function html_repeater_label() {
			?>
			<div class="glf-row glf-repeater-header">
				<?php foreach ($this->params['fields'] as $field): ?>
					<?php
						$col = isset($this->params['col']) ? $this->params['col'] : 12;
						$col = isset($field['col']) ? $field['col'] : $col;
					?>
					<div class="glf-col glf-col-<?php echo esc_attr($col); ?>">
						<label class="glf-label"><?php echo (isset($field['title']) ? $field['title'] : ''); ?></label>
					</div>
				<?php endforeach;?>
			</div><!-- /.glf-row -->
			<?php
		}

		function html_content() {
			$count = $this->get_value();
			
			if (empty($count)) {
				$count = 0;
			}
			?>
				<div class="glf-field-content-wrap">
					<?php $this->html_repeater_label(); ?>
					<?php
					if (!$count) {
						$count = 1;
					}
					$content_wrap_class = 'glf-field-content-inner glf-field-content-inner-clone';
					if (isset($this->params['sort']) && $this->params['sort']) {
						$content_wrap_class .= ' glf-field-sortable';
					}
					?>
					<div class="<?php echo esc_attr($content_wrap_class); ?>">
						<input type="hidden" name="<?php echo esc_attr($this->get_name()); ?>" value="<?php echo esc_attr($count); ?>"/>
						<?php for ($i = 0; $i < $count; $i++): ?>
							<div class="glf-row glf-field-content glf-field-content-repeater glf-clone-field">
								<?php if (isset($this->params['sort']) && $this->params['sort']): ?>
									<?php $this->html_clone_button_sort(); ?>
								<?php endif;?>
								<?php $this->render_content($i); ?>
								<?php $this->html_clone_button_remove(); ?>
							</div><!-- /.glf-field-content -->
						<?php endfor;?>
					</div>
					<?php $this->html_repeater_desc(); ?>
					<?php $this->html_clone_button_add(); ?>
				</div><!-- /.glf-field-content-wrap -->
			<?php
		}

		function render_content($index = 0)
		{
			if (!isset($this->params['fields']) || !is_array($this->params['fields'])) {
				return;
			}
			foreach ($this->params['fields'] as $field) {
				if (!isset($field['type']) || in_array($field['type'], array('row', 'group', 'repeater', 'panel')) ) {
					continue;
				}
				$col = isset($this->params['col']) ? $this->params['col'] : 12;
				$col = isset($field['col']) ? $field['col'] : $col;

				$field_cls = glf_get_field_class_name($field['type']);
				$meta = new $field_cls($field, 'repeater', $col, $this->panel_id, $this->panel_index);
				$meta->index = $index;
				$meta->panel_default = $this->panel_default;
				$meta->render();
			}
		}
		/**
		 * Get default value
		 *
		 * @return int
		 */
		function get_default() {
			return 0;
		}
	}
}