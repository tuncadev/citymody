<?php
/**
 *
 */
namespace mlcf7pll\frontend;


new Form_Locale();
class Form_Locale
{

    public function __construct()
    {

        if (is_admin())
            return;

        add_filter('get_post_metadata', [$this, 'fix_form_locale'], 20, 5);

    }

    /**
     * change cf7 form lang attribute to the current polylang language
     *
     * example: <div role="form" class="wpcf7" id="wpcf7-f5-p13-o1" lang="fr-FR" dir="ltr">
     *
     * @param $value
     * @param $object_id
     * @param $meta_key
     * @param $single
     * @param $meta_type
     * @return false|\PLL_Language|string
     */
    public function fix_form_locale($value, $object_id, $meta_key, $single, $meta_type ){

        if($meta_key == '_locale'){
            $post = get_post($object_id);
            if (is_object($post) && !empty($post->post_type) && $post->post_type == 'wpcf7_contact_form'){
                if(function_exists('pll_current_language')){
                    $value = pll_current_language('locale');
                }
            }
        }
        return $value;
    }
}
