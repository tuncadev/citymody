<?php

/**
 * Fired during plugin deactivation
 *
 */
if (!defined('ABSPATH')) {
	exit;
}

if (!class_exists('Civi_Deactivator')) {
	require_once CIVI_PLUGIN_DIR . 'includes/admin/class-civi-schedule.php';
	/**
	 * Fired during plugin deactivation
	 * Class Civi_Deactivator
	 */
	class Civi_Deactivator
	{
		/**
		 * Run when plugin deactivated
		 */
		public static function deactivate()
		{
			Civi_Schedule::clear_scheduled_hook();
		}
	}
}
