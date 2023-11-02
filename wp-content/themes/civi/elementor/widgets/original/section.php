<?php

namespace Civi_Elementor;

use Elementor\Plugin;
use Elementor\Controls_Manager;

defined('ABSPATH') || exit;

class Modify_Widget_Section extends Modify_Base
{

	private static $_instance = null;

	public static function instance()
	{
		if (is_null(self::$_instance)) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	public function initialize()
	{
		add_action('elementor/element/section/section_layout/after_section_end', [
			$this,
			'section_layout',
		]);
	}

	/**
	 * Update section gap control in layout section.
	 *
	 * @param \Elementor\Controls_Stack $element
	 */
	public function section_layout($element)
	{
		// Get section args.
		$section_gap = Plugin::instance()->controls_manager->get_control_from_stack($element->get_unique_name(), 'gap');

		// Change gap select options.
		$section_gap['options'] = [
			// Change 'Default' => 'Normal' text.
			'default'  => esc_html__('Normal', 'civi'),
			'no'       => esc_html__('No Gap', 'civi'),
			'narrow'   => esc_html__('Narrow', 'civi'),
			'extended' => esc_html__('Extended', 'civi'),
			'wide'     => esc_html__('Wide', 'civi'),
			'wider'    => esc_html__('Wider', 'civi'),
			// Add flexible gap width.
			'custom'   => esc_html__('Custom', 'civi'),
		];

		// Change default gap setting from 'default' => 'extended'.
		$section_gap['default'] = 'extended';

		// Apply gap changed.
		$element->update_control('gap', $section_gap);

		$element->start_injection([
			'type' => 'control',
			'at'   => 'after',
			'of'   => 'gap',
		]);

		$element->add_control('gap_beside', [
			'label'        => esc_html__('Gap Beside', 'civi'),
			'type'         => Controls_Manager::SELECT,
			'default'      => 'yes',
			'options'      => [
				'no'  => esc_html__('No', 'civi'),
				'yes' => esc_html__('Yes', 'civi'),
			],
			'prefix_class' => 'elementor-section-gap-beside-',
			'condition'    => [
				'gap!' => 'no',
			],
		]);

		$element->add_responsive_control('gap_width', [
			'label'     => esc_html__('Gap Width', 'civi'),
			'type'      => Controls_Manager::SLIDER,
			'range'     => [
				'px' => [
					'min'  => 0,
					'max'  => 100,
					'step' => 2,
				],
			],
			'selectors' => [
				'{{WRAPPER}} > .elementor-column-gap-custom > .elementor-row > .elementor-column > .elementor-element-populated' => 'padding-left: calc( {{SIZE}}{{UNIT}} / 2); padding-right: calc( {{SIZE}}{{UNIT}} / 2);',
				'{{WRAPPER}}.elementor-section-gap-beside-no > .elementor-column-gap-custom > .elementor-row'                    => 'margin-left: calc( -{{SIZE}}{{UNIT}} / 2); margin-right: calc( -{{SIZE}}{{UNIT}} / 2);',
			],
			'condition' => [
				'gap' => 'custom',
			],
		]);

		$element->end_injection();

		// Add control custom alignment of content.
		$element->start_injection([
			'type' => 'control',
			'at'   => 'before',
			'of'   => 'content_position',
		]);

		$element->add_control('content_alignment', [
			'label'        => esc_html__('Content Alignment', 'civi'),
			'type'         => Controls_Manager::CHOOSE,
			'options'      => Widget_Utils::get_control_options_horizontal_alignment(),
			'default'      => 'center',
			'toggle'       => false,
			'prefix_class' => 'elementor-section-content-align-',
			'condition'    => [
				'content_width!' => '',
			],
		]);

		$element->add_control('column_vertical_alignment', [
			'label'                => esc_html__('Column Vertical Alignment', 'civi'),
			'label_block'          => true,
			'type'                 => Controls_Manager::CHOOSE,
			'options'              => Widget_Utils::get_control_options_vertical_full_alignment(),
			'default'              => 'stretch',
			'toggle'               => false,
			'prefix_class'         => 'elementor-section-column-vertical-align-',
			'selectors'            => [
				'{{WRAPPER}} > .elementor-container > .elementor-row' => 'align-items: {{VALUE}}',
			],
			'selectors_dictionary' => [
				'top'    => 'flex-start',
				'middle' => 'center',
				'bottom' => 'flex-end',
			],
		]);

		$element->update_control('content_position', [
			'condition' => [
				'column_vertical_alignment' => 'stretch',
			],
		]);

		$element->end_injection();
	}
}

Modify_Widget_Section::instance()->initialize();
