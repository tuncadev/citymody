<?php
ihc_delete_template();//DELETE
if (isset($_POST['ihc_bttn']) && !empty( $_POST['ihc_admin_locker_nonce'] ) && wp_verify_nonce( sanitize_text_field($_POST['ihc_admin_locker_nonce']), 'ihc_admin_locker_nonce' )){
    Ihc_Db::save_update_locker_template( indeed_sanitize_textarea_array($_POST) );//SAVE, UPDATE
}
$subtab = isset( $_REQUEST['subtab'] ) ? sanitize_text_field($_REQUEST['subtab'])  : 'lockers_list';
?>
<div class="ihc-subtab-menu">
	<a class="ihc-subtab-menu-item <?php echo ( $subtab =='add_new') ? 'ihc-subtab-selected' : '';?>" href="<?php echo esc_url($url . '&tab=' . $tab . '&subtab=add_new');?>"><?php esc_html_e('Add New Locker Template', 'ihc');?></a>
	<a class="ihc-subtab-menu-item <?php echo ( $subtab =='lockers_list' ) ? 'ihc-subtab-selected' : '';?>" href="<?php echo esc_url($url.'&tab='.$tab.'&subtab=lockers_list');?>"><?php esc_html_e('Manage Lockers', 'ihc');?></a>
	<div class="ihc-clear"></div>
</div>
<?php
	echo ihc_inside_dashboard_error_license();
	echo ihc_check_default_pages_set();//set default pages message
	echo ihc_check_payment_gateways();
	echo ihc_is_curl_enable();
	do_action( "ihc_admin_dashboard_after_top_menu" );
?>
<div class="iump-wrapper">
<div class="ihc-dashboard-form-wrap">
<?php

	$subtab = 'lockers_list';
	if (isset($_REQUEST['subtab'])){
		$subtab = sanitize_text_field($_REQUEST['subtab']);
	}
	if ($subtab=='add_new'){
		if (isset($_REQUEST['ihc_edit_id']) && $_REQUEST['ihc_edit_id']){
			//edit
			$meta_arr = ihc_return_meta('ihc_lockers', sanitize_text_field($_REQUEST['ihc_edit_id']) );
		} else {
			//new
			$meta_arr = ihc_locker_meta_keys();
		}

		///////////////////// ADD NEW/edit SETION
		?>
			<form method="post" action="<?php echo esc_url($url.'&tab='.$tab.'&subtab=lockers_list');?>">
				<?php
					if(isset($_REQUEST['ihc_edit_id']) && $_REQUEST['ihc_edit_id']!=''){
						echo '<input type="hidden" value="' . sanitize_text_field($_REQUEST['ihc_edit_id']) . '" name="template_id" />';//for update
					}
				?>

				<input type="hidden" name="ihc_admin_locker_nonce" value="<?php echo wp_create_nonce( 'ihc_admin_locker_nonce' );?>" />

				<div class="ihc-stuffbox">
					<h3><?php esc_html_e('Add New Inside Locker', 'ihc');?></h3>
					<div class="inside">
						<div class="iump-form-line iump-no-border">
							<h2><?php esc_html_e('Inside Locker Template Name', 'ihc');?></h2>
							<p><?php esc_html_e('Name of the Locker Template will be used for Administration purpose only when you will use a such template to restrict partial content inside WordPress Pages.', 'ihc');?></p>
							<div class="row">
                	<div class="col-xs-4">
                             <div class="input-group">
                                <span class="input-group-addon"><?php esc_html_e('Template Name', 'ihc');?></span>
                                <input class="form-control" type="text" value="<?php echo esc_attr($meta_arr['ihc_locker_name']);?>" name="ihc_locker_name" placeholder="<?php esc_html_e('suggestive Locker Template Name', 'ihc');?>">
                             </div>
                     </div>
                 </div>
						</div>

						<div class="iump-special-line">
							<h2><?php esc_html_e('Inside Locker Theme', 'ihc');?></h2>
							<p><?php esc_html_e('Choose the best Theme for your website or particular page. You can customize it further with using Custom CSS Box.', 'ihc');?></p>
							<?php
								$templates = array(1=>'Default', 2=>'Basic', 3=>'Zipped', 4=>'Zone', 5=>'Majic Transparent', 6=>'Star', 7=>'Clouddy', 8=>'Darks');
							?>
							<select name="ihc_locker_template" id="ihc_locker_template" onChange="setAddVal(this, '#ihc_locker_login_template');ihcLockerPreview();" class="ihc_profile_form_template-st">
								<?php
									foreach($templates as $k=>$v){
										?>
											<option value="<?php echo esc_attr($k);?>" <?php if($k==$meta_arr['ihc_locker_template']){
												echo 'selected';
											}
											?> >
												<?php echo esc_html($v);?>
											</option>
										<?php
									}
								?>
							</select>
							<input type="hidden" id="ihc_locker_login_template" name="ihc_locker_login_template" value="<?php echo esc_attr($meta_arr['ihc_locker_login_template']);?>" />
						</div>

						<div class="iump-form-line iump-no-border">
							<h2><?php esc_html_e('Additional Display Options', 'ihc');?></h2>
							<p><?php esc_html_e('Choose what options will be available inside Locker box for who has no access to the content. Uncheck all if you wish just to hide the content from Page.', 'ihc');?></p>
						</div>
						<div class="iump-form-line iump-no-border">
							<input type="checkbox" onClick="checkAndH(this, '#ihc_locker_login_form');ihcLockerPreview();" <?php if($meta_arr['ihc_locker_login_form']==1){
								echo 'checked';
							}?>
							/>
							<strong><?php esc_html_e('Login Form', 'ihc');?></strong>
							<input type="hidden" id="ihc_locker_login_form" name="ihc_locker_login_form" value="<?php echo esc_attr($meta_arr['ihc_locker_login_form']);?>" />
						</div>
						<div class="iump-form-line iump-no-border">
							<input type="checkbox" onClick="checkAndH(this, '#ihc_locker_additional_links');ihcLockerPreview();" <?php if($meta_arr['ihc_locker_additional_links']==1){
								echo 'checked';
							}
							?>
							/><strong><?php esc_html_e('Additional Links', 'ihc');?></strong>
							<input type="hidden" id="ihc_locker_additional_links" name="ihc_locker_additional_links" value="<?php echo esc_attr($meta_arr['ihc_locker_additional_links']);?>" />
						</div>
						<div class="iump-form-line iump-no-border">
							<input type="checkbox" onClick="checkAndH(this, '#ihc_locker_display_sm');ihcLockerPreview();" <?php if ($meta_arr['ihc_locker_display_sm']==1){
								 echo 'checked';
							}
							?>
							/><strong><?php esc_html_e('Display Social Media Login', 'ihc');?></strong>
							<input type="hidden" id="ihc_locker_display_sm" name="ihc_locker_display_sm" value="<?php echo (isset($meta_arr['ihc_locker_display_sm'])) ? $meta_arr['ihc_locker_display_sm']  : '';?>" />
						</div>

						<div class="iump-form-line iump-no-border">
							<h2><?php esc_html_e('Inside Locker Messsage', 'ihc');?></h2>
							<p><?php esc_html_e('This Message will show up on the top of Locker Box. You can inform members why this content is restrict and what they should do to access it.', 'ihc');?></p>
							<?php
								$settings = array(
										'media_buttons' => true,
										'textarea_name' => 'ihc_locker_custom_content',
										'textarea_rows' => 5,
										'tinymce' => true,
										'quicktags' => true,
										'teeny' => true,
								);
								$meta_arr['ihc_locker_custom_content'] = ihc_correct_text($meta_arr['ihc_locker_custom_content']);
								wp_editor( $meta_arr['ihc_locker_custom_content'], 'ihc_locker_custom_content', $settings );
							?>

						</div>

						<div class="iump-form-line">
								<input type="button" onClick="ihcUpdateTextarea()" id="ihc-update-bttn-show-edit" value="<?php esc_html_e('Update Message', 'ihc');?>" class="button button-primary button-large ihc-notification-list-logs ihc-display-none"/>
						</div>
						<div class="ihc-wrapp-submit-bttn ihc-stuffbox-submit-wrap">
							<input id="ihc_submit_bttn_locker" type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_bttn" class="button button-primary button-large" />
						</div>
					</div>
				</div>

				<div class="ihc-stuffbox">
					<h3><?php esc_html_e('Locker Preview', 'ihc');?></h3>
					<div class="inside">
						<div id="locker-preview"></div>
					</div>
				</div>

				<div class="ihc-stuffbox">
					<h3><?php esc_html_e('Custom CSS', 'ihc');?></h3>
					<div class="inside">
						<textarea id="ihc_locker_custom_css" name="ihc_locker_custom_css" onBlur="ihcLockerPreview();" class="ihc-dashboard-textarea-full"><?php echo stripslashes($meta_arr['ihc_locker_custom_css']);?></textarea>
						<div class="ihc-wrapp-submit-bttn">
							<input id="ihc_submit_bttn" type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_bttn" class="button button-primary button-large" />
						</div>
					</div>
				</div>

			</form>

		<?php
	}else{
		?>
		<div class="clear"></div>

		<div class="iump-page-title">Ultimate Membership Pro -
							<span class="second-text">
								<?php esc_html_e('Inside Lockers', 'ihc');?>
							</span>
						</div>
		<a href="<?php echo esc_url($url.'&tab='.$tab.'&subtab=add_new');?>" class="indeed-add-new-like-wp">
			<i class="fa-ihc fa-add-ihc"></i><?php esc_html_e('Add New Locker Template', 'ihc');?>
		</a>
		<span class="ihc-top-message"><?php esc_html_e('...create Locker templates for further use!', 'ihc');?></span>

		<div class="clear"></div>
		<?php
		////////////////// LIST LOCKER
		$templates = ihc_return_meta('ihc_lockers');
		if($templates){
		?>

			<div class="ihc-manage-templates">
				<div>
						<table class="wp-list-table widefat fixed tags ihc-admin-tables">
						    <thead>
						        <tr>
									<th class="manage-column" width="30px"><?php esc_html_e('ID', 'ihc');?></th>
									<th class="manage-column"><?php esc_html_e('Name', 'ihc');?></th>
									<th class="manage-column"><?php esc_html_e('Theme', 'ihc');?></th>
									<th class="manage-column"><?php esc_html_e('Edit', 'ihc');?></th>
									<th class="manage-column"><?php esc_html_e('Preview', 'ihc');?></th>
									<th class="manage-column"><?php esc_html_e('Remove', 'ihc');?></th>
								</tr>
							</thead>
						    <tfoot>
						        <tr>
									<th class="manage-column"><?php esc_html_e('ID', 'ihc');?></th>
									<th class="manage-column"><?php esc_html_e('Name', 'ihc');?></th>
									<th class="manage-column"><?php esc_html_e('Theme', 'ihc');?></th>
									<th class="manage-column"><?php esc_html_e('Edit', 'ihc');?></th>
									<th class="manage-column"><?php esc_html_e('Preview', 'ihc');?></th>
									<th class="manage-column"><?php esc_html_e('Rremove', 'ihc');?></th>
						        </tr>
						    </tfoot>
						<?php
						$i= 1;
						foreach($templates as $k=>$v){
							?>
							<tr class="<?php if($i%2==0){
								 echo 'alternate';
							}
							?>
							">
								<td><?php echo esc_html($k);?></td>
								<td class="ihc-highlighted-label">
									<?php
										echo esc_html($v['ihc_locker_name']);
									?>
								</td>
								<td>
								<span class="subcr-type-list">
									<?php
									$templates = array(1=>'Default', 2=>'Basic', 3=>'Zipped', 4=>'Zone', 5=>'Majic Transparent', 6=>'Star', 7=>'Clouddy', 8=>'Darks');
										echo esc_html($templates [$v['ihc_locker_template']]);
									?>
									</span>
								</td>
								<td>
									<a href="<?php echo esc_url($url.'&tab=locker&subtab=add_new&ihc_edit_id='.$k);?>">
										<i class="fa-ihc ihc-icon-edit-e"></i>
									</a>
								</td>
								<td>
									<a href="javascript:void(0)" onClick='ihcLockerPreviewWi(<?php echo esc_attr($k);?>, 1);'>
										<i class="fa-ihc ihc-icon-preview"></i>
									</a>
								</td>
								<td>
									<span class="ihc-js-admin-delete-locker ihc-delete-link" data-id="<?php echo esc_attr($k);?>" >
										<i class="fa-ihc ihc-icon-remove-e"></i>
									</span>
								</td>
							</tr>
							<?php
							$i++;
						}
						?>
						</table>
				</div>
			</div>
			<div id="locker-preview"></div>
		<?php
		}else{
			?>
				<div class="ihc-warning-message"> <?php esc_html_e('No Inside Lockers Templates available! Please create your first Inside Locker.', 'ihc');?></div>
			<?php
		}
	}
?>


</div>
</div>
