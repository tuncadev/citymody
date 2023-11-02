<?php
namespace UmpESh;

class Utilities
{
    /**
     * @var string
     */
    private static $parentMinimumVersion           = '';
    /**
     * @var string
     */
    private static $parentPluginLocation           = '/plugins/indeed-membership-pro/indeed-membership-pro.php';
    /**
     * @var string
     */
    private static $addOnName                      = '';
    /**
     * @var string
     */
    private static $pluginParentName               = 'Ultimate Membership Pro';
    /**
     * @var string
     */
    private static $pluginParentConstantName       = 'IHC_PATH';
    /**
     * @var string
     */
    private static $langDomain                     = '';
    /**
	* @var array
	 */
	private static $addOnSettings			             = [];

    /**
     * @param array
     * @return none
     */
    public static function setSettings($settings=[])
    {
        self::$addOnSettings = $settings;
        self::$langDomain = self::$addOnSettings['lang_domain'];
        self::$addOnName = self::$addOnSettings['name'];
        self::$parentMinimumVersion = self::$addOnSettings['ump_min_version'];
    }

    public static function setLang()
    {
        // language
        add_action('plugins_loaded', function(){
           load_plugin_textdomain( self::$langDomain, false, self::$addOnSettings['plugin_base_name'] . '/languages/' );
        });
    }


    /**
     * @param none
     * @return bool
     */
    public static function canRun()
    {
        // check if ump is instaled
        if ( !defined( self::$pluginParentConstantName ) ){
            add_action( 'admin_notices', [ get_class(), 'adminNotice' ] );
            return false;
        }

        // check if ump version is not too old
        $parentCurrentVersion = self::getCurrentVersion( WP_CONTENT_DIR . self::$parentPluginLocation );
        if ( version_compare( self::$parentMinimumVersion, $parentCurrentVersion, '>' ) ){
            add_action( 'admin_notices', [ get_class(), 'adminNoticeVersion' ] );
            return false;
        }
        return true;
    }

    /**
     * @param string
     * @return string
     */
    private static function getCurrentVersion( $pluginName='' )
    {
        if ( ! function_exists('get_plugin_data') ){
            require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        }
        $pluginData = get_plugin_data( $pluginName );
    		return isset( $pluginData['Version'] ) ? $pluginData['Version'] : 0;
    }

    /**
     * @param none
     * @return none
     */
    public static function adminNotice()
    {
        $admin_notice = '<div class="error"><p>' . sprintf( __( '%s is inactive. The %s plugin must be active for %s to work. Please install & activate %s.', 'extended-shortcodes-for-ultimate-membership-pro' ), self::$addOnName, self::$pluginParentName, self::$addOnName, self::$pluginParentName )  . '</p></div>';
        $allowedHtml = [
                          'div' 		=> [ 'class' => [] ],
                          'p'				=> [],
        ];
        echo wp_kses( $admin_notice, $allowedHtml );
    }

    /**
     * @param none
     * @return none
     */
    public static function adminNoticeVersion()
    {
        $admin_notice_version = '<div class="error"><p>' . sprintf( __( 'In order to use %s you must have %s starting with v.%s.', 'extended-shortcodes-for-ultimate-membership-pro' ), self::$addOnName, self::$pluginParentName, self::$parentMinimumVersion )  . '</p></div>';
        $allowedHtml = [
                          'div' 		=> [ 'class' => [] ],
                          'p'				=> [],
        ];
        echo wp_kses( $admin_notice_version, $allowedHtml );
    }

}
