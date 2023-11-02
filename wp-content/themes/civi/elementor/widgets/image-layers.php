<?php

namespace Civi_Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Repeater;

defined('ABSPATH') || exit;

class Widget_Image_Layers extends Base
{

	public function get_name()
	{
		return 'civi-image-layers';
	}

	public function get_title()
	{
		return esc_html__('Image Layers', 'civi');
	}

	public function get_icon_part()
	{
		return 'eicon-photo-library';
	}

	public function get_keywords()
	{
		return ['image', 'photo', 'visual', 'layer'];
	}

    public function get_style_depends()
    {
        return ['civi-el-widget-image-layers'];
    }

	protected function register_controls()
	{
		$this->add_artboard_section();

		$this->add_layers_section();

		$this->add_artboard_style_section();
	}

	private function add_artboard_section()
	{
		$this->start_controls_section('artboard_section', [
			'label' => esc_html__('Artboard', 'civi'),
		]);

		$this->add_responsive_control('width', [
			'label'      => esc_html__('Width', 'civi'),
			'type'       => Controls_Manager::SLIDER,
			'default'    => [
				'size' => 500,
				'unit' => 'px',
			],
			'size_units' => ['%', 'px'],
			'range'      => [
				'%'  => [
					'min' => 5,
					'max' => 100,
				],
				'px' => [
					'min' => 1,
					'max' => 1920,
				],
			],
			'selectors'  => [
				'{{WRAPPER}} .artboard' => 'width: {{SIZE}}{{UNIT}};',
			],
		]);

		$this->add_responsive_control('height', [
			'label'      => esc_html__('Height', 'civi'),
			'type'       => Controls_Manager::SLIDER,
			'default'    => [
				'size' => 500,
				'unit' => 'px',
			],
			'size_units' => ['%', 'px'],
			'range'      => [
				'%'  => [
					'min' => 5,
					'max' => 100,
				],
				'px' => [
					'min' => 1,
					'max' => 1000,
				],
			],
			'selectors'  => [
				'{{WRAPPER}} .artboard' => 'height: {{SIZE}}{{UNIT}};',
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

		$this->end_controls_section();
	}

	private function add_layers_section()
	{
		$this->start_controls_section('layers_section', [
			'label' => esc_html__('Layers', 'civi'),
		]);

		$repeater = new Repeater();

		$repeater->add_control('static', [
			'label' => esc_html__('Static Layer', 'civi'),
			'type'  => Controls_Manager::SWITCHER,
		]);

		$repeater->start_controls_tabs('layer_tabs');

		$repeater->start_controls_tab('layer_content_tab', [
			'label' => esc_html__('Image', 'civi'),
		]);

		$repeater->add_control('image', [
			'label' => esc_html__('Image', 'civi'),
			'type'  => Controls_Manager::MEDIA,
		]);

		$repeater->add_group_control(Group_Control_Image_Size::get_type(), [
			'name'      => 'image',
			'default'   => 'full',
			'separator' => 'before',
		]);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab('layer_position_tab', [
			'label' => esc_html__('Position', 'civi'),
		]);

		$repeater->add_responsive_control('width', [
			'label'      => esc_html__('Width', 'civi'),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => ['%', 'px'],
			'range'      => [
				'%'  => [
					'min' => 5,
					'max' => 100,
				],
				'px' => [
					'min' => 1,
					'max' => 1920,
				],
			],
			'selectors'  => [
				'{{WRAPPER}} {{CURRENT_ITEM}} .layer-content' => 'width: {{SIZE}}{{UNIT}};',
			],
		]);

		$repeater->add_responsive_control('height', [
			'label'      => esc_html__('Height', 'civi'),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => ['%', 'px'],
			'range'      => [
				'%'  => [
					'min' => 5,
					'max' => 100,
				],
				'px' => [
					'min' => 1,
					'max' => 1000,
				],
			],
			'selectors'  => [
				'{{WRAPPER}} {{CURRENT_ITEM}} .layer-content' => 'height: {{SIZE}}{{UNIT}};',
			],
		]);

		$repeater->add_responsive_control('horizontal_align', [
			'label'                => esc_html__('Horizontal Align', 'civi'),
			'type'                 => Controls_Manager::CHOOSE,
			'options'              => Widget_Utils::get_control_options_horizontal_alignment(),
			'default'              => 'center',
			'toggle'               => false,
			'selectors_dictionary' => [
				'left'  => 'flex-start',
				'right' => 'flex-end',
			],
			'selectors'            => [
				'{{WRAPPER}} {{CURRENT_ITEM}}' => 'justify-content: {{VALUE}}',
			],
			'render_type'          => 'template',
		]);

		$repeater->add_responsive_control('vertical_align', [
			'label'                => esc_html__('Vertical Align', 'civi'),
			'type'                 => Controls_Manager::CHOOSE,
			'options'              => Widget_Utils::get_control_options_vertical_alignment(),
			'default'              => 'middle',
			'toggle'               => false,
			'selectors_dictionary' => [
				'top'    => 'flex-start',
				'middle' => 'center',
				'bottom' => 'flex-end',
			],
			'selectors'            => [
				'{{WRAPPER}} {{CURRENT_ITEM}}' => 'align-items: {{VALUE}}',
			],
			'prefix_class'         => 'layer%s-v-align-',
			'render_type'          => 'template',
		]);

		$repeater->add_responsive_control('margin', [
			'label'      => esc_html__('Offset', 'civi'),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => ['px', '%', 'em'],
			'selectors'  => [
				'{{WRAPPER}} {{CURRENT_ITEM}} .layer-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
		]);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab('layer_style_tab', [
			'label' => esc_html__('Style', 'civi'),
		]);

		$repeater->add_group_control(Group_Control_Box_Shadow::get_type(), [
			'name'     => 'box_shadow',
			'selector' => '{{WRAPPER}} {{CURRENT_ITEM}} .layer-content',
		]);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab('layer_loop_tab', [
			'label' => esc_html__('Loop', 'civi'),
		]);

		$repeater->add_control('loop', [
			'label'   => esc_html__('Loop', 'civi'),
			'type'    => Controls_Manager::SELECT,
			'options' => [
				''                => esc_html__('None', 'civi'),
				'rotate'          => esc_html__('Rotate', 'civi'),
				'move-horizontal' => esc_html__('Move - Horizontal', 'civi'),
				'move-vertical'   => esc_html__('Move - Vertical', 'civi'),
			],
			'default' => '',
		]);

		$repeater->add_control('loop_speed', [
			'label'     => esc_html__('Transition Duration', 'civi'),
			'type'      => Controls_Manager::NUMBER,
			'default'   => 3000,
			'selectors' => [
				'{{WRAPPER}} {{CURRENT_ITEM}} .layer-loop' => 'animation-duration: {{VALUE}}ms;',
			],
		]);

		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		$this->add_control('layers', [
			'type'   => Controls_Manager::REPEATER,
			'fields' => $repeater->get_controls(),
		]);

		$this->end_controls_section();
	}

	private function add_artboard_style_section()
	{
		$this->start_controls_section('artboard_style_section', [
			'label' => esc_html__('Artboard', 'civi'),
			'tab'   => Controls_Manager::TAB_STYLE,
		]);

		$this->add_group_control(Group_Control_Box_Shadow::get_type(), [
			'name'     => 'artboard_box_shadow',
			'selector' => '{{WRAPPER}} .artboard',
		]);

		$this->end_controls_section();
	}

	protected function render()
	{
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute('artboard', 'class', 'artboard');
?>
		<div <?php $this->print_attributes_string('artboard'); ?>>
			<div class="layers-wrapper">
				<?php if (!empty($settings['layers'])) {

					$layer_index = 0;

					foreach ($settings['layers'] as $key => $layer) {
						$layer_index++;
						$layer_id          = $layer['_id'];
						$layer_key         = 'layer_' . $layer_id;
						$layer_content_key = 'layer_content_' . $layer_id;

						$this->add_render_attribute($layer_key, [
							'class' => [
								'layer',
								'elementor-repeater-item-' . $layer_id,
							],
							'style' => "z-index: {$layer_index}",
						]);

						if ('yes' === $layer['static']) {
							$this->add_render_attribute($layer_key, [
								'class' => 'static-layer',
							]);
						}

						$this->add_render_attribute($layer_content_key, [
							'class' => 'layer-content',
						]);

						if (!empty($layer['loop'])) {
							$this->add_render_attribute($layer_content_key, [
								'class' => 'layer-loop loop-' . $layer['loop'],
							]);
						}
				?>
						<div <?php $this->print_attributes_string($layer_key); ?>>
							<div <?php $this->print_attributes_string($layer_content_key); ?>>
								<?php echo \Civi_Image::get_elementor_attachment([
									'settings' => $layer,
								]); ?>
							</div>
						</div>
				<?php
					}
				}
				?>
			</div>
		</div>
<?php
	}
}
