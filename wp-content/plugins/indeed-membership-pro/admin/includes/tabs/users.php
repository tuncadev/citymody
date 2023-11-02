<div class="ihc-subtab-menu">
	<?php ?>
	<a class="ihc-subtab-menu-item  <?php echo ( isset($_REQUEST['ihc-new-user']) && $_REQUEST['ihc-new-user']  == 'true') ? 'ihc-subtab-selected' : '';?>" href="<?php echo esc_url($url . '&tab=users&ihc-new-user=true');?>"><?php esc_html_e('Add New Member', 'ihc');?></a>
	<a class="ihc-subtab-menu-item  <?php echo ( !isset($_REQUEST['ihc-new-user'])) ? 'ihc-subtab-selected' : '';?>" href="<?php echo esc_url($url . '&tab=' . $tab );?>"><?php esc_html_e('Manage Members', 'ihc');?></a>

	<div class="ihc-clear"></div>
</div>
<?php
wp_enqueue_script( 'ihcAdminSendEmail', IHC_URL . 'admin/assets/js/ihcAdminSendEmail.js', ['jquery'], 10.1 );
wp_enqueue_script( 'ihcSearchUsers', IHC_URL . 'admin/assets/js/search_users.js', ['jquery'], 10.1 );

echo ihc_inside_dashboard_error_license();
$is_uap_active = ihc_is_uap_active();

//
if (isset($_POST['delete_users']) && !empty( $_POST['ihc_du'] ) && wp_verify_nonce( sanitize_text_field($_POST['ihc_du']), 'ihc_delete_users' ) ){
	ihc_delete_users(0, indeed_sanitize_array($_POST['delete_users']));
}

// save user
if ( isset( $_POST['ihc_save_member'] ) ){
		$memberObject = new \Indeed\Ihc\Admin\MemberAddEdit();
		$userId = $memberObject->save( indeed_sanitize_array($_POST) );
		if ( $userId == 0 ){
				$errors = $memberObject->getErrors();
		}
}

// print errors from save user if its case
if (!empty($errors) && count($errors)>0){
	if ( isset( $errors['general'] ) && $errors['general'] !== '' ){
			echo '<div class="ihc-wrapp-the-errors">' . $errors['general'] . '</div>';
			unset( $errors['general'] );
	}
	if (!empty($errors) && count($errors)>0){
			echo '<div class="ihc-wrapp-the-errors">';
			foreach ( $errors as $key=>$err ){
					echo esc_html__('Field ', 'ihc') . $key . ': ' . $err;
			}
			echo '</div>';
	}
}


//set default pages message
echo ihc_check_default_pages_set();
echo ihc_check_payment_gateways();
echo ihc_is_curl_enable();
do_action( "ihc_admin_dashboard_after_top_menu" );

	if (isset($_REQUEST['ihc-edit-user']) || isset($_REQUEST['ihc-new-user'])){
		//add edit user
		$memberObject = new \Indeed\Ihc\Admin\MemberAddEdit();
		if ( isset( $_GET['ihc-edit-user'] ) ){
				$memberObject->setUid( sanitize_text_field($_GET['ihc-edit-user']) );
		}
		$form = $memberObject->form();

		?>
			<div class="ihc-stuffbox ihc-add-new-user-wrapper">
				<h3><?php esc_html_e('Add/Update Membership Members', 'ihc');?></h3>
				<div class="inside">
                	<div class="ihc-admin-edit-user">
                     <div class="ihc-admin-user-form-wrapper">
                   			 <h2><?php esc_html_e('Member Profile details', 'ihc');?></h2>
					 		<p><?php esc_html_e('Manage what fields are available for Admin setup from "Showcases->Register Form->Custom Fields" section ', 'ihc');?></p>
                    </div>
						<?php echo esc_ump_content($form);?>
					</div>
                </div>
			</div>
		<?php
	} else {
$directLogin = get_option( 'ihc_direct_login_enabled' );
$individual_page = get_option( 'ihc_individual_page_enabled' );
?>
<div class="iump-wrapper">
	<div id="col-right" class="ihc-admin-listing-users">
		<div class="iump-page-title">Ultimate Membership Pro -
			<span class="second-text">
				<?php esc_html_e('Membership Members', 'ihc');?>
			</span>
		</div>
		<a href="<?php echo esc_url($url.'&tab=users&ihc-new-user=true');?>" class="indeed-add-new-like-wp">
			<i class="fa-ihc fa-add-ihc"></i><?php esc_html_e('Add New Member', 'ihc');?>
		</a>

		<div class="ihc-special-buttons-users">
			<div class="ihc-special-button" onclick="ihcShowHide('.ihc-filters-wrapper');"><i class="fa-ihc fa-export-csv"></i><?php esc_html_e('Apply Filters', 'ihc');?></div>
			<div class="ihc-special-button ihc-list-user-make-csv"  id="ihc_make_user_csv_file" data-get_variables='<?php echo json_encode( indeed_sanitize_array($_GET) );?>' onClick="ihcMakeUserCsv();"><i class="fa-ihc fa-export-csv"></i><?php esc_html_e( 'Export CSV', 'ihc' );?></div>
			<div class="ihc-hidden-download-link"><a href="" target="_blank"><?php esc_html_e("Click on this if download doesn't start automatically in 20 seconds!");?></a></div>
			<div class="ihc-clear"></div>
		</div>


		<?php
		$hidded = 'ihc-display-none';
		$possibles = array(
				'search_user',
				'levels',
				'roles',
				'order',
				'levelStatus',
				'approvelRequest',
				'emailVerification',
				'advancedOrder',
		);
		foreach ( $possibles as $possible ){
				if ( isset( $_GET[$possible] ) ){
						$hidded ='';
				}
		}
		?>
		<div class="ihc-filters-wrapper <?php echo esc_attr($hidded); ?>" >
			<form method="get" >
				<input type="hidden" name="page" value="ihc_manage" />
				<input type="hidden" name="tab" value="users" />
				<div class="ihc-section-wrapper">
                 <div class="ihc-filter-section-wrapper ihc-filter-search">
                 	<div class="row-fluid">
					<div class="span10">
						<div class="iump-form-line iump-no-border">
							<input name="search_user" type="text" value="<?php echo (isset($_GET['search_user']) ? sanitize_text_field( $_GET['search_user'] ) : '') ?>" placeholder="<?php esc_html_e('Search by Name or Username, Email', 'ihc');?>..."/>
						</div>
					</div>
					<div class="span2">
						<input type="submit" value="<?php esc_html_e( 'Search Members', 'ihc' );?>" name="search" class="button button-primary button-large" id="ihc_search_user_base_field" data-base_link="<?php echo admin_url( 'admin.php?page=ihc_manage&tab=users' );?>" />
					</div>
                    </div>
				</div>
                <div class="ihc-filter-section-wrapper">
                <div class="row-fluid">
                	<div class="col-xs-6">
                    	<div class="span12">
					<div class="iump-form-line iump-no-border">
						<h3><?php esc_html_e( 'Filter by Memberships', 'ihc' );?></h3>
                        <div class="ihc-search-user-select-filter-bttn js-ihc-select-all-levels ihc-search-user-select-all"><?php esc_html_e( 'Select all Memberships', 'ihc');?></div>
						<div class="ihc-search-user-select-filter-bttn js-ihc-deselect-all-levels  ihc-search-user-select-all"><?php esc_html_e( 'Deselect all Memberships', 'ihc');?></div>
                        <div></div>
						<?php
								$levels_arr = \Indeed\Ihc\Db\Memberships::getAll();
						?>
						<?php if ( $levels_arr ):?>
								<?php
										$getValues = isset( $_GET['levels'] ) ? sanitize_text_field($_GET['levels']) : '';
										if ( stripos( $getValues, ',' ) !== false ) {
												$getValues = explode( ',', $getValues);
										} else {
												$getValues = array( $getValues );
										}
								?>
								<?php foreach ( $levels_arr as $id => $levelData ): ?>
										<?php $enabled = in_array( $id, $getValues ) ? 1 : 0;?>
										<div class="ihc-search-user-select-filter-bttn js-ihc-search-select <?php echo ($enabled === 1) ? 'ihc-green-bttn' : 'ihc-gray-bttn';?>" data-name="levels" data-value="<?php echo esc_attr($id);?>" data-enabled="<?php echo esc_attr($enabled);?>" ><?php echo esc_html($levelData['label']);?></div>
								<?php endforeach;?>
						<?php endif;?>

					</div>
				</div>
                		<div class="span12">
					<div class="iump-form-line iump-no-border">
						<h3><?php esc_html_e( 'Filter by Memberships status', 'ihc' );?></h3>
						<?php
						$statusArray = array(
																	'active'			  => esc_html__( 'Active', 'ihc' ),
																	'expired'			  => esc_html__( 'Expired', 'ihc' ),
																	'hold'				  => esc_html__( 'On hold', 'ihc' ),
																	'expire_soon'  => esc_html__( 'Expire soon', 'ihc' ),
						);
						?>
						<?php if ( $statusArray ):?>
								<?php
										$getValues = isset( $_GET['levelStatus'] ) ? sanitize_text_field($_GET['levelStatus']) : '';
										if ( stripos( $getValues, ',' ) !== false ) {
												$getValues = explode( ',', $getValues);
										} else {
												$getValues = array( $getValues );
										}
								?>
								<?php foreach ( $statusArray as $key => $label ): ?>
										<?php $enabled = in_array( $key, $getValues ) ? 1 : 0;?>
										<div class="ihc-search-user-select-filter-bttn js-ihc-search-select ihc-filter-level-<?php echo esc_attr($key); ?> <?php echo ($enabled === 1 ) ? 'ihc-green-bttn' : 'ihc-gray-bttn';?>" data-name="levelStatus" data-value="<?php echo esc_attr($key);?>" data-enabled="<?php echo esc_attr($enabled);?>" ><?php echo esc_attr($label);?></div>
								<?php endforeach;?>
						<?php endif;?>
					</div>
				</div>

                    </div>

                    <div class="col-xs-6">
                    	<div class="span12">
					<div class="iump-form-line iump-no-border ihc-filter-wproles">
						<h3><?php esc_html_e( 'WordPress Roles', 'ihc' );?></h3>
							<?php
								$filter_roles = ihc_get_wp_roles_list();
								if ( isset( $filter_roles['pending_user'] ) ){
										unset( $filter_roles['pending_user'] );
								}
							?>
							<?php if ($filter_roles):?>
                            <div class="ihc-search-user-select-filter-bttn js-ihc-select-all-roles ihc-search-user-select-all"><?php esc_html_e( 'Select all Roles', 'ihc');?></div>
							<div class="ihc-search-user-select-filter-bttn js-ihc-deselect-all-roles ihc-search-user-select-all"><?php esc_html_e( 'Deselect all Roles', 'ihc');?></div>
                            <div></div>
									<?php
											$getValues = isset( $_GET['roles'] ) ? sanitize_text_field($_GET['roles']) : '';
											if ( stripos( $getValues, ',' ) !== false ) {
													$getValues = explode( ',', $getValues);
											} else {
													$getValues = array( $getValues );
											}
									?>
									<?php foreach ( $filter_roles as $key => $label ): ?>
											<?php $enabled = in_array( $key, $getValues ) ? 1 : 0;?>
											<div class="ihc-search-user-select-filter-bttn js-ihc-search-select <?php echo ($enabled === 1) ? 'ihc-green-bttn' : 'ihc-gray-bttn';?>" data-name="roles" data-value="<?php echo esc_attr($key);?>" data-enabled="<?php echo esc_attr($enabled);?>" ><?php echo esc_html($label);?></div>
									<?php endforeach;?>
							<?php endif;?>

					</div>
				</div>
                <div>
                <h3><?php esc_html_e( 'Administrator Requests', 'ihc' );?></h3>

						<?php $enabled = isset( $_GET['approvelRequest'] ) && $_GET['approvelRequest'] ? 1 : 0;?>
						<div class="ihc-search-user-select-filter-bttn js-ihc-search-select <?php echo ($enabled === 1) ? 'ihc-green-bttn' : 'ihc-gray-bttn';?>" data-name="approvelRequest" data-value="1" data-enabled="<?php echo esc_attr($enabled);?>" ><?php esc_html_e( 'Approvel request', 'ihc' );?></div>

						<?php $enabled = isset( $_GET['emailVerification'] ) && $_GET['emailVerification'] ? 1 : 0;?>
						<div class="ihc-search-user-select-filter-bttn js-ihc-search-select <?php echo ($enabled === 1) ? 'ihc-green-bttn' : 'ihc-gray-bttn';?>" data-name="emailVerification" data-value="1" data-enabled="<?php echo esc_attr($enabled);?>" ><?php esc_html_e( 'Pending E-mail Verification', 'ihc' );?></div>

                </div>
                    </div>


	</div>
    </div>
    <div class="ihc-filter-section-wrapper  ihc-filter-orders">
                <div class="row-fluid">
                <div class="col-xs-8">
				<div class="span12">
					<div class="iump-form-line iump-no-border">
						<h3><?php esc_html_e( 'Order', 'ihc' );?></h3>
						<?php
								$possibleOrders = array(
																					'display_name_asc'										=> esc_html__( 'Name ASC', 'ihc' ),
																					'display_name_desc'										=> esc_html__( 'Name DESC', 'ihc'),
																					'user_login_asc'											=> esc_html__( 'Username ASC', 'ihc' ),
																					'user_login_desc'											=> esc_html__( 'Username DESC', 'ihc' ),
																					'user_email_asc'											=> esc_html__( 'Email ASC', 'ihc' ),
																					'user_email_desc'											=> esc_html__( 'Email DESC', 'ihc' ),
																					'ID_asc'															=> esc_html__( 'ID ASC', 'ihc' ),
																					'ID_desc'															=> esc_html__( 'ID DESC', 'ihc' ),
																					'user_registered_asc'									=> esc_html__( 'Registered Time ASC', 'ihc' ),
																					'user_registered_desc'								=> esc_html__( 'Registered Time DESC', 'ihc' ),
								);
						?>
						<?php foreach ( $possibleOrders as $key => $label ):?>
								<?php $enabled = isset( $_GET['order'] ) && $_GET['order'] == $key ? 1 : 0;?>
								<div class="ihc-search-user-select-filter-bttn js-ihc-search-order <?php echo ($enabled === 1) ? 'ihc-green-bttn' : 'ihc-gray-bttn';?>" data-name="order" data-value="<?php echo esc_attr($key);?>" data-enabled="<?php echo esc_attr($enabled);?>" ><?php echo esc_html($label);?></div>
						<?php endforeach;?>
					</div>
				</div>
			</div>
            <div class="col-xs-4">
				<div class="span12">
						<?php
								$getValues = isset( $_GET['advancedOrder'] ) ? sanitize_text_field($_GET['advancedOrder']) : '';
								if ( stripos( $getValues, ',' ) !== false ) {
										$getValues = explode( ',', $getValues);
								} else {
										$getValues = array( $getValues );
								}
						?>
						<h3><?php esc_html_e( 'Advanced order', 'ihc' );?></h3>
						<?php $enabled = in_array( 'newSubscription', $getValues ) ? 1 : 0;?>
						<div class="ihc-search-user-select-filter-bttn js-ihc-search-advanced-order <?php echo ($enabled === 1) ? 'ihc-green-bttn' : 'ihc-gray-bttn';?>" data-name="advancedOrder" data-value="newSubscription" data-enabled="<?php echo esc_attr($enabled);?>" ><?php esc_html_e( 'New Memberships', 'ihc' );?></div>
						<?php $enabled = in_array( 'recentlyExpired', $getValues ) ? 1 : 0;?>
						<div class="ihc-search-user-select-filter-bttn js-ihc-search-advanced-order <?php echo ($enabled === 1) ? 'ihc-green-bttn' : 'ihc-gray-bttn';?>" data-name="advancedOrder" data-value="recentlyExpired" data-enabled="<?php echo esc_attr($enabled);?>" ><?php esc_html_e( 'Recently Expired', 'ihc' );?></div>
						<?php $enabled = in_array( 'goingToExpire', $getValues ) ? 1 : 0;?>
						<div class="ihc-search-user-select-filter-bttn js-ihc-search-advanced-order <?php echo ($enabled === 1) ? 'ihc-green-bttn' : 'ihc-gray-bttn';?>" data-name="advancedOrder" data-value="goingToExpire" data-enabled="<?php echo esc_attr($enabled);?>" ><?php esc_html_e( 'Going to expire', 'ihc' );?></div>
				</div>
			</div>
            </div>
            </div>
			</div>
			</form>
		</div>
		<form method="post"  class="ihc-filter-form" name="ihc-users">
			<?php
				$currency = get_option( 'ihc_currency' );
				$limit = (isset($_GET['ihc_limit'])) ? (int)sanitize_text_field($_GET['ihc_limit']) : 25;
				$start = 0;
				if(isset($_GET['ihcdu_page'])){
					$pg = (int)sanitize_text_field($_GET['ihcdu_page']) - 1;
					if ( $pg < 0){
						$pg = 0;
					}
					$start = (int)$pg * $limit;
				}
				$search_query = isset($_GET['search_user']) ? sanitize_text_field($_GET['search_user']) : '';
				$filter_role = isset($_GET['roles']) ? sanitize_text_field($_GET['roles']) : '';
				$search_level = isset($_GET['levels']) ? sanitize_text_field($_GET['levels']) : -1;
				$order = isset($_GET['order']) ? $_GET['order'] : 'user_registered_desc'; // user_registered_desc
				$approveRequest = isset( $_GET['approvelRequest'] ) && $_GET['approvelRequest'] ? true : false;
				$advancedOrder = isset( $_GET['advancedOrder'] ) ? sanitize_text_field($_GET['advancedOrder']) : '';
				$levelStatus = isset( $_GET['levelStatus'] ) ? sanitize_text_field($_GET['levelStatus']) : '';
				$emailVerification = isset( $_GET['emailVerification'] ) && $_GET['emailVerification'] ? 1 : 0;

				$searchUsers = new \Indeed\Ihc\Db\SearchUsers();
				$searchUsers->setLimit( $limit )
										->setOffset( $start )
										->setOrder( $order )
										->setLid( $search_level )
										->setSearchWord( $search_query )
										->setRole( $filter_role )
										->setAdvancedOrder( $advancedOrder )
										->setLevelStatus( $levelStatus )
										->setOnlyDoubleEmailVerification( $emailVerification )
										->setApprovelRequest( $approveRequest );
				$total_users = $searchUsers->getCount();
				$users = $searchUsers->getResults();
				$levelDetails = \Ihc_Db::getLevelsDetails();

				$doubleEmailVerfication = get_option( 'ihc_register_double_email_verification' );
			?>
			<div>
				<?php
					//SEARCH FILTER BY USER LEVELS
					if ($start==0){
                                            $current_page = 1;
					} else {
                                            $current_page = (int)sanitize_text_field($_GET['ihcdu_page']);
                                        }

					require_once IHC_PATH . 'classes/Ihc_Pagination.class.php';

					$url = IHC_PROTOCOL . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
					$pagination_object = new Ihc_Pagination(array(
																'base_url' => $url,
																'param_name' => 'ihcdu_page',
																'total_items' => $total_users,
																'items_per_page' => $limit,
																'current_page' => $current_page,
					));
					$pagination = $pagination_object->output();


					/// UAP
					if ($is_uap_active){
						global $indeed_db;
						if (empty($indeed_db) && defined('UAP_PATH')){
							include UAP_PATH . 'classes/Uap_Db.class.php';
							$indeed_db = new Uap_Db;
						}
					}
					/// UAP

					$magic_feat_user_sites = ihc_is_magic_feat_active('user_sites');

					if ($users){
						?>
							<div class="ihc-delete-button-wrapper">
								<div class="ihc-delete-button" >
									<input type="submit" value="<?php esc_html_e('Remove', 'ihc');?>" name="delete" onClick="event.preventDefault();ihcFirstConfirmBeforeSubmitForm('<?php esc_html_e('Are You Sure You want to delete selected Members?');?>');" class="button button-primary button-large ihc-remove-group-button"/>
								</div>
<?php
$url = IHC_PROTOCOL . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$url = remove_query_arg('ihc_limit', $url);
$url = remove_query_arg('ihcdu_page', $url);
?>
								<div class="ihc-offset-wrapper">
									<strong><?php esc_html_e('Number of Members per Page:', 'ihc');?></strong>
									<select name="ihc_limit" class="js-ihc-search-users-limit" data-url="<?php echo esc_url($url . '&ihc_limit=');?>" >
										<?php
											foreach(array(5,25,50,100,200,500) as $v){
												?>
													<option value="<?php echo esc_attr($v);?>" <?php if($limit==$v){
														 echo 'selected';
													}
													?>
													><?php echo esc_html($v);?></option>
												<?php
											}
										?>
									</select>
								</div>
								<?php //////////////////PAGINATION
											echo esc_ump_content($pagination);
									?>
								<div class="clear"></div>
							</div>
							<div class="iump-rsp-table">
						   <table class="wp-list-table widefat fixed tags ihc-admin-tables ihc-admin-tables-users">
							  <thead>
								<tr>
									  <th class="manage-column column-cb check-column ihc-users-table-col1">
									  	<input type="checkbox" onClick="ihcSelectAllCheckboxes( this, '.ihc-delete-user' );" />
									  </th>
										 <th class="manage-column ihc-users-table-col2">
											 <?php esc_html_e('User ID', 'ihc');?>
										</th>
									  <th class="manage-column column-primary ihc-users-table-col3">
											<?php esc_html_e('Full Name', 'ihc');?>
									  </th>
									  <th class="manage-column ihc-users-table-col4">
											<?php esc_html_e('Email Address', 'ihc');?>
									  </th>
									  <th class="manage-column ihc-users-table-col5">
											<?php esc_html_e('Membership Plans', 'ihc');?>
									  </th>
										<th class=" manage-column ihc-users-table-col6"><?php esc_html_e( 'Total Spend', 'ihc' );?></th>
										<?php do_action( 'ump_action_admin_list_user_column_name_after_total_spend' );?>
									  <?php if (!empty($magic_feat_user_sites)):?>
									  <th class="manage-column">
									  		<?php esc_html_e('Sites', 'ihc');?>
									  </th>
									  <?php endif;?>
									  <th class="manage-column ihc-users-table-col7">
											<?php esc_html_e('WP Member Role', 'ihc');?>
									  </th>
									  <th class="manage-column ihc-users-table-col8">
											<?php esc_html_e('Email Status', 'ihc');?>
									  </th>
									  <th class="manage-column ihc-users-table-col9">
											<?php esc_html_e('Sign Up date', 'ihc');?>
									  </th>
									  <th class="manage-column ihc-users-table-col10">
											<?php esc_html_e('Details', 'ihc');?>
									  </th>
							    </tr>
							  </thead>

								<tbody>
							  <?php
							  		$i = 1;
							  		$available_roles = ihc_get_wp_roles_list();

							  		foreach ($users as $user){
											$userIds[] = $user->ID;
							  			$verified_email =  get_user_meta($user->ID, 'ihc_verification_status', TRUE);
											$roles = isset($user->roles) ? array_keys(unserialize($user->roles)) : $user->roles;
							  			?>
			    						   		<tr id="<?php echo esc_attr("ihc_user_id_" . $user->ID);?>" class="<?php if($i%2==0){
															 echo esc_attr('alternate');
														}
														?>" onMouseOver="ihcDhSelector('#user_tr_<?php echo esc_attr($user->ID);?>', 1);" onMouseOut="ihcDhSelector('#user_tr_<?php echo esc_attr($user->ID);?>', 0);">
			    						   			<th class="check-column">
									  					<input type="checkbox" class="ihc-delete-user" name="delete_users[]" value="<?php echo esc_attr($user->ID);?>" />
									 						</th>
															<th class="check-column"><span class="ihc-users-list-wpid"><?php echo esc_attr($user->ID); ?></span></th>
			    						   			<td class="has-row-actions column-primary">
														  <div class="ihc-users-list-avatar-wrapper">
																<?php
																$avatar = ihc_get_avatar_for_uid( $user->ID );
										                if ( !isset( $avatar ) ){
										                    $avatar = 'https://secure.gravatar.com/avatar/1cc31b08528740e0d8519581e6bf1b04?s=96&amp;d=mm&amp;r=g';
										                }
										            ?>
										            <img src="<?php echo esc_url($avatar);?>" />
															</div>
															<div class="ihc-users-list-fullname-wrapper">
																<div class="ihc-users-list-fullname">
                                                    <?php
																	$firstName = isset( $user->first_name ) ? $user->first_name : '';
																	$lastName = isset( $user->last_name ) ? $user->last_name : '';
			    						   					if ( !empty( $firstName ) || !empty( $lastName ) ){
			    						   						echo esc_html($firstName) . ' ' . esc_html($lastName);
			    						   					} else {
			    						   						echo esc_html($user->user_nicename);
			    						   					}
			    						   				?>
															</div>
														<div class="ihc-users-list-username-wrapper">
															<span class="ihc-users-list-username"><?php echo esc_html($user->user_login);?></span>
														<?php
															if ($is_uap_active && !empty($indeed_db)){
																$is_affiliate = $indeed_db->is_user_affiliate_by_uid($user->ID);
																if ($is_affiliate){
																	?>
																	<span class="ihc-user-is-affiliate"><?php esc_html_e('Affiliate', 'ihc');?></span>
																	<?php
																}
															}
														?>
													</div>
														<div class="ihc-buttons-rsp ihc-visibility-hidden" id="user_tr_<?php echo esc_attr($user->ID);?>">
															<a class="iump-btns" href="<?php echo esc_url($url . '&tab=users&ihc-edit-user=' . $user->ID );?>"><?php esc_html_e('Edit', 'ihc');?></a>
															| <a class="iump-btns" target="_blank" href="<?php echo ihcAdminUserDetailsPage( $user->ID );?>"><?php esc_html_e('Member Profile', 'ihc');?></a>
															| <a class="iump-btns" href="<?php echo admin_url('admin.php?page=ihc_manage&tab=edit-user-subscriptions&uid=') . esc_attr($user->ID);?>" target="_blank"><?php esc_html_e( 'Manage Plans', 'ihc' );?></a>
															| <a class="iump-btns ihc-delete-link" onClick="ihcDeleteUserPrompot(<?php echo esc_attr($user->ID);?>);" href="javascript:return false;"><?php esc_html_e('Delete', 'ihc');?></a>

															<?php
																///get role !!!!
																if (isset($roles) && isset( $roles[0] ) && $roles[0]=='pending_user'){
																	?>
																	<span id="approveUserLNK<?php echo esc_attr($user->ID);?>" onClick="ihcApproveUser(<?php echo esc_attr($user->ID);?>);">
																	 | <span class="iump-btns ihc-approve-link"><?php esc_html_e('Approve', 'ihc');?></span>
																	</span>
																	<?php
																}
																if ($verified_email==-1){
																	?>
																	<span id="approve_email_<?php echo esc_attr($user->ID);?>" onClick="ihcApproveEmail(<?php echo esc_attr($user->ID);?>, '<?php esc_html_e("Verified", "ihc");?>');">
																	 | <span class="iump-btns ihc-approve-link"><?php esc_html_e('Approve Email', 'ihc');?></span>
																	</span>
																	<?php
																}
															?>
														</div>
													</div>
													<div class="ihc-clear"></div>
														<button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button>
			    						   			</td>
			    						   			<!--td class="ihc-users-list-name">
			    						   				<?php echo esc_html($user->user_login);?>
			    						   			</td-->
			    						   			<td>
			    						   				<a href="<?php echo 'mailto:' . esc_url($user->user_email);?>" target="_blank"><?php echo esc_html($user->user_email);?></a>
			    						   			</td>
			    						   			<td>
																<strong>
			    						   				<?php
															$levels = array();
															if ( $user->levels && stripos( $user->levels, ',' ) !== false ){
																	$levels = explode( ',', $user->levels );
															} else {
																	$levels[] = $user->levels;
															}

															if ( $levels ){
																foreach ( $levels as $levelData ){
																			if ( $levelData == -1 ){
																					continue;
																			}
																			if ( strpos( $levelData, '|' ) !== false ){
																					$levelDataArray = explode( '|', $levelData );
																			} else {
																					$levelDataArray = array();
																			}

																			$lid = isset( $levelDataArray[0] ) ? $levelDataArray[0] : '';
																			$level_data = array(
																						'level_id'		=> $lid,
																						'start_time'	=> isset( $levelDataArray[1] ) ? $levelDataArray[1] : '',
																						'expire_time' => isset( $levelDataArray[2] ) ? $levelDataArray[2] : '',
																						'level_slug'	=> isset( $levelDetails[$lid]['slug'] ) ? $levelDetails[$lid]['slug'] : '',
																						'label'				=> isset( $levelDetails[$lid]['label'] ) ? $levelDetails[$lid]['label'] : '',
																			);

					    						   					$is_expired_class = '';
					    						   					$level_title = "Active";

																			/// is expired
																			if ( !\Indeed\Ihc\UserSubscriptions::isActive( $user->ID, $lid ) ){
																					$is_expired_class = 'ihc-expired-level';
																					$level_title = "Hold/Expired";
																			}

																			$level_format = ihc_prepare_level_show_format($level_data);
																	?>
                                                                    <div class="ihc-level-skin-wrapper">
                                                                    	<span class="ihc-level-skin-element ihc-level-skin-box">
                                                                        	<span class="ihc-level-skin-element">
                                                                            	<span class="ihc-level-skin-line"></span>
                                                                                <span class="ihc-level-skin-min <?php echo esc_attr($level_format['time_class']); ?>"><?php echo esc_html($level_format['start_time_format']); ?></span>
                                                                                <span class="ihc-level-skin-max <?php echo esc_attr($level_format['time_class']); ?>"><?php echo esc_html($level_format['expire_time_format']); ?></span>
                                                                            </span>
                                                                            <span class="ihc-level-skin-bar <?php echo esc_attr($level_format['bar_class']);?>" style = " width:<?php echo esc_attr($level_format['bar_width']);?>%;">

                                                                                <span class="ihc-level-skin-single <?php echo esc_attr($level_format['tooltip_class']);?>"><?php echo esc_html($level_format['tooltip_message']);?></span>
                                                                            </span>
                                                                            <span class="ihc-level-skin-grid">
																																							<?php
																																							if ($level_data['label'] === '' || $level_data['label'] === false ){
																																									$level_data['label'] = \Indeed\Ihc\Db\Memberships::getMembershipLabel( $level_data['level_id'] );
																																							}
																																							?>
                                                                            	<?php echo esc_html($level_data['label']);?>
                                                                            </span>
                                                                            <span class="ihc-level-skin-down-grid"><?php echo esc_html($level_format['extra_message']);?></span>
                                                                        </span>
                                                                    </div>


																	<!--div class="level-type-list <?php echo esc_attr($is_expired_class);?>" title="<?php echo esc_attr($level_data['level_slug']);?>"><?php echo esc_html($level_data['label']);?></div-->
																	<?php
																}
															}
			    						   				?>
															</strong>
			    						   			</td>
															<td class="ihc-users-list-joindate"><span id='<?php echo 'ihc_js_total_spent_for_' . esc_attr($user->ID);?>'><?php
																	echo ihc_format_price_and_currency_with_price_wrapp($currency, 0);
															?></span></td>
															<?php do_action( 'ump_action_admin_list_user_row_after_total_spend', $user->ID );?>
															<?php if (!empty($magic_feat_user_sites)):?>
			    						   				<?php
															$sites = array();
															$temp = array();
															if (!empty($user_levels)){
																foreach ($user_levels as $lid=>$level_data){
																	$temp['blog_id'] = Ihc_Db::get_user_site_for_uid_lid($user->ID, $lid);
																	if (!empty($temp['blog_id'])){
																		$site_details = get_blog_details( $temp['blog_id'] );
																		$temp['link'] = untrailingslashit($site_details->domain . $site_details->path);
																		$temp['blogname'] = $site_details->blogname;
																		if (strpos($temp['link'], 'http')===FALSE){
																			$temp['link'] = 'http://' . $temp['link'];
																		}
																		$temp['extra_class'] = Ihc_Db::is_blog_available($temp['blog_id']) ? 'fa-sites-is-active' : 'fa-sites-is-not-active';
																		$sites[] = $temp;
																	}
																}
															}
			    						   				?>
												  		<td class="manage-column">
												  			<?php if ($sites):?>
												  				<?php foreach ($sites as $site_data):?>
														  			<a href="<?php echo esc_url($temp['link']);?>" target="_blank" title="<?php echo esc_attr($temp['blogname']);?>">
															  			<i class="fa-ihc fa-user_sites-ihc <?php echo esc_attr($site_data['extra_class']);?>"></i>
														  			</a>
												  				<?php endforeach;?>
												  			<?php endif;?>
												  		</td>
												  	<?php endif;?>
			    						   			<td>
			    						   				<div id="user-<?php echo esc_attr($user->ID);?>-status">
				    						   				<?php
				    						   					if (isset($roles) && isset($roles[0]) && $roles[0]=='pending_user'){
				    						   						 ?>
				    						   						 	<span class="subcr-type-list iump-pending"><?php esc_html_e('Pending', 'ihc');?></span>
				    						   						 <?php
				    						   					} else {
				    						   						 ?>
				    						   						 	<span class="subcr-type-list"><?php
				    						   						 		if (isset($roles) && isset( $roles[0] ) && isset($available_roles[$roles[0]])){
				    						   						 			echo esc_html($available_roles[$roles[0]]);
				    						   						 		} else {
																				echo '-';
				    						   						 		}
				    						   						 	?></span>
				    						   						 <?php
				    						   					}
																		if (count($roles)>1){
																				for ($i=1;$i<count($roles); $i++){
																						?>
																						<span class="subcr-type-list">
																								<?php if (isset($available_roles[$roles[$i]])){
																									 echo esc_html($available_roles[$roles[$i]]);
																								}else{
																									echo esc_html__('Unknown role', 'ihc');
																								}
																								?>
																						</span>
																						<?php
																				}
																		}
				    						   				?>
			    						   				</div>
			    						   			</td>
			    						   			<td><?php
			    						   				$div_id = "user_email_" . $user->ID . "_status";
			    						   				$class = 'subcr-type-list';
			    						   				if ($verified_email==1){
			    						   					$label = esc_html__('Verified', 'ihc');
			    						   				} else if ($verified_email==-1){
			    						   					$label = esc_html__('Unapproved', 'ihc');
			    						   					$class = 'subcr-type-list iump-pending';
			    						   				} else {
			    						   					$label = esc_html__('-', 'ihc');
			    						   				}
			    						   				?>
			    						   					<div id="<?php echo esc_attr($div_id);?>">
			    						   						<span class="<?php echo esc_attr($class);?>"><?php echo esc_html($label);?></span>
			    						   					</div>
			    						   				<?php
																if ($verified_email==-1){
																	 if ( $doubleEmailVerfication ):?>
																		<span id="resend_double_email_email_<?php echo esc_attr($user->ID);?>_verification" >
																			 <span class="iump-btns ihc-approve-link ihc-js-resend-email-verification-link" data-user_id="<?php echo esc_attr($user->ID);?>" ><?php esc_html_e('Resend Verification link', 'ihc');?></span>
																		</span>
																	<?php
																	endif;
																}
			    						   			?></td>
			    						   			<td class="ihc-users-list-joindate">
			    						   				<?php
			    						   					echo ihc_convert_date_to_us_format(esc_html($user->user_registered));
			    						   				?>
			    						   			</td>
													<td>
														<!-- Manage plans -->
															<div class="level-type-list ihc_small_lightgrey_button"><a href="<?php echo admin_url('admin.php?page=ihc_manage&tab=edit-user-subscriptions&uid=') . esc_attr($user->ID);?>" target="_blank" class="ihc-manage-plan-link-color"><?php esc_html_e( 'Manage Plans', 'ihc' );?></a></div>

														 <div class="ihc_frw_button ihc_small_blue_button">
																		<a  class="ihc-white-link" target="_blank" href="<?php echo esc_url(ihcAdminUserDetailsPage( $user->ID ));?>"><?php esc_html_e('Member Profile', 'ihc');?></a>
															</div>
														<?php
														$ord_count = ihc_get_user_orders_count($user->ID);
														if(isset($ord_count) && $ord_count > 0): ?>
														<div class="ihc_frw_button"> <a href="<?php echo admin_url('admin.php?page=ihc_manage&tab=orders&uid=') . $user->ID;?>" target="_blank"><?php esc_html_e('Orders', 'ihc');?></a></div>
														<?php endif;?>
														<?php unset($ord_count);?>

                            <?php if ($directLogin):?>
																<div class="ihc_frw_button ihc_small_yellow_button ihc-admin-direct-login-generator ihc-pointer " data-uid="<?php echo esc_attr($user->ID); ?>"><?php esc_html_e('Direct Login', 'ihc');?></div>
														<?php endif;?>


                            <div class="ihc_frw_button ihc_small_grey_button ihc-admin-do-send-email-via-ump" data-uid="<?php echo esc_attr($user->ID); ?>"><?php esc_html_e('Direct Email', 'ihc');?></div>

														<?php if (ihc_is_magic_feat_active('user_reports') && Ihc_User_Logs::get_count_logs('user_logs', $user->ID)):?>
															<div class="level-type-list ihc_small_red_button"> <a href="<?php echo admin_url('admin.php?page=ihc_manage&tab=view_user_logs&type=user_logs&uid=') . esc_attr($user->ID);?>" target="_blank" class="ihc-white-link"><?php esc_html_e('User Reports', 'ihc');?></a></div>
														<?php endif;?>

                            <?php if ($individual_page):?>
																<div class="level-type-list ihc_small_orange_button"> <a href="<?php echo esc_url(ihc_return_individual_page_link($user->ID));?>" target="_blank" class="ihc-white-link"><?php esc_html_e('Individual Page', 'ihc');?></a></div>
														<?php endif;?>

													</td>
			    						   		</tr>
							  			<?php
							  			$i++;
							  		}
							  ?>
							</tbody>
							<tfoot>
							<tr>
									<th  class="manage-column column-cb check-column ihc-users-list-col1">
										<input type="checkbox" onClick="ihcSelectAllCheckboxes( this, '.ihc-delete-user' );" />
									</th>
									 <th class="manage-column ihc-users-list-col2">
										 <?php esc_html_e('User ID', 'ihc');?>
									</th>
									<th class="manage-column column-primary">
										<?php esc_html_e('Full Name', 'ihc');?>
									</th>
									<th class="manage-column">
										<?php esc_html_e('Email Address', 'ihc');?>
									</th>
									<th class="manage-column ihc-users-list-col3">
										<?php esc_html_e('Membership Plans', 'ihc');?>
									</th>
									<th><?php esc_html_e( 'Total Spend', 'ihc' );?></th>
									<?php do_action( 'ump_action_admin_list_user_column_name_after_total_spend' );?>
									<?php if (!empty($magic_feat_user_sites)):?>
									<th class="manage-column">
											<?php esc_html_e('Sites', 'ihc');?>
									</th>
									<?php endif;?>
									<th class="manage-column">
										<?php esc_html_e('WP Member Role', 'ihc');?>
									</th>
									<th class="manage-column">
										<?php esc_html_e('Email Status', 'ihc');?>
									</th>
									<th class="manage-column">
										<?php esc_html_e('Sign up date', 'ihc');?>
									</th>
									<th class="manage-column">
										<?php esc_html_e('Details', 'ihc');?>
									</th>
								</tr>
							</tfoot>
						   </table>
						 </div>
						   <div class="ihc-users-list-del-btn">
						   		<input type="submit" value="<?php esc_html_e('Delete', 'ihc');?>" name="delete" onClick="event.preventDefault();ihcFirstConfirmBeforeSubmitForm('<?php esc_html_e('Are You Sure You want to delete selected Members?');?>');" class="button button-primary button-large ihc-remove-group-button"/>
						   </div>
						<?php
					}else{ ?>
					<div  class="ihc-warning-message"><?php esc_html_e('No Members Available.', 'ihc');?></div>
					<?php }
				?>
			</div>
			<input type="hidden" name="ihc_du" value="<?php echo wp_create_nonce( 'ihc_delete_users' );?>" />
		</form>
	</div>
</div>
<div class="clear"></div>

<?php if ( !empty( $userIds ) ):?>
	<span class="ihc-js-users-list-users-spent-values" data-value="<?php echo esc_attr(implode(',', $userIds));?>"></span>
<?php endif;?>
<?php
}
