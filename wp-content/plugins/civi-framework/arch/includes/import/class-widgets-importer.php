<?php

/**
 * Civi Widgets Importer
 *
 * @package Civi_Framework
 */

if (!defined('ABSPATH')) {
	exit;
}


/**
 * Civi Widgets Importer Class
 */
class Civi_Widgets_Importer
{

	/**
	 * Instance
	 *
	 * @var Civi_Widgets_Importer The single instance of the class.
	 */
	private static $instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @return Civi_Widgets_Importer An instance of the class.
	 */
	public static function instance()
	{
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Get available widgets in current site
	 *
	 * @return array
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
	 * Imports widgets from a json string.
	 *
	 * @param string $data Widget JSON.
	 */
	public function import($data)
	{

		global $wp_registered_sidebars;

		// Have valid data? If no data or could not decode.
		if (empty($data) || !is_object($data)) {
			return new \WP_Error(
				'corrupted_import_data',
				esc_html__('Error: Widget import data could not be read. Please try a different file.', 'civi-framework')
			);
		}

		// Delete all existings widgets.
		update_option('sidebars_widgets', array());

		// Get all available widgets that site supports.
		$available_widgets = $this->available_widgets();

		// Get all existing widget instances.
		$widget_instances = array();
		foreach ($available_widgets as $widget_data) {
			$widget_instances[$widget_data['id_base']] = get_option('widget_' . $widget_data['id_base']);
		}

		// Loop import data's sidebars.
		foreach ($data as $sidebar_id => $widgets) {

			// Skip inactive widgets (should not be in export file).
			if ('wp_inactive_widgets' === $sidebar_id) {
				continue;
			}

			// Check if sidebar is available on this site. Otherwise add widgets to inactive, and say so.
			if (isset($wp_registered_sidebars[$sidebar_id])) {
				$sidebar_available    = true;
				$use_sidebar_id       = $sidebar_id;
				$sidebar_message_type = 'success';
				$sidebar_message      = '';
			} else {
				$sidebar_available    = false;
				$use_sidebar_id       = 'wp_inactive_widgets'; // add to inactive if sidebar does not exist in theme.
				$sidebar_message_type = 'error';
				$sidebar_message      = esc_html__('Sidebar does not exist in theme (moving widget to Inactive)', 'civi-framework');
			}

			// Result for sidebar.
			$results[$sidebar_id]['name']         = !empty($wp_registered_sidebars[$sidebar_id]['name']) ? $wp_registered_sidebars[$sidebar_id]['name'] : $sidebar_id; // Sidebar name if theme supports it; otherwise ID.
			$results[$sidebar_id]['message_type'] = $sidebar_message_type;
			$results[$sidebar_id]['message']      = $sidebar_message;
			$results[$sidebar_id]['widgets']      = array();

			foreach ($widgets as $widget_instance_id => $widget) {

				$fail = false;

				// Get id_base (remove -# from end) and instance ID number.
				$id_base            = preg_replace('/-[0-9]+$/', '', $widget_instance_id);
				$instance_id_number = str_replace($id_base . '-', '', $widget_instance_id);

				// Does site support this widget?
				if (!$fail && !isset($available_widgets[$id_base])) {
					$fail                = true;
					$widget_message_type = 'error';
					$widget_message      = esc_html__('Site does not support widget', 'civi-framework');
				}

				$widget = json_decode(wp_json_encode($widget), true);

				if (!$fail && isset($widget_instances[$id_base])) {

					// Get existing widgets in this sidebar.
					$sidebars_widgets = get_option('sidebars_widgets');
					$sidebar_widgets  = isset($sidebars_widgets[$use_sidebar_id]) ? $sidebars_widgets[$use_sidebar_id] : array(); // check Inactive if that's where will go.

					// Loop widgets with ID base.
					$single_widget_instances = !empty($widget_instances[$id_base]) ? $widget_instances[$id_base] : array();
					foreach ($single_widget_instances as $check_id => $check_widget) {

						// Is widget in same sidebar and has identical settings?
						if (in_array("$id_base-$check_id", $sidebar_widgets, true) && (array) $widget === $check_widget) {
							$fail                = true;
							$widget_message_type = 'warning';
							$widget_message      = esc_html__('Widget already exists', 'civi-framework'); // Explain why widget not imported.
							break;
						}
					}
				}

				// No failure.
				if (!$fail) {

					$single_widget_instances   = get_option('widget_' . $id_base); // all instances for that widget ID base, get fresh every time.
					$single_widget_instances   = !empty($single_widget_instances) ? $single_widget_instances : array('_multiwidget' => 1); // start fresh if have to.
					$single_widget_instances[] = $widget; // add it.

					// Get the key it was given.
					end($single_widget_instances);
					$new_instance_id_number = key($single_widget_instances);

					// If key is 0, make it 1
					// When 0, an issue can occur where adding a widget causes data from other widget to load, and the widget doesn't stick (reload wipes it).
					if ('0' === strval($new_instance_id_number)) {
						$new_instance_id_number                             = 1;
						$single_widget_instances[$new_instance_id_number] = $single_widget_instances[0];
						unset($single_widget_instances[0]);
					}

					// Move _multiwidget to end of array for uniformity.
					if (isset($single_widget_instances['_multiwidget'])) {
						$multiwidget = $single_widget_instances['_multiwidget'];
						unset($single_widget_instances['_multiwidget']);
						$single_widget_instances['_multiwidget'] = $multiwidget;
					}

					// Update option with new widget.
					update_option('widget_' . $id_base, $single_widget_instances);

					// Assign widget instance to sidebar.
					$sidebars_widgets                      = get_option('sidebars_widgets'); // which sidebars have which widgets, get fresh every time.
					$new_instance_id                       = $id_base . '-' . $new_instance_id_number; // use ID number from new widget instance.
					$sidebars_widgets[$use_sidebar_id][] = $new_instance_id; // add new instance to sidebar.
					update_option('sidebars_widgets', $sidebars_widgets); // save the amended data.

					// Success message.
					if ($sidebar_available) {
						$widget_message_type = 'success';
						$widget_message      = esc_html__('Imported', 'civi-framework');
					} else {
						$widget_message_type = 'warning';
						$widget_message      = esc_html__('Imported to Inactive', 'civi-framework');
					}
				}

				// Result for widget instance.
				$results[$sidebar_id]['widgets'][$widget_instance_id]['name']         = isset($available_widgets[$id_base]['name']) ? $available_widgets[$id_base]['name'] : $id_base; // Widget name or ID if name not available (not supported by site).
				$results[$sidebar_id]['widgets'][$widget_instance_id]['title']        = !empty($widget['title']) ? $widget['title'] : __('No Title', 'civi-framework'); // Show "No Title" if widget instance is untitled.
				$results[$sidebar_id]['widgets'][$widget_instance_id]['message_type'] = $widget_message_type;
				$results[$sidebar_id]['widgets'][$widget_instance_id]['message']      = $widget_message;
			}
		}

		return $results;
	}

	/**
	 * Format results for log file
	 *
	 * @param array $results widget import results.
	 */
	public function format_results_for_log($results)
	{
		if (empty($results)) {
			esc_html_e('No results for widget import!', 'civi-framework');
		}

		// Loop sidebars.
		foreach ($results as $sidebar) {
			echo $sidebar['name'] . ' : ' . $sidebar['message'] . PHP_EOL . PHP_EOL; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			// Loop widgets.
			foreach ($sidebar['widgets'] as $widget) {
				echo $widget['name'] . ' - ' . $widget['title'] . ' - ' . esc_html($widget['message']) . PHP_EOL; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			echo PHP_EOL;
		}
	}
}
