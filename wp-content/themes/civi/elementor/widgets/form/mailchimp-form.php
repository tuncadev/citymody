<?php

namespace Civi_Elementor;

use Elementor\Controls_Manager;

defined('ABSPATH') || exit;

class Widget_Mailchimp_Form extends Form_Base
{

	public function get_name()
	{
		return 'civi-mailchimp-form';
	}

	public function get_title()
	{
		return esc_html__('Mailchimp Form', 'civi');
	}

	public function get_keywords()
	{
		return ['mailchimp', 'form', 'subscribe'];
	}

    public function get_style_depends()
    {
        return ['civi-el-widget-mailchimp-form'];
    }

	protected function register_controls()
	{
		$this->add_content_section();

		$this->add_field_style_section();

		$this->add_button_style_section();
	}

	private function add_content_section()
	{
		$this->start_controls_section('content_section', [
			'label' => esc_html__('Layout', 'civi'),
		]);

		$this->add_responsive_control(
			'align',
			[
				'label' => esc_html__('Alignment', 'civi'),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left'    => [
						'title' => esc_html__('Left', 'civi'),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'civi'),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__('Right', 'civi'),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .mc4wp-form-fields' => 'justify-content:{{VALUE}}; -webkit-box-pack:{{VALUE}};-ms-flex-pack:{{VALUE}};',
				],
			]
		);

		$this->add_control('form_id', [
			'label'       => esc_html__('Form Id', 'civi'),
			'description' => esc_html__('Input the id of form. Leave blank to show default form.', 'civi'),
			'type'        => Controls_Manager::TEXT,
		]);

		$this->add_control('style', [
			'label'        => esc_html__('Style', 'civi'),
			'type'         => Controls_Manager::SELECT,
			'options'      => [
				'01' => '01',
				'02' => '02',
			],
			'default'      => '01',
			'prefix_class' => 'civi-mailchimp-form-style-',
		]);

		$this->end_controls_section();
	}

	protected function render()
	{
		$settings = $this->get_settings_for_display();
		$form_id  = !empty($settings['form_id']) ? $settings['form_id'] : '';


		if ('' === $form_id && function_exists('mc4wp_get_forms')) {
			$mc_forms = mc4wp_get_forms();
			if (count($mc_forms) > 0) {
				$form_id = $mc_forms[0]->ID;
			}
		}

		$this->add_render_attribute('box', 'class', 'civi-mailchimp-form');
?>
		<?php if (function_exists('mc4wp_show_form') && $form_id !== '') : ?>
			<div <?php $this->print_render_attribute_string('box') ?>>
				<?php mc4wp_show_form($form_id); ?>
			</div>
		<?php endif; ?>
<?php
	}
}
