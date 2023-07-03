<?php 
	 function prefix_add_footer_styles() {
	   wp_enqueue_style( 'um-lavan', get_template_directory_uri() . '/ultimate-member/assets/css/um-kaya-talenthunt.css',false,'1.1','all');
		wp_enqueue_script( 'lavan-um-js', get_theme_file_uri( '/ultimate-member/assets/js/kaya-um-custom.js' ), array('jquery'), '', true );
	};
	add_action( 'get_footer', 'prefix_add_footer_styles' );



/* Dynamic Profle tabs for user page only */
	if( function_exists( 'pods' ) ) {
		include_once get_template_directory() . '/ultimate-member/inc/custom-dynamic-profile-tabs-functions.php';
	}
				/* UM Contact options if CF7 plugin activated */
				if( function_exists( 'WPCF7' ) ) {
					include_once get_template_directory() . '/ultimate-member/inc/cf7-um-user-email.php';
				}
				/* compcard styles if kiriki plugin activated */
				if( function_exists( 'Kirki' ) ) {
					/* Load um Customizer  */
					 include_once get_template_directory() . '/ultimate-member/inc/um-customizer-kirki.php';


					if ( Kirki::get_option( 'theme_config_id', 'compcard_styles' ) == 'compcard_style-1' ) { 

						 	function um_kaya_compcard(){
					    wp_enqueue_style(
					        'drweb-print-style', 
					        get_stylesheet_directory_uri() . '/ultimate-member/assets/css/um-kaya-compcard.css', 
					        array(), 
					        '20130821', 
					        'print' // print styles only
					    );
					}
					add_action( 'wp_enqueue_scripts', 'um_kaya_compcard' );
					//endif;

					}
						if ( Kirki::get_option( 'theme_config_id', 'compcard_styles' ) == 'compcard_style-2' ) { 
						
					function um_kaya_compcard(){
					    wp_enqueue_style(
					        'drweb-print-style', 
					        get_stylesheet_directory_uri() . '/ultimate-member/assets/css/um-kaya-compcard2.css', 
					        array(), 
					        '20130821', 
					        'print' // print styles only
					    );
					}
					add_action( 'wp_enqueue_scripts', 'um_kaya_compcard' );
					//endif;
					}
				}
 /**
 * Returns a user meta value
 * Usage [um_user meta_key="" show_label="true" ] meta_key ex: gender, first_name, etc leave user_id empty if you want to retrive the current user's meta value.
 * meta_key is the field name that you've set in the UM form builder
 * You can modify the return meta_value with filter hook 'um_user_shortcode_filter__{$meta_key}'
 */

function um_user_shortcode( $atts ) {

	$atts = extract( shortcode_atts( array(
		'user_id' => 0,
		'meta_key' => '',
                'show_label' => false,
	), $atts ) );

	
	if ( empty( $meta_key ) ) return;
	
	if( empty( $user_id ) ) $user_id = um_profile_id();
    
    $meta_value = get_user_meta( $user_id, $meta_key, true );

    if( is_serialized( $meta_value ) ){
       $meta_value = unserialize( $meta_value );
    } 

    if( is_array( $meta_value ) ){
         $meta_value = implode(",",$meta_value );
    }  
    
    if( $show_label && ! empty( $meta_value )  ) {   
        $label = UM()->fields()->get_label( $meta_key );
        $meta_value = "<span class='kaya_um_user_meta'> <strong> {$label}: </strong> {$meta_value} </span>";
    }

    return apply_filters("um_user_shortcode_filter__{$meta_key}", $meta_value );

}
add_shortcode( 'um_user', 'um_user_shortcode' );


/* Custom Label for Search Role */

add_filter("um_search_fields","um_custom_search_field_role_select", 10, 2 );
function um_custom_search_field_role_select( $attrs, $field_key ){

    if( 'role_select' == $field_key ){
        $attrs['label'] = __('Talent Type','ultimate-member');
    }

    return $attrs;
}

/* PODs  Import data */
if( !class_exists('Kaya_Pods_Configuration') ){
	class Kaya_Pods_Configuration
	{
		function __construct(){
			add_action('init', array($this, 'kaya_pods_data_configuration'));
		}
	
		public function kaya_pods_data_configuration(){
		 	if ( defined( 'PODS_VERSION' ) ) {
		 		$pods_data = pods_api()->load_pods( array( 'names' => true ) );
			   	if( empty($pods_data) ){
				    $pods = get_parent_theme_file_path( '/inc/kaya-xml-files/pods_data.json');
				    $encode_pods_options = file_get_contents($pods);
				    $pod_options = json_decode($encode_pods_options, true);
				    $pods = pods_api()->import_package($pod_options, true);
				}
			}
		}
	}
}
new Kaya_Pods_Configuration(); 

// Limit media library access
  
add_filter( 'ajax_query_attachments_args', 'wpb_show_current_user_attachments' );
 
function wpb_show_current_user_attachments( $query ) {
    $user_id = get_current_user_id();
    if ( $user_id && !current_user_can('activate_plugins') && !current_user_can('edit_others_posts
') ) {
        $query['author'] = $user_id;
    }
    return $query;
} 
?>