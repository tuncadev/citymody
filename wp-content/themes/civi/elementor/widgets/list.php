<?php

namespace Civi_Elementor;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;

defined('ABSPATH') || exit;

class Widget_List extends Base
{

	public function get_name()
	{
		return 'civi-list';
	}

	public function get_title()
	{
		return esc_html__('Modern Icon List', 'civi');
	}

	public function get_icon_part()
	{
		return 'eicon-bullet-list';
	}

	public function get_keywords()
	{
		return ['modern', 'icon list', 'icon', 'list'];
	}

	public function get_script_depends()
	{
		return ['civi-widget-list'];
	}

	public function get_style_depends()
	{
		return ['civi-el-widget-list'];
	}

	protected function register_controls()
	{
		$this->add_list_section();

		$this->add_styling_section();

		$this->add_heading_style_section();

		$this->add_text_style_section();

		$this->add_icon_style_section();
	}

	private function add_list_section()
	{
		$this->start_controls_section('list_section', [
			'label' => esc_html__('Icon List', 'civi'),
		]);

		$this->add_control('style', [
			'label'        => esc_html__('Style', 'civi'),
			'type'         => Controls_Manager::SELECT,
			'default'      => '',
			'options'      => [
				''            => esc_html__('Normal', 'civi'),
				'icon-border' => esc_html__('Icon Border', 'civi'),
			],
			'prefix_class' => 'civi-list-style-',
		]);

		$this->add_control('layout', [
			'label'        => esc_html__('Layout', 'civi'),
			'label_block'  => false,
			'type'         => Controls_Manager::CHOOSE,
			'default'      => 'block',
			'options'      => [
				'block'   => [
					'title' => esc_html__('Default', 'civi'),
					'icon'  => 'eicon-editor-list-ul',
				],
				'inline'  => [
					'title' => esc_html__('Inline', 'civi'),
					'icon'  => 'eicon-ellipsis-h',
				],
				'columns' => [
					'title' => esc_html__('Columns', 'civi'),
					'icon'  => 'eicon-columns',
				],
			],
			'prefix_class' => 'civi-list-layout-',
		]);

		$this->add_control('icon', [
			'label'       => esc_html__('Default Icon', 'civi'),
			'description' => esc_html__('Choose default icon for all items.', 'civi'),
			'type'        => Controls_Manager::ICONS,
		]);

		$this->add_control('icon_vertical_alignment', [
			'label'                => esc_html__('Icon Alignment', 'civi'),
			'type'                 => Controls_Manager::CHOOSE,
			'options'              => Widget_Utils::get_control_options_vertical_alignment(),
			'default'              => 'middle',
			'selectors_dictionary' => [
				'top'    => 'flex-start',
				'middle' => 'center',
				'bottom' => 'flex-end',
			],
			'selectors'            => [
				'{{WRAPPER}} .list-header' => 'align-items: {{VALUE}}',
			],
		]);

		$this->add_control(
			'enable_toggle',
			[
				'label' => esc_html__('Enable Toggle (Mobie)', 'civi'),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
			]
		);

		$this->add_control('heading', [
			'label'       => esc_html__('Heading', 'civi'),
			'type'        => Controls_Manager::TEXT,
			'default'     => esc_html__('Heading', 'civi'),
			'label_block' => true,
			'condition' => [
				'enable_toggle!' => '',
			],
		]);

		$repeater = new Repeater();

		$repeater->add_control('text', [
			'label'       => esc_html__('Text', 'civi'),
			'type'        => Controls_Manager::TEXTAREA,
			'default'     => esc_html__('Text', 'civi'),
			'label_block' => true,
		]);

		$repeater->add_control('icon', [
			'label' => esc_html__('Icon', 'civi'),
			'type'  => Controls_Manager::ICONS,
		]);

		$repeater->add_control('link', [
			'label'       => esc_html__('Link', 'civi'),
			'type'        => Controls_Manager::URL,
			'dynamic'     => [
				'active' => true,
			],
			'placeholder' => esc_html__('https://your-link.com', 'civi'),
		]);

		$repeater->add_control(
			'enable_badge',
			[
				'label' => esc_html__('Enable Badge', 'civi'),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
			]
		);

		$repeater->add_control('badge_text', [
			'label'       => esc_html__('Text', 'civi'),
			'type'        => Controls_Manager::TEXT,
			'default'     => esc_html__('Hot', 'civi'),
			'condition' => [
				'enable_badge' => 'yes',
			],
		]);

		$repeater->add_control('badge_color', [
			'label'     => esc_html__('Background Color', 'civi'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .badge' => 'background-color: {{VALUE}};',
			],
			'condition' => [
				'enable_badge' => 'yes',
			],
		]);

		$this->add_control('items', [
			'label'       => esc_html__('Items', 'civi'),
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $repeater->get_controls(),
			'default'     => [
				[
					'text' => esc_html__('List Item #1', 'civi'),
				],
				[
					'text' => esc_html__('List Item #2', 'civi'),
				],
				[
					'text' => esc_html__('List Item #3', 'civi'),
				],
			],
			'title_field' => '{{{ elementor.helpers.renderIcon( this, icon, {}, "i", "panel" ) || \'<i class="{{ icon }}" aria-hidden="true"></i>\' }}} {{{ text }}}',
		]);

		$this->end_controls_section();
	}

	private function add_styling_section()
	{
		$this->start_controls_section('styling_section', [
			'label' => esc_html__('Styling', 'civi'),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

		$this->add_control('items_vertical_spacing', [
			'label'      => esc_html__('Items Spacing', 'civi'),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => ['px'],
			'range'      => [
				'px' => [
					'max'  => 200,
					'step' => 1,
				],
			],
			'selectors'  => [
				'{{WRAPPER}}.civi-list-layout-block .item + .item, {{WRAPPER}}.civi-list-layout-columns .item:nth-child(2) ~ .item' => 'margin-top: {{SIZE}}{{UNIT}};',
				'{{WRAPPER}}.civi-list-layout-inline .item'                                                                             => 'margin-bottom: {{SIZE}}{{UNIT}};',
			],
		]);

		$this->add_control('width', [
			'label'      => esc_html__('Width', 'civi'),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => ['%', 'px'],
			'range'      => [
				'%'  => [
					'max'  => 100,
					'step' => 1,
				],
				'px' => [
					'max'  => 1000,
					'step' => 1,
				],
			],
			'selectors'  => [
				'{{WRAPPER}} .civi-list' => 'width: {{SIZE}}{{UNIT}};',
			],
		]);

		$this->add_responsive_control('alignment', [
			'label'     => esc_html__('Alignment', 'civi'),
			'type'      => Controls_Manager::CHOOSE,
			'options'   => Widget_Utils::get_control_options_horizontal_alignment(),
			'default'   => '',
			'selectors' => [
				'{{WRAPPER}}' => 'text-align: {{VALUE}};',
			],
		]);

		$this->add_responsive_control('text_align', [
			'label'     => esc_html__('Text Align', 'civi'),
			'type'      => Controls_Manager::CHOOSE,
			'options'   => Widget_Utils::get_control_options_text_align(),
			'default'   => '',
			'selectors' => [
				'{{WRAPPER}} .item' => 'text-align: {{VALUE}};',
			],
		]);

		$this->end_controls_section();
	}

	private function add_heading_style_section()
	{
		$this->start_controls_section('heading_style_section', [
			'label' => esc_html__('Heading', 'civi'),
			'tab'   => Controls_Manager::TAB_STYLE,
			'condition' => [
				'enable_toggle!' => '',
			],
		]);

		$this->add_group_control(Group_Control_Typography::get_type(), [
			'name'     => 'heading_typography',
			'label'    => esc_html__('Typography', 'civi'),
			'selector' => '{{WRAPPER}} .heading',
		]);

		$this->add_control('heading_spacing', [
			'label'      => esc_html__('Spacing', 'civi'),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => ['px'],
			'range'      => [
				'px' => [
					'max'  => 100,
					'step' => 1,
				],
			],
			'selectors'  => [
				'{{WRAPPER}} .list-inner' => 'margin-top: {{SIZE}}{{UNIT}};',
			],
		]);

		$this->start_controls_tabs('heading_style_tabs');

		$this->start_controls_tab('heading_style_normal_tab', [
			'label' => esc_html__('Normal', 'civi'),
		]);

		$this->add_group_control(Group_Control_Text_Gradient::get_type(), [
			'name'     => 'heading',
			'selector' => '{{WRAPPER}} .heading',
		]);

		$this->end_controls_tab();

		$this->start_controls_tab('heading_style_hover_tab', [
			'label' => esc_html__('Hover', 'civi'),
		]);

		$this->add_group_control(Group_Control_Text_Gradient::get_type(), [
			'name'     => 'hover_heading',
			'selector' => '{{WRAPPER}} .heading:hover',
		]);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	private function add_text_style_section()
	{
		$this->start_controls_section('text_style_section', [
			'label' => esc_html__('Text', 'civi'),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

		$this->add_group_control(Group_Control_Typography::get_type(), [
			'name'     => 'text_typography',
			'label'    => esc_html__('Typography', 'civi'),
			'selector' => '{{WRAPPER}} .text',
		]);

		$this->start_controls_tabs('text_style_tabs');

		$this->start_controls_tab('text_style_normal_tab', [
			'label' => esc_html__('Normal', 'civi'),
		]);

		$this->add_group_control(Group_Control_Text_Gradient::get_type(), [
			'name'     => 'text',
			'selector' => '{{WRAPPER}} .text',
		]);

		$this->end_controls_tab();

		$this->start_controls_tab('text_style_hover_tab', [
			'label' => esc_html__('Hover', 'civi'),
		]);

		$this->add_group_control(Group_Control_Text_Gradient::get_type(), [
			'name'     => 'hover_text',
			'selector' => '{{WRAPPER}} .link:hover .text',
		]);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	private function add_icon_style_section()
	{
		$this->start_controls_section('icon_style_section', [
			'label' => esc_html__('Icon', 'civi'),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

		$this->add_responsive_control('icon_space', [
			'label'     => esc_html__('Spacing', 'civi'),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [
				'px' => [
					'min' => 0,
					'max' => 100,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .icon' => 'margin-right: {{SIZE}}{{UNIT}};',
			],
		]);

		$this->add_responsive_control('icon_size', [
			'label'     => esc_html__('Size', 'civi'),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [
				'px' => [
					'min' => 3,
					'max' => 20,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .icon' => 'font-size: {{SIZE}}{{UNIT}};',
			],
		]);

		$this->add_responsive_control('min_width', [
			'label'          => esc_html__('Min Width', 'civi'),
			'type'           => Controls_Manager::SLIDER,
			'default'        => [
				'unit' => 'px',
			],
			'tablet_default' => [
				'unit' => 'px',
			],
			'mobile_default' => [
				'unit' => 'px',
			],
			'size_units'     => ['px', '%'],
			'range'          => [
				'%'  => [
					'min' => 1,
					'max' => 100,
				],
				'px' => [
					'min' => 0,
					'max' => 100,
				],
			],
			'selectors'      => [
				'{{WRAPPER}} .icon' => 'min-width: {{SIZE}}{{UNIT}};',
			],
		]);

		$this->start_controls_tabs('icon_style_tabs');

		$this->start_controls_tab('icon_style_normal_tab', [
			'label' => esc_html__('Normal', 'civi'),
		]);

		$this->add_group_control(Group_Control_Text_Gradient::get_type(), [
			'name'     => 'icon',
			'selector' => '{{WRAPPER}} .icon',
		]);

		$this->add_control('icon_marker_color', [
			'label'     => esc_html__('Icon Marker', 'civi'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}}.civi-list-style-icon-border .icon' => 'border-color: {{VALUE}};',
			],
			'condition' => [
				'style' => [
					'icon-border',
				],
			],
		]);

		$this->end_controls_tab();

		$this->start_controls_tab('icon_style_hover_tab', [
			'label' => esc_html__('Hover', 'civi'),
		]);

		$this->add_group_control(Group_Control_Text_Gradient::get_type(), [
			'name'     => 'hover_icon',
			'selector' => '{{WRAPPER}} .link:hover .icon',
		]);

		$this->add_control('hover_icon_marker_color', [
			'label'     => esc_html__('Icon Marker', 'civi'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}}.civi-list-style-icon-border .link:hover .icon' => 'border-color: {{VALUE}};',
			],
			'condition' => [
				'style' => [
					'icon-border',
				],
			],
		]);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function render()
	{
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute('wrapper', 'class', 'civi-list');
		if ($settings['enable_toggle'] == 'yes') {
			$this->add_render_attribute('wrapper', 'class', 'toggle');
		}
		$global_icon_html = '';
		if (!empty($settings['icon']['value'])) {
			$global_icon_html = '<div class="civi-icon icon">' . $this->get_render_icon($settings, $settings['icon'], ['aria-hidden' => 'true'], false, 'icon') . '</div>';
		}
?>
		<div <?php $this->print_attributes_string('wrapper'); ?>>
			<?php if ($settings['enable_toggle'] == 'yes') { ?>
				<h4 class="heading"><?php esc_html_e($settings['heading']); ?><i class="far fa-chevron-down"></i></h4>
				<div class="list-inner">
				<?php } ?>
				<?php if ($settings['items'] && count($settings['items']) > 0) {
					foreach ($settings['items'] as $key => $item) {
						$item_key = 'item_' . $item['_id'];
						$this->add_render_attribute($item_key, 'class', 'item');

						$link_tag = 'div';

						$item_link_key = 'item_link_' . $item['_id'];

						$this->add_render_attribute($item_link_key, 'class', 'link');

						if (!empty($item['link']['url'])) {
							$link_tag = 'a';
							$this->add_link_attributes($item_link_key, $item['link']);
						}
				?>
						<div <?php $this->print_attributes_string($item_key); ?>>

							<?php printf('<%1$s %2$s>', $link_tag, $this->get_render_attribute_string($item_link_key)); ?>

							<div class="list-header">
								<?php if (!empty($item['icon']['value'])) { ?>
									<div class="civi-icon icon">
										<?php $this->render_icon($settings, $item['icon'], ['aria-hidden' => 'true'], false, 'icon'); ?>
									</div>
								<?php } else { ?>
									<?php echo '' . $global_icon_html; ?>
								<?php } ?>

								<div class="text-wrap">
									<?php if (isset($item['text'])) { ?>
										<span class="text">
											<?php echo wp_kses_post($item['text']); ?>
										</span>
										<?php if (!empty($item['enable_badge'])) { ?>
											<span class="badge"><?php echo $item['badge_text']; ?></span>
										<?php } ?>
									<?php } ?>
								</div>
							</div>

							<?php printf('</%1$s>', $link_tag); ?>

						</div>
				<?php
					}
				}
				?>
				<?php if ($settings['enable_toggle'] == 'yes') { ?>
				</div>
			<?php } ?>
		</div>
<?php
	}
}
