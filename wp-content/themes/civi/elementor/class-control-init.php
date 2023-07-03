<?php

namespace Civi_Elementor;

defined('ABSPATH') || exit;

class Control_Init
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
		require_once CIVI_ELEMENTOR_DIR . '/class-font-awesome-pro.php';
		require_once CIVI_ELEMENTOR_DIR . '/class-font-elementor.php';

		/**
		 * Register Controls.
		 */
		add_action('elementor/controls/controls_registered', array($this, 'init_controls'));

		/**
		 * Edit Controls.
		 */
		// Add custom Motion Effect - Entrance Animation.
		add_filter('elementor/controls/animations/additional_animations', [
			$this,
			'add_custom_entrance_animations',
		]);

		/**
		 * Add custom shape divider
		 */
		add_filter('elementor/shapes/additional_shapes', [$this, 'add_custom_shape_divider']);
	}

	public function add_custom_shape_divider($additional_shapes)
	{
		$additional_shapes['center-curve'] = [
			'title'        => esc_html__('Curve Alt', 'civi'),
			'has_negative' => true,
			'height_only'  => true,
			'url'          => get_template_directory_uri() . '/assets/shape-divider/center-curve.svg',
			'path'         => get_template_directory() . '/assets/shape-divider/center-curve.svg',
		];

		$additional_shapes['tilt-curve'] = [
			'title'       => esc_html__('Tile Curve', 'civi'),
			'has_flip'    => true,
			'height_only' => true,
			'url'         => get_template_directory_uri() . '/assets/shape-divider/curve-tilt.svg',
			'path'        => get_template_directory() . '/assets/shape-divider/curve-tilt.svg',
		];

		$additional_shapes['mountain-alt'] = [
			'title'       => esc_html__('Mountain Alt', 'civi'),
			'has_flip'    => true,
			'height_only' => true,
			'url'         => get_template_directory_uri() . '/assets/shape-divider/mountain-alt.svg',
			'path'        => get_template_directory() . '/assets/shape-divider/mountain-alt.svg',
		];

		return $additional_shapes;
	}

	public function add_custom_entrance_animations($animations)
	{
		$animations['By Civi'] = [
			'CiviFadeInDown'   => 'Civi - Fade In Down',
			'CiviFadeInLeft'   => 'Civi - Fade In Left',
			'CiviFadeInRight'  => 'Civi - Fade In Right',
			'CiviFadeInUp'     => 'Civi - Fade In Up',
			'CiviSlideInDown'  => 'Civi - Slide In Down',
			'CiviSlideInLeft'  => 'Civi - Slide In Left',
			'CiviSlideInRight' => 'Civi - Slide In Right',
			'CiviSlideInUp'    => 'Civi - Slide In Up',
			'CiviBottomToTop'    => 'Civi - Bottom To Top',
			'CiviSpin'    => 'Civi - Spin',
		];

		return $animations;
	}

	/**
	 * @param \Elementor\Controls_Manager $controls_manager
	 *
	 * Include controls files and register them
	 */
	public function init_controls($controls_manager)
	{
		// Include controls files.
		require_once CIVI_ELEMENTOR_DIR . '/controls/group-control-text-gradient.php';
		require_once CIVI_ELEMENTOR_DIR . '/controls/group-control-text-stroke.php';
		require_once CIVI_ELEMENTOR_DIR . '/controls/group-control-advanced-border.php';
		require_once CIVI_ELEMENTOR_DIR . '/controls/group-control-button.php';
		require_once CIVI_ELEMENTOR_DIR . '/controls/group-control-tooltip.php';

		// Group Control.
		$controls_manager->add_group_control(Group_Control_Text_Gradient::get_type(), new Group_Control_Text_Gradient());
		$controls_manager->add_group_control(Group_Control_Text_Stroke::get_type(), new Group_Control_Text_Stroke());
		$controls_manager->add_group_control(Group_Control_Advanced_Border::get_type(), new Group_Control_Advanced_Border());
		$controls_manager->add_group_control(Group_Control_Button::get_type(), new Group_Control_Button());
		$controls_manager->add_group_control(Group_Control_Tooltip::get_type(), new Group_Control_Tooltip());
	}
}

Control_Init::instance()->initialize();
