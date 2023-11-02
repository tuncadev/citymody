<?php
// exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Cookie Notice Modules WP Fastest Cache class.
 *
 * Compatibility since: 1.0.0
 *
 * @class Cookie_Notice_Modules_WPFastestCache
 */
class Cookie_Notice_Modules_WPFastestCache {

	/**
	 * Constructor.
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'admin_init', [ $this, 'check_wpfc' ], 11 );
	}

	/**
	 * Compatibility with WP Fastest Cache plugin.
	 *
	 * @return void
	 */
	public function check_wpfc() {
		// is preloading enabled?
		if ( isset( $GLOBALS['wp_fastest_cache_options']->wpFastestCachePreload ) )
			$this->disable_preload( $GLOBALS['wp_fastest_cache_options'] );

		// is caching enabled?
		if ( isset( $GLOBALS['wp_fastest_cache_options']->wpFastestCacheStatus ) ) {
			// update 2.4.9+
			if ( version_compare( Cookie_Notice()->db_version, '2.4.9', '<=' ) )
				$this->delete_cache();

			add_action( 'updated_option', [ $this, 'check_updated_option' ], 10, 3 );
		}
	}

	/**
	 * Delete cache files after updating Cookie Notice settings or status.
	 *
	 * @return void
	 */
	public function check_updated_option( $option, $old_value, $new_value ) {
		if ( $option === 'cookie_notice_status' || $option === 'cookie_notice_options' )
			$this->delete_cache();
	}

	/**
	 * Disable preloading.
	 *
	 * @param object $options
	 * @return void
	 */
	private function disable_preload( $options ) {
		// disable preload
		unset( $options->wpFastestCachePreload );

		// delete preload option
		delete_option( 'WpFastestCachePreLoad' );

		// clear preload hook
		wp_clear_scheduled_hook( 'wp_fastest_cache_Preload' );

		// update options
		update_option( 'WpFastestCache', json_encode( $options ) );
	}

	/**
	 * Delete all cache files.
	 *
	 * @return void
	 */
	private function delete_cache() {
		// check constant and function existence
		if ( defined( 'WPFC_DISABLE_HOOK_CLEAR_ALL_CACHE' ) && WPFC_DISABLE_HOOK_CLEAR_ALL_CACHE && isset( $GLOBALS['wp_fastest_cache'] ) && method_exists( $GLOBALS['wp_fastest_cache'], 'deleteCache' ) ) {
			// bypass constant and call function directly
			$GLOBALS['wp_fastest_cache']->deleteCache( true );
		} else {
			// call function normally
			wpfc_clear_all_cache( true );
		}
	}
}

new Cookie_Notice_Modules_WPFastestCache();