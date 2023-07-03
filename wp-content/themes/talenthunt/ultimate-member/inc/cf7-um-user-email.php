<?php

/**
 * Profile Contact Form 7 - Code
 *
 * This snippet changes the recipient email when contact form 7 is submitted from the User profile.
 */
/*Add Hidden Fields to Capture Profile User ID data*/
add_filter( 'wpcf7_form_hidden_fields',  'talents_profile_cf7_hook' );
function talents_profile_cf7_hook( $fields ){
     if ( um_is_core_page( 'user' ) ) {
		$fields['_wpcf7_ultimate_member_profile_id'] = um_profile_id();
      }
     return $fields;
}

add_filter('wpcf7_before_send_mail','talents__wpcf7_mail_sent_function', 4, 1);
function talents__wpcf7_mail_sent_function($instance){
    
	 $properites = $instance->get_properties();
	if(isset($_REQUEST['_wpcf7_ultimate_member_profile_id'])){
	$user = get_user_by( 'ID', absint($_REQUEST['_wpcf7_ultimate_member_profile_id']) );
	
	if ( ! is_wp_error( $user ) && isset( $user->user_email ) && is_email( $user->user_email ) ) {
						$properites['mail']['recipient'] =$user->user_email;
			}
	
	}
    
     $instance->set_properties($properites);
    return ($instance);
}