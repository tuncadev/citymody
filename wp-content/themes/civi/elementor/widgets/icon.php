<?php

namespace Civi_Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;

defined('ABSPATH') || exit;

class Widget_Icon extends Base
{

	public function get_name()
	{
		return 'civi-icon';
	}

	public function get_title()
	{
		return esc_html__('Modern Icon', 'civi');
	}

	public function get_icon_part()
	{
		return 'eicon-favorite';
	}

	public function get_keywords()
	{
		return ['icon'];
	}

    public function get_style_depends()
    {
        return ['civi-el-widget-icon'];
    }

	protected function register_controls()
	{
		$this->add_icon_section();

		$this->add_icon_style_section();
	}

	private function add_icon_section()
	{
		$this->start_controls_section('icon_section', [
			'label' => esc_html__('Icon', 'civi'),
		]);

		$this->add_control('icon', [
			'label'      => esc_html__('Icon', 'civi'),
			'show_label' => false,
			'type'       => Controls_Manager::ICONS,
			'default'    => [
				'value'   => 'fas fa-star',
				'library' => 'fa-solid',
			],
		]);

		$this->add_control('view', [
			'label'        => esc_html__('View', 'civi'),
			'type'         => Controls_Manager::SELECT,
			'options'      => [
				'default' => esc_html__('Default', 'civi'),
				'stacked' => esc_html__('Stacked', 'civi'),
				'bubble'  => esc_html__('Bubble', 'civi'),
			],
			'default'      => 'default',
			'prefix_class' => 'civi-view-',
			'render_type'  => 'template',
			'condition'    => [
				'icon[value]!' => '',
			],
		]);

		$this->add_control('shape', [
			'label'        => esc_html__('Shape', 'civi'),
			'type'         => Controls_Manager::SELECT,
			'options'      => [
				'circle' => esc_html__('Circle', 'civi'),
				'square' => esc_html__('Square', 'civi'),
			],
			'default'      => 'circle',
			'condition'    => [
				'view'         => ['stacked'],
				'icon[value]!' => '',
			],
			'prefix_class' => 'civi-shape-',
		]);

		$this->add_control('link', [
			'label'       => esc_html__('Link', 'civi'),
			'type'        => Controls_Manager::URL,
			'dynamic'     => [
				'active' => true,
			],
			'placeholder' => esc_html__('https://your-link.com', 'civi'),
		]);

		$this->add_control('alignment', [
			'label'     => esc_html__('Alignment', 'civi'),
			'type'      => Controls_Manager::CHOOSE,
			'options'   => Widget_Utils::get_control_options_text_align(),
			'default'   => 'center',
			'selectors' => [
				'{{WRAPPER}}' => 'text-align: {{VALUE}};',
			],
		]);

		$this->end_controls_section();
	}

	private function add_icon_style_section()
	{
		$this->start_controls_section('icon_style_section', [
			'label'     => esc_html__('Icon', 'civi'),
			'tab'       => Controls_Manager::TAB_STYLE,
			'condition' => [
				'icon[value]!' => '',
			],
		]);

		$this->add_responsive_control('icon_wrap_height', [
			'label'     => esc_html__('Wrap Height', 'civi'),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [
				'px' => [
					'min' => 6,
					'max' => 300,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .civi-icon-wrap' => 'height: {{SIZE}}{{UNIT}};',
			],
		]);

		$this->start_controls_tabs('icon_colors');

		$this->start_controls_tab('icon_colors_normal', [
			'label' => esc_html__('Normal', 'civi'),
		]);

		$this->add_group_control(Group_Control_Text_Gradient::get_type(), [
			'name'     => 'icon',
			'selector' => '{{WRAPPER}} .icon',
		]);

		$this->end_controls_tab();

		$this->start_controls_tab('icon_colors_hover', [
			'label' => esc_html__('Hover', 'civi'),
		]);

		$this->add_group_control(Group_Control_Text_Gradient::get_type(), [
			'name'     => 'hover_icon',
			'selector' => '{{WRAPPER}} .civi-icon:hover .icon',
		]);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control('icon_size', [
			'label'     => esc_html__('Size', 'civi'),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [
				'px' => [
					'min' => 6,
					'max' => 300,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .civi-icon, {{WRAPPER}} .civi-icon-view' => 'font-size: {{SIZE}}{{UNIT}};',
			],
		]);

		$this->add_control('icon_rotate', [
			'label'     => esc_html__('Rotate', 'civi'),
			'type'      => Controls_Manager::SLIDER,
			'default'   => [
				'unit' => 'deg',
			],
			'selectors' => [
				'{{WRAPPER}} .icon i' => 'transform: rotate({{SIZE}}{{UNIT}});',
			],
		]);

		// Icon View Settings.
		$this->add_control('icon_view_heading', [
			'label'     => esc_html__('Icon View', 'civi'),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
			'condition' => [
				'view' => ['stacked', 'bubble'],
			],
		]);

		$this->add_control('icon_padding', [
			'label'     => esc_html__('Padding', 'civi'),
			'type'      => Controls_Manager::SLIDER,
			'selectors' => [
				'{{WRAPPER}} .civi-icon-view' => 'padding: {{SIZE}}{{UNIT}};',
			],
			'range'     => [
				'em' => [
					'min' => 0,
					'max' => 5,
				],
			],
			'condition' => [
				'view' => ['stacked'],
			],
		]);

		$this->add_control('icon_border_radius', [
			'label'      => esc_html__('Border Radius', 'civi'),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%'],
			'selectors'  => [
				'{{WRAPPER}} .civi-icon-view' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
			'condition'  => [
				'view' => ['stacked'],
			],
		]);

		$this->start_controls_tabs('icon_view_colors', [
			'condition' => [
				'view' => ['stacked', 'bubble'],
			],
		]);

		$this->start_controls_tab('icon_view_colors_normal', [
			'label' => esc_html__('Normal', 'civi'),
		]);

		$this->add_group_control(Group_Control_Background::get_type(), [
			'name'     => 'icon_view',
			'selector' => '{{WRAPPER}} .civi-icon-view',
		]);

		$this->add_group_control(Group_Control_Box_Shadow::get_type(), [
			'name'     => 'icon_view',
			'selector' => '{{WRAPPER}} .civi-icon-view',
		]);

		$this->end_controls_tab();

		$this->start_controls_tab('icon_view_colors_hover', [
			'label' => esc_html__('Hover', 'civi'),
		]);

		$this->add_group_control(Group_Control_Background::get_type(), [
			'name'     => 'hover_icon_view',
			'selector' => '{{WRAPPER}} .civi-icon:hover .civi-icon-view',
		]);

		$this->add_group_control(Group_Control_Box_Shadow::get_type(), [
			'name'     => 'hover_icon_view',
			'selector' => '{{WRAPPER}} .civi-icon:hover .civi-icon-view',
		]);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function render()
	{
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute('box', 'class', 'civi-icon');
		$box_tag = 'div';

		if (!empty($settings['link']['url'])) {
			$box_tag = 'a';

			$this->add_render_attribute('box', 'class', 'link-secret');
			$this->add_link_attributes('box', $settings['link']);
		}
?>
		<?php printf('<%1$s %2$s>', $box_tag, $this->get_render_attribute_string('box')); ?>
		<?php $this->print_icon($settings); ?>
		<?php printf('</%1$s>', $box_tag); ?>
	<?php
	}

	protected function content_template()
	{
		$id = uniqid('svg-gradient');
		// @formatter:off
	?>
		<# var svg_id='<?php esc_html_e($id); ?>' ; #>

			<# view.addRenderAttribute( 'box' , 'class' , 'civi-icon' ); var box_tag='div' ; if ( '' !==settings.link.url ) { box_tag='a' ; view.addRenderAttribute( 'box' , 'class' , 'link-secret' ); view.addRenderAttribute( 'box' , 'href' , '#' ); } view.addRenderAttribute( 'icon' , 'class' , 'civi-icon icon' ); if ( 'svg'===settings.icon.library ) { view.addRenderAttribute( 'icon' , 'class' , 'civi-svg-icon' ); } if ( 'gradient'===settings.icon_color_type ) { view.addRenderAttribute( 'icon' , 'class' , 'civi-gradient-icon' ); } else { view.addRenderAttribute( 'icon' , 'class' , 'civi-solid-icon' ); } var iconHTML=elementor.helpers.renderIcon( view, settings.icon, { 'aria-hidden' : true }, 'i' , 'object' ); #>
				<{{{ box_tag }}} {{{ view.getRenderAttributeString( 'box' ) }}}>
					<div class="civi-icon-wrap">
						<div class="civi-icon-view first">
							<div {{{ view.getRenderAttributeString( 'icon' ) }}}>
								<# if ( iconHTML.rendered ) { #>
									<# var stop_a=settings.icon_color_a_stop.size + settings.icon_color_a_stop.unit; var stop_b=settings.icon_color_b_stop.size + settings.icon_color_b_stop.unit; var iconValue=iconHTML.value; if ( typeof iconValue==='string' ) { var strokeAttr='stroke="' + 'url(#' + svg_id + ')"' ; var fillAttr='fill="' + 'url(#' + svg_id + ')"' ; iconValue=iconValue.replace(new RegExp(/stroke="#(.*?)" /, 'g' ), strokeAttr); iconValue=iconValue.replace(new RegExp(/fill="#(.*?)" /, 'g' ), fillAttr); } #>
										<svg aria-hidden="true" focusable="false" class="svg-defs-gradient">
											<defs>
												<linearGradient id="{{{ svg_id }}}" x1="0%" y1="0%" x2="0%" y2="100%">
													<stop class="stop-a" offset="{{{ stop_a }}}" />
													<stop class="stop-b" offset="{{{ stop_b }}}" />
												</linearGradient>
											</defs>
										</svg>

										{{{ iconValue }}}
										<# } #>
							</div>
						</div>

						<# if ( 'bubble'==settings.view ) { #>
							<div class="civi-icon-view second"></div>
							<# } #>

					</div>
				</{{{ box_tag }}}>
			<?php
			// @formatter:off
		}

		private function print_icon(array $settings)
		{
			$this->add_render_attribute('icon', 'class', [
				'civi-icon',
				'icon',
			]);

			$is_svg = isset($settings['icon']['library']) && 'svg' === $settings['icon']['library'] ? true : false;

			if ($is_svg) {
				$this->add_render_attribute('icon', 'class', [
					'civi-svg-icon',
				]);
			}

			if ('gradient' === $settings['icon_color_type']) {
				$this->add_render_attribute('icon', 'class', [
					'civi-gradient-icon',
				]);
			} else {
				$this->add_render_attribute('icon', 'class', [
					'civi-solid-icon',
				]);
			}
			?>
				<div class="civi-icon-wrap">
					<div class="civi-icon-view first">
						<div <?php $this->print_attributes_string('icon'); ?>>
							<?php $this->render_icon($settings, $settings['icon'], ['aria-hidden' => 'true'], $is_svg, 'icon'); ?>
						</div>
					</div>

					<?php if ('bubble' === $settings['view']) { ?>
						<div class="civi-icon-view second"></div>
					<?php } ?>
				</div>
		<?php
		}
	}
