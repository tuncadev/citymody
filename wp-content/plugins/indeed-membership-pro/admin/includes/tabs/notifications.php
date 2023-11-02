<?php
echo ihc_inside_dashboard_error_license();
echo ihc_check_default_pages_set();
echo ihc_check_payment_gateways();
echo ihc_is_curl_enable();
do_action( "ihc_admin_dashboard_after_top_menu" );
?>
<div class="iump-wrapper">
<?php
$notifications = new \Indeed\Ihc\Notifications();
$notification_arr = $notifications->getAllNotificationNames();

if (isset($_GET['edit_notification']) || isset($_GET['add_notification'])){
	//add/edit

	$notification_id = (isset($_GET['edit_notification'])) ? sanitize_text_field($_GET['edit_notification']) : FALSE;
	$meta_arr = ihc_get_notification_metas($notification_id);


$meta_arr['message'] = stripslashes( htmlspecialchars_decode( $meta_arr['message'] )  );
	?>
	<form method="post" action="<?php echo esc_url($url.'&tab=notifications');?>">

		<input type="hidden" value="<?php echo wp_create_nonce( 'ihc_admin_notifications_nonce' );?>" name="ihc_admin_notifications_nonce" />
		<?php
			if ($notification_id){
				?>
				<input type="hidden" name="notification_id" value="<?php echo esc_attr($notification_id);?>" />
				<?php
			}
		?>
		<div class="ihc-stuffbox">
			<h3><?php esc_html_e('Add new Email Notification', 'ihc');?></h3>
			<div class="inside">
				<div class="iump-form-line">
					<h2><?php esc_html_e('Email Notification Action', 'ihc');?></h2>
					<select name="notification_type" id="notification_type" class="ump-js-change-notification-type iump-form-select ihc-form-element ihc-form-element-select ihc-form-select">
						<?php
							foreach ($notification_arr as $k=>$v){
								//Manually set optGroups
								switch($k){
									case 'admin_user_register':
											echo ' <optgroup label="' . esc_html__('-----Admininistrator Notifications-----', 'ihc') . '"> </optgroup>';
											echo ' <optgroup label="' . esc_html__('Register Process', 'ihc') . '">';
										break;
									case 'ihc_new_subscription_assign_notification-admin':
													echo ' <optgroup label="Subscriptions">';
													break;
									case 'ihc_order_placed_notification-admin':
													echo ' <optgroup label="Payments">';
													break;
									case 'admin_user_profile_update':
													echo ' <optgroup label="Customer Actions">';
													break;


									case 'register':
												  echo ' <optgroup label="' . esc_html__('---------Member Notifications----------', 'ihc') . '"> </optgroup>';
													echo ' <optgroup label="Register Process">';
													break;
									case 'register_lite_send_pass_to_user':
													echo ' <optgroup label="Register Lite">';
													break;
									case 'email_check':
													echo ' <optgroup label="Double Email Verification">';
													break;
									case 'reset_password_process':
													echo ' <optgroup label="Reset Password Process">';
													break;
									case 'approve_account':
													echo ' <optgroup label="Customer Account">';
													break;
									case 'user_update':
													echo ' <optgroup label="Customer Actions">';
													break;
									case 'ihc_new_subscription_assign_notification':
													echo ' <optgroup label="Subscriptions">';
													break;
									case 'ihc_order_placed_notification-user':
													echo ' <optgroup label="Payments">';
													break;
									case 'drip_content-user':
										echo ' <optgroup label="Drip Content">';
										break;
								}
								?>
								<option value="<?php echo esc_attr($k);?>" <?php echo ($meta_arr['notification_type']==$k) ? 'selected' : ''; ?>><?php echo esc_html($v);?></option>
								<?php
								switch($k){
									case 'admin_user_register':
									case 'admin_user_expire_level':
									case 'admin_before_subscription_payment_due':
									case 'ihc_delete_subscription_notification-admin':
										echo ' </optgroup>';
										break;

									case 'review_request':
									case 'register_lite_send_pass_to_user':
									case 'email_check_success':
									case 'change_password':
									case 'delete_account':
									case 'ihc_delete_subscription_notification':
									case 'expire':
									case 'upcoming_card_expiry_reminder':
									case 'drip_content-user':
										echo ' </optgroup>';
										break;
								}
							}
							do_action( 'ihc_admin_notification_type_select_field', $meta_arr['notification_type'] );
						?>
					</select>
					<?php
							$notificationObject = new \Indeed\Ihc\Notifications();
							$notificationPattern = $notificationObject->getNotificationTemplate( $meta_arr['notification_type'] );
							$explanation = isset( $notificationPattern['explanation'] ) ? $notificationPattern['explanation'] : '';
					?>
					<div id="ihc_notification_explanation"><?php echo esc_html($explanation);?></div>
				</div>
				<div class="iump-special-line">
					<h2><?php esc_html_e('Membership Target', 'ihc');?></h2>

					<select name="level_id" class="iump-form-select ihc-form-element ihc-form-element-select ihc-form-select">
						<option value="-1" <?php echo ($meta_arr['level_id']==-1) ? 'selected' : ''; ?>><?php esc_html_e( 'All', 'ihc' );?></option>
						<?php
						$levels = \Indeed\Ihc\Db\Memberships::getAll();
						if ($levels && count($levels)){
							foreach ($levels as $k=>$v){
								?>
									<option value="<?php echo esc_attr($k);?>" <?php echo ($meta_arr['level_id']==$k) ? 'selected' : ''; ?>><?php echo esc_html($v['name']);?></option>
								<?php
							}
						}
						?>
					</select>
					<div class="ihc-notification-edit-available"><?php
						echo esc_html__('Available only for:', 'ihc')
							. ', ' . $notification_arr['register']
							. ', ' . $notification_arr['review_request']
							. ', ' . $notification_arr['before_expire']
							. ', ' . $notification_arr['expire']
							. ', ' . $notification_arr['payment']
							. ', ' . $notification_arr['bank_transfer']
							. ', ' . $notification_arr['admin_user_register']
							. ', ' . $notification_arr['admin_user_expire_level']
							. ', ' . $notification_arr['admin_before_user_expire_level']
							. ', ' . $notification_arr['admin_user_payment']
							. '.';
					;?></div>
				</div>
				<div class="iump-form-line">
					<h2 class="ihc-notification-edit-headline"><?php esc_html_e('Email Subject', 'ihc');?></h2>
					<input type="text" class="ihc-edit-notification-subject iump-form-select ihc-form-element ihc-form-element-select ihc-form-select" name="subject" value="<?php echo esc_attr($meta_arr['subject']);?>" id="notification_subject" />
				</div>
				<div class="iump-form-line">
					<h2 class="ihc-notification-edit-headline"><?php esc_html_e('Email Message', 'ihc');?></h2>
				</div>
				<div class="ihc-notification-edit-message">
					<?php wp_editor( $meta_arr['message'], 'ihc_message', array('textarea_name'=>'message', 'quicktags'=>TRUE) );?>
				</div>
				<div class="ihc-notification-edit-constants">
						<?php	$constants = ihcNotificationConstants( $meta_arr['notification_type'] );?>
						<div class="ump-js-list-constants">
						<?php foreach ($constants as $k=>$v):?>
								<div><?php echo esc_html($k);?></div>
						<?php endforeach;?>
						</div>

						<?php
						$extra_constants = ihc_get_custom_constant_fields();
						?><h4><?php esc_html__('Custom Fields constants', 'ihc');?></h4><?php
						foreach ($extra_constants as $k=>$v){
							?>
							<div><?php echo esc_html($k);?></div>
							<?php
						}
					?>
				</div>

				<div class="ihc-clear"></div>
				<div class="ihc-stuffbox-submit-wrap iump-submit-form">
					<input type="submit"
					value="<?php if ($notification_id){
						esc_html_e('Save Changes', 'ihc');
					} else{
						esc_html_e('Save Changes', 'ihc');
					}?>
					" name="ihc_save" id="ihc_submit_bttn" class="button button-primary button-large ihc_submit_bttn">
				</div>
			</div>
		</div>
				<!-- PUSHOVER -->
				<?php if (ihc_is_magic_feat_active('pushover')):?>
				<div class="ihc-stuffbox ihc-stuffbox-magic-feat">
					<h3><?php esc_html_e('Pushover Mobile Notification', 'ihc');?></h3>
					<div class="inside">
						<div class="iump-form-line">
							<h2><?php esc_html_e('Send Pushover Mobile Notification', 'ihc');?></h2>
							<label class="iump_label_shiwtch ihc-switch-button-margin">
								<?php $checked = (empty($meta_arr['pushover_status'])) ? '' : 'checked';?>
								<input type="checkbox" class="iump-switch" onClick="iumpCheckAndH(this, '#pushover_status');" <?php echo esc_attr($checked);?> />
								<div class="switch ihc-display-inline"></div>
							</label>
							<input type="hidden" name="pushover_status" value="<?php echo (isset($meta_arr['pushover_status'])) ? $meta_arr['pushover_status'] : '';?>" id="pushover_status" />
						</div>

						<div class="iump-form-line">
							<h2><?php esc_html_e('Pushover Message:', 'ihc');?></h2>
							<textarea name="pushover_message" class="ihc-notification-edit-pushmessage" onBlur="ihcCheckFieldLimit(1024, this);"><?php echo stripslashes((isset($meta_arr['pushover_message'])) ? esc_html( $meta_arr['pushover_message'] ) : '');?></textarea>
							<div><?php esc_html_e('Only Plain Text and up to ', 'ihc');?><span>1024</span><?php esc_html_e(' characters are available!', 'ihc');?></div>
						</div>
						<div class="ihc-stuffbox-submit-wrap iump-submit-form">
							<input type="submit"
							value="
							<?php if ($notification_id){
								esc_html_e('Save Changes', 'ihc');
							} else{
								esc_html_e('Save Changes', 'ihc');
							}
							?>
							" name="ihc_save" id="ihc_submit_bttn" class="button button-primary button-large ihc_submit_bttn">
						</div>
					</div>
				</div>
				<?php else :?>
					<input type="hidden" name="pushover_message" value=""/>
					<input type="hidden" name="pushover_status" value=""/>
				<?php endif;?>
				<!-- PUSHOVER -->


	</form>

<?php
} else {
	//listing
	$notificationObject = new \Indeed\Ihc\Notifications();
	$notification_arr = apply_filters( 'ihc_admin_list_notifications_types', $notification_arr );
	if (isset($_POST['ihc_save']) && !empty($_POST['ihc_admin_notifications_nonce']) && wp_verify_nonce( sanitize_text_field($_POST['ihc_admin_notifications_nonce']), 'ihc_admin_notifications_nonce' ) ){
		$notificationObject->save(indeed_sanitize_textarea_array($_POST));
	} else if (isset($_POST['delete_notification_by_id']) && !empty($_POST['ihc_admin_notifications_nonce']) && wp_verify_nonce( sanitize_text_field($_POST['ihc_admin_notifications_nonce']), 'ihc_admin_notifications_nonce' ) ){
		$notificationObject->deleteOne( sanitize_text_field($_POST['delete_notification_by_id']) );
	}
	$data = $notificationObject->getAllNotifications();
	$exclude = apply_filters( 'ihc_admin_remove_notification_from_listing_by_type', [] );
	$runtime = $notificationObject->getNotificationRuntime();
		?>
		<div id="col-right" class="ihc-notification-list-wrapper">
		<div class="iump-page-title">Ultimate Membership Pro -
							<span class="second-text">
								<?php esc_html_e('Notifications', 'ihc');?>
							</span>
						</div>
			<a href="<?php echo esc_url( $url .'&tab=notifications&add_notification=true');?>" class="indeed-add-new-like-wp"><i class="fa-ihc fa-add-ihc"></i><?php esc_html_e('Add New Notification', 'ihc');?></a>
			<span class="ihc-top-message"><?php esc_html_e('...create your notification Templates!', 'ihc');?></span>
			<a href="javascript:void(0)" title="<?php esc_html_e('Let you know if your website is able to send emails independently of UMP settings. A test email should be received on Admin email address.', 'ihc');?>" class="button button-primary button-large ihc-remove-group-button ihc-notification-list-check" onClick="ihcCheckEmailServer();"><?php esc_html_e('Check SMTP Mail Server', 'ihc');?></a>
			<a class="button button-primary button-large ihc-notification-list-check ihc-notification-list-logs" href="<?php echo admin_url( 'admin.php?page=ihc_manage&tab=notification-logs' );?>" target="_blank"><?php esc_html_e( 'Notifications Logs', 'ihc' );?></a>
			<div class="ihc-clear"></div>
			<?php
			if ($data){
			?>
				<form id="delete_notification" method="post" >
						<input type="hidden" value="<?php echo wp_create_nonce( 'ihc_admin_notifications_nonce' );?>" name="ihc_admin_notifications_nonce" />
						<input type="hidden" value="" id="delete_notification_by_id" name="delete_notification_by_id"/>
				</form>
				<div class="iump-rsp-table">
				<div class="ihc-sortable-table-wrapp">
					<table class="wp-list-table widefat fixed tags ihc-admin-tables" id="ihc-levels-table">
						<thead>
							<tr>
								<th class="manage-column ihc-notification-table-subject"><?php esc_html_e('Subject', 'ihc');?></th>
								<th class="manage-column ihc-notification-table-action"><?php esc_html_e('Action', 'ihc');?></th>
								<th class="manage-column"><?php esc_html_e('Goes to', 'ihc');?></th>
								<th class="manage-column"><?php esc_html_e('RunTime', 'ihc');?></th>
								<th class="manage-column ihc-text-center"><?php esc_html_e('Memberships Target', 'ihc');?></th>
								<?php if (ihc_is_magic_feat_active('pushover')):?>
									<th class="manage-column ihc-text-center"><?php esc_html_e('Mobile Notification', 'ihc');?></th>
								<?php endif;?>
								<th class="manage-column" ><?php esc_html_e('Options', 'ihc');?></th>
							</tr>
						</thead>

						<tfoot>
							<tr>
								<th class="manage-column"><?php esc_html_e('Subject', 'ihc');?></th>
								<th class="manage-column"><?php esc_html_e('Action', 'ihc');?></th>
								<th class="manage-column"><?php esc_html_e('Goes to', 'ihc');?></th>
								<th class="manage-column"><?php esc_html_e('RunTime', 'ihc');?></th>
								<th class="manage-column ihc-text-center"><?php esc_html_e('Memberships Target', 'ihc');?></th>
								<?php if (ihc_is_magic_feat_active('pushover')):?>
									<th class="manage-column ihc-text-center"><?php esc_html_e('Mobile Notification', 'ihc');?></th>
								<?php endif;?>
								<th class="manage-column" ><?php esc_html_e('Actions', 'ihc');?></th>
							</tr>
						</tfoot>

						<tbody class="ui-sortable">
							<?php
								$notificationObject = new \Indeed\Ihc\Notifications();
								$admin_actions = $notificationObject->getAdminCases();
								foreach ($data as $item){
									if ( $exclude && in_array( $item->notification_type, $exclude ) ) {
										continue;
									}
								?>
								<tr onmouseover="ihcDhSelector('#notify_tr_<?php echo esc_attr($item->id);?>', 1);" onmouseout="ihcDhSelector('#notify_tr_<?php echo esc_attr($item->id);?>', 0);">
									<td><?php
										if (strlen($item->subject)>100){
											echo esc_html(substr($item->subject, 0, 100) . ' ...');
										} else {
											echo esc_html($item->subject);
										}

										?>
										<div class ="ihc-buttons-rsp ihc-visibility-hidden" id="notify_tr_<?php echo esc_attr($item->id);?>">
											<a class ="iump-btns" href="<?php echo esc_url($url.'&tab=notifications&edit_notification='.$item->id);?>"><?php esc_html_e('Edit', 'ihc');?></a> |
											<span class ="iump-btns ihc-delete-link ihc-js-admin-notifications-delete-notification" data-id="<?php echo esc_attr($item->id);?>" ><?php esc_html_e('Delete', 'ihc');?></span>
										</div>
									</td>
									<td class="ihc-highlighted-label"><?php
										echo isset( $notification_arr[$item->notification_type] ) ? esc_html($notification_arr[$item->notification_type]) : '';
									?></td>


									<td><?php
										if (in_array($item->notification_type, $admin_actions)){
											echo 'Administrators';
										} else {
											echo 'Member';
										}
									?></td>
									<td><?php echo isset( $runtime[$item->notification_type] ) ? esc_html($runtime[$item->notification_type]) : '';?></td>
									<td class="ihc-text-center"><?php
										if ($item->level_id==-1){
											echo 'All';
										} else {
											$level_data = ihc_get_level_by_id($item->level_id);
											echo esc_html($level_data['name']);
										}
									?></td>


									<?php if (ihc_is_magic_feat_active('pushover')):?>
										<td class="ihc-text-center">
											<?php if (!empty($item->pushover_status)):?>
												<i class="fa-ihc fa-pushover-on-ihc"></i>
											<?php endif;?>
										</td>
									<?php endif;?>

									<td>
											<div class="ihc-js-notifications-fire-notification-test ihc-notifications-list-send"
														data-notification_id="<?php echo esc_attr($item->id);?>"
														data-email="<?php echo esc_url(get_option( 'admin_email' ));?>"
											><?php esc_html_e('Send Test Email', 'ihc');?></div>
									</td>
								</tr>
							<?php
								}
							?>
						</tbody>
					</table>
				</div>
			</div>
				<?php
				}
				?>

		</div>

<?php
}
?>
</div>
<?php
