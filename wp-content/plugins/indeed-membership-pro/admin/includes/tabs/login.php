<?php $subtab = isset( $_GET['subtab'] ) ? sanitize_text_field($_GET['subtab']) : 'design';?>
<div class="ihc-subtab-menu">
	<a class="ihc-subtab-menu-item <?php echo ($subtab =='design') ? 'ihc-subtab-selected' : '';?>" href="<?php echo esc_url( $url . '&tab=' . $tab . '&subtab=design' );?>"><?php esc_html_e('Login Form Showcase', 'ihc');?></a>
	<a class="ihc-subtab-menu-item <?php echo ($subtab =='msg') ? 'ihc-subtab-selected' : '';?>" href="<?php echo esc_url( $url . '&tab=' . $tab . '&subtab=msg' );?>"><?php esc_html_e('Custom Messages', 'ihc');?></a>
	<div class="ihc-clear"></div>
</div>
<?php
	echo ihc_inside_dashboard_error_license();
	//set default pages message
	echo ihc_check_default_pages_set();
	echo ihc_check_payment_gateways();
	echo ihc_is_curl_enable();
	do_action( "ihc_admin_dashboard_after_top_menu" );
	$login_templates = array(
							  13 => '(#13) '.esc_html__('Ultimate Member', 'ihc'),
							  12 => '(#12) '.esc_html__('MegaBox', 'ihc'),
							  11 => '(#11) '.esc_html__('Flat New Style', 'ihc'),
							  10 => '(#10) '.esc_html__('Simple BootStrap Theme', 'ihc'),
							  9 => '(#9) '.esc_html__('Radius Gradient Theme', 'ihc'),
							  8 => '(#8) '.esc_html__('Border Pink Theme', 'ihc'),
							  7 => '(#7) '.esc_html__('Double Long Theme', 'ihc'),
							  6 => '(#6) '.esc_html__('Premium Theme', 'ihc'),
							  5 => '(#5) '.esc_html__('Labels Theme', 'ihc'),
							  4 =>  '(#4) '.esc_html__('Simple Green Theme', 'ihc'),
							  3 => '(#3) '.esc_html__('BlueBox Theme', 'ihc'),
							  2 =>'(#2) '.esc_html__('Basic Theme', 'ihc'),
							  1 => '(#1) '.esc_html__('Standard Theme', 'ihc')
							  );


	if ($subtab=='design'){
		if ( isset($_POST['ihc_save'] ) && !empty($_POST['ihc_admin_login_settings_nonce']) && wp_verify_nonce( sanitize_text_field($_POST['ihc_admin_login_settings_nonce']), 'ihc_admin_login_settings_nonce' ) ){
				ihc_save_update_metas('login');
		}

		$meta_arr = ihc_return_meta_arr('login');
		?>
		<div class="iump-page-title">Ultimate Membership Pro -
							<span class="second-text">
								<?php esc_html_e('Login Form', 'ihc');?>
							</span>
						</div>
			<div class="ihc-stuffbox">
				<div class="impu-shortcode-display">
					[ihc-login-form]
				</div>
			</div>
			<form  method="post" >
				<input type="hidden" name="ihc_admin_login_settings_nonce" value="<?php echo wp_create_nonce( 'ihc_admin_login_settings_nonce' );?>" />
				<div class="ihc-login-showcase-sectionone1">
					<div class="ihc-stuffbox">
						<h3><?php esc_html_e('Login Form Display', 'ihc');?></h3>
						<div class="inside">
						  <div class="iump-register-select-template">
						  <?php esc_html_e('Login Form Template:', 'ihc');?>
							<select name="ihc_login_template" id="ihc_login_template" onChange="ihcLoginPreview();" class="ihc_profile_form_template-st">
							<?php
								foreach ($login_templates as $k=>$value){
									echo '<option value="ihc-login-template-'.$k.'"'. ($meta_arr['ihc_login_template']=='ihc-login-template-'.$k ? 'selected': '') .'>'.$value.'</option>';
								}
							?>
							</select>
						 </div>
						 <div>
							<div id="ihc-preview-login"></div>
						</div>
							<div class="ihc-wrapp-submit-bttn">
								<input type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large ihc_submit_bttn" />
							</div>
						</div>
					</div>


				</div>
			   <div class="ihc-login-showcase-sectiontwo2">
				<div class="ihc-stuffbox">
					<h3><?php esc_html_e('Additional Options', 'ihc');?></h3>
					<div class="inside">
							<div class="iump-form-line iump-no-border">
									<input type="checkbox" onClick="checkAndH(this, '#ihc_login_remember_me');ihcLoginPreview();" <?php if($meta_arr['ihc_login_remember_me']==1){
										 echo 'checked';
									}
									?>
									/>
									<input type="hidden" name="ihc_login_remember_me" value="<?php echo esc_attr($meta_arr['ihc_login_remember_me']);?>" id="ihc_login_remember_me"/>
									<span><strong><?php esc_html_e('Remember Me', 'ihc');?></strong></span>
							</div>
							<div class="iump-form-line iump-no-border">
									<input type="checkbox" onClick="checkAndH(this, '#ihc_login_register');ihcLoginPreview();" <?php if($meta_arr['ihc_login_register']==1){
										 echo 'checked';
									}
									?>
									/>
									<input type="hidden" name="ihc_login_register" value="<?php echo esc_attr($meta_arr['ihc_login_register']);?>" id="ihc_login_register"/>
									<span><strong><?php esc_html_e('Register Link', 'ihc');?></strong></span>
							</div>
							<div class="iump-form-line iump-no-border">
									<input type="checkbox" onClick="checkAndH(this, '#ihc_login_pass_lost');ihcLoginPreview();" <?php if($meta_arr['ihc_login_pass_lost']==1){
										 echo 'checked';
									}
									?>
									/>
									<span><strong><?php esc_html_e('Lost your password', 'ihc');?></strong></span>
									<input type="hidden" name="ihc_login_pass_lost" value="<?php echo esc_attr($meta_arr['ihc_login_pass_lost']);?>" id="ihc_login_pass_lost"/>
							</div>
							<div class="iump-form-line iump-no-border">
									<input type="checkbox" onClick="checkAndH(this, '#ihc_login_show_sm');ihcLoginPreview();" <?php if ($meta_arr['ihc_login_show_sm']==1){
										 echo 'checked';
									}
									?>
									/>
									<span><strong><?php esc_html_e('Show Social Media Login Buttons', 'ihc');?></strong></span>
									<input type="hidden" name="ihc_login_show_sm" value="<?php echo esc_attr($meta_arr['ihc_login_show_sm']);?>" id="ihc_login_show_sm"/>
							</div>
							<div class="iump-form-line iump-no-border">
									<input type="checkbox" onClick="checkAndH(this, '#ihc_login_show_recaptcha');ihcLoginPreview();" <?php if ($meta_arr['ihc_login_show_recaptcha']==1){
										 echo 'checked';
									}
									?>
									/>
									<span><strong><?php esc_html_e('Show ReCaptcha', 'ihc');?></strong></span>
									<input type="hidden" name="ihc_login_show_recaptcha" value="<?php echo esc_attr($meta_arr['ihc_login_show_recaptcha']);?>" id="ihc_login_show_recaptcha"/>
							</div>
							<div class="ihc-wrapp-submit-bttn">
								<input type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large ihc_submit_bttn" />
							</div>
						</div>
				  </div>
				  <div class="ihc-stuffbox">
						<h3><?php esc_html_e('Additional Custom CSS', 'ihc');?></h3>
						<div class="inside">
							<textarea id="ihc_login_custom_css" name="ihc_login_custom_css" onBlur="ihcLoginPreview();" class="ihc-dashboard-textarea-full"><?php echo stripslashes($meta_arr['ihc_login_custom_css']);?></textarea>
							<div class="ihc-wrapp-submit-bttn">
								<input type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large ihc_submit_bttn" />
							</div>
						</div>
					</div>
				</div>
			</form>
		<?php
	} else {
		if ( isset($_POST['ihc_save'] ) && !empty($_POST['ihc_admin_login_settings_nonce']) && wp_verify_nonce( sanitize_text_field($_POST['ihc_admin_login_settings_nonce']), 'ihc_admin_login_settings_nonce' ) ){
				ihc_save_update_metas('login-messages');
		}
		$meta_arr = ihc_return_meta_arr('login-messages');
		?>
			<form  method="post" >
				<input type="hidden" name="ihc_admin_login_settings_nonce" value="<?php echo wp_create_nonce( 'ihc_admin_login_settings_nonce' );?>" />
				<div class="ihc-stuffbox">
					<h3><?php esc_html_e('Custom Message:', 'ihc');?></h3>
					<div class="inside">
						<div class="ihc-custom-messages-sectionone">
							<h4><?php esc_html_e('Login Form Messages:', 'ihc');?></h4>
							<div>
								<div class="iump-labels-special"><?php esc_html_e('Successfully Message:', 'ihc');?></div>
								<textarea name="ihc_login_succes" class="ihc-dashboard-textarea"><?php echo ihc_correct_text($meta_arr['ihc_login_succes']);?></textarea>
							</div>
							<div>
								<div class="iump-labels-special"><?php esc_html_e('Default message for pending users:', 'ihc');?></div>
								<textarea name="ihc_login_pending" class="ihc-dashboard-textarea"><?php echo ihc_correct_text($meta_arr['ihc_login_pending']);?></textarea>
							</div>
							<div>
								<div class="iump-labels-special"><?php esc_html_e('Default message for error on social login', 'ihc');?></div>
								<textarea name="ihc_social_login_failed" class="ihc-dashboard-textarea"><?php echo ihc_correct_text($meta_arr['ihc_social_login_failed']);?></textarea>
							</div>
							<div>
								<div class="iump-labels-special"><?php esc_html_e('Error Message:', 'ihc');?></div>
								<textarea name="ihc_login_error" class="ihc-dashboard-textarea"><?php echo ihc_correct_text($meta_arr['ihc_login_error']);?></textarea>
							</div>
							<div>
								<div class="iump-labels-special"><?php esc_html_e('E-mail Pending:', 'ihc');?></div>
								<textarea name="ihc_login_error_email_pending" class="ihc-dashboard-textarea"><?php echo ihc_correct_text($meta_arr['ihc_login_error_email_pending']);?></textarea>
							</div>
						</div>

						<div class="ihc-custom-messages-sectiontwo">
							<h4><?php esc_html_e('Reset Password Messages:', 'ihc');?></h4>
							<div>
								<div class="iump-labels-special"><?php esc_html_e('Successfully Message:', 'ihc');?></div>
								<textarea name="ihc_reset_msg_pass_ok" class="ihc-dashboard-textarea"><?php echo ihc_correct_text($meta_arr['ihc_reset_msg_pass_ok']);?></textarea>
							</div>

							<div>
								<div class="iump-labels-special"><?php esc_html_e('Error Message:', 'ihc');?></div>
								<textarea name="ihc_reset_msg_pass_err" class="ihc-dashboard-textarea"><?php echo ihc_correct_text($meta_arr['ihc_reset_msg_pass_err']);?></textarea>
							</div>

							<div>
								<div class="iump-labels-special"><?php esc_html_e('ReCaptcha Error:', 'ihc');?></div>
								<textarea name="ihc_login_error_on_captcha" class="ihc-dashboard-textarea"><?php echo ihc_correct_text($meta_arr['ihc_login_error_on_captcha']);?></textarea>
							</div>

							<div>
								<div class="iump-labels-special"><?php esc_html_e('Ajax Error Message:', 'ihc');?></div>
								<textarea name="ihc_login_error_ajax" class="ihc-dashboard-textarea"><?php echo ihc_correct_text($meta_arr['ihc_login_error_ajax']);?></textarea>
							</div>

						</div>

						<div class="ihc-wrapp-submit-bttn">
							<input type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large ihc_submit_bttn" />
						</div>
					</div>
				</div>
			</form>
		<?php
	}
?>
