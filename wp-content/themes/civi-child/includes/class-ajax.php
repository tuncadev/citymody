<?php
if (!defined('ABSPATH')) {
	exit;
}

if (!class_exists('Civi_Ajax_Include')) {

	/**
	 *  Class Civi_Ajax
	 */
	class Civi_Ajax_Include
	{

		/**
		 * The constructor.
		 */
		public function __construct()
		{
			add_action('wp_ajax_get_login_user', array($this, 'get_login_user'));
			add_action('wp_ajax_nopriv_get_login_user', array($this, 'get_login_user'));

			add_action('wp_ajax_get_register_user', array($this, 'get_register_user'));
			add_action('wp_ajax_nopriv_get_register_user', array($this, 'get_register_user'));

			add_action('wp_ajax_verify_code', array($this, 'verify_code'));
			add_action('wp_ajax_nopriv_verify_code', array($this, 'verify_code'));

			add_action('wp_ajax_fb_ajax_login_or_register', array($this, 'fb_ajax_login_or_register'));
			add_action('wp_ajax_nopriv_fb_ajax_login_or_register', array($this, 'fb_ajax_login_or_register'));

			add_action('wp_ajax_google_ajax_login_or_register', array($this, 'google_ajax_login_or_register'));
			add_action('wp_ajax_nopriv_google_ajax_login_or_register', array($this, 'google_ajax_login_or_register'));

			add_action('wp_ajax_get_script_social_login', array($this, 'get_script_social_login'));
			add_action('wp_ajax_nopriv_get_script_social_login', array($this, 'get_script_social_login'));

			// Reset password
			add_action('wp_ajax_civi_reset_password_ajax', array($this, 'reset_password_ajax'));
			add_action('wp_ajax_nopriv_civi_reset_password_ajax', array($this, 'reset_password_ajax'));

			add_action('wp_ajax_change_password_ajax', array($this, 'change_password_ajax'));
			add_action('wp_ajax_nopriv_change_password_ajax', array($this, 'change_password_ajax'));
		}

		//////////////////////////////////////////////////////////////////
		// Ajax Login
		//////////////////////////////////////////////////////////////////
		function get_login_user()
		{
			$email    = $_POST['email'];
			$password = $_POST['password'];
			$captcha  = $_POST['captcha'];
			$num_captcha  = $_POST['num_captcha'];

			$user_login = $email;
			$url_redirect = '';
			$enable_redirect_after_login  = Civi_Helper::civi_get_option('enable_redirect_after_login');
			$redirect_for_admin  = Civi_Helper::civi_get_option('redirect_for_admin');
			$redirect_for_employer  = Civi_Helper::civi_get_option('redirect_for_employer');
			$redirect_for_candidate  = Civi_Helper::civi_get_option('redirect_for_candidate');

			if (is_email($email)) {
				$current_user = get_user_by('email', $email);
				$user_login   = $current_user->user_login;
			}

			$array = array();
			$array['user_login']    = $user_login;
			$array['user_password'] = $password;
			$array['remember']      = true;
			$user = wp_signon($array, false);

			$enable_captcha = Civi_Helper::civi_get_option('enable_captcha');
			if ($enable_captcha) {
				if (intval($captcha) == intval($num_captcha)) {
					$msg = esc_html__('Captcha success', 'civi');
				} else {
					$msg = esc_html__('Captcha failed', 'civi');
					echo json_encode(array('success' => false, 'messages' => $msg, 'class' => 'text-error'));
					wp_die();
				}
			}

			if (!is_wp_error($user)) {
				$users  = get_user_by('login', $user_login);
				if (in_array('civi_user_candidate', (array) $users->roles) && $enable_redirect_after_login && $redirect_for_candidate != '') {
					$url_redirect = get_page_link($redirect_for_candidate);
				} else if (in_array('civi_user_employer', (array) $users->roles) && $enable_redirect_after_login && $redirect_for_employer != '') {
					$url_redirect = get_page_link($redirect_for_employer);
				} else if (in_array('administrator', (array) $users->roles) && $enable_redirect_after_login && $redirect_for_admin != '') {
					$url_redirect = get_page_link($redirect_for_admin);
				}
				$msg = esc_html__('Login success', 'civi');

				echo json_encode(array('success' => true, 'messages' => $msg, 'class' => 'text-success', 'url_redirect' => $url_redirect));
			} else {
				$msg = esc_html__('Username or password is wrong. Please try again', 'civi');
				echo json_encode(array('success' => false, 'messages' => $msg, 'class' => 'text-error'));
			}
			wp_die();
		}

		//////////////////////////////////////////////////////////////////
		// Ajax Register
		//////////////////////////////////////////////////////////////////
		function get_register_user()
		{
			$account_type = $_POST['account_type'];
			$firstname    = $_POST['firstname'];
			$lastname     = $_POST['lastname'];
			$companyname  = $_POST['companyname'];
			$email        = $_POST['email'];
			$password     = $_POST['password'];
			$captcha      = $_POST['captcha'];
			$num_captcha  = $_POST['num_captcha'];
			$user_login   = $companyname;
			$url_redirect = '';
			$enable_redirect_after_login  = Civi_Helper::civi_get_option('enable_redirect_after_login');
			$verify_user_time  = Civi_Helper::civi_get_option('verify_user_time');
			$enable_verify_user  = Civi_Helper::civi_get_option('enable_verify_user');
			$redirect_for_admin  = Civi_Helper::civi_get_option('redirect_for_admin');
			$redirect_for_employer  = Civi_Helper::civi_get_option('redirect_for_employer');
			$redirect_for_candidate  = Civi_Helper::civi_get_option('redirect_for_candidate');
			$userdata = array(
				'user_login' 	=> $user_login,
				'first_name' 	=> $firstname,
				'last_name'  	=> $lastname,
				'display_name'	=> $companyname,
				'user_email' 	=> $email,
				'user_pass'  	=> $password,
				'account_type'  => $account_type
			);

			global $wpdb;
			$user = $wpdb->get_row(
				$wpdb->prepare(
					"SELECT user_login, user_email FROM {$wpdb->users} WHERE user_login = %s OR user_email = %s",
					$user_login,
					$email
				)
			);
			if ($user) {
				$msg = '';
				if ($user->user_login == $user_login) {
					$msg = esc_html__('Username already exists', 'civi');
				} else {
					$msg = esc_html__('Email already exists', 'civi');
				}
				echo json_encode(array('success' => false, 'messages' => $msg, 'class' => 'text-error'));
			} else {
				if ($enable_verify_user) {
					$cookie_name = trim($user_login);
					$cookie_value = rand(100000, 999999);
					setcookie($cookie_name, $cookie_value, time() + intval($verify_user_time), "/");
					$args = array(
						'code_verify_user' => $cookie_value,
					);
					Civi_Helper::civi_send_email($email, 'mail_verify_user', $args);
					echo json_encode(array(
						'success' => true,
						'cookie_name' => $cookie_name,
						'cookie_value' => $cookie_value,
						'user_login' => $user_login,
						'password' => $password,
						'email' => $email,
						'userdata' => $userdata,
						'class' => 'text-success',
						'url_redirect' => $url_redirect
					));
				} else {
					$user_id = wp_insert_user($userdata);
					if ($user_id == 0) {
						$user_login = substr($email,  0, strpos($email, '@'));
						$userdata = array(
							'user_login' 	=> $user_login,
							'first_name' 	=> $firstname,
							'last_name'  	=> $lastname,
							'display_name'	=> $companyname,
							'user_email' 	=> $email,
							'user_pass'  	=> $password,
							'account_type'  => $account_type
						);
						$user_id = wp_insert_user($userdata);
					}
					$msg = '';

					$enable_captcha = Civi_Helper::civi_get_option('enable_captcha');
					if ($enable_captcha) {
						if (intval($captcha) == intval($num_captcha)) {
							$msg = esc_html__('Captcha success', 'civi');
						} else {
							$msg = esc_html__('Captcha failed', 'civi');
							echo json_encode(array('success' => false, 'messages' => $msg, 'class' => 'text-error'));
							wp_die();
						}
					}

					if (!is_wp_error($user_id)) {
						if ($account_type == 'civi_user_employer') {
							$u = new WP_User($user_id);

							// Remove role
							if (get_role('civi_user_candidate')) {
								$u->remove_role('civi_user_candidate');
							}

							// Add role
							$u->add_role('civi_user_employer');
						}

						$creds = array();
						$creds['user_login']    = $user_login;
						$creds['user_email']    = $email;
						$creds['user_password'] = $password;
						$creds['remember']      = true;
						$user = wp_signon($creds, false);
						$msg  = esc_html__('Register success', 'civi');

						$admin_email = get_option('admin_email');

						$args = array(
							'your_name' => $user_login,
							'user_login_register' => $email,
							'user_pass_register' => $password
						);

						Civi_Helper::civi_send_email($email, 'mail_register_user', $args);

						Civi_Helper::civi_send_email($admin_email, 'admin_mail_register_user', $args);

						$users  = get_user_by('login', $user_login);
						if (in_array('civi_user_candidate', (array) $users->roles) && $enable_redirect_after_login && $redirect_for_candidate != '') {
							$url_redirect = get_page_link($redirect_for_candidate);
						} else if (in_array('civi_user_employer', (array) $users->roles) && $enable_redirect_after_login && $redirect_for_employer != '') {
							$url_redirect = get_page_link($redirect_for_employer);
						} else if (in_array('administrator', (array) $users->roles) && $enable_redirect_after_login && $redirect_for_admin != '') {
							$url_redirect = get_page_link($redirect_for_admin);
						}
						echo json_encode(array('success' => true, 'messages' => $msg, 'class' => 'text-success', 'url_redirect' => $url_redirect));
					} else {
						$msg = esc_html__('Username/Email address is existing', 'civi');
						echo json_encode(array('success' => false, 'messages' => $msg, 'class' => 'text-error'));
					}
				}
			}

			wp_die();
		}

		//////////////////////////////////////////////////////////////////
		// Verify User
		//////////////////////////////////////////////////////////////////
		function verify_code()
		{
			$verify_code    = $_POST['verify_code'];
			$account_type = $_POST['account_type'];
			$firstname    = $_POST['firstname'];
			$lastname     = $_POST['lastname'];
			$companyname  = $_POST['companyname'];
			$email        = $_POST['email'];
			$password     = $_POST['password'];
			$user_login   = $companyname;
			$url_redirect = '';

			$enable_redirect_after_login  = Civi_Helper::civi_get_option('enable_redirect_after_login');
			$enable_verify_user  = Civi_Helper::civi_get_option('enable_verify_user');
			$redirect_for_admin  = Civi_Helper::civi_get_option('redirect_for_admin');
			$redirect_for_employer  = Civi_Helper::civi_get_option('redirect_for_employer');
			$redirect_for_candidate  = Civi_Helper::civi_get_option('redirect_for_candidate');

			if (isset($_COOKIE[trim($user_login)]) && intval($verify_code) == intval($_COOKIE[trim($user_login)])) {
				$userdata = array(
					'user_login' 	=> $user_login,
					'first_name' 	=> $firstname,
					'last_name'  	=> $lastname,
					'display_name'	=> $companyname,
					'user_email' 	=> $email,
					'user_pass'  	=> $password,
					'account_type'  => $account_type
				);
				$user_id = wp_insert_user($userdata);

				if ($user_id == 0) {
					$user_login = substr($email,  0, strpos($email, '@'));
					$userdata = array(
						'user_login' 	=> $user_login,
						'first_name' 	=> $firstname,
						'last_name'  	=> $lastname,
						'display_name'	=> $companyname,
						'user_email' 	=> $email,
						'user_pass'  	=> $password,
						'account_type'  => $account_type
					);
					$user_id = wp_insert_user($userdata);
				}

				if ($account_type == 'civi_user_employer') {
					$u = new WP_User($user_id);

					// Remove role
					if (get_role('civi_user_candidate')) {
						$u->remove_role('civi_user_candidate');
					}

					// Add role
					$u->add_role('civi_user_employer');
				}

				$creds = array();
				$creds['user_login']    = $user_login;
				$creds['user_email']    = $email;
				$creds['user_password'] = $password;
				$creds['remember']      = true;
				$user = wp_signon($creds, false);
				$msg  = esc_html__('Verify success', 'civi');

				$admin_email = get_option('admin_email');

				$args = array(
					'your_name' => $user_login,
					'user_login_register' => $email,
					'user_pass_register' => $password
				);

				Civi_Helper::civi_send_email($email, 'mail_register_user', $args);

				Civi_Helper::civi_send_email($admin_email, 'admin_mail_register_user', $args);

				$users  = get_user_by('login', $user_login);
				if (in_array('civi_user_candidate', (array) $users->roles) && $enable_redirect_after_login && $redirect_for_candidate != '') {
					$url_redirect = get_page_link($redirect_for_candidate);
				} else if (in_array('civi_user_employer', (array) $users->roles) && $enable_redirect_after_login && $redirect_for_employer != '') {
					$url_redirect = get_page_link($redirect_for_employer);
				} else if (in_array('administrator', (array) $users->roles) && $enable_redirect_after_login && $redirect_for_admin != '') {
					$url_redirect = get_page_link($redirect_for_admin);
				}
				echo json_encode(array('success' => true, 'messages' => $msg, 'class' => 'text-success', 'url_redirect' => $url_redirect));
			} else {
				$msg  = esc_html__('The code is incorrect or has expired.', 'civi');
				echo json_encode(array('success' => false, 'messages' => $msg, 'class' => 'text-error', 'url_redirect' => $url_redirect));
			}

			wp_die();
		}

		//////////////////////////////////////////////////////////////////
		// Ajax fb login or register
		//////////////////////////////////////////////////////////////////
		function fb_ajax_login_or_register()
		{
			$id    = $_POST['id'];
			$email = $_POST['email'];
			$name  = $_POST['name'];
			$userdata = array(
				'user_login'   => $id,
				'user_pass'    => $id,
				'user_email'   => $email,
				'display_name' => $name,
			);
			$civi_dashboard_page_id  = Civi_Helper::civi_get_option('civi_candidate_dashboard_page_id', 0);
			$url_redirect = get_page_link($civi_dashboard_page_id);

			$user_id = wp_insert_user($userdata);
			if (is_wp_error($user_id)) {
				$creds = array();
				$creds['user_login']    = $id;
				$creds['user_password'] = $id;
				$creds['remember']      = true;
				$user = wp_signon($creds, false);

				$msg = '';
				if (!is_wp_error($user)) {
					$msg = esc_html__('Login success', 'civi');
					echo json_encode(array('success' => true, 'messages' => $msg, 'class' => 'text-success', 'url_redirect' => $url_redirect));
				} else {
					$msg = esc_html__('This email has been used to register', 'civi');
					echo json_encode(array('success' => false, 'messages' => $msg, 'class' => 'text-error'));
				}
				wp_die();
			} else {
				wp_set_current_user($user_id);
				wp_set_auth_cookie($user_id, true);

				$new_candidate = array(
					'post_type' => 'candidate',
					'post_status' => 'publish',
				);
				$new_candidate['post_title'] = $name;
				$post_id = wp_insert_post($new_candidate, true);
			}
			echo json_encode(array('success' => true, 'class' => 'text-success', 'message' => esc_html__('Login success', 'civi'), 'url_redirect' => $url_redirect));
			wp_die();
		}

		//////////////////////////////////////////////////////////////////
		// Ajax reset password
		//////////////////////////////////////////////////////////////////
		public function reset_password_ajax()
		{
			check_ajax_referer('civi_reset_password_ajax_nonce', 'civi_security_reset_password');
			$allowed_html = array();
			$user_login = wp_kses($_POST['user_login'], $allowed_html);

			if (empty($user_login)) {
				echo json_encode(array('success' => false, 'class' => 'text-warning', 'message' => esc_html__('Enter a username or email address.', 'civi')));
				wp_die();
			}

			if (strpos($user_login, '@')) {
				$user_data = get_user_by('email', trim($user_login));
				if (empty($user_data)) {
					echo json_encode(array('success' => false, 'class' => 'text-error', 'message' => esc_html__('There is no user registered with that email address.', 'civi')));
					wp_die();
				}
			} else {
				$login = trim($user_login);
				$user_data = get_user_by('login', $login);

				if (!$user_data) {
					echo json_encode(array('success' => false, 'class' => 'text-error', 'message' => esc_html__('Invalid username', 'civi')));
					wp_die();
				}
			}
			$user_login = $user_data->user_login;
			$user_email = $user_data->user_email;
			$key = get_password_reset_key($user_data);

			if (is_wp_error($key)) {
				echo json_encode(array('success' => false, 'message' => $key));
				wp_die();
			}

			$message = esc_html__('Someone has requested a password reset for the following account:', 'civi') . "\r\n\r\n";
			$message .= network_home_url('/') . "\r\n\r\n";
			$message .= sprintf(esc_html__('Username: %s', 'civi'), $user_login) . "\r\n\r\n";
			$message .= esc_html__('If this was a mistake, just ignore this email and nothing will happen.', 'civi') . "\r\n\r\n";
			$message .= esc_html__('To reset your password, visit the following address:', 'civi') . "\r\n\r\n";
			$message .= '<' . get_home_url() . '?action=rp&key=' . $key . '&login=' . rawurlencode($user_login) . ">\r\n";

			if (is_multisite())
				$blogname = $GLOBALS['current_site']->site_name;
			else
				$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

			$title = sprintf(esc_html__('[%s] Password Reset', 'civi'), $blogname);
			$title = apply_filters('retrieve_password_title', $title, $user_login, $user_data);
			$message = apply_filters('retrieve_password_message', $message, $key, $user_login, $user_data);
			if ($message && !wp_mail($user_email, wp_specialchars_decode($title), $message)) {
				echo json_encode(array('success' => false, 'class' => 'text-error', 'message' => esc_html__('The email could not be sent.', 'civi') . "\r\n" . esc_html__('Possible reason: your host may have disabled the mail() function.', 'civi')));
				wp_die();
			} else {
				echo json_encode(array('success' => true, 'class' => 'text-success', 'message' => esc_html__('Please, Check your email to get new password', 'civi')));
				wp_die();
			}
		}

		public function change_password_ajax()
		{
			$new_password  	= $_POST['new_password'];
			$login  		= $_POST['login'];
			$user_data 		= get_user_by('login', $login);

			$password = wp_set_password($new_password, $user_data->ID);

			echo json_encode(array('success' => true, 'class' => 'text-success', 'message' => esc_html__('Please, re-login!', 'civi')));

			wp_die();
		}

		//////////////////////////////////////////////////////////////////
		// Ajax fb login or register
		//////////////////////////////////////////////////////////////////
		function google_ajax_login_or_register()
		{
			$id     = $_POST['id'];
			$email  = $_POST['email'];
			$name   = $_POST['name'];
			$avatar = $_POST['avatar'];
			$userdata = array(
				'user_login'   => $id,
				'user_pass'    => $id,
				'user_email'   => $email,
				'display_name' => $name,
			);
			$civi_dashboard_page_id  = Civi_Helper::civi_get_option('civi_candidate_dashboard_page_id', 0);
			$url_redirect = get_page_link($civi_dashboard_page_id);

			$user_id = wp_insert_user($userdata);

			if (is_wp_error($user_id)) {
				$creds = array();
				$creds['user_login']    = $id;
				$creds['user_password'] = $id;
				$creds['remember']      = true;
				$user = wp_signon($creds, false);

				$msg = '';
				if (!is_wp_error($user)) {
					$msg = esc_html__('Login success', 'civi');
					echo json_encode(array('success' => true, 'messages' => $msg, 'class' => 'text-success', 'url_redirect' => $url_redirect));
				} else {
					$msg = esc_html__('This email has been used to register', 'civi');
					echo json_encode(array('success' => false, 'messages' => $msg, 'class' => 'text-error'));
				}
				wp_die();
			} else {
				wp_set_current_user($user_id);
				wp_set_auth_cookie($user_id, true);

				$new_candidate = array(
					'post_type' => 'candidate',
					'post_status' => 'publish',
				);
				$new_candidate['post_title'] = $name;
				$post_id = wp_insert_post($new_candidate, true);

				update_user_meta($user_id, CIVI_METABOX_PREFIX . 'user-google-email', $email);
			}
			echo json_encode(array('success' => true, 'class' => 'text-success', 'message' => esc_html__('Login success', 'civi'), 'url_redirect' => $url_redirect));
			wp_die();
		}


		//////////////////////////////////////////////////////////////////
		// get script social login
		//////////////////////////////////////////////////////////////////
		function get_script_social_login()
		{
			// Facebook API
			$enable_social_login = civi_get_option('enable_social_login');
			$facebook_app_id = civi_get_option('facebook_app_id');
			$fb_script = '';
			if ($facebook_app_id && $enable_social_login && !is_user_logged_in()) {
				if (is_ssl()) {
					$fb_script = '<script defer="defer" src="https://connect.facebook.net/' . get_locale() . '/sdk.js#xfbml=1&version=v4.0&appId=' . $facebook_app_id . '&autoLogAppEvents=1" id="facebook-api-js"></script>';
				} else {
					$fb_script = '<script defer="defer" src="http://connect.facebook.net/' . get_locale() . '/sdk.js#xfbml=1&version=v4.0&appId=' . $facebook_app_id . '&autoLogAppEvents=1" id="facebook-api-js"></script>';
				}
			}

			//Google API
			$google_script = '';
			if ($enable_social_login && !is_user_logged_in()) {
				$google_script = '<script src="https://apis.google.com/js/platform.js?ver=1.0.0" id="google-api-js" gapi_processed="true"></script>';
			}

			//Captcha
			ob_start();
			$captcha = rand(1000, 9999);
			$enable_captcha = Civi_Helper::civi_get_option('enable_captcha');
			if ($enable_captcha) : ?>
				<input type="text" class="form-control civi-captcha" name="ip_captcha" />
				<input type="hidden" class="form-control civi-num-captcha" name="ip_num_captcha" data-captcha="<?php echo $captcha; ?>" />
				<?php Civi_Helper::civi_image_captcha($captcha); ?>
<?php endif;
			$html_captcha = ob_get_clean();

			echo json_encode(array('success' => true, 'google' => $google_script, 'fb' => $fb_script, 'captcha' => $html_captcha,));

			wp_die();
		}
	}
}
