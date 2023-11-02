<?php

namespace Civi_Elementor;

use Elementor\Group_Control_Base;
use Elementor\Controls_Manager;

defined('ABSPATH') || exit;

/**
 * Elementor tooltip control.
 *
 * A base control for creating tooltip control.
 *
 * @since 1.0.0
 */
class Group_Control_Tooltip extends Group_Control_Base
{

	protected static $fields;

	public static function get_type()
	{
		return 'tooltip';
	}

	protected function init_fields()
	{
		$fields = [];

		$fields['skin'] = [
			'label'   => esc_html__('Tooltip Skin', 'civi'),
			'type'    => Controls_Manager::SELECT,
			'options' => [
				''        => esc_html__('Black', 'civi'),
				'white'   => esc_html__('White', 'civi'),
				'primary' => esc_html__('Primary', 'civi'),
			],
			'default' => '',
		];

		$fields['position'] = [
			'label'   => esc_html__('Tooltip Position', 'civi'),
			'type'    => Controls_Manager::SELECT,
			'options' => [
				'top'          => esc_html__('Top', 'civi'),
				'right'        => esc_html__('Right', 'civi'),
				'bottom'       => esc_html__('Bottom', 'civi'),
				'left'         => esc_html__('Left', 'civi'),
				'top-left'     => esc_html__('Top Left', 'civi'),
				'top-right'    => esc_html__('Top Right', 'civi'),
				'bottom-left'  => esc_html__('Bottom Left', 'civi'),
				'bottom-right' => esc_html__('Bottom Right', 'civi'),
			],
			'default' => 'top',
		];

		return $fields;
	}

	protected function get_default_options()
	{
		return [
			'popover' => [
				'starter_title' => _x('Tooltip', 'Tooltip Control', 'civi'),
				'starter_name'  => 'enable',
				'starter_value' => 'yes',
				'settings'      => [
					'render_type' => 'template',
				],
			],
		];
	}
}
