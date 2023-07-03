<?php

namespace Civi_Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use Elementor\Utils;
use Elementor\Icons_Manager;
use Elementor\Modules\DynamicTags\Module as TagsModule;

defined('ABSPATH') || exit;

class Widget_Banner extends Base
{

	public function get_name()
	{
		return 'civi-banner';
	}

	public function get_title()
	{
		return esc_html__('Banner', 'civi');
	}

	public function get_icon_part()
	{
		return 'eicon-image-rollover';
	}

	public function get_keywords()
	{
		return ['banner'];
	}

    public function get_style_depends()
    {
        return ['civi-el-widget-banner'];
    }

	protected function register_controls()
	{
		$this->add_layout_section();
		$this->add_content_section();
		$this->add_button_settings_section();

		$this->add_box_style_section();
		$this->add_content_style_section();
		$this->add_button_style_section();
	}

	private function add_layout_section()
	{
		$this->start_controls_section('layout_section', [
			'label' => esc_html__('Layout', 'civi'),
		]);

		$this->add_control('style', [
			'label'   => esc_html__('Style', 'civi'),
			'type'    => Controls_Manager::SELECT,
			'options' => [
				'01' => '01',
				'02' => '02',
				'03' => '03',
				'04' => '04',
			],
			'default' => '01',
		]);

		$this->add_control('hover_effect', [
			'label'        => esc_html__('Hover Effect', 'civi'),
			'type'         => Controls_Manager::SELECT,
			'options'      => [
				''         => esc_html__('None', 'civi'),
				'zoom-in'  => esc_html__('Zoom In', 'civi'),
				'zoom-out' => esc_html__('Zoom Out', 'civi'),
				'move-up'  => esc_html__('Move Up', 'civi'),
			],
			'default'      => '',
			'prefix_class' => 'civi-animation-',
		]);

		$this->add_control('image', [
			'label'   => esc_html__('Choose Image', 'civi'),
			'type'    => Controls_Manager::MEDIA,
			'dynamic' => [
				'active' => true,
			],
			'default' => [
				'url' => Utils::get_placeholder_image_src(),
			],
		]);


		$this->add_control('size_mode', [
			'label' => esc_html__('Size Mode', 'civi'),
			'type' => Controls_Manager::SELECT,
			'options' => [
				'custom-mode' => esc_html__('Custom Mode', 'civi'),
				'custom-height' => esc_html__('Custom Height', 'civi'),
			],
			'default' => 'custom-height',
		]);

		$this->add_responsive_control(
			'size_width',
			[
				'label' => esc_html__('Custom Width', 'civi'),
				'type' => Controls_Manager::NUMBER,
				'default' => 1,
				'min' => 1,
				'condition' => [
					'size_mode' => 'custom-mode',
				],
				'selectors' => [
					'{{WRAPPER}} .civi-banner-bg ' => '--civi-custom-width: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'size_height',
			[
				'label' => esc_html__('Custom Height', 'civi'),
				'type' => Controls_Manager::NUMBER,
				'default' => 1,
				'min' => 1,
				'condition' => [
					'size_mode' => 'custom-mode',
				],
				'selectors' => [
					'{{WRAPPER}} .civi-banner-bg ' => 'padding-bottom:calc(({{VALUE}}/var(--civi-custom-width))*100%)',
				],
			]
		);

		$this->add_responsive_control(
			'size_custom_height',
			[
				'label' => esc_html__('Custom Height', 'civi'),
				'type' => Controls_Manager::NUMBER,
				'default' => 400,
				'min' => 1,
				'condition' => [
					'size_mode' => 'custom-height',
				],
				'selectors' => [
					'{{WRAPPER}} .civi-banner-bg ' => 'height: {{VALUE}}px',
				],
			]
		);

		$this->end_controls_section();
	}

	private function add_content_section()
	{
		$this->start_controls_section('content_section', [
			'label' => esc_html__('Content', 'civi'),
		]);

		$this->add_control('title_text', [
			'label'       => esc_html__('Title', 'civi'),
			'type'        => Controls_Manager::TEXT,
			'dynamic'     => [
				'active' => true,
			],
			'default'     => esc_html__('This is the heading', 'civi'),
			'placeholder' => esc_html__('Enter your title', 'civi'),
			'label_block' => true,
		]);

		$this->add_control('title_size', [
			'label'   => esc_html__('Title HTML Tag', 'civi'),
			'type'    => Controls_Manager::SELECT,
			'options' => [
				'h1'   => 'H1',
				'h2'   => 'H2',
				'h3'   => 'H3',
				'h4'   => 'H4',
				'h5'   => 'H5',
				'h6'   => 'H6',
				'div'  => 'div',
				'span' => 'span',
				'p'    => 'p',
			],
			'default' => 'h3',
		]);

		$this->add_control('sub_title', [
			'label'       => esc_html__('Sub Title', 'civi'),
			'type'        => Controls_Manager::TEXT,
			'default'     => esc_html__('Sub Heading', 'civi'),
		]);

		$this->add_control('description', [
			'label' => esc_html__('Description', 'civi'),
			'default' => esc_html__('Description on the Banner', 'civi'),
			'type' => Controls_Manager::WYSIWYG,
		]);

		$this->add_control('link', [
			'label'       => esc_html__('Link', 'civi'),
			'type'        => Controls_Manager::URL,
			'dynamic'     => [
				'active' => true,
			],
			'placeholder' => esc_html__('https://your-link.com', 'civi'),
			'separator'   => 'before',
		]);

		$this->end_controls_section();
	}

	private function add_button_settings_section()
	{
		$this->start_controls_section('button_settings_section', [
			'label' => esc_html__('Button', 'civi'),
		]);


		$this->add_control('button_style', [
			'label'   => esc_html__('Style', 'civi'),
			'type'    => Controls_Manager::SELECT,
			'default' => 'classic',
			'options' => Widget_Utils::get_button_style(),
		]);

		$this->add_control('button_text', [
			'label'       => esc_html__('Text', 'civi'),
			'type'        => Controls_Manager::TEXT,
			'dynamic'     => [
				'active' => true,
			],
			'default'     => esc_html__('Click here', 'civi'),
		]);

		$this->add_control('button_icon', [
			'label'       => esc_html__('Icon', 'civi'),
			'type'        => Controls_Manager::ICONS,
			'label_block' => true,
		]);

		$this->end_controls_section();
	}

	private function add_box_style_section()
	{
		$this->start_controls_section('box_style_section', [
			'label' => esc_html__('Box', 'civi'),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'box_background',
				'label' => esc_html__('Background', 'civi'),
				'types' => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .civi-box .content',
			]
		);

		$this->add_responsive_control('text_align', [
			'label'     => esc_html__('Alignment', 'civi'),
			'type'      => Controls_Manager::CHOOSE,
			'options'   => Widget_Utils::get_control_options_text_align_full(),
			'selectors' => [
				'{{WRAPPER}} .civi-box' => 'text-align: {{VALUE}};',
			],
		]);

		$this->add_responsive_control('box_padding', [
			'label'      => esc_html__('Padding', 'civi'),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%', 'em'],
			'selectors'  => [
				'{{WRAPPER}} .civi-box .content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);

		$this->add_responsive_control('box_border_radius', [
			'label'      => esc_html__('Border Radius', 'civi'),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%', 'em'],
			'selectors'  => [
				'{{WRAPPER}} .civi-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);

		$this->add_responsive_control('box_max_width', [
			'label'      => esc_html__('Max Width', 'civi'),
			'type'       => Controls_Manager::SLIDER,
			'default'    => [
				'unit' => 'px',
			],
			'size_units' => ['px', '%'],
			'range'      => [
				'%'  => [
					'min' => 1,
					'max' => 100,
				],
				'px' => [
					'min' => 1,
					'max' => 1600,
				],
			],
			'selectors'  => [
				'{{WRAPPER}} .civi-box .content' => 'max-width: {{SIZE}}{{UNIT}};',
			],
		]);

		$this->end_controls_section();
	}

	private function add_content_style_section()
	{
		$this->start_controls_section('content_style_section', [
			'label' => esc_html__('Content', 'civi'),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

		$this->add_control('heading_title', [
			'label'     => esc_html__('Title', 'civi'),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		]);

		$this->add_control('title_color', [
			'label'     => esc_html__('Color', 'civi'),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				'{{WRAPPER}} .title' => 'color: {{VALUE}};',
			],
		]);


		$this->add_control('title_hover_color', [
			'label'     => esc_html__('Hover Color', 'civi'),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				'{{WRAPPER}} .title:hover' => 'color: {{VALUE}};',
			],
		]);

		$this->add_group_control(Group_Control_Typography::get_type(), [
			'name'     => 'title_typography',
			'selector' => '{{WRAPPER}} .title',
		]);

		$this->add_control('sub_title_style', [
			'label'     => esc_html__('Sub Title', 'civi'),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		]);

		$this->add_control('sub_title_color', [
			'label'     => esc_html__('Color', 'civi'),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				'{{WRAPPER}} .sub-title' => 'color: {{VALUE}};',
			],

		]);

		$this->add_group_control(Group_Control_Typography::get_type(), [
			'name'     => 'sub_title_typography',
			'selector' => '{{WRAPPER}} .sub-title',
		]);

		$this->add_responsive_control('sub_title_spacing', [
			'label' => esc_html__('Spacing', 'civi'),
			'type' => Controls_Manager::SLIDER,
			'selectors' => [
				'{{WRAPPER}} .sub-title' => 'margin-bottom: {{SIZE}}px',
			],
		]);

		$this->add_control('description_style', [
			'label'     => esc_html__('Description', 'civi'),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		]);

		$this->add_control('description_color', [
			'label'     => esc_html__('Color', 'civi'),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => [
				'{{WRAPPER}} .description' => 'color: {{VALUE}};',
			],
		]);

		$this->add_group_control(Group_Control_Typography::get_type(), [
			'name'     => 'description_typography',
			'selector' => '{{WRAPPER}} .description',
		]);

		$this->add_responsive_control('description_spacing', [
			'label' => esc_html__('Spacing', 'civi'),
			'type' => Controls_Manager::SLIDER,
			'selectors' => [
				'{{WRAPPER}} .description' => 'margin-top: {{SIZE}}px',
			],
		]);


		$this->end_controls_section();
	}

	private function add_button_style_section()
	{
		$this->start_controls_section('button_style_section', [
			'label' => esc_html__('Button', 'civi'),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

		$this->add_group_control(Group_Control_Typography::get_type(), [
			'name'     => 'button_typography',
			'selector' => '{{WRAPPER}} .civi-button .button-text',
		]);

		$this->start_controls_tabs('button_text_style_tabs');

		$this->start_controls_tab('button_text_style_normal_tab', [
			'label' => esc_html__('Normal', 'civi'),
		]);

		$this->add_group_control(Group_Control_Text_Gradient::get_type(), [
			'name'     => 'button_text_color',
			'selector' => '{{WRAPPER}} .civi-button .button-text',
		]);

		$this->add_control('button_background_color', [
			'label'     => esc_html__('Background', 'civi'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .civi-button' => 'background-color: {{VALUE}};',
			],
		]);

		$this->add_control('button_border_color', [
			'label'     => esc_html__('Border', 'civi'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .civi-button' => 'border-color: {{VALUE}};',
			],
		]);

		$this->add_control('button_line_color', [
			'label'     => esc_html__('Line', 'civi'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .civi-button.button-border-bottom:after' => 'background: {{VALUE}};',
			],
			'condition' => [
				'button_style' => 'border-bottom',
			],
		]);

		$this->end_controls_tab();

		$this->start_controls_tab('button_style_hover_tab', [
			'label' => esc_html__('Hover', 'civi'),
		]);

		$this->add_group_control(Group_Control_Text_Gradient::get_type(), [
			'name'     => 'hover_text',
			'selector' => '{{WRAPPER}} .civi-button:hover .button-text',
		]);

		$this->add_control('hover_button_background_color', [
			'label'     => esc_html__('Background', 'civi'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .civi-button:hover' => 'background-color: {{VALUE}};',
			],
		]);

		$this->add_control('hover_button_border_color', [
			'label'     => esc_html__('Border', 'civi'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .civi-button:hover' => 'border-color: {{VALUE}};',
			],
		]);

		$this->add_control('hover_button_line_color', [
			'label'     => esc_html__('Line', 'civi'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .civi-button.button-border-bottom:after' => 'background: {{VALUE}};',
			],
			'condition' => [
				'button_style' => 'border-bottom',
			],
		]);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control('button_style_spacing', [
			'label'     => esc_html__('Spacing', 'civi'),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [
				'px' => [
					'max' => 50,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .civi-button-wrapper'  => 'margin-top: {{SIZE}}{{UNIT}};',
			],
		]);


		$this->add_control('button_style_height', [
			'label'     => esc_html__('Height', 'civi'),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [
				'px' => [
					'max' => 50,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .civi-button'  => 'min-height: {{SIZE}}{{UNIT}};',
				'{{WRAPPER}} .civi-button'  => 'line-height: {{SIZE}}{{UNIT}};',
			],
		]);

		$this->add_responsive_control('button_style_padding', [
			'label'      => esc_html__('Padding', 'civi'),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%', 'em'],
			'selectors'  => [
				'{{WRAPPER}} .civi-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);

		$this->add_responsive_control('button_style_border_radius', [
			'label'      => esc_html__('Border Radius', 'civi'),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%', 'em'],
			'selectors'  => [
				'{{WRAPPER}} .civi-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);

		$this->add_control('title_icon', [
			'label'     => esc_html__('Icon', 'civi'),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		]);

		$this->add_control('icon_align', [
			'label'       => esc_html__('Position', 'civi'),
			'type'        => Controls_Manager::CHOOSE,
			'options'     => [
				'left'  => [
					'title' => esc_html__('Left', 'civi'),
					'icon'  => 'eicon-h-align-left',
				],
				'right' => [
					'title' => esc_html__('Right', 'civi'),
					'icon'  => 'eicon-h-align-right',
				],
			],
			'default'     => 'left',
		]);

		$this->add_control('icon_indent', [
			'label'     => esc_html__('Spacing', 'civi'),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [
				'px' => [
					'max' => 50,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .civi-button.icon-left .button-icon'  => 'margin-right: {{SIZE}}{{UNIT}};',
				'{{WRAPPER}} .civi-button.icon-right .button-icon' => 'margin-left: {{SIZE}}{{UNIT}};',
			],
		]);

		$this->add_responsive_control('icon_font_size', [
			'label'     => esc_html__('Font Size', 'civi'),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [
				'px' => [
					'min' => 8,
					'max' => 30,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .civi-button .button-icon' => 'font-size: {{SIZE}}{{UNIT}};',
			],
		]);

		$this->start_controls_tabs('icon_skin_tabs');

		$this->start_controls_tab('icon_skin_normal_tab', [
			'label' => esc_html__('Normal', 'civi'),
		]);

		$this->add_group_control(Group_Control_Text_Gradient::get_type(), [
			'name'     => 'icon',
			'selector' => '{{WRAPPER}} .civi-button .button-icon',
		]);

		$this->end_controls_tab();

		$this->start_controls_tab('icon_skin_hover_tab', [
			'label' => esc_html__('Hover', 'civi'),
		]);

		$this->add_group_control(Group_Control_Text_Gradient::get_type(), [
			'name'     => 'icon_hover',
			'selector' => '{{WRAPPER}} .civi-button:hover .button-icon',
		]);


		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function render()
	{
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute('wrapper', 'class', 'civi-banner civi-box');
		$this->add_render_attribute('wrapper', 'class', 'style-' . $settings['style']);

		if ($settings['image']['url'] !== '') {
			$bg_style = "background-image : url({$settings['image']['url']})";
			$this->add_render_attribute('bg_attr', array(
				'class' => 'civi-banner-bg image',
				'style' => $bg_style,
			));
		} ?>
		<div <?php echo $this->get_render_attribute_string('wrapper') ?>>
			<div class="content-wrap">
				<div class="civi-image">
					<div <?php echo $this->get_render_attribute_string('bg_attr') ?>></div>
				</div>
				<div class="content">
					<?php $this->print_sub_title($settings); ?>
					<?php $this->print_title($settings); ?>
					<?php $this->print_description($settings); ?>
					<?php $this->print_button($settings); ?>
				</div>
			</div>
		</div>
	<?php
	}

	private function print_title(array $settings)
	{
		if (empty($settings['title_text'])) {
			return;
		}

		$box_tag = 'span';
		if (!empty($settings['link']['url'])) {
			$box_tag = 'a';
			$this->add_link_attributes('link', $settings['link']);
			$this->add_render_attribute('link', 'class', 'link-secret');
		}

		$this->add_render_attribute('title_text', 'class', 'title');

		$this->add_inline_editing_attributes('title_text', 'none');

		$title_html = $settings['title_text'];

		printf('<%1$s %2$s>', $settings['title_size'], $this->get_render_attribute_string('title_text'));
		printf('<%1$s %2$s>%3$s</%1$s>', $box_tag, $this->get_render_attribute_string('link'), $title_html);
		printf('</%1$s>', $settings['title_size']);
	}

	private function print_sub_title(array $settings)
	{
		if (empty($settings['sub_title'])) {
			return;
		}
		echo '<p class="sub-title">' . $settings['sub_title'] . '</p>';
	}

	private function print_description(array $settings)
	{
		if (empty($settings['description'])) {
			return;
		}
		echo '<div class="description">' . wp_kses_post($settings['description']) . '</div>';
	}

	private function print_button(array $settings)
	{
		if (empty($settings['button_text'])) {
			return;
		}

		$this->add_render_attribute('wrapper-buttton', 'class', 'civi-button-wrapper');

		$button_tag = 'div';
		if (!empty($settings['link']['url'])) {
			$this->add_link_attributes('button', $settings['link']);
			$this->add_render_attribute('button', 'class', 'button-has-link');
			$button_tag = 'a';
		}

		$this->add_render_attribute('button', 'class', 'civi-button');
		$this->add_render_attribute('button', 'role', 'button');

		if (!empty($settings['button_style'])) {
			$this->add_render_attribute('button', 'class', 'button-' . $settings['button_style']);
		}

		if (!empty($settings['size'])) {
			$this->add_render_attribute('button', 'class', 'button-' . $settings['button_size']);
		}

		if (!empty($settings['icon_align'])) {
			$this->add_render_attribute('button', 'class', 'icon-' . $settings['icon_align']);
		}

		$this->add_render_attribute([
			'button-content-wrapper' => [
				'class' => 'button-content-wrapper',
			],
			'button_icon'      => [
				'class' => [
					'button-icon',
				],
			],
			'button_text'            => [
				'class' => 'button-text',
			],
		]);

		$this->add_inline_editing_attributes('button_text', 'none');

	?>
		<div <?php $this->print_attributes_string('wrapper-buttton'); ?>>
			<?php printf('<%1$s %2$s>', $button_tag, $this->get_render_attribute_string('button')); ?>
			<div <?php $this->print_attributes_string('button-content-wrapper'); ?>>
				<?php if (!empty($settings['button_icon']['value']) && $settings['icon_align'] === 'left') : ?>
					<span <?php $this->print_attributes_string('button_icon'); ?>>
						<?php Icons_Manager::render_icon($settings['button_icon']); ?>
					</span>
				<?php endif; ?>

				<?php if (!empty($settings['button_text'])) : ?>
					<span <?php $this->print_attributes_string('button_text'); ?>><?php esc_html_e($settings['button_text']); ?></span>
				<?php endif; ?>

				<?php if (!empty($settings['button_icon']['value']) && $settings['icon_align'] === 'right') : ?>
					<span <?php $this->print_attributes_string('button_icon'); ?>>
						<?php Icons_Manager::render_icon($settings['button_icon']); ?>
					</span>
				<?php endif; ?>
			</div>
			<?php printf('</%1$s>', $button_tag); ?>
		</div>
<?php
	}
}
