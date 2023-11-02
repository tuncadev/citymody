<?php

namespace Civi_Elementor;

use Elementor\Controls_Manager;

defined('ABSPATH') || exit;

class Modify_Widget_Animated_Headline extends Modify_Base
{

	private static $_instance = null;

	public static function instance()
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public function initialize()
	{
		add_action('elementor/element/animated-headline/section_style_text/before_section_end', [
			$this,
			'before_end_section_style_text',
		]);

		add_action('elementor/element/animated-headline/section_style_text/after_section_end', [
			$this,
			'add_dimension_section',
		]);
	}

	/**
	 * @param \Elementor\Widget_Base $element The edited element.
	 */
	public function add_dimension_section($element)
	{
		$element->start_controls_section('heading_dimension_section', [
			'tab'   => Controls_Manager::TAB_STYLE,
			'label' => esc_html__('Dimension', 'civi'),
		]);

		$element->add_responsive_control('heading_max_width', [
			'label'          => esc_html__('Max Width', 'civi'),
			'type'           => Controls_Manager::SLIDER,
			'default'        => [
				'unit' => 'px',
			],
			'tablet_default' => [
				'unit' => 'px',
			],
			'mobile_default' => [
				'unit' => 'px',
			],
			'size_units'     => ['px', '%'],
			'range'          => [
				'%'  => [
					'min' => 1,
					'max' => 100,
				],
				'px' => [
					'min' => 1,
					'max' => 1600,
				],
			],
			'selectors'      => [
				'{{WRAPPER}} .elementor-headline' => 'max-width: {{SIZE}}{{UNIT}};',
			],
		]);

		$element->add_responsive_control('heading_alignment', [
			'label'                => esc_html__('Alignment', 'civi'),
			'label_block'          => false,
			'type'                 => Controls_Manager::CHOOSE,
			'options'              => Widget_Utils::get_control_options_horizontal_alignment(),
			'selectors_dictionary' => [
				'left'  => 'flex-start',
				'right' => 'flex-end',
			],
			'selectors'            => [
				'{{WRAPPER}} .elementor-widget-container' => 'display: flex; justify-content: {{VALUE}}',
			],
		]);

		$element->end_controls_section();
	}

	/**
	 * @param \Elementor\Widget_Base $element The edited element.
	 */
	public function before_end_section_style_text($element)
	{
		$element->start_injection([
			'type' => 'control',
			'at'   => 'after',
			'of'   => 'heading_words_style',
		]);

		$element->add_responsive_control('words_before_spacing', [
			'label'     => esc_html__('Before Spacing', 'civi'),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [
				'px' => [
					'min' => 0,
					'max' => 200,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .elementor-headline-dynamic-wrapper' => 'margin-left: {{SIZE}}{{UNIT}};',
			],
		]);

		$element->add_responsive_control('words_after_spacing', [
			'label'     => esc_html__('After Spacing', 'civi'),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [
				'px' => [
					'min' => 0,
					'max' => 200,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .elementor-headline-dynamic-wrapper' => 'margin-right: {{SIZE}}{{UNIT}};',
			],
		]);

		$element->add_control('line_color_color', [
			'label'     => esc_html__('Line Color', 'civi'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .elementor-headline-animation-type-clip .elementor-headline-dynamic-wrapper::after ' => 'background-color: {{VALUE}};',
			],
			'condition' => [
				'headline_style' => 'rotate',
				'animation_type' => 'clip',
			],
		]);

		$element->end_injection();
	}
}

Modify_Widget_Animated_Headline::instance()->initialize();
