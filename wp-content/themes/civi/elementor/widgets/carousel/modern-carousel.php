<?php

namespace Civi_Elementor;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Utils;

defined('ABSPATH') || exit;

class Widget_Modern_Carousel extends Carousel_Base
{

	public function get_name()
	{
		return 'civi-modern-carousel';
	}

	public function get_title()
	{
		return esc_html__('Modern Carousel', 'civi');
	}

	public function get_icon_part()
	{
		return 'eicon-posts-carousel';
	}

	public function get_keywords()
	{
		return ['modern', 'carousel'];
	}

	public function get_style_depends()
	{
		return ['civi-el-widget-modern-carousel'];
	}

	public function get_script_depends()
	{
		return ['civi-group-widget-carousel'];
	}

	protected function register_controls()
	{
		$this->add_content_section();

		$this->add_style_section();

		parent::register_controls();
	}

	protected function render()
	{
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute('wrapper', 'class', 'civi-modern-carousel');
?>
		<div <?php $this->print_attributes_string('wrapper'); ?>>
			<?php $this->print_slider($settings); ?>
		</div>
		<?php
	}

	private function add_content_section()
	{
		$this->start_controls_section('content_section', [
			'label' => esc_html__('Content', 'civi'),
		]);

		$this->add_control('style', [
			'label'        => esc_html__('Style', 'civi'),
			'type'         => Controls_Manager::SELECT,
			'options'      => array(
				'01' => '01',
				'02' => '02',
			),
			'default'      => '01',
			'prefix_class' => 'civi-modern-carousel-style-',
			'render_type'  => 'template',
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

		$this->add_responsive_control('height', [
			'label'          => esc_html__('Height', 'civi'),
			'type'           => Controls_Manager::SLIDER,
			'default'        => [
				'size' => 470,
				'unit' => 'px',
			],
			'tablet_default' => [
				'unit' => 'px',
			],
			'mobile_default' => [
				'unit' => 'px',
			],
			'size_units'     => ['px', '%', 'vh'],
			'range'          => [
				'%'  => [
					'min' => 1,
					'max' => 100,
				],
				'px' => [
					'min' => 1,
					'max' => 1600,
				],
				'vh' => [
					'min' => 1,
					'max' => 100,
				],
			],
			'selectors'      => [
				'{{WRAPPER}} .swiper-slide' => 'height: {{SIZE}}{{UNIT}};',
			],
			'render_type'    => 'template',
			'condition'      => [
				'style' => ['01'],
			],
		]);

		$this->add_group_control(Group_Control_Image_Size::get_type(), [
			'label'     => esc_html__('Image Size', 'civi'),
			'name'      => 'image',
			'default'   => 'full',
			'separator' => 'before',
		]);

		$repeater = new Repeater();

		$repeater->add_control('image', [
			'label'   => esc_html__('Image', 'civi'),
			'type'    => Controls_Manager::MEDIA,
			'default' => [
				'url' => Utils::get_placeholder_image_src(),
			],
		]);

		$repeater->add_control('title', [
			'label'       => esc_html__('Title', 'civi'),
			'type'        => Controls_Manager::TEXTAREA,
			'dynamic'     => [
				'active' => true,
			],
			'placeholder' => esc_html__('Enter your title', 'civi'),
			'default'     => esc_html__('Add Your Heading Text Here', 'civi'),
		]);

		$repeater->add_control('tags', [
			'label'       => esc_html__('Tags', 'civi'),
			'type'        => Controls_Manager::TEXTAREA,
			'placeholder' => esc_html__('One tag per line', 'civi'),
		]);

		$repeater->add_control('description', [
			'label' => esc_html__('Description', 'civi'),
			'type'  => Controls_Manager::TEXTAREA,
		]);

		$repeater->add_control('button_text', [
			'label' => esc_html__('Button Text', 'civi'),
			'type'  => Controls_Manager::TEXT,
		]);

		$repeater->add_control('link', [
			'label'       => esc_html__('Link', 'civi'),
			'type'        => Controls_Manager::URL,
			'dynamic'     => [
				'active' => true,
			],
			'placeholder' => esc_html__('https://your-link.com', 'civi'),
		]);

		$this->add_control('slides', [
			'label'       => esc_html__('Slides', 'civi'),
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $repeater->get_controls(),
			'default'     => [
				[
					'title'       => 'Automatic Updates',
					'tags'        => 'Design',
					'description' => 'Lorem ipsum dolor sit amet, consect etur elit. Suspe ndisse suscipit',
				],
				[
					'title'       => 'Flexible Options',
					'tags'        => 'Strategy',
					'description' => 'Lorem ipsum dolor sit amet, consect etur elit. Suspe ndisse suscipit',
				],
				[
					'title'       => 'Lifetime Use',
					'tags'        => 'Testing',
					'description' => 'Lorem ipsum dolor sit amet, consect etur elit. Suspe ndisse suscipit',
				],
			],
			'title_field' => '{{{ title }}}',
		]);

		$this->end_controls_section();
	}

	private function add_style_section()
	{
		$this->start_controls_section('style_section', [
			'label' => esc_html__('Style', 'civi'),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

		$this->add_control('slide_wrapper_heading', [
			'label' => esc_html__('Wrapper', 'civi'),
			'type'  => Controls_Manager::HEADING,
		]);

		$this->add_responsive_control('text_align', [
			'label'       => esc_html__('Text Align', 'civi'),
			'label_block' => true,
			'type'        => Controls_Manager::CHOOSE,
			'options'     => Widget_Utils::get_control_options_text_align(),
			'default'     => '',
			'selectors'   => [
				'{{WRAPPER}} .slide-content' => 'text-align: {{VALUE}};',
			],
		]);

		$this->add_responsive_control('slide_wrapper_padding', [
			'label'      => esc_html__('Padding', 'civi'),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%'],
			'selectors'  => [
				'{{WRAPPER}} .slide-layers' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);

		$this->add_responsive_control('box_border_radius', [
			'label'      => esc_html__('Border Radius', 'civi'),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%'],
			'selectors'  => [
				'{{WRAPPER}} .civi-box' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);

		$this->add_control('image_style_heading', [
			'label'     => esc_html__('Image', 'civi'),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		]);

		$this->add_responsive_control('image_border_radius', [
			'label'      => esc_html__('Border Radius', 'civi'),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%'],
			'selectors'  => [
				'{{WRAPPER}} .civi-image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);

		$this->add_control('title_style_heading', [
			'label'     => esc_html__('Title', 'civi'),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		]);

		$this->add_control('title_margin', [
			'label'      => esc_html__('Margin', 'civi'),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%'],
			'selectors'  => [
				'{{WRAPPER}} .title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);

		$this->add_group_control(Group_Control_Typography::get_type(), [
			'name'     => 'title_typography',
			'label'    => esc_html__('Typography', 'civi'),
			'selector' => '{{WRAPPER}} .title',
		]);

		$this->add_control('title_color', [
			'label'     => esc_html__('Color', 'civi'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .title' => 'color: {{VALUE}};',
			],
		]);

		$this->add_control('title_hover_color', [
			'label'     => esc_html__('Hover Color', 'civi'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .title:hover' => 'color: {{VALUE}};',
			],
		]);

		$this->add_control('description_style_heading', [
			'label'     => esc_html__('Description', 'civi'),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		]);

		$this->add_control('description_color', [
			'label'     => esc_html__('Text Color', 'civi'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .description' => 'color: {{VALUE}};',
			],
		]);

		$this->end_controls_section();
	}

	protected function print_slides(array $settings)
	{

		foreach ($settings['slides'] as $slide) :
			$slide_id = $slide['_id'];
			$item_key = 'item_' . $slide_id;
			$box_key = 'box_' . $slide_id;
			$box_tag = 'div';

			$this->add_render_attribute($item_key, 'class', [
				'swiper-slide',
				'elementor-repeater-item-' . $slide_id,
			]);

			$this->add_render_attribute($box_key, 'class', 'civi-box slide-wrapper');

			if (!empty($slide['link']['url'])) {
				$box_tag = 'a';
				$this->add_render_attribute($box_key, 'class', 'link-secret');
				$this->add_link_attributes($box_key, $slide['link']);
			}
		?>
			<div <?php $this->print_attributes_string($item_key); ?>>
				<?php printf('<%1$s %2$s>', $box_tag, $this->get_render_attribute_string($box_key)); ?>

				<?php if ('02' === $settings['style']) : ?>
					<div class="slide-image civi-image">
						<?php echo \Civi_Image::get_elementor_attachment([
							'settings'      => $slide,
							'size_settings' => $settings,
						]); ?>

						<div class="slide-overlay"></div>
					</div>

					<div class="slide-content">
						<div class="slide-layers">
							<?php if (!empty($slide['tags'])) : ?>
								<?php
								$tags = explode("\n", str_replace("\r", "", $slide['tags']));
								?>
								<div class="slide-tags">
									<?php foreach ($tags as $tag) : ?>
										<span class="slide-tag"><?php esc_html_e($tag); ?></span>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>

							<?php if (!empty($slide['title'])) : ?>
								<div class="slide-layer-wrap title-wrap">
									<div class="slide-layer">
										<h3 class="title"><?php echo wp_kses($slide['title'], 'civi-default'); ?></h3>
									</div>
								</div>
							<?php endif; ?>

							<?php if (!empty($slide['description'])) : ?>
								<div class="slide-layer-wrap description-wrap">
									<div class="slide-layer">
										<div class="description"><?php esc_html_e($slide['description']); ?></div>
									</div>
								</div>
							<?php endif; ?>

							<?php if (!empty($slide['button_text'])) : ?>
								<div class="slide-layer-wrap button-wrap">
									<div class="slide-layer">
										<div class="slide-button right-icon">
											<div class="button-content-wrapper">
												<span class="button-text">
													<?php esc_html_e($slide['button_text']); ?>
												</span>
												<span class="button-icon">
													<i class="far fa-long-arrow-right"></i>
												</span>
											</div>
										</div>
									</div>
								</div>
							<?php endif; ?>
						</div>
					</div>
				<?php else : ?>
					<div class="slide-image civi-image">
						<?php echo \Civi_Image::get_elementor_attachment([
							'settings'      => $slide,
							'size_settings' => $settings,
						]); ?>

						<div class="slide-overlay"></div>

						<div class="slide-content">
							<div class="slide-layers">

								<?php if (!empty($slide['tags'])) : ?>
									<?php
									$tags = explode("\n", str_replace("\r", "", $slide['tags']));
									?>
									<div class="slide-tags">
										<?php foreach ($tags as $tag) : ?>
											<span class="slide-tag"><?php esc_html_e($tag); ?></span>
										<?php endforeach; ?>
									</div>
								<?php endif; ?>

								<?php if (!empty($slide['title'])) : ?>
									<div class="slide-layer-wrap title-wrap">
										<div class="slide-layer">
											<h3 class="title"><?php echo wp_kses($slide['title'], 'civi-default'); ?></h3>
										</div>
									</div>
								<?php endif; ?>

								<?php if (!empty($slide['description'])) : ?>
									<div class="slide-layer-wrap description-wrap">
										<div class="slide-layer">
											<div class="description"><?php esc_html_e($slide['description']); ?></div>
										</div>
									</div>
								<?php endif; ?>

								<?php if (!empty($slide['button_text'])) : ?>
									<div class="slide-layer-wrap button-wrap">
										<div class="slide-layer">
											<div class="slide-button right-icon">
												<div class="button-content-wrapper">
													<span class="button-text">
														<?php esc_html_e($slide['button_text']); ?>
													</span>
													<span class="button-icon">
														<i class="far fa-long-arrow-right"></i>
													</span>
												</div>
											</div>
										</div>
									</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
				<?php endif; ?>

				<?php printf('</%1$s>', $box_tag); ?>
			</div>
<?php endforeach;
	}
}
