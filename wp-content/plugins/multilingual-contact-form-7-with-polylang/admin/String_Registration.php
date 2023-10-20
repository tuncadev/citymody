<?php
namespace mlcf7pll\admin;

use mlcf7pll\Helpers;

new String_Registration();
class String_Registration
{


    function __construct()
    {
        add_action('admin_init', [$this, 'register_strings']);

    }

    /**
     * register Contact Form 7 Strings for translation in Polylang
     * searches for strings in curly brackets like {translate me}
     */
    function register_strings(){

        if(!class_exists('WPCF7_ContactForm'))
            return;

        $forms = \WPCF7_ContactForm::find();
        /* @var \WPCF7_ContactForm form  */
        foreach ($forms as $form){

            // get all form properties
            // this includes the form, email, email_2, messages
            // probably also supports if other plugins extend the properties (not tested)
            $texts_to_translate = $form->get_properties();

            // hook to include/exclude texts to/from translation
            $texts_to_translate = apply_filters('mlcf7pll_texts_to_translate', $texts_to_translate);

            // get the strings from the strings/arrays
            $this->register_strings_from_array_recursive($texts_to_translate);

        }

    }

    /**
     * searches for all strings in curly brackets in strings and arrays to register them for polylang
     *
     * @param $string_or_array string|array
     */
    protected function register_strings_from_array_recursive($string_or_array){
        // don´t do anything if polylang isn´t active
        if(!function_exists('pll_register_string')) {
            return;
        }
        if(is_array($string_or_array)){
            foreach($string_or_array as $item){
                $this->register_strings_from_array_recursive($item);
            }
        } else {
            // get translate strings from text, e.g. {Senden}
            $strings = Helpers::extract_translation_strings($string_or_array);
            foreach ($strings as $string){
                pll_register_string('cf7-'.sanitize_title($string), $string, 'Contact Form 7');
            }
        }
    }


}