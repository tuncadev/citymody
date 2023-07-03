<?php


if( !function_exists('kaya_get_pods_user_fields') ){
	function kaya_get_pods_user_fields($user_slug='user'){
		if( !function_exists('pods_api') ){
			return false;
		}

	 	$pod_slug = pods_v( 'last', 'url' );
	    $pod_options_data = pods( $user_slug, $pod_slug );
	    if( !empty($pod_options_data) ){	    	
	      return $pod_options_data->api->fields;
	    }
	}
}

/**
 * It displayed 'files' meta data  as tab section, like: images, video and richtextarea...
 * @param ( string ) $user_slug
 */
function kaya_user_tab_section($user_slug){
	$pods_user_meta_fields = kaya_get_pods_user_fields($user_slug);
	$pod = pods( $user_slug, get_the_id() );
	if( !empty($pods_user_meta_fields) ){
			echo '<ul class="tabs_content_wrapper">';
				foreach ($pods_user_meta_fields as $key => $meta_fields) {
					if(($meta_fields['type'] == 'file') || ($meta_fields['type'] == 'wysiwyg') || ($meta_fields['type'] == 'video') ){
						$fields_data = get_post_meta(get_the_ID(), $meta_fields['name'], false);
						if(!empty($fields_data) && !empty($fields_data[0])){
		          			echo '<li><a href="#'.$meta_fields['name'].'">'.$meta_fields['label'].'</a></li>';
						}
					}
				}
			echo '</ul>';
	}
}

/* when Tab data is empty hide tabs
 */
add_filter('um_profile_tabs', 'kaya_um_pods_profile_tabs', 1000 );
function kaya_um_pods_profile_tabs( $tabs ) {
	if( !function_exists('pods_api') ){
		return false;
	}

	$pods_user_meta_fields = kaya_get_pods_user_fields('user');

	if( !empty($pods_user_meta_fields) ){
		$pod = pods( 'user', um_user('ID'));
		if( !empty($tabs) && empty($tab_content) ){
			foreach ($pods_user_meta_fields as $key => $meta_fields) {


							$tabs[$meta_fields['name']] = array(
							'name' => $meta_fields['label'],
							'icon' => !empty($meta_fields['options']['class']) ? $meta_fields['options']['class'] : 'um-icon-android-image',
							//'default_privacy'   => false,

							);
							
				if(($meta_fields['type'] == 'file') || ($meta_fields['type'] == 'wysiwyg') || ($meta_fields['type'] == 'pick')){
					$tab_content = $pod->display($meta_fields['name']);

					if( empty($tab_content) ){


						$user_id = um_profile_id();

						if ( ! user_can( $user_id, empty($tab_content) ) ) {
							unset ($tabs[$meta_fields['name']]);
						}

							
					}

				}
			
			}
		}
	}
	return $tabs;
}
// End
 
add_action( 'template_redirect', 'kaya_um_profile_tab_content', 20 );
function kaya_um_profile_tab_content() {

	$default_tab = um_get_option('profile_menu_default_tab'); 

	$tab = isset($_REQUEST['profiletab']) ? $_REQUEST['profiletab'] : $default_tab;
	
	if ( ($tab == "main") || ($tab == "comments") || ( $tab == "posts" ) ) {
		return false;
	}

	add_action( "um_profile_content_{$tab}_default", function () use ($tab) {

		//$kaya_user_settings = get_option('kaya_user_settings');
		$pod = pods( 'user', um_user('ID'));
		$tab_content = $pod->display($tab);			
		if( !empty($tab_content) ){
			$tab_data = $pod->api->pod_data['fields'][$tab];
			if( $tab_data['options']['file_type'] == 'audio' ){ // Audio Data
				$videos = explode(' ', $tab_content);
				foreach ($videos as $key => $src) {
					echo wp_audio_shortcode( array( 'src' => $src ) );
				}
			}elseif( $tab_data['options']['file_type'] == 'video' ){ // Video Data
				$videos = explode(' ', $tab_content);
				foreach ($videos as $key => $src) {
					echo wp_video_shortcode( array( 'src' => $src ) );
				}
			}else{
				echo do_shortcode($tab_content); // Images
			}
		}
	});
}
?>