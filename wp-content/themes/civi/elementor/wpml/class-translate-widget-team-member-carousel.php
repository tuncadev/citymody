<?php

namespace Civi_Elementor;

use WPML_Elementor_Module_With_Items;

defined('ABSPATH') || exit;

class Translate_Widget_Team_Member_Carousel extends WPML_Elementor_Module_With_Items
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
			'name',
			'content',
			'position',
			'profile' => ['url'],
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
				return esc_html__('Team Member Carousel: Name', 'civi');

			case 'content':
				return esc_html__('Team Member Carousel: Content', 'civi');

			case 'position':
				return esc_html__('Team Member Carousel: Position', 'civi');

			case 'url':
				return esc_html__('Team Member Carousel: Profile', 'civi');

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
			case 'position':
				return 'LINE';

			case 'content':
				return 'AREA';

			case 'url':
				return 'LINK';

			default:
				return '';
		}
	}
}
