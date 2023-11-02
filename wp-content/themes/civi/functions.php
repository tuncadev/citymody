<?php

/**
 * Define constants
 */
$civi_theme = wp_get_theme();

if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}

if (!empty($civi_theme['Template'])) {
	$civi_theme = wp_get_theme($civi_theme['Template']);
}

if (!defined('CIVI_THEME_NAME')) {
	define('CIVI_THEME_NAME', $civi_theme['Name']);
}

if (!defined('CIVI_THEME_SLUG')) {
	define('CIVI_THEME_SLUG', $civi_theme['Template']);
}

if (!defined('CIVI_THEME_VER')) {
	define('CIVI_THEME_VER', $civi_theme['Version']);
}

if (!defined('CIVI_THEME_DIR')) {
	define('CIVI_THEME_DIR', trailingslashit(get_template_directory()));
}

if (!defined('CIVI_THEME_URI')) {
	define('CIVI_THEME_URI', get_template_directory_uri());
}

if (!defined('CIVI_THEME_PREFIX')) {
	define('CIVI_THEME_PREFIX', 'civi_');
}

if (!defined('CIVI_METABOX_PREFIX')) {
	define('CIVI_METABOX_PREFIX', 'civi-');
}

if (!defined('CIVI_CUSTOMIZER_DIR')) {
	define('CIVI_CUSTOMIZER_DIR', CIVI_THEME_DIR . '/customizer');
}

if (!defined('CIVI_IMAGES')) {
	define('CIVI_IMAGES', CIVI_THEME_URI . '/assets/images/');
}

define('CIVI_ELEMENTOR_DIR', get_template_directory() . DS . 'elementor');
define('CIVI_ELEMENTOR_URI', get_template_directory_uri() . '/elementor');
define('CIVI_ELEMENTOR_ASSETS', get_template_directory_uri() . '/elementor/assets');

/**
 * Load Theme Class.
 *
 */
foreach (glob(get_template_directory() . '/includes/*.php') as $theme_class) {
	require_once($theme_class);
}

require_once CIVI_ELEMENTOR_DIR . '/class-entry.php';

function civi_load_elementor_options()
{
	update_option('elementor_disable_typography_schemes', 'yes');
}

add_action('after_switch_theme', 'civi_load_elementor_options');

add_filter('wp_mail_smtp_core_wp_mail_function_incorrect_location_notice', '__return_false');

/**
 * Init the theme
 *
 */
new Civi_Init();
