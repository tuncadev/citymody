<?php
/*
Plugin Name: Extended Shortcodes for Ultimate Membership Pro
Plugin URI: https://store.wpindeed.com/
Description: Extend Ultimate Membership Pro functionality with a list of shortcodes which can be used by admin in order to manage content restriction, helpful links for users and much more based on subscriptions.
Version: 1.6
Author: WPIndeed
Author URI: https://store.wpindeed.com

Text Domain: extended-shortcodes-for-ultimate-membership-pro
Domain Path: /languages

@package        Indeed Ultimate Membership Pro AddOn - Extended Shortcodes
@author           WPIndeed Development
*/
add_action( 'init', 'umpExtendedShortcodesRun');
if ( !function_exists( 'umpExtendedShortcodesRun' ) ):
function umpExtendedShortcodesRun(){
	include plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

	$pluginSettings = new \UmpESh\Settings();
	$viewObject = new \UmpESh\View();

	\UmpESh\Utilities::setSettings( $pluginSettings->get() );
	\UmpESh\Utilities::setLang();
	if ( !\UmpESh\Utilities::canRun() ){
			return;
	}

	if ( is_admin() ){
			$UmpEShAdmin = new \UmpESh\Admin\Main( $pluginSettings->get(), $viewObject );
	}
	$UmpESh = new \UmpESh\Main( $pluginSettings->get(), $viewObject );
}
endif;
