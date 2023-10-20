<?php
if ( !empty( $_POST['ihc_save'] ) && isset( $_POST['ihc_membership_card_enable'] ) ){
		if ( empty( $_POST['ihc_membership_card_enable'] ) ){
				\Ihc_Db::deactivateApTab( 'membership_cards' );
		} else {
				\Ihc_Db::activateApTab( 'membership_cards' );
		}
}
ihc_save_update_metas('ihc_membership_card');//save update metas
$data['metas'] = ihc_return_meta_arr('ihc_membership_card');//getting metas
echo ihc_check_default_pages_set();//set default pages message
echo ihc_check_payment_gateways();
echo ihc_is_curl_enable();
do_action( "ihc_admin_dashboard_after_top_menu" );

$levels = \Indeed\Ihc\Db\Memberships::getAll();

$extra_fields = array();
$reg_fields = ihc_get_user_reg_fields();

$exclude_names = array('tos', 'ihc_coupon', 'confirm_email','ihc_optin_accept','ihc_memberlist_accept','first_name','last_name');
$exclude_tyes = array('password','ihc_country','ihc_state','upload_image','capcha','social_media','ihc_invitation_code_field','ihc_dynamic_price','payment_select','file','plain_text','conditional_text');

foreach ($reg_fields as $k=>$v){
if (!in_array($v['name'], $exclude_names) && !in_array($v['type'], $exclude_tyes)){
	if (isset($v['native_wp']) && $v['native_wp']){
		$extra_fields[$v['name']] = esc_html__($v['label'], 'ihc');
	} else {
		$extra_fields[$v['name']] = $v['label'];
	}
	if (empty($extra_fields[$v['name']])){
		unset($extra_fields[$v['name']]);
	}
}
}

?>
<form  method="post">
	<div class="ihc-stuffbox">
		<h3 class="ihc-h3"><?php esc_html_e('Membership Card', 'ihc');?></h3>
		<div class="inside">

			<div class="iump-form-line">
				<h2><?php esc_html_e('Activate/Hold Membership Card', 'ihc');?></h2>
				<p><?php esc_html_e('Members will find their Membership Cards into My Account page, under Membership Cards special tab, and may print them for further usage out of the website.', 'ihc');?></p>
				<label class="iump_label_shiwtch ihc-switch-button-margin">
					<?php $checked = ($data['metas']['ihc_membership_card_enable']) ? 'checked' : '';?>
					<input type="checkbox" class="iump-switch" onClick="iumpCheckAndH(this, '#ihc_membership_card_enable');" <?php echo esc_attr($checked);?> />
					<div class="switch ihc-display-inline"></div>
				</label>
				<input type="hidden" name="ihc_membership_card_enable" value="<?php echo esc_attr($data['metas']['ihc_membership_card_enable']);?>" id="ihc_membership_card_enable" />
			</div>
			<div class="iump-form-line">
				<h2><?php esc_html_e('Main Structure Options', 'ihc');?></h2>
			</div>
			<div class="iump-form-line">
				<h4><?php esc_html_e('Select Card Template', 'ihc');?></h4>
				<p><?php esc_html_e('Choose one of predefined templates available for Membership Cards', 'ihc');?></p>
				<select name="ihc_membership_card_template" class="iump-form-select ihc-form-element ihc-form-element-select ihc-form-select">
					<?php
					$templates = array(
										'ihc-membership-card-1' => esc_html__('Template 1', 'ihc'),
										'ihc-membership-card-2' => esc_html__('Template 2', 'ihc'),
										'ihc-membership-card-3' => esc_html__('Template 3', 'ihc'),
					);
					foreach ($templates as $k=>$v):
						?>
						<option value="<?php echo esc_attr($k);?>" <?php if ($k==$data['metas']['ihc_membership_card_template']){
							 echo 'selected';
						}
						?>
						>
						<?php echo esc_html($v);?></option>
						<?php
					endforeach;
				?></select>
			</div>

			<div class="iump-form-line">
				<h4><?php esc_html_e('Background Card Color', 'ihc');?></h4>
				<p><?php esc_html_e('Use the predefined color generated by selected Template or choose a custom one.', 'ihc');?></p>
				<div class="row">
					<div class="col-xs-2">
						<input type="text" class="ihc_membership_card_background_color" name="ihc_membership_card_background_color" value="<?php echo esc_attr($data['metas']['ihc_membership_card_background_color']);?>"/>
						<div>
							<i>ex: #000000</i>
						</div>
					</div>
				</div>

		 </div>

			<div class="iump-form-line">
				<h4><?php esc_html_e('Select Membership Card Size', 'ihc');?></h4>
				<p><?php esc_html_e('Three different predefined sizes are available.', 'ihc');?></p>
				<select name="ihc_membership_card_size" class="iump-form-select ihc-form-element ihc-form-element-select ihc-form-select ihc-js-membership-card-select-size">
					<?php
					$templates = array(
										'ihc-membership-card-small' => esc_html__('Small', 'ihc'),
										'ihc-membership-card-medium' => esc_html__('Medium', 'ihc'),
										'ihc-membership-card-large' => esc_html__('Large', 'ihc'),
					);
					foreach ($templates as $k=>$v):
						?>
						<option value="<?php echo esc_attr($k);?>" <?php if ($k==$data['metas']['ihc_membership_card_size']){
							 echo 'selected';
						}
						?>
						>
						<?php echo esc_html($v);?></option>
						<?php
					endforeach;
					switch ( $data['metas']['ihc_membership_card_size'] ){
							case 'ihc-membership-card-small':
								$imageHref = IHC_URL . 'admin/assets/images/membership-card-size-small.png';
								break;
							case 'ihc-membership-card-medium':
								$imageHref = IHC_URL . 'admin/assets/images/membership-card-size-medium.png';
								break;
							case 'ihc-membership-card-large':
								$imageHref = IHC_URL . 'admin/assets/images/membership-card-size-large.png';
								break;
					}
				?></select>
				<div class="ihc-membership-card-size-image-example-wrapper" data-path_to_image="<?php echo esc_url(IHC_URL  . 'admin/assets/images/' );?>" >
					<img src="<?php echo esc_url($imageHref);?>" class="ihc-membership-card-size-image-example membership-card-size-small"/>
				</div>
			</div>

			<div class="iump-form-line">

			<div class="row">
			<div class="col-xs-5">
				<h4><?php esc_html_e('Membership Card Logo', 'ihc');?></h4>
				<p><?php esc_html_e('Custom Image will show up on Membership Cards', 'ihc');?></p>
				<div class="ihc-membership-card-settings-image-type-box-wrapper">
					<div class="ihc-membership-card-settings-image-type-box">
						<input type="radio" name="ihc-membership-card-settings-image-type" class=" ihc-js-image-type-selector" value="1" <?php echo ($data['metas']['ihc-membership-card-settings-image-type'] === '1') ? 'checked': ''; ?> >
						<label><?php esc_html_e('Company Logo', 'ihc');?></label>
						<img src="<?php echo esc_url(IHC_URL . "assets/images/default-logo1.png");?>" class="ihc-membership-card-settings-image-example"/>
					</div>
					<div class="ihc-membership-card-settings-image-type-box">
						<input type="radio" name="ihc-membership-card-settings-image-type" class=" ihc-js-image-type-selector" value="2" <?php echo ($data['metas']['ihc-membership-card-settings-image-type'] === '2') ? 'checked': ''; ?> >
						<label><?php esc_html_e('Member Avatar', 'ihc');?></label>
						<img src="<?php echo esc_url(IHC_URL . "assets/images/no-avatar.png");?>" class="ihc-membership-card-settings-image-example"/>
					</div>
				</div>

				<?php
						$class = ($data['metas']['ihc-membership-card-settings-image-type'] === '1') ? '' : 'ihc-display-none';
				?>
				<input type="text" class="ihc-membership-card-settings-image <?php echo esc_attr($class);?>" name="ihc_membership_card_image" value="<?php echo esc_url($data['metas']['ihc_membership_card_image']);?>" onClick="openMediaUp(this);" />

			</div>
			</div>
			</div>

			<div class="iump-form-line">
				<h2><?php esc_html_e('Display Information Options', 'ihc');?></h2>
			</div>
			<div class="iump-form-line">
				<div class="row">
				<div class="iump-form-line">
					<h4><?php esc_html_e('Member Membership', 'ihc');?></h4>
					<p><?php esc_html_e('Customize the label for this particular line that will shows up besides the Membership Name. Leave empty if you want to show only Membership Name.', 'ihc');?></p>
				</div>
							<div class="col-xs-3">
													<div class="input-group">
														<span class="input-group-addon" id="basic-addon1"><?php esc_html_e('Label:', 'ihc');?></span>
														<input type="text" class="form-control" name="ihc_membership_member_level_label" value="<?php echo esc_attr($data['metas']['ihc_membership_member_level_label']);?>" />
													</div>
										</div>
							</div>
				</div>
					<div class="iump-form-line">
			<div class="row">
				<div class="iump-form-line">
					<h4><?php esc_html_e('Member Since', 'ihc');?></h4>
					<p><?php esc_html_e('(Optional) Will be mentioned on Membership Card when mebmer registered on your Website. Customize also the label besides this information.', 'ihc');?></p>
				</div>
				<div class="col-xs-3">

								<label class="iump_label_shiwtch ihc-switch-button-margin">
									<?php $checked = ($data['metas']['ihc_membership_member_since_enable']) ? 'checked' : '';?>
									<input type="checkbox" class="iump-switch" onClick="iumpCheckAndH(this, '#ihc_membership_member_since_enable');" <?php echo esc_attr($checked);?> />
									<div class="switch ihc-display-inline"></div>
								</label>
								<input type="hidden" name="ihc_membership_member_since_enable" value="<?php echo esc_attr($data['metas']['ihc_membership_member_since_enable']);?>" id="ihc_membership_member_since_enable" />
								<div class="input-group">
									<span class="input-group-addon" id="basic-addon1"><?php esc_html_e('Member since label:', 'ihc');?></span>
									<input type="text" class="form-control" name="ihc_membership_member_since_label" value="<?php echo esc_attr($data['metas']['ihc_membership_member_since_label']);?>" />
								</div>
						</div>
					</div>
			</div>



					<div class="iump-form-line">
			<div class="row">
				<div class="iump-form-line">
				<h4><?php esc_html_e('Membership Expire Date', 'ihc');?></h4>
				<p><?php esc_html_e('(Optional) Will be mentioned when the Date when Membership will expires for current Member. Customize also the label besides this information.', 'ihc');?></p>
				</div>
					<div class="col-xs-3">
											<label class="iump_label_shiwtch ihc-switch-button-margin">
												<?php $checked = ($data['metas']['ihc_membership_member_level_expire']) ? 'checked' : '';?>
												<input type="checkbox" class="iump-switch" onClick="iumpCheckAndH(this, '#ihc_membership_member_level_expire');" <?php echo esc_attr($checked);?> />
												<div class="switch ihc-display-inline"></div>
											</label>
											<input type="hidden" name="ihc_membership_member_level_expire" value="<?php echo esc_attr($data['metas']['ihc_membership_member_level_expire']);?>" id="ihc_membership_member_level_expire" />
											<div class="input-group">
												<span class="input-group-addon" id="basic-addon1"><?php esc_html_e('Label:', 'ihc');?></span>
												<input type="text" class="form-control" name="ihc_membership_member_level_expire_label" value="<?php echo esc_attr($data['metas']['ihc_membership_member_level_expire_label']);?>">
											</div>
							</div>
					</div>
			</div>


					<div class="iump-form-line">
						<div class="row">
							<div class="iump-form-line">
							<h4><?php esc_html_e('User ID', 'ihc');?></h4>
							<p><?php esc_html_e('(Optional) The unique ID generated for each member by WordPress. Customize also the label besides this information.', 'ihc');?></p>
							</div>
								<div class="col-xs-3">
														<label class="iump_label_shiwtch ihc-switch-button-margin">
															<?php $checked = ($data['metas']['ihc_membership_member_show_uid']) ? 'checked' : '';?>
															<input type="checkbox" class="iump-switch" onClick="iumpCheckAndH(this, '#ihc_membership_member_show_uid');" <?php echo esc_attr($checked);?> />
															<div class="switch ihc-display-inline"></div>
														</label>
														<input type="hidden" name="ihc_membership_member_show_uid" value="<?php echo esc_attr($data['metas']['ihc_membership_member_show_uid']);?>" id="ihc_membership_member_show_uid" />
														<div class="input-group">
															<span class="input-group-addon" id="basic-addon1"><?php esc_html_e('Label:', 'ihc');?></span>
															<input type="text" class="form-control" name="ihc_membership_member_uid_label" value="<?php echo esc_attr($data['metas']['ihc_membership_member_uid_label']);?>">
														</div>
										</div>
								</div>
						</div>

						<div class="iump-form-line">
							<div class="row">
								<div class="iump-form-line">
								<h4><?php esc_html_e('Additional Member Fields ', 'ihc');?></h4>
								<p><?php esc_html_e('(Optional) Add extra member information on Membership Card', 'ihc');?></p>
							</div>
									<div class="col-xs-3">
										<select name="ihc_membership_member_show_extrafields[]" multiple size="5" class="iump-form-select ihc-form-element ihc-form-element-select ihc-form-select">
											<?php
											foreach ($extra_fields as $k=>$v):
												?>
												<option value="<?php echo esc_attr($k);?>" <?php echo (is_array($data['metas']['ihc_membership_member_show_extrafields']) && in_array($k,$data['metas']['ihc_membership_member_show_extrafields'])) ? 'selected' : ''; ?> >
												<?php echo esc_html($v);?></option>
												<?php
											endforeach;
										?></select>
										</div>
							</div>
						</div>

			<?php if (!empty($levels)):?>
				<div class="iump-form-line">
					<h2><?php esc_html_e('Select Membership Plans ', 'ihc');?></h2>
					<p><?php esc_html_e('Select specific Membership Plans for which members may receives Cards.', 'ihc');?></p>
					<?php $exclude_vals = explode(',', $data['metas']['ihc_membership_card_exclude_levels']);?>
					<?php foreach ($levels as $lid=>$level_arr):?>
					<div class="ihc-membership-card-settings-levels">
						<?php $checked = (in_array($lid, $exclude_vals)) ? '' : 'checked';?>
						<input type="checkbox" <?php echo esc_attr($checked);?> onClick="ihcAddToHiddenWhenUncheck(this, '<?php echo esc_attr($lid);?>', '#ihc_membership_card_exclude_levels');" /> <?php echo esc_html($level_arr['label']);?>
					</div>
					<?php endforeach;?>
				</div>
			<?php endif;?>
			<input type="hidden" name="ihc_membership_card_exclude_levels" value="<?php echo esc_attr($data['metas']['ihc_membership_card_exclude_levels']);?>" id="ihc_membership_card_exclude_levels" />


		<div class="iump-form-line">
			<h2><?php esc_html_e('Custom CSS', 'ihc');?></h2>
			<p><?php esc_html_e('Customize further Cards style by adding your own style code.', 'ihc');?></p>
			<textarea name="ihc_membership_card_custom_css" class="ihc-custom-css-box"><?php echo esc_html(stripslashes($data['metas']['ihc_membership_card_custom_css']));?></textarea>
		</div>

			<div class="ihc-user-list-shortcode-wrapp">
				<div class="content-shortcode">
					<span class="the-shortcode"><?php esc_html_e('Shortcode: ', 'ihc');?> [ihc-membership-card]</span>
				</div>
			</div>


			<div class="ihc-wrapp-submit-bttn ihc-submit-form">
				<input id="ihc_submit_bttn" type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_save" class="button button-primary button-large" />
			</div>

		</div>
	</div>
</form>
