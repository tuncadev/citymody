<?php
namespace mlcf7pll\frontend;

new Submission();
class Submission
{


    function __construct()
    {
        add_filter( 'wpcf7_posted_data', [$this, 'fix_pipes_values_in_posted_data'] );
    }


    /**
     * This enables the pipes feature together with string translation
     * Example: [checkbox* your-country "{China}|1" "{India}|2" "{San Marino}|3"]
     *
     * @fixes: https://wordpress.org/support/topic/pipes-and-multilingual-contact-form-7/
     *
     * @param $posted_data
     * @return mixed
     */
    function fix_pipes_values_in_posted_data($posted_data){

        if(!function_exists('pll__'))
            return $posted_data;

        $submission   = \WPCF7_Submission::get_instance();
        $contact_form = $submission->get_contact_form();

        $tags = $contact_form->scan_form_tags();

        $langs = pll_languages_list();

        foreach($tags as $tag){
            if(in_array($tag->basetype, ['radio', 'checkbox', 'select'])
                && !empty($tag->values) && is_array($tag->values)
                && self::has_curly_bracket_values($tag->values)
            ){
                if(!empty($tag->pipes)) {
                    // $tag->raw_name or $tag->name??
                    $posted_values = $posted_data[$tag->raw_name];
                    // fix issue: https://wordpress.org/support/topic/php-8-0-8-1-compatibility/
                    // most likely caused by file upload field from external plugin: https://wordpress.org/plugins/drag-and-drop-multiple-file-upload-contact-form-7/
                    // [mfile PacienteFiles filetypes:jpeg|jpg|png|docx|docs|doc|pdf|zip]
                    if(!is_countable($posted_values)) continue;

                    $pipesObj = $tag->pipes;
                    $pipes = $pipesObj->to_array();

                    foreach($pipes as $pipe){
                        $string_to_translate = str_replace(['{', '}'], '', $pipe[0]);
                        // search if this is translated to any language as we don´t know the current language here
                        foreach($langs as $lang){
                            $translated = pll_translate_string($string_to_translate, $lang);

                            for($i = 0; $i < count($posted_values); $i++){
                                if($posted_values[$i] == $translated) {
                                    // set value to pipe value "{word}|33" : "Wort" => 33
                                    $posted_data[$tag->raw_name][$i] = $pipe[1];
                                }
                            }
                        }
                    }
                }
            }
        }
        return $posted_data;

    }


    static function has_curly_bracket_values($arr){
        foreach($arr as $item){
            if(is_string($item) && strpos($item, '{' ) !== false){
                return true;
            }
        }
        return false;
    }
}



