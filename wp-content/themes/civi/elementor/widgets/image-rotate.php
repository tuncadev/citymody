<?php

namespace Civi_Elementor;

use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Utils;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Typography;
use Elementor\Core\Schemes\Color;

defined('ABSPATH') || exit;

class Widget_Image_Rotate extends Base
{

	public function get_name()
	{
		return 'civi-image-rotate';
	}

	public function get_title()
	{
		return esc_html__('Image Rotate', 'civi');
	}

	public function get_icon_part()
	{
		return 'eicon-image-rollover';
	}

	public function get_keywords()
	{
		return ['image', 'photo', 'box'];
	}

    public function get_style_depends()
    {
        return ['civi-el-widget-image-rotate'];
    }

	protected function register_controls()
	{
		$this->add_image_Rotate_section();

		$this->add_box_style_section();

		$this->add_image_style_section();

		$this->add_content_style_section();
	}

	private function add_image_rotate_section()
	{
		$this->start_controls_section('image_section', [
			'label' => esc_html__('Image Rotate', 'civi'),
		]);

		$this->add_control('postion', [
			'label'     => esc_html__('Position Animation', 'civi'),
			'type'      => Controls_Manager::CHOOSE,
			'default'   => 'top',
			'options'   => [
				'top'   => [
					'title' => esc_html__('Top', 'civi'),
					'icon'  => 'eicon-v-align-top',
				],
				'left' => [
					'title' => esc_html__('Left', 'civi'),
					'icon'  => 'eicon-h-align-right',
				],
			],
			'toggle'    => false,
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

		$this->add_control('title_text', [
			'label'       => esc_html__('Heading', 'civi'),
			'type'        => Controls_Manager::TEXT,
			'dynamic'     => [
				'active' => true,
			],
			'default'     => esc_html__('This is the heading', 'civi'),
			'placeholder' => esc_html__('Enter your title', 'civi'),
			'label_block' => true,
		]);

		$this->add_control('description_text', [
			'label'       => esc_html__('Description', 'civi'),
			'type'        => Controls_Manager::TEXTAREA,
			'dynamic'     => [
				'active' => true,
			],
			'default'     => esc_html__('Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'civi'),
			'placeholder' => esc_html__('Enter your description', 'civi'),
			'separator'   => 'none',
			'rows'        => 10,
			'label_block' => true,
		]);

		$this->add_control('title_size', [
			'label'   => esc_html__('HTML Tag', 'civi'),
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

		$this->end_controls_section();
	}

	private function add_box_style_section()
	{
		$this->start_controls_section('box_style_section', [
			'label' => esc_html__('Box', 'civi'),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

		$this->add_control('boxx_border_radius', [
			'label'      => esc_html__('Border Radius', 'civi'),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%'],
			'selectors'  => [
				'{{WRAPPER}} .image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				'{{WRAPPER}} .content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);

		$this->add_responsive_control('text_align', [
			'label'     => esc_html__('Alignment', 'civi'),
			'type'      => Controls_Manager::CHOOSE,
			'options'   => Widget_Utils::get_control_options_text_align_full(),
			'selectors' => [
				'{{WRAPPER}} .content' => 'text-align: {{VALUE}};',
			],
		]);

		$this->add_responsive_control('box_padding', [
			'label'      => esc_html__('Padding', 'civi'),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%', 'em'],
			'selectors'  => [
				'{{WRAPPER}} .civi-box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'{{WRAPPER}} .civi-box' => 'max-width: {{SIZE}}{{UNIT}};',
			],
		]);

		$this->add_responsive_control('box_horizontal_alignment', [
			'label'                => esc_html__('Horizontal Alignment', 'civi'),
			'label_block'          => true,
			'type'                 => Controls_Manager::CHOOSE,
			'options'              => Widget_Utils::get_control_options_horizontal_alignment(),
			'default'              => 'center',
			'toggle'               => false,
			'selectors_dictionary' => [
				'left'  => 'flex-start',
				'right' => 'flex-end',
			],
			'selectors'            => [
				'{{WRAPPER}} .elementor-widget-container' => 'display: flex; justify-content: {{VALUE}}',
			],
		]);

		$this->start_controls_tabs('box_colors');

		$this->start_controls_tab('box_colors_normal', [
			'label' => esc_html__('Normal', 'civi'),
		]);

		$this->add_group_control(Group_Control_Background::get_type(), [
			'name'     => 'box',
			'selector' => '{{WRAPPER}} .civi-box',
		]);

		$this->add_group_control(Group_Control_Advanced_Border::get_type(), [
			'name'     => 'box_border',
			'selector' => '{{WRAPPER}} .civi-box',
		]);

		$this->add_group_control(Group_Control_Box_Shadow::get_type(), [
			'name'     => 'box',
			'selector' => '{{WRAPPER}} .civi-box',
		]);

		$this->end_controls_tab();

		$this->start_controls_tab('box_colors_hover', [
			'label' => esc_html__('Hover', 'civi'),
		]);

		$this->add_group_control(Group_Control_Background::get_type(), [
			'name'     => 'box_hover',
			'selector' => '{{WRAPPER}} .civi-box:before',
		]);

		$this->add_group_control(Group_Control_Advanced_Border::get_type(), [
			'name'     => 'box_hover_border',
			'selector' => '{{WRAPPER}} .civi-box:hover',
		]);

		$this->add_group_control(Group_Control_Box_Shadow::get_type(), [
			'name'     => 'box_hover',
			'selector' => '{{WRAPPER}} .civi-box:hover',
		]);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	private function add_image_style_section()
	{
		$this->start_controls_section('image_style_section', [
			'label' => esc_html__('Image', 'civi'),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

		$this->add_control('image_border_radius', [
			'label'      => esc_html__('Border Radius', 'civi'),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%'],
			'selectors'  => [
				'{{WRAPPER}} .image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);

		$this->add_group_control(Group_Control_Box_Shadow::get_type(), [
			'name'     => 'image_shadow',
			'selector' => '{{WRAPPER}} .image img',
		]);

		$this->add_group_control(Group_Control_Css_Filter::get_type(), [
			'name'     => 'css_filters',
			'selector' => '{{WRAPPER}} .image img',
		]);

		$this->add_control('image_opacity', [
			'label'     => esc_html__('Opacity', 'civi'),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [
				'px' => [
					'max'  => 1,
					'min'  => 0.10,
					'step' => 0.01,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .image img' => 'opacity: {{SIZE}};',
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

		$this->add_responsive_control('conent_padding', [
			'label'      => esc_html__('Padding', 'civi'),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%', 'em'],
			'selectors'  => [
				'{{WRAPPER}} .content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);

		$this->add_responsive_control('content_horizontal_alignment', [
			'label'                => esc_html__('Horizontal Alignment', 'civi'),
			'label_block'          => true,
			'type'                 => Controls_Manager::CHOOSE,
			'options'              => Widget_Utils::get_control_options_vertical_alignment(),
			'default'              => 'center',
			'toggle'               => false,
			'selectors_dictionary' => [
				'top'  => 'flex-start',
				'middle'  => 'center',
				'bottom' => 'flex-end',
			],
			'selectors'            => [
				'{{WRAPPER}} .content' => 'display: flex;flex-direction: column;justify-content: {{VALUE}}',
			],
		]);

		$this->add_group_control(Group_Control_Background::get_type(), [
			'name'     => 'content',
			'selector' => '{{WRAPPER}} .content',
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

		$this->add_group_control(Group_Control_Typography::get_type(), [
			'name'     => 'title_typography',
			'selector' => '{{WRAPPER}} .title',
			'scheme'   => Typography::TYPOGRAPHY_1,
		]);

		$this->add_control('heading_description', [
			'label'     => esc_html__('Description', 'civi'),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		]);

		$this->add_responsive_control('description_top_space', [
			'label'     => esc_html__('Spacing', 'civi'),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [
				'px' => [
					'min' => 0,
					'max' => 100,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .description' => 'margin-top: {{SIZE}}{{UNIT}};',
			],
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
			'scheme'   => Typography::TYPOGRAPHY_3,
		]);

		$this->end_controls_section();
	}

	protected function render()
	{
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute('wrapper', 'class', 'civi-image-rotate civi-box');
		$this->add_render_attribute('wrapper', 'class', 'postion-' . $settings['postion']);

		$box_tag = 'div';
?>
		<?php printf('<%1$s %2$s>', $box_tag, $this->get_render_attribute_string('wrapper')); ?>
		<div class="content-wrap">

			<?php if (!empty($settings['image']['url'])) : ?>
				<div class="layer civi-image image">
					<?php echo \Civi_Image::get_elementor_attachment([
						'settings' => $settings,
					]); ?>
				</div>
			<?php endif; ?>

			<div class="layer content">
				<?php $this->print_title($settings); ?>

				<?php $this->print_description($settings); ?>
			</div>

		</div>
		<?php printf('</%1$s>', $box_tag); ?>
	<?php
	}

	private function print_title(array $settings)
	{
		if (empty($settings['title_text'])) {
			return;
		}

		$this->add_render_attribute('title_text', 'class', 'title');

		$this->add_inline_editing_attributes('title_text', 'none');

		$title_html = $settings['title_text'];

		printf('<%1$s %2$s>%3$s</%1$s>', $settings['title_size'], $this->get_render_attribute_string('title_text'), $title_html);
	}

	private function print_description(array $settings)
	{
		if (empty($settings['description_text'])) {
			return;
		}

		$this->add_render_attribute('description_text', 'class', 'description');
		$this->add_inline_editing_attributes('description_text');
	?>
		<div <?php $this->print_render_attribute_string('description_text'); ?>>
			<?php echo wp_kses($settings['description_text'], 'civi-default'); ?>
		</div>
<?php
	}
}
