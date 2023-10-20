<?php

if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ){
  exit();
}
if ( !defined('IHC_PATH')){
    define( 'IHC_PATH', plugin_dir_path(__FILE__) );
}
require_once IHC_PATH . 'utilities.php';
require_once IHC_PATH . 'autoload.php';

// revoke
$ihcElCheck = new \Indeed\Ihc\Services\ElCheck();
$ihcElCheck->doRevoke();

if ( get_option('ihc_keep_data_after_delete') == 1 ){
  return;
}
// remove data
require_once plugin_dir_path(__FILE__) . 'classes/Ihc_Db.class.php';
Ihc_Db::do_uninstall();
require_once plugin_dir_path(__FILE__) . 'classes/OldLogs.php';
$oldLogs = new \Indeed\Ihc\OldLogs();
$oldLogs->ECP();
$oldLogs->WECP();
