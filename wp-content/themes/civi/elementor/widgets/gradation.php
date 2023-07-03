<?php

namespace Civi_Elementor;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;

defined('ABSPATH') || exit;

//@todo Not compatible with WPML.

class Widget_Gradation extends Base
{

	public function get_name()
	{
		return 'civi-gradation';
	}

	public function get_title()
	{
		return esc_html__('Gradation', 'civi');
	}

	public function get_icon_part()
	{
		return 'eicon-navigation-horizontal';
	}

	public function get_keywords()
	{
		return ['gradation', 'step'];
	}

    public function get_style_depends()
    {
        return ['civi-el-widget-gradation'];
    }

	protected function register_controls()
	{
		$this->start_controls_section('layout_section', [
			'label' => esc_html__('Layout', 'civi'),
		]);

		$repeater = new Repeater();

		$repeater->add_control('title', [
			'label'       => esc_html__('Title', 'civi'),
			'type'        => Controls_Manager::TEXT,
			'default'     => esc_html__('Title', 'civi'),
			'label_block' => true,
		]);

		$repeater->add_control('description', [
			'label' => esc_html__('Description', 'civi'),
			'type'  => Controls_Manager::TEXTAREA,
		]);

		$this->add_control('items', [
			'label'       => esc_html__('Items', 'civi'),
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $repeater->get_controls(),
			'default'     => [
				[
					'title'       => esc_html__('Step #1', 'civi'),
					'description' => esc_html__('Suspe ndisse suscipit sagittis leo sit met condimentum estibulum issim Lorem ipsum dolor sit amet, consectetur cium', 'civi'),
				],
				[
					'title'       => esc_html__('Step #2', 'civi'),
					'description' => esc_html__('Suspe ndisse suscipit sagittis leo sit met condimentum estibulum issim Lorem ipsum dolor sit amet, consectetur cium', 'civi'),
				],
				[
					'title'       => esc_html__('Step #3', 'civi'),
					'description' => esc_html__('Suspe ndisse suscipit sagittis leo sit met condimentum estibulum issim Lorem ipsum dolor sit amet, consectetur cium', 'civi'),
				],
			],
			'title_field' => '{{{ title }}}',
		]);

		$this->end_controls_section();

		$this->add_styling_section();
	}

	private function add_styling_section()
	{
		$this->start_controls_section('styling_section', [
			'label' => esc_html__('Styling', 'civi'),
			'tab'   => Controls_Manager::TAB_STYLE,
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

		$this->add_responsive_control('spacing', [
			'label'     => esc_html__('Spacing', 'civi'),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [
				'px' => [
					'min' => 0,
					'max' => 100,
				],
			],
			'selectors' => [
				'{{WRAPPER}} .item + .item' => 'margin-top: {{VALUE}};',
			],
		]);

		$this->add_control('title_heading', [
			'label'     => esc_html__('Title', 'civi'),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
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

		$this->add_control('description_heading', [
			'label'     => esc_html__('Description', 'civi'),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		]);

		$this->add_group_control(Group_Control_Typography::get_type(), [
			'name'     => 'description_typography',
			'label'    => esc_html__('Typography', 'civi'),
			'selector' => '{{WRAPPER}} .description',
		]);

		$this->add_control('description_color', [
			'label'     => esc_html__('Color', 'civi'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .description' => 'color: {{VALUE}};',
			],
		]);

		$this->add_control('line_heading', [
			'label'     => esc_html__('Line', 'civi'),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		]);

		$this->add_control('line_color', [
			'label'     => esc_html__('Color', 'civi'),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .line:before' => 'border-color: {{VALUE}};',
			],
		]);

		$this->end_controls_section();
	}

	protected function render()
	{
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute('wrapper', 'class', 'civi-gradation');
?>
		<div <?php $this->print_attributes_string('wrapper'); ?>>
			<?php
			$loop_count = 0;
			if ($settings['items'] && count($settings['items']) > 0) {
				foreach ($settings['items'] as $key => $item) {
					$loop_count++;
			?>
					<div class="item">

						<div class="count-wrap">
							<div class="count"><?php esc_html_e($loop_count); ?></div>
							<div class="line"></div>
						</div>

						<div class="content-wrap">
							<?php if (isset($item['title'])) : ?>
								<h5 class="title"><?php echo wp_kses_post($item['title']); ?></h5>
							<?php endif; ?>

							<?php if (isset($item['description'])) : ?>
								<div class="description"><?php esc_html_e($item['description']); ?></div>
							<?php endif; ?>
						</div>
					</div>
			<?php
				}
			}
			?>
		</div>
<?php
	}
}
