<?php
namespace mlcf7pll\frontend;

use mlcf7pll\Helpers;

new String_Translation();
class String_Translation
{


    function __construct()
    {

        // translate the form itself
        add_filter( 'wpcf7_form_elements', [$this, 'translate_form_elements'] );

        // translate (AJAX) form messages
        add_filter( 'wpcf7_display_message', [$this, 'translate_cf7_messages'], 90, 2);

        // translate emails
        add_filter( 'wpcf7_mail_components', [$this, 'do_translate'], 90, 1);


    }


    /**
     * translate
     *
     * @param $text_or_array
     * @return array|mixed|string
     */
    function do_translate($text_or_array){

        if ( !is_textdomain_loaded( 'pll_string' ) ) {
            Helpers::load_string_translations_textdomain();
        }

        return Helpers::translate_translation_strings_recursive($text_or_array);
    }


    /**
     * translate the strings in the form
     *
     * @param $content
     * @return mixed
     */
    function translate_form_elements( $content ) {

        return $this->do_translate($content);

    }


    /**
     * DOES NOT WORK AS IN AJAX the 'pll_string' textdomain is not loaded for some reason, probably because pll does not know the current language, as it needs to be sent with the AJAX call? with /de/jjhj
     *
     * This is an optional way to translate the messages with polylang string translations
     * Alternative to the "normal" way via .mo-files implemented in the Messages_Translation Class
     *
     * @param $message
     * @param $status
     * @return string|void
     */
    function translate_cf7_messages($message, $status){

        return $this->do_translate($message);

    }


}