<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
if (!class_exists('GLF_Field_Info')) {
	class GLF_Field_Info extends GLF_Field
	{
		function render()
		{
			$desc = isset($this->params['desc']) ? $this->params['desc']: '';
			$title = isset($this->params['title']) ? $this->params['title']: '';
			$class_inner = array('glf-info-inner');
			if (isset($this->params['style'])) {
				$class_inner[] = 'glf-info-style-' . $this->params['style'];
			}

			$icon = isset($this->params['icon']) ? $this->params['icon'] : '';
			if ($icon === true) {
				if (isset($this->params['style'])) {
					switch ($this->params['style']) {
						case 'info':
							$icon = 'dashicons-info';
							break;
						case 'warning':
							$icon = 'dashicons-shield-alt';
							break;
						case 'success':
							$icon = 'dashicons-yes';
							break;
						case 'error':
							$icon = 'dashicons-dismiss';
							break;
					}
				}
				else {
					$icon = 'dashicons-wordpress';
				}
			}

			if (isset($this->params['icon'])) {
				$class_inner[] = 'glf-info-has-icon glf-clearfix';
			}

			?>
			<div id="<?php echo esc_attr($this->get_id()); ?>" class="glf-field glf-field-info" <?php $this->the_required(); ?>>
				<div class="<?php echo join(' ', $class_inner) ?>">
					<div class="glf-info-content">
						<?php if (isset($this->params['icon'])): ?>
							<span class="glf-info-content-icon dashicons <?php echo esc_attr($icon); ?>"></span>
						<?php endif;?>

						<?php if (!empty($title)): ?>
							<div class="glf-info-content-title">
								<?php echo wp_kses_post($title); ?>
							</div>
						<?php endif;?>
					
						<?php if (!empty($desc)): ?>
						<div class="glf-info-content-desc">
							<?php echo wp_kses_post($desc); ?>
						</div>
						<?php endif;?>
					</div>
				</div>
			</div>
			<?php
		}
	}
}