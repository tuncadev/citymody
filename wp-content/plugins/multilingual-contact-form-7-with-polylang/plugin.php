<?php
/**
 * Plugin Name: Multilingual Contact Form 7 with Polylang
 * Plugin URI:
 * Description: Enables translation and use of the same forms in different languages of Contact Form 7 forms with Polylang
 * Version: 1.0.8
 * Author: Andreas Münch
 * Author URI: https://andreasmuench.de
 * Text Domain: multilangual-cf7-polylang
 * Domain Path: /languages.
 *
 */


namespace mlcf7pll;

use mlcf7pll\settings\Mlcf7pll_Settings_Page;

/**
 * Prevent direct access data leaks.
 **/
if (!defined('ABSPATH')) {
    exit;
}


// initiate plugin
Plugin::instance();

/**
 * Main initiation class.
 *
 * @since  NEXT
 */
final class Plugin
{
    /**
     * Singleton instance of plugin.
     */
    private static $_instance = null;

    // the Plugin Name as defined above
    public static $plugin_name;
    // the Plugin Version as defined above
    public static $plugin_version;
    // e.g "example-plugin"
    public static $plugin_basename;
    public static $plugin_dir;
    public static $plugin_url;

    public static function instance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Plugin constructor
     * do stuff here that works immediately, because not all WP functions are loaded at this point
     */
    protected function __construct()
    {
        // get plugin data, get_plugin_data() does not work here and only in admin
        $plugin_data = get_file_data(__FILE__, [
            'Name' => 'Plugin Name',
            'Version' => 'Version',
        ], 'plugin');

        self::$plugin_name = $plugin_data['Name'];;
        self::$plugin_version = $plugin_data['Version'];;
        self::$plugin_basename = plugin_basename( __FILE__ );
        self::$plugin_dir = untrailingslashit(plugin_dir_path(__FILE__));
        self::$plugin_url = untrailingslashit(plugins_url(basename(plugin_dir_path(__FILE__)), basename(__FILE__)));

        self::init();

        register_activation_hook(__FILE__, array(__CLASS__, 'plugin_activation'));
        register_deactivation_hook(__FILE__, array(__CLASS__, 'plugin_deactivation'));

    }

    /**
     * init stuff
     */
    public static function init()
    {

        require_once('core/settings/Settings_Page.php');
        require_once('core/fields/Field.php');
        require_once('inc/Helpers.php');
        require_once('admin/String_Registration.php');
        require_once('admin/Settings_Page.php');
        require_once('frontend/Submission.php');
        require_once('frontend/String_Translation.php');
        require_once('frontend/Messages_Translation.php');
        require_once('frontend/Form_Locale.php');

        new Mlcf7pll_Settings_Page(self::$plugin_basename);


    }

    public static function plugin_activation(){

        error_log(self::$plugin_name.' plugin_activation');
    }

    public static function plugin_deactivation(){

        error_log(self::$plugin_name.' plugin_deactivation');
    }

}

