<?php

if ( !defined('ABSPATH') ) {
	exit; // Exit if accessed directly
}

if ( !class_exists('GLF_Field_Divide') ) {
	class GLF_Field_Divide extends GLF_Field
	{
		function render()
		{
			$id = isset($this->params['id']) ? $this->params['id']: '';
			$attr_inner = array();
			if (!empty($id)) {
				$attr_inner[] = sprintf('id="%s"', esc_attr($id));
			}

			$class_inner = array('glf-field-divide-inner');
			if (isset($this->params['style'])) {
				$class_inner[] = 'glf-divide-style-' . $this->params['style'];
			}
			?>
			<div <?php echo join(' ', $attr_inner) ?> class="<?php echo join(' ', $class_inner) ?>" <?php $this->the_required(); ?>>
				<div><span></span></div>
			</div>
			<?php
		}
	}
}