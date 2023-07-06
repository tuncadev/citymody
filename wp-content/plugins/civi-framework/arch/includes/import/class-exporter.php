<?php

/**
 * Civi Exporter
 *
 * @package Civi_Framework
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Civi Exporter Class
 */
class Civi_Exporter
{

	/**
	 * Instance
	 *
	 * @var Civi_Exporter The single instance of the class.
	 */
	private static $instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @return Civi_Exporter An instance of the class.
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
		// Export content.
		add_filter('export_wp_filename', array($this, 'export_wp_filename'));
		add_action('admin_post_export_content', array($this, 'export_content'));

		// Export Widgets.
		add_action('admin_post_export_widgets', array($this, 'export_widgets'));

		// Export Customizer.
		add_action('admin_post_export_customizer_settings', array($this, 'export_customizer_settings'));

		// Export Theme Options.
		add_action('admin_post_export_theme_options', array($this, 'export_theme_options'));

		// Export Menu Locations.
		add_action('admin_post_export_menus', array($this, 'export_menus'));

		// Export Elementor
		add_action('admin_post_export_elementor', array($this, 'export_elementor'));

		// Export Page Options.
		add_action('admin_post_export_page_options', array($this, 'export_page_options'));

		// Media Package.
		add_action('admin_post_export_media_package', array($this, 'export_media_package'));
	}

	/**
	 * Export items
	 */
	public static function get_export_items()
	{

		$export_items = array(
			array(
				'name'        => esc_html__('Content', 'civi-framework'),
				'action'      => 'export_content',
				'icon'        => 'lab la-wordpress-simple',
				'description' => esc_html__('Create an XML file containing your posts, pages, comments, custom fields, categories, and tags', 'civi-framework'),
			),
			array(
				'name'        => esc_html__('Widgets', 'civi-framework'),
				'action'      => 'export_widgets',
				'icon'        => 'las la-shapes',
				'description' => esc_html__('Create a text file containing your widgets', 'civi-framework'),
			),
			array(
				'name'        => esc_html__('Customizer Settings', 'civi-framework'),
				'action'      => 'export_customizer_settings',
				'icon'        => 'las la-tools',
				'description' => esc_html__('Create a text file containing your customizer settings (in Appearance > Customize)', 'civi-framework'),
			),

			array(
				'name'        => esc_html__('Theme Options', 'civi-framework'),
				'action'      => 'export_theme_options',
				'icon'        => 'lab la-themeisle',
				'description' => esc_html__('Create a text file containing your theme options', 'civi-framework'),
			),

			array(
				'name'        => esc_html__('Elementor', 'civi-framework'),
				'action'      => 'export_elementor',
				'icon'        => 'lab la-elementor',
				'description' => esc_html__('Create a text file containing your elementor', 'civi-framework'),
			),

			array(
				'name'        => esc_html__('Menus', 'civi-framework'),
				'action'      => 'export_menus',
				'icon'        => 'las la-bars',
				'description' => esc_html__('Create a text file containing your menus', 'civi-framework'),
			),
			array(
				'name'        => esc_html__('Page Options', 'civi-framework'),
				'action'      => 'export_page_options',
				'icon'        => 'lar la-file-alt',
				'description' => esc_html__('Create a text file containing the Homepage & Post Page settings', 'civi-framework'),
			),
		);

		if (class_exists('ZipArchive')) {
			$export_items[] = array(
				'name'              => esc_html__('Media Package', 'civi-framework'),
				'action'            => 'export_media_package',
				'icon'              => 'lar la-image',
				'input_file_name'   => true,
				'default_file_name' => 'media-01',
				'description'       => esc_html__('Create a zip package containing all files in the wp-content/uploads directory', 'civi-framework'),
			);
		}

		return apply_filters('civi_export_items', $export_items);
	}

	/**
	 * Allowed File Extensions.
	 * Used when export the media package.
	 *
	 * @return array The array of allowed file extensions.
	 */
	protected function get_allowed_exts()
	{
		$mime_types   = wp_get_mime_types();
		$allowed_exts = array();

		foreach ($mime_types as $key => $mime_type) {
			if (strpos($key, '|') !== false) {
				$types = explode('|', $key);

				foreach ($types as $type) {
					$allowed_exts[] = $type;
				}
			} else {
				$allowed_exts[] = $key;
			}
		}

		$allowed_exts = apply_filters('civi_export_allowed_exts', $allowed_exts);

		return $allowed_exts;
	}

	/**
	 * Save export file
	 *
	 * @param string $file_name File Name.
	 * @param string $file_content File Content.
	 */
	public function save_file($file_name, $file_content)
	{
		ob_get_clean();

		header('Content-Type: application/text', true, 200);
		header("Content-Disposition: attachment; filename={$file_name}");
		header('Pragma: no-cache');
		header('Expires: 0');

		if (ob_get_contents()) ob_end_clean();
		flush();

		// Output file contents.
		echo $file_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		exit;
	}

	/**
	 * Change the content export filename.
	 *
	 * @return string WP filename.
	 */
	public function export_wp_filename()
	{
		return 'content.xml';
	}

	/**
	 * Export content
	 */
	public function export_content()
	{

		if (!verify_nonce('export_content')) {
			wp_die(esc_html__('Invalid nonce', 'civi-framework'));
		}

		require_once ABSPATH . 'wp-admin/includes/export.php';
		export_wp();
	}

	/**
	 * Export widgets
	 * Copy from Widget Importer & Exporter plugin: https://wordpress.org/plugins/widget-importer-exporter/
	 */
	public function export_widgets()
	{

		if (!verify_nonce('export_widgets')) {
			wp_die(esc_html__('Invalid nonce', 'civi-framework'));
		}

		// Get all available widgets site supports.
		$available_widgets = $this->available_widgets();

		// Get all widget instances for each widget.
		$widget_instances = array();

		// Loop widgets.
		foreach ($available_widgets as $widget_data) {

			// Get all instances for this ID base.
			$instances = get_option('widget_' . $widget_data['id_base']);

			if (!empty($instances)) {
				foreach ($instances as $instance_id => $instance_data) {

					// Key is ID (not _multiwidget).
					if (is_numeric($instance_id)) {
						$unique_instance_id                      = $widget_data['id_base'] . '-' . $instance_id;
						$widget_instances[$unique_instance_id] = $instance_data;
					}
				}
			}
		}

		// Gather sidebars with their widget instances.
		$sidebars_widgets          = get_option('sidebars_widgets');
		$sidebars_widget_instances = array();
		foreach ($sidebars_widgets as $sidebar_id => $widget_ids) {

			// Skip inactive widgets.
			if ('wp_inactive_widgets' === $sidebar_id) {
				continue;
			}

			// Skip if no data or not an array (array_version).
			if (!is_array($widget_ids) || empty($widget_ids)) {
				continue;
			}

			// Loop widget IDs for this sidebar.
			foreach ($widget_ids as $widget_id) {

				// Is there an instance for this widget ID?
				if (isset($widget_instances[$widget_id])) {

					// Add to array.
					$sidebars_widget_instances[$sidebar_id][$widget_id] = $widget_instances[$widget_id];
				}
			}
		}

		$data = apply_filters('civi_export_widgets_data', $sidebars_widget_instances);
		$this->save_file('widgets.json', wp_json_encode($data));
	}

	/**
	 * Available widgets
	 *
	 * Gather site's widgets into array with ID base, name, etc.
	 * Used by export and import functions.
	 *
	 * @return array Widget information
	 */
	private function available_widgets()
	{
		global $wp_registered_widget_controls;

		$widget_controls = $wp_registered_widget_controls;

		$available_widgets = array();

		foreach ($widget_controls as $widget) {

			if (!empty($widget['id_base']) && !isset($available_widgets[$widget['id_base']])) {
				$available_widgets[$widget['id_base']]['id_base'] = $widget['id_base'];
				$available_widgets[$widget['id_base']]['name']    = $widget['name'];
			}
		}

		return $available_widgets;
	}

	/**
	 * Export customizer settings.
	 */
	public function export_customizer_settings()
	{

		if (!verify_nonce('export_customizer_settings')) {
			wp_die(esc_html__('Invalid nonce', 'civi-framework'));
		}

		$data = get_theme_mods();
		unset($data['nav_menu_locations']);

		$data = apply_filters('civi_export_customizer_settings_data', $data);
		$this->save_file('customizer.json', wp_json_encode($data));
	}


	/**
	 * Export Theme Options
	 */
	public function export_theme_options()
	{

		if (!verify_nonce('export_theme_options')) {
			wp_die(esc_html__('Invalid nonce', 'civi-framework'));
		}

		$data = get_option('civi-framework');

		$data = apply_filters('civi_export_theme_options_data', $data);
		$this->save_file('theme-options.json', wp_json_encode($data));
	}

	/**
	 * Export Elementor
	 */
	public function export_elementor()
	{

		if (!verify_nonce('export_elementor')) {
			wp_die(esc_html__('Invalid nonce', 'civi-framework'));
		}

		$elementor_options = array(
			'elementor_active_kit',
			'elementor_global_css',
			'elementor_cpt_support',
			'elementor_disable_color_schemes',
			'elementor_disable_typography_schemes',
			'elementor_default_generic_fonts',
			'elementor_unfiltered_files_upload',
			'elementor_scheme_color',
			'elementor_scheme_typography',
			'elementor_scheme_color-picker',
			'elementor_custom_icon_sets_config',
			'elementor_allow_svg',
			'elementor_library_category_children',
		);

		$elementor_options = apply_filters('civi_export_elementor_data', $elementor_options);

		$data = array();

		if (!empty($elementor_options)) {
			foreach ($elementor_options as $option) {
				$setting = get_option($option);

				if ($setting) {
					$data[$option] = $setting;
				}
			}
		}

		$this->save_file('elementor.json', wp_json_encode($data));
	}

	/**
	 * Export Menus
	 */
	public function export_menus()
	{

		if (!verify_nonce('export_menus')) {
			wp_die(esc_html__('Invalid nonce', 'civi-framework'));
		}

		global $wpdb;
		$data        = array();
		$locations   = get_nav_menu_locations();
		$terms_table = $wpdb->prefix . 'terms';

		foreach ((array) $locations as $location => $menu_id) {
			$menu_slug = $wpdb->get_results("SELECT * FROM $terms_table where term_id={$menu_id}", ARRAY_A); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.NoCaching
			if (!empty($menu_slug)) {
				$data[$location] = $menu_slug[0]['slug'];
			}
		}

		$data = apply_filters('civi_export_menus_data', $data);
		$this->save_file('menus.json', wp_json_encode($data));
	}

	/**
	 * Export page options.
	 */
	public function export_page_options()
	{

		if (!verify_nonce('export_page_options')) {
			wp_die(esc_html__('Invalid nonce', 'civi-framework'));
		}

		$data = array();

		$data['show_on_front'] = get_option('show_on_front');

		// Get front page title.
		$front_page_id = intval(get_option('page_on_front'));
		if (0 !== $front_page_id) {
			$data['page_on_front'] = get_the_title($front_page_id);
		}

		// Get blog page title.
		$blog_page_id = intval(get_option('page_for_posts'));
		if (0 !== $blog_page_id) {
			$data['page_for_posts'] = get_the_title($blog_page_id);
		}

		$data = apply_filters('civi_export_page_options_data', $data);
		$this->save_file('page-options.json', wp_json_encode($data));
	}

	/**
	 * Export media package.
	 */
	public function export_media_package()
	{

		if (!verify_nonce('export_media_package')) {
			wp_die(esc_html__('Invalid nonce', 'civi-framework'));
		}

		$file_name = isset($_POST['media-package-file-name']) ? GLF_THEME_SLUG . '-' . sanitize_text_field(wp_unslash($_POST['media-package-file-name'])) . '.zip' : 'media-01.zip'; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$file_name = apply_filters('civi_export_archive_file_name', $file_name);

		$source    = WP_CONTENT_DIR . '/uploads';
		$file_path = WP_CONTENT_DIR . '/' . $file_name;

		// Check the source folder is writeable or not?
		if (!is_writeable(WP_CONTENT_DIR)) {
			wp_die(__('Could not write files into \'wp-content\' directory, permission denined.', 'civi-framework')); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		// Action: civi_before_create_media_package.
		do_action('civi_before_create_media_package');

		if (!$this->create_media_package($source, $file_path)) {
			wp_die(esc_html__('Could not create the media package, please try again.', 'civi-framework'));
		}

		// Action: civi_after_create_media_package.
		do_action('civi_after_create_media_package');

		// Download zip archive.
		if (file_exists($file_path)) {
			ob_get_clean();

			header('Content-Type: application/zip', true, 200);
			header("Content-Disposition: attachment; filename={$file_name}");
			header('Content-Length: ' . filesize($file_path));
			header('Pragma: no-cache');
			header('Expires: 0');

			ob_end_clean();
			flush();

			if (!readfile("{$file_path}")) { // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_readfile
				// translators: $s: Archive file name.
				wp_die(sprintf(esc_html__('Could not read file %s.', 'civi-framework'), esc_html($file_path)));
			}

			// Delete the file in wp-content directory.
			unlink($file_path);
			exit;
		} else {
			// translators: $s: Archive file name.
			wp_die(sprintf(esc_html__('The file %s does not exists.', 'civi-framework'), esc_html($file_path)));
		}
	}

	/**
	 * Copy files in wp-content/uploads to a new folder and skip all generated files & hidden files.
	 * Eg: image-DDDxDDD.ext or image-DDDxDDD@2x.ext or .DS_Store, etc...
	 *
	 * @param string $source Source folder.
	 * @param string $dest Destination folder.
	 *
	 * @return boolean Create zip package successful or not?
	 */
	protected function create_media_package($source, $dest)
	{

		if (!class_exists('ZipArchive')) {
			wp_die(__('Could not create zip file. The extension \'ZipArchive\' is not enabled.', 'civi-framework')); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		$source = str_replace('\\', '/', realpath($source));

		if (is_dir($source)) {

			if (file_exists($dest)) {
				unlink($dest);
			}

			$zip = new ZipArchive();

			if (!$zip->open($dest, ZIPARCHIVE::CREATE)) {
				return false;
			}

			$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

			foreach ($files as $file) {
				$file      = str_replace('\\', '/', $file);
				$file_name = basename($file);

				if ('.' === $file_name || '..' === $file_name) {
					continue;
				}

				if (is_file($file)) {
					$file_path     = realpath($file);
					$relative_path = substr($file_path, strlen($source) + 1);
					$file_dir      = end(explode('/', dirname($file, 1)));

					// Only add files in year/month folders.
					if (is_numeric($file_dir)) {
						// Skip unnecessary files.
						if (substr($file_name, 0, 1) !== '.' && preg_match('/(-\d{1,}x\d{1,}+|@2x)\.\w{3,}$/', $file_name) === 0) {
							// Only add files that have allowed extensions.
							$ext = end(explode('.', $file_name));
							if (in_array($ext, $this->get_allowed_exts(), true)) {
								$zip->addFile($file_path, $relative_path);
							}
						}
					}
				}
			}

			return $zip->close();
		}

		return false;
	}

	/**
	 * Download image from placeholder.com
	 *
	 * @param string $file File path.
	 * @param string $relative_path Relative path.
	 * @param string $dest Destination directory.
	 */
	protected function download_image($file, $relative_path, $dest)
	{
		$result      = false;
		$skip_prefix = apply_filters('civi_placeholder_skip_prefix', '__');

		$file_path     = realpath($file);
		$file_name     = basename($file);
		$ext           = end(explode('.', $file_name));
		list($w, $h) = getimagesize($file);

		if (!empty($skip_prefix)) {
			// Skip all images that have $skip_prefix.
			if (substr($file_name, 0, strlen($skip_prefix)) !== $skip_prefix) {
				$result = copy("https://via.placeholder.com/{$w}x{$h}.${ext}", "{$dest}/{$relative_path}");
			}
		}

		return $result;
	}
}

new Civi_Exporter();