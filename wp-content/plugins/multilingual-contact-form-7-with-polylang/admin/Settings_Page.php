<?php

namespace mlcf7pll\settings;

use mlcf7pll\core\settings\Settings_Page;

/**
 * Class My_Settings_Page
 *
 * @package mlcf7pll\settings
 */

class Mlcf7pll_Settings_Page extends Settings_Page
{

    public function __construct($plugin_basename=null)
    {

        $args = [
            'slug' => 'mlcf7pll-plugin-settings',
            'settings_prefix' => 'mlcf7pll_',
            'page_title' => __('Multilangual CF7 Polylang', 'multilangual-cf7-polylang'),
            'settings' => $this->get_settings_fields()
        ];

        parent::__construct($args, $plugin_basename);
    }


    protected function get_settings_fields()
    {

        $settings = array(
            'section_one' => array(
                'title' =>  __('Experimental features','multilangual-cf7-polylang'),
                'description' => __('','multilangual-cf7-polylang'),
                'fields' => array(

                    array(
                        'name' => 'fix_ajax_form_messages',
                        'label' => __('Form messages fix','multilangual-cf7-polylang'),
                        'type' => 'checkbox',
                        'default' => 'on',
                        'description' => __('In some installations, form messages received after sending data are not being translated. Activate this feature if you have this issue. This may however cause another issue that directly after a language switch the translation may still be in the old language.','multilangual-cf7-polylang'),
                    ),
                ),
            )
        );

        return $settings;
    }


}