<?php
/**
 * Integrates live translation of messages,
 * instead of cf7´s weird approach to only translate once for the settings
 *
 * This makes it possible to use one form in multiple languages
 *
 * Requirement is, that the messages in the form are set to their default english texts,
 * so the translation can work
 *
 * todo:
 * make an option to override the configured messages
 * by using Helpers::get_untranslated_default_messages()
 * and get the default messages in translate_cf7_messages() by the $status
 *
 */
namespace mlcf7pll\frontend;

use mlcf7pll\Helpers;

new Messages_Translation();
class Messages_Translation
{

    public function __construct()
    {

        if (is_admin())
            return;

        add_filter('load_textdomain_mofile', [$this, 'load_textdomain_mofile']);

        add_filter('wpcf7_display_message', [$this, 'translate_cf7_messages'], 90, 2);

        if (get_option('mlcf7pll_fix_ajax_form_messages', 'on')){
            add_filter('locale', [$this, 'maybe_save_locale_to_cookie'], 9999);
        }
    }

    /**
     * As we need the pll_language cookie we must make sure it is set sooner than PLL would do it.
     * This fixes a bug that on first visit of a translated page (not primary language)
     * the cookie is not set and the messages will not be translated correctly
     *
     * @param $locale
     * @return mixed
     */
    function maybe_save_locale_to_cookie($locale){

        if(empty($_COOKIE['pll_language'])){
            $_COOKIE['pll_language'] = $locale;
        }
        return $locale;
    }



    /**
     * Fix translation of cf7 messages
     * Force loading the translation language version that is defined in polylang cookie
     * This is necessary as cf7 is not supporting translation of messages in different languages for multilanguage websites,
     * but only translates them once and saves the translations statically
     */
    function load_textdomain_mofile( $mofile ) {

        // if the cookie is not set, we cannot retrieve the current language, also do this only for cf7 mofiles and only in the REST requests (AJAX)
        if(empty($_COOKIE['pll_language']) || empty($mofile) || strpos($mofile, 'contact-form-7') === false || !Helpers::is_rest()){
            return $mofile;
        }

        $user_locale = get_user_locale();
        $pll_locale = Helpers::pll_get_locale_by_slug($_COOKIE['pll_language']);

        return str_replace( "{$user_locale}.mo", "{$pll_locale}.mo", $mofile );
    }


    /**
     * translate the cf7 messages
     * only works with the standard messages that are also defined in the translation files
     * if these are changed in the form settings, the translation does not work
     */
    function translate_cf7_messages($message, $status){

        // make sure the cf7 textdomain is loaded
        // this may not be the case if the base language of the site is US EN
        Helpers::maybe_load_cf7_textdomain();

        $translated = __($message, 'contact-form-7');
        return $translated;
    }


}






