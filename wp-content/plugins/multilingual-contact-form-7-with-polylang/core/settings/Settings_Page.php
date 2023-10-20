<?php

namespace mlcf7pll\core\settings;

use mlcf7pll\core\fields\Field;

/**
 * Class Settings_Page
 *
 * Simple Single Page Settings with Sections but without Tabs
 *
 * This Class should be extended!
 *
 * based on https://gist.github.com/hlashbrooke/9267467
 *
 * @package mlcf7pll\settings
 */
class Settings_Page
{

    protected $args;
    protected $settings;

    /**
     * Settings_Page constructor.
     * @param array $args
     */
    public function __construct($args = null, $plugin_basename=null)
    {
        $this->args = wp_parse_args(

            $args,
            [
                'slug' => 'example-plugin-settings',
                'settings_prefix' => 'ep_',
                'page_title' => __('Example Plugin Settings','multilangual-cf7-polylang'),
                'settings' => array()
            ]
        );

        // Initialise settings
        add_action('admin_menu', array($this, 'init'));

        // Add settings page to menu
        add_action('admin_menu', array($this, 'add_menu_item'));

        // Register plugin settings
        add_action('admin_init', array($this, 'register_settings'));

        // Add settings link to plugins page
        if(!empty($plugin_basename)){
            add_filter('plugin_action_links_'.$plugin_basename, array($this, 'add_settings_link'));
        }

    }


    /**
     *
     */
    function init(){

        /**
         * make settings extendable by other plugins or themes
         */
       $this->settings =  apply_filters($this->args['slug'].'_settings_fields', $this->args['settings']);

    }


    /**
     * Add settings page to admin menu
     * @return void
     */
    public function add_menu_item()
    {
        // add to WP settings as subpage menu item
        $page = add_submenu_page(
            'options-general.php',
            $this->args['page_title'],
            $this->args['page_title'],
            'manage_options',
             $this->args['slug'],
            array($this, 'render_page')
        );

//        add_action( 'admin_print_styles-' . $page, array( $this, 'settings_assets' ) );
    }


    /**
     * Add settings link to plugin list table
     * @param array $links Existing links
     * @return array        Modified links
     */
    public function add_settings_link($links) {
        $link = '<a href="admin.php?page='.$this->args['slug'].'">'.__('Settings').'</a>';
        array_unshift( $links, $link);
        return $links;
    }

    /**
     * Build settings fields
     * override in Child Class
     * @return array Fields to be displayed on settings page
     */
    protected function get_settings_fields()
    {
        $settings = array();

        return $settings;
    }

    /**
     * Register plugin settings
     * @return void
     */
    public function register_settings()
    {
        if (is_array($this->settings)) {
            foreach ($this->settings as $section => $data) {

                // Add section to page
                add_settings_section($section, $data['title'], [$this, 'settings_section'], $this->args['slug']);

                foreach ($data['fields'] as $field) {

                    // Register field
                    $option_name = $this->args['settings_prefix'] . $field['name'];


                    $register_setting_args = [];
                    // Sanitize callback for field
                    $sanitize_callback = '';
                    if (isset($field['sanitize_callback'])) {
                        $register_setting_args['sanitize_callback'] = $field['sanitize_callback'];
                    }

                    register_setting(
                        $this->args['slug'],
                        $option_name,
                        $register_setting_args
                    );

                    // Add field to page
                    add_settings_field(
                        $field['name'],
                        $field['label'],
                        array($this, 'display_field'),
                        $this->args['slug'],
                        $section,
                        array('field' => $field)
                    );
                }
            }
        }
    }

    public function settings_section($section)
    {
        // output section description if defined
        if(!empty($this->settings[$section['id']]['description'])){
            echo '<p>' . $this->settings[$section['id']]['description'] . '</p>' . PHP_EOL;
        }

    }


    /**
     * Generate HTML for displaying fields
     * TODO: separate this into new Class Fields to make it usable by Metaboxes AND Settings/Tool Pages
     * @param array $args Field data
     * @return void
     */
    public function display_field($args)
    {

        $field = $args['field'];

        if(empty($field['id'])){
            $field['id'] =  $field['name'];
        }

        // fragwürdig, lieber gleich in der data mit prefix definieren?
        $field['name'] = $this->args['settings_prefix'] . $field['name'];

        $option = get_option($field['name']);

        $value = '';
        if (isset($field['default'])) {
            $value = $field['default'];
        }

        // für checkboxes kompliziert wenn default = true ist
        // Eintrag nicht vorhanden: $option === false
        // Eintrag positiv: $option === 'on'  etc.
        // Eintrag vorhanden, aber nicht positiv: option !== false
        if($field['type'] == 'checkbox'){
            if($option !== false) $value = $option;
        } else if ($option) {
            $value = $option;
        }

        if ($option && ($option !== false || $field['type'] != 'checkbox')) {
            $value = $option;
        }

        $field = new Field($field);
        $field->render($value);
    }


    /**
     * Load settings page content
     * @return void
     */
    public function render_page()
    {
        // Build page HTML
        $html = '';
        $html .= '<div class="wrap" id="'.$this->args['slug'].'">' . PHP_EOL;
        $html .= '<h1>'.$this->args['page_title'].'</h1>';
        $html .= '<form method="post" action="options.php" enctype="multipart/form-data">' . PHP_EOL;

        ob_start();

        settings_fields($this->args['slug']);

        do_settings_sections($this->args['slug']);

        submit_button();

        $html .= ob_get_clean();

        $html .= '</form>' . PHP_EOL;
        $html .= '</div>' . PHP_EOL;

        echo $html;
    }






}
