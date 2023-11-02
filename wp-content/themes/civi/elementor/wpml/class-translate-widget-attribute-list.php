<?php

namespace Civi_Elementor;

use WPML_Elementor_Module_With_Items;

defined('ABSPATH') || exit;

class Translate_Widget_Attribute_List extends WPML_Elementor_Module_With_Items
{

	/**
	 * Repeater field id
	 *
	 * @return string
	 */
	public function get_items_field()
	{
		return 'items';
	}

	/**
	 * Repeater items field id
	 *
	 * @return array List inner fields translatable.
	 */
	public function get_fields()
	{
		return [
			'name',
			'value',
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
			case 'name':
				return esc_html__('Attribute: Name', 'civi');

			case 'value':
				return esc_html__('Attribute: Value', 'civi');

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
				return 'LINE';

			case 'value':
				return 'AREA';

			default:
				return '';
		}
	}
}
