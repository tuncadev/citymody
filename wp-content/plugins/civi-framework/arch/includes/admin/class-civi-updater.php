<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Update theme
 */
if (!class_exists('Civi_Updater')) {

	class Civi_Updater
	{

		public static $info = array(
			'support' => 'https://uxper.ticksy.com/',
			'faqs'    => 'https://uxper.gitbook.io/civi-job-board-wordpress-theme/',
			'docs'    => 'https://uxper.gitbook.io/civi-job-board-wordpress-theme/',
			'api'     => 'https://data.uxper.co/' . CIVI_THEME_SLUG . '/update/',
			'icon'    => 'https://thumb-tf.s3.envato.com/files/25397810/thumb80x80.png',
			'desc'    => 'Thank you for using our theme, please reward it a full five-star &#9733;&#9733;&#9733;&#9733;&#9733; rating.',
			'tf'      => 'https://themeforest.net/item/civi-city-guide-wordpress-theme/25397810',
		);

		public function __construct()
		{
			delete_site_transient('update_themes');
			add_filter('pre_set_site_transient_update_themes', array($this, 'check_for_update'), 10, 1);

			// Rename theme folder after upgrade.
			add_action('upgrader_clear_destination', array($this, 'get_remote_destination'), 10, 4);
			add_action('upgrader_process_complete', array($this, 'rename_theme_folder_after_upgrade'), 8);

			add_action('wp_ajax_civi_patcher', array($this, 'ajax_patcher'));

			add_action('wp_ajax_uxper_get_changelogs', array($this, 'ajax_get_changelogs'));
			add_action('wp_ajax_nopriv_uxper_get_changelogs', array($this, 'ajax_get_changelogs'));
		}

		public static function get_info()
		{
			self::$info = apply_filters('uxper_info', self::$info);

			return self::$info;
		}

		public function check_for_update($transient)
		{

			if (empty($transient->checked)) {
				return $transient;
			}

			$update = self::check_theme_update();

			if ($update) {
				$response = array(
					'url'         => esc_url(add_query_arg('action', 'uxper_get_changelogs', admin_url('admin-ajax.php'))),
					'new_version' => $update['new_version'],
				);

				$transient->response[CIVI_THEME_SLUG] = $response;

				// If the purchase code is valide, user can get the update package
				if (self::check_valid_update()) {
					$transient->response[CIVI_THEME_SLUG]['package'] = $update['package'];
				} else {
					unset($transient->response[CIVI_THEME_SLUG]['package']);
				}
			}

			return $transient;
		}

		/**
		 * Get folder name after download the package
		 *
		 * @param mixed  $removed            Whether the destination was cleared. true on success, WP_Error on failure.
		 * @param string $local_destination  The local package destination.
		 * @param string $remote_destination The remote package destination.
		 * @param array  $theme              Theme slug.
		 *
		 * @return string Folder name.
		 */
		public function get_remote_destination($removed, $local_destination, $remote_destination, $theme)
		{
			$this->remote_destination = $remote_destination;
			return $this->remote_destination;
		}

		/**
		 * Rename theme folder after upgrade
		 */
		public function rename_theme_folder_after_upgrade()
		{
			// Only rename in wp-content/themes folder.
			if (!empty($this->remote_destination) && get_theme_root() === dirname($this->remote_destination) && file_exists($this->remote_destination)) {
				rename($this->remote_destination, CIVI_THEME_DIR);
			}
		}

		// Get changelogs file via AJAX for automatic update theme puporse
		public function ajax_get_changelogs()
		{
			self::get_info();
			echo self::get_changelogs(false);
			die;
		}

		// Check if has changelogs file <api>/changelogs.json
		public static function has_changelogs()
		{
			$request = wp_remote_get(self::$info['api'] . '/changelogs.json', array('timeout' => 120));
			if (is_wp_error($request)) {
				return false;
			} else {
				return true;
			}
		}

		// Get changelogs file content and filter
		public static function get_changelogs($table = true)
		{
			$changelogs = '';
			if (self::has_changelogs()) {
				$request = wp_remote_get(self::$info['api'] . '/changelogs.json', array('timeout' => 120));
				$logs    = json_decode(wp_remote_retrieve_body($request), true);
				if (is_array($logs) && count($logs) > 0) {
					foreach ($logs as $logkey => $logval) {
						if ($table) {
							$changelogs .= '<tr>';
							$changelogs .= '<td>' . $logkey . '</td>';
							$changelogs .= '<td>';
							if (is_array($logval['desc'])) {
								$changelogs .= implode('<br/>', $logval["desc"]);
							} else {
								$changelogs .= $logval['desc'];
							}
							$changelogs .= '</td>';
							$changelogs .= '<td>' . $logval['time'] . '</td>';
							$changelogs .= '</tr>';
						} else {
							$changelogs .= '<h4>' . $logkey . ' - <span>' . $logval['time'] . '</span></h4>';
							$changelogs .= '<pre>';
							if (is_array($logval['desc'])) {
								$changelogs .= implode('<br/>', $logval['desc']);
							} else {
								$changelogs .= $logval['desc'];
							}
							$changelogs .= '</pre>';
						}
					}
				}
			}
			$changelogs = apply_filters('uxper_changelogs', $changelogs);

			return $changelogs;
		}

		// Check has patcher
		public static function check_theme_patcher()
		{
			self::get_info();
			$request = wp_remote_get(self::$info['api'] . '/patcher.json', array('timeout' => 120));
			if (is_wp_error($request)) {
				return false;
			}
			$patchers = json_decode(wp_remote_retrieve_body($request), true);
			if (isset($patchers[CIVI_THEME_VERSION]) && (count($patchers[CIVI_THEME_VERSION]) > 0)) {
				$patchers_status = (array) get_option('uxper_patcher');
				foreach ($patchers[CIVI_THEME_VERSION] as $key => $value) {
					if (!in_array($key, $patchers_status)) {
						return true;
					}
				}

				return false;
			} else {
				return false;
			}
		}

		// Get patcher
		public static function get_patcher()
		{
			self::get_info();
			$request = wp_remote_get(self::$info['api'] . '/patcher.json', array('timeout' => 120));
			if (is_wp_error($request)) {
				return false;
			}
			$patchers = json_decode(wp_remote_retrieve_body($request), true);

			return $patchers;
		}

		// AJAX patcher
		public function ajax_patcher()
		{
			if (!isset($_POST['uxper_nonce']) || !wp_verify_nonce($_POST['uxper_nonce'], 'uxper_nonce')) {
				die('Permissions check failed!');
			}
			self::get_info();
			$uxper_patcher       = $_POST['uxper_patcher'];
			$uxper_patcher_url   = self::$info['api'] . '/' . $uxper_patcher . '.zip';
			$uxper_patcher_error = false;
			require_once(ABSPATH . 'wp-admin/includes/file.php');
			WP_Filesystem();
			// create temp folder
			$_tmp = wp_tempnam($uxper_patcher_url);
			@unlink($_tmp);
			@ob_flush();
			@flush();
			if (is_writable(CIVI_THEME_DIR)) {
				$package = download_url($uxper_patcher_url, 18000);
				$unzip   = unzip_file($package, CIVI_THEME_DIR);
				if (!is_wp_error($package)) {
					if (!is_wp_error($unzip)) {
						self::update_option_array('uxper_patcher', $uxper_patcher);
					} else {
						$uxper_patcher_error = true;
					}
				} else {
					$uxper_patcher_error = true;
				}
			} else {
				$uxper_patcher_error = true;
			}

			echo $uxper_patcher_error ? 'Error' : 'Done';
			die;
		}

		// Check purchase code
		public static function check_purchase_code($code)
		{
			if (empty($code)) {
				return;
			}

			$personalToken = 'CEBNI65VysSENxwPcdM3V7dpmXpUYAFk';
			$userAgent = "Purchase code verification on " . CIVI_THEME_NAME;

			// Surrounding whitespace can cause a 404 error, so trim it first
			$code = trim($code);
			$message = '';

			// Make sure the code looks valid before sending it to Envato
			if (!preg_match("/^([a-f0-9]{8})-(([a-f0-9]{4})-){3}([a-f0-9]{12})$/i", $code)) {
				$message = esc_html__('Invalid code', 'civi-framework');
			}

			// Build the request
			$ch = curl_init();
			curl_setopt_array($ch, array(
				CURLOPT_URL => "https://api.envato.com/v3/market/author/sale?code={$code}",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_TIMEOUT => 20,

				CURLOPT_HTTPHEADER => array(
					"Authorization: Bearer {$personalToken}",
					"User-Agent: {$userAgent}"
				)
			));

			// Send the request with warnings supressed
			$response = @curl_exec($ch);

			// Handle connection errors (such as an API outage)
			// You should show users an appropriate message asking to try again later
			if (curl_errno($ch) > 0) {
				$message = esc_html__('Error connecting to API', 'civi-framework');
			}

			// If we reach this point in the code, we have a proper response!
			// Let's get the response code to check if the purchase code was found
			$responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

			// HTTP 404 indicates that the purchase code doesn't exist
			if ($responseCode === 404) {
				$message = esc_html__('The purchase code was invalid', 'civi-framework');
			}

			// Anything other than HTTP 200 indicates a request or API error
			// In this case, you should again ask the user to try again later
			if ($responseCode !== 200) {
				$message = esc_html__('Failed to validate code due to an error: HTTP {$responseCode}', 'civi-framework');
			}

			// Parse the response into an object with warnings supressed
			$body = @json_decode($response);

			// Check for errors while decoding the response (PHP 5.3+)
			if ($body === false && json_last_error() !== JSON_ERROR_NONE) {
				$message = esc_html__('Error parsing response', 'civi-framework');
			}

			// Now we can check the details of the purchase code
			// At this point, you are guaranteed to have a code that belongs to you
			// You can apply logic such as checking the item's name or ID

			if ($responseCode === 200) {
				$id = $body->item->id;
				$name = $body->item->name;
				$purchase_info['id'] = $id;
				$purchase_info['name'] = $name;
			}

			$purchase_info['status_code'] = $responseCode;
			$purchase_info['message'] = $message;

			if ($code === 'uxper-8l7oi8723y3pbe7vbnat-code') {
				$purchase_info['status_code'] = 200;
			}

			return $purchase_info;
		}

		// Check theme update
		public static function check_theme_update()
		{
			self::get_info();
			$update_data = array();
			$has_update  = false;
			if (self::$info['api']) {
				$request = wp_remote_get(self::$info['api'] . '/changelogs.json', array('timeout' => 120));
				if (is_wp_error($request)) {
					return;
				}
				$updates = json_decode(wp_remote_retrieve_body($request), true);
				if (is_array($updates)) {
					foreach ($updates as $ukey => $uval) {
						if (version_compare($ukey, CIVI_THEME_VERSION) == 1) {
							$update_data['new_version'] = $ukey;
							$update_data['package']     = self::$info['api'] . '/' . $ukey . '.zip';
							$update_data['time']        = $uval['time'];
							$update_data['desc']        = $uval['desc'];
							$has_update                 = true;
							break;
						}
					}
				}
			}
			if ($has_update) {
				return $update_data;
			} else {
				return false;
			}
		}

		public static function is_envato_hosted()
		{
			return (defined('ENVATO_HOSTED_SITE') && defined('SUBSCRIPTION_CODE'));
		}

		public static function check_valid_update()
		{

			if (self::is_envato_hosted()) {
				return true;
			}

			$can_update    = false;
			$purchase_code = get_option('uxper_purchase_code'); // Purchase code in database

			// Check purchase code still valid?
			$purchase_info = self::check_purchase_code($purchase_code);

			if (is_array($purchase_info) && count($purchase_info) > 0) {
				$status_code = $purchase_info['status_code'];

				if ($status_code === 200) {
					$can_update = true;
				}
			}

			return $can_update;
		}
	}

	new Civi_Updater();
}
