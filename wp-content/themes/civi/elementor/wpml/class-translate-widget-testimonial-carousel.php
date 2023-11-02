<?php

namespace Civi_Elementor;

use WPML_Elementor_Module_With_Items;

defined('ABSPATH') || exit;

class Translate_Widget_Testimonial_Carousel extends WPML_Elementor_Module_With_Items
{

	/**
	 * Repeater field id
	 *
	 * @return string
	 */
	public function get_items_field()
	{
		return 'slides';
	}

	/**
	 * Repeater items field id
	 *
	 * @return array List inner fields translatable.
	 */
	public function get_fields()
	{
		return [
			'title',
			'content',
			'name',
			'position',
		];
	}

	/**
	 * @param string $field
	 *
	 * @return string
	 */
	protected function get_title($field)
	{
		switch ($field) {
			case 'title':
				return esc_html__('Testimonial Carousel: Title', 'civi');

			case 'name':
				return esc_html__('Testimonial Carousel: Name', 'civi');

			case 'content':
				return esc_html__('Testimonial Carousel: Content', 'civi');

			case 'position':
				return esc_html__('Testimonial Carousel: Position', 'civi');

			default:
				return '';
		}
	}

	/**
	 * @param string $field
	 *
	 * @return string
	 */
	protected function get_editor_type($field)
	{
		switch ($field) {
			case 'name':
			case 'title':
			case 'position':
				return 'LINE';

			case 'content':
				return 'AREA';

			default:
				return '';
		}
	}
}
