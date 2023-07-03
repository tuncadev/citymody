<?php
$enable_captcha = Civi_Helper::civi_get_option('enable_captcha');
$captcha = rand(1000, 9999);

global $current_user;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
if (isset($_GET['action']) && $_GET['action'] == 'rp') {
	$class_open = 'open';
} else {
	$class_open = '';
}

?>
<div class="popup popup-account <?php esc_html_e($class_open) ?>" id="popup-form">
	<div class="bg-overlay"></div>
	<div class="inner-popup custom-scrollbar">
		<a href="#" class="btn-close">
			<i class="far fa-times large"></i>
		</a>
		<div class="head-popup">
			<div class="tabs-form">
				<a class="btn-login active" href="#ux-login" data-form="ux-login"><?php esc_html_e('Log in', 'civi'); ?></a>
				<a class="btn-register" href="#ux-register" data-form="ux-register"><?php esc_html_e('Sign Up', 'civi'); ?></a>
				<div class="loading-effect"><span class="civi-dual-ring"></span></div>
			</div>

			<?php if (is_user_logged_in()) { ?>
				<p class="notice"><i class="fal fa-exclamation-circle"></i><?php esc_html_e('Please login role Candidate to view', 'civi'); ?></p>
			<?php } ?>
		</div>

		<div class="body-popup">

			<?php
			if (isset($_GET['action']) && $_GET['action'] == 'rp') :
			?>

				<div class="civi-new-password-wrap">
					<form action="#" method="post">
						<div class="form-group control-password">
							<input name="new_password" type="text" id="new-password" class="form-control control-icon" placeholder="<?php esc_attr_e('Enter new password', 'civi'); ?>">
							<span><i class="fas fa-eye"></i></span>
						</div>
						<div class="button-wrap">
							<a href="#" class="generate-password"><?php esc_html_e('Generate Password', 'civi'); ?></a>
							<button type="submit" id="civi_newpass" class="btn gl-button"><?php esc_html_e('Save password', 'civi'); ?></button>
							<input type="hidden" name="login" id="login" value="<?php esc_html_e($_GET['login']) ?>">
							<p class="msg"><?php esc_html_e('Sending info,please wait...', 'civi'); ?></p>
						</div>
					</form>
				</div>

			<?php else : ?>

				<form action="#" id="ux-login" class="form-account active ux-login" method="post">

					<?php do_action('civi_user_demo_sign_in'); ?>

					<div class="form-group">
						<label for="ip_email" class="label-field"><?php esc_html_e('Account or Email', 'civi'); ?></label>
						<input type="text" id="ip_email" class="form-control input-field" name="email" placeholder="<?php esc_attr_e('Enter Account or Email', 'civi') ?>">
					</div>
					<div class="form-group">
						<label for="ip_password" class="label-field"><?php esc_html_e('Password', 'civi'); ?></label>
						<input type="password" id="ip_password" class="form-control input-field" name="password" autocomplete="on" placeholder="<?php esc_attr_e('Enter Password', 'civi') ?>">
					</div>

					<?php
					$enable_captcha = Civi_Helper::civi_get_option('enable_captcha');
					if ($enable_captcha) :
					?>
						<div class="form-group form-captcha">
							<input type="text" class="form-control civi-captcha" name="ip_captcha" />
							<input type="hidden" class="form-control civi-num-captcha" name="ip_num_captcha" data-captcha="<?php echo $captcha; ?>" />
							<?php Civi_Helper::civi_image_captcha($captcha); ?>
						</div>
					<?php endif; ?>

					<p class="msg"><?php esc_html_e('Sending login info,please wait...', 'civi'); ?></p>

					<div class="form-group">
						<div class="forgot-password">
							<span><?php esc_html_e('Forgot your password? ', 'civi'); ?></span>
							<a class="btn-reset-password" href="#"><?php esc_html_e('Reset password.', 'civi'); ?></a>
						</div>
					</div>

					<div class="form-group">
						<button type="submit" class="gl-button btn button" value="<?php esc_attr_e('Sign in', 'civi'); ?>"><?php esc_html_e('Sign in', 'civi'); ?></button>
					</div>
				</form>

				<div class="civi-reset-password-wrap form-account">
					<div id="civi_messages_reset_password" class="civi_messages message"></div>
					<form method="post" enctype="multipart/form-data">
						<div class="form-group control-username">
							<input name="user_login" id="user_login" class="form-control control-icon" placeholder="<?php esc_attr_e('Enter your username or email', 'civi'); ?>">
							<?php wp_nonce_field('civi_reset_password_ajax_nonce', 'civi_security_reset_password'); ?>
							<input type="hidden" name="action" id="reset_password_action" value="civi_reset_password_ajax">
							<p class="msg"><?php esc_html_e('Sending info,please wait...', 'civi'); ?></p>
							<button type="submit" id="civi_forgetpass" class="btn gl-button"><?php esc_html_e('Get new password', 'civi'); ?></button>
						</div>
					</form>
					<a class="back-to-login" href="#"><i class="fas fa-arrow-left"></i><?php esc_html_e('Back to login', 'civi'); ?></a>
				</div>

				<form action="#" id="ux-register" class="form-account ux-register" method="post">
					<?php
					$enable_user_role = Civi_Helper::civi_get_option('enable_user_role', '1');
					if ($enable_user_role) {
					?>
						<div class="form-group">
							<div class="row">
								<div class="col-6">
									<div class="col-group">
										<label for="civi_user_candidate" class="label-field radio-field">
											<input type="radio" value="civi_user_candidate" id="civi_user_candidate" name="account_type">
											<span><i class="fal fa-user"></i><?php esc_html_e('Candidate', 'civi'); ?></span>
										</label>
									</div>
								</div>
								<div class="col-6">
									<div class="col-group">
										<label for="civi_user_employer" class="label-field radio-field">
											<input type="radio" value="civi_user_employer" id="civi_user_employer" name="account_type" checked>
											<span><i class="fal fa-briefcase"></i><?php esc_html_e('Employer', 'civi'); ?></span>
										</label>
									</div>
								</div>
							</div>
						</div>
					<?php } else { ?>
						<input type="radio" checked value="civi_user_candidate" id="civi_user_candidate" name="account_type" class="hide">
					<?php } ?>
					<div class="form-group">
						<div class="row">
							<div class="col-6">
								<div class="col-group">
									<label for="ip_reg_firstname" class="label-field"><?php esc_html_e('First Name', 'civi'); ?></label>
									<input type="text" id="ip_reg_firstname" class="form-control input-field" name="reg_firstname" placeholder="<?php esc_attr_e('Name', 'civi') ?>">
								</div>
							</div>
							<div class="col-6">
								<div class="col-group">
									<label for="ip_reg_lastname" class="label-field"><?php esc_html_e('Last Name', 'civi'); ?></label>
									<input type="text" id="ip_reg_lastname" class="form-control input-field" name="reg_lastname" placeholder="<?php esc_attr_e('Last Name', 'civi') ?>">
								</div>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="ip_reg_company_name" class="label-field"><?php esc_html_e('Username', 'civi'); ?></label>
						<input type="text" id="ip_reg_company_name" class="form-control input-field" name="reg_company_name" placeholder="<?php esc_attr_e('Enter Username', 'civi') ?>">
					</div>
					<div class="form-group">
						<label for="ip_reg_email" class="label-field"><?php esc_html_e('Email', 'civi'); ?></label>
						<input type="email" id="ip_reg_email" class="form-control input-field" name="reg_email" placeholder="<?php esc_attr_e('Enter Email', 'civi') ?>">
					</div>
					<div class="form-group">
						<label for="ip_reg_password" class="label-field"><?php esc_html_e('Password', 'civi'); ?></label>
						<input type="password" id="ip_reg_password" class="form-control input-field" name="reg_password" autocomplete="on" placeholder="<?php esc_attr_e('Enter Password', 'civi') ?>">
					</div>
					<?php
					$enable_captcha = Civi_Helper::civi_get_option('enable_captcha');
					if ($enable_captcha) :
					?>
						<div class="form-group form-captcha">
							<input type="text" class="form-control civi-captcha" name="ip_captcha" />
							<input type="hidden" class="form-control civi-num-captcha" name="ip_num_captcha" data-captcha="<?php echo $captcha; ?>" />
							<?php Civi_Helper::civi_image_captcha($captcha); ?>
						</div>
					<?php endif; ?>

					<div class="form-group accept-account">
						<?php
						$terms_condition 	= Civi_Helper::civi_get_option('terms_condition');
						$privacy_policy = Civi_Helper::civi_get_option('privacy_policy');
						?>
						<input type="checkbox" id="ip_accept_account" class="form-control custom-checkbox" name="accept_account">
						<label for="ip_accept_account"><?php printf(esc_html__('Accept the %1$s and %2$s', 'civi'), '<a href="' . get_permalink($terms_condition) . '">' . esc_html__('Terms', 'civi') . '</a>', '<a href="' . get_permalink($privacy_policy) . '">' . esc_html__('Privacy Policy', 'civi') . '</a>'); ?></label>
					</div>

					<p class="msg"><?php esc_html_e('Sending register info,please wait...', 'civi'); ?></p>

					<div class="form-group">
						<button type="submit" class="gl-button btn button" value="<?php esc_attr_e('Sign in', 'civi'); ?>"><?php esc_html_e('Sign up', 'civi'); ?></button>
					</div>
				</form>

				<form action="#" id="ux-verify" class="form-account ux-verify" method="post">
					<div class="form-group">
						<label for="verify-code" class="label-field"><?php esc_html_e('Verify Code', 'civi'); ?></label>
						<input type="text" id="verify-code" class="form-control input-field" name="verify_code" placeholder="<?php esc_attr_e('Enter Code', 'civi') ?>">
					</div>
					<p class="msg"><?php esc_html_e('Sending register info,please wait...', 'civi'); ?></p>
					<p><?php esc_html_e('Please check your email and enter the verification code to activate your account.', 'civi'); ?></p>
					<div class="form-group">
						<button type="submit" class="gl-button btn button" value="<?php esc_attr_e('Verify', 'civi'); ?>"><?php esc_html_e('Verify', 'civi'); ?></button>
					</div>
				</form>

			<?php endif; ?>
		</div>

		<div class="footer-popup addon-login-wrap">
			<?php
			$enable_social_login = Civi_Helper::civi_get_option('enable_social_login', '1');
			if (class_exists('Civi_Framework') && $enable_social_login) {
			?>

				<div class="addon-login">
					<?php esc_html_e('Or Continue with', 'civi'); ?>
				</div>

				<ul>
					<li><a class="facebook-login" href="#"><i class="fab fa-facebook-f"></i></a></li>
					<li><a class="google-login" href="#"><i class="fab fa-google"></i></a></li>
					<li><a class="linkedin-login" href="<?php echo esc_url(Civi_LinkedIn::getAuthUrl()); ?>"><i class="fab fa-linkedin-in"></i></a></li>
				</ul>

			<?php } ?>
		</div>
	</div>
</div>