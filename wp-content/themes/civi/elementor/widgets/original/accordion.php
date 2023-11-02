<?php

namespace Civi_Elementor;

use Elementor\Controls_Manager;

defined('ABSPATH') || exit;

class Modify_Widget_Accordion extends Modify_Base
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
		add_action('elementor/element/accordion/section_title/after_section_end', [
			$this,
			'update_default_icon',
		]);

		add_action('elementor/element/accordion/section_title_style/after_section_start', [
			$this,
			'section_title_style',
		]);

		add_action('elementor/element/accordion/section_toggle_style_title/before_section_end', [
			$this,
			'add_options_title_style_section',
		]);
	}

	/**
	 * Update default icon & active icon.
	 * From FA solid to regular
	 *
	 * @param \Elementor\Widget_Base $element The edited element.
	 */
	public function update_default_icon($element)
	{
		$element->update_control('selected_icon', [
			'default' => [
				'value'   => 'fal fa-plus',
				'library' => 'fa-regular',
			],
		]);

		$element->update_control('selected_active_icon', [
			'default' => [
				'value'   => 'fal fa-minus',
				'library' => 'fa-regular',
			],
		]);
	}

	/**
	 * @param \Elementor\Widget_Base $element The edited element.
	 */
	public function section_title_style($element)
	{
		$element->add_control('style', [
			'label'        => esc_html__('Style', 'civi'),
			'type'         => Controls_Manager::SELECT,
			'default'      => '01',
			'options'      => [
				'01' => '01',
				'02' => '02',
				'03' => '03',
			],
			'prefix_class' => 'elementor-accordion-style-',
		]);

		$element->add_control('spacing_between', [
			'label'     => esc_html__('Spacing Between', 'civi'),
			'type'      => Controls_Manager::SLIDER,
			'default'   => array(
				'size' => 0,
				'unit' => 'px',
			),
			'range'     => [
				'px' => [
					'min' => 0,
					'max' => 100,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .elementor-accordion .elementor-accordion-item + .elementor-accordion-item' => 'margin-top: {{SIZE}}{{UNIT}};',
			],
		]);
	}

	/**
	 * @param \Elementor\Widget_Base $element The edited element.
	 */
	public function add_options_title_style_section($element)
	{
		// Update title active color selectors.
		$element->update_control('tab_active_color', [
			'selectors' => [
				// Original.
				'{{WRAPPER}} .elementor-accordion .elementor-tab-title.elementor-active' => 'color: {{VALUE}};',
				// Added.
				'{{WRAPPER}} .elementor-accordion .elementor-tab-title:hover'            => 'color: {{VALUE}};',
				'{{WRAPPER}} .elementor-accordion .elementor-tab-title:hover a'          => 'color: {{VALUE}};',
			],
		]);

		$element->add_control('title_border_width', [
			'label'     => esc_html__('Border Width', 'civi'),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [
				'px' => [
					'min' => 0,
					'max' => 10,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .elementor-accordion .elementor-accordion-item .elementor-tab-title' => 'border-bottom-width: {{SIZE}}{{UNIT}};',
			],
			'separator' => 'before',
		]);

		$element->add_control('title_border_color', [
			'label'     => esc_html__('Border Color', 'civi'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .elementor-accordion .elementor-accordion-item .elementor-tab-title' => 'border-bottom-color: {{VALUE}};',
			],
		]);
	}
}

Modify_Widget_Accordion::instance()->initialize();
