<?php
/**
 * Cron controller
 *
 * @package FormidablePro
 * @since 5.4.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

/**
 * Class FrmProCronController
 */
class FrmProCronController {

	/**
	 * Gets all cron events.
	 *
	 * @return string[]
	 */
	private static function get_events() {
		return array(
			'frm_pro_delete_temp_files_event' => 'hourly',
		);
	}

	/**
	 * Runs cron events.
	 */
	public static function init_cron() {
		$events = self::get_events();

		foreach ( $events as $event => $recurrence ) {
			if ( 'frm_pro_delete_temp_files_event' === $event && ! intval( FrmDb::get_var( 'frm_fields', array( 'type' => 'file' ), 'COUNT(*)' ) ) ) {
				continue;
			}

			if ( ! wp_next_scheduled( $event ) ) {
				wp_schedule_event( time(), $recurrence, $event );
			}
		}
	}

	/**
	 * Removes all cron events.
	 */
	public static function remove_cron() {
		$events = self::get_events();

		foreach ( $events as $event => $recurrence ) {
			$timestamp = wp_next_scheduled( $event );
			if ( false !== $timestamp ) {
				wp_unschedule_event( $timestamp, $event );
			}
		}
	}
}
