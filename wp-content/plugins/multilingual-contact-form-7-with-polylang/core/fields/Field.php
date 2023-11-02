<?php

namespace mlcf7pll\core\fields;

/**
 * Fields for Metaboxes, Settings and Tools Pages
 *
 * based on https://gist.github.com/hlashbrooke/9267467
 */
class Field
{

    protected $args;

    /**
     * @var array already enqueued scripts and styles
     */
    static $enqueued = [];

    public function __construct($args)
    {
        $this->args = $args;
    }



    /**
     * Generate HTML for displaying fields
     *
     * @return void
     */
    public function render($value)
    {
        $this->enqueue_standard_styles();

//        $field = $args['field'];
        $args = $this->args;

//        \el('$args', $args);
        

        if(empty($args['id'])){
            $args['id'] =  $args['name'];
        }

        // maybe retrieve select options via a callback
        if(!empty($args['options_callback'])){
            $args['options'] = call_user_func($args['options_callback']);
        }


        $html = '';

        switch ($args['type']) {

            case 'text':
            case 'password':
            case 'number':
            case 'time':

            case 'email':
                $html .= $this->label($args);
                $html .= '<input id="' . esc_attr($args['id']) . '" 
                        type="' . $args['type'] . '" 
                        name="' . esc_attr($args['name']) . '" '. $this->placeholder($args). ' 
                        value="' . $value . '" 
                        class="dwe-field regular-text"/>' . PHP_EOL;
                $html .= $this->description($args);
                break;

            case 'textarea':
                $html .= $this->label($args);
                $html .= $this->description($args);
                $html .= '<p><textarea id="' . esc_attr($args['id']) . '" 
                    rows="5" cols="50" 
                    name="' . esc_attr($args['name']) . '" '. $this->placeholder($args). ' 
                    class="dwe-field large-text">' . $value . '</textarea></p>' . PHP_EOL;
                break;


            case 'select':
                $html .= $this->label($args);
                $html .= '<select name="' . esc_attr($args['name']) . '" id="' . esc_attr($args['id']) . '">';
                foreach ($args['options'] as $k => $v) {
                    $selected = false;
                    if ($k == $value) {
                        $selected = true;
                    }
                    $html .= '<option ' . selected($selected, true, false) . ' value="' . esc_attr($k) . '">' . $v . '</option>';
                }
                $html .= '</select> ';
                $html .= $this->description($args);
                break;

            case 'select_multi':
                $html .= $this->label($args);
                if(!is_array($value)) $value = [$value];
                $html .= $this->description($args);
                $html .= '<select name="' . esc_attr($args['name']) . '[]" id="' . esc_attr($args['id']) . '" multiple="multiple">';
                foreach ($args['options'] as $k => $v) {
                    $selected = false;
                    if (in_array($k, $value)) {
                        $selected = true;
                    }
                    $html .= '<option ' . selected($selected, true, false) . ' value="' . esc_attr($k) . '" />' . $v . '</label> ';
                }
                $html .= '</select> ';
                break;

            case 'checkbox':
//                $html .= $this->label($args);
                $checked = '';
                if (!empty($value) && 'on' == $value) {
                    $checked = 'checked="checked"';
                }
                $html .= '<label for="' . esc_attr($args['id']) . '">
                            <input id="' . esc_attr($args['id']) . '" type="' . $args['type'] . '" name="' . esc_attr($args['name']) . '" ' . $checked . '/>
                            '.$args['description'].'
                          </label>' . PHP_EOL;
                break;

            case 'checkbox_multi':
                $html .= $this->label($args);
                if(!is_array($value)) $value = [$value];
                foreach ($args['options'] as $k => $v) {
                    $checked = false;
                    if (in_array($k, $value)) {
                        $checked = true;
                    }
                    $html .= '<label for="' . esc_attr($args['id'] . '_' . $k) . '"><input type="checkbox" ' . checked($checked, true, false) . ' name="' . esc_attr($args['name']) . '[]" value="' . esc_attr($k) . '" id="' . esc_attr($args['id'] . '_' . $k) . '" /> ' . $v . '</label> ';
                }
                $html .= $this->description($args);
                break;

            case 'radio':
                $html .= $this->label($args);
                $html .= '<fieldset>
                    <legend class="screen-reader-text"><span>'.  $args['label']  .'</span></legend>';
                    $html .= $this->description($args);
                     foreach ($args['options'] as $k => $v) {
                         $checked = ($k == $value);
                         $html .= '<label><input type="radio" ' . checked($checked, true, false) . ' name="' . esc_attr($args['name']) . '" value="' . esc_attr($k) . '" > ' . $v . '</label><br> ';
                     }

                $html .= '</fieldset>';

                break;


            case 'file':
                $html .= $this->label($args);
                $this->enqueue_fileupload_scripts();
                $thumb_src = '';
                $thumbnail_name = '';
                if ($value) {
                    $att = wp_get_attachment_metadata($value);
                    //  $mime_type = get_post_mime_type($value);
                    $thumb_src = wp_get_attachment_thumb_url($value);
                    if(empty($thumb_src)){
                        $thumb_src= home_url() .'/wp-includes/images/media/document.png';
                    }
                    $thumbnail_name = basename ( get_attached_file( $value ) );
                }
                $html .= $this->description($args);
                $html .= '<div id="' . $args['name'] . '_preview" class="file_preview">'. PHP_EOL;
                $html .= '  <img id="' . $args['name'] . '_thumb" class="file_thumb" src="' . $thumb_src . '"  /><br/>' . PHP_EOL;
                $html .= '  <div id="' . $args['name'] . '_filename" class="" style="padding-bottom:8px">'.$thumbnail_name.'</div>' . PHP_EOL;
                $html .= '</div>'. PHP_EOL;
                $html .= '<input id="' . $args['name'] . '_button" type="button" data-uploader_title="' . __('Upload file') . '" data-uploader_button_text="' . __('Use file') . '" class="button file_upload_button" value="' . __('Upload File') . '" />' . PHP_EOL;
                $html .= '<input id="' . $args['name'] . '_delete" type="button" class="button file_remove_button" value="' . __('Remove file') . '" />' . PHP_EOL;
                $html .= '<input id="' . $args['name'] . '" class="file_data_field" type="hidden" name="' . $args['name'] . '" value="' . $value . '"/><br/>' . PHP_EOL;
                break;

            case 'color':
                $this->enqueue_colorpicker_scripts();
                $html .= $this->label($args);
                $html .='<div class="color-picker" style="position:relative;">';
                $html .='<input type="text" name="'.esc_attr_e($args['name']).'" class="color" value="'. esc_attr_e($value).'"/>';
                $html .='<div style="position:absolute;background:#FFF;z-index:99;border-radius:100%;" class="colorpicker"></div>';
                $html .='</div>';
                break;

        }


        echo $html;
    }


    /**
     * get description html
     *
     * @param $args
     */
    function description($args){

        if (empty($args['description'])) {
            return '';
        }

        return '<p id="' . esc_attr($args['id']) . '-description" class="description">' . $args['description'] . '</p>' . PHP_EOL;

    }

    /**
     * @param $args
     * @return string
     */
    function placeholder($args){

        if (empty($args['placeholder'])) {
            return '';
        }

        return ' placeholder="' .  esc_attr($args['placeholder']) .'" ';

    }

    /**
     * @param $args
     * @return string
     */
    function label($args){
        if (empty($args['show_label'])) {
            return '';
        }

        $for = !empty($args['id']) ? ' for="'.esc_html($args['id']).'" ' : '';

        return '<label class="dwe-field-label" '.$for.'>'.esc_html($args['label']).'</label>'.PHP_EOL;

    }




    /**
     * our custom scripts for different field types
     */
    function enqueue_fields_scripts(){

        // only enqueue once
        $name = 'fields';
        if(in_array($name, self::$enqueued)) return;

        wp_register_script( 'fields-admin-js', plugin_dir_url(__FILE__) . 'js/settings.js', array( 'farbtastic', 'jquery' ), '1.0.0' );
        wp_enqueue_script( 'fields-admin-js' );


        self::$enqueued[] = $name;

    }

    /**
     * farbtastic for colorpicker
     */
    function enqueue_colorpicker_scripts(){
        // only enqueue once
        $name = 'farbtastic';
        if(in_array($name, self::$enqueued)) return;

        // We're including the farbtastic script & styles here because they're needed for the colour picker
        // If you're not including a colour picker field then you can leave these calls out as well as the farbtastic dependency for the wpt-admin-js script below
        wp_enqueue_style( 'farbtastic' );
        wp_enqueue_script( 'farbtastic' );

        wp_enqueue_script( 'field-colorpicker', plugin_dir_url(__FILE__) . 'js/field_colorpicker.js', array( 'farbtastic', 'jquery' ), '1.0.0' );
//        wp_enqueue_script( 'fields-colorpicker' );

        self::$enqueued[] = $name;
    }

    /**
     * wp media for image upload
     */
    function enqueue_fileupload_scripts(){
        // only enqueue once
        $name = 'wp_media';
        if(in_array($name, self::$enqueued)) return;

        // We're including the WP media scripts here because they're needed for the file upload field
        // If you're not including an file upload then you can leave this function call out
        wp_enqueue_media();

        wp_enqueue_script( 'field-file', plugin_dir_url(__FILE__) . 'js/field_file.js', array( 'jquery' ), '1.0.0' );

        self::$enqueued[] = $name;
    }

    /**
     * enqueue custom styles so the same fields work better in settings pages and in metaboxes
     */
    function enqueue_standard_styles(){
        // only enqueue once
        $name = 'field_styles';
        if(in_array($name, self::$enqueued)) return;

        add_action('admin_footer', [$this, 'output_standard_styles']);

        self::$enqueued[] = $name;
    }


    function output_standard_styles(){
        ?>
        <style>
            /* make input field not larger than side metabox */
            .dwe-field.regular-text {
                max-width:100%;
            }
            .dwe-field-label {
                display: block;
                padding-bottom: 4px;
                font-weight:bold;
            }


        </style>
        <?php
    }



}
