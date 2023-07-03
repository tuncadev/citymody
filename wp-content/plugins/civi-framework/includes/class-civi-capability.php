<?php

if (!defined('ABSPATH')) {
	exit;
}

if (!class_exists('Civi_Capability')) {
	/**
	 * Class Civi_Capability
	 */
	class Civi_Capability
	{
		public function __construct()
		{
			$this->create_roles();
		}

		public static function create_roles()
		{
			global $wp_roles;

			if (!class_exists('WP_Roles')) {
				return;
			}

			if (!isset($wp_roles)) {
				$wp_roles = new WP_Roles();
			}

			$capabilities = self::get_core_capabilities();

			foreach ($capabilities as $cap_group) {
				foreach ($cap_group as $cap) {
					$wp_roles->add_cap('administrator', $cap);
				}
			}
		}

		private static function get_core_capabilities()
		{
			$capabilities = array();

			$capabilities['core'] = array(
				'manage_civi_framework'
			);

			$capability_types = array('jobs', 'booking', 'package', 'invoice', 'company');

			foreach ($capability_types as $capability_type) {

				$capabilities[$capability_type] = array(
					// Post type
					"edit_{$capability_type}",
					"read_{$capability_type}",
					"delete_{$capability_type}",
					"edit_{$capability_type}s",
					"edit_others_{$capability_type}s",
					"publish_{$capability_type}s",
					"read_private_{$capability_type}s",
					"delete_{$capability_type}s",
					"delete_private_{$capability_type}s",
					"delete_published_{$capability_type}s",
					"delete_others_{$capability_type}s",
					"edit_private_{$capability_type}s",
					"edit_published_{$capability_type}s",

					// Terms
					"manage_{$capability_type}_terms",
					"edit_{$capability_type}_terms",
					"delete_{$capability_type}_terms",
					"assign_{$capability_type}_terms"
				);
			}

			return $capabilities;
		}
	}
	new Civi_Capability();
}
