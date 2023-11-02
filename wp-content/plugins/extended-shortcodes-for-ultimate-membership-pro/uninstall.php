<?php
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) exit();
include plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

if ( !function_exists( 'ihc_return_meta_arr' ) ){
	return;
}
$settings = new \UmpESh\Settings();
$pluginSlug = $settings->getPluginSlug();
$settings = ihc_return_meta_arr( $pluginSlug );
if ( !$settings ){
    return;
}
foreach ( $settings as $optionName => $optionValue ){
    delete_option( $optionName );
}
