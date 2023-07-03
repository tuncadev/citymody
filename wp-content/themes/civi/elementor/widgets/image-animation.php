<?php

namespace Civi_Elementor;

use Elementor\Group_Control_Box_Shadow;
use Elementor\Utils;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Css_Filter;

defined('ABSPATH') || exit;

class Widget_Image_Animation extends Base
{

	public function get_name()
	{
		return 'civi-image-animation';
	}

	public function get_title()
	{
		return esc_html__('Image Animation', 'civi');
	}

	public function get_icon_part()
	{
		return 'eicon-animation';
	}

	public function get_keywords()
	{
		return ['image', 'photo', 'box'];
	}

	public function get_style_depends()
	{
		return ['civi-el-widget-image-animation'];
	}

	protected function register_controls()
	{
		$this->add_image_animation_section();

		$this->add_image_style_section();
	}

	private function add_image_animation_section()
	{
		$this->start_controls_section('image_section', [
			'label' => esc_html__('Image Animation', 'civi'),
		]);

		$this->add_control('style', [
			'label'        => esc_html__('Style', 'civi'),
			'type'         => Controls_Manager::SELECT,
			'options'      => [
				'jump' => esc_html__('Jump', 'civi'),
				'circular-motion' => esc_html__('Circular Motion', 'civi'),
			],
			'default'      => 'jump',
			'prefix_class' => 'civi-style-',
		]);

		$this->add_control('image', [
			'label'   => esc_html__('Choose Image', 'civi'),
			'type'    => Controls_Manager::MEDIA,
			'dynamic' => [
				'active' => true,
			],
		]);

		$this->add_control('postion', [
			'label'        => esc_html__('Postion', 'civi'),
			'type'         => Controls_Manager::SELECT,
			'options'      => [
				'top' => esc_html__('Top', 'civi'),
				'left' => esc_html__('Left', 'civi'),
			],
			'default'      => 'top',
            'prefix_class' => 'jump-',
		]);

		$this->add_control(
			'image_transform_jump_top',
			[
				'label' => esc_html__('TranslateY', 'civi'),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--civi-animation-jump-top: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'style' => 'jump',
					'postion' => 'top',
				],
			]
		);

		$this->add_control(
			'image_transform_jump_left',
			[
				'label' => esc_html__('TranslateX', 'civi'),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--civi-animation-jump-left: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'style' => 'jump',
					'postion' => 'left',
				],
			]
		);

		$this->add_control(
			'image_transformX_0',
			[
				'label' => esc_html__('TranslateX (0)', 'civi'),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--civi-animation-transformX-0: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'style' => 'circular-motion',
				],
			]
		);


		$this->add_control(
			'image_transformY_0',
			[
				'label' => esc_html__('TranslateY (0)', 'civi'),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--civi-animation-transformY-0: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'style' => 'circular-motion',
				],
			]
		);

		$this->add_control(
			'image_transformX_100',
			[
				'label' => esc_html__('TranslateX (100%)', 'civi'),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--civi-animation-transformX-100: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'style' => 'circular-motion',
				],
			]
		);

		$this->add_control(
			'image_transformY_100',
			[
				'label' => esc_html__('TranslateY (100%)', 'civi'),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--civi-animation-transformY-100: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'style' => 'circular-motion',
				],
			]
		);

		$this->add_control(
			'animation_delay',
			[
				'label' => esc_html__('Animation Delay', 'civi') . ' (ms)',
				'type' => Controls_Manager::NUMBER,
				'default' => '',
				'min' => 0,
				'step' => 100,
				'selectors' => [
					'{{WRAPPER}}' => '--civi-animation-delay: {{SIZE}}ms;',
				],
			]
		);

		$this->end_controls_section();
	}

	private function add_image_style_section()
	{
		$this->start_controls_section('image_style_section', [
			'label' => esc_html__('Image', 'civi'),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

		$this->add_control('image_border_radius', [
			'label'      => esc_html__('Border Radius', 'civi'),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%'],
			'selectors'  => [
				'{{WRAPPER}} .image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);

		$this->add_group_control(Group_Control_Box_Shadow::get_type(), [
			'name'     => 'image_shadow',
			'selector' => '{{WRAPPER}} .image img',
		]);

		$this->add_group_control(Group_Control_Css_Filter::get_type(), [
			'name'     => 'css_filters',
			'selector' => '{{WRAPPER}} .image img',
		]);

		$this->add_control('image_opacity', [
			'label'     => esc_html__('Opacity', 'civi'),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [
				'px' => [
					'max'  => 1,
					'min'  => 0.10,
					'step' => 0.01,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .image img' => 'opacity: {{SIZE}};',
			],
		]);

		$this->end_controls_section();
	}

	protected function render()
	{
		$settings = $this->get_settings_for_display();
		$this->add_render_attribute('wrapper', 'class', array(
			'civi-image-animation',
			'style-' . $settings['style'],
		)); ?>
		<div <?php echo $this->get_render_attribute_string('wrapper'); ?>>
			<?php if (!empty($settings['image']['url'])) : ?>
				<div class="civi-image image">
					<?php echo \Civi_Image::get_elementor_attachment([
						'settings' => $settings,
					]); ?>
				</div>
			<?php endif; ?>
		</div>
<?php
	}
}
