<?php
// exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Check if cookies are accepted.
 *
 * @return bool Whether cookies are accepted
 */
if ( ! function_exists( 'cn_cookies_accepted' ) ) {
	function cn_cookies_accepted() {
		return (bool) Cookie_Notice::cookies_accepted();
	}
}

/**
 * Check if cookies are set.
 *
 * @return bool Whether cookies are set
 */
if ( ! function_exists( 'cn_cookies_set' ) ) {
	function cn_cookies_set() {
		return (bool) Cookie_Notice::cookies_set();
	}
}

/**
 * Get active caching plugins.
 *
 * @param array $args
 * @return array
 */
function cn_get_active_caching_plugins( $args = [] ) {
	if ( isset( $args['versions'] ) && $args['versions'] === true )
		$version = true;
	else
		$version = false;

	$active_plugins = [];

	// autoptimize 2.4.0+
	if ( cn_is_plugin_active( 'autoptimize' ) ) {
		if ( $version )
			$active_plugins['Autoptimize'] = '2.4.0';
		else
			$active_plugins[] = 'Autoptimize';
	}

	// litespeed 3.0.0+
	if ( cn_is_plugin_active( 'litespeed' ) ) {
		if ( $version )
			$active_plugins['LiteSpeed Cache'] = '3.0.0';
		else
			$active_plugins[] = 'LiteSpeed Cache';
	}

	// siteground optimizer 5.5.0+
	if ( cn_is_plugin_active( 'sgoptimizer' ) ) {
		if ( $version )
			$active_plugins['SiteGround Optimizer'] = '5.5.0';
		else
			$active_plugins[] = 'SiteGround Optimizer';
	}

	// wp fastest cache 1.0.0+
	if ( cn_is_plugin_active( 'wpfastestcache' ) ) {
		if ( $version )
			$active_plugins['WP Fastest Cache'] = '1.0.0';
		else
			$active_plugins[] = 'WP Fastest Cache';
	}

	// wp rocket 3.8.0+
	if ( cn_is_plugin_active( 'wprocket' ) ) {
		if ( $version )
			$active_plugins['WP Rocket'] = '3.8.0';
		else
			$active_plugins[] = 'WP Rocket';
	}

	// wp super cache 1.6.9+
	if ( cn_is_plugin_active( 'wpsupercache' ) ) {
		if ( $version )
			$active_plugins['WP Super Cache'] = '1.6.9';
		else
			$active_plugins[] = 'WP Super Cache';
	}

	return $active_plugins;
}

/**
 * Check whether specified plugin is active.
 *
 * @global object $siteground_optimizer_loader
 * @global int $wpsc_version
 *
 * @return bool
 */
function cn_is_plugin_active( $plugin = '' ) {
	// no valid plugin?
	if ( ! in_array( $plugin, [ 'autoptimize', 'litespeed', 'sgoptimizer', 'wpfastestcache', 'wprocket', 'wpsupercache', 'contactform7', 'elementor', 'amp' ], true ) )
		return false;

	// autoptimize 2.4.0+
	if ( $plugin === 'autoptimize' && function_exists( 'autoptimize' ) && defined( 'AUTOPTIMIZE_PLUGIN_VERSION' ) && version_compare( AUTOPTIMIZE_PLUGIN_VERSION, '2.4', '>=' ) )
		return true;
	// litespeed 3.0.0+
	elseif ( $plugin === 'litespeed' && class_exists( 'LiteSpeed\Core' ) && defined( 'LSCWP_CUR_V' ) && version_compare( LSCWP_CUR_V, '3.0', '>=' ) )
		return true;
	// siteground optimizer 5.5.0+
	elseif ( $plugin === 'sgoptimizer' ) {
		global $siteground_optimizer_loader;

		if ( ! empty( $siteground_optimizer_loader ) && is_object( $siteground_optimizer_loader ) && is_a( $siteground_optimizer_loader, 'SiteGround_Optimizer\Loader\Loader' ) && defined( '\SiteGround_Optimizer\VERSION' ) && version_compare( \SiteGround_Optimizer\VERSION, '5.5', '>=' ) )
			return true;
	// wp fastest cache 1.0.0+
	} elseif ( $plugin === 'wpfastestcache' && function_exists( 'wpfc_clear_all_cache' ) )
		return true;
	// wp rocket 3.8.0+
	elseif ( $plugin === 'wprocket' && function_exists( 'rocket_init' ) && defined( 'WP_ROCKET_VERSION' ) && version_compare( WP_ROCKET_VERSION, '3.8', '>=' ) )
		return true;
	// wp super cache 1.6.9+
	elseif ( $plugin === 'wpsupercache' ) {
		global $wpsc_version;

		if ( ( ( ! empty( $wpsc_version ) && $wpsc_version >= 169 ) || ( defined( 'WPSC_VERSION' ) && version_compare( WPSC_VERSION, '1.6.9', '>=' ) ) ) )
			return true;
	// contact form 5.1.0+
	} elseif ( $plugin === 'contactform7' && class_exists( 'WPCF7' ) && class_exists( 'WPCF7_RECAPTCHA' ) && defined( 'WPCF7_VERSION' ) && version_compare( WPCF7_VERSION, '5.1', '>=' ) )
		return true;
	// elementor 1.3.0+
	elseif ( $plugin === 'elementor' && did_action( 'elementor/loaded' ) && defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, '1.3', '>=' ) )
		return true;
	// amp 2.0.0+
	elseif ( $plugin === 'amp' && function_exists( 'amp_is_enabled' ) && defined( 'AMP__VERSION' ) && version_compare( AMP__VERSION, '2.0', '>=' ) )
		return true;

	return false;
}