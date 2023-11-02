<?php

namespace Civi_Elementor;

use Elementor\Controls_Manager;

defined('ABSPATH') || exit;

class Modify_Widget_Column extends Modify_Base
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
		add_action('elementor/element/column/layout/before_section_end', [
			$this,
			'add_column_order_control',
		]);

		add_action('elementor/element/column/section_advanced/after_section_end', [
			$this,
			'update_padding_option_selectors',
		]);
	}

	/**
	 * Adding column order control to layout section.
	 *
	 * @param \Elementor\Widget_Base $element The edited element.
	 */
	public function add_column_order_control($element)
	{
		$element->add_responsive_control('order', [
			'label'     => esc_html__('Column Order', 'civi'),
			'type'      => Controls_Manager::NUMBER,
			'min'       => 1,
			'max'       => 12,
			'step'      => 1,
			'selectors' => [
				'{{WRAPPER}}' => 'order: {{VALUE}};',
			],
		]);
	}

	/**
	 * Update padding option selectors.
	 *
	 * @param \Elementor\Widget_Base $element The edited element.
	 */
	public function update_padding_option_selectors($element)
	{
		$element->update_responsive_control('padding', [
			'selectors' => [
				// Make stronger selector for compatible with theme.
				'{{WRAPPER}} > .elementor-element-populated.elementor-element-populated' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);
	}
}

Modify_Widget_Column::instance()->initialize();
