<?php
/**
 *  Plugin Name: Civi Framework
 *  Plugin URI: https://uxper.co/
 *  Description: Civi Framework.
 *  Version: 1.0.4
 *  Author: Uxper
 *  Author URI: https://uxper.co/
 *  Text Domain: civi-framework
 *
 *  @package Civi Framework
 *  @author uxper
 *
 **/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('Civi_Framework')) {
    class Civi_Framework
    {

        public function __construct()
        {

            $this->define_constants();
            $this->load_textdomain();

            register_deactivation_hook(__FILE__, array($this, 'civi_deactivate'));
            add_action('plugins_loaded', array($this, 'includes'));
            add_filter('upload_mimes', array($this, 'civi_svg_upload'));
            add_filter('kirki/config', array($this, 'kirki_update_url'), 10, 1);

            if (is_multisite()) {
                $blog_id = get_current_blog_id();
                $upload_path = WP_CONTENT_DIR . '/uploads/sites/' . $blog_id . '/';
            }
        }

        /**
         *  Define constant
         **/
        private function define_constants()
        {

            $theme = wp_get_theme();
            if (!empty($theme['Template'])) {
                $theme = wp_get_theme($theme['Template']);
            }
            $plugin_dir_name = dirname(__FILE__);
            $plugin_dir_name = str_replace('\\', '/', $plugin_dir_name);
            $plugin_dir_name = explode('/', $plugin_dir_name);
            $plugin_dir_name = end($plugin_dir_name);

            if (!defined('CIVI_PLUGIN_FILE')) {
                define('CIVI_PLUGIN_FILE', __FILE__);
            }

            if (!defined('CIVI_PLUGIN_NAME')) {
                define('CIVI_PLUGIN_NAME', $plugin_dir_name);
            }

            if (!defined('CIVI_PLUGIN_DIR')) {
                define('CIVI_PLUGIN_DIR', plugin_dir_path(__FILE__));
            }
            if (!defined('CIVI_PLUGIN_URL')) {
                define('CIVI_PLUGIN_URL', trailingslashit(plugins_url(CIVI_PLUGIN_NAME)));
            }

            if (!defined('CIVI_PLUGIN_PREFIX')) {
                define('CIVI_PLUGIN_PREFIX', 'civi');
            }

            if (!defined('CIVI_METABOX_PREFIX')) {
                define('CIVI_METABOX_PREFIX', 'civi-');
            }

            if (!defined('CIVI_OPTIONS_NAME')) {
                define('CIVI_OPTIONS_NAME', 'civi-framework');
            }

            if (!defined('CIVI_THEME_NAME')) {
                define('CIVI_THEME_NAME', $theme['Name']);
            }

            if (!defined('CIVI_THEME_SLUG')) {
                define('CIVI_THEME_SLUG', $theme['Template']);
            }

            if (!defined('CIVI_THEME_VERSION')) {
                define('CIVI_THEME_VERSION', $theme['Version']);
            }

            if (!defined('GLF_THEME_DIR')) {
                define('GLF_THEME_DIR', get_template_directory());
            }

            if (!defined('GLF_THEME_URL')) {
                define('GLF_THEME_URL', get_template_directory_uri());
            }

            if (!defined('GLF_THEME_SLUG')) {
                define('GLF_THEME_SLUG', $theme['Template']);
            }

            if (!defined('CIVI_PLUGIN_VER')) {
                define('CIVI_PLUGIN_VER', '1.0.0');
            }

            if (!defined('CIVI_AJAX_URL')) {
                $ajax_url = admin_url('admin-ajax.php', 'relative');
                define('CIVI_AJAX_URL', $ajax_url);
            }
        }

        public function load_textdomain()
        {
            $mofile = CIVI_PLUGIN_DIR . 'languages/' . 'civi-framework-' . get_locale() . '.mo';

            if (file_exists($mofile)) {
                load_textdomain('civi-framework', $mofile);
            }
        }

        /**
         * The code that runs during plugin deactivation.
         */
        public function civi_deactivate()
        {
            require_once CIVI_PLUGIN_DIR . 'includes/class-civi-deactivator.php';
            Civi_Deactivator::deactivate();
        }

        /**
         * Upload Svg
         */
        public function civi_svg_upload($mimes) {
            $mimes['svg'] = 'image/svg+xml';
            return $mimes;
        }

        /**
         *  Includes
         **/
        public function includes()
        {

            if (!class_exists('Base_Framework')) {
                add_filter('civi_base_url', 'base_url', 1);

                function base_url()
                {
                    return CIVI_PLUGIN_URL . 'includes/base/';
                }
                require_once CIVI_PLUGIN_DIR . 'includes/base/base.php';
            }

            // Core
            include_once(CIVI_PLUGIN_DIR . 'includes/class-civi-core.php');

            // Kirki
            include_once(CIVI_PLUGIN_DIR . 'includes/kirki/kirki.php');

            // Base Widget
            include_once(CIVI_PLUGIN_DIR . 'modules/widgets/base.php');

            // Base Elementor
            include_once(CIVI_PLUGIN_DIR . 'modules/elementor/base.php');
        }

        /**
         *  Kirki update url
         **/
        public function kirki_update_url($config)
        {
            $config['url_path'] = CIVI_PLUGIN_URL . '/includes/kirki/';

            return $config;
        }

        /**
         *  Fix Upload Path Multisite
         **/
        public function fix_upload_paths($data)
        {
            $data['basedir'] = $data['basedir'] . '/sites/' . get_current_blog_id();
            $data['path'] = $data['basedir'] . $data['subdir'];
            $data['baseurl'] = $data['baseurl'] . '/sites/' . get_current_blog_id();
            $data['url'] = $data['baseurl'] . $data['subdir'];

            return $data;
        }
    }

    new Civi_Framework();
}
