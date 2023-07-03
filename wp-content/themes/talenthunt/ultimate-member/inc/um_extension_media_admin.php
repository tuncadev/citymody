<?php
/*
  Plugin Name: Ultimate Member Extension - Media Upload
  Plugin URI:
  Description: Custom Ultimate Member Extension - Media Upload
  Version: 1.0
  Author: SH
  Author URI:
 */
global $wpdb;
define('UEME_VERSION', 10);
add_action('plugins_loaded', 'ueme_plugins_update');
function ueme_plugins_update() {
}
/* Uninstall and Activation handlers */
register_activation_hook(__FILE__, 'ueme_activate');
register_deactivation_hook(__FILE__, 'ueme_deactivate');
register_uninstall_hook(__FILE__, 'ueme_deactivate_uninstall');
function ueme_activate() {
     add_option('UEME_VERSION',UEME_VERSION);
}
function ueme_deactivate_uninstall() {
     delete_option('UEME_VERSION');
}
function ueme_deactivate() {
    delete_option('UEME_VERSION');
}
 if ( ! class_exists( 'UM' ) ) {
}
else
{
    add_filter( 'um_core_fields_hook', 'custom_media_upload', 10, 1 );
    add_filter( 'um_edit_field_profile_custommem', 'my_edit_field_html', 10, 2 );
    add_filter( 'um_view_field', 'my_view_field', 10, 3 );
    add_action( 'wp_enqueue_scripts', 'umem_enqueue_scripts');
    add_filter( 'ajax_query_attachments_args','umem_filter_media');
    add_action('wp_footer','ueme_media_style');
}
       function ueme_media_style()
       {
           ?>
                <style type="text/css">
                   #um-editing .um-profile-body .custommediawrapper table:not(.pods-meta):not(.picker__table):not(.page-50).custommediatable
                    {
                        display:block !important;
                    }
                    #umem_media_sepeartor
                    {
                        margin:5px 2px;
                    }
                </style>
           <?php
       }
        function custom_media_upload( $core_fields ) {
           $core_fields['custommem'] = array(
            'name' => 'Media Uploader',
            'col1' => array('_title','_metakey','_help','_visibility'),
            'col2' => array('_label','_public','_roles'),
            'col3' => array('_required'),
            'validate' => array(
                '_title' => array(
                    'mode' => 'required',
                    'error' => __('You must provide a title','ultimate-member')
                ),
                '_metakey' => array(
                    'mode' => 'unique',
                )
            )
        );
        return $core_fields;
        }
        function my_edit_field_html( $output, $data ) {
          
            $value="";
            @$value=(($data['metakey']!='')?UM()->user()->profile[$data['metakey']]:'');

            @$label=((isset($data['label']) && $data['label']!='')?$data['label']:'');
            
            $output='<div id="um_field_'.$data['metakey'].'" class="um-field"><div class="um-field-label"><label for="um_field_'.$data['metakey'].'">'.$label.'</label><div class="um-clear"></div></div><div class="um-field-area"><input autocomplete="off" class="'.$data['classes'].'" type="hidden" name="'.$data['metakey'].'" id="'.$data['metakey'].'" value="'.$value.'" data-key="'.$data['metakey'].'"><input type="button" id="media_uploader_btn_'.$data['metakey'].'" name="media_uploader_btn_'.$data['metakey'].'" class="customemediauploader" data-key="'.$data['metakey'].'" value="Add Media" /></div><div class="custommediawrapper" id="custommediawrapper_'.$data['metakey'].'"></div></div>';
            return $output;
		 }
             function my_view_field( $output, $data, $type ) {
                $noutput="";
                $returnoutput="";
                
                if($data['metakey']!='') {
                
                if($type=='custommem') {
                $noutput=UM()->user()->profile[$data['metakey']];
                $noutput=str_replace("~~~~","~~",$noutput);
                $noutput= explode("~~",$noutput);
                $returnoutput .='<style>.custommediawrapper-out {
                    display: grid;
                    grid-template-columns: repeat(3, 1fr);
                    grid-template-rows: 1fr;
                    grid-column-gap: 0px;
                    grid-row-gap: 0px;
                    }</style>';
                $returnoutput .="<div class='custommediawrapper-out1' id='custommediagallery-".$data['metakey']."'>";
                for($i=0;$i<count($noutput);$i++)
                {
                    if(trim($noutput[$i])!='')
                    {
                        $nd=explode("~",$noutput[$i]);

                       if(isset($nd) && count($nd)>1){
                        if($nd[0]=='image')
                        $returnoutput .="<div class='umem_media_sepeartor'><a href='".$nd[1]."' data-lightbox='test'><img src='".$nd[1]."' width='350px' height='350px' /></a></div>";
                        if($nd[0]=='audio')
                        $returnoutput .="<div class='umem_media_sepeartor'><audio controls><source src='".$nd[1]."' type='audio/mpeg'>Your browser does not support the audio tag.</audio></div>";
                        if($nd[0]=='video')
                        $returnoutput .="<div class='umem_media_sepeartor'><video width='auto' height='300px' controls><source src='".$nd[1]."' type='video/mp4'>Your browser does not support the video tag.</video></div>";

                        if($nd[0]=='iframe')
                        $returnoutput .='<div class="umem_media_sepeartor"><iframe width="300" height="300" src="' .$nd[1].'" frameborder="0"></iframe></div>';

                        if($nd[0]!='image' && $nd[0]!='audio' && $nd[0]!='video' && $nd[0]!='iframe')
                        $returnoutput .="<div class='umem_media_sepeartor'><a href='".$nd[1]."' target='_blank' class='um-button'>File</a></div>";
                       }
                    }
                }
                $returnoutput .="</div>";

                $returnoutput .="<script> lightbox.option({'resizeDuration': 50,'wrapAround': true})</script>";
             }
            }

                if($returnoutput!='')
                return  $returnoutput;
                else
                return $output;
             }
    
	function umem_enqueue_scripts() {
		wp_enqueue_media();
		
        wp_enqueue_script( 'models-um-media-js', get_theme_file_uri( '/ultimate-member/assets/js/upload-media-frontend.js' ), array('jquery'), '', true );
        

        if ( true == get_theme_mod( 'um_kaya_light_box', true ) ) :

         wp_enqueue_script( 'simple-lightbox-js', get_theme_file_uri( '/ultimate-member/assets/js/lightbox.min.js' ), array('jquery'), '', true );
       wp_enqueue_style( 'simple-lightbox-css', get_template_directory_uri() . '/ultimate-member/assets/css/lightbox.min.css',false,'1.3','all');
         else : endif; 
       
	}
	
	function umem_filter_media( $query ) {
	
		if ( ! current_user_can( 'manage_options' ) )
			$query['author'] = get_current_user_id();
		return $query;
	}
    ?>