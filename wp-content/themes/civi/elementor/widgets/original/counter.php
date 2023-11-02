<?php

namespace Civi_Elementor;

use Elementor\Controls_Manager;

defined('ABSPATH') || exit;

class Modify_Widget_Counter extends Modify_Base
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
		add_action('elementor/element/counter/section_title/before_section_end', [
			$this,
			'before_section_title_end',
		]);

		add_action('elementor/element/counter/section_number/before_section_end', [
			$this,
			'before_section_number_end',
		]);
	}

	/**
	 * @param \Elementor\Widget_Base $element The edited element.
	 */

	public function before_section_number_end($element)
	{
		$element->add_control('content_text_align', [
			'label'        => esc_html__('Text Align', 'civi'),
			'label_block'  => false,
			'type'         => Controls_Manager::CHOOSE,
			'default'      => 'center',
			'options'      => array(
				'flex-start'   => [
					'title' => esc_html__('Left', 'civi'),
					'icon'  => 'eicon-text-align-left',
				],
				'center' => [
					'title' => esc_html__('Center', 'civi'),
					'icon'  => 'eicon-text-align-center',
				],
				'flex-end'  => [
					'title' => esc_html__('Right', 'civi'),
					'icon'  => 'eicon-text-align-right',
				],
			),
			'selectors'    => [
				'{{WRAPPER}} .elementor-counter' => 'display:flex;flex-direction:column;align-items: {{VALUE}};',
			],
		]);
	}

	public function before_section_title_end($element)
	{
		$element->add_responsive_control('title_margin', [
			'label'      => esc_html__('Margin', 'civi'),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%', 'em'],
			'selectors'  => [
				'{{WRAPPER}} .elementor-counter .elementor-counter-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);
	}
}

Modify_Widget_Counter::instance()->initialize();
