<?php

namespace Civi_Elementor;

use Elementor\Group_Control_Base;
use Elementor\Controls_Manager;

defined('ABSPATH') || exit;

/**
 * Elementor advanced border control.
 *
 * A base control for creating border control. Displays input fields to define
 * border type, border width and border color.
 *
 * @since 1.0.0
 */
class Group_Control_Text_Stroke extends Group_Control_Base
{

	/**
	 * Fields.
	 *
	 * Holds all the border control fields.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @static
	 *
	 * @var array Border control fields.
	 */
	protected static $fields;

	/**
	 * Get border control type.
	 *
	 * Retrieve the control type, in this case `advanced-border`.
	 *
	 * @since  1.0.0
	 * @access public
	 * @static
	 *
	 * @return string Control type.
	 */
	public static function get_type()
	{
		return 'text-stroke';
	}

	/**
	 * Init fields.
	 *
	 * Initialize border control fields.
	 *
	 * @since  1.2.2
	 * @access protected
	 *
	 * @return array Control fields.
	 */
	protected function init_fields()
	{
		$fields = [];

		$fields['color'] = [
			'label'     => esc_html__('Color', 'civi'),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				'{{SELECTOR}}' => '-webkit-text-stroke-color: {{VALUE}};',
			],
		];

		$fields['width'] = [
			'label'      => esc_html__('Width', 'civi'),
			'type'       => Controls_Manager::SLIDER,
			'range'      => [
				'px' => [
					'min'  => 1,
					'max'  => 30,
					'step' => 1,
				],
			],
			'selectors'  => [
				'{{SELECTOR}}' => '-webkit-text-fill-color: rgba(0, 0, 0, 0);-webkit-text-stroke-width: {{SIZE}}{{UNIT}};',
			],
			'responsive' => true,
		];

		return $fields;
	}

	/**
	 * Get default options.
	 *
	 * Retrieve the default options of the border control. Used to return the
	 * default options while initializing the border control.
	 *
	 * @since  1.9.0
	 * @access protected
	 *
	 * @return array Default border control options.
	 */
	protected function get_default_options()
	{
		return [
			'popover' => [
				'starter_title' => _x('Text Stroke', 'Text Stroke Control', 'civi'),
				'starter_name'  => 'enable',
				'starter_value' => 'yes',
				'settings'      => [
					'render_type' => 'ui',
				],
			],
		];
	}
}
