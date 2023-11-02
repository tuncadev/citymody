<?php
namespace mlcf7pll\admin;



new Editor_Panels();
class Editor_Panels
{


    function __construct()
    {

        add_filter( 'wpcf7_editor_panels', [$this, 'add_wpcf7_editor_panels'] );
    }


    function add_wpcf7_editor_panels($panels){

        $panels['polylang-integration'] = array(
            'title' => __('Polylang integration', ''),
            'callback' => [$this, 'render_polylang_panel']
        );
        return $panels;
    }


    function render_polylang_panel($post){

        $description = 'test 123';

        ?>
        <h2><?php echo esc_html( __( 'Polylang integration', '' ) ); ?></h2>
        <fieldset>
            <legend><?php echo $description; ?></legend>
            <textarea id="wpcf7-additional-settings" name="wpcf7-additional-settings" cols="100" rows="8" class="large-text" data-config-field="additional_settings.body"><?php echo esc_textarea( $post->prop( 'additional_settings' ) ); ?></textarea>
        </fieldset>
        <?php

    }



}




