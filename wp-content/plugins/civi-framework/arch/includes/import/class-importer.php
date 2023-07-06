<?php

/**
 * Civi Importer
 *
 * @package Civi_Framework
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Civi Importer Class
 */
class Civi_Importer
{

	/**
	 * Instance
	 *
	 * @var Civi_Importer The single instance of the class.
	 */
	private static $instance = null;

	/**
	 * Demo slug.
	 *
	 * @var string Demo slug.
	 */
	private $demo_slug = '';

	/**
	 * The instance of the Civi_Content_Importer class.
	 *
	 * @var object
	 */
	public $importer;

	/**
	 * The path of the log file.
	 *
	 * @var string
	 */
	public $log_file_path;

	/**
	 * Holds any error messages, that should be printed out at the end of the import.
	 *
	 * @var string
	 */
	public $frontend_error_messages = array();

	/**
	 * Make plugin page options available to other methods.
	 *
	 * @var array
	 */
	private $plugin_page_setup = array();

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @return Civi_Importer An instance of the class.
	 */
	public static function instance()
	{
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	public function __construct()
	{

		// Fetch import files.
		add_action('wp_ajax_fetch_demo_steps', array($this, 'fetch_demo_steps'));
		add_action('wp_ajax_nopriv_fetch_demo_steps', array($this, 'fetch_demo_steps'));

		// Import demo.
		add_action('wp_ajax_import_demo', array($this, 'import_demo'));
		add_action('wp_ajax_nopriv_import_demo', array($this, 'import_demo'));

		// Download media package.
		add_action('wp_ajax_download_media_package', array($this, 'download_media_package'));
		add_action('wp_ajax_nopriv_download_media_package', array($this, 'download_media_package'));

		// Copy images.
		add_action('wp_ajax_copy_images', array($this, 'copy_images'));
		add_action('wp_ajax_nopriv_copy_images', array($this, 'copy_images'));

		// Import content.
		add_action('wp_ajax_import_content_xml', array($this, 'import_content_xml'));
		add_action('wp_ajax_nopriv_import_content_xml', array($this, 'import_content_xml'));

		// Refresh jobs.
		add_action('wp_ajax_refresh_data', array($this, 'refresh_data'));
		add_action('wp_ajax_nopriv_refresh_data', array($this, 'refresh_data'));

		// Import widgets.
		add_action('wp_ajax_import_widgets_json', array($this, 'import_widgets_json'));
		add_action('wp_ajax_nopriv_import_widgets_json', array($this, 'import_widgets_json'));

		// Import customizer.
		add_action('wp_ajax_import_customizer_json', array($this, 'import_customizer_json'));
		add_action('wp_ajax_nopriv_import_customizer_json', array($this, 'import_customizer_json'));

		// Import theme options.
		add_action('wp_ajax_import_theme_options_json', array($this, 'import_theme_options_json'));
		add_action('wp_ajax_nopriv_import_theme_options_json', array($this, 'import_theme_options_json'));

		// Import menu locations.
		add_action('wp_ajax_import_menus_json', array($this, 'import_menus_json'));
		add_action('wp_ajax_nopriv_import_menus_json', array($this, 'import_menus_json'));

		// Import page options.
		add_action('wp_ajax_import_page_options_json', array($this, 'import_page_options_json'));
		add_action('wp_ajax_nopriv_import_page_options_json', array($this, 'import_page_options_json'));

		// Import Elementor settings.
		add_action('wp_ajax_import_elementor_json', array($this, 'import_elementor_json'));
		add_action('wp_ajax_nopriv_import_elementor_json', array($this, 'import_elementor_json'));

		// Set importer.
		add_action('after_setup_theme', array($this, 'setup_content_importer'));
	}

	/**
	 * Find all import issues
	 *
	 * @return array Issues array.
	 */
	public static function get_import_issues()
	{
		$issues = array();

		// Check PHP extensions and such.
		if (!function_exists('curl_init')) {
			$issues[] = esc_html__('Your server does not have cURL.', 'civi-framework');
		}

		if (!class_exists('DOMDocument')) {
			$issues[] = esc_html__('Your server does not have DOMDocument.', 'civi-framework');
		}

		if (!function_exists('fsockopen')) {
			$issues[] = esc_html__('Your server does not have fsockopen.', 'civi-framework');
		}

		if (!class_exists('XMLReader')) {
			$issues[] = esc_html__('Your server does not have XMLReader extension.', 'civi-framework');
		}

		if (!class_exists('ZipArchive')) {
			$issues[] = esc_html__('Your server does not have ZipArchive extension.', 'civi-framework');
		}

        $max_execution_time = ini_get('max_execution_time');
        if (300 > $max_execution_time) {
            set_time_limit(300);
        }

		// Check required plugins.
		$civi_plugins                    = apply_filters('civi_plugins', array());
		$not_installed_required_plugins = array();
		$not_activated_required_plugins = array();

		foreach ($civi_plugins as $plugin) {

			if ($plugin['required']) {
				if (!TGM_Plugin_Activation::$instance->is_plugin_installed($plugin['slug'])) {
					$not_installed_required_plugins[] = $plugin['name'];
				} elseif (!TGM_Plugin_Activation::$instance->is_plugin_active($plugin['slug'])) {
					$not_activated_required_plugins[] = $plugin['name'];
				}
			}
		}

		if (!empty($not_installed_required_plugins)) {
			$issues[] = sprintf(
				wp_kses_post(
					// translators: %1$s Plugin name list, %2$s Install Plugins page URL.
					_n(
						'A required plugin: <strong>%1$s</strong> is not installed. <a href="%2$s">Install it</a>.',
						'Some required plugins: <strong>%1$s</strong> is not installed. <a href="%2$s">Install these plugins</a>.',
						count($not_installed_required_plugins),
						'civi-framework'
					)
				),
				implode(', ', $not_installed_required_plugins),
				add_query_arg(
					array(
						'page'          => 'tgmpa-install-plugins',
						'plugin_status' => 'install',
					),
					admin_url('/admin.php')
				)
			);
		}

		if (!empty($not_activated_required_plugins)) {
			$issues[] = sprintf(
				wp_kses_post(
					// translators: %1$s Plugin name list, %2$s Install Plugins page URL.
					_n(
						'A required plugin: <strong>%1$s</strong> is installed but not activated. <a href="%2$s">Activate it</a>.',
						'Some required plugins: <strong>%1$s</strong> is installed but not activated. <a href="%2$s">Activate these plugins</a>.',
						count($not_activated_required_plugins),
						'civi-framework'
					)
				),
				implode(', ', $not_activated_required_plugins),
				add_query_arg(
					array(
						'page'          => 'tgmpa-install-plugins',
						'plugin_status' => 'activate',
					),
					admin_url('/admin.php')
				)
			);
		}

		return apply_filters('civi_import_issues', $issues);
	}

	/**
	 * Get all demos.
	 */
	public static function get_import_demos()
	{
		return apply_filters('civi_import_demos', array());
	}

	/**
	 * Get steps in a demo.
	 *
	 * @param string $demo_slug Demo slug.
	 * @return array Demo steps of a demo
	 */
	public function get_demo_steps($demo_slug)
	{

		$demos = self::get_import_demos();

		$import_dir         = GLF_THEME_DIR . '/assets/import/' . $demo_slug;
		$content_xml        = "{$import_dir}/content.xml";
		$widgets_json       = "{$import_dir}/widgets.json";
		$customizer_json    = "{$import_dir}/customizer.json";
		$menus_json         = "{$import_dir}/menus.json";
		$theme_options_json  = "{$import_dir}/theme-options.json";
		$page_options_json  = "{$import_dir}/page-options.json";
		$elementor_json     = "{$import_dir}/elementor.json";
		$media_package      = isset($demos[$demo_slug]['media_package_local']) ? $demos[$demo_slug]['media_package_local'] : '';
		$demo_steps         = array();

		if (!file_exists($import_dir)) {
			// translators: %s: Import directory.
			wp_send_json_error(sprintf(esc_html__('The directory %s/ does not exist.', 'civi-framework'), $import_dir));
		}

		// Fetch the media package in local.
		if (file_exists($media_package)) {
			$demo_steps['media_package_local'] = esc_html__('Media Package (on local)', 'civi-framework');
		} else {
			$demo_steps['media_package_url'] = esc_html__('Media Package (on cloud)', 'civi-framework');
		}

		// Fetch the content.xml file.
		if (file_exists($content_xml)) {
			$demo_steps['content_xml'] = esc_html__('Content (posts, pages, custom post types, categories, comments, etc..)', 'civi-framework');
		} else {
			// translators: %s: content.xml.
			wp_send_json_error(sprintf(esc_html__('The file %s does not exist.', 'civi-framework'), $content_xml));
		}

		// Fetch the widgets.json file.
		if (file_exists($widgets_json)) {
			$demo_steps['widgets_json'] = esc_html__('Widgets', 'civi-framework');
		}

		// Fetch the widgets.json file.
		if (file_exists($customizer_json)) {
			$demo_steps['customizer_json'] = esc_html__('Customizer Settings', 'civi-framework');
		}

		// Fetch the menus.json file.
		if (file_exists($menus_json)) {
			$demo_steps['menus_json'] = esc_html__('Menus', 'civi-framework');
		}

		// Fetch the elementor.json file.
		if (file_exists($elementor_json)) {
			$demo_steps['elementor_json'] = esc_html__('Elementor', 'civi-framework');
		}

		// Fetch the page-options.json file.
		if (file_exists($theme_options_json)) {
			$demo_steps['theme_options_json'] = esc_html__('Theme Options', 'civi-framework');
		}

		// Fetch the page-options.json file.
		if (file_exists($page_options_json)) {
			$demo_steps['page_options_json'] = esc_html__('Page Options', 'civi-framework');
		}

		return apply_filters('civi_demo_steps', $demo_steps, $demo_slug);
	}

	/**
	 * Verify data before import content.
	 *
	 * @param string $action Action name.
	 */
	public function verify_before_call_ajax($action)
	{

		if (!verify_nonce($action)) {
			wp_send_json_error(esc_html__('Invalid nonce', 'civi-framework'));
		}

		if (isset($_POST['demo_slug'])) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$this->demo_slug = sanitize_text_field(wp_unslash($_POST['demo_slug'])); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		} else {
			wp_send_json_error(esc_html__('Demo slug is not defined.', 'civi-framework'));
		}
	}

	/**
	 * Show demo steps to form
	 */
	public function fetch_demo_steps()
	{

		$this->verify_before_call_ajax('fetch_demo_steps');

		$demo_steps = $this->get_demo_steps($this->demo_slug);
		$demo_slug  = $this->demo_slug;

		ob_start();
		require_once CIVI_PLUGIN_DIR . 'includes/import/views/popup-demo-steps-form.php';
		$html = ob_get_clean();

		wp_send_json_success($html);
	}

	/**
	 * Import Demo.
	 */
	public function import_demo()
	{

		$this->verify_before_call_ajax('import_demo');

		$demos          = self::get_import_demos();
		$demo_slug      = $this->demo_slug;
		$selected_steps = array();

		// Get import steps.
		if (isset($_POST['selected_steps']) && !empty($_POST['selected_steps'])) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			// Remove all empty item in steps.
			$selected_steps_str = sanitize_text_field(wp_unslash($_POST['selected_steps'])); // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$selected_steps     = explode(',', $selected_steps_str); // phpcs:ignore WordPress.Security.NonceVerification.Missing

			if (empty($selected_steps)) {
				wp_send_json_error(esc_html__('No import steps found.', 'civi-framework'));
			}
		} else {
			wp_send_json_error(esc_html__('Please select at least 1 item to continue.', 'civi-framework'));
		}

		// If we have the media package in the theme directory, just extract it & copy all images inside to /wp-content/uploads.
		if (in_array('media_package_local', $selected_steps, true)) {
			$media_package_local = $demos[$demo_slug]['media_package_local'];

			ob_start();
			require_once CIVI_PLUGIN_DIR . 'includes/import/views/popup-copy-images-form.php';
			$html = ob_get_clean();

			wp_send_json_success($html);
		}

		// If the media package is in the cloud, we need to download it first.
		if (in_array('media_package_url', $selected_steps, true)) {
			$media_package_url = $demos[$demo_slug]['media_package_url'];

			ob_start();
			require_once CIVI_PLUGIN_DIR . 'includes/import/views/popup-download-form.php';
			$html = ob_get_clean();

			wp_send_json_success($html);
		}
		// Get import content form.
		if (!in_array('media_package_local', $selected_steps, true) && !in_array('media_package_url', $selected_steps, true)) {
			$demo_steps           = $this->get_demo_steps($demo_slug);
			$import_content_steps = $this->get_import_content_steps($demo_steps, $selected_steps);

			ob_start();
			require_once CIVI_PLUGIN_DIR . 'includes/import/views/popup-import-content-form.php';
			$html = ob_get_clean();

			wp_send_json_success($html);
		}
	}

	/**
	 * Download media package.
	 */
	public function download_media_package()
	{

		$this->verify_before_call_ajax('download_media_package');

		if (defined('WP_CONTENT_DIR') && !is_writable(WP_CONTENT_DIR)) {
			// translators: %s: wp-content directory.
			wp_send_json_error(sprintf(esc_html__('Could not write files into %s directory. Permission denied.', 'civi-framework'), WP_CONTENT_DIR));
		}

		$demos     = self::get_import_demos();
		$demo_slug = $this->demo_slug;

		if (!isset($_POST['media_package_url']) || empty($_POST['media_package_url'])) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			wp_send_json_error(esc_html__('Could not download the media package. The URL is not defined.', 'civi-framework'));
		}

		$media_package_url  = sanitize_text_field(wp_unslash($_POST['media_package_url'])); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$selected_steps_str = isset($_POST['selected_steps']) ? sanitize_text_field(wp_unslash($_POST['selected_steps'])) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$media_package      = download_url($media_package_url, 1800);
		$media_path         = WP_CONTENT_DIR . '/' . GLF_THEME_SLUG . '-' . $demo_slug;

		if (!is_wp_error($media_package)) {

			require_once ABSPATH . 'wp-admin/includes/file.php';
			WP_Filesystem();
			global $wp_filesystem;

			// Delete the media folder if it already exists.
			if (file_exists($media_path) && !$wp_filesystem->rmdir($media_path, true)) {
				// translators: %s: Path to media package.
				wp_send_json_error(sprintf(esc_html__('Could not create %s directory. It already exists but Civi Framework cannot delete it because of permission denied.', 'civi-framework'), $media_path));
			}

			// Unzip the media package.
			if (wp_mkdir_p($media_path)) {
				$unzip = unzip_file($media_package, $media_path);

				if (is_wp_error($unzip)) {
					// translators: %s: Path to media package.
					wp_send_json_error(sprintf(esc_html__('Could not unzip the media package: %s.', 'civi-framework'), $media_package));
				}

				unlink($media_package);
			} else {
				// translators: %s: Path to media package.
				wp_send_json_error(sprintf(esc_html__('Could not create %s directory.', 'civi-framework'), $media_path));
			}
		} else {
			wp_send_json_error(
				sprintf(
					// translators: %1$s: Error code, %3$s: Error message, %2$s: Direct link of the media package.
					__('ERROR %1$s: Could not download the media package. %2$s. Please try to download it from <a href="%3$s" target="_blank">here</a> and try to import manually.', 'civi-framework'),
					$media_package->get_error_code(),
					$media_package->get_error_message(),
					esc_url($media_package_url)
				)
			);
		}

		// After downloading the media package, we have to copy images to wp-content/uploads.
		ob_start();
		require_once CIVI_PLUGIN_DIR . 'includes/import/views/popup-copy-images-form.php';
		$html = ob_get_clean();

		wp_send_json_success($html);
	}

	public function recurse_copy($src, $dst)
	{
		$dir = opendir($src);
		@mkdir($dst);
		while (false !== ($file = readdir($dir))) {
			if (($file != '.') && ($file != '..')) {
				if (is_dir($src . '/' . $file)) {
					recurse_copy($src . '/' . $file, $dst . '/' . $file);
				} else {
					copy($src . '/' . $file, $dst . '/' . $file);
				}
			}
		}
		closedir($dir);
		//echo "$src";
	}

	/**
	 * Copy images to wp-content/uploads
	 */
	public function copy_images()
	{

		$this->verify_before_call_ajax('copy_images');

		if (defined('WP_CONTENT_DIR') && !is_writable(WP_CONTENT_DIR)) {
			// translators: %s: wp-content directory.
			wp_send_json_error(sprintf(esc_html__('Could not write files into %s directory. Permission denied.', 'civi-framework'), WP_CONTENT_DIR));
		}

		$demo_slug  = $this->demo_slug;
		$media_path = WP_CONTENT_DIR . '/' . GLF_THEME_SLUG . '-' . $demo_slug;

		if (isset($_POST['media_package_local'])) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			// Unzip the local media package.
			$media_package_local = sanitize_text_field(wp_unslash($_POST['media_package_local'])); // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$unzip               = unzip_file($media_package_local, $media_path);
		}

		if (!file_exists($media_path)) {
			// translators: %s: Media package path.
			wp_send_json_error(sprintf(esc_html__('Could not found this directory: %s.', 'civi-framework'), $media_path));
		}

		if (!is_dir($media_path)) {
			// translators: %s: Media package path.
			wp_send_json_error(sprintf(esc_html__('%s is not a directory.', 'civi-framework'), $media_path));
		}

		$current_files = $this->list_files(WP_CONTENT_DIR . '/uploads');
		$new_files     = $this->list_files($media_path);

		foreach ($current_files as $key => $value) {
			// Remove all files already exist.
			if (isset($new_files[$key])) {
				unset($new_files[$key]);
			}
		}

		$uploads = wp_upload_dir();
		$upload_path = WP_CONTENT_DIR . '/uploads/';

		if (is_multisite()) {
			$blog_id = get_current_blog_id();
			$upload_path = WP_CONTENT_DIR . '/uploads/sites/' . $blog_id . '/';
		}

		// After copying image, delete the media directory.
		require_once ABSPATH . 'wp-admin/includes/file.php';
		WP_Filesystem();
		global $wp_filesystem;

		copy_dir(
			$media_path . '/',
			$upload_path
		);

		$wp_filesystem->rmdir($media_path, true);

		ob_start();

		if (isset($_POST['selected_steps']) && !empty($_POST['selected_steps'])) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			// Get import content form.
			$selected_steps_str   = sanitize_text_field(wp_unslash($_POST['selected_steps'])); // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$selected_steps       = explode(',', $selected_steps_str); // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$demo_steps           = $this->get_demo_steps($demo_slug);
			$import_content_steps = $this->get_import_content_steps($demo_steps, $selected_steps);

			require_once CIVI_PLUGIN_DIR . 'includes/import/views/popup-import-content-form.php';
		} else {
			// If we don't have next steps, display success animation.
			require_once CIVI_PLUGIN_DIR . 'includes/import/views/popup-success.php';
		}
		$html = ob_get_clean();

		wp_send_json_success($html);
	}

	/**
	 * List all files in a directory
	 *
	 * @param string $folder Folder.
	 * @param string $parent_folder Parent folder.
	 */
	private function list_files($folder, $parent_folder = null)
	{

		if (null === $parent_folder) {
			$parent_folder = $folder;
		}

		$stack = array();

		if (is_dir($folder)) {
			$dir = opendir($folder);

			while (false !== ($file = readdir($dir))) { // phpcs:ignore WordPress.CodeAnalysis.AssignmentInCondition.FoundInWhileCondition

				$file_path = "{$folder}/{$file}";

				if (substr($file, 0, 1) === '.' && '.' !== $file && '..' !== $file) {
					// Delete all hidden files.
					unlink($file_path);
				} elseif (is_file($file_path)) {
					$stack[rawurlencode(str_replace("{$parent_folder}/", '', $file_path))] = 1;
				} elseif (is_dir($file_path) && '.' !== $file && '..' !== $file) {
					$stack[rawurlencode(str_replace("{$parent_folder}/", '', $file_path))] = 4;

					// Recursive.
					$stack = $stack + $this->list_files($file_path, $parent_folder);
				}
			}
		}

		return $stack;
	}

	/**
	 * Get next step to import
	 *
	 * @param string $current_step Key of an item in $steps array.
	 * @param array  $steps Import steps.
	 */
	public function get_next_step($current_step, $steps)
	{
		$position = array_search($current_step, $steps, true);
		return $steps[$position + 1];
	}

	/**
	 * Get steps while importing content (pages, posts, widgets, ...etc)
	 *
	 * @param array $demo_steps All steps while importing demo.
	 * @param array $selected_steps Import steps that selected by user.
	 *
	 * @return array An key - value array, use when importing website content.
	 */
	private function get_import_content_steps($demo_steps, $selected_steps)
	{
		unset($demo_steps['media_package_url']);
		unset($demo_steps['media_package_local']);
		foreach ($demo_steps as $key => $step) {
			if (!in_array($key, $selected_steps, true)) {
				unset($demo_steps[$key]);
			}
		}

		return $demo_steps;
	}

	/**
	 * Set up the importer, after the theme has loaded and instantiate the importer.
	 */
	public function setup_content_importer()
	{

		// Importer options array.
		$importer_options = apply_filters(
			'civi_importer_options',
			array(
				'fetch_attachments' => true,
			)
		);

		// Logger options for the logger used in the importer.
		$logger_options = apply_filters(
			'civi_logger_options',
			array(
				'logger_min_level' => 'warning',
			)
		);

		// Configure logger instance and set it to the importer.
		$logger            = new Civi_Import_Logger();
		$logger->min_level = $logger_options['logger_min_level'];

		// Create importer instance with proper parameters.
		$this->importer = new Civi_Content_Importer($importer_options, $logger);
	}

	/**
	 * Get content importer data, so we can continue the import with this new AJAX request.
	 *
	 * @return boolean
	 */
	public function use_existing_importer_data()
	{
		$data = get_transient('civi_importer_data');
		if (false !== $data) {
			$this->frontend_error_messages = empty($data['frontend_error_messages']) ? array() : $data['frontend_error_messages'];
			$this->log_file_path           = empty($data['log_file_path']) ? '' : $data['log_file_path'];
			$this->importer->set_importer_data($data);

			return true;
		}
		return false;
	}

	/**
	 * Get the current state of selected data.
	 *
	 * @return array
	 */
	public function get_current_importer_data()
	{
		return array(
			'frontend_error_messages' => $this->frontend_error_messages,
			'log_file_path'           => $this->log_file_path,
		);
	}

	/**
	 * Getter function to retrieve the private log_file_path value.
	 *
	 * @return string The log_file_path value.
	 */
	public function get_log_file_path()
	{
		return $this->log_file_path;
	}

	/**
	 * Setter function to append additional value to the private frontend_error_messages value.
	 *
	 * @param string $text The additional value that will be appended to the existing frontend_error_messages.
	 */
	public function append_to_frontend_error_messages($text)
	{
		$lines = array();

		if (!empty($text)) {
			$text  = str_replace('<br>', PHP_EOL, $text);
			$lines = explode(PHP_EOL, $text);
		}

		foreach ($lines as $line) {
			if (!empty($line) && !in_array($line, $this->frontend_error_messages, true)) {
				$this->frontend_error_messages[] = $line;
			}
		}
	}

	/**
	 * Read import files
	 *
	 * @param string $file_name File name.
	 *
	 * @return mixed
	 */
	public function read_import_file($file_name)
	{
		$file = GLF_THEME_DIR . '/assets/import/' . $this->demo_slug . '/' . $file_name;

		if (!file_exists($file)) {
			// translators: %s: File name.
			return new WP_Error('file_not_found', sprintf(esc_html__('The %s file does not exist.', 'civi-framework'), $file_name));
		}

		require_once ABSPATH . 'wp-admin/includes/file.php';
		WP_Filesystem();
		global $wp_filesystem;

		$file_content = $wp_filesystem->get_contents($file);

		return $file_content;
	}

	/**
	 * Send a JSON response with final report.
	 */
	public function send_final_response()
	{
		// Delete importer data transient for current import.
		delete_transient('civi_importer_data');

		$message = '';

		ob_start();
		require_once CIVI_PLUGIN_DIR . 'includes/import/views/popup-success.php';

		Civi_Import_Logger::append_to_file(
			esc_html__('The demo import successfully finished', 'civi-framework'),
			$this->log_file_path,
			''
		);

		// Count import.
		update_option(GLF_THEME_SLUG . '_' . $this->demo_slug . '_imported', true);

		wp_send_json_success(ob_get_clean());
	}

	/**
	 * Import content.
	 */
	public function import_content_xml()
	{

		$this->verify_before_call_ajax('import_content_xml');

		$demo_slug            = $this->demo_slug;
		$import_content_steps = array();

		if (isset($_POST['import_content_steps']) && !empty($_POST['import_content_steps'])) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			// Remove all empty item in steps.
			$import_content_steps = explode(',', sanitize_text_field(wp_unslash($_POST['import_content_steps']))); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		}

		$content_xml = GLF_THEME_DIR . '/assets/import/' . $demo_slug . '/content.xml';

		if (!file_exists($content_xml)) {
			wp_send_json_error(esc_html__('The content.xml file does not exist.', 'civi-framework'));
		}

		// Try to update PHP memory limit (so that it does not run out of it).
		ini_set('memory_limit', apply_filters('civi_import_memory_limit', '350M')); // phpcs:ignore WordPress.PHP.IniSet.memory_limit_Blacklisted

		// Is this a new AJAX call to continue the previous import?
		$use_existing_importer_data = $this->use_existing_importer_data();

		if (!$use_existing_importer_data) {

			// Create a date and time string to use for demo and log file names.
			Civi_Import_Logger::set_demo_import_start_time();

			// Define log file path.
			$this->log_file_path = Civi_Import_Logger::get_log_path();

			Civi_Import_Logger::append_to_file(
				'',
				$this->log_file_path,
				esc_html__('Importing content', 'civi-framework')
			);
		}

		// Save the initial import data as a transient, so other import parts (in new AJAX calls) can use that data.
		set_transient('civi_importer_data', $this->get_current_importer_data(), 0.1 * HOUR_IN_SECONDS);

		/**
		 * 1). Execute the actions hooked to the 'civi_before_content_import_execution' action:
		 */
		do_action('civi_before_content_import_execution');

		/**
		 * 2). Import content
		 * Returns any errors greater then the "warning" logger level, that will be displayed on front page.
		 */
		$this->append_to_frontend_error_messages($this->importer->import_content($content_xml));

		/**
		 * 3). Execute the actions hooked to the 'civi_after_content_import_execution' action.
		 */
		do_action('civi_after_content_import_execution');

		// Request the after all import AJAX call.
		if (!empty($import_content_steps)) {
			$next_step = $this->get_next_step('content_xml', $import_content_steps);

			if ($next_step) {
				wp_send_json(
					array(
						'next_step' => $next_step,
						'_wpnonce'  => wp_create_nonce('import_' . $next_step),
					)
				);
			}
		}

		// Save the import data as a transient, so other import parts (in new AJAX calls) can use that data.
		set_transient('civi_importer_data', $this->get_current_importer_data(), 0.1 * HOUR_IN_SECONDS);

		// Send a JSON response.
		$this->send_final_response();
	}

	/**
	 * Refresh jobs.
	 */
	public function refresh_data()
	{
        //jobs
        $args_jobs = array(
            'post_type'   => 'jobs',
            'numberposts' => -1
        );
        $get_jobs = get_posts($args_jobs);
        foreach ($get_jobs as $jobs) {
            $jobs->post_title = $jobs->post_title . '';
            wp_update_post($jobs);
        }

        //company
        $args_company = array(
            'post_type'   => 'company',
            'numberposts' => -1
        );
        $get_company = get_posts($args_company);
        foreach ($get_company as $company) {
            $company->post_title = $company->post_title . '';
            wp_update_post($company);
        }

        //elementor
		$args_ele = array(
			'post_type'   => 'elementor_library',
			'numberposts' => -1
		);
		$ele_builder = get_posts($args_ele);
		foreach ($ele_builder as $ele) {
			$ele->post_title = $ele->post_title . '';
			wp_update_post($ele);
		}

		ob_start();

		require_once CIVI_PLUGIN_DIR . 'includes/import/views/popup-refresh.php';

		wp_send_json_success(ob_get_clean());
	}

	/**
	 * Import widgets.
	 */
	public function import_widgets_json()
	{

		$this->verify_before_call_ajax('import_widgets_json');

		$import_content_steps = array();

		if (isset($_POST['import_content_steps']) && !empty($_POST['import_content_steps'])) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			// Remove all empty item in steps.
			$import_content_steps = explode(',', sanitize_text_field(wp_unslash($_POST['import_content_steps']))); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		}

		// Is this a new AJAX call to continue the previous import?
		$use_existing_importer_data = $this->use_existing_importer_data();

		if (!$use_existing_importer_data) {
			// Create a date and time string to use for demo and log file names.
			Civi_Import_Logger::set_demo_import_start_time();

			// Define log file path.
			$this->log_file_path = Civi_Import_Logger::get_log_path();
		}

		set_transient('civi_importer_data', $this->get_current_importer_data(), 0.1 * HOUR_IN_SECONDS);

		$widgets_json = $this->read_import_file('widgets.json');

		if (!is_wp_error($widgets_json)) {
			$widgets_json = json_decode($widgets_json);

			$widgets_importer = Civi_Widgets_Importer::instance();
			$result           = $widgets_importer->import($widgets_json);

			if (!is_wp_error($result)) {
				ob_start();
				$widgets_importer->format_results_for_log($result);
				$message = ob_get_clean();

				// Add this message to log file.
				Civi_Import_Logger::append_to_file($message, $this->log_file_path, esc_html__('Importing widgets', 'civi-framework'));

				// Finish or go to next steps?
				if (!empty($import_content_steps)) {
					$next_step = $this->get_next_step('widgets_json', $import_content_steps);

					if ($next_step) {
						wp_send_json(
							array(
								'next_step' => $next_step,
								'_wpnonce'  => wp_create_nonce('import_' . $next_step),
							)
						);
					}
				}

				$this->send_final_response();
			} else {
				// Write error to log file.
				$error_message = $result->get_error_message();
				Civi_Import_Logger::append_to_file($error_message, $this->log_file_path, esc_html__('Importing widgets', 'civi-framework'));
				wp_send_json_error($error_message);
			}
		} else {
			$error_message = $widgets_json->get_error_message();
			Civi_Import_Logger::append_to_file($error_message, $this->log_file_path, esc_html__('Importing widgets', 'civi-framework'));
			wp_send_json_error($error_message);
		}
	}

	/**
	 * Import Customizer options
	 */
	public function import_customizer_json()
	{

		$this->verify_before_call_ajax('import_customizer_json');

		$import_content_steps = array();

		if (isset($_POST['import_content_steps']) && !empty($_POST['import_content_steps'])) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			// Remove all empty item in steps.
			$import_content_steps = explode(',', sanitize_text_field(wp_unslash($_POST['import_content_steps']))); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		}

		// Is this a new AJAX call to continue the previous import?
		$use_existing_importer_data = $this->use_existing_importer_data();

		if (!$use_existing_importer_data) {
			// Create a date and time string to use for demo and log file names.
			Civi_Import_Logger::set_demo_import_start_time();

			// Define log file path.
			$this->log_file_path = Civi_Import_Logger::get_log_path();
		}

		set_transient('civi_importer_data', $this->get_current_importer_data(), 0.1 * HOUR_IN_SECONDS);

		$data = $this->read_import_file('customizer.json');

		if (!is_wp_error($data) && !empty($data)) {
			$data = json_decode($data, true);

			// Have valid data? If no data or could not decode.
			if (!is_array($data)) {
				Civi_Import_Logger::append_to_file(
					esc_html__('Error: Customizer import data could not be read. Please try a different file.', 'civi-framework'),
					$this->log_file_path,
					esc_html__('Importing customizer options', 'civi-framework')
				);

				wp_send_json_error(esc_html__('Error: Customizer import data could not be read. Please try a different file.', 'civi-framework'));
			}

			$nav_menu_locations = get_theme_mod('nav_menu_locations');
			remove_theme_mods(); // Reset customizer options.
			$data['nav_menu_locations'] = $nav_menu_locations;
			$message                    = '';

			foreach ($data as $name => $value) {
				set_theme_mod($name, $value);
				$message .= $name . esc_html__(' - Imported', 'civi-framework') . PHP_EOL;
			}

			Civi_Import_Logger::append_to_file(
				$message,
				$this->log_file_path,
				esc_html__('Importing customizer options', 'civi-framework')
			);

			// Finish or go to next steps?
			if (!empty($import_content_steps)) {
				$next_step = $this->get_next_step('customizer_json', $import_content_steps);

				if ($next_step) {
					wp_send_json(
						array(
							'next_step' => $next_step,
							'_wpnonce'  => wp_create_nonce('import_' . $next_step),
						)
					);
				}
			}

			$this->send_final_response();
		} else {
			$error_message = $data->get_error_message();
			Civi_Import_Logger::append_to_file($error_message, $this->log_file_path, esc_html__('Importing customizer options', 'civi-framework'));
			wp_send_json_error($error_message);
		}
	}

	/**
	 * Import Theme Option
	 */
	public function import_theme_options_json()
	{

		$this->verify_before_call_ajax('import_theme_options_json');

		$import_content_steps = array();

		if (isset($_POST['import_content_steps']) && !empty($_POST['import_content_steps'])) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			// Remove all empty item in steps.
			$import_content_steps = explode(',', sanitize_text_field(wp_unslash($_POST['import_content_steps']))); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		}

		// Is this a new AJAX call to continue the previous import?
		$use_existing_importer_data = $this->use_existing_importer_data();

		if (!$use_existing_importer_data) {
			// Create a date and time string to use for demo and log file names.
			Civi_Import_Logger::set_demo_import_start_time();

			// Define log file path.
			$this->log_file_path = Civi_Import_Logger::get_log_path();
		}

		set_transient('civi_importer_data', $this->get_current_importer_data(), 0.1 * HOUR_IN_SECONDS);

		$data = $this->read_import_file('theme-options.json');

		if (!is_wp_error($data) && !empty($data)) {
			$data = json_decode($data, true);

			// Have valid data? If no data or could not decode.
			if (!is_array($data)) {
				Civi_Import_Logger::append_to_file(
					esc_html__('Error: Theme options import data could not be read. Please try a different file.', 'civi-framework'),
					$this->log_file_path,
					esc_html__('Importing theme options', 'civi-framework')
				);

				wp_send_json_error(esc_html__('Error: Theme options import data could not be read. Please try a different file.', 'civi-framework'));
			}

			$message = '';

			update_option('civi-framework', $data);
			foreach ($data as $option => $value) {
				if (update_option($option, $value)) {
					$message .= $option . esc_html__(' - Imported', 'civi-framework') . PHP_EOL;
				} else {
					$message .= $option . esc_html__(' - Skipped', 'civi-framework') . PHP_EOL;
				}
			}

			Civi_Import_Logger::append_to_file(
				$message,
				$this->log_file_path,
				esc_html__('Importing theme options', 'civi-framework')
			);

			// Finish or go to next steps?
			if (!empty($import_content_steps)) {
				$next_step = $this->get_next_step('theme_options_json', $import_content_steps);

				if ($next_step) {
					wp_send_json(
						array(
							'next_step' => $next_step,
							'_wpnonce'  => wp_create_nonce('import_' . $next_step),
						)
					);
				}
			}

			$this->send_final_response();
		} else {
			$error_message = $data->get_error_message();
			Civi_Import_Logger::append_to_file($error_message, $this->log_file_path, esc_html__('Importing theme options', 'civi-framework'));
			wp_send_json_error($error_message);
		}
	}


	/**
	 * Import menu locations
	 */
	public function import_menus_json()
	{

		$this->verify_before_call_ajax('import_menus_json');

		$import_content_steps = array();

		if (isset($_POST['import_content_steps']) && !empty($_POST['import_content_steps'])) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			// Remove all empty item in steps.
			$import_content_steps = explode(',', sanitize_text_field(wp_unslash($_POST['import_content_steps']))); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		}

		// Is this a new AJAX call to continue the previous import?
		$use_existing_importer_data = $this->use_existing_importer_data();

		if (!$use_existing_importer_data) {
			// Create a date and time string to use for demo and log file names.
			Civi_Import_Logger::set_demo_import_start_time();

			// Define log file path.
			$this->log_file_path = Civi_Import_Logger::get_log_path();
		}

		set_transient('civi_importer_data', $this->get_current_importer_data(), 0.1 * HOUR_IN_SECONDS);

		$data = $this->read_import_file('menus.json');

		if (!is_wp_error($data) && !empty($data)) {
			$data = json_decode($data, true);

			// Have valid data? If no data or could not decode.
			if (!is_array($data)) {
				Civi_Import_Logger::append_to_file(
					esc_html__('Error: Menu import data could not be read. Please try a different file.', 'civi-framework'),
					$this->log_file_path,
					esc_html__('Importing menu', 'civi-framework')
				);

				wp_send_json_error(esc_html__('Error: Menu import data could not be read. Please try a different file.', 'civi-framework'));
			}

			global $wpdb;
			$terms_table = "{$wpdb->prefix}terms";
			$menu_array  = array();
			$message     = '';

			foreach ($data as $registered_menu => $menu_slug) {
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$term_rows = $wpdb->get_results("SELECT * FROM {$terms_table} where slug='{$menu_slug}'", ARRAY_A);

				if (isset($term_rows[0]['term_id'])) {
					$term_id_by_slug = $term_rows[0]['term_id'];
				} else {
					$term_id_by_slug = null;
				}

				$menu_array[$registered_menu] = $term_id_by_slug;

				$message .= "{$registered_menu} - {$menu_slug}" . esc_html__(' - Imported', 'civi-framework') . PHP_EOL;
			}

			set_theme_mod('nav_menu_locations', array_map('absint', $menu_array));

			Civi_Import_Logger::append_to_file(
				$message,
				$this->log_file_path,
				esc_html__('Importing menu', 'civi-framework')
			);

			// Finish or go to next steps?
			if (!empty($import_content_steps)) {
				$next_step = $this->get_next_step('menus_json', $import_content_steps);

				if ($next_step) {
					wp_send_json(
						array(
							'next_step' => $next_step,
							'_wpnonce'  => wp_create_nonce('import_' . $next_step),
						)
					);
				}
			}

			$this->send_final_response();
		} else {
			$error_message = $data->get_error_message();
			Civi_Import_Logger::append_to_file($error_message, $this->log_file_path, esc_html__('Importing menu', 'civi-framework'));
			wp_send_json_error($error_message);
		}
	}

	/**
	 * Import page options
	 */
	public function import_page_options_json()
	{

		$this->verify_before_call_ajax('import_page_options_json');

		$import_content_steps = array();

		if (isset($_POST['import_content_steps']) && !empty($_POST['import_content_steps'])) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			// Remove all empty item in steps.
			$import_content_steps = explode(',', sanitize_text_field(wp_unslash($_POST['import_content_steps']))); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		}

		// Is this a new AJAX call to continue the previous import?
		$use_existing_importer_data = $this->use_existing_importer_data();

		if (!$use_existing_importer_data) {
			// Create a date and time string to use for demo and log file names.
			Civi_Import_Logger::set_demo_import_start_time();

			// Define log file path.
			$this->log_file_path = Civi_Import_Logger::get_log_path();
		}

		set_transient('civi_importer_data', $this->get_current_importer_data(), 0.1 * HOUR_IN_SECONDS);

		$data = $this->read_import_file('page-options.json');

		if (!is_wp_error($data) && !empty($data)) {
			$data = json_decode($data, true);

			// Have valid data? If no data or could not decode.
			if (!is_array($data)) {
				Civi_Import_Logger::append_to_file(
					esc_html__('Error: Page options import data could not be read. Please try a different file.', 'civi-framework'),
					$this->log_file_path,
					esc_html__('Importing page options', 'civi-framework')
				);

				wp_send_json_error(esc_html__('Error: Page options import data could not be read. Please try a different file.', 'civi-framework'));
			}

			$message = '';

			if (isset($data['show_on_front']) && !empty($data['show_on_front'])) {
				if (update_option('show_on_front', $data['show_on_front'])) {
					$message .= 'show_on_front:' . $data['show_on_front'] . esc_html__(' - Imported', 'civi-framework') . PHP_EOL;
				} else {
					$message .= 'show_on_front:' . $data['show_on_front'] . esc_html__(' - Skipped', 'civi-framework') . PHP_EOL;
				}
			}

			if (!empty($data['page_on_front'])) {
				$page = get_page_by_title($data['page_on_front']);

				if (update_option('page_on_front', $page->ID)) {
					$message .= 'page_on_front:' . $data['page_on_front'] . esc_html__(' - Imported', 'civi-framework') . PHP_EOL;
				} else {
					$message .= 'page_on_front:' . $data['page_on_front'] . esc_html__(' - Skipped', 'civi-framework') . PHP_EOL;
				}
			}

			if (!empty($data['page_for_posts'])) {
				$page = get_page_by_title($data['page_for_posts']);

				if (update_option('page_for_posts', $page->ID)) {
					$message .= 'page_for_posts:' . $data['page_for_posts'] . esc_html__(' - Imported', 'civi-framework') . PHP_EOL;
				} else {
					$message .= 'page_for_posts:' . $data['page_for_posts'] . esc_html__(' - Imported', 'civi-framework') . PHP_EOL;
				}
			}

			// Delete 'Hello World' post.
			wp_trash_post(1);

			// Delete 'Sample Page'.
			wp_trash_post(2);

			Civi_Import_Logger::append_to_file(
				$message,
				$this->log_file_path,
				esc_html__('Importing page options', 'civi-framework')
			);

			// Finish or go to next steps?
			if (!empty($import_content_steps)) {
				$next_step = $this->get_next_step('page_options_json', $import_content_steps);

				if ($next_step) {
					wp_send_json(
						array(
							'next_step' => $next_step,
							'_wpnonce'  => wp_create_nonce('import_' . $next_step),
						)
					);
				}
			}

			$this->send_final_response();
		} else {
			$error_message = $data->get_error_message();
			Civi_Import_Logger::append_to_file($error_message, $this->log_file_path, esc_html__('Importing page options', 'civi-framework'));
			wp_send_json_error($error_message);
		}
	}

	/**
	 * Import Elementor Settings
	 */
	public function import_elementor_json()
	{

		$this->verify_before_call_ajax('import_elementor_json');

		if (!did_action('elementor/loaded')) {
			wp_send_json_error(__('Could not export Elementor settings. The plugin Elementor is not installed.', 'civi-framework')); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		$import_content_steps = array();

		if (isset($_POST['import_content_steps']) && !empty($_POST['import_content_steps'])) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			// Remove all empty item in steps.
			$import_content_steps = explode(',', sanitize_text_field(wp_unslash($_POST['import_content_steps']))); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		}

		// Is this a new AJAX call to continue the previous import?
		$use_existing_importer_data = $this->use_existing_importer_data();

		if (!$use_existing_importer_data) {
			// Create a date and time string to use for demo and log file names.
			Civi_Import_Logger::set_demo_import_start_time();

			// Define log file path.
			$this->log_file_path = Civi_Import_Logger::get_log_path();
		}

		set_transient('civi_importer_data', $this->get_current_importer_data(), 0.1 * HOUR_IN_SECONDS);

		$data = $this->read_import_file('elementor.json');

		if (!is_wp_error($data) && !empty($data)) {
			$data = json_decode($data, true);

			// Have valid data? If no data or could not decode.
			if (!is_array($data)) {
				Civi_Import_Logger::append_to_file(
					esc_html__('Error: Elementor settings import data could not be read. Please try a different file.', 'civi-framework'),
					$this->log_file_path,
					esc_html__('Importing Elementor settings', 'civi-framework')
				);

				wp_send_json_error(esc_html__('Error: Elementor settings import data could not be read. Please try a different file.', 'civi-framework'));
			}

			$message = '';

			foreach ($data as $option => $value) {
				if (update_option($option, $value)) {
					$message .= $option . esc_html__(' - Imported', 'civi-framework') . PHP_EOL;
				} else {
					$message .= $option . esc_html__(' - Skipped', 'civi-framework') . PHP_EOL;
				}
			}

			Civi_Import_Logger::append_to_file(
				$message,
				$this->log_file_path,
				esc_html__('Importing Elementor settings', 'civi-framework')
			);

			// Finish or go to next steps?
			if (!empty($import_content_steps)) {
				$next_step = $this->get_next_step('elementor_json', $import_content_steps);

				if ($next_step) {
					wp_send_json(
						array(
							'next_step' => $next_step,
							'_wpnonce'  => wp_create_nonce('import_' . $next_step),
						)
					);
				}
			}

			$this->send_final_response();
		} else {
			$error_message = $data->get_error_message();
			Civi_Import_Logger::append_to_file($error_message, $this->log_file_path, esc_html__('Importing Elementor settings', 'civi-framework'));
			wp_send_json_error($error_message);
		}
	}
}
