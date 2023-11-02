<?php
wp_enqueue_script( 'ihc-print-this' );

$invoiceCss = get_option( 'ihc_invoices_custom_css' );
if ( $invoiceCss !== false && $invoiceCss != '' ){
	wp_register_style( 'dummy-handle', false );
	wp_enqueue_style( 'dummy-handle' );
	wp_add_inline_style( 'dummy-handle', stripslashes( $invoiceCss ) );
}

if ( isset( $_POST['save_edit_order'] ) && !empty( $_POST['ihc_admin_edit_order_nonce'] ) && wp_verify_nonce( sanitize_text_field($_POST['ihc_admin_edit_order_nonce']), 'ihc_admin_edit_order_nonce' ) ){
		$orderObject = new \Indeed\Ihc\Db\Orders();
		$orderData = indeed_sanitize_array($_POST);
		$orderObject->setData( indeed_sanitize_array($_POST) )->setId( sanitize_text_field($_POST['id']) )->save();

		$orderData = $orderObject->fetch()->get();

		$orderMeta = new \Indeed\Ihc\Db\OrderMeta();
		$paymentGateway = $orderMeta->get( sanitize_text_field($_POST['id']), 'ihc_payment_type' );

		switch ( $_POST['status'] ){
				case 'pending':
					$args = [ 'manual' => true, 'expire_time' => '0000-00-00 00:00:00', 'payment_gateway' => $paymentGateway ];
					\Indeed\Ihc\UserSubscriptions::makeComplete( $orderData->uid, $orderData->lid, true, $args );
					\Indeed\Ihc\UserSubscriptions::updateStatus( $orderData->uid, $orderData->lid, 0 );
					do_action( 'ihc_action_after_cancel_subscription', $orderData->uid, $orderData->lid );
					break;
				case 'Completed':
						$levelData = \Indeed\Ihc\Db\Memberships::getOne( $orderData->lid );
						if (isset($levelData['access_trial_time_value']) && $levelData['access_trial_time_value'] > 0 && \Indeed\Ihc\UserSubscriptions::isFirstTime($orderData['uid'], sanitize_text_field($_POST['lid']) )){
							/// CHECK FOR TRIAL
								\Indeed\Ihc\UserSubscriptions::makeComplete( $orderData->uid, $orderData->lid, true, [ 'manual' => true, 'payment_gateway' => $paymentGateway ] );
						} else {
								\Indeed\Ihc\UserSubscriptions::makeComplete( $orderData->uid, $orderData->lid, false, [ 'manual' => true, 'payment_gateway' => $paymentGateway ] );
						}
						if ( $paymentGateway === 'bank_transfer' ){
							// create a transaction_id for this entry
			        $orderMeta = new \Indeed\Ihc\Db\OrderMeta();
							$transactionId = $orderData->uid . '_' . $orderData->lid . '_' . time();
							$orderMeta->save( sanitize_text_field( $_POST['id'] ), 'transaction_id', $transactionId );
							do_action( 'ihc_payment_completed', $orderData->uid, $orderData->lid );
						}
					break;
				case 'error':
					\Indeed\Ihc\UserSubscriptions::updateStatus( $orderData->uid, $orderData->lid, 0 );
					do_action( 'ihc_action_after_cancel_subscription', $orderData->uid, $orderData->lid );
					break;
				case 'refund':
					$deleteLevelForUser = apply_filters( 'ihc_filter_delete_level_for_user_on_payment_refund', true, $orderData->uid, $orderData->lid );
			    do_action( 'ihc_action_payments_before_refund', $orderData->uid, $orderData->lid );
	        if ( $deleteLevelForUser ){
	            \Indeed\Ihc\UserSubscriptions::deleteOne( $orderData->uid, $orderData->lid );
	        }
	        do_action( 'ihc_action_payments_after_refund', $orderData->uid, $orderData->lid );
					break;
				case 'fail':
					\Indeed\Ihc\UserSubscriptions::deleteOne( $orderData->uid, $orderData->lid );
					break;
		}
}

////////////// create order manually
if (isset($_POST['save_order']) && !empty( $_POST['ihc_admin_add_new_order_nonce'] ) && wp_verify_nonce( sanitize_text_field($_POST['ihc_admin_add_new_order_nonce']), 'ihc_admin_add_new_order_nonce' ) ){
		require_once IHC_PATH . 'admin/classes/Ihc_Create_Orders_Manually.php';
		$Ihc_Create_Orders_Manually = new Ihc_Create_Orders_Manually( indeed_sanitize_array($_POST) );
		$Ihc_Create_Orders_Manually->process();
		if (!$Ihc_Create_Orders_Manually->get_status()){
				$create_order_message = '<div class="ihc-danger-box">' . esc_html($Ihc_Create_Orders_Manually->get_reason()) . '</div>';
		} else {
				$create_order_message = '<div class="ihc-success-box">' . esc_html__('Order has been created!', 'ihc') . '</div>';
		}
}

if (!empty($_POST['submit_new_payment'])){
	unset($_POST['submit_new_payment']);
	$array = indeed_sanitize_array($_POST);
	if (empty($array['txn_id'])){
		/// set txn_id
		$array['txn_id'] = sanitize_text_field($_POST['uid']) . '_' . sanitize_text_field($_POST['order_id']) . '_' . indeed_get_unixtimestamp_with_timezone();
	}
	$array['message'] = 'success';


	/// THIS PIECE OF CODE ACT AS AN IPN SERVICE.
	$level_data = ihc_get_level_by_id(sanitize_text_field($_POST['level']));
	if (isset($level_data['access_trial_time_value']) && $level_data['access_trial_time_value'] > 0 && \Indeed\Ihc\UserSubscriptions::isFirstTime( sanitize_text_field($_POST['uid']), sanitize_text_field($_POST['level']) )){
		/// CHECK FOR TRIAL
			\Indeed\Ihc\UserSubscriptions::makeComplete( sanitize_text_field($_POST['uid']), sanitize_text_field($_POST['level']), true, [ 'manual' => true ] );
	} else {
		  \Indeed\Ihc\UserSubscriptions::makeComplete( sanitize_text_field($_POST['uid']), sanitize_text_field($_POST['level']), false, [ 'manual' => true ] );
	}

	do_action( 'ihc_payment_completed', sanitize_text_field($_POST['uid']), sanitize_text_field($_POST['level']) );
	ihc_insert_update_transaction( sanitize_text_field($_POST['uid']), $array['txn_id'], $array);

	Ihc_User_Logs::set_user_id(sanitize_text_field($_POST['uid']));
	Ihc_User_Logs::set_level_id(sanitize_text_field($_POST['level']));
	Ihc_User_Logs::write_log( esc_html__('Complete transaction.', 'ihc'), 'payments');

	unset($array);
}
$uid = (isset($_GET['uid'])) ? sanitize_text_field($_GET['uid']) : 0;

	$data['total_items'] = Ihc_Db::get_count_orders($uid);
	if ($data['total_items']){
		$url = admin_url('admin.php?page=ihc_manage&tab=orders');
		$limit = 25;
		$current_page = (empty($_GET['ihc_payments_list_p'])) ? 1 : sanitize_text_field($_GET['ihc_payments_list_p']);
		if ($current_page>1){
			$offset = ( $current_page - 1 ) * $limit;
		} else {
			$offset = 0;
		}
		include_once IHC_PATH . 'classes/Ihc_Pagination.class.php';
		$pagination = new Ihc_Pagination(array(
												'base_url' => $url,
												'param_name' => 'ihc_payments_list_p',
												'total_items' => $data['total_items'],
												'items_per_page' => $limit,
												'current_page' => $current_page,
		));
		if ($offset + $limit>$data['total_items']){
			$limit = $data['total_items'] - $offset;
		}
		$data['pagination'] = $pagination->output();
		$data['orders'] = Ihc_Db::get_all_order($limit, $offset, $uid);
	}
	$data['view_transaction_base_link'] = admin_url('admin.php?page=ihc_manage&tab=payments&details_id=');
	$data['add_new_transaction_by_order_id_link'] = admin_url('admin.php?page=ihc_manage&tab=new_transaction&order_id=');

	$payment_gateways = ihc_list_all_payments();
	$payment_gateways['woocommerce'] = esc_html__( 'WooCommerce', 'ihc' );

	$show_invoices = (ihc_is_magic_feat_active('invoices')) ? TRUE : FALSE;
	$invoiceShowOnlyCompleted = get_option('ihc_invoices_only_completed_payments');
	require_once IHC_PATH . 'classes/Orders.class.php';
	$Orders = new Ump\Orders();
?>

<?php
echo ihc_inside_dashboard_error_license();
echo ihc_check_default_pages_set();//set default pages message
echo ihc_check_payment_gateways();
echo ihc_is_curl_enable();
do_action( "ihc_admin_dashboard_after_top_menu" );
?>
<div class="iump-wrapper">
<div class="iump-page-title">Ultimate Membership Pro -
	<span class="second-text"><?php esc_html_e('Orders List', 'ihc');?></span>
</div>
<a href="<?php echo admin_url('admin.php?page=ihc_manage&tab=add_new_order');?>" class="indeed-add-new-like-wp">
			<i class="fa-ihc fa-add-ihc"></i><?php esc_html_e('Add New Order', 'ihc');?></a>

<?php if (!empty($create_order_message)):?>
    <div><?php echo esc_ump_content($create_order_message);?></div>
<?php endif;?>

<?php if (!empty($data['orders'])):?>
	<?php echo esc_ump_content($data['pagination']);?>
		<div class="iump-rsp-table">
<table class="wp-list-table widefat fixed tags ihc-admin-tables ihc-order-tables">
	<thead>
		<tr>
			<th class="manage-column check-column ihc-order-id">
				<span><?php esc_html_e('ID', 'ihc');?></span>
			</th>
			<th class="manage-column column-primary">
				<span><?php esc_html_e('Code', 'ihc');?></span>
			</th>
			<th class="manage-column">
				<span><?php esc_html_e('Customer', 'ihc');?></span>
			</th>
			<th class="manage-column  ihc-order-level">
				<span><?php esc_html_e('Memberships', 'ihc');?></span>
			</th>
			<?php if ( ihc_is_magic_feat_active( 'taxes' ) ):?>
			<th class="manage-column">
					<span><?php esc_html_e('Net Amount', 'ihc');?></span>
			</th>
			<th class="manage-column">
					<span><?php esc_html_e('Taxes', 'ihc');?></span>
			</th>
			<?php endif;?>
			<th class="manage-column">
				<span><?php esc_html_e('Total Amount', 'ihc');?></span>
			</th>
			<th class="manage-column">
				<span><?php esc_html_e('Payment method', 'ihc');?></span>
			</th>
			<th class="manage-column">
				<span><?php esc_html_e('Date', 'ihc');?></span>
			</th>
			<th class="manage-column">
				<span><?php esc_html_e('Coupon', 'ihc');?></span>
			</th>
			<th class="manage-column">
				<span><?php esc_html_e('Transaction', 'ihc');?></span>
			</th>
			<?php if ($show_invoices):?>
				<th class="manage-column">
					<span><?php esc_html_e('Invoices', 'ihc');?></span>
				</th>
			<?php endif;?>
			<th class="manage-column">
				<span><?php esc_html_e('Status', 'ihc');?></span>
			</th>
			<th class="manage-column  ihc-order-actions">
				<span><?php esc_html_e('Actions', 'ihc');?></span>
			</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th class="manage-column check-column">
				<span><?php esc_html_e('ID', 'ihc');?></span>
			</th>
			<th class="manage-column column-primary">
				<span><?php esc_html_e('Code', 'ihc');?></span>
			</th>
			<th class="manage-column">
				<span><?php esc_html_e('Customer', 'ihc');?></span>
			</th>
			<th class="manage-column">
				<span><?php esc_html_e('Items', 'ihc');?></span>
			</th>
			<?php if ( ihc_is_magic_feat_active( 'taxes' ) ):?>
			<th class="manage-column">
					<span><?php esc_html_e('Net Amount', 'ihc');?></span>
			</th>
			<th class="manage-column">
					<span><?php esc_html_e('Taxes', 'ihc');?></span>
			</th>
			<?php endif;?>
			<th class="manage-column">
				<span><?php esc_html_e('Total Amount', 'ihc');?></span>
			</th>
			<th class="manage-column">
				<span><?php esc_html_e('Payment method', 'ihc');?></span>
			</th>
			<th class="manage-column">
				<span><?php esc_html_e('Date', 'ihc');?></span>
			</th>
			<th class="manage-column">
				<span><?php esc_html_e('Coupon', 'ihc');?></span>
			</th>
			<th class="manage-column">
				<span><?php esc_html_e('Transaction', 'ihc');?></span>
			</th>
			<?php if ($show_invoices):?>
				<th class="manage-column">
					<span><?php esc_html_e('Invoice', 'ihc');?></span>
				</th>
			<?php endif;?>
			<th class="manage-column">
				<span><?php esc_html_e('Status', 'ihc');?></span>
			</th>
			<th class="manage-column">
				<span><?php esc_html_e('Actions', 'ihc');?></span>
			</th>
		</tr>
	</tfoot>

	<?php
	$i = 1;
	$orderMeta = new \Indeed\Ihc\Db\OrderMeta();
	foreach ($data['orders'] as $array):?>
		<?php
				$firstChage = false;
				$firstAmount = false;
				$firstAmount = $orderMeta->get( $array['id'], 'first_amount' );
				if(isset($firstAmount) && $firstAmount == $array['amount_value']){
					$firstChage = true;
				}
				$taxes = $orderMeta->get( $array['id'], 'taxes_amount' );
				if ( $taxes == null ){
						$taxes = $orderMeta->get( $array['id'], 'tax_value' );
				}
				if ( $taxes == null ){
						if(isset($firstChage) && $firstChage == true){
							$taxes = $orderMeta->get( $array['id'], 'first_amount_taxes' );
						}else{
							$taxes = $orderMeta->get( $array['id'], 'taxes' );
						}

				}
		?>
		<tr  class="<?php if($i%2==0){
			 echo 'alternate';
		}
		?>
		">
			<th class="check-column"><?php echo esc_html($array['id']);?></th>
			<td class="column-primary"><?php
				if (!empty($array['metas']['code'])){
					echo '<a href="' . admin_url( '/admin.php?page=ihc_manage&tab=order-edit&order_id=' . esc_attr($array['id']) ) . '" target="_blank" >' . esc_html($array['metas']['code']) . '</a>';
				} else {
					echo '-';
				}
			?>
			<button type="button" class="toggle-row"><span class="screen-reader-text"><?php esc_html_e('Show more details', 'ihc');?></span></button>
		</td>
			<td><span class="ihc-order-user"><a target="_blank" href="<?php echo esc_url(ihcAdminUserDetailsPage( $array['uid'] ));?>"><?php echo esc_html($array['user']);?></a></span></td>
			<td><div  class="ihc-order-membership"><?php echo esc_html($array['level']);?></div></td>
			<?php if ( ihc_is_magic_feat_active( 'taxes' ) ):?>
			<td>
				<?php if(isset($firstChage) && $firstChage == true && isset($firstAmount)){
						$netAmount = $firstAmount;
						if ( $taxes != false ){
							$netAmount = $firstAmount - $taxes;
						}
						echo esc_html($netAmount) . ' ' . esc_html($array['amount_type']);
				 }else{ ?>
					<?php $value = $orderMeta->get( $array['id'], 'base_price' );?>
					<?php if ( $value !== null ):?>
							<?php echo esc_html($value) . ' ' . esc_html($array['amount_type']);?>
					<?php elseif ( $taxes != false ):?>
							<?php $netAmount = $array['amount_value'] - $taxes;?>
							<?php echo esc_html($netAmount) . ' ' . esc_html($array['amount_type']);?>
					<?php else :?>
							<?php echo esc_html($array['amount_value']) . ' ' . esc_html($array['amount_type']);?>
					<?php endif;?>
				<?php } ?>
			</td>
			<td>
				<?php if ( $taxes !== null ):?>
						<?php echo esc_html($taxes) . ' ' . esc_html($array['amount_type']);?>
				<?php endif;?>
			</td>
			<?php endif;?>
			<td><span class="order-total-amount"><?php echo esc_html($array['amount_value']) . ' ' . esc_html($array['amount_type']);?></span></td>
			<td><?php
				$payment_gateway = "";
				if (empty($array['metas']['ihc_payment_type'])):
					echo '-';
				else:
					if (!empty($array['metas']['ihc_payment_type'])){
						$gateway_key = $array['metas']['ihc_payment_type'];
						echo isset( $payment_gateways[$gateway_key] ) ? $payment_gateways[$gateway_key] : '-';
						 $payment_gateway = isset( $payment_gateways[$gateway_key] ) ? $payment_gateways[$gateway_key] : '-';
					}
				endif;
			?></td>
			<td><?php echo ihc_convert_date_time_to_us_format($array['create_date']);?></td>
			<td><?php
					$coupon = $Orders->get_meta_by_order_and_name($array['id'], 'coupon_used');
					if ($coupon){
						 echo esc_html($coupon);
					}else{
						 echo '-';
					}
			?></td>
			<td><?php
								$transactionLink = '';
								$transactionId = $orderMeta->get( $array['id'], 'transaction_id' );

								if ( $transactionId == '' )
								echo '-';
								else{
									switch ( $array['metas']['ihc_payment_type'] ){
											case 'paypal':
												if ( get_option( 'ihc_paypal_sandbox' ) ){
													$transactionLink = 'https://www.sandbox.paypal.com/activity/payment/' . $transactionId;
												} else {
													$transactionLink = 'https://www.paypal.com/activity/payment/' . $transactionId;
												}
												break;
											case 'paypal_express_checkout':
													if ( get_option( 'ihc_paypal_express_checkout_sandbox' ) ){
														$transactionLink = 'https://www.sandbox.paypal.com/activity/payment/' . $transactionId;
													} else {
														$transactionLink = 'https://www.paypal.com/activity/payment/' . $transactionId;
													}
													break;
											case 'stripe':

												break;
											case 'stripe_checkout_v2':
												$key = get_option( 'ihc_stripe_checkout_v2_publishable_key' );
												if ( strpos( $key, 'pk_test' ) !== false ){
													$transactionLink = 'https://dashboard.stripe.com/test/payments/' . $transactionId;
												} else {
													$transactionLink = 'https://dashboard.stripe.com/payments/' . $transactionId;
												}
												break;
											case 'stripe_connect':
											$key = get_option( 'ihc_stripe_connect_live_mode' );
											if ( $key == false ){
													$transactionLink = 'https://dashboard.stripe.com/test/payments/' . $transactionId;
												} else {
													$transactionLink = 'https://dashboard.stripe.com/payments/' . $transactionId;
												}
												break;

											case 'mollie':
												$transactionLink = 'https://www.mollie.com/dashboard/payments/' . $transactionId;
												break;
											case 'twocheckout':
												if ( strpos( $transactionId, '_' ) !== false ){
														$temporaryTransactionId = explode( '_', $transactionId );
														$transactionId = isset( $temporaryTransactionId[1] ) ? $temporaryTransactionId[1] : $transactionId;
												}
												$transactionLink = 'https://secure.2checkout.com/cpanel/order_info.php?refno=' . $transactionId;
												break;
											case 'braintree':
											if(get_option( 'ihc_braintree_merchant_id' )){
												$merchantID = get_option( 'ihc_braintree_merchant_id' );
												if ( get_option( 'ihc_braintree_sandbox' )){
													$transactionLink = 'https://sandbox.braintreegateway.com/merchants/'.$merchantID.'/transactions/' . $transactionId;
												}else{
													$transactionLink = 'https://braintreegateway.com/merchants/'.$merchantID.'/transactions/' . $transactionId;
												}
											}
											break;
									default:
												$transactionLink = '#';
												break;
									}
								}
			?><a target="_blank" title="<?php echo esc_html__('Check Transaction on ', 'ihc') . esc_attr( $payment_gateway ); ?>" href="<?php echo esc_url($transactionLink);?>"><?php echo esc_html($transactionId);?></a></td>
			<?php if ($show_invoices):?>
				<?php if ( !empty( $invoiceShowOnlyCompleted ) && $array['status'] !== 'Completed' ):?>
					<td data-title="<?php esc_html_e('Level', 'ihc');?>">-</td>
				<?php else:?>
					<td><i class="fa-ihc fa-invoice-preview-ihc iump-pointer" onClick="iumpGenerateInvoice(<?php echo esc_attr($array['id']);?>);"></i></td>
				<?php endif;?>
			<?php endif;?>
			<td>
				<strong>
				<?php
					switch ($array['status']){
						case 'Completed':
						esc_html_e('Completed', 'ihc');
							break;
						case 'pending':
							echo '<div>' . esc_html__('Pending', 'ihc') . '</div>';

							break;
						case 'fail':
						case 'failed':
							esc_html_e('Fail', 'ihc');
							break;
						case 'error':
							esc_html_e('Error', 'ihc');
							break;
						default:
							echo esc_html( $array['status'] );
							break;
					}
				?>
			</strong>
			</td>
			<td class="column ihc-order-actions">

					<?php if ( $array['status'] == 'pending' ):?>
								<span class="ihc-js-make-order-completed ihc-pointer" data-id="<?php echo esc_attr($array['id']);?>" ><i  title="<?php esc_html_e( 'Make Completed', 'ihc' );?>" class="fa-ihc ihc-icon-completed-e"></i></span>
					<?php endif;?>

					<a title="<?php esc_html_e( 'Edit', 'ihc' );?>" href="<?php echo admin_url( 'admin.php?page=ihc_manage&tab=order-edit&order_id=' . esc_attr($array['id']) );?>" >
						<i class="fa-ihc ihc-icon-edit-e"></i>
					</a>
					<?php if ( isset( $array['metas']['ihc_payment_type'] )
								&& in_array( $array['metas']['ihc_payment_type'], [ 'stripe', 'paypal', 'paypal_express_checkout', 'stripe_checkout_v2', 'stripe_connect', 'mollie', 'twocheckout','braintree' , 'authorize' ] ) ) :?>
						<?php
						$chargingPlan = '';
						$refundLink = '';
						$subscriptionId = $orderMeta->get( $array['id'], 'subscription_id' );
						switch ( $array['metas']['ihc_payment_type'] ){
								case 'paypal':
									if ( get_option( 'ihc_paypal_sandbox' ) ){
										if ( $subscriptionId != '' ){
												$chargingPlan = 'https://www.sandbox.paypal.com/billing/subscriptions/' . esc_attr($subscriptionId);
										}
					          $refundLink = 'https://www.sandbox.paypal.com/activity/actions/refund/edit/' . esc_attr($transactionId);
					        } else {
										if ( $subscriptionId != '' ){
												$chargingPlan = 'https://www.paypal.com/billing/subscriptions/' . esc_attr($subscriptionId);
										}
					          $refundLink = 'https://www.paypal.com/activity/actions/refund/edit/' . esc_attr($transactionId);
					        }
									break;
								case 'paypal_express_checkout':
									if ( get_option( 'ihc_paypal_express_checkout_sandbox' ) ){
										if ( $subscriptionId != '' ){
												$chargingPlan = 'https://www.sandbox.paypal.com/billing/subscriptions/' . esc_attr($subscriptionId);
										}
										$refundLink = 'https://www.sandbox.paypal.com/activity/actions/refund/edit/' . esc_attr($transactionId);
									} else {
										if ( $subscriptionId != '' ){
												$chargingPlan = 'https://www.paypal.com/billing/subscriptions/' . esc_attr($subscriptionId);
										}
										$refundLink = 'https://www.paypal.com/activity/actions/refund/edit/' . esc_attr($transactionId);
									}
									break;
								case 'stripe':

									break;
								case 'stripe_checkout_v2':
									$key = get_option( 'ihc_stripe_checkout_v2_publishable_key' );
									if ( strpos( $key, 'pk_test' ) !== false ){
										if ( $subscriptionId != '' ){
												$chargingPlan = 'https://dashboard.stripe.com/test/subscriptions/' . esc_attr($subscriptionId);
										}
										$refundLink = 'https://dashboard.stripe.com/test/payments/' . esc_attr($transactionId);
									} else {
										if ( $subscriptionId != '' ){
												$chargingPlan = 'https://dashboard.stripe.com/subscriptions/' . esc_attr($subscriptionId);
										}
										$refundLink = 'https://dashboard.stripe.com/payments/' . esc_attr($transactionId);
									}
									break;
								case 'stripe_connect':
									$key = get_option( 'ihc_stripe_connect_live_mode' );
									if ( $key == false ){
										if ( $subscriptionId != '' ){
												$chargingPlan = 'https://dashboard.stripe.com/test/subscriptions/' . esc_attr($subscriptionId);
										}
										$refundLink = 'https://dashboard.stripe.com/test/payments/' . esc_attr($transactionId);
									} else {
										if ( $subscriptionId != '' ){
												$chargingPlan = 'https://dashboard.stripe.com/subscriptions/' . esc_attr($subscriptionId);
										}
										$refundLink = 'https://dashboard.stripe.com/payments/' . esc_attr($transactionId);
									}
									break;
								case 'mollie':
									$customerId = $orderMeta->get( $array['id'], 'customer_id' );
									if ( $customerId != '' ){
											$chargingPlan = 'https://www.mollie.com/dashboard/customers/' . esc_attr($customerId);
									}
									$refundLink = 'https://www.mollie.com/dashboard/payments/' . esc_attr($transactionId);
									break;
								case 'twocheckout':
									if ( $subscriptionId != '' ){
											$chargingPlan = 'https://secure.2checkout.com/cpanel/license_info.php?refno=' . esc_attr($subscriptionId);
									}
									break;
								case 'braintree':
								if(get_option( 'ihc_braintree_merchant_id' )){
									$merchantID = get_option( 'ihc_braintree_merchant_id' );
									if ( $subscriptionId != '' ){
										if ( get_option( 'ihc_braintree_sandbox' )){
											$chargingPlan = 'https://sandbox.braintreegateway.com/merchants/'.esc_attr($merchantID).'/subscriptions/' . esc_attr($subscriptionId);
										}else{
											$chargingPlan = 'https://braintreegateway.com/merchants/'.esc_attr($merchantID).'/subscriptions/' . esc_attr($subscriptionId);
										}
									}
								}
								break;
								case 'authorize':
									if ( $subscriptionId != '' ){
										if ( get_option( 'ihc_authorize_sandbox' ) == 1){
											$chargingPlan = 'https://sandbox.authorize.net/ui/themes/sandbox/ARB/SubscriptionDetail.aspx?SubscrID=' . esc_attr($subscriptionId);
										}else{
											$chargingPlan = 'https://authorize.net/ui/themes/ARB/SubscriptionDetail.aspx?SubscrID=' . esc_attr($subscriptionId);
										}
									}
								break;
						}
						if ( $refundLink != '' ):?>
							<a title="<?php esc_html_e( 'Refund', 'ihc' );?>" href="<?php echo esc_url($refundLink);?>" target="_blank" ><i class="fa-ihc ihc-icon-refund-e"></i></a>
						<?php endif;?>

						<?php if ( $chargingPlan != '' ):?>
							<a title="<?php echo esc_html__( 'Check Charging plan on ', 'ihc' ) . esc_attr($payment_gateway);?>" href="<?php echo esc_html($chargingPlan);?>" target="_blank" ><i class="fa-ihc ihc-icon-plan-e"></i></a>
						<?php endif;?>

					<?php endif;?>

							<span class="ihc-pointer ihc-js-delete-order" data-id="<?php echo esc_attr($array['id']);?>" title="<?php esc_html_e( 'Remove', 'ihc' );?>" >
									<i class="fa-ihc ihc-icon-remove-e"></i>
							</span>
			</td>
		</tr>
	<?php
		$i++;
	 endforeach;?>

</table>
</div>
<?php endif;?>
</div>
