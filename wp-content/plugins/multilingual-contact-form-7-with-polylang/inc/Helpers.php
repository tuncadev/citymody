<?php
namespace mlcf7pll;


class Helpers
{


    /**
     * translates translation strings in a text or all texts in an array
     *
     * example:
     * translate_translation_strings('{Message}:');
     * -> 'Nachricht:';
     *
     * @param $text_or_array string|array
     * @return mixed
     */
    static function translate_translation_strings_recursive($text_or_array){
        $pll_active = function_exists('pll__');

        if(is_array($text_or_array)){
            foreach($text_or_array as $key => $item){
                $text_or_array[$key] = self::translate_translation_strings_recursive($item);
            }
            return $text_or_array;

        } else {
            // is a simple string
            $strings = Helpers::extract_translation_strings($text_or_array);

            foreach ($strings as $string){
                if($pll_active) {
                    // cf7 sometimes outputs html decoded and sometimes html encoded (in attributes)
                    // as in polylang it is always saved as decoded, we must translate the decoded string
                    $string_decoded = html_entity_decode($string, ENT_QUOTES);
//                    $string_decoded = htmlspecialchars_decode($string_decoded, ENT_QUOTES);

                    $text_or_array = str_replace('{'.$string.'}', pll__($string_decoded), $text_or_array);

                } else {
                    // if polylang is not active we simply remove the curly braces
                    $text_or_array = str_replace('{'.$string.'}', $string, $text_or_array);
                }
            }
            return $text_or_array;
        }
    }



    /**
     * get strings to translate from string
     *
     * example:
     * extract_translation_strings('This {foo} is {bar}');
     * -> ['foo', 'bar']
     *
     * @param $string
     * @return mixed
     */
    static function extract_translation_strings($string){

        $pattern = '~{(.*?)}~s';
        preg_match_all($pattern, $string, $matches);

        return $matches[1];
    }


    /**
     * helper function to get the locale ('de_DE') from the slug ('de')
     *
     * @param $slug
     * @return |null
     */
    static function pll_get_locale_by_slug($slug){
        if(!function_exists('pll_languages_list')){
            return null;
        }
        $langs = pll_languages_list(['fields' => []]);
        foreach($langs as $lang){
            if($lang->slug==$slug){
                return $lang->locale;
            }
        }
        return null;
    }


    /**
     * can be used to override the message settings if we want to force the translation of the original strings
     *
     * @return mixed|void
     */
    static function get_untranslated_default_messages(){

        // add filter to disable translating temporarily
        add_filter('gettext', 'mlcf7pll\Helpers::deactivate_gettext', 999, 3 );

        $messages = wpcf7_messages();

        // remove the filter to make translating work again
        remove_filter('gettext', 'mlcf7pll\Helpers::deactivate_gettext', 999);

        return $messages;

    }

    /**
     * simply returns the original (english) text, disables translating
     *
     * @param $translation
     * @param $text
     * @param $domain
     * @return mixed
     */
    static function deactivate_gettext( $translation, $text, $domain){
        return $text;
    }


    /**
     * in AJAX (?) we need to manually load the string translations
     * we do this based on the polylang cookie pll_language
     * This may not work in all cases?
     * 
     * loading og the textdomain is mainly copied from how polylang does it
     */
    static function load_string_translations_textdomain(){
        // don´t do anything if polylang isn´t active
        if(!function_exists('pll__')) {
            return;
        }

        // no cookie? we have no way to get the current language
        if(empty($_COOKIE['pll_language'])){
            return;
        }

        $languages = get_terms( 'language', array( 'hide_empty' => false, 'orderby' => 'term_group' ) );

        foreach ($languages as $lang_term){
            if($lang_term->slug == $_COOKIE['pll_language']){
                // quickly fake language array
                $lang_data = (array)$lang_term;
                $lang_data['term_props']['language'] = (array)$lang_term;
                $language = new \PLL_Language($lang_data);
            }
        }

        if ( ! empty( $language ) ) {
            $mo = new \PLL_MO();
            $mo->import_from_db($language);
            $GLOBALS['l10n']['pll_string'] = &$mo;
        }
    }



    static function maybe_load_cf7_textdomain(){

        if(is_textdomain_loaded('contact-form-7'))
            return;

        // temporarily make determine_locale() return "our" polylang cookie locale, so _get_path_to_translation_from_lang_dir() works
        add_filter('pre_determine_locale', 'mlcf7pll\Helpers::pre_determine_locale', 20);

        //todo deprecated? -> maybe replace with  _load_textdomain_just_in_time('contact-form-7'); ?
        $path = _get_path_to_translation_from_lang_dir( 'contact-form-7' );
        remove_filter('pre_determine_locale', 'mlcf7pll\Helpers::pre_determine_locale', 20);

        load_textdomain('contact-form-7', $path);


    }

    /**
     * return the locale defined in the polylang cookie
     *
     * @param $locale
     * @return |null
     */
    static function pre_determine_locale($locale){

        if(empty($_COOKIE['pll_language']))
            return $locale;

        return Helpers::pll_get_locale_by_slug($_COOKIE['pll_language']);

    }

    /**
     * Checks if the current request is a WP REST API request.
     *
     * Case #1: After WP_REST_Request initialisation
     * Case #2: Support "plain" permalink settings and check if `rest_route` starts with `/`
     * Case #3: It can happen that WP_Rewrite is not yet initialized,
     *          so do this (wp-settings.php)
     * Case #4: URL Path begins with wp-json/ (your REST prefix)
     *          Also supports WP installations in subfolders
     *
     * @returns boolean
     * @author matzeeable
     */
    static function is_rest() {
        if (defined('REST_REQUEST') && REST_REQUEST // (#1)
            || isset($_GET['rest_route']) // (#2)
            && strpos( $_GET['rest_route'], '/', 0 ) === 0)
            return true;

        // (#3)
        global $wp_rewrite;
        if ($wp_rewrite === null) $wp_rewrite = new \WP_Rewrite();

        // (#4)
        $rest_url = wp_parse_url( trailingslashit( rest_url( ) ) );
        $current_url = wp_parse_url( add_query_arg( array( ) ) );
        return strpos( $current_url['path'], $rest_url['path'], 0 ) === 0;
    }


    /**
     * multibyte str_replace, currently not in use
     *
     * @param $search
     * @param $replace
     * @param $subject
     * @param $count
     * @return mixed|string
     */
    static function mb_str_replace($search, $replace, $subject, &$count = 0)
    {
        if (!is_array($subject))
        {
            $searches = is_array($search) ? array_values($search) : array($search);
            $replacements = is_array($replace) ? array_values($replace) : array($replace);
            $replacements = array_pad($replacements, count($searches), '');
            foreach ($searches as $key => $search)
            {
                $parts = mb_split(preg_quote($search), $subject);
                $count += count($parts) - 1;
                $subject = implode($replacements[$key], $parts);
            }
        }
        else
        {
            foreach ($subject as $key => $value)
            {
                $subject[$key] = self::mb_str_replace($search, $replace, $value, $count);
            }
        }
        return $subject;
    }


}