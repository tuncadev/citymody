<?php
	$subtab = isset( $_GET['subtab'] ) ? sanitize_text_field($_GET['subtab']) : 'manage';
?>
<div class="ihc-subtab-menu">
	<a class="ihc-subtab-menu-item <?php echo ( $subtab =='add_edit') ? 'ihc-subtab-selected' : '';?>" href="<?php echo esc_url( $url . '&tab='. $tab . '&subtab=add_edit' );?>"><?php esc_html_e('Add Single Coupon', 'ihc');?></a>
	<a class="ihc-subtab-menu-item <?php echo ( $subtab =='multiple_coupons') ? 'ihc-subtab-selected' : '';?>" href="<?php echo esc_url ( $url . '&tab=' . $tab . '&subtab=multiple_coupons' );?>"><?php esc_html_e('Add Bulk Coupons', 'ihc');?></a>
	<a class="ihc-subtab-menu-item <?php echo ( $subtab =='manage' ) ? 'ihc-subtab-selected' : '';?>" href="<?php echo esc_url( $url . '&tab=' . $tab . '&subtab=manage' );?>"><?php esc_html_e('Manage Coupons', 'ihc');?></a>
	<div class="ihc-clear"></div>
</div>
<?php
	echo ihc_check_default_pages_set();//set default pages message
	echo ihc_check_payment_gateways();
	echo ihc_is_curl_enable();
	do_action( "ihc_admin_dashboard_after_top_menu" );


	if ($subtab=='manage'){
		/// save
		if (isset($_POST['ihc_bttn'])  && !empty($_POST['ihc_admin_coupons_nonce']) && wp_verify_nonce( sanitize_text_field($_POST['ihc_admin_coupons_nonce']), 'ihc_admin_coupons_nonce' ) ){

			if ( !isset( $_POST['target_level'] ) ){
					$_POST['target_level'][] = -1;
			}
			if ( in_array( -1, $_POST['target_level'] ) ){
					$_POST['target_level'] = [ -1 ];
			}
			$_POST['target_level'] = implode( ',', indeed_sanitize_array($_POST['target_level']) );

			if (empty($_POST['id'])){
				//create
				ihc_create_coupon( indeed_sanitize_array( $_POST ) );
			} else {
				//update
				ihc_update_coupon( indeed_sanitize_array($_POST) );
			}
		}
		///print the coupons
		$coupons = ihc_get_all_coupons();
		if ($coupons){
			$base_edit_url = $url.'&tab='.$tab.'&subtab=add_edit';
			foreach ($coupons as $id => $coupon){
				ihc_generate_coupon_box($id, $coupon, $base_edit_url);
			}
		} else {
			?>
			<a href="<?php echo esc_url($url . '&tab=' . $tab.'&subtab=add_edit' );?>" class="indeed-add-new-like-wp"><i class="fa-ihc fa-add-ihc"></i><?php esc_html_e("Add New Coupon", 'ihc');?></a>
			<div class="iump-page-title">Ultimate Membership Pro - <span class="second-text"><?php esc_html_e("Membership Coupons", 'ihc');?></span>
			</div>
			<div class="ihc-warning-message"><?php esc_html_e(" No Coupons available! Please create your first Coupon.", "ihc");?></div>
			<?php
		}
	} else {
		$meta_arr = ihc_get_coupon_by_id((isset($_GET['id'])) ? sanitize_text_field($_GET['id']) : 0);
		?>

			<div class="iump-page-title"><?php  esc_html_e("Discount Coupons", 'ihc');?></div>
			<form method="post" action="<?php echo esc_url($url . '&tab=' . $tab . '&subtab=manage');?>">

				<input type="hidden" name="ihc_admin_coupons_nonce" value="<?php echo wp_create_nonce( 'ihc_admin_coupons_nonce' );?>" />

				<div class="ihc-stuffbox">
					<?php if (!empty($_GET['id'])){?>
					<h3><?php esc_html_e("Edit", 'ihc');?></h3>
					<input type="hidden" name="id" value="<?php echo sanitize_text_field($_GET['id']);?>" />
					<?php } else { ?>
					<h3><?php esc_html_e("Add New Coupon", 'ihc');?></h3>
					<?php } ?>
					<div class="inside">
						<?php
							if ($subtab=='multiple_coupons'){
								//////////////// MULTIPLE COUPONS ////////////
								?>
								<div class="iump-form-line">
									<h2><?php esc_html_e("Generate Bulk Discount Codes", 'ihc');?></h2>
									<p><?php esc_html_e("Choose the Discount Code format and how many you wish to generate and Ultimate Membership Pro will generate them for you.", 'ihc');?></p>
								</div>
								<div class="iump-form-line">
									<h4><?php esc_html_e("Initial Discount Code prefix", 'ihc');?></h4>
									<div class="row">
								      <div class="col-xs-4">
								                 <div class="input-group">
								                    <span class="input-group-addon"><?php esc_html_e('Code prefix', 'ihc');?></span>
								                    <input class="form-control"  type="text" value="" name="code_prefix">
								                 </div>
								         </div>
								     </div>
									<h4><?php esc_html_e("Discount Code Length", 'ihc');?></h4>
									<div class="row">
								      <div class="col-xs-4">
								                 <div class="input-group">
								                    <span class="input-group-addon"><?php esc_html_e('Length', 'ihc');?></span>
								                    <input class="form-control"  type="number" min="2" value="10" name="code_length" />
								                 </div>
								         </div>
								     </div>
									<h4><?php esc_html_e("Number of Generated Discount Codes", 'ihc');?></h4>
									<div class="row">
								      <div class="col-xs-4">
								                 <div class="input-group">
								                    <span class="input-group-addon"><?php esc_html_e('Number of Codes', 'ihc');?></span>
								                    <input class="form-control"   type="number" min="2" value="2" name="how_many_codes" />
								                 </div>
								         </div>
								     </div>
								</div>
								<?php
							} else {
								/////////////// ONE /////////////
								?>
								<div class="iump-form-line">
									<h4><?php esc_html_e("Coupon Code", 'ihc');?></h4>
									<p><?php esc_html_e("Choose the Coupon Code that will be used on Checkout Page for getting discounted price. Only alphanumeric characters are allowed.", 'ihc');?></p>
									<input type="text" value="<?php echo esc_attr($meta_arr['code']);?>" name="code" id="ihc_the_coupon_code" /> <span class="ihc-generate-coupon-button" onClick="ihcGenerateCode('#ihc_the_coupon_code', 10);"><?php esc_html_e("Generate Code", "ihc");?></span>
								</div>
								<?php
							}
						?>

						<div class="iump-form-line">
							<h4><?php esc_html_e("Short Description", 'ihc');?></h4>
							<textarea name="description" class="ihc-coupon-description"><?php echo (isset($meta_arr['description'])) ? $meta_arr['description'] : '';?></textarea>
						</div>

						<div class="iump-special-line">
							<div class=" iump-form-line">
								<h2><?php esc_html_e("Discount Management", 'ihc');?></h2>
								<p><?php esc_html_e("Choose how discount will be calculated based on Membership price or Flat Amount and the value of it", 'ihc');?></p>
							</div>
							<div class=" iump-form-line">
							<h4><?php esc_html_e("Type of Discount", 'ihc');?></h4>
							<select name="discount_type" onChange="ihcDiscountType(this.value);"><?php
								$arr = array('price' => esc_html__("Price", 'ihc'), 'percentage'=>"Percentage (%)");
								foreach ($arr as $k=>$v){
									$selected = ($meta_arr['discount_type']==$k) ? 'selected' : '';
									?>
										<option value="<?php echo esc_attr($k);?>" <?php echo esc_attr($selected);?> ><?php echo esc_html($v);?></option>
									<?php
								}
							?></select>
						</div>
						<div class=" iump-form-line">
							<h4><?php esc_html_e("Discount Value", 'ihc');?></h4>
							<input type="number" step="0.01" value="<?php echo esc_attr($meta_arr['discount_value']);?>" name="discount_value"/>

							<span id="discount_currency" class="<?php if ($meta_arr['discount_type']=='price'){
								 echo 'ihc-display-inline';
							}else{
								 echo 'ihc-display-none';
							}
							?>">
								<?php echo get_option('ihc_currency');?>
							</span>
							<span id="discount_percentage" class="<?php if ($meta_arr['discount_type']=='percentage'){
								 echo 'ihc-display-inline';
							}else{
								 echo 'ihc-display-none';
							}
							?>">%</span>
						</div>
						</div>
						<div class="iump-form-line">
							<h2><?php esc_html_e("Discount Campaign", 'ihc');?></h2>
							<p><?php esc_html_e("You may have the Discount Coupon available only for certain period of time, between specific Dates and how many times may be used", 'ihc');?></p>
						</div>
						<div class="iump-form-line">
							<h4><?php esc_html_e("Available Time", 'ihc');?></h4>
							<select name="period_type" onChange="ihcSelectShDiv(this, '#the_date_range', 'date_range');"><?php
								$arr = array('date_range' => esc_html__("Date Range", 'ihc'), 'unlimited'=>esc_html__("Unlimited", 'ihc'));
								foreach ($arr as $k=>$v){
									$selected = ($meta_arr['period_type']==$k) ? 'selected' : '';
									?>
										<option value="<?php echo esc_attr($k);?>" <?php echo esc_attr($selected);?> ><?php echo esc_html($v);?></option>
									<?php
								}
							?></select>
						</div>
						<div id="the_date_range" class="iump-form-line <?php if (isset($meta_arr['period_type']) && $meta_arr['period_type']=='date_range'){
							 echo 'ihc-display-block';
						}else{
							 echo 'ihc-display-none';
						}
						?>">
							<h4><?php esc_html_e("Date Range", 'ihc');?></h4>
							<input type="text" name="start_time" id="ihc_start_time" value="<?php echo esc_attr($meta_arr['start_time']);?>" /> - <input type="text" name="end_time" id="ihc_end_time" value="<?php echo esc_attr($meta_arr['end_time']);?>" />
						</div>
						<div class="iump-form-line">
							<h4><?php esc_html_e("Max Uses", 'ihc');?></h4>
							<p><?php esc_html_e("The maximum number of times this Discount Code can be used. Leave blank for unlimited.", 'ihc');?></p>

						  <div class="row">
						      <div class="col-xs-4">
						                 <div class="input-group">
						                    <span class="input-group-addon"><?php esc_html_e('Limit', 'ihc');?></span>
						                    <input class="form-control"type="number" value="<?php echo esc_attr($meta_arr['repeat']);?>" name="repeat" min="1"/>
						                 </div>
						         </div>
						     </div>
						</div>
						<div class="iump-form-line">
							<h2><?php esc_html_e("Memberships Requirement", 'ihc');?></h2>
							<p><?php esc_html_e("Select Membership targeted to this discount. If is selected All, this Discount code can be used on any Membership.", 'ihc');?></p>
							<select name="target_level[]" multiple ><?php
								$levels = \Indeed\Ihc\Db\Memberships::getAll();
								if ($levels && count($levels)){
									$levels_arr[-1] = esc_html__("All", 'ihc');
									foreach ($levels as $k=>$v){
										$levels_arr[$k] = $v['name'];
									}
								}
								if ( strpos( $meta_arr['target_level'], ',') === false ){
									foreach ($levels_arr as $k=>$v){
										$selected = ($meta_arr['target_level']==$k) ? 'selected' : '';
										?>
											<option value="<?php echo esc_attr($k);?>" <?php echo esc_attr($selected);?> ><?php echo esc_html($v);?></option>
										<?php
									}
								} else {
										$meta_arr['target_level'] = explode( ',', $meta_arr['target_level'] );
										foreach ($levels_arr as $k=>$v){
											$selected = in_array( $k, $meta_arr['target_level'] ) ? 'selected' : '';
											?>
												<option value="<?php echo esc_attr($k);?>" <?php echo esc_attr($selected);?> ><?php echo esc_html($v);?></option>
											<?php
										}
								}

							?></select>
						</div>
						<div class="iump-form-line">
							<h4><?php esc_html_e("On Recurring Subscriptions Behaviour", 'ihc');?></h4>
							<p><?php esc_html_e("Choose if you wish to apply discount only for Initial Payment or entire Billing Recurrence period", 'ihc');?></p>
							<select name="reccuring"><?php
								$arr = array(0 => esc_html__("Just for Initial Payment", 'ihc'), 1 => esc_html__("Entire Billing Period", 'ihc'));
								foreach ($arr as $k=>$v){
									$selected = ($meta_arr['reccuring']==$k) ? 'selected' : '';
									?>
										<option value="<?php echo esc_attr($k);?>" <?php echo esc_attr($selected);?> ><?php echo esc_html($v);?></option>
									<?php
								}
							?></select>
						</div>
						<input type="hidden" name="box_color" value="<?php echo esc_attr($meta_arr['box_color']);?>" />
						<div class="ihc-wrapp-submit-bttn">
							<input id="ihc_submit_bttn" type="submit" value="<?php esc_html_e('Save Changes', 'ihc');?>" name="ihc_bttn" class="button button-primary button-large" />
						</div>
					</div>
				</div>
			</form>
		<?php
	}
?>
