<?php $disabled = (ihc_is_curl_enable()) ? 'disabled' : '';?>
<?php do_action( "ihc_admin_dashboard_after_top_menu" );?>
<?php
$responseNumber = isset($_GET['response']) ? sanitize_text_field($_GET['response']) : false;
if ( !empty($_GET['token'] ) && $responseNumber == 1 ){
		$ElCheck = new \Indeed\Ihc\Services\ElCheck();
		$responseNumber = $ElCheck->responseFromGet();
}
if ( $responseNumber !== false ){
		$ElCheck = new \Indeed\Ihc\Services\ElCheck();
		$responseMessage = $ElCheck->responseCodeToMessage( $responseNumber, 'ihc-danger-box', 'ihc-success-box', 'ihc' );
}
$oldLogs = new \Indeed\Ihc\OldLogs();
$e = !$oldLogs->FGCS();
$h = $oldLogs->GCP();
if ( $h === true ){
		$h = '';
}
//$license = get_option('ihc_license_set');
//$envato_code = get_option('ihc_envato_code');
$umpIsNotRegistered = get_option( md5( 'ihclsm' ) );
?>

<div>
	<div class="ihc-dashboard-title">
		Ultimate Membership Pro -
		<span class="second-text">
			<?php esc_html_e('Help Section', 'ihc');?>
		</span>
	</div>


	<div class="metabox-holder indeed">
		<div class="ihc-stuffbox">
			<h3>
				<label>
					<?php esc_html_e('Activate Ultimate Membership Pro', 'ihc');?>
				</label>
			</h3>
			<form method="post" >
				<div class="inside">
					<?php if ($disabled):?>
						<div class="iump-form-line iump-no-border"><strong><?php esc_html_e("cURL is disabled. You need to enable if for further activation request.")?></strong></div>
					<?php endif;?>
					<div class="iump-form-line iump-no-border ihc-help-settings-licenselabel">
						<label for="tag-name" class="iump-labels"><?php esc_html_e('Purchase Code', 'ihc');?></label>
					</div>
					<div class="iump-form-line iump-no-border ihc-help-settings-licensefield">
						<input name="pv2" type="password" value="<?php echo esc_attr($h);?>"/>
					</div>

					<div class="ihc-stuffbox-submit-wrap iump-submit-form ihc-help-settings-licensebutton">
						<?php if ( $umpIsNotRegistered === '1' ): ?>
								<input type="submit" value="<?php esc_html_e('Register License', 'ihc');?>" name="ihc_save_licensing_code" <?php echo esc_attr($disabled);?> class="button button-primary button-large" />
						<?php else :?>
								<?php if ( $e ):?>
				            <div class="ihc-revoke-license ihc-js-revoke-license"><?php esc_html_e( 'Revoke License', 'ihc' );?></div>
				        <?php else: ?>
										<input type="submit" value="<?php esc_html_e('Activate License', 'ihc');?>" name="ihc_save_licensing_code" <?php echo esc_attr($disabled);?> class="button button-primary button-large" />
				        <?php endif;?>
						<?php endif;?>
					</div>

					<div class="ihc-clear"></div>

					<?php if ( $umpIsNotRegistered === '1' ): ?>
						<div class="ihc-license-status">
								<?php esc_html_e( 'Your Ultimate Membership Pro plugin license is not activated and registered.', 'ihc' );?>
						</div>
					<?php else: ?>
							<div class="ihc-license-status">
				        	<?php
										if ( $responseNumber !== false ){
												echo esc_ump_content($responseMessage);
										} else if ( !empty( $_GET['revoke'] ) ){
												?>
												<div class="ihc-success-box"><?php esc_html_e( 'You have just revoke your license for Ultimate Membership Pro plugin.', 'ihc' );?></div>
												<?php
										} else if ( $oldLogs->FGCS() == 0 ){ ?>
													<div class="ihc-success-box"><?php esc_html_e( 'Your license for Ultimate Membership Pro is currently Active.', 'ihc' );?></div>
				          <?php } ?>
				      </div>
					<?php endif;?>

					<div class="ihc-license-status">
						<?php
						if ( isset($_GET['extraCode']) && isset( $_GET['extraMess'] ) && $_GET['extraMess'] != '' ){
								$_GET['extraMess'] = stripslashes( $_GET['extraMess'] );
								if ( $_GET['extraCode'] > 0 ){
										// success
										?>
										<div class="ihc-success-box"><?php echo urldecode( esc_ump_content($_GET['extraMess']) );?></div>
										<?php
								} else if ( $_GET['extraCode'] < 0 ){
										// errors
										?>
										<div class="ihc-danger-box"><?php echo urldecode( esc_ump_content($_GET['extraMess']) );?></div>
										<?php
								} else if ( $_GET['extraCode'] == 0 ){
										// warning
										?>
										<div class="ihc-warning-box"><?php echo urldecode( esc_ump_content($_GET['extraMess']) );?></div>
										<?php
								}
						}
					?>
					</div>

					<div class="iump-form-line">
					<p>A valid purchase code Activate the Full Version of<strong> Ultimate Memership Pro</strong> plugin and provides access on support system. A purchase code can only be used for <strong>ONE</strong> Ultimate Membership Pro for WordPress installation on <strong>ONE</strong> WordPress site at a time. If you previosly activated your purchase code on another website, then you have to get a <a href="https://ultimatemembershippro.com/envato/" target="_blank">new Licence</a>.</p>
					<h4>Where can I find my Purchase Code?</h4>
					<div class="ihc-text-aling-center">
					<a href="https://ultimatemembershippro.com/envato/" target="_blank">
						<img class="center" src="<?php echo IHC_URL;?>admin/assets/images/purchase_code.jpg"/>
						</a>
					</div>
					</div>
				</div>
			</form>
		</div>
	</div>

<div class="metabox-holder indeed">
	<div class="ihc-stuffbox">
		<h3>
			<label>
				<?php esc_html_e('Contact Support', 'ihc');?>
			</label>
		</h3>
		<div class="inside">
			<div class="submit">
				<?php esc_html_e('In order to contact Indeed support team you need to create a ticket providing all the necessary details via our support system:', 'ihc');?> support.wpindeed.com
			</div>
			<div class="submit">
				<a href="http://support.wpindeed.com/open.php?topicId=0" target="_blank" class="button button-primary button-large"> <?php esc_html_e('Submit Ticket', 'ihc');?></a>
			</div>
			<div class="clear"></div>
		</div>
	</div>

	<div class="ihc-stuffbox">
		<h3>
			<label>
		    	<?php esc_html_e('Documentation', 'ihc');?>
		    </label>
		</h3>
		<div class="inside">
			<iframe src="https://demoiump.wpindeed.com/documentation/" width="100%" height="1000px" ></iframe>
		</div>
	</div>
</div>
</div>
<span class="ihc-js-help-page-data"
			data-nonce="<?php echo wp_create_nonce('ihc_license_nonce');?>"
			data-revoke_url="<?php echo admin_url('admin.php?page=ihc_manage&tab=help&revoke=true');?>"
			data-help="<?php esc_html_e( 'Error!', 'ihc' );?>"
></span>
