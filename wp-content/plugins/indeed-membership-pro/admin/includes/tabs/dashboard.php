<?php
$currency = get_option('ihc_currency');
echo ihc_inside_dashboard_error_license();
do_action( "ihc_admin_dashboard_after_top_menu" );
$ordersObject = new \Indeed\Ihc\Db\Orders();
$currency = get_option( 'ihc_currency' );
?>
<div>
	<div class="iump-page-title">
		Ultimate Membership Pro -
		<span class="second-text">
			<?php esc_html_e('Dashboard Overall', 'ihc');?>
		</span>
	</div>
<div class="ihc-dashboard-wrapper">

	<div class="ihc-dashboard-row-title"><?php esc_html_e('Last 30 days', 'ihc');?></div>
	<div class="row-fluid">

		<div class="span4">
			<div class="ihc-dashboard-box-wrapper">
				<div class="ihc-dashboard-box-top-section">
					<i class="fa-ihc fa-ihc-dashboard fa-users-ihc"></i>
					<div class="ihc-dashboard-stats">
						<?php
								$percentage = false;
								$start = time() - 30*24*60*60;// 30days
								$end = time();
								$lastThirty = \Indeed\Ihc\Db\Users::countInInterval( $start, $end );

								$start = time() - 60*24*60*60;// 60days
								$end = time() - 30*24*60*60;// 30days
								$beforeLastThirty = \Indeed\Ihc\Db\Users::countInInterval( $start, $end );

								if ( $beforeLastThirty > 0 ){
										$percentage = $beforeLastThirty / 100;
										$percentage = $lastThirty / $percentage;
										$percentage = round( $percentage, 1 );
										$percentage = $percentage - 100;
								}
						?>
						<div class="ihc-dashboard-stats-title"><?php esc_html_e('Members', 'ihc');?></div>
						<span class="ihc-dashboard-stats-count"><?php echo esc_html($lastThirty);?></span>
							<?php if ( $percentage !== false ):?>
								<?php $extraClass = $percentage > -0.01 ? 'ihc-dashboard-stats-trendup' : 'ihc-dashboard-stats-trenddown';?>
								<span class="ihc-dashboard-stats-trend <?php echo esc_attr($extraClass);?>">
									<i class="fa-ihc fa-arrow-ihc"></i>
									<?php echo esc_html($percentage);?>
									<span>%</span>
								</span>
							<?php endif;?>
					</div>
				</div>
				<div class="ihc-dashboard-box-bottom-section">
					<a href="<?php echo admin_url( 'admin.php?page=ihc_manage&tab=users' );?>"><?php esc_html_e('View all', 'ihc');?></a>
				</div>
			</div>
		</div>

		<div class="span4">
			<div class="ihc-dashboard-box-wrapper">
				<div class="ihc-dashboard-box-top-section">
					<i class="fa-ihc fa-ihc-dashboard fa-levels-ihc"></i>
					<div class="ihc-dashboard-stats">
						<?php
								$percentage = false;
								$start = time() - 30*24*60*60;// 30days
								$end = time();
								$lastThirty = \Indeed\Ihc\UserSubscriptions::countInInterval( $start, $end );

								$start = time() - 60*24*60*60;// 60days
								$end = time() - 30*24*60*60;// 30days
								$beforeLastThirty = \Indeed\Ihc\UserSubscriptions::countInInterval( $start, $end );

								if ( $beforeLastThirty > 0 ){
										$percentage = $beforeLastThirty / 100;
										$percentage = $lastThirty / $percentage;
										$percentage = round( $percentage, 1 );
										$percentage = $percentage - 100;
								}
						?>
						<div class="ihc-dashboard-stats-title"><?php esc_html_e('Memberships', 'ihc');?></div>
						<span class="ihc-dashboard-stats-count"><?php echo esc_html($lastThirty);?></span>

						<?php if ( $percentage !== false ):?>
							<?php $extraClass = $percentage > -0.01 ? 'ihc-dashboard-stats-trendup' : 'ihc-dashboard-stats-trenddown';?>
							<span class="ihc-dashboard-stats-trend <?php echo esc_attr($extraClass);?>">
								<i class="fa-ihc fa-arrow-ihc"></i>
								<?php echo esc_html($percentage);?>
								<span>%</span>
							</span>
						<?php endif;?>

					</div>
				</div>
				<div class="ihc-dashboard-box-bottom-section">
					<a href="<?php echo admin_url( 'admin.php?page=ihc_manage&tab=users' );?>"><?php esc_html_e('View all', 'ihc');?></a>
				</div>
			</div>
		</div>

		<div class="span4">
			<div class="ihc-dashboard-box-wrapper">
				<div class="ihc-dashboard-box-top-section">
					<i class="fa-ihc fa-ihc-dashboard fa-payment_settings-ihc"></i>
					<div class="ihc-dashboard-stats">
						<?php
								$percentage = false;
								$start = time() - 30*24*60*60;// 30days
								$end = time();
								$lastThirty = $ordersObject->getTotalAmountInInterval( $start, $end );

								$start = time() - 60*24*60*60;// 60days
								$end = time() - 30*24*60*60;// 30days
								$beforeLastThirty = $ordersObject->getTotalAmountInInterval( $start, $end );
								if ( $beforeLastThirty > 0 ){
										$percentage = $beforeLastThirty / 100;
										$percentage = $lastThirty / $percentage;
										$percentage = round( $percentage, 1 );
										$percentage = $percentage - 100;
								}
						?>
						<div class="ihc-dashboard-stats-title"><?php esc_html_e('Earnings', 'ihc');?></div>
						<span class="ihc-dashboard-stats-count"><?php echo ihc_format_price_and_currency( $currency, $lastThirty );?></span>
						<?php if ( $percentage !== false ):?>
							<?php $extraClass = $percentage > -0.01 ? 'ihc-dashboard-stats-trendup' : 'ihc-dashboard-stats-trenddown';?>
							<span class="ihc-dashboard-stats-trend <?php echo esc_attr($extraClass);?>">
								<i class="fa-ihc fa-arrow-ihc"></i>
								<?php echo esc_html($percentage);?>
								<span>%</span>
							</span>
						<?php endif;?>
					</div>
				</div>
				<div class="ihc-dashboard-box-bottom-section">
					<a href="<?php echo admin_url( 'admin.php?page=ihc_manage&tab=orders' );?>"><?php esc_html_e('View all', 'ihc');?></a>
				</div>
			</div>
		</div>

	</div>

	<div class="ihc-dashboard-row-title"><?php esc_html_e('All-time', 'ihc');?></div>
	<div class="row-fluid">

		<div class="span3">
			<div class="ihc-dashboard-box-wrapper">
				<div class="ihc-dashboard-box-top-section">
					<i class="fa-ihc fa-ihc-dashboard fa-users-ihc"></i>
					<div class="ihc-dashboard-stats">
						<div class="ihc-dashboard-stats-title"><?php esc_html_e('Members', 'ihc');?></div>
						<span class="ihc-dashboard-stats-count"><?php echo \Indeed\Ihc\Db\Users::countAll();?></span>
					</div>
				</div>
				<div class="ihc-dashboard-box-bottom-section">
					<a href="<?php echo admin_url( 'admin.php?page=ihc_manage&tab=users' );?>"><?php esc_html_e('View all', 'ihc');?></a>
				</div>
			</div>
		</div>

		<div class="span3">
			<div class="ihc-dashboard-box-wrapper">
				<div class="ihc-dashboard-box-top-section">
					<i class="fa-ihc fa-ihc-dashboard fa-levels-ihc"></i>
					<div class="ihc-dashboard-stats">
						<div class="ihc-dashboard-stats-title"><?php esc_html_e('Memberships', 'ihc');?></div>
						<span class="ihc-dashboard-stats-count"><?php echo \Indeed\Ihc\UserSubscriptions::getCount();?></span>
					</div>
				</div>
				<div class="ihc-dashboard-box-bottom-section">
					<a href="<?php echo admin_url( 'admin.php?page=ihc_manage&tab=users' );?>"><?php esc_html_e('View all', 'ihc');?></a>
				</div>
			</div>
		</div>

		<div class="span3">
			<div class="ihc-dashboard-box-wrapper">
				<div class="ihc-dashboard-box-top-section">
					<i class="fa-ihc fa-ihc-dashboard fa-payment_settings-ihc"></i>
					<div class="ihc-dashboard-stats">
						<div class="ihc-dashboard-stats-title"><?php esc_html_e('Earnings', 'ihc');?></div>
						<span class="ihc-dashboard-stats-count"><?php
									echo ihc_format_price_and_currency( $currency, $ordersObject->getTotalAmount() );
							?></span>
					</div>
				</div>
				<div class="ihc-dashboard-box-bottom-section">
					<a href="<?php echo admin_url( 'admin.php?page=ihc_manage&tab=orders' );?>"><?php esc_html_e('View all', 'ihc');?></a>
				</div>
			</div>
		</div>

		<div class="span3">
			<div class="ihc-dashboard-box-wrapper">
				<div class="ihc-dashboard-box-top-section">
					<i class="fa-ihc fa-ihc-dashboard fa-payments-ihc"></i>
					<div class="ihc-dashboard-stats">
						<div class="ihc-dashboard-stats-title"><?php esc_html_e('Orders', 'ihc');?></div>
						<span class="ihc-dashboard-stats-count"><?php	echo esc_html($ordersObject->getCountAll());?></span>
					</div>
				</div>
				<div class="ihc-dashboard-box-bottom-section">
					<a href="<?php echo admin_url( 'admin.php?page=ihc_manage&tab=orders' );?>"><?php esc_html_e('View all', 'ihc');?></a>
				</div>
			</div>
		</div>

	</div>

	<div class="ihc-dashboard-row-title"><?php esc_html_e('Overall Earnings', 'ihc');?></div>
	<div class="row-fluid">
		<div class="span12">
			<div class="ihc-dashboard-earnings-graph-wrapper">
					<?php
							$timePassed = $ordersObject->getFirstOrderDaysPassed();

							$startTime = time() - $timePassed * 24 * 60 * 60;

							if ( $timePassed < 31 ){
									// days
									$earnings_arr = $ordersObject->getTotalAmountInLastTime( $startTime, 'days' );
							} else if ( $timePassed > 30 && $timePassed < 181 ){
									// weeks
									$earnings_arr = $ordersObject->getTotalAmountInLastTime( $startTime, 'weeks' );
							} else if ( $timePassed > 180 && $timePassed < 721 ){
									// months
									$earnings_arr = $ordersObject->getTotalAmountInLastTime( $startTime, 'months' );
							} else if ( $timePassed > 720 ){
									// years
									$earnings_arr = $ordersObject->getTotalAmountInLastTime( $startTime, 'years' );
							}
							if ( count( $earnings_arr ) > 18 ){
									$extraClass = 'flot-tick-label-rotate';
							} else {
									$extraClass =  '';
							}
					?>
					<div id="ihc-chart-earnings" class='ihc-flot <?php echo esc_attr($extraClass);?>'></div>

					<?php if ($earnings_arr):	?>
							<?php foreach ( $earnings_arr as $k => $v ):?>
									<?php
											$date = $v->the_time;
											$sum = $v->sum_value;
									?>
									<span class="ihc-js-dashboard-earnings-data" data-date="<?php echo esc_attr($date);?>" data-sum="<?php echo esc_attr($sum);?>"></span>
							<?php endforeach;?>
					<?php endif;?>

			</div>
		</div>
	</div>

</div>


</div>
<?php
