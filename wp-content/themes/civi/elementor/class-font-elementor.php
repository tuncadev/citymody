<?php

namespace Civi_Elementor;

defined('ABSPATH') || exit;

class Font_Elementor
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
		/**
		 * Enabled elementor icons.
		 */
		//add_filter( 'elementor/icons_manager/native', [ $this, 'add_eicons_to_icon_manager' ] );

		/**
		 * Disabled elementor icons.
		 * Star rating widget used.
		 */
		//add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'remove_eicons' ] );

	}

	public function add_eicons_to_icon_manager($settings)
	{
		$json_url = CIVI_ELEMENTOR_ASSETS . '/libs/eicons/eicons.json';

		$settings['eicons'] = [
			'name'          => 'eicons',
			'label'         => esc_html__('Eicons', 'civi'),
			'url'           => false,
			'enqueue'       => false,
			'prefix'        => 'eicon-',
			'displayPrefix' => '',
			'labelIcon'     => 'eicon-elementor-square',
			'ver'           => '5.3.0',
			'fetchJson'     => $json_url,
			'native'        => true,
		];

		return $settings;
	}

	public function remove_eicons()
	{
		if (!\Elementor\Plugin::$instance->preview->is_preview_mode()) {
			wp_dequeue_style('elementor-icons');
		}
	}
}

Font_Elementor::instance()->initialize();
