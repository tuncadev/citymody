<?php
if (!defined('ABSPATH')) {
	exit;
}

if (!class_exists('Base_Framework')) {
	/**
	 * The core plugin class
	 * Class Base_Framework
	 */
	class Civi_Base
	{
		/**
		 * Constructor SP_Loader
		 * *******************************************************
		 */
		public function __construct()
		{
			$this->define_constants();
			$this->includes();

			/*
			 * Register auto loader for fields type
			 */
			spl_autoload_register(array($this, 'fields_autoload'));
			add_action('init', array($this, 'theme_init'));
		}

		public function theme_init()
		{
			if (!defined('GLF_OPTIONS_FONT_USING')) {
				define('GLF_OPTIONS_FONT_USING', 'glf_font_using');
			}
		}

		/**
		 * Define constant using in BASE
		 * *******************************************************
		 */
		private function define_constants()
		{
			$base_dir     = str_replace('\\', '/', trailingslashit(dirname(__FILE__)));
			$template_dir = str_replace('\\', '/', trailingslashit(get_template_directory()));
			$base_url     = '';

			/**
			 * Define plugin DIR
			 */
			if (!defined('GLF_BASE_DIR')) {
				define('GLF_BASE_DIR', plugin_dir_path(__FILE__));
			}

			if (strpos($base_dir, $template_dir) === false) {
				$base_dir_name = 'base';
				$base_url      = trailingslashit(plugins_url($base_dir_name));
			} else {
				$sub_template_dir = substr($base_dir, strlen($template_dir));
				$base_url         = trailingslashit(get_template_directory_uri()) . $sub_template_dir;
			}

			$base_url = apply_filters('civi_base_url', $base_url);

			/**
			 * Define plugin URL
			 */
			if (!defined('GLF_BASE_URL')) {
				define('GLF_BASE_URL', $base_url);
			}

			if (!defined('GLF_BASE_RESOURCE_PREFIX')) {
				define('GLF_BASE_RESOURCE_PREFIX', 'glf_');
			}
			/**
			 * Define Plugin VERSION
			 */
			if (!defined('GLF_VER')) {
				define('GLF_VER', '1.0');
			}
		}

		/**
		 * Includes library for plugin
		 * *******************************************************
		 */
		private function includes()
		{
			/*
			 * Function
			 */
			require_once GLF_BASE_DIR . 'inc/functions.php';

			/*
			 * Define post type
			 */
			require_once GLF_BASE_DIR . 'controls/post-type.php';

			/*
			 * Define taxonomy for post type
			 */
			require_once GLF_BASE_DIR . 'controls/taxonomy.php';

			/*
			 * Meta box for post type Attribute
			 */
			require_once GLF_BASE_DIR . 'controls/meta-box.php';

			/*
			 * Define term meta custom config
			 */
			require_once GLF_BASE_DIR . 'controls/term-meta.php';

			/*
			 * Define theme options
			 */
			require_once GLF_BASE_DIR . 'controls/theme-options.php';

			/*
			 * Required Field abstract class
			 */
			require_once GLF_BASE_DIR . 'fields/field.php';
		}

		/**
		 * Auto load fields
		 * *******************************************************
		 */
		public function fields_autoload($class_name)
		{
			$class = preg_replace('/^GLF_Field_/', '', $class_name);
			if ($class != $class_name) {
				$class = strtolower($class);
				include_once(GLF_BASE_DIR . "fields/{$class}/{$class}.class.php");
			}
		}
	}

	new Civi_Base();
}
