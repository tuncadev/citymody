<?php

namespace Civi_Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Core\Schemes\Color;
use Elementor\Icons_Manager;
use Elementor\Core\Files\Assets\Svg\Svg_Handler;
use Elementor\Utils;

defined('ABSPATH') || exit;

class Group_Control_Icon extends Icons_Manager
{

	private static function render_svg_icon($value)
	{
		if (!isset($value['id'])) {
			return '';
		}
		return Svg_Handler::get_inline_svg($value['id']);
	}

	private static function render_icon_html($icon, $attributes = [], $tag = 'i')
	{
		$icon_types = self::get_icon_manager_tabs();
		if (isset($icon_types[$icon['library']]['render_callback']) && is_callable($icon_types[$icon['library']]['render_callback'])) {
			return call_user_func_array($icon_types[$icon['library']]['render_callback'], [$icon, $attributes, $tag]);
		}

		if (empty($attributes['class'])) {
			$attributes['class'] = $icon['value'];
		} else {
			if (is_array($attributes['class'])) {
				$attributes['class'][] = $icon['value'];
			} else {
				$attributes['class'] .= ' ' . $icon['value'];
			}
		}
		return '<' . $tag . ' ' . Utils::render_html_attributes($attributes) . '></' . $tag . '>';
	}

	public static function render_icon($icon, $attributes = [], $tag = 'i')
	{
		if (empty($icon['library'])) {
			return false;
		}
		$output = '';
		// handler SVG Icon
		if ('svg' === $icon['library']) {
			$output = self::render_svg_icon($icon['value']);
		} else {
			$output = self::render_icon_html($icon, $attributes, $tag);
		}
		return $output;
	}
}

class Widget_Instagram extends Base
{

	public function get_name()
	{
		return 'civi-instagram-addons';
	}

	public function get_title()
	{
		return __('Instagram', 'civi');
	}

	public function get_icon_part()
	{
		return 'eicon-instagram-gallery';
	}

	public function get_categories()
	{
		return ['civi'];
	}

	public function get_keywords()
	{
		return ['menu', 'civi'];
	}

	public function get_style_depends()
	{
		return [
			'elementor-icons-shared-0-css', 'elementor-icons-fa-brands', 'elementor-icons-fa-regular', 'elementor-icons-fa-solid',
			'slick','civi-el-widget-instagram',
		];
	}

	public function get_script_depends()
	{
		return [
			'slick',
			'civi-widgets-scripts',
		];
	}

	protected function register_controls()
	{

		$this->start_controls_section(
			'instagram_content',
			[
				'label' => __('Instagram', 'civi'),
			]
		);

		$this->add_control(
			'instagram_style',
			[
				'label' => __('Style', 'civi'),
				'type' => Controls_Manager::SELECT,
				'default' => '1',
				'options' => [
					'1'   => __('Style One', 'civi'),
					'2'   => __('Style Two', 'civi'),
					'3'   => __('Style Three', 'civi'),
					'4'   => __('Style Four', 'civi'),
				],
			]
		);

		// $this->add_control(
		//     'userid',
		//     [
		//         'label'         => __( 'Instagram user ID', 'civi' ),
		//         'type'          => Controls_Manager::TEXT,
		//         'placeholder'   => __( '6666969077', 'civi' ),
		//         'label_block'   =>true,
		//         'description'   => wp_kses_post( '(<a href="'.esc_url('https://codeofaninja.com/tools/find-instagram-user-id').'" target="_blank">Lookup your User ID</a>)', 'civi' ),
		//     ]
		// );

		$this->add_control(
			'access_token',
			[
				'label'         => __('Instagram Access Token', 'civi'),
				'type'          => Controls_Manager::TEXT,
				'label_block'   => true,
				'description'   => wp_kses_post('(<a href="' . esc_url('https://developers.facebook.com/docs/instagram-basic-display-api/getting-started') . '" target="_blank">Lookup your Access Token</a>)', 'civi'),
			]
		);

		$this->add_control(
			'delete_cache',
			[
				'label'         => __('Delete existing caching data', 'civi'),
				'type'          => Controls_Manager::SWITCHER,
				'separator'     => 'before',
			]
		);

		$this->add_control(
			'cash_time_duration',
			[
				'label' => __('Cache Time Duration', 'civi'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'minute'    => __('Minute', 'civi'),
					'hour'      => __('Hour', 'civi'),
					'day'       => __('Day', 'civi'),
					'week'      => __('Week', 'civi'),
					'month'     => __('Month', 'civi'),
					'year'      => __('Year', 'civi'),
				],
				'default' => 'day',
				'condition' => [
					'delete_cache!' => 'yes',
				],
				'label_block' => true,
			]
		);

		$this->add_control(
			'limit',
			[
				'label' => __('Item Limit', 'civi'),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 200,
				'step' => 1,
				'default' => 8,
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'instagram_column',
			[
				'label' => __('Column', 'civi'),
				'type' => Controls_Manager::SELECT,
				'description'   => wp_kses_post('If the slider is off, Then it will work.', 'civi'),
				'prefix_class' => 'civiinstagram-column%s-',
				'default' => '4',
				'required' => true,
				'options' => [
					'1'   => __('1', 'civi'),
					'2'   => __('2', 'civi'),
					'3'   => __('3', 'civi'),
					'4'   => __('4', 'civi'),
					'5'   => __('5', 'civi'),
					'6'   => __('6', 'civi'),
				],
			]
		);

		$this->add_control(
			'show_caption',
			[
				'label'         => __('Show Caption', 'civi'),
				'type'          => Controls_Manager::SWITCHER,
				'label_on'      => __('Show', 'civi'),
				'label_off'     => __('Hide', 'civi'),
				'return_value'  => 'yes',
				'default'       => 'yes',
			]
		);

		$this->add_control(
			'show_light_box',
			[
				'label'         => __('Show Light Box', 'civi'),
				'type'          => Controls_Manager::SWITCHER,
				'label_on'      => __('Show', 'civi'),
				'label_off'     => __('Hide', 'civi'),
				'return_value'  => 'yes',
				'default'       => 'yes',
			]
		);

		$this->add_control(
			'show_flow_button',
			[
				'label'         => __('Show Follow Button', 'civi'),
				'type'          => Controls_Manager::SWITCHER,
				'label_on'      => __('Show', 'civi'),
				'label_off'     => __('Hide', 'civi'),
				'return_value'  => 'yes',
				'default'       => 'yes',
			]
		);

		$this->add_control(
			'flow_button_txt',
			[
				'label' => __('Follow button Aditional text', 'civi'),
				'type' => Controls_Manager::TEXT,
				'default' => __('Follow @', 'civi'),
				'condition' => [
					'show_flow_button' => 'yes',
				],
				'label_block' => true,
			]
		);

		$this->add_control(
			'slider_on',
			[
				'label'         => __('Slider', 'civi'),
				'type'          => Controls_Manager::SWITCHER,
				'label_on'      => __('On', 'civi'),
				'label_off'     => __('Off', 'civi'),
				'return_value'  => 'yes',
				'default'       => 'no',
			]
		);

		$this->add_control(
			'zoomicon_type',
			[
				'label' => esc_html__('Zoom Icon Type', 'civi'),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'img' => [
						'title' => __('Image', 'civi'),
						'icon' => 'fa fa-picture-o',
					],
					'icon' => [
						'title' => __('Icon', 'civi'),
						'icon' => 'fa fa-info',
					]
				],
				'default' => 'img',
				'condition' => [
					'show_light_box' => 'yes',
				],
			]
		);

		$this->add_control(
			'zoom_image',
			[
				'label' => __('Image', 'civi'),
				'type' => Controls_Manager::MEDIA,
				'dynamic' => [
					'active' => true,
				],
				'condition' => [
					'show_light_box' => 'yes',
					'zoomicon_type' => 'img',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'zoom_imagesize',
				'default' => 'large',
				'separator' => 'none',
				'condition' => [
					'show_light_box' => 'yes',
					'zoomicon_type' => 'img',
				]
			]
		);

		$this->add_control(
			'zoom_icon',
			[
				'label' => __('Zoom Icon', 'civi'),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-plus',
					'library' => 'solid',
				],
				'condition' => [
					'show_light_box' => 'yes',
					'zoomicon_type' => 'icon',
				]
			]
		);

		$this->add_control(
			'flow_button_icon',
			[
				'label' => __('Flow Button Icon', 'civi'),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fab fa-instagram',
					'library' => 'solid',
				],
				'condition' => [
					'show_flow_button' => 'yes',
				]
			]
		);

		$this->end_controls_section();

		// Slider setting
		$this->start_controls_section(
			'instagram_slider_option',
			[
				'label' => esc_html__('Slider Option', 'civi'),
				'condition' => [
					'slider_on' => 'yes',
				]
			]
		);

		$this->add_control(
			'slitems',
			[
				'label' => esc_html__('Slider Items', 'civi'),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 20,
				'step' => 1,
				'default' => 8,
				'condition' => [
					'slider_on' => 'yes',
				]
			]
		);

		$this->add_control(
			'slarrows',
			[
				'label' => esc_html__('Slider Arrow', 'civi'),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					'slider_on' => 'yes',
				]
			]
		);

		$this->add_control(
			'slprevicon',
			[
				'label' => __('Previous icon', 'civi'),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-angle-left',
					'library' => 'solid',
				],
				'condition' => [
					'slider_on' => 'yes',
					'slarrows' => 'yes',
				]
			]
		);

		$this->add_control(
			'slnexticon',
			[
				'label' => __('Next icon', 'civi'),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-angle-right',
					'library' => 'solid',
				],
				'condition' => [
					'slider_on' => 'yes',
					'slarrows' => 'yes',
				]
			]
		);

		$this->add_control(
			'sldots',
			[
				'label' => esc_html__('Slider dots', 'civi'),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'no',
				'condition' => [
					'slider_on' => 'yes',
				]
			]
		);

		$this->add_control(
			'slpause_on_hover',
			[
				'type' => Controls_Manager::SWITCHER,
				'label_off' => __('No', 'civi'),
				'label_on' => __('Yes', 'civi'),
				'return_value' => 'yes',
				'default' => 'yes',
				'label' => __('Pause on Hover?', 'civi'),
				'condition' => [
					'slider_on' => 'yes',
				]
			]
		);

		$this->add_control(
			'slcentermode',
			[
				'label' => esc_html__('Center Mode', 'civi'),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'no',
				'condition' => [
					'slider_on' => 'yes',
				]
			]
		);

		$this->add_control(
			'slcenterpadding',
			[
				'label' => esc_html__('Center padding', 'civi'),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 500,
				'step' => 1,
				'default' => 50,
				'condition' => [
					'slider_on' => 'yes',
					'slcentermode' => 'yes',
				]
			]
		);

		$this->add_control(
			'slautolay',
			[
				'label' => esc_html__('Slider auto play', 'civi'),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'separator' => 'before',
				'default' => 'no',
				'condition' => [
					'slider_on' => 'yes',
				]
			]
		);

		$this->add_control(
			'slautoplay_speed',
			[
				'label' => __('Autoplay speed', 'civi'),
				'type' => Controls_Manager::NUMBER,
				'default' => 3000,
				'condition' => [
					'slider_on' => 'yes',
				]
			]
		);


		$this->add_control(
			'slanimation_speed',
			[
				'label' => __('Autoplay animation speed', 'civi'),
				'type' => Controls_Manager::NUMBER,
				'default' => 300,
				'condition' => [
					'slider_on' => 'yes',
				]
			]
		);

		$this->add_control(
			'slscroll_columns',
			[
				'label' => __('Slider item to scroll', 'civi'),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 10,
				'step' => 1,
				'default' => 1,
				'condition' => [
					'slider_on' => 'yes',
				]
			]
		);

		$this->add_control(
			'heading_tablet',
			[
				'label' => __('Tablet', 'civi'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'after',
				'condition' => [
					'slider_on' => 'yes',
				]
			]
		);

		$this->add_control(
			'sltablet_display_columns',
			[
				'label' => __('Slider Items', 'civi'),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 8,
				'step' => 1,
				'default' => 1,
				'condition' => [
					'slider_on' => 'yes',
				]
			]
		);

		$this->add_control(
			'sltablet_scroll_columns',
			[
				'label' => __('Slider item to scroll', 'civi'),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 8,
				'step' => 1,
				'default' => 1,
				'condition' => [
					'slider_on' => 'yes',
				]
			]
		);

		$this->add_control(
			'sltablet_width',
			[
				'label' => __('Tablet Resolution', 'civi'),
				'description' => __('The resolution to tablet.', 'civi'),
				'type' => Controls_Manager::NUMBER,
				'default' => 750,
				'condition' => [
					'slider_on' => 'yes',
				]
			]
		);

		$this->add_control(
			'heading_mobile',
			[
				'label' => __('Mobile Phone', 'civi'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'after',
				'condition' => [
					'slider_on' => 'yes',
				]
			]
		);

		$this->add_control(
			'slmobile_display_columns',
			[
				'label' => __('Slider Items', 'civi'),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 4,
				'step' => 1,
				'default' => 1,
				'condition' => [
					'slider_on' => 'yes',
				]
			]
		);

		$this->add_control(
			'slmobile_scroll_columns',
			[
				'label' => __('Slider item to scroll', 'civi'),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 4,
				'step' => 1,
				'default' => 1,
				'condition' => [
					'slider_on' => 'yes',
				]
			]
		);

		$this->add_control(
			'slmobile_width',
			[
				'label' => __('Mobile Resolution', 'civi'),
				'description' => __('The resolution to mobile.', 'civi'),
				'type' => Controls_Manager::NUMBER,
				'default' => 480,
				'condition' => [
					'slider_on' => 'yes',
				]
			]
		);

		$this->end_controls_section(); // Slider Option end

		// Style tab section
		$this->start_controls_section(
			'civi_instagram_style_section',
			[
				'label' => __('Style', 'civi'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'instagram_background',
				'label' => __('Background', 'civi'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .civi-instragram ul.civi-instagram-list',
			]
		);

		$this->add_responsive_control(
			'instagram_margin',
			[
				'label' => __('Margin', 'civi'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .civi-instragram ul.civi-instagram-list' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'instagram_padding',
			[
				'label' => __('Padding', 'civi'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .civi-instragram ul.civi-instagram-list' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section(); // Style Section

		// Item Style
		$this->start_controls_section(
			'civi_instagram_item_style_section',
			[
				'label' => __('Item', 'civi'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'instagram_item_background',
				'label' => __('Background', 'civi'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .civi-instragram ul.civi-instagram-list li',
			]
		);

		$this->add_responsive_control(
			'instagram_item_margin',
			[
				'label' => __('Margin', 'civi'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .civi-instragram ul.civi-instagram-list li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'instagram_item_padding',
			[
				'label' => __('Padding', 'civi'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .civi-instragram ul.civi-instagram-list li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'instagram_item_border',
				'label' => __('Border', 'civi'),
				'selector' => '{{WRAPPER}} .civi-instragram ul.civi-instagram-list li',
			]
		);

		$this->add_responsive_control(
			'instagram_item_border_radius',
			[
				'label' => esc_html__('Border Radius', 'civi'),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .civi-instragram ul.civi-instagram-list li' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
				],
			]
		);

		$this->add_control(
			'instagram_item_overlay_color',
			[
				'label' => __('Overlay Color', 'civi'),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Color::get_type(),
					'value' => Color::COLOR_1,
				],
				'default' => 'rgba(0, 0, 0, 0.7)',
				'selectors' => [
					'{{WRAPPER}} .civi-instragram ul.civi-instagram-list li .instagram-clip::before' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section(); // Item Style end

		// Zoom icon Style
		$this->start_controls_section(
			'civi_instagram_icon_style_section',
			[
				'label' => __('Icon', 'civi'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'zoomicon_type' => 'icon',
					'zoom_icon[value]!' => '',
				]
			]
		);

		$this->add_control(
			'icon_size',
			[
				'label' => __('Font Size', 'civi'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 43,
				],
				'selectors' => [
					'{{WRAPPER}} .civi-instragram ul.civi-instagram-list .zoom_icon i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .civi-instragram ul.civi-instagram-list .zoom_icon svg' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'instagram_icon_color',
			[
				'label' => __('Color', 'civi'),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Color::get_type(),
					'value' => Color::COLOR_1,
				],
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .civi-instragram ul.civi-instagram-list .zoom_icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .civi-instragram ul.civi-instagram-list .zoom_icon svg' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'instagram_icon_background',
				'label' => __('Background', 'civi'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .civi-instragram ul.civi-instagram-list .zoom_icon',
			]
		);

		$this->add_responsive_control(
			'instagram_icon_padding',
			[
				'label' => __('Padding', 'civi'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .civi-instragram ul.civi-instagram-list .zoom_icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'instagram_icon_border',
				'label' => __('Border', 'civi'),
				'selector' => '{{WRAPPER}} .civi-instragram ul.civi-instagram-list .zoom_icon',
			]
		);

		$this->add_responsive_control(
			'instagram_icon_border_radius',
			[
				'label' => esc_html__('Border Radius', 'civi'),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .civi-instragram ul.civi-instagram-list .zoom_icon' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
				],
			]
		);

		$this->end_controls_section(); // Zoom icon Style end

		// Zoom icon Style
		$this->start_controls_section(
			'civi_instagram_caption_style_section',
			[
				'label' => __('Caption', 'civi'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'commentlike_size',
			[
				'label' => __('Font Size', 'civi'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 16,
				],
				'selectors' => [
					'{{WRAPPER}} .civi-instragram ul.civi-instagram-list li .instagram-clip .civi-content .instagram-like-comment p' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'instagram_commentlike_color',
			[
				'label' => __('Color', 'civi'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .civi-instragram ul.civi-instagram-list li .instagram-clip .civi-content .instagram-like-comment p' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'instagram_commentlike_padding',
			[
				'label' => __('Padding', 'civi'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .civi-instragram ul.civi-instagram-list li .instagram-clip .civi-content .instagram-like-comment p' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'instagram_commentlike_margin',
			[
				'label' => __('Margin', 'civi'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .civi-instragram ul.civi-instagram-list li .instagram-clip .civi-content .instagram-like-comment p' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section(); // Zoom icon Style end

		// Style instagram arrow style start
		$this->start_controls_section(
			'civi_instagram_arrow_style',
			[
				'label'     => __('Arrow', 'civi'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'slider_on' => 'yes',
					'slarrows'  => 'yes',
				],
			]
		);

		$this->start_controls_tabs('instagram_arrow_style_tabs');

		// Normal tab Start
		$this->start_controls_tab(
			'instagram_arrow_style_normal_tab',
			[
				'label' => __('Normal', 'civi'),
			]
		);

		$this->add_control(
			'civi_instagram_arrow_color',
			[
				'label' => __('Color', 'civi'),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Color::get_type(),
					'value' => Color::COLOR_1,
				],
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .civi-instragram .slick-arrow' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'civi_instagram_arrow_fontsize',
			[
				'label' => __('Font Size', 'civi'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .civi-instragram .slick-arrow' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'instagram_arrow_background',
				'label' => __('Background', 'civi'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .civi-instragram .slick-arrow',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'civi_instagram_arrow_border',
				'label' => __('Border', 'civi'),
				'selector' => '{{WRAPPER}} .civi-instragram .slick-arrow',
			]
		);

		$this->add_responsive_control(
			'civi_instagram_arrow_border_radius',
			[
				'label' => esc_html__('Border Radius', 'civi'),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .civi-instragram .slick-arrow' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
				],
			]
		);

		$this->add_control(
			'civi_instagram_arrow_height',
			[
				'label' => __('Height', 'civi'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 30,
				],
				'selectors' => [
					'{{WRAPPER}} .civi-instragram .slick-arrow' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'civi_instagram_arrow_width',
			[
				'label' => __('Width', 'civi'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 30,
				],
				'selectors' => [
					'{{WRAPPER}} .civi-instragram .slick-arrow' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'civi_instagram_arrow_padding',
			[
				'label' => __('Padding', 'civi'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .civi-instragram .slick-arrow' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_tab(); // Normal tab end

		// Hover tab Start
		$this->start_controls_tab(
			'instagram_arrow_style_hover_tab',
			[
				'label' => __('Hover', 'civi'),
			]
		);

		$this->add_control(
			'civi_instagram_arrow_hover_color',
			[
				'label' => __('Color', 'civi'),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Color::get_type(),
					'value' => Color::COLOR_1,
				],
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .civi-instragram .slick-arrow:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'instagram_arrow_hover_background',
				'label' => __('Background', 'civi'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .civi-instragram .slick-arrow:hover',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'civi_instagram_arrow_hover_border',
				'label' => __('Border', 'civi'),
				'selector' => '{{WRAPPER}} .civi-instragram .slick-arrow:hover',
			]
		);

		$this->add_responsive_control(
			'civi_instagram_arrow_hover_border_radius',
			[
				'label' => esc_html__('Border Radius', 'civi'),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .civi-instragram .slick-arrow:hover' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
				],
			]
		);

		$this->end_controls_tab(); // Hover tab end

		$this->end_controls_tabs();

		$this->end_controls_section(); // Style instagram arrow style end


		// Style instagram Dots style start
		$this->start_controls_section(
			'civi_instagram_dots_style',
			[
				'label'     => __('Pagination', 'civi'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'slider_on' => 'yes',
					'sldots'  => 'yes',
				],
			]
		);

		$this->start_controls_tabs('instagram_dots_style_tabs');

		// Normal tab Start
		$this->start_controls_tab(
			'instagram_dots_style_normal_tab',
			[
				'label' => __('Normal', 'civi'),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'instagram_dots_background',
				'label' => __('Background', 'civi'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .civi-instragram .slick-dots li',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'civi_instagram_dots_border',
				'label' => __('Border', 'civi'),
				'selector' => '{{WRAPPER}} .civi-instragram .slick-dots li',
			]
		);

		$this->add_responsive_control(
			'civi_instagram_dots_border_radius',
			[
				'label' => esc_html__('Border Radius', 'civi'),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .civi-instragram .slick-dots li' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
				],
			]
		);

		$this->add_control(
			'civi_instagram_dots_height',
			[
				'label' => __('Height', 'civi'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 15,
				],
				'selectors' => [
					'{{WRAPPER}} .civi-instragram .slick-dots li' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'civi_instagram_dots_width',
			[
				'label' => __('Width', 'civi'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 15,
				],
				'selectors' => [
					'{{WRAPPER}} .civi-instragram .slick-dots li' => 'width: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->end_controls_tab(); // Normal tab end

		// Hover tab Start
		$this->start_controls_tab(
			'instagram_dots_style_hover_tab',
			[
				'label' => __('Active', 'civi'),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'instagram_dots_hover_background',
				'label' => __('Background', 'civi'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .civi-instragram .slick-dots li.slick-active',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'civi_instagram_dots_hover_border',
				'label' => __('Border', 'civi'),
				'selector' => '{{WRAPPER}} .civi-instragram .slick-dots li.slick-active',
			]
		);

		$this->add_responsive_control(
			'civi_instagram_dots_hover_border_radius',
			[
				'label' => esc_html__('Border Radius', 'civi'),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .civi-instragram .slick-dots li.slick-active' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
				],
			]
		);

		$this->end_controls_tab(); // Hover tab end

		$this->end_controls_tabs();

		$this->end_controls_section(); // Style instagram dots style end

	}

	protected function render($instance = [])
	{
		$settings   = $this->get_settings_for_display();
		$id         = $this->get_id();

		$this->add_render_attribute('civi_instragram', 'class', 'civi-instragram');
		$this->add_render_attribute('civi_instragram', 'class', 'civi-instragram-style-' . $settings['instagram_style']);

		$limit        = !empty($settings['limit']) ? $settings['limit'] : 8;
		$access_token = !empty($settings['access_token']) ? $settings['access_token'] : '';

		$cache_duration = $this->get_cacheing_duration($settings['cash_time_duration']);
		$transient_var  = $id . '_' . $limit;

		if ($settings['delete_cache'] === 'yes') {
			delete_transient($transient_var);
			$cache_duration = MINUTE_IN_SECONDS;
		}

		if (empty($access_token)) {
			echo '<p>' . esc_html__('Please enter your access token.', 'civi') . '</p>';
			return;
		}

		if (false === ($items = get_transient($transient_var))) {

			$url = 'https://graph.instagram.com/me/media?fields=id,caption,media_type,media_url,permalink,thumbnail_url,timestamp,username&limit=200&access_token=' . esc_html($access_token);

			$instagram_data = wp_remote_retrieve_body(wp_remote_get($url));

			$instagram_data = json_decode($instagram_data, true);

			if (!is_wp_error($instagram_data)) {

				if (isset($instagram_data['error']['message'])) {

					echo '<p>' . esc_html__('Incorrect access token specified.', 'civi') . '</p>';
				}

				$items = array();
				foreach ($instagram_data['data'] as $data_item) {
					$item['id']         = $data_item['id'];
					$item['media_type'] = $data_item['media_type'];
					$item['src']        = $data_item['media_url'];
					$item['username']   = $data_item['username'];
					$item['link']       = $data_item['permalink'];
					$item['timestamp']  = $data_item['timestamp'];
					$item['caption']    = !empty($data_item['caption']) ? $data_item['caption'] : '';
					$items[]            = $item;
				}
				set_transient($transient_var, $items, 1 * $cache_duration);
			}
		}

		$username      = !empty($items[0]['username']) ? $items[0]['username'] : '';
		$profile_link  = !empty($items[0]['username']) ? 'https://www.instagram.com/' . $items[0]['username'] : '#';

		// Instagram Attribute
		$this->add_render_attribute('instagram_attr', 'class', 'civi-instagram-list');
		if ($settings['slider_on'] == 'yes') {
			$this->add_render_attribute('instagram_attr', 'class', 'civi-carousel-activation');

			$slider_settings = [
				'arrows' => ('yes' === $settings['slarrows']),
				'arrow_prev_txt' => Group_Control_Icon::render_icon($settings['slprevicon'], ['aria-hidden' => 'true']),
				'arrow_next_txt' => Group_Control_Icon::render_icon($settings['slnexticon'], ['aria-hidden' => 'true']),
				'dots' => ('yes' === $settings['sldots']),
				'autoplay' => ('yes' === $settings['slautolay']),
				'autoplay_speed' => absint($settings['slautoplay_speed']),
				'animation_speed' => absint($settings['slanimation_speed']),
				'pause_on_hover' => ('yes' === $settings['slpause_on_hover']),
				'center_mode' => ('yes' === $settings['slcentermode']),
				'center_padding' => absint($settings['slcenterpadding']),
			];

			$slider_responsive_settings = [
				'display_columns' => $settings['slitems'],
				'scroll_columns' => $settings['slscroll_columns'],
				'tablet_width' => $settings['sltablet_width'],
				'tablet_display_columns' => $settings['sltablet_display_columns'],
				'tablet_scroll_columns' => $settings['sltablet_scroll_columns'],
				'mobile_width' => $settings['slmobile_width'],
				'mobile_display_columns' => $settings['slmobile_display_columns'],
				'mobile_scroll_columns' => $settings['slmobile_scroll_columns'],

			];

			$slider_settings = array_merge($slider_settings, $slider_responsive_settings);

			$this->add_render_attribute('instagram_attr', 'data-settings', wp_json_encode($slider_settings));
		}

?>
		<div <?php echo $this->get_render_attribute_string('civi_instragram'); ?>>

			<ul <?php echo $this->get_render_attribute_string('instagram_attr'); ?>>
				<?php
				if (isset($items) && !empty($items)) :
					$countitem = 0;
					foreach ($items as $item) :
						$countitem++;
				?>
						<li>
							<a href="<?php echo esc_url($item['link']); ?>">
								<img src="<?php echo esc_url($item['src']); ?>" alt="<?php echo esc_attr($item['username']); ?>">
							</a>
							<?php if ($settings['show_caption'] == 'yes' || $settings['show_light_box'] == 'yes') : ?>
								<div class="instagram-clip">
									<div class="civi-content">
										<?php if ($settings['show_caption'] == 'yes' && !empty($item['caption'])) : ?>
											<div class="instagram-like-comment">
												<p><?php esc_html_e($item['caption']); ?></p>
											</div>
										<?php endif;
										if ($settings['show_light_box'] == 'yes') : ?>
											<div class="instagram-btn">
												<a class="image-popup-vertical-fit" href="<?php echo esc_url($item['src']); ?>">
													<?php
													if (!empty($settings['zoom_image']) && $settings['zoomicon_type'] == 'img') {
														echo Group_Control_Image_Size::get_attachment_image_html($settings, 'zoom_imagesize', 'zoom_image');
													} else {
														echo sprintf('<span class="zoom_icon">%1$s</span>', Group_Control_Icon::render_icon($settings['zoom_icon'], ['aria-hidden' => 'true']));
													}
													?>
												</a>
											</div>
										<?php endif; ?>

									</div>
								</div>
							<?php endif; ?>
						</li>
				<?php if ($countitem == $limit) {
							break;
						}
					endforeach;
				endif; ?>
			</ul>
			<?php
			if ($settings['show_flow_button'] == 'yes') :
				$btn_prefix_txt = !empty($settings['flow_button_txt']) ? $settings['flow_button_txt'] : '';
			?>
				<a class="instagram_follow_btn" href="<?php echo esc_url($profile_link); ?>" target="_blank">
					<?php echo Group_Control_Icon::render_icon($settings['flow_button_icon'], ['aria-hidden' => 'true']); ?>
					<span><?php esc_html_e($btn_prefix_txt . ' ' . $username); ?></span>
				</a>
			<?php endif; ?>

		</div>

<?php
	}

	protected function get_cacheing_duration($duration)
	{
		switch ($duration) {
			case 'minute':
				$cache_duration = MINUTE_IN_SECONDS;
				break;
			case 'hour':
				$cache_duration = HOUR_IN_SECONDS;
				break;
			case 'day':
				$cache_duration = DAY_IN_SECONDS;
				break;
			case 'week':
				$cache_duration = WEEK_IN_SECONDS;
				break;
			case 'month':
				$cache_duration = MONTH_IN_SECONDS;
				break;
			case 'year':
				$cache_duration = YEAR_IN_SECONDS;
				break;
			default:
				$cache_duration = DAY_IN_SECONDS;
		}
		return $cache_duration;
	}
}
