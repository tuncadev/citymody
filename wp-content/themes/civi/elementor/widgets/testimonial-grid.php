<?php

namespace Civi_Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Image_Size;
use Elementor\Repeater;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;

defined('ABSPATH') || exit;

class Widget_Testimonial_Grid extends Base
{

	private $current_item_key;
	private $current_item;

	protected function get_current_key()
	{
		return $this->current_item_key;
	}

	protected function get_current_item()
	{
		return $this->current_item;
	}

	public function get_name()
	{
		return 'civi-testimonial-grid';
	}

	public function get_title()
	{
		return esc_html__('Testimonial Masonry', 'civi');
	}

	public function get_icon_part()
	{
		return ' eicon-posts-masonry';
	}

	public function get_keywords()
	{
		return ['testimonial', 'grid'];
	}

	public function get_script_depends()
	{
		return ['civi-group-widget-grid', 'civi-grid-layout'];
	}

	public function get_style_depends()
	{
		return ['civi-el-widget-testimonial-grid'];
	}

	protected function register_controls()
	{
		$this->add_layout_section();

		$this->add_content_section();

		$this->add_grid_section();

		$this->add_box_style_section();

		$this->add_thumbnail_style_section();

		$this->add_content_style_section();

		$this->add_avatar_style_section();
	}

	private function add_layout_section()
	{
		$this->start_controls_section('layout_section', [
			'label' => esc_html__('Layout', 'civi'),
		]);

		$this->add_control('layout', [
			'label'        => esc_html__('Layout', 'civi'),
			'type'         => Controls_Manager::SELECT,
			'default'      => 'image-stacked',
			'options'      => [
				'image-inline'  => esc_html__('Image Inline', 'civi'),
				'image-stacked' => esc_html__('Image Stacked', 'civi'),
				'image-above'   => esc_html__('Image Above', 'civi'),
			],
			'render_type'  => 'template',
			'prefix_class' => 'layout-',
		]);

		$this->add_control('image_position', [
			'label'        => esc_html__('Image Position', 'civi'),
			'type'         => Controls_Manager::CHOOSE,
			'label_block'  => false,
			'default'      => 'below',
			'options'      => [
				'above'  => [
					'title' => esc_html__('Above', 'civi'),
					'icon'  => 'eicon-v-align-top',
				],
				'below'  => [
					'title' => esc_html__('Below', 'civi'),
					'icon'  => 'eicon-v-align-bottom',
				],
				'bottom' => [
					'title' => esc_html__('Bottom', 'civi'),
					'icon'  => 'eicon-v-align-stretch',
				],
			],
			'render_type'  => 'template',
			'prefix_class' => 'image-position-',
		]);

		$this->add_control('hover_effect', [
			'label'        => esc_html__('Hover Effect', 'civi'),
			'type'         => Controls_Manager::SELECT,
			'options'      => [
				''         => esc_html__('None', 'civi'),
				'zoom-in'  => esc_html__('Zoom In', 'civi'),
				'zoom-out' => esc_html__('Zoom Out', 'civi'),
			],
			'default'      => '',
			'prefix_class' => 'civi-animation-',
		]);

		$this->end_controls_section();
	}

	private function add_grid_section()
	{
		$this->start_controls_section('grid_options_section', [
			'label' => esc_html__('Grid Options', 'civi'),
		]);

		$this->add_responsive_control('grid_columns', [
			'label'          => esc_html__('Columns', 'civi'),
			'type'           => Controls_Manager::NUMBER,
			'min'            => 1,
			'max'            => 12,
			'step'           => 1,
			'default'        => 3,
			'tablet_default' => 2,
			'mobile_default' => 1,
		]);

		$this->add_responsive_control('grid_gutter', [
			'label'   => esc_html__('Gutter', 'civi'),
			'type'    => Controls_Manager::NUMBER,
			'min'     => 0,
			'max'     => 200,
			'step'    => 1,
			'default' => 30,
		]);

		$this->end_controls_section();
	}

	private function add_content_section()
	{
		$this->start_controls_section('content_section', [
			'label' => esc_html__('Content', 'civi'),
		]);

		$repeater = new Repeater();

		$repeater->add_control('thumbnail', [
			'label' => esc_html__('Thumbnail', 'civi'),
			'type'  => Controls_Manager::MEDIA,
		]);

		$repeater->add_control('title', [
			'label'       => esc_html__('Title', 'civi'),
			'label_block' => true,
			'type'        => Controls_Manager::TEXT,
		]);

		$repeater->add_control('content', [
			'label' => esc_html__('Content', 'civi'),
			'type'  => Controls_Manager::TEXTAREA,
		]);

		$repeater->add_control('avatar', [
			'label' => esc_html__('Avatar', 'civi'),
			'type'  => Controls_Manager::MEDIA,
		]);

		$repeater->add_control('name', [
			'label'   => esc_html__('Name', 'civi'),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__('John Doe', 'civi'),
		]);

		$repeater->add_control('position', [
			'label'   => esc_html__('Position', 'civi'),
			'type'    => Controls_Manager::TEXT,
			'default' => esc_html__('CEO', 'civi'),
		]);

		$repeater->add_control('rating', [
			'label' => esc_html__('Rating', 'civi'),
			'type'  => Controls_Manager::NUMBER,
			'min'   => 0,
			'max'   => 5,
			'step'  => 0.1,
		]);

		$placeholder_image_src = Utils::get_placeholder_image_src();

		$this->add_control('items', [
			'label'       => esc_html__('Items', 'civi'),
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $repeater->get_controls(),
			'default'     => [
				[
					'thumbnail' => ['url' => $placeholder_image_src],
					'content'   => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'civi'),
					'name'      => esc_html__('John Doe', 'civi'),
					'position'  => esc_html__('CEO', 'civi'),
				],
				[
					'thumbnail' => ['url' => $placeholder_image_src],
					'content'   => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'civi'),
					'name'      => esc_html__('John Doe', 'civi'),
					'position'  => esc_html__('CEO', 'civi'),
				],
				[
					'thumbnail' => ['url' => $placeholder_image_src],
					'content'   => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'civi'),
					'name'      => esc_html__('John Doe', 'civi'),
					'position'  => esc_html__('CEO', 'civi'),
				],
			],
			'separator'   => 'after',
			'title_field' => '{{{ name }}}',
		]);

		$this->add_group_control(Group_Control_Image_Size::get_type(), [
			'name'           => 'thumbnail',
			'default'        => 'full',
			'fields_options' => [
				'size' => [
					'label' => esc_html__('Thumbnail Size', 'civi'),
				],
			],
		]);

		$this->add_group_control(Group_Control_Image_Size::get_type(), [
			'name'           => 'avatar',
			'default'        => 'thumbnail',
			'fields_options' => [
				'size' => [
					'label' => esc_html__('Avatar Size', 'civi'),
				],
			],
		]);

		$this->end_controls_section();
	}

	private function add_box_style_section()
	{
		$this->start_controls_section('box_style_section', [
			'label' => esc_html__('Box', 'civi'),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

		$this->add_responsive_control('box_alignment', [
			'label'     => esc_html__('Alignment', 'civi'),
			'type'      => Controls_Manager::CHOOSE,
			'options'   => Widget_Utils::get_control_options_text_align(),
			'selectors' => [
				'{{WRAPPER}} .grid-item' => 'text-align: {{VALUE}}',
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
				'{{WRAPPER}} .testimonial-item' => 'width: {{SIZE}}{{UNIT}};',
			],
		]);

		$this->add_responsive_control('box_padding', [
			'label'      => esc_html__('Padding', 'civi'),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%', 'em'],
			'selectors'  => [
				'{{WRAPPER}} .testimonial-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);

		$this->add_group_control(Group_Control_Background::get_type(), [
			'name'     => 'box',
			'selector' => '{{WRAPPER}} .testimonial-item',
		]);

		$this->end_controls_section();
	}

	private function add_thumbnail_style_section()
	{
		$this->start_controls_section('thumbnail_style_section', [
			'label' => esc_html__('Thumbnail', 'civi'),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

		$this->add_control('thumbnail_border_radius', [
			'label'     => esc_html__('Border Radius', 'civi'),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [
				'px' => [
					'min' => 0,
					'max' => 200,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .civi-image' => 'border-radius: {{SIZE}}{{UNIT}}',
			],
		]);

		$this->start_controls_tabs('thumbnail_effects_tabs');

		$this->start_controls_tab('thumbnail_effects_normal_tab', [
			'label' => esc_html__('Normal', 'civi'),
		]);

		$this->add_group_control(Group_Control_Box_Shadow::get_type(), [
			'name'     => 'thumbnail_box_shadow',
			'selector' => '{{WRAPPER}} .civi-image',
		]);

		$this->add_group_control(Group_Control_Css_Filter::get_type(), [
			'name'     => 'css_filters',
			'selector' => '{{WRAPPER}} .civi-image img',
		]);

		$this->add_control('thumbnail_opacity', [
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
				'{{WRAPPER}} .civi-image img' => 'opacity: {{SIZE}};',
			],
		]);

		$this->end_controls_tab();

		$this->start_controls_tab('thumbnail_effects_hover_tab', [
			'label' => esc_html__('Hover', 'civi'),
		]);

		$this->add_group_control(Group_Control_Box_Shadow::get_type(), [
			'name'     => 'thumbnail_box_shadow_hover',
			'selector' => '{{WRAPPER}} .civi-box:hover .civi-image',
		]);

		$this->add_group_control(Group_Control_Css_Filter::get_type(), [
			'name'     => 'css_filters_hover',
			'selector' => '{{WRAPPER}} .civi-box:hover .civi-image img',
		]);

		$this->add_control('thumbnail_opacity_hover', [
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
				'{{WRAPPER}} .civi-box:hover .civi-image img' => 'opacity: {{SIZE}};',
			],
		]);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	private function add_content_style_section()
	{
		$this->start_controls_section('content_style_section', [
			'label' => esc_html__('Content', 'civi'),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

		$this->add_responsive_control('content_max_width', [
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
				'{{WRAPPER}} .content-wrap' => 'width: {{SIZE}}{{UNIT}};',
			],
		]);

		$this->add_responsive_control('content_alignment', [
			'label'                => esc_html__('Alignment', 'civi'),
			'type'                 => Controls_Manager::CHOOSE,
			'options'              => Widget_Utils::get_control_options_horizontal_alignment(),
			'selectors_dictionary' => [
				'left'  => 'flex-start',
				'right' => 'flex-end',
			],
			'selectors'            => [
				'{{WRAPPER}} .testimonial-main-content' => 'justify-content: {{VALUE}}',
			],
		]);

		$this->add_control('content_text_align', [
			'label'        => esc_html__('Text Align', 'civi'),
			'label_block'  => false,
			'type'         => Controls_Manager::CHOOSE,
			'options'      => Widget_Utils::get_control_options_text_align(),
			'prefix_class' => 'align-',
			'selectors'    => [
				'{{WRAPPER}} .content-wrap' => 'text-align: {{VALUE}};',
			],
		]);

		$this->add_control('title_heading', [
			'label'     => esc_html__('Title', 'civi'),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		]);

		$this->add_control('title_color', [
			'label'     => esc_html__('Color', 'civi'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .title' => 'color: {{VALUE}};',
			],
		]);

		$this->add_group_control(Group_Control_Typography::get_type(), [
			'name'     => 'title_typography',
			'label'    => esc_html__('Typography', 'civi'),
			'selector' => '{{WRAPPER}} .title',
		]);

		$this->add_responsive_control('title_margin', [
			'label'      => esc_html__('Margin', 'civi'),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%', 'em'],
			'selectors'  => [
				'{{WRAPPER}} .title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);

		$this->add_control('text_heading', [
			'label'     => esc_html__('Text', 'civi'),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		]);

		$this->add_control('text_color', [
			'label'     => esc_html__('Color', 'civi'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .text' => 'color: {{VALUE}};',
			],
		]);

		$this->add_group_control(Group_Control_Typography::get_type(), [
			'name'     => 'text_typography',
			'label'    => esc_html__('Typography', 'civi'),
			'selector' => '{{WRAPPER}} .text',
		]);

		$this->add_control('name_heading', [
			'label'     => esc_html__('Name', 'civi'),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		]);

		$this->add_control('name_color', [
			'label'     => esc_html__('Color', 'civi'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .name' => 'color: {{VALUE}};',
			],
		]);

		$this->add_group_control(Group_Control_Typography::get_type(), [
			'name'     => 'name_typography',
			'label'    => esc_html__('Typography', 'civi'),
			'selector' => '{{WRAPPER}} .name',
		]);

		$this->add_control('position_heading', [
			'label'     => esc_html__('Position', 'civi'),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		]);

		$this->add_control('position_color', [
			'label'     => esc_html__('Color', 'civi'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .position' => 'color: {{VALUE}};',
			],
		]);

		$this->add_group_control(Group_Control_Typography::get_type(), [
			'name'     => 'position_typography',
			'label'    => esc_html__('Typography', 'civi'),
			'selector' => '{{WRAPPER}} .position',
		]);

		$this->end_controls_section();
	}

	private function add_avatar_style_section()
	{
		$this->start_controls_section('avatar_style_section', [
			'label' => esc_html__('Avatar', 'civi'),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

		$this->add_responsive_control('avatar_spacing', [
			'label'     => esc_html__('Spacing', 'civi'),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [
				'px' => [
					'min' => 0,
					'max' => 500,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .info' => 'padding-top: {{SIZE}}{{UNIT}};',
			],
		]);

		$this->add_responsive_control('avatar_img_size', [
			'label'     => esc_html__('Size', 'civi'),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [
				'px' => [
					'min' => 30,
					'max' => 200,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .avatar img' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
			],
		]);

		$this->end_controls_section();
	}

	protected function render()
	{
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute('wrapper', [
			'class' => [
				'civi-grid-wrapper',
			],
		]);

		$this->add_render_attribute('content-wrapper', 'class', 'civi-grid lazy-grid');

		$grid_options = $this->get_grid_options($settings);

		$this->add_render_attribute('wrapper', 'data-grid', wp_json_encode($grid_options));
?>
		<div <?php $this->print_attributes_string('wrapper'); ?>>
			<div <?php $this->print_attributes_string('content-wrapper'); ?>>
				<div class="grid-sizer"></div>
				<?php foreach ($settings['items'] as $item) : ?>
					<?php
					$item_id                = $item['_id'];
					$this->current_item     = $item;
					$this->current_item_key = 'item_' . $item_id;

					$this->add_render_attribute($this->get_current_key(), [
						'class' => [
							'grid-item',
							'elementor-repeater-item-' . $item_id,
						],
					]);
					?>
					<div <?php $this->print_attributes_string($this->get_current_key()); ?>>
						<?php
						$this->add_render_attribute($this->get_current_key() . '-testimonial', [
							'class' => 'civi-box testimonial-item',
						]);
						?>
						<div <?php $this->print_attributes_string($this->get_current_key() . '-testimonial'); ?>>
							<?php $this->print_testimonial_thumbnail(); ?>

							<?php $this->print_testimonial_main_content(); ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	<?php
	}

	protected function get_grid_options(array $settings)
	{
		$grid_options = [
			'type' => 'masonry',
		];

		// Columns.
		if (!empty($settings['grid_columns'])) {
			$grid_options['columns'] = $settings['grid_columns'];
		}

		if (!empty($settings['grid_columns_tablet'])) {
			$grid_options['columnsTablet'] = $settings['grid_columns_tablet'];
		}

		if (!empty($settings['grid_columns_mobile'])) {
			$grid_options['columnsMobile'] = $settings['grid_columns_mobile'];
		}

		// Gutter
		if (!empty($settings['grid_gutter'])) {
			$grid_options['gutter'] = $settings['grid_gutter'];
		}

		if (!empty($settings['grid_gutter_tablet'])) {
			$grid_options['gutterTablet'] = $settings['grid_gutter_tablet'];
		}

		if (!empty($settings['grid_gutter_mobile'])) {
			$grid_options['gutterMobile'] = $settings['grid_gutter_mobile'];
		}

		return $grid_options;
	}

	private function print_testimonial_rating($rating = 5)
	{
		$full_stars = intval($rating);
		$template   = '';

		$template .= str_repeat('<span class="fa fa-star"></span>', $full_stars);

		$half_star = floatval($rating) - $full_stars;

		if ($half_star != 0) {
			$template .= '<span class="fa fa-star-half-alt"></span>';
		}

		$empty_stars = intval(5 - $rating);
		$template    .= str_repeat('<span class="far fa-star"></span>', $empty_stars);

		echo '<div class="testimonial-rating">' . $template . '</div>';
	}

	private function print_testimonial_cite()
	{
		$item = $this->get_current_item();

		if (empty($item['name']) && empty($item['position'])) {
			return;
		}

		$html = '<div class="cite">';
		if (!empty($item['name'])) {
			$html .= '<h6 class="name">' . $item['name'] . '</h6>';
		}
		if (!empty($item['position'])) {
			$html .= '<span class="position">' . $item['position'] . '</span>';
		}
		$html .= '</div>';

		echo '' . $html;
	}

	private function print_testimonial_avatar()
	{
		$settings = $this->get_settings_for_display();
		$item     = $this->get_current_item();

		if (empty($item['avatar']['url'])) {
			return;
		}
	?>
		<div class="avatar">
			<?php echo \Civi_Image::get_elementor_attachment([
				'settings'       => $item,
				'image_key'      => 'avatar',
				'image_size_key' => 'avatar',
				'size_settings'  => $settings,
			]); ?>
		</div>
	<?php
	}

	private function print_testimonial_info()
	{
	?>
		<div class="info">
			<?php $this->print_testimonial_avatar(); ?>

			<?php $this->print_testimonial_cite(); ?>
		</div>
	<?php
	}

	private function print_testimonial_thumbnail()
	{
		$settings = $this->get_settings_for_display();
		$item     = $this->get_current_item();

		if (empty($item['thumbnail']['url'])) {
			return;
		}
	?>
		<div class="civi-image thumbnail">
			<?php echo \Civi_Image::get_elementor_attachment([
				'settings'       => $item,
				'image_key'      => 'thumbnail',
				'image_size_key' => 'thumbnail',
				'size_settings'  => $settings,
			]); ?>
		</div>
	<?php
	}

	private function print_testimonial_main_content()
	{
	?>
		<div class="testimonial-main-content">
			<div class="content-wrap">
				<?php $this->print_layout(); ?>
			</div>
		</div>
	<?php
	}

	private function print_layout()
	{
		$settings = $this->get_settings_for_display();
		$item     = $this->get_current_item();
	?>
		<?php if ('above' === $settings['image_position']) : ?>
			<?php $this->print_testimonial_info(); ?>
		<?php endif; ?>

		<?php if ($item['content']) : ?>
			<div class="content">
				<?php if (!empty($item['title'])) : ?>
					<h4 class="title"><?php esc_html_e($item['title']); ?></h4>
				<?php endif; ?>

				<?php if (!empty($item['rating'])) : ?>
					<?php $this->print_testimonial_rating($item['rating']); ?>
				<?php endif; ?>

				<div class="text">
					<?php echo wp_kses($item['content'], 'civi-default'); ?>
				</div>
			</div>
		<?php endif; ?>

		<?php if (in_array($settings['image_position'], array('below', 'bottom'), true)) : ?>
			<?php $this->print_testimonial_info(); ?>
		<?php endif; ?>

<?php
	}
}
