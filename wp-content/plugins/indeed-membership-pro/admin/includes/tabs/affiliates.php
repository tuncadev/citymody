<?php
echo ihc_inside_dashboard_error_license();
$is_uap_active = ihc_is_uap_active();
if ($is_uap_active):
?>
<div class="ihc-subtab-menu">
	<a class="ihc-subtab-menu-item" href="<?php echo esc_url($url.'&tab='.$tab.'&subtab=list');?>"><?php esc_html_e('Affiliates', 'ihc');?></a>
	<a class="ihc-subtab-menu-item" href="<?php echo esc_url($url.'&tab='.$tab.'&subtab=options');?>"><?php esc_html_e('Account Page', 'ihc');?></a>
	<div class="ihc-clear"></div>
</div>
<?php endif;?>

<div class="iump-wrapper">
	<?php if ($is_uap_active): ?>
		<div class="ihc-dashboard-title">
			Ultimate Membership Pro -
			<span class="second-text">
				<?php esc_html_e('Affiliates', 'ihc');?>
			</span>
		</div>
	<?php endif; ?>

		<?php if ($is_uap_active):?>
				<?php
				if (empty($_GET['subtab']) || sanitize_text_field($_GET['subtab'])=='list'):
					////////////////////////////////////// LISTING ///////////////////////////////////////
				$limit = (isset($_REQUEST['ihc_limit'])) ? sanitize_text_field($_REQUEST['ihc_limit']) : 25;
				$start = 0;
				if(isset($_REQUEST['ihcdu_page'])){
					$pg = sanitize_text_field($_REQUEST['ihcdu_page']) - 1;
					$start = (int)$pg * $limit;
				}

				$filter_role = '';
                                if(isset($_REQUEST['filter_role'])){
					$filter_role = sanitize_text_field($_REQUEST['filter_role']);
                                }

				$orderby = 'registered';
                                if(isset($_REQUEST['orderby_user'])){
                                    $orderby = sanitize_text_field($_REQUEST['orderby_user']);
                                }


				$ordertype = 'DESC';
                                if(isset($_REQUEST['ordertype_user'])){
                                    $ordertype = sanitize_text_field($_REQUEST['ordertype_user']);
                                }


				$search_term = '';
                                if(isset($_REQUEST['search_user'])){
                                    $search_term = sanitize_text_field($_REQUEST['search_user']);
                                }


					global $wpdb;
					$current_time = indeed_get_unixtimestamp_with_timezone();
					if ($search_term != ''){
						function ihc_pre_user_query($user_query){
							$user_query->query_fields = 'DISTINCT ' . $user_query->query_fields;
						}
						add_action( 'pre_user_query', 'ihc_pre_user_query');
						global $wp_query;
						$users_obj = new WP_User_Query(array(
														    'role' => $filter_role,
															'meta_query' => array(
																'relation' => 'AND',
														        array(
														            'key' => $wpdb->get_blog_prefix() . 'capabilities',
														            'value' => 'administrator',
														            'compare' => 'NOT LIKE'
														        ),
																array(
																	'relation' => 'OR',
																	array(
																		'key'     => 'first_name',
																		'value'   => $search_term,
																		'compare' => 'LIKE'
																	),
																	array(
																		'key'     => 'last_name',
																		'value'   => $search_term,
																		'compare' => 'LIKE'
																	),
																	array(
																		'key' => 'nickname',
																		'value' => $search_term ,
																		'compare' => 'LIKE'
																	)
																)
														    ),
															'offset' => $start,
															'number' => $limit,
															'orderby' => $orderby,
															'order' => $ordertype,
														));

						//////////////////PAGINATION
						$all_users = new WP_User_Query(array(
														    'role' => $filter_role,
															'meta_query' => array(
																'relation' => 'AND',
														        array(
														            'key' => $wpdb->get_blog_prefix() . 'capabilities',
														            'value' => 'administrator',
														            'compare' => 'NOT LIKE'
														        ),
																array(
																		'relation' => 'OR',
																		array(
																			'key'     => 'first_name',
																			'value'   => $search_term,
																			'compare' => 'LIKE'
																		),
																		array(
																			'key'     => 'last_name',
																			'value'   => $search_term,
																			'compare' => 'LIKE'
																		),
																		array(
																			'key' => 'nickname',
																			'value' => $search_term ,
																			'compare' => 'LIKE'
																		)
																	)
														    )
														));
						$users = $users_obj->results;
						$all_users = $all_users->results;
					} else {
						$users_obj = new WP_User_Query(array(
														    'role' => $filter_role,
															'meta_query' => array(
																array(
														            'key' => $wpdb->get_blog_prefix() . 'capabilities',
														            'value' => 'administrator',
														            'compare' => 'NOT LIKE'
														        )

														    ),
															'offset' => $start,
															'number' => $limit,
															'orderby' => $orderby,
															'order' => $ordertype,
														));
						//////////////////PAGINATION
						$users = $users_obj->results;
						$all_users = $users;
						$total_users = Ihc_Db::user_get_count(TRUE);
					}


					//SEARCH FILTER BY USER LEVELS
					if ($start==0){
						 $current_page = 1;
					}else{
						$current_page = sanitize_text_field($_REQUEST['ihcdu_page']);
					}
					if (!isset($total_users)){
						$total_users = count($all_users);
					}

					require_once IHC_PATH . 'classes/Ihc_Pagination.class.php';
					$url  = admin_url('admin.php?page=ihc_manage&tab=affiliates&ihc_limit=' . $limit);
					$pagination_object = new Ihc_Pagination(array(
																'base_url' => $url,
																'param_name' => 'ihcdu_page',
																'total_items' => count($all_users),
																'items_per_page' => $limit,
																'current_page' => $current_page,
					));
					$pagination = $pagination_object->output();

					global $indeed_db;
					if (empty($indeed_db) && defined('UAP_PATH')){
						include UAP_PATH . 'classes/Uap_Db.class.php';
						$indeed_db = new Uap_Db;
					}

					$hidded = 'ihc-display-none';
					if (isset($_REQUEST['search_user']) || isset($_REQUEST['filter_role']) || isset($_REQUEST['ordertype_level']) || isset($_REQUEST['orderby_user']) || isset($_REQUEST['ordertype_user']) ){
						 $hidded ='';
					}

					?>
					<div class="ihc-special-buttons-users">
						<div class="ihc-special-button" onclick="ihcShowHide('.ihc-filters-wrapper');"><i class="fa-ihc fa-export-csv"></i><?php esc_html_e('Add Filters', 'uap');?></div>
						<div class="ihc-clear"></div>
					</div>
					<div class="ihc-filters-wrapper  <?php echo esc_attr($hidded);?>">
						<form method="post" >
							<div class="row-fluid">
								<div class="span4">
									<div class="iump-form-line iump-no-border">
										<input name="search_user" type="text" value="<?php echo (isset($_REQUEST['search_user']) ? $_REQUEST['search_user'] : '') ?>" placeholder="<?php esc_html_e('Search by Name or Username', 'ihc');?>..."/>
									</div>
								</div>
								<div class="span2 ihc-aff-search-wrapper">
									<input type="submit" value="Search" name="search" class="button button-primary button-large">
								</div>
							</div>
						</form>
					</div>
							<div>

								<div class="ihc-aff-perpage-wrapper">
									<strong><?php esc_html_e('Number of Users to Display:', 'ihc');?></strong>
									<select name="ihc_limit" class="ihc-js-admin-affiliates-limit"
										data-url='<?php echo admin_url('admin.php?page=ihc_manage&tab=affiliates&ihc_limit=');?>'
									>
										<?php
											foreach (array(5,25,50,100) as $v){
												?>
													<option value="<?php echo esc_attr($v);?>" <?php if($limit==$v){
														 echo 'selected';
													}
													?> ><?php echo esc_html($v);?></option>
												<?php
											}
										?>
									</select>
								</div>
								<?php echo esc_ump_content($pagination);?>
								<div class="clear"></div>
							</div>

						<table class="wp-list-table widefat fixed tags">
							<thead>
								<tr>
									  <th class="manage-column">
											<?php esc_html_e('Username', 'ihc');?>
									  </th>
									  <th class="manage-column">
											<?php esc_html_e('Name', 'ihc');?>
									  </th>
									  <th class="manage-column">
											<?php esc_html_e('E-mail', 'ihc');?>
									  </th>
									  <th><?php esc_html_e('Affiliate', 'ihc');?></th>
									  <th><?php esc_html_e('Join Date', 'ihc');?></th>
								</tr>
							</thead>
							<tfoot>
								<tr>
									  <th class="manage-column">
											<?php esc_html_e('Username', 'ihc');?>
									  </th>
									  <th class="manage-column">
											<?php esc_html_e('Name', 'ihc');?>
									  </th>
									  <th class="manage-column">
											<?php esc_html_e('E-mail', 'ihc');?>
									  </th>
									  <th><?php esc_html_e('Affiliate', 'ihc');?></th>
									  <th><?php esc_html_e('Join Date', 'ihc');?></th>
								</tr>
							</tfoot>
							  <?php
							  		$i = 1;
							  		foreach ($users as $user){
							  			?>
			    						   		<tr id="<?php echo esc_attr("ihc_user_id_" . $user->data->ID);?>" class="<?php if($i%2==0){
															 echo 'alternate';
														}
														?>" onMouseOver="ihcDhSelector('#user_tr_<?php echo esc_attr($user->data->ID);?>', 1);" onMouseOut="ihcDhSelector('#user_tr_<?php echo esc_attr($user->data->ID);?>', 0);">
			    						   			<td>
														<?php echo esc_attr($user->data->user_login);?>
			    						   			</td>
			    						   			<td class="ihc-aff-affname">
			    						   				<?php
			    						   					$first_name = get_user_meta($user->data->ID, 'first_name', true);
			    						   					$last_name = get_user_meta($user->data->ID, 'last_name', true);
			    						   					if ($first_name || $last_name){
			    						   						echo esc_html($first_name) .' '.esc_html($last_name);
			    						   					} else {
			    						   						echo esc_html($user->data->user_nicename);
			    						   					}
			    						   				?>
			    						   			</td>
			    						   			<td>
			    						   				<?php echo esc_html($user->user_email);?>
			    						   			</td>
													<td>
														<div>
															<label class="iump_label_shiwtch-uap-affiliate">
																<?php
																	$uid = $user->data->ID;
																	$checked = (!empty($indeed_db) && $indeed_db->is_user_affiliate_by_uid($uid)) ? 'checked' : '';
																?>
																<input type="checkbox" class="iump-switch" id="uap_checkbox_<?php echo esc_attr($uid);?>" onClick="ihcChangeUapAffiliate(<?php echo esc_attr($uid);?>);" <?php echo esc_attr($checked);?>/>
																<div class="switch ihc-display-inline"></div>
															</label>
														</div>
													</td>
			    						   			<td>
			    						   				<?php
			    						   					echo esc_html($user->user_registered);
			    						   				?>
			    						   			</td>
			    						   		</tr>
							  			<?php
							  			$i++;
							  		}
							  ?>
						</table>
				<?php
					else :
						///////////////////////// OPTIONS
						if (!empty( $_POST['ihc_save'] ) ){
							ihc_save_update_metas('affiliate_options');
						}
						$meta_arr = ihc_return_meta_arr('affiliate_options');
				?>
					<form method="post" >
						<div class="ihc-stuffbox">
							<h3><?php esc_html_e('Account Page - Affiliate Options', 'ihc');?></h3>
							<div class="inside">
								<div>
									<span class="iump-labels-onbutton"><?php esc_html_e('Show Tab', 'ihc');?></span>
									<label class="iump_label_shiwtch iump-onbutton">
										<?php $checked = ($meta_arr['ihc_ap_show_aff_tab']) ? 'checked' : ''; ?>
										<input type="checkbox" class="iump-switch" onclick="iumpCheckAndH(this, '#ihc_ap_show_aff_tab');" <?php echo esc_attr($checked);?>>
										<div class="switch  ihc-display-inline"></div>
									</label>
									<input type="hidden" name="ihc_ap_show_aff_tab" id="ihc_ap_show_aff_tab" value="<?php echo esc_attr($meta_arr['ihc_ap_show_aff_tab']);?>" />
								</div>
								<div>
									<span class="iump-labels-onbutton"><?php esc_html_e('Message', 'ihc');?></span>
									<div  class="iump-wp_editor">
										<?php wp_editor(stripslashes($meta_arr['ihc_ap_aff_msg']), 'ihc_ap_aff_msg', array('textarea_name'=>'ihc_ap_aff_msg', 'editor_height'=>200));?>
									</div>
								</div>
								<div><?php echo esc_html__("You can add 'Become Button' with the following shortcode: ", 'ihc') . '<b>[uap-user-become-affiliate]</b>';?></div>
								<div class="ihc-submit-form">
									<input type="submit" value="Save Changes" name="ihc_save" class="button button-primary button-large">
								</div>
							</div>
						</div>
					</form>
				<?php endif;?>

		<?php else:?>
		<div class="metabox-holder indeed">
		<div class="ihc-stuffbox ihc-aff-message">
			<div class="ihc-warning-box">
					To get this section Available <a href="https://wpindeed.com/ultimate-affiliate-pro" target="_blank">Ultimate Affiliate Pro</a> Plugin needs to be activated on your WordPress website.
			</div>
			<div class="ihc-aff-message-name">Ultimate Affiliate Pro </div>
			<div class="ihc-aff-message-title">The most Complete Affiliate Program Plugin for WordPress</div>
			<div class="ihc-aff-message-description">
				<p><strong>Ultimate Affiliate Pro</strong> is the newest and most completed Affiliate WordPress Plugin that allow you provide a premium platform for your Affiliates with different rewards and amounts based on Ranks or special Offers.</p>
				<p>You can turn on your Website into a REAL business and an income machine where you just need to sit down and let the others to work for you!</p>
				<p>Each Affiliate can creates his own marketing Campaign and brings more Affiliates via the <strong>“Multi-Level-Marketing”</strong> strategy.</p>

				<div><a href="https://wpindeed.com/ultimate-affiliate-pro" target="_blank"  id="ihc_submit_bttn">Get Ultimate Affiliate Pro Now</a></div>
			</div>
			<div class="ihc-aff-message-additional">
				<a href="https://wpindeed.com/ultimate-affiliate-pro" target="_blank">
					<img src="<?php echo IHC_URL;?>admin/assets/images/uap-image-preview.jpg" class="ihc-display-block"/>
				</a>
				<a href="https://wpindeed.com/ultimate-affiliate-pro" target="_blank">
					<img src="<?php echo IHC_URL;?>admin/assets/images/uap_prev1.png" class="ihc-display-block"/>
				</a>
				<a href="https://wpindeed.com/ultimate-affiliate-pro" target="_blank">
					<img src="<?php echo IHC_URL;?>admin/assets/images/uap_prev2.png" class="ihc-display-block"/>
				</a>
				<a href="https://wpindeed.com/ultimate-affiliate-pro" target="_blank">
					<img src="<?php echo IHC_URL;?>admin/assets/images/uap_prev3.png" class="ihc-display-block"/>
				</a>
				<a href="https://wpindeed.com/ultimate-affiliate-pro" target="_blank">
					<img src="<?php echo IHC_URL;?>admin/assets/images/uap_prev4.png" class="ihc-display-block"/>
				</a>
				<a href="https://wpindeed.com/ultimate-affiliate-pro" target="_blank">
					<img src="<?php echo IHC_URL;?>admin/assets/images/uap_prev5.png" class="ihc-display-block"/>
				</a>

			</div>

		</div>
		</div>
		<?php endif;?>
	<div class="ihc-clear"></div>
</div>
<?php
