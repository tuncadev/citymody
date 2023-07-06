<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
global $current_user;
$current_user = wp_get_current_user();
$user_id = $current_user->ID;
$package_id = isset($_GET['package_id']) ? civi_clean(wp_unslash($_GET['package_id'])) : '';
$user_package_id = get_the_author_meta(CIVI_METABOX_PREFIX . 'package_id', $user_id);
$civi_profile = new Civi_Profile();
$check_package = $civi_profile->user_package_available($user_id);

$package_free = get_post_meta($package_id, CIVI_METABOX_PREFIX . 'package_free', true);

if ($package_free == 1) {
	$package_price = 0;
} else {
	$package_price = get_post_meta($package_id, CIVI_METABOX_PREFIX . 'package_price', true);
}
$package_time_unit = get_post_meta($package_id, CIVI_METABOX_PREFIX . 'package_time_unit', true);
$package_num_job = get_post_meta($package_id, CIVI_METABOX_PREFIX . 'package_number_job', true);
$package_period = get_post_meta($package_id, CIVI_METABOX_PREFIX . 'package_period', true);
$package_unlimited_job = get_post_meta($package_id, CIVI_METABOX_PREFIX . 'package_unlimited_job', true);
$package_unlimited_time = get_post_meta($package_id, CIVI_METABOX_PREFIX . 'package_unlimited_time', true);
$package_featured_job = get_post_meta($package_id, CIVI_METABOX_PREFIX . 'package_unlimited_job_featured', true);
$package_num_featured_job = get_post_meta($package_id, CIVI_METABOX_PREFIX . 'package_number_featured', true);
$package_featured = get_post_meta($package_id, CIVI_METABOX_PREFIX . 'package_featured', true);
$package_title = get_the_title($package_id);
$package_billing_frquency = get_post_meta($package_id, CIVI_METABOX_PREFIX . 'package_period', true);
$package_additional = get_post_meta($package_id, CIVI_METABOX_PREFIX . 'package_additional_details', true);
if ($package_additional > 0) {
	$package_additional_text = get_post_meta($package_id, CIVI_METABOX_PREFIX . 'package_details_text', true);
}

if ($package_billing_frquency > 1) {
	$package_time_unit .= 's';
}
if ($package_period > 1) {
	$package_time_unit .= 's';
}
if ($package_featured == 1) {
	$is_featured = ' active';
} else {
	$is_featured = '';
}
$terms_condition = civi_get_option('terms_condition');
$allowed_html = array(
	'a' => array(
		'href' => array(),
		'title' => array(),
		'target' => array()
	),
	'strong' => array()
);
$enable_paypal = civi_get_option('enable_paypal', 1);
$enable_stripe = civi_get_option('enable_stripe', 1);
$enable_woocheckout = civi_get_option('enable_woocheckout', 1);
$enable_wire_transfer = civi_get_option('enable_wire_transfer', 1);
$select_packages_link = civi_get_permalink('package');
?>

<div class="row">
	<div class="col-lg-8 col-md-7 col-sm-6">
		<?php if (($package_id == $user_package_id) && $check_package == 1) : ?>
			<div class="entry-heading">
				<h2 class="entry-title"><?php esc_html_e('Checked Package', 'civi-framework'); ?></h2>
			</div>

			<div class="alert alert-warning" role="alert"><?php echo sprintf(__('You currently have "%s" package. The package hasn\'t expired yet, so you cannot buy it at this time. If you would like, you can buy another package.', 'civi-framework'), $package_title); ?></div>
		<?php else : ?>

			<?php if ($package_price > 0) : ?>
				<div class="civi-payment-method-wrap">
					<div class="entry-heading">
						<h2 class="entry-title"><?php esc_html_e('Payment Method', 'civi-framework'); ?></h2>
					</div>
					<?php if ($enable_paypal != 0) : ?>
						<div class="radio active">
							<label>
								<input type="radio" class="payment-paypal" name="civi_payment_method" value="paypal" checked>
								<img src="<?php echo esc_attr(CIVI_PLUGIN_URL . 'assets/images/paypal.png'); ?>" alt="<?php esc_html_e('Paypal', 'civi-framework'); ?>">
								<?php esc_html_e('Pay With Paypal', 'civi-framework'); ?>
							</label>
						</div>
					<?php endif; ?>

					<?php if ($enable_stripe != 0) : ?>
						<div class="radio">
							<label>
								<input type="radio" class="payment-stripe" name="civi_payment_method" value="stripe">
								<img src="<?php echo esc_attr(CIVI_PLUGIN_URL . 'assets/images/stripe.png'); ?>" alt="<?php esc_html_e('Stripe', 'civi-framework'); ?>">
								<?php esc_html_e('Pay with Credit Card', 'civi-framework'); ?>
							</label>
							<?php
							$civi_payment = new Civi_Payment();
							$civi_payment->stripe_payment_per_package($package_id);
							?>
						</div>
					<?php endif; ?>

                    <?php if ($enable_woocheckout != 0): ?>
                        <div class="radio">
                            <label>
                                <input type="radio" class="payment-woocheckout" name="civi_payment_method" value="woocheckout">
                                <img src="<?php echo esc_attr(CIVI_PLUGIN_URL . 'assets/images/woocommerce-logo.png'); ?>" alt="<?php esc_html_e('Woocommerce', 'civi-framework'); ?>">
                                <?php esc_html_e('Pay with Woocommerce', 'civi-framework'); ?>
                            </label>
                        </div>
                    <?php endif; ?>

					<?php if ($enable_wire_transfer != 0) : ?>
						<div class="radio wire-transfer">
							<label>
								<input type="radio" name="civi_payment_method" value="wire_transfer">
								<i class="fas fa-window-restore"></i><?php esc_html_e('Wire Transfer', 'civi-framework'); ?>
							</label>
						</div>
						<div class="civi-wire-transfer-info">
							<?php
							$html_info = civi_get_option('wire_transfer_info', '');
							echo wpautop($html_info);
							?>
						</div>
					<?php endif; ?>

				</div>
			<?php endif; ?>
			<input type="hidden" name="civi_package_id" value="<?php echo esc_attr($package_id); ?>">

			<p class="terms-conditions"><i class="fa fa-hand-o-right"></i> <?php echo sprintf(wp_kses(__('Please read <a target="_blank" href="%s"><strong>Terms & Conditions</strong></a> first', 'civi-framework'), $allowed_html), get_permalink($terms_condition)); ?></p>
			<?php if ($package_price > 0) : ?>
				<button id="civi_payment_package" type="submit" class="btn btn-success btn-submit gl-button"><?php esc_html_e('Pay Now', 'civi-framework'); ?></button>
				<?php else :
				$user_free_package = get_the_author_meta(CIVI_METABOX_PREFIX . 'free_package', $user_id);
				if ($user_free_package == 'yes' && $check_package == 1) : ?>
					<div class="civi-message alert alert-warning" role="alert"><?php esc_html_e('You have already used your first free package, please choose different package.', 'civi-framework'); ?></div>
				<?php else : ?>
					<button id="civi_free_package" type="submit" class="btn btn-success btn-submit civi-button"><?php esc_html_e('Get Free Listing Package', 'civi-framework'); ?></button>
				<?php endif; ?>
			<?php endif; ?>

		<?php endif; ?>
	</div>

	<div class="col-lg-4 col-md-5 col-sm-6">
		<div class="civi-payment-for civi-package-wrap panel panel-default">
			<div class="entry-heading">
				<h2 class="entry-title"><?php esc_html_e('Selected Package', 'civi-framework'); ?></h2>
			</div>
			<div class="civi-package-item panel panel-default <?php echo esc_attr($is_featured); ?>">
				<?php if (has_post_thumbnail($package_id)) : ?>
					<div class="civi-package-thumbnail"><?php echo get_the_post_thumbnail($package_id); ?></div>
				<?php endif; ?>

				<div class="civi-package-title">
					<h2 class="entry-title"><?php echo get_the_title($package_id); ?></h2>
				</div>

				<ul class="list-group">
					<li class="list-group-item">
						<i class="fas fa-check"></i>
						<span class="badge">
							<?php if ($package_unlimited_job == 1) {
								esc_html_e('Unlimited', 'civi-framework');
							} else {
								esc_html_e($package_num_job);
							} ?>
						</span>
						<?php esc_html_e('job posting', 'civi-framework'); ?>
					</li>

					<li class="list-group-item">
						<i class="fas fa-check"></i>
						<span class="badge">
							<?php if ($package_featured_job == 1) {
								esc_html_e('Unlimited', 'civi-framework');
							} else {
								esc_html_e($package_num_featured_job);
							} ?>
						</span>
						<?php esc_html_e('featured job', 'civi-framework') ?>
					</li>
					<li class="list-group-item">
						<i class="fas fa-check"></i>
						<?php esc_html_e('Job post live for', 'civi-framework'); ?>
						<span class="badge">
							<?php if ($package_unlimited_time == 1) {
								esc_html_e('never expires', 'civi-framework');
							} else {
                                esc_html_e($package_period . ' ' . Civi_Package::get_time_unit($package_time_unit));
							}
							?>
						</span>
					</li>
					<?php if ($package_additional > 0) {
						foreach ($package_additional_text as $value) { ?>
							<li class="list-group-item">
								<i class="fas fa-check"></i>
								<span class="badge">
									<?php esc_html_e($value); ?>
								</span>
							</li>
					<?php }
					} ?>
				</ul>

				<div class="civi-total-price">
					<span><?php esc_html_e('Total', 'civi-framework'); ?></span>
					<span class="price">
						<?php
						if ($package_price > 0) {
							echo civi_get_format_money($package_price, '', 2, true);
						} else {
							esc_html_e('Free', 'civi-framework');
						}
						?>
					</span>
				</div>

				<a class="civi-button" href="<?php echo esc_url($select_packages_link); ?>"><?php esc_html_e('Change Package', 'civi-framework'); ?></a>
			</div>
		</div>
	</div>
</div>
