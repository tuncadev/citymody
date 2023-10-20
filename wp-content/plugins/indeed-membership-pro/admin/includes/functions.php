<?php
function ihc_return_all_cpt( $excluded=array() ){
	//return all custom post type except the built in and the $excluded
	$args = array('public' => true, '_builtin' => false);
	$data = get_post_types($args);
	if(count($excluded)>0){
		foreach($excluded as $e){
			if(in_array($e, $data)){
				 $data = array_diff($data, array($e) );
			}
		}
	}
	return $data;
}

function ihc_meta_box_settings_html(){
	require_once IHC_PATH . 'admin/includes/meta_boxes/page_post_settings.php';
	do_action( 'ihc_meta_box_post_settings_html' );
	// @description run on add/edit post, print some extra html on settings box. @param none
}

function ihc_meta_box_replace_content_html(){
	require_once IHC_PATH . 'admin/includes/meta_boxes/replace_content.php';
}

function ihc_meta_box_default_pages_html(){
	require_once IHC_PATH . 'admin/includes/meta_boxes/default_pages.php';
}

/**
 * @param none
 * @return none
 */
function ihc_drip_content_return_meta_box()
{

	require_once IHC_PATH . 'admin/includes/meta_boxes/drip_content.php';
}

function ihc_update_metas()
{
	if(!isset($_REQUEST['ihc_submit'])){
		 return;
	}
	$metas = ihc_get_metas();
	foreach($metas as $k=>$v){
		if(isset($_REQUEST[$k])){
			$data = get_option($k);
			if ( $k !== FALSE ){
                            // update
                            if ( is_array( $_REQUEST[$k] ) ){
                                update_option($k, indeed_sanitize_array( $_REQUEST[$k] ) );
                            } else {
                                update_option($k, sanitize_text_field( $_REQUEST[$k] ) );
                            }
			} else {
                            // create
                            if ( is_array( $_REQUEST[$k] ) ){
                                add_option($k, indeed_sanitize_array( $_REQUEST[$k] ) );
                            } else {
                                add_option($k, sanitize_text_field( $_REQUEST[$k] ) );
                            }
			}
		}
	}
}

function ihc_delete_template()
{
    $i_delete_id = isset( $_REQUEST['i_delete_id'] ) ? sanitize_text_field( $_REQUEST['i_delete_id'] ) : false;
    if ( $i_delete_id ){
        $option_name = 'ihc_lockers';
	$data = get_option($option_name);
	if ( $data === false || !isset( $data[$i_delete_id] ) ){
            return;
	}
	unset( $data[$i_delete_id] );
	update_option( $option_name, $data );
    }
}

function ihc_save_update_trimmed_metas($payment_service)
{
    if(isset($_REQUEST['ihc_save'])) {
	$data = ihc_return_meta_arr($payment_service, true);
	foreach($data as $k=>$v) {
            if ( isset( $_REQUEST[$k] ) ){
		$data_db = get_option($k);
		if($data_db!==FALSE){
                    if ( is_array( $_REQUEST[$k] ) ){
                        update_option($k, indeed_sanitize_array( $_REQUEST[$k] ) );
                    } else {
                        update_option($k, sanitize_text_field( $_REQUEST[$k] ) );
                    }
		} else {
                    if ( is_array( $_REQUEST[$k] ) ){
                        add_option($k, indeed_sanitize_array( $_REQUEST[$k] ) );
                    } else {
                        add_option($k, sanitize_text_field( $_REQUEST[$k] ) );
                    }
                }
            }
	}
    }
}

function ihc_save_update_metas($group)
{
	if (isset($_REQUEST['ihc_save'])){
		$data = ihc_return_meta_arr($group, true);
		foreach ($data as $k=>$v){
			if (isset($_REQUEST[$k])){
				$data_db = get_option($k);
				if($data_db!==FALSE){
                                    if ( is_array( $_REQUEST[$k] ) ){
                                        update_option($k, indeed_sanitize_array( $_REQUEST[$k] ) );
                                    } else {
                                        update_option($k, wp_kses_post( $_REQUEST[$k] ) );
                                    }
				} else {
                                    if ( is_array( $_REQUEST[$k] ) ){
                                         add_option($k, indeed_sanitize_array( $_REQUEST[$k] ) );
                                    } else {
                                         add_option($k, wp_kses_post( $_REQUEST[$k] ) );
                                    }
                                }
			}
		}
	}
}

/**
 * @param array ($_POST)
 * @return none
 */
function ihc_save_update_metas_general_defaults($post_data=array())
{
	$data = ihc_return_meta_arr('general-defaults', true);

	//EXTRA CHECK - REWRITE RULE FOR Visitor Inside User Page
	if (isset($post_data['ihc_general_register_view_user'])){
		ihc_save_rewrite_rule_for_register_view_page( sanitize_text_field( $post_data['ihc_general_register_view_user'] ) );
	}

	foreach ($data as $k=>$v){
		if (isset($post_data[$k])){
                        if ( is_array( $post_data[$k] ) ){
                            update_option( $k, indeed_sanitize_array( $post_data[$k] ) );
                        } else {
                            update_option( $k, sanitize_text_field( $post_data[$k] ) );
                        }
		}
	}
}

function ihc_check_default_pages_set($meta_box=false){
	$arr = array(
					'ihc_general_redirect_default_page' 			=>  esc_html__('Default Redirect', 'ihc'),
					'ihc_general_login_default_page' 					=>  esc_html__('Login', 'ihc'),
					'ihc_general_register_default_page' 			=>  esc_html__('Register', 'ihc'),
					'ihc_general_lost_pass_page' 							=>  esc_html__('Lost Password', 'ihc'),
					'ihc_general_logout_page' 								=>  esc_html__('LogOut', 'ihc'),
					'ihc_general_user_page' 									=>  esc_html__('Account User', 'ihc'),
					'ihc_general_tos_page' 										=>  esc_html__('TOS', 'ihc'),
					'ihc_subscription_plan_page' 							=>  esc_html__('Subscription Plan', 'ihc'),
					'ihc_checkout_page' 											=>  esc_html__('Checkout', 'ihc'),
					'ihc_thank_you_page' 											=>  esc_html__('Thank You', 'ihc'),
					'ihc_general_register_view_user' 					=>  esc_html__('Visitor Inside User', 'ihc')
				);
	$str = '';

		if($meta_box){
			foreach($arr as $name=>$label){
				$value = get_option($name);

				//if page does not exists
				if($value!=-1 && (!get_post_status($value) || get_post_status($value)=='trash') ){
					$value = -1;
				}

				if($value==FALSE || $value==-1){
					$str .= '<div class="ihc-not-set">' .  esc_html__('Default', 'ihc') . ' '.$label.' ' .  esc_html__('Page', 'ihc') . ' <strong>' .  esc_html__('is missing!', 'ihc') . '</strong></div>';
				}
			}
			//return string for metabox
		}else{
			foreach($arr as $name=>$label){
				$value = get_option($name);

				//if page does not exists
				if($value!=-1 && (!get_post_status($value) || get_post_status($value)=='trash') ){
					$value = -1;
				}

				if($value==FALSE || $value==-1){
					if($str!=''){
						 $str .= '<span class="iump-separator"> | </span>';
					}
					$str .= $label.' ' .  esc_html__('Page', 'ihc');
				}
			}
			//for general settings
			if($str){
				$str = '<div class="ihc-not-set"><strong>' .  esc_html__('Current Ultimate Membership Pro Default Pages are missing:', 'ihc') . ' </strong>' . $str . '.</div>';
			}
		}
	return $str;
}

function ihc_is_curl_enable(){
	/*
	 * @param none
	 * @return string
	 */
	if (!function_exists('curl_version')){
		return '<div class="ihc-not-set"><strong>' .  esc_html__('Curl is disabled. Contact your hosting provider for more details', 'ihc') . ' </strong></div>';
	}
	return '';
}

function ihc_meta_box_page_type_message(){
	global $post;
	$str = '';
	if(get_post_type($post->ID)=='page'){
		//CHECK IF CURRENT PAGE IF REGISTER OR LOST PASSWORD
		$register_page = get_option('ihc_general_register_default_page');
		$lost_pass = get_option('ihc_general_lost_pass_page');
		$login_page = get_option('ihc_general_login_default_page');
		$redirect = get_option('ihc_general_redirect_default_page');
		$logout = get_option('ihc_general_logout_page');
		$user_page = get_option('ihc_general_user_page');
		$tos = get_option('ihc_general_tos_page');
		$subscription_plan = get_option('ihc_subscription_plan_page');
		$checkout_page = get_option('ihc_checkout_page');
		$thank_you_page = get_option('ihc_thank_you_page');
		$view_user_page = get_option('ihc_general_register_view_user');

		switch ($post->ID){
			case $register_page:
				$str .= esc_html_e('Register Page', 'ihc');
			break;
			case $lost_pass:
				$str .= esc_html_e('Lost Password Page', 'ihc');
			break;
			case $login_page:
				$str .= esc_html_e('Login Page', 'ihc');
			break;
			case $redirect:
				$str .=  esc_html__('Redirect Page', 'ihc') . '<div class="ihc-meta-box-err-msg">' .  esc_html__('You can only Replace the content.', 'ihc') . '</div>';
			break;
			case $logout:
				$str .=  esc_html__('Logout Page', 'ihc');
			break;
			case $user_page:
				$str .=  esc_html__('User Page', 'ihc');
			break;
			case $tos:
				$str .=  esc_html__('TOS Page', 'ihc');
			break;
			case $subscription_plan:
				$str .=  esc_html__('Subscription Plan Page', 'ihc');
			break;
			case $subscription_plan:
				$str .=  esc_html__('Subscription Plan Page', 'ihc');
			break;
			case $checkout_page:
					$print = esc_html__('Checkout Page', 'ihc');
			break;
			case $thank_you_page:
					$print = esc_html__('Thank you Page', 'ihc');
			break;
			case $view_user_page:
				$str .=  esc_html__('Visitor Inside User Page', 'ihc');
			break;
			default:
				return '';
			break;
		}
		if($str){
			$str = '<div class="ihc-meta-box-message"><span>' .  esc_html__('This Page is set as:', 'ihc') . ' </span>'.$str.'</div>';
		}
	}
	return $str;
}

function ihc_get_default_pages_il($return_set=false){
	$unset_arr = FALSE;
	$set_arr = FALSE;
	$arr_labels = array( 'ihc_general_register_default_page' =>  esc_html__('Register', 'ihc'),
                             'ihc_general_lost_pass_page'        =>  esc_html__('Lost Password', 'ihc'),
			     'ihc_general_login_default_page'    =>  esc_html__('Login', 'ihc'),
			     'ihc_general_redirect_default_page' =>  esc_html__('Redirect', 'ihc'),
                             'ihc_general_logout_page'           =>  esc_html__('LogOut', 'ihc'),
                             'ihc_general_user_page'             =>  esc_html__('Account User', 'ihc'),
                             'ihc_general_tos_page'              =>  esc_html__('TOS', 'ihc'),
                             'ihc_subscription_plan_page'        =>  esc_html__('Subscription', 'ihc'),
                             'ihc_checkout_page'                 =>  esc_html__('Checkout', 'ihc'),
                             'ihc_thank_you_page'                =>  esc_html__('Thank You', 'ihc'),
                             'ihc_general_register_view_user'    =>  esc_html__('Visitor Inside User', 'ihc')
	);
	foreach($arr_labels as $name=>$label){
		$data = get_option($name);
		$arr_ids[$name] = -1;

		if ($data){
			$arr_ids[$name] = $data;
			/////testing if page really exists
			if($arr_ids[$name]!=-1 && (!get_post_status($arr_ids[$name]) || get_post_status($arr_ids[$name])=='trash') ){
				$arr_ids[$name] = -1;
			}
		}
		if ($arr_ids[$name]==-1){
			$unset_arr[$name] = $label;
		} else {
			$set_arr[$name] = $data;
		}
	}
	if($return_set){
			return $set_arr;
	}
	return $unset_arr;
}

/**
 * DELETE USERS
 * @param int, array
 * @return none
 */
function ihc_delete_users($single_id=0, $ids=array()){

	if (!empty($single_id)){
            $ids[] = sanitize_text_field($single_id);
	}
	if ($ids){
            foreach ($ids as $id){
                $id = sanitize_text_field( $id );
                do_action('ihc_delete_user_action', $id);
                //delete
                wp_delete_user($id);
                \Indeed\Ihc\UserSubscriptions::deleteAllForUser( $id );
            }
	}
}

function ihc_get_user_custom_fields(){
	$data = get_option('ihc_user_fields');
	if($data!==FALSE){
		$not_native = array();
		foreach($data as $key=>$value){
			if($value['native_wp']==0){
				$not_native[] = array('name' => $value['name'], 'type' => $value['type'], 'label' => $value['label']);
			}
		}
		return $not_native;
	}
	return FALSE;
}

function ihc_delete_payment_entry($id)
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'indeed_members_payments';
	$q = $wpdb->prepare("DELETE FROM $table_name WHERE id=%d", $id);
	$wpdb->query($q);
}

function ihc_save_block_urls()
{
	if (isset($_REQUEST['ihc_save_block_url'])){
		foreach (array('ihc_block_url_entire', 'ihc_block_url_word') as $val){
			if (isset($_REQUEST[$val.'-url']) && $_REQUEST[$val.'-url']){
				$data = get_option($val);
				if ($data){
					$key = ihc_array_value_exists($data, sanitize_text_field( $_REQUEST[$val.'-url'] ), 'url');

					if ($key===FALSE){
						$arrayKeys = array_keys($data);
						$key = end( $arrayKeys ) + 1;
					}
				} else {
					$key = 1;
				}

				$data[$key] = array(
						'url'           => sanitize_text_field($_REQUEST[$val.'-url']),
						'redirect'      => sanitize_text_field($_REQUEST[$val.'-redirect']),
						'target_users'  => sanitize_text_field($_REQUEST[$val.'-target_users']),
				);
				if (isset($_REQUEST['block_or_show'])){
					$data[$key]['block_or_show'] = sanitize_text_field($_REQUEST['block_or_show']);
				}
				update_option($val, $data);
			}
		}
	}
}

function ihc_delete_block_urls()
{
        $delete_block_url = isset($_REQUEST['delete_block_url']) ? sanitize_text_field($_REQUEST['delete_block_url']) : false;
        $delete_block_regex = isset($_REQUEST['delete_block_regex']) ? sanitize_text_field($_REQUEST['delete_block_regex']) : false;
	if ( $delete_block_url ){
            $data = get_option('ihc_block_url_entire');
            if (isset($data[$delete_block_url])){
                unset($data[$delete_block_url]);
            }
            update_option('ihc_block_url_entire', $data);
	}
	if ( $delete_block_regex ){
            $data = get_option('ihc_block_url_word');
            if (isset($data[$delete_block_regex])){
                unset($data[$delete_block_regex]);
            }
            update_option('ihc_block_url_word', $data);
	}
}

/**
 * STATISTIC FUNCTIONS (for dashboard)
 * @param int : 1 = total, 2 = pending users, 3 = approved users
 * @return counts of users
 * all (without admin)
 * pending users
 * approved users (without admin)
 */
function ihc_get_users_counts($type = 1)
{
	global $wpdb;
	$meta_key = $wpdb->get_blog_prefix() . 'capabilities';
	$table = $wpdb->base_prefix . 'usermeta';
	if ($type==1){
		//all
		$query = $wpdb->prepare( "SELECT COUNT(DISTINCT(user_id)) as c FROM {$wpdb->base_prefix}usermeta WHERE meta_key=%s AND meta_value NOT LIKE '%administrator%';", $meta_key );
		$data = $wpdb->get_row( $query );
		if ($data && isset($data->c)){
			return $data->c;
		}
	} else if($type==2){
		//pending users
		$query = $wpdb->prepare( "SELECT COUNT(DISTINCT(user_id)) as c FROM {$wpdb->base_prefix}usermeta WHERE meta_key=%s AND meta_value LIKE '%pending_user%';", $meta_key );
		$data = $wpdb->get_row( $query );
		if ($data && isset($data->c)){
			return $data->c;
		}
	} else {
		//approved users
		$query = $wpdb->prepare( "SELECT COUNT(DISTINCT(user_id)) as c FROM {$wpdb->base_prefix}usermeta WHERE meta_key=%s AND meta_value NOT LIKE '%administrator%' AND meta_value NOT LIKE '%pending_user%';", $meta_key );
		$data = $wpdb->get_row( $query );
		if ($data && isset($data->c)){
			return $data->c;
		}
	}
	return 0;
}

function ihc_get_last_five_users()
{
	global $wpdb;
	$users = FALSE;
	$users_obj = new WP_User_Query(array(
									    'meta_query' => array(
													        array(
													            'key' => $wpdb->get_blog_prefix() . 'capabilities',
													            'value' => 'administrator',
													            'compare' => 'NOT LIKE'
													        )
													    ),
										'orderby' => 'user_registered',
										'order' => 'DESC',
										'number' => 5,
				));
	if (isset($users_obj->results) && count($users_obj->results)){
		 $users = $users_obj->results;
	}
	return $users;
}

function ihc_get_levels_top_by_transactions()
{
	global $wpdb;
	$levels_arr = array();
	$arr = array();
	$levels_data = \Indeed\Ihc\Db\Memberships::getAll();
	if ($levels_data && count($levels_data)){
		$levels_arr = array();
		foreach ($levels_data as $k=>$v){
			$levels_arr[$k] = 0;
		}
		//No query parameters required, Safe query. prepare() method without parameters can not be called
		$query = "SELECT COUNT(lid) as c, lid FROM {$wpdb->prefix}ihc_orders WHERE status='Completed' GROUP BY lid;";
		$data = $wpdb->get_results( $query );
		if ($data){
			foreach ($data as $object){
				if (isset($levels_data[$object->lid]['name'])){ /// does level exists
					$arr[$levels_data[$object->lid]['label']] = $object->c;
				}
			}
		}
	}
	return $arr;
}

function ihc_generate_color()
{
    mt_srand((double)microtime()*1000000);
    $color_code = '';
    while(strlen($color_code)<6){
        $color_code .= sprintf("%02X", mt_rand(0, 255));
    }
	return '#'.$color_code;
}

function ihc_get_notification_metas($id=FALSE)
{
	global $wpdb;
	if ($id){
		$id = sanitize_text_field($id);
		$query = $wpdb->prepare( "SELECT id,notification_type,level_id,subject,message,pushover_message,pushover_status,status
																	FROM `{$wpdb->prefix}ihc_notifications` WHERE id=%d;", $id );
		return (array)$wpdb->get_row( $query );
	} else {
		return array('notification_type'=>'', 'level_id'=>-1, 'subject'=>'', 'message'=>'',);
	}
}

function ihc_general_options_print_page_links($id=FALSE)
{
        $id = sanitize_text_field( $id );
	if ($id!=-1 && $id!==FALSE && get_post_status( $id ) !== 'trash' ){
		$target_page_link = get_permalink($id);
		if ($target_page_link) {
			echo '<div class="ihc-general-options-link-pages">' .  esc_html__('Link:', 'ihc') . ' <a href="' . $target_page_link . '" target="_blank">' . $target_page_link . '</a></div>';
		}
	}
	return '';
}

/**
 * @param string
 * @return array
 */
function ihc_check_payment_status($p_type='')
{
	$return = array();
	$return['active'] = '';
	$return['status'] = 0;
	$return['settings'] = 'Uncompleted';
	switch($p_type){
		case 'paypal':
					  $arr = ihc_return_meta_arr('payment_paypal');
					  if ($arr['ihc_paypal_status'] == 1){
							$return['active'] = 'paypal-active'; $return['status'] = 1;
						}
					  if ($arr['ihc_paypal_email'] != ''){
							 $return['settings'] = 'Completed';
						}
					  break;
		case 'stripe_checkout_v2':
					  $arr = ihc_return_meta_arr('payment_stripe_checkout_v2');
					  if ($arr['ihc_stripe_checkout_v2_status'] == 1){
							$return['active'] = 'stripe-checkout-v2-active'; $return['status'] = 1;
						}
					  if ($arr['ihc_stripe_checkout_v2_secret_key'] != '' && $arr['ihc_stripe_checkout_v2_publishable_key'] != ''){
							 $return['settings'] = 'Completed';
						}
			  break;
		case 'authorize':
					  $arr = ihc_return_meta_arr('payment_authorize');
					  if ($arr['ihc_authorize_status'] == 1){
							$return['active'] = 'authorize-active'; $return['status'] = 1;
						}
					  if ($arr['ihc_authorize_login_id'] != '' && $arr['ihc_authorize_transaction_key'] != ''){
							 $return['settings'] = 'Completed';
						}
					  break;
		case 'twocheckout':
			$arr = ihc_return_meta_arr('payment_twocheckout');
			if ($arr['ihc_twocheckout_status'] == 1) {
				$return['active'] = 'twocheckout-active'; $return['status'] = 1;
			}
			if ($arr['ihc_twocheckout_api_user'] != '' && $arr['ihc_twocheckout_api_pass'] != ''
				&& $arr['ihc_twocheckout_private_key'] != '' && $arr['ihc_twocheckout_account_number'] != ''
				&& $arr['ihc_twocheckout_secret_word'] != '' ) $return['settings'] = 'Completed';
			break;
		case 'bank_transfer':
			$arr = ihc_return_meta_arr('payment_bank_transfer');
			if ($arr['ihc_bank_transfer_status'] == 1) {
				$return['active'] = 'bank_transfer-active'; $return['status'] = 1;
			}
			if (isset($arr['ihc_bank_transfer_message'])){
				$return['settings'] = 'Completed';
			}
			break;
		case 'braintree':
			$arr = ihc_return_meta_arr('payment_braintree');
			if ($arr['ihc_braintree_status'] == 1) {
				$return['active'] = 'braintree-active'; $return['status'] = 1;
			}
			if (!empty($arr['ihc_braintree_merchant_id']) && !empty($arr['ihc_braintree_public_key']) && !empty($arr['ihc_braintree_private_key'])){
				$return['settings'] = 'Completed';
			}
			break;
		case 'mollie':
				$arr = ihc_return_meta_arr('payment_mollie');
				if ($arr['ihc_mollie_status'] == 1) {
					$return['active'] = 'mollie-active'; $return['status'] = 1;
				}
				if (!empty($arr['ihc_mollie_api_key'])){
					$return['settings'] = 'Completed';
				}
			break;
		case 'paypal_express_checkout':
			$arr = ihc_return_meta_arr('payment_paypal_express_checkout');
			if ($arr['ihc_paypal_express_checkout_status'] == 1) {
				$return['active'] = 'paypal_express_checkout-active'; $return['status'] = 1;
			}

			if (!empty($arr['ihc_paypal_express_checkout_signature']) && !empty($arr['ihc_paypal_express_checkout_user'])
			&& !empty($arr['ihc_paypal_express_checkout_password']) ){
				$return['settings'] = 'Completed';
			}
			break;
		case 'pagseguro':
			$arr = ihc_return_meta_arr('payment_pagseguro');
			if ($arr['ihc_pagseguro_status'] == 1) {
					$return['active'] = 'pagseguro-active'; $return['status'] = 1;
			}
			if ( !empty($arr['ihc_pagseguro_email']) && !empty($arr['ihc_pagseguro_token']) ){
					$return['settings'] = 'Completed';
			}
			break;
		case 'stripe_connect':
		  $arr = ihc_return_meta_arr('payment_stripe_connect');
		  if ($arr['ihc_stripe_connect_status'] == 1){
					$return['active'] = 'stripe-connect-active';
					$return['status'] = 1;
			}
		  if (($arr['ihc_stripe_connect_publishable_key'] != '' && $arr['ihc_stripe_connect_client_secret'] != '' && $arr['ihc_stripe_connect_account_id'] != '') ||
					($arr['ihc_stripe_connect_test_publishable_key'] != '' && $arr['ihc_stripe_connect_test_client_secret'] != '' && $arr['ihc_stripe_connect_test_account_id'] != '') ){
				 $return['settings'] = 'Completed';
			}
		  break;
	}
	$return = apply_filters( 'ihc_payment_gateway_box_status', $return, $p_type );
	// @description

	return $return;
}

/**
 * @param id = int, settings = array, url = string
 * @return string
 */
function ihc_generate_coupon_box($id=0, $settings=array(), $url='')
{
	$div_id = "ihc_coupon_box_" . sanitize_text_field($id);
	?>
	<div class="ihc-coupon-admin-box-wrap" id="<?php echo esc_attr($div_id);?>">
		<div class="ihc-coupon-box-wrap ihc-box-background-<?php echo substr($settings['settings']['box_color'],1);?>">
			<div class="ihc-coupon-box-main">
				<div class="ihc-coupon-box-title"><?php echo esc_html($settings['code']);?></div>
				<div class="ihc-coupon-box-content">
					<div class="ihc-coupon-box-levels"><?php
						esc_html_e("Target Memberships: ", "ihc");
						echo '<span>';
						if ($settings['settings']['target_level']==-1){
							esc_html_e("All", "ihc");
						} else if ( strpos( $settings['settings']['target_level'], ',') !== false ){
							$settings['settings']['target_level'] = explode( ',', $settings['settings']['target_level'] );
							foreach ( $settings['settings']['target_level'] as $lid ){
								$membershipLabels[] = \Indeed\Ihc\Db\Memberships::getMembershipLabel( $lid );
							}
							echo implode( ',', $membershipLabels );
						} else {
								echo \Indeed\Ihc\Db\Memberships::getMembershipLabel( $settings['settings']['target_level'] );
						}
						echo '</span>';
					?></div>


				</div>
				<div class="ihc-coupon-box-links-wrap">
					<div class="ihc-coupon-box-links">
						<a href="<?php echo esc_url($url . '&id=' . $id);?>" class="ihc-coupon-box-link"><?php esc_html_e('Edit', 'ihc');?></a>
						<div class="ihc-coupon-box-link" onClick="ihcDeleteCoupon(<?php echo esc_attr($id);?>, '#<?php echo esc_attr($div_id);?>');"><?php esc_html_e('Delete', 'ihc');?></div>
					</div>
				</div>
			</div>
			<div class="ihc-coupon-box-bottom">
				<div class="ihc-coupon-box-bottom-disccount"><?php
						echo esc_html($settings['settings']['discount_value']);
						if ($settings['settings']['discount_type']=='percentage'){
							echo esc_html('%');
						} else {
							echo ' ' . esc_html( get_option( 'ihc_currency' ) );
						}
					?></div>
				<div class="ihc-coupon-box-bottom-submitted"><?php
					esc_html_e("Submited Coupons:", "ihc");
					echo ' <strong>' . esc_html( $settings['submited_coupons_count'] );
					if (!empty($settings['settings']['repeat'])){
						 echo esc_html("/") . esc_html($settings['settings']['repeat']);
					}
					echo '</strong>';
				?></div>

				<div class="ihc-coupon-box-bottom-date"><?php
						if ($settings['settings']['period_type']=='unlimited'){
							echo '<span>'. esc_html__("No Date range", 'ihc').'</span>';
						}else if (!empty($settings['settings']['start_time']) && !empty($settings['settings']['end_time'])) {
							echo  esc_html__("From ", "ihc") .'<span>'. esc_html($settings['settings']['start_time']) . "</span><br/> " .  esc_html__("to ", "ihc") .'<span>' . esc_html($settings['settings']['end_time']) . '</span>';
						} else {
							echo '-';
						}
					?>
				</div>
				<div class="clear"></div>
			</div>
		</div>
	</div>
	<?php
}

/**
 * @param array $_POST
 * @return none
 */
function ihc_add_new_redirect_link($post_data=array())
{
	if (!empty($post_data)){
                if ( isset( $post_data['url'] ) ){
                    $post_data['url'] = sanitize_text_field( $post_data['url'] );
                }
                if ( isset( $post_data['name'] ) ){
                    $post_data['name'] = sanitize_text_field( $post_data['name'] );
                }

		if (strpos($post_data['url'], 'http')===FALSE){
			$post_data['url'] = "http://" . $post_data['url'];
		}
		$data = get_option("ihc_custom_redirect_links_array");
		if ($data && is_array($data)){
                    if (!array_key_exists($post_data['name'], $data)){
			$data[$post_data['name'] ] = $post_data['url'];
                    }
		} else {
			$data[$post_data['name'] ] = $post_data['url'];
		}
		update_option("ihc_custom_redirect_links_array", $data);
	}
}

/**
 * @param string
 * @return none
 */
function ihc_delete_redirect_link($name='')
{

	$data = get_option("ihc_custom_redirect_links_array");
	if (isset($data[$name])){
		unset($data[$name]);
	}
	update_option("ihc_custom_redirect_links_array", $data);
}

/**
 * @param none
 * @return array
 */
function ihc_get_redirect_links_as_arr_for_select()
{
	$return = array();
	$redirect_links = get_option("ihc_custom_redirect_links_array");
	if (is_array($redirect_links) && count($redirect_links)){
		foreach ($redirect_links as $k=>$v){
			$return[$k] =  esc_html__("Custom Link: ", 'ihc') . $k;
		}
	}
	if (ihc_is_magic_feat_active('individual_page')){
		$return['#individual_page#'] =  esc_html__('Individual Page (from Extensions)', 'ihc');
	}
	return $return;
}

/**
 * @param none
 * @return string
 */
function ihc_check_payment_gateways()
{
	$levels = \Indeed\Ihc\Db\Memberships::getAll();
	if ($levels){
		$paid_levels = FALSE;
		foreach ($levels as $level){
			if ($level['payment_type']=='payment'){
				$paid_levels = TRUE;
			}
		}
		if ($paid_levels){
			$payments_gateways = ihc_list_all_payments();
			$err_msg = TRUE;
			foreach ($payments_gateways as $payment_gateway => $label ){
				if (ihc_check_payment_available($payment_gateway)){
					$err_msg = FALSE;
					break;
				}
			}

			if ($err_msg){
				return '<div class="ihc-not-set"><strong>' . esc_html__('No Payment Gateway was activated or properly set!', 'ihc') . '</strong></div>';
			}

			$default_payment = get_option('ihc_payment_selected');
			if (!ihc_check_payment_available($default_payment)){
				return '<div class="ihc-not-set"><strong>' .  esc_html__("Default Payment Gateway it's not activated or properly set!", 'ihc') . '</strong></div>';
			}
		}
	}
}

/**
 * @param none
 * @return float
 */
function get_ump_version()
{
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	$plugin_data = get_plugin_data( IHC_PATH . 'indeed-membership-pro.php', false, false);
	return $plugin_data['Version'];
}

/**
 * @param string, array, string .  key can be post_type (post, pages, products) || category name
 * @return none
 */
function ihc_save_block_group($group_name='', $post_data=array(), $key='')
{
	if ($group_name && $post_data){
		$data = get_option($group_name);
		if (empty($data)){
			$key = 0;
		} else {
			end($data);
			$key = key($data);
			$key++;
		}
		$data[$key] = indeed_sanitize_array($post_data);
		update_option($group_name, $data);
	}
}

/**
 * @param string, int
 * @return none
 */
function ihc_delete_block_group($group_name='', $key=0)
{
	 if (isset($group_name) && isset($key)){
	 	 $data = get_option($group_name);
		 if (!empty($data[$key])){
		 	unset($data[$key]);
		 }
		 update_option($group_name, $data);
	 }
}

function ihc_return_membership_plan($level = array(), $currency='' )
{

    $details = '';

    if(!isset($level) || empty($level))
    return $details;

   $price = '';
   if(isset($level['price'])){
                   if($level['price'] > 0) {
                                   $price = ihc_format_price_and_currency($currency, $level['price']);
                   }else{
                           $price =  esc_html__('Free','ihc');
                   }
   }

   $period = '';
   $trialprice = '';
   $trialperiod = '';

           switch($level['access_type']){
                   case 'unlimited':
                           $period .=  esc_html__(' for ','ihc'). esc_html__('Lifetime', 'ihc');
                           break;
                   case 'limited':
                           if(isset($level['access_limited_time_value']) && isset($level['access_limited_time_type']) ){
                                   $period .=  esc_html__(' for ','ihc').$level['access_limited_time_value'].' ';
                                   switch($level['access_limited_time_type']){
                                                   case 'D':
                                                                   if($level['access_limited_time_value'] > 1) {
                                                                           $period .=  esc_html__('days', 'ihc');
                                                                   }else{
                                                                           $period .=  esc_html__('day', 'ihc');
                                                                   }
                                                           break;
                                                   case 'W':
                                                                   if($level['access_limited_time_value'] > 1) {
                                                                           $period .=  esc_html__('weeks', 'ihc');
                                                                   }else{
                                                                           $period .=  esc_html__('week', 'ihc');
                                                                   }
                                                           break;
                                                   case 'M':
                                                                   if($level['access_limited_time_value'] > 1) {
                                                                           $period .=  esc_html__('months', 'ihc');
                                                                   }else{
                                                                           $period .=  esc_html__('month', 'ihc');
                                                                   }
                                                           break;
                                                   case 'Y':
                                                                   if($level['access_limited_time_value'] > 1) {
                                                                           $period .=  esc_html__('years', 'ihc');
                                                                   }else{
                                                                           $period .=  esc_html__('year', 'ihc');
                                                                   }
                                                           break;
                                                   default:
                                                                   if($level['access_limited_time_value'] > 1) {
                                                                           $period .=  esc_html__('days', 'ihc');
                                                                   }else{
                                                                           $period .=  esc_html__('day', 'ihc');
                                                                   }

                                   }
                           }
                           break;

                   case 'date_interval':
                           if(isset($level['access_interval_start']) && isset($level['access_interval_end']) ){
                                   $period .=  esc_html__(' between ','ihc').ihc_convert_date_to_us_format($level['access_interval_start']). esc_html__(' and ', 'ihc').ihc_convert_date_to_us_format($level['access_interval_end']);
                           }
                           break;

                   case 'regular_period':
                           $period .=  esc_html__(' on every ', 'ihc');
                           $additional_details = '';

                                   if($level['access_regular_time_type'] == 'D'){
                                           if($level['access_regular_time_value'] == 1){
                                                   $period .= esc_html__('day', 'ihc');
                                           }elseif($level['access_regular_time_value'] > 1){
                                                   $period .= $level['access_regular_time_value'].  esc_html__(' days', 'ihc');
                                           }
                                   }
                                   if($level['access_regular_time_type'] == 'W'){
                                           if($level['access_regular_time_value'] == 1){
                                                   $period .= esc_html__('week', 'ihc');
                                           }elseif($level['access_regular_time_value'] > 1){
                                                   $period .= $level['access_regular_time_value'].  esc_html__(' weeks', 'ihc');
                                           }
                                   }
                                   if($level['access_regular_time_type'] == 'M'){
                                           if($level['access_regular_time_value'] == 1){
                                                   $period .= esc_html__('month', 'ihc');
                                           }elseif($level['access_regular_time_value'] > 1){
                                                   $period .=$level['access_regular_time_value']. esc_html__(' months', 'ihc');
                                           }
                                   }
                                   if($level['access_regular_time_type'] == 'Y'){
                                           if($level['access_regular_time_value'] == 1){
                                                           $period .= esc_html__('year', 'ihc');
                                           }elseif($level['access_regular_time_value'] > 1){
                                                           $period .= $level['access_regular_time_value']. esc_html__(' years', 'ihc');
                                           }
                                   }

                                   if ($level['billing_type'] == 'bl_limited' && $level['billing_limit_num'] > 1){
                                           $additional_details = esc_html__(' for ', 'ihc').$level['billing_limit_num']. esc_html__(' installments', 'ihc');
                                   }

                                   $period .= $additional_details;

                                   if ( !empty( $level['access_trial_type'] )
           && ( (!empty( $level['access_trial_time_value'] ) && ($level['access_trial_type']==1))
           || (!empty( $level['access_trial_couple_cycles'] ) && ($level['access_trial_type']==2)) ) ){
                                           if ( $level['access_trial_price'] > 0  ) {
                                                   $trialprice .= ihc_format_price_and_currency($currency, $level['access_trial_price']);
                                           }else{
                                                   $trialprice .=  esc_html__('Free', 'ihc');
                                           }
                                           $trialperiod .=  esc_html__(' for ', 'ihc');

                                           if($level['access_trial_type']==1){
                                                   if($level['access_trial_time_type'] == 'D'){
                                                           if($level['access_trial_time_value'] == 1){
                                                                   $trialperiod .= esc_html__('1 day', 'ihc');
                                                           }elseif($level['access_trial_time_value'] > 1){
                                                                   $trialperiod .= $level['access_trial_time_value'].  esc_html__(' days', 'ihc');
                                                           }
                                                   }
                                                   if($level['access_trial_time_type'] == 'W'){
                                                           if($level['access_trial_time_value'] == 1){
                                                                   $trialperiod .= esc_html__('1 week', 'ihc');
                                                           }elseif($level['access_trial_time_value'] > 1){
                                                                   $trialperiod .= $level['access_trial_time_value'].  esc_html__(' weeks', 'ihc');
                                                           }
                                                   }
                                                   if($level['access_trial_time_type'] == 'M'){
                                                           if($level['access_trial_time_value'] == 1){
                                                                   $trialperiod .= esc_html__('1 month', 'ihc');
                                                           }elseif($level['access_trial_time_value'] > 1){
                                                                   $trialperiod .=$level['access_trial_time_value']. esc_html__(' months', 'ihc');
                                                           }
                                                   }
                                                   if($level['access_trial_time_type'] == 'Y'){
                                                           if($level['access_trial_time_value'] == 1){
                                                                           $trialperiod .= esc_html__('1 year', 'ihc');
                                                           }elseif($level['access_trial_time_value'] > 1){
                                                                           $trialperiod .= $level['access_trial_time_value']. esc_html__(' years', 'ihc');
                                                           }
                                                   }
                                           }
                                           if($level['access_trial_type']==2){
                                                   if($level['access_trial_couple_cycles'] == 1){
                                                                   $trialperiod .= esc_html__('1 cycle', 'ihc');
                                                   }elseif($level['access_trial_couple_cycles'] > 1){
                                                                   $trialperiod .= $level['access_trial_couple_cycles']. esc_html__(' cycles', 'ihc');
                                                   }
                                           }

                                           $trialperiod .=  esc_html__(' then ', 'ihc');
                                   }
                           break;

                   default:
                   $period .=  esc_html__('Life Time', 'ihc');
           }
           if($trialprice != '' && $trialperiod != '')
                           $details .= '<span class="ihc-membership-price ihc-membership-trialprice">' . esc_ump_content( $trialprice ) . '</span>'.'<span class="ihc-membership-price-period ihc-membership-trialperiod">' . esc_ump_content( $trialperiod ) . '</span>';

   if($price != '' && $period != '')
                   $details .= '<span class="ihc-membership-price">' . esc_ump_content( $price ) . '</span>'.'<span class="ihc-membership-price-period">' . esc_ump_content( $period ) . '</span>';

   return $details;
}
