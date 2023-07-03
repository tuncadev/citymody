<?php

namespace Civi_Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;

defined('ABSPATH') || exit;

class Widget_Fancy_Heading extends Base
{

	public function get_name()
	{
		return 'civi-fancy-heading';
	}

	public function get_title()
	{
		return esc_html__('Fancy Heading', 'civi');
	}

	public function get_icon_part()
	{
		return 'eicon-heading';
	}

	public function get_keywords()
	{
		return ['Fancy Heading'];
	}

	public function get_script_depends()
	{
		return ['civi-widget-fancy-heading', 'typed'];
	}

    public function get_style_depends()
    {
        return ['civi-el-widget-fancy-heading'];
    }

	protected function register_controls()
	{
		$this->add_content_section();

		$this->add_content_settings_section();

		$this->add_content_style_section();

		$this->add_prefix_controls_section();
	}

	private function add_content_section()
	{
		$this->start_controls_section('fancy_heading_section', [
			'label' => esc_html__('Fancy Heading', 'civi'),
			'tab' => Controls_Manager::TAB_CONTENT,
		]);


		$this->add_responsive_control(
			'fancy_heading_align',
			[
				'label' => esc_html__('Alignment', 'civi'),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__('Left', 'civi'),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'civi'),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__('Right', 'civi'),
						'icon' => 'eicon-text-align-right',
					],
				],
				'prefix_class' => 'elementor%s-align-',
				'default' => '',
			]
		);

		$this->add_control('fancy_heading_tag', [
			'label' => esc_html__('HTML Tag', 'civi'),
			'type' => Controls_Manager::SELECT,
			'options' => [
				'h1' => esc_html__('H1', 'civi'),
				'h2' => esc_html__('H2', 'civi'),
				'h3' => esc_html__('H3', 'civi'),
				'h4' => esc_html__('H4', 'civi'),
				'h5' => esc_html__('H5', 'civi'),
				'h6' => esc_html__('H6', 'civi'),
				'div' => esc_html__('div', 'civi'),
				'span' => esc_html__('span', 'civi'),
				'p' => esc_html__('p', 'civi'),
			],
			'default' => 'h2',
		]);

		$repeater = new Repeater();

		$repeater->add_control(
			'fancy_heading_field_animated',
			[
				'label' => esc_html__('Text', 'civi'),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__('Better', 'civi'),
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'fancy_heading_text',
			[
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'label' => esc_html__('Animated Text', 'civi'),
				'label_block' => true,
				'separator' => 'before',
				'default'     => [
					[
						'fancy_heading_field_animated'   => esc_html__('Better', 'civi'),
					],
					[
						'fancy_heading_field_animated'   => esc_html__('Bigger', 'civi'),
					],
					[
						'fancy_heading_field_animated'   => esc_html__('Faster', 'civi'),
					],
				],
				'title_field' => '{{{ fancy_heading_field_animated }}}',
			]
		);

		$this->add_control('fancy_heading_prefix', [
			'label' => esc_html__('Prefix', 'civi'),
			'type' => Controls_Manager::TEXT,
			'default' => esc_html__('Got Talent Meet', 'civi'),
			'description' => esc_html__('Text before Fancy text', 'civi'),
			'separator' => 'before',
			'label_block' => true,
			'dynamic' => [
				'active' => true,
			],
		]);

		$this->add_control(
			'fancy_heading_suffix',
			[
				'label' => esc_html__('Suffix', 'civi'),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'dynamic' => [
					'active' => true,
				],
				'description' => esc_html__('Text after Fancy text', 'civi'),
			]
		);

		$this->end_controls_section();
	}

	private function add_content_settings_section()
	{
		$this->start_controls_section('fancy_heading_settings_section', [
			'label' => esc_html__('Settings', 'civi'),
			'tab' => Controls_Manager::TAB_CONTENT,
		]);

		$this->add_responsive_control('fancy_heading_alignment', [
			'label' => esc_html__('Alignment', 'civi'),
			'type' => Controls_Manager::CHOOSE,
			'options' => [
				'flex-start' => [
					'title' => esc_html__('Left', 'civi'),
					'icon' => 'eicon-h-align-left',
				],
				'center' => [
					'title' => esc_html__('Center', 'civi'),
					'icon' => 'eicon-h-align-center',
				],
				'flex-end' => [
					'title' => esc_html__('Right', 'civi'),
					'icon' => 'eicon-h-align-right',
				],
			],
			'condition' => [
				'fancy_heading_max_width[size]!' => '',
			],
			'selectors' => [
				'{{WRAPPER}} .elementor-widget-container' => 'display: -webkit-box; display: -ms-flexbox ; display: flex; -webkit-box-pack:{{VALUE}};-ms-flex-pack:{{VALUE}};justify-content:{{VALUE}}',
			],
		]);


		$this->add_control(
			'fancy_heading_type',
			[
				'label' => esc_html__('Animation Type', 'civi'),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'typing' => esc_html__('Typing', 'civi'),
					'loading' => esc_html__('Loading Bar', 'civi'),
					'zoom-in' => esc_html__('Zoom In', 'civi'),
					'zoom-out' => esc_html__('Zoom Out', 'civi'),
					'slider-right' => esc_html__('Slider Right', 'civi'),
					'slider-left' => esc_html__('Slider Left', 'civi'),
					'slider-top' => esc_html__('Slider Top', 'civi'),
					'slider-bottom' => esc_html__('Slider Bottom', 'civi'),
					'rotate' => esc_html__('Rotate Style', 'civi'),
				],
				'default' => 'typing',
			]
		);

		$this->add_control(
			'fancy_heading_slide_up_pause_time',
			array(
				'label' => esc_html__('Pause Time (Milliseconds)', 'civi'),
				'type' => Controls_Manager::NUMBER,
				'description' => esc_html__('How long should the word/string stay visible? Set a value in milliseconds.', 'civi'),
				'condition' => [
					'fancy_heading_type!' => 'typing',
				],
			)
		);

		$this->add_control(
			'fancy_heading_typing_speed',
			array(
				'label' => esc_html__('Typing Speed', 'civi'),
				'type' => Controls_Manager::NUMBER,
				'condition' => [
					'fancy_heading_type' => 'typing',
				],
			)
		);
		$this->add_control(
			'fancy_heading_typing_delay',
			array(
				'label' => esc_html__('Delay on Change', 'civi'),
				'type' => Controls_Manager::NUMBER,
				'condition' => [
					'fancy_heading_type' => 'typing',
				],
			)
		);

		$this->add_control(
			'fancy_heading_typing_loop',
			[
				'label' => esc_html__('Loop the Typing', 'civi'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('Yes', 'civi'),
				'label_off' => esc_html__('No', 'civi'),
				'return_value' => 'yes',
				'default' => 'yes',
				'condition' => [
					'fancy_heading_type' => 'typing',
				],
			]
		);

		$this->add_control(
			'fancy_heading_typing_cursor',
			[
				'label' => esc_html__('Display Type Cursor', 'civi'),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => esc_html__('Yes', 'civi'),
				'label_off' => esc_html__('No', 'civi'),
				'return_value' => 'yes',
				'condition' => [
					'fancy_heading_type' => 'typing',
				],
			]
		);

		$this->end_controls_section();
	}

	private function add_content_style_section()
	{
		$this->start_controls_section('fancy_heading_wrapper_style_section', [
			'tab' => Controls_Manager::TAB_STYLE,
			'label' => esc_html__('Wrapper', 'civi'),
		]);

		$this->add_responsive_control('fancy_heading_max_width', [
			'label' => esc_html__('Max Width', 'civi'),
			'type' => Controls_Manager::SLIDER,
			'default' => [
				'unit' => 'px',
			],
			'tablet_default' => [
				'unit' => 'px',
			],
			'mobile_default' => [
				'unit' => 'px',
			],
			'size_units' => ['px', '%'],
			'range' => [
				'%' => [
					'min' => 1,
					'max' => 100,
				],
				'px' => [
					'min' => 1,
					'max' => 2000,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .civi-fancy-heading' => 'width: {{SIZE}}{{UNIT}};',
			],
		]);

		$this->end_controls_section();

		$this->start_controls_section('fancy_heading_animated_style_section', [
			'label' => esc_html__('Animated Text', 'civi'),
			'tab' => Controls_Manager::TAB_STYLE,
		]);

		$this->add_group_control(Group_Control_Typography::get_type(), [
			'name' => 'fancy_heading_animated_typography',
			'selector' => '{{WRAPPER}} .civi-fancy-heading-animated b,{{WRAPPER}} .civi-fancy-heading-typing .civi-fancy-heading-animated',
		]);

		$this->add_control('fancy_heading_animated_color', [
			'label'     => esc_html__('Color', 'civi'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .civi-fancy-heading-animated' => 'color: {{VALUE}};',
			],
		]);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'fancy_heading_animated_border',
				'label' => esc_html__('Border', 'civi'),
				'selector' => '{{WRAPPER}} .civi-fancy-heading-animated b,{{WRAPPER}} .civi-fancy-heading-typing .civi-fancy-heading-animated',
			]
		);

		$this->add_responsive_control(
			'fancy_heading_animated_border_radius',
			[
				'label' => esc_html__('Border Radius', 'civi'),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .civi-fancy-heading-animated b,{{WRAPPER}} .civi-fancy-heading-typing .civi-fancy-heading-animated' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
				],
			]
		);

		$this->add_responsive_control(
			'fancy_heading_animated_padding',
			[
				'label' => esc_html__('Padding', 'civi'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .civi-fancy-heading-animated b,{{WRAPPER}} .civi-fancy-heading-typing .civi-fancy-heading-animated' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'fancy_heading_animated_label_border',
			[
				'label' => esc_html__('Border Bar Waiting', 'civi'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'fancy_heading_type' => 'loading',
				],
			]
		);

		$this->add_group_control(Group_Control_Background::get_type(), [
			'name' => 'fancy_heading_animated_background_border',
			'selector' => '{{WRAPPER}} .civi-fancy-heading-animated:after',
			'condition' => [
				'fancy_heading_type' => 'loading',
			],
		]);

		$this->end_controls_section();
	}

	private function add_prefix_controls_section()
	{
		$this->start_controls_section('fancy_heading_prefix_suffix_style_section', [
			'label' => esc_html__('Prefix & Suffix', 'civi'),
			'tab' => Controls_Manager::TAB_STYLE,
			'condition' => [
				'fancy_heading_prefix!' => '',
			],
		]);


		$this->add_group_control(Group_Control_Typography::get_type(), [
			'name' => 'fancy_heading_prefix_suffix_typography',
			'selector' => '{{WRAPPER}} .civi-fancy-heading-before , {{WRAPPER}} .civi-fancy-heading-after',
		]);

		$this->add_control('fancy_heading_prefix_suffix_color', [
			'label'     => esc_html__('Color', 'civi'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .civi-fancy-heading-before, {{WRAPPER}} .civi-fancy-heading-after' => 'color: {{VALUE}};',
			],
		]);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'fancy_heading_prefix_suffix_border',
				'label' => esc_html__('Border', 'civi'),
				'selector' => '{{WRAPPER}} .civi-fancy-heading-before , {{WRAPPER}} .civi-fancy-heading-after',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'fancy_heading_prefix_suffix_border_radius',
			[
				'label' => esc_html__('Border Radius', 'civi'),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .civi-fancy-heading-before , {{WRAPPER}} .civi-fancy-heading-after' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'fancy_heading_prefix_suffix_box_shadow',
				'label' => esc_html__('Box Shadow', 'civi'),
				'selector' => '{{WRAPPER}} .civi-fancy-heading-before , {{WRAPPER}} .civi-fancy-heading-after',
			]
		);

		$this->add_responsive_control(
			'fancy_heading_prefix_padding',
			[
				'label' => esc_html__('Padding Prefix', 'civi'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .civi-fancy-heading-before' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'fancy_heading_suffix_padding',
			[
				'label' => esc_html__('Padding Suffix', 'civi'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .civi-fancy-heading-after' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_section();
	}

	public function data_animation_heading()
	{
		$settings = $this->get_settings_for_display();
		if (isset($settings['fancy_heading_text'])) {
			foreach ($settings['fancy_heading_text'] as $item) {
				if (!empty($item['fancy_heading_field_animated'])) {
					$fancy_heading[] = $item['fancy_heading_field_animated'];
				}
			}
			return $fancy_heading;
		}
	}

	protected function render()
	{
		$settings = $this->get_settings_for_display();

		$fancy_classes = array(
			'civi-fancy-heading',
		);

		if ($settings['fancy_heading_type'] !== '') {
			$fancy_classes[] = 'civi-fancy-heading-' . $settings['fancy_heading_type'];
		}

		if (!empty($settings['fancy_heading_class'])) {
			$fancy_classes[] = $settings['fancy_heading_class'];
		}

		$settings_data = array();
		if ($settings['fancy_heading_slide_up_pause_time'] !== '') {
			$settings_data['animationDelay'] = $settings['fancy_heading_slide_up_pause_time'];
		}

		$data_classes = '';
		if ($settings['fancy_heading_type'] === 'typing') {
			$data_text = $this->data_animation_heading();
			$data_classes =  wp_json_encode($data_text);

			if ($settings['fancy_heading_typing_speed'] !== '') {
				$settings_data['typingSpeed'] = $settings['fancy_heading_typing_speed'];
			}

			if ($settings['fancy_heading_typing_delay'] !== '') {
				$settings_data['typingDelay'] = $settings['fancy_heading_typing_delay'];
			}

			if ($settings['fancy_heading_typing_loop'] === 'yes') {
				$settings_data['typingLoop'] = true;
			}

			if ($settings['fancy_heading_typing_cursor'] === 'yes') {
				$settings_data['typingCursor'] = true;
			}
		}

		$this->add_render_attribute('fancy_warpper', array(
			'class' => $fancy_classes,
			'data-text' => $data_classes,
			'data-settings-options' => wp_json_encode($settings_data),
		));

		$tag_html = $settings['fancy_heading_tag'];
		$j = 0;


		$fancy_heading_animated_classes = array('civi-fancy-heading-animated');
		if (!empty($settings['fancy_heading_animated_class'])) {
			$fancy_heading_animated_classes[] = $settings['fancy_heading_animated_class'];
		}
		$this->add_render_attribute('fancy_heading_warpper', 'class', $fancy_heading_animated_classes);

		printf('<%1$s %2$s>', $tag_html, $this->get_render_attribute_string('fancy_warpper'));
		if ($settings['fancy_heading_prefix'] !== '') : ?>
			<span class="civi-fancy-heading-before"><?php echo wp_kses_post($settings['fancy_heading_prefix']) ?></span>
		<?php endif; ?>
		<span <?php $this->print_render_attribute_string('fancy_heading_warpper') ?>>
			<?php if (isset($settings['fancy_heading_text']) && $settings['fancy_heading_type'] !== 'typing') : ?>
				<?php foreach ($settings['fancy_heading_text'] as $i => $item) :
					$j++;
					$item_setting_key = $this->get_repeater_setting_key('fancy_heading_animated_item', 'fancy_heading_text', $i);
					$items_class = array(
						'civi-fancy-heading-item',
					);
					if ($j == '1') {
						$items_class[] = 'civi-fancy-heading-show';
					}
					$this->add_render_attribute('fancy_heading_item' . $j, 'class', $items_class);
				?>
					<b <?php echo $this->get_render_attribute_string('fancy_heading_item' . $j); ?>><?php esc_html_e($item['fancy_heading_field_animated']) ?> </b>
				<?php endforeach; ?>
			<?php endif; ?>
		</span>
		<?php
		if ($settings['fancy_heading_suffix'] !== '') : ?>
			<span class="civi-fancy-heading-after"><?php esc_html_e($settings['fancy_heading_suffix']) ?></span>
		<?php endif;
		printf('</%1$s>', $tag_html); ?>
<?php
	}
}
