<?php

/**
 * Logger class
 *
 * @package Civi_Framework
 */

/**
 * Class Civi_Import_Logger
 */
class Civi_Import_Logger extends \ProteusThemes\WPContentImporter2\WPImporterLoggerCLI
{
	/**
	 * Variable for front-end error display.
	 *
	 * @var string
	 */
	public $error_output = '';

	/**
	 * Holds the date and time string for demo import and log file.
	 *
	 * @var string
	 */
	public static $demo_import_start_time = '';

	/**
	 * Overwritten log function from WP_Importer_Logger_CLI.
	 *
	 * Logs with an arbitrary level.
	 *
	 * @param mixed  $level level of reporting.
	 * @param string $message log message.
	 * @param array  $context context to the log message.
	 */
	public function log($level, $message, array $context = array())
	{

		// Save error messages for front-end display.
		$this->error_output($level, $message, $context = array());

		if ($this->level_to_numeric($level) < $this->level_to_numeric($this->min_level)) {
			return;
		}

		printf(
			'[%s] %s' . PHP_EOL,
			esc_html(strtoupper($level)),
			$message // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		);
	}


	/**
	 * Save messages for error output.
	 * Only the messages greater then Error.
	 *
	 * @param mixed  $level level of reporting.
	 * @param string $message log message.
	 * @param array  $context context to the log message.
	 */
	public function error_output($level, $message, array $context = array())
	{
		if ($this->level_to_numeric($level) < $this->level_to_numeric('error')) {
			return;
		}

		$this->error_output .= sprintf(
			'[%s] %s<br>',
			strtoupper($level),
			$message
		);
	}

	/**
	 * Set the $demo_import_start_time class variable with the current date and time string.
	 */
	public static function set_demo_import_start_time()
	{
		self::$demo_import_start_time = date(apply_filters('civi_date_format_for_file_names', 'Y-m-d__H-i-s'));
	}

	/**
	 * Get log file path
	 *
	 * @return string, path to the log file
	 */
	public static function get_log_path()
	{
		$upload_dir  = wp_upload_dir();
		$upload_path = apply_filters('civi_upload_file_path', trailingslashit($upload_dir['path']));

		$log_path = $upload_path . apply_filters('civi_log_file_prefix', 'log_file_') . self::$demo_import_start_time . apply_filters('civi_log_file_suffix_and_file_extension', '.txt');

		self::register_file_as_media_attachment($log_path);

		return $log_path;
	}

	/**
	 * Get log file url
	 *
	 * @param string $log_path log path to use for the log filename.
	 * @return string, url to the log file.
	 */
	public static function get_log_url($log_path)
	{
		$upload_dir = wp_upload_dir();
		return $upload_dir['url'] . '/' . basename($log_path);
	}

	/**
	 * Register file as attachment to the Media page.
	 *
	 * @param string $log_path log file path.
	 * @return void
	 */
	public static function register_file_as_media_attachment($log_path)
	{
		// Check the type of file.
		$log_mimes = array('txt' => 'text/plain');
		$filetype  = wp_check_filetype(basename($log_path), apply_filters('civi_file_mimes', $log_mimes));

		// Prepare an array of post data for the attachment.
		$attachment = array(
			'guid'           => self::get_log_url($log_path),
			'post_mime_type' => $filetype['type'],
			'post_title'     => apply_filters('civi_attachment_prefix', esc_html__('Civi Framework Importer - ', 'civi-framework')) . preg_replace('/\.[^.]+$/', '', basename($log_path)),
			'post_content'   => '',
			'post_status'    => 'inherit',
		);

		// Insert the file as attachment in Media page.
		$attach_id = wp_insert_attachment($attachment, $log_path);
	}

	/**
	 * Append content to the file.
	 *
	 * @param string $content content to be saved to the file.
	 * @param string $file_path file path where the content should be saved.
	 * @param string $separator_text separates the existing content of the file with the new content.
	 * @return boolean|WP_Error, path to the saved file or WP_Error object with error message.
	 */
	public static function append_to_file($content, $file_path, $separator_text = '')
	{

		// Verify WP file-system credentials.
		if (!is_writable(WP_CONTENT_DIR)) {
			return new WP_Error(
				'wrong_login_credentials',
				__('Your WordPress login credentials don\'t allow to use WP_Filesystem!', 'civi-framework')
			);
		}

		// By this point, the $wp_filesystem global should be working, so let's use it to create a file.
		global $wp_filesystem;

		$existing_data = '';
		if (file_exists($file_path)) {
			$existing_data = $wp_filesystem->get_contents($file_path);
		}

		// Style separator.
		$separator = PHP_EOL . '---' . $separator_text . '---' . PHP_EOL;

		// if ( ! $wp_filesystem->put_contents( $file_path, $existing_data . $separator . $content . PHP_EOL ) ) {
		// 	return new WP_Error(
		// 		'failed_writing_file_to_server',
		// 		sprintf(
		// 			// translators: %1$s: <br> tag, %2$s: File path.
		// 			__( 'An error occurred while writing file to your server! Tried to write a file to: %1$s%2$s.', 'civi-framework' ),
		// 			'<br>',
		// 			$file_path
		// 		)
		// 	);
		// }

		return true;
	}
}
