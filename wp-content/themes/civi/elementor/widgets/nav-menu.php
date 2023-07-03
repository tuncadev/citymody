<?php

namespace Civi_Elementor;

use Elementor\Controls_Manager;
use Elementor\Core\Schemes;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;
//use ElementorPro\Plugin;

defined('ABSPATH') || exit;

class Widget_Nav_Menu extends Base
{

	protected $nav_menu_index = 1;

	public function get_name()
	{
		return 'civi-modern-menu';
	}

	public function get_title()
	{
		return esc_html__('Modern Menu', 'civi');
	}

	public function get_icon_part()
	{
		return 'eicon-nav-menu';
	}

	public function get_keywords()
	{
		return ['menu', 'civi'];
	}

	public function get_script_depends()
	{
		return [
			'civi-widget-modern-menu',
		];
	}

	public function get_style_depends()
	{
		return ['civi-el-widget-modern-menu'];
	}

	private function get_available_menus()
	{
		$menus = wp_get_nav_menus();

		$options = [];

		foreach ($menus as $menu) {
			$options[$menu->slug] = $menu->name;
		}

		return $options;
	}

	protected function get_nav_menu_index()
	{
		return $this->nav_menu_index++;
	}

	/**
	 * Register controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls()
	{

		$this->start_controls_section(
			'section_layout',
			[
				'label' => __('Layout', 'civi'),
			]
		);

		$menus = $this->get_available_menus();

		if (!empty($menus)) {
			$this->add_control(
				'menu',
				[
					'label' => __('Menu', 'civi'),
					'type' => Controls_Manager::SELECT,
					'options' => $menus,
					'default' => array_keys($menus)[0],
					'save_default' => true,
					'separator' => 'after',
					'description' => sprintf(__('Go to the <a href="%s" target="_blank">Menus screen</a> to manage your menus.', 'civi'), admin_url('nav-menus.php')),
				]
			);
		} else {
			$this->add_control(
				'menu',
				[
					'type' => Controls_Manager::RAW_HTML,
					'raw' => '<strong>' . __('There are no menus in your site.', 'civi') . '</strong><br>' . sprintf(__('Go to the <a href="%s" target="_blank">Menus screen</a> to create one.', 'civi'), admin_url('nav-menus.php?action=edit&menu=0')),
					'separator' => 'after',
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
				]
			);
		}

		$this->add_control(
			'layout',
			[
				'label' => __('Layout', 'civi'),
				'type' => Controls_Manager::SELECT,
				'default' => 'horizontal',
				'options' => [
					'horizontal' => __('Horizontal', 'civi'),
					'dropdown' => __('Dropdown', 'civi'),
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'align_items',
			[
				'label' => __('Align', 'civi'),
				'type' => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options' => [
					'left' => [
						'title' => __('Left', 'civi'),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __('Center', 'civi'),
						'icon' => 'eicon-h-align-center',
					],
					'right' => [
						'title' => __('Right', 'civi'),
						'icon' => 'eicon-h-align-right',
					],
					'justify' => [
						'title' => __('Stretch', 'civi'),
						'icon' => 'eicon-h-align-stretch',
					],
				],
				'prefix_class' => 'elementor-nav-menu__align-',
				'condition' => [
					'layout!' => 'dropdown',
				],
			]
		);

		$this->add_control(
			'pointer',
			[
				'label' => __('Pointer', 'civi'),
				'type' => Controls_Manager::SELECT,
				'default' => 'underline',
				'options' => [
					'none' => __('None', 'civi'),
					'underline' => __('Underline', 'civi'),
					'overline' => __('Overline', 'civi'),
					'double-line' => __('Double Line', 'civi'),
					'framed' => __('Framed', 'civi'),
					'background' => __('Background', 'civi'),
					'text' => __('Text', 'civi'),
				],
				'style_transfer' => true,
				'condition' => [
					'layout!' => 'dropdown',
				],
			]
		);

		$this->add_control(
			'animation_line',
			[
				'label' => __('Animation', 'civi'),
				'type' => Controls_Manager::SELECT,
				'default' => 'fade',
				'options' => [
					'fade' => 'Fade',
					'slide' => 'Slide',
					'grow' => 'Grow',
					'drop-in' => 'Drop In',
					'drop-out' => 'Drop Out',
					'none' => 'None',
				],
				'condition' => [
					'layout!' => 'dropdown',
					'pointer' => ['underline', 'overline', 'double-line'],
				],
			]
		);

		$this->add_control(
			'animation_framed',
			[
				'label' => __('Animation', 'civi'),
				'type' => Controls_Manager::SELECT,
				'default' => 'fade',
				'options' => [
					'fade' => 'Fade',
					'grow' => 'Grow',
					'shrink' => 'Shrink',
					'draw' => 'Draw',
					'corners' => 'Corners',
					'none' => 'None',
				],
				'condition' => [
					'layout!' => 'dropdown',
					'pointer' => 'framed',
				],
			]
		);

		$this->add_control(
			'animation_background',
			[
				'label' => __('Animation', 'civi'),
				'type' => Controls_Manager::SELECT,
				'default' => 'fade',
				'options' => [
					'fade' => 'Fade',
					'grow' => 'Grow',
					'shrink' => 'Shrink',
					'sweep-left' => 'Sweep Left',
					'sweep-right' => 'Sweep Right',
					'sweep-up' => 'Sweep Up',
					'sweep-down' => 'Sweep Down',
					'shutter-in-vertical' => 'Shutter In Vertical',
					'shutter-out-vertical' => 'Shutter Out Vertical',
					'shutter-in-horizontal' => 'Shutter In Horizontal',
					'shutter-out-horizontal' => 'Shutter Out Horizontal',
					'none' => 'None',
				],
				'condition' => [
					'layout!' => 'dropdown',
					'pointer' => 'background',
				],
			]
		);

		$this->add_control(
			'animation_text',
			[
				'label' => __('Animation', 'civi'),
				'type' => Controls_Manager::SELECT,
				'default' => 'grow',
				'options' => [
					'grow' => 'Grow',
					'shrink' => 'Shrink',
					'sink' => 'Sink',
					'float' => 'Float',
					'skew' => 'Skew',
					'rotate' => 'Rotate',
					'none' => 'None',
				],
				'condition' => [
					'layout!' => 'dropdown',
					'pointer' => 'text',
				],
			]
		);

		$this->add_control(
			'indicator',
			[
				'label' => __('Submenu Indicator', 'civi'),
				'type' => Controls_Manager::SELECT,
				'default' => 'classic',
				'options' => [
					'none' => __('None', 'civi'),
					'classic' => __('Classic', 'civi'),
					'chevron' => __('Chevron', 'civi'),
					'angle' => __('Angle', 'civi'),
					'plus' => __('Plus', 'civi'),
				],
				'prefix_class' => 'elementor-nav-menu--indicator-',
			]
		);

		$this->add_control(
			'heading_mobile_dropdown',
			[
				'label' => __('Mobile Dropdown', 'civi'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'layout!' => 'dropdown',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_main-menu',
			[
				'label' => __('Main Menu', 'civi'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'layout!' => 'dropdown',
				],

			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'menu_typography',
				'scheme' => Schemes\Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .elementor-nav-menu .elementor-item',
			]
		);

		$this->start_controls_tabs('tabs_menu_item_style');

		$this->start_controls_tab(
			'tab_menu_item_normal',
			[
				'label' => __('Normal', 'civi'),
			]
		);

		$this->add_control(
			'color_menu_item',
			[
				'label' => __('Text Color', 'civi'),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_3,
				],
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-nav-menu--main .menu>li>a' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_menu_item_hover',
			[
				'label' => __('Hover', 'civi'),
			]
		);

		$this->add_control(
			'color_menu_item_hover',
			[
				'label' => __('Text Color', 'civi'),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_4,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-nav-menu--main .menu>li>a:hover,
					{{WRAPPER}} .elementor-nav-menu--main .menu>li>a.elementor-item-active,
					{{WRAPPER}} .elementor-nav-menu--main .menu>li>a.highlighted,
					{{WRAPPER}} .elementor-nav-menu--main .menu>li>a:focus' => 'color: {{VALUE}}',
				],
				'condition' => [
					'pointer!' => 'background',
				],
			]
		);

		$this->add_control(
			'color_menu_item_hover_pointer_bg',
			[
				'label' => __('Text Color', 'civi'),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .elementor-nav-menu--main .menu>li>a:hover,
					{{WRAPPER}} .elementor-nav-menu--main .menu>li>a.elementor-item-active,
					{{WRAPPER}} .elementor-nav-menu--main .menu>li>a.highlighted,
					{{WRAPPER}} .elementor-nav-menu--main .menu>li>a:focus' => 'color: {{VALUE}}',
				],
				'condition' => [
					'pointer' => 'background',
				],
			]
		);

		$this->add_control(
			'pointer_color_menu_item_hover',
			[
				'label' => __('Pointer Color', 'civi'),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_4,
				],
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-nav-menu--main:not(.e--pointer-framed) .elementor-item:before,
					{{WRAPPER}} .elementor-nav-menu--main:not(.e--pointer-framed) .elementor-item:after' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .e--pointer-framed .elementor-item:before,
					{{WRAPPER}} .e--pointer-framed .elementor-item:after' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'pointer!' => ['none', 'text'],
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_menu_item_active',
			[
				'label' => __('Active', 'civi'),
			]
		);

		$this->add_control(
			'color_menu_item_active',
			[
				'label' => __('Text Color', 'civi'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-nav-menu--main .menu>li>a.elementor-item-active' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'pointer_color_menu_item_active',
			[
				'label' => __('Pointer Color', 'civi'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-nav-menu--main:not(.e--pointer-framed) .elementor-item.elementor-item-active:before,
					{{WRAPPER}} .elementor-nav-menu--main:not(.e--pointer-framed) .elementor-item.elementor-item-active:after' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .e--pointer-framed .elementor-item.elementor-item-active:before,
					{{WRAPPER}} .e--pointer-framed .elementor-item.elementor-item-active:after' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'pointer!' => ['none', 'text'],
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		/* This control is required to handle with complicated conditions */
		$this->add_control(
			'hr',
			[
				'type' => Controls_Manager::DIVIDER,
			]
		);

		$this->add_responsive_control(
			'pointer_width',
			[
				'label' => __('Pointer Width', 'civi'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 30,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .e--pointer-framed .elementor-item:before' => 'border-width: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .e--pointer-framed.e--animation-draw .elementor-item:before' => 'border-width: 0 0 {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .e--pointer-framed.e--animation-draw .elementor-item:after' => 'border-width: {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}} 0 0',
					'{{WRAPPER}} .e--pointer-framed.e--animation-corners .elementor-item:before' => 'border-width: {{SIZE}}{{UNIT}} 0 0 {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .e--pointer-framed.e--animation-corners .elementor-item:after' => 'border-width: 0 {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}} 0',
					'{{WRAPPER}} .e--pointer-underline .elementor-item:after,
					 {{WRAPPER}} .e--pointer-overline .elementor-item:before,
					 {{WRAPPER}} .e--pointer-double-line .elementor-item:before,
					 {{WRAPPER}} .e--pointer-double-line .elementor-item:after' => 'height: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'pointer' => ['underline', 'overline', 'double-line', 'framed'],
				],
			]
		);

		$this->add_responsive_control(
			'padding_horizontal_menu_item',
			[
				'label' => __('Horizontal Padding', 'civi'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-nav-menu--main .menu>li>a' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'padding_vertical_menu_item',
			[
				'label' => __('Vertical Padding', 'civi'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-nav-menu--main .menu>li>a' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'menu_space_between',
			[
				'label' => __('Space Between', 'civi'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors' => [
					'body:not(.rtl) {{WRAPPER}} .elementor-nav-menu--layout-horizontal .elementor-nav-menu > li:not(:last-child)' => 'margin-right: {{SIZE}}{{UNIT}}',
					'body.rtl {{WRAPPER}} .elementor-nav-menu--layout-horizontal .elementor-nav-menu > li:not(:last-child)' => 'margin-left: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .elementor-nav-menu--main:not(.elementor-nav-menu--layout-horizontal) .elementor-nav-menu > li:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'border_radius_menu_item',
			[
				'label' => __('Border Radius', 'civi'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .elementor-item:before' => 'border-radius: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .e--animation-shutter-in-horizontal .elementor-item:before' => 'border-radius: {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}} 0 0',
					'{{WRAPPER}} .e--animation-shutter-in-horizontal .elementor-item:after' => 'border-radius: 0 0 {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .e--animation-shutter-in-vertical .elementor-item:before' => 'border-radius: 0 {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}} 0',
					'{{WRAPPER}} .e--animation-shutter-in-vertical .elementor-item:after' => 'border-radius: {{SIZE}}{{UNIT}} 0 0 {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'pointer' => 'background',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'style_toggle',
			[
				'label' => __('Toggle Button', 'civi'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('tabs_toggle_style');

		$this->start_controls_tab(
			'tab_toggle_style_normal',
			[
				'label' => __('Normal', 'civi'),
			]
		);

		$this->add_control(
			'toggle_color',
			[
				'label' => __('Color', 'civi'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .icon-menu' => 'color: {{VALUE}}', // Harder selector to override text color control
				],
			]
		);

		$this->add_control(
			'toggle_background_color',
			[
				'label' => __('Background Color', 'civi'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .icon-menu' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_toggle_style_hover',
			[
				'label' => __('Hover', 'civi'),
			]
		);

		$this->add_control(
			'toggle_color_hover',
			[
				'label' => __('Color', 'civi'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .icon-menu:hover' => 'color: {{VALUE}}', // Harder selector to override text color control
				],
			]
		);

		$this->add_control(
			'toggle_background_color_hover',
			[
				'label' => __('Background Color', 'civi'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .icon-menu:hover' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'toggle_size',
			[
				'label' => __('Size', 'civi'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 15,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .icon-menu' => 'font-size: {{SIZE}}{{UNIT}}',
				],
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'toggle_border',
				'selector' => '{{WRAPPER}} .icon-menu',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'toggle_border_radius',
			[
				'label' => __('Border Radius', 'civi'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .icon-menu' => 'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'toggle_padding',
			[
				'label' => __('Padding', 'civi'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .icon-menu' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_dropdown',
			[
				'label' => __('Dropdown', 'civi'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'dropdown_description',
			[
				'raw' => __('On desktop, this will affect the submenu. On mobile, this will affect the entire menu.', 'civi'),
				'type' => Controls_Manager::RAW_HTML,
				'content_classes' => 'elementor-descriptor',
			]
		);

		$this->start_controls_tabs('tabs_dropdown_item_style');

		$this->start_controls_tab(
			'tab_dropdown_item_normal',
			[
				'label' => __('Normal', 'civi'),
			]
		);

		$this->add_control(
			'color_dropdown_item',
			[
				'label' => __('Text Color', 'civi'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-nav-menu--dropdown a, {{WRAPPER}} .elementor-menu-toggle' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'background_color_dropdown_item',
			[
				'label' => __('Background Color', 'civi'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-nav-menu--dropdown' => 'background-color: {{VALUE}}',
				],
				'separator' => 'none',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_dropdown_item_hover',
			[
				'label' => __('Hover', 'civi'),
			]
		);

		$this->add_control(
			'color_dropdown_item_hover',
			[
				'label' => __('Text Color', 'civi'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-nav-menu--dropdown a:hover,
					{{WRAPPER}} .elementor-nav-menu--dropdown a.elementor-item-active,
					{{WRAPPER}} .elementor-nav-menu--dropdown a.highlighted,
					{{WRAPPER}} .elementor-menu-toggle:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'background_color_dropdown_item_hover',
			[
				'label' => __('Background Color', 'civi'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-nav-menu--dropdown a:hover,
					{{WRAPPER}} .elementor-nav-menu--dropdown a.elementor-item-active,
					{{WRAPPER}} .elementor-nav-menu--dropdown a.highlighted' => 'background-color: {{VALUE}}',
				],
				'separator' => 'none',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_dropdown_item_active',
			[
				'label' => __('Active', 'civi'),
			]
		);

		$this->add_control(
			'color_dropdown_item_active',
			[
				'label' => __('Text Color', 'civi'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-nav-menu--dropdown a.elementor-item-active' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'background_color_dropdown_item_active',
			[
				'label' => __('Background Color', 'civi'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .elementor-nav-menu--dropdown a.elementor-item-active' => 'background-color: {{VALUE}}',
				],
				'separator' => 'none',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'dropdown_typography',
				'scheme' => Schemes\Typography::TYPOGRAPHY_4,
				'exclude' => ['line_height'],
				'selector' => '{{WRAPPER}} .elementor-nav-menu--dropdown .elementor-item, {{WRAPPER}} .elementor-nav-menu--dropdown  .elementor-sub-item',
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'dropdown_border',
				'selector' => '{{WRAPPER}} .elementor-nav-menu--dropdown',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'dropdown_border_radius',
			[
				'label' => __('Border Radius', 'civi'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .elementor-nav-menu--dropdown' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .elementor-nav-menu--dropdown li:first-child a' => 'border-top-left-radius: {{TOP}}{{UNIT}}; border-top-right-radius: {{RIGHT}}{{UNIT}};',
					'{{WRAPPER}} .elementor-nav-menu--dropdown li:last-child a' => 'border-bottom-right-radius: {{BOTTOM}}{{UNIT}}; border-bottom-left-radius: {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'dropdown_box_shadow',
				'exclude' => [
					'box_shadow_position',
				],
				'selector' => '{{WRAPPER}} .elementor-nav-menu--main .elementor-nav-menu--dropdown, {{WRAPPER}} .elementor-nav-menu__container.elementor-nav-menu--dropdown',
			]
		);

		$this->add_responsive_control(
			'padding_horizontal_dropdown_item',
			[
				'label' => __('Horizontal Padding', 'civi'),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .elementor-nav-menu--dropdown a' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}}',
				],
				'separator' => 'before',

			]
		);

		$this->add_responsive_control(
			'padding_vertical_dropdown_item',
			[
				'label' => __('Vertical Padding', 'civi'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-nav-menu--dropdown a' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'heading_dropdown_divider',
			[
				'label' => __('Divider', 'civi'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'dropdown_divider',
				'selector' => '{{WRAPPER}} .elementor-nav-menu--dropdown li:not(:last-child)',
				'exclude' => ['width'],
			]
		);

		$this->add_control(
			'dropdown_divider_width',
			[
				'label' => __('Border Width', 'civi'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-nav-menu--dropdown li:not(:last-child)' => 'border-bottom-width: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'dropdown_divider_border!' => '',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render()
	{
		$settings = $this->get_settings_for_display();

		$args = array();

		$defaults = array(
			'echo'        => false,
			'menu'        => $settings['menu'],
			'menu_class'  => 'elementor-nav-menu menu',
			'menu_id'     => 'menu-' . $this->get_nav_menu_index() . '-' . $this->get_id(),
			'fallback_cb' => '__return_empty_string',
			'container'   => '',
		);

		$args = wp_parse_args($args, $defaults);

		if (has_nav_menu('primary') && class_exists('Civi_Walker_Nav_Menu')) {
			$args['walker'] = new \Civi_Walker_Nav_Menu;
		}

		if ('vertical' === $settings['layout']) {
			$args['menu_class'] .= ' sm-vertical';
		}

		// Add custom filter to handle Nav Menu HTML output.
		add_filter('nav_menu_link_attributes', [$this, 'handle_link_classes'], 10, 4);
		add_filter('nav_menu_submenu_css_class', [$this, 'handle_sub_menu_classes']);
		add_filter('nav_menu_item_id', '__return_empty_string');

		// General Menu.
		$menu_html = wp_nav_menu($args);

		// Remove all our custom filters.
		remove_filter('nav_menu_link_attributes', [$this, 'handle_link_classes']);
		remove_filter('nav_menu_submenu_css_class', [$this, 'handle_sub_menu_classes']);
		remove_filter('nav_menu_item_id', '__return_empty_string');

		$args_mobile = array();

		$defaults_mobile = array(
			'echo'        => false,
			'menu'        => $settings['menu'],
			'menu_class'  => 'menu',
			'menu_id'     => 'menu-' . $this->get_nav_menu_index() . '-' . $this->get_id(),
			'fallback_cb' => '__return_empty_string',
			'container'   => '',
		);

		$args_mobile = wp_parse_args($args_mobile, $defaults_mobile);

		// General Menu.
		$dropdown_menu_html = wp_nav_menu($args_mobile);

		$classes = array('elementor-nav-menu--main', 'elementor-nav-menu--layout-' . $settings['layout'], 'e--pointer-' . $settings['pointer']);

?>
		<nav class="site-menu main-menu desktop-menu <?php echo join(' ', $classes); ?>">
			<?php echo $menu_html; ?>
		</nav>

		<div class="mb-menu canvas-menu canvas-left <?php echo esc_attr('elementor-nav-menu--layout-' . $settings['layout']); ?>">
			<a href="#" class="icon-menu">
				<i class="far fa-bars"></i>
			</a>

			<div class="bg-overlay"></div>

			<div class="site-menu area-menu mobile-menu">

				<div class="inner-menu custom-scrollbar">

					<a href="#" class="btn-close">
						<i class="far fa-times"></i>
					</a>

					<?php \Civi_Templates::site_logo('dark'); ?>

					<?php echo $dropdown_menu_html; ?>
				</div>
			</div>
		</div>
<?php
	}

	public function handle_link_classes($atts, $item, $args, $depth)
	{
		$classes = $depth ? 'elementor-sub-item' : 'elementor-item';
		$is_anchor = false !== strpos($atts['href'], '#');

		if (!$is_anchor && in_array('current-menu-item', $item->classes)) {
			$classes .= ' elementor-item-active';
		}

		if ($is_anchor) {
			$classes .= ' elementor-item-anchor';
		}

		if (empty($atts['class'])) {
			$atts['class'] = $classes;
		} else {
			$atts['class'] .= ' ' . $classes;
		}

		return $atts;
	}

	public function handle_sub_menu_classes($classes)
	{
		$classes[] = 'elementor-nav-menu--dropdown';

		return $classes;
	}
}
