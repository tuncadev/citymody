<?php

if ( !defined('ABSPATH') ) {
	exit; // Exit if accessed directly
}

if ( !class_exists('GLF_Field_Editor') ) {
	class GLF_Field_Editor extends GLF_Field
	{
		function enqueue()
		{
			wp_enqueue_script(GLF_BASE_RESOURCE_PREFIX . 'editor', GLF_BASE_URL . 'fields/editor/assets/editor.js', array(), GLF_VER, true);
		}
		function render_content($content_args = '')
		{
			
			$field_value = $this->get_value();
			/**
			 * Setup up default args
			 */
			$defaults = array(
				'textarea_name' => $this->get_name(),
				'editor_class'  => isset($this->params['class']) ? $this->params['class'] : '',
				'textarea_rows' => 10, //Wordpress default
			);
			$this->params['args'] = isset($this->params['args']) ? $this->params['args'] : array();

			$args = wp_parse_args( $this->params['args'], $defaults );
			$editor_id = $this->get_name() . '__editor';
			$editor_id = str_replace('[', '__',$editor_id);
			$editor_id = str_replace(']', '__',$editor_id);
			?>
			<div class="glf-field-editor-inner">
				<?php wp_editor( $field_value, $editor_id, $args ); ?>
			</div>
		<?php
		}
	}
}