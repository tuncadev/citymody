<?php

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Setup for customizer of this theme
 */
class Civi_Customizer
{
	/**
	 * The constructor.
	 */
	public function __construct()
	{
		// Remove unused native sections and controls.
		add_action('customize_register', array($this, 'remove_customizer_sections'));

		// Load customizer sections when all widgets init
		add_action('widgets_init', array($this, 'load_customizer'), 99);

		add_action('wp_default_scripts', array($this, 'wp_default_custom_scripts'));
	}

	/**
	 * Load Customizer.
	 */
	public function load_customizer()
	{
		get_template_part('customizer/customizer');
	}

	/**
	 * Remove unused native sections and controls
	 *
	 * @param $wp_customize
	 */
	public function remove_customizer_sections($wp_customize)
	{
		$wp_customize->remove_section('nav');
		$wp_customize->remove_section('colors');
		$wp_customize->remove_section('background_image');
		$wp_customize->remove_section('header_image');
		$wp_customize->remove_control('display_header_text');
	}

	public function wp_default_custom_scripts($scripts)
	{
		if (is_admin()) {
			$scripts->add('wp-color-picker', "/wp-admin/js/color-picker.js", array('iris'), false, 1);
			did_action('init') && $scripts->localize(
				'wp-color-picker',
				'wpColorPickerL10n',
				array(
					'clear'            => __('Clear', 'civi'),
					'clearAriaLabel'   => __('Clear color', 'civi'),
					'defaultString'    => __('Default', 'civi'),
					'defaultAriaLabel' => __('Select default color', 'civi'),
					'pick'             => __('Select Color', 'civi'),
					'defaultLabel'     => __('Color value', 'civi'),
				)
			);
		}
	}
}
