<?php

namespace Civi_Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;

defined('ABSPATH') || exit;

class Modify_Widget_Icon_Box extends Modify_Base
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
		add_action('elementor/element/icon-box/section_style_icon/before_section_end', [
			$this,
			'add_box_shadow_options',
		]);
	}

	/**
	 * @param \Elementor\Widget_Base $element The edited element.
	 */
	public function add_box_shadow_options($element)
	{
		// Add box shadow option for Icon.
		$element->start_injection([
			'type' => 'control',
			'at'   => 'after',
			'of'   => 'secondary_color',
		]);

		$element->add_group_control(Group_Control_Box_Shadow::get_type(), [
			'name'     => 'icon_box_shadow',
			'selector' => '{{WRAPPER}} .elementor-icon',
		]);

		$element->end_injection();

		// Add box shadow option for Icon Hover.
		$element->start_injection([
			'type' => 'control',
			'at'   => 'after',
			'of'   => 'hover_secondary_color',
		]);

		$element->add_group_control(Group_Control_Box_Shadow::get_type(), [
			'name'     => 'icon_hover_box_shadow',
			'selector' => '{{WRAPPER}} .elementor-icon:hover',
		]);

		$element->end_injection();

		// Add divider before.
		$element->update_responsive_control('icon_space', [
			'separator' => 'before',
		]);
	}
}

Modify_Widget_Icon_Box::instance()->initialize();
