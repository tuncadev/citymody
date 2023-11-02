<?php

namespace Civi_Elementor;

use Elementor\Controls_Manager;

defined('ABSPATH') || exit;

class Modify_Widget_Progress extends Modify_Base
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
		add_action('elementor/element/progress/section_progress/before_section_end', [
			$this,
			'before_section_progress_end',
		]);

		add_action('elementor/element/progress/section_progress_style/before_section_end', [
			$this,
			'before_section_progress_style_end',
		]);
	}

	/**
	 * @param \Elementor\Widget_Base $element The edited element.
	 */
	public function before_section_progress_end($element)
	{
		$element->start_injection([
			'type' => 'control',
			'at'   => 'after',
			'of'   => 'display_percentage',
		]);

		$element->add_control('percentage_position', [
			'label'        => esc_html__('Position', 'civi'),
			'type'         => Controls_Manager::SELECT,
			'options'      => [
				'inside'  => __('Inside', 'civi'),
				'outside' => __('Outside', 'civi'),
			],
			'default'      => 'inside',
			'prefix_class' => 'elementor-progress-percentage-',
			'separator'    => 'after',
			'condition'    => [
				'display_percentage' => 'show',
			],
		]);

		$element->end_injection();
	}

	/**
	 * @param \Elementor\Widget_Base $element The edited element.
	 */
	public function before_section_progress_style_end($element)
	{
		$element->add_control('bar_percentage_color', [
			'label'     => __('Percentage Color', 'civi'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .elementor-progress-percentage' => 'color: {{VALUE}};',
			],
		]);
	}
}

Modify_Widget_Progress::instance()->initialize();
