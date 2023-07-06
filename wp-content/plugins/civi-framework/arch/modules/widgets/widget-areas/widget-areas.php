<?php

/**
 * Bingo Framework Widget Areas Manager
 *
 * @package WordPress
 * @subpackage emo
 * @since emo 1.0
 */
if (!class_exists('CIVI_Widget_Areas')) {
	class CIVI_Widget_Areas
	{
		protected $widget_areas = array();

		protected $version = '1.0';

		protected $widget_areas_key =  'bin-widget-areas';

		public function __construct()
		{
			if (is_admin()) {
				add_action('admin_print_scripts', array($this, 'add_new_widget_area_box'));
				add_action('load-widgets.php', array($this, 'add_widget_area_area'), 100);
				add_action('load-widgets.php', array($this, 'enqueue'), 100);
				add_action('wp_ajax_civi_delete_widget_area', array($this, 'delete_widget_area'));
			}
			add_action('widgets_init', array(&$this, 'register_custom_widget_areas'), 11);
		}

		public function enqueue()
		{
			wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'widget-areas', plugins_url(CIVI_PLUGIN_NAME . '/widgets/widget-areas/assets/js/widget-areas.min.js'), array('jquery'), $this->version);
			wp_enqueue_style(CIVI_PLUGIN_PREFIX . 'widget-areas', plugins_url(CIVI_PLUGIN_NAME . '/widgets/widget-areas/assets/css/widget-areas.min.css'), array(), $this->version, 'screen');
			wp_localize_script(
				CIVI_PLUGIN_PREFIX . 'widget-areas',
				'civi_widget_areas_variable',
				array(
					'ajax_url' => admin_url('admin-ajax.php'),
					'confirm_delete' => esc_html__('Are you sure to delete this widget areas?', 'civi-framework')
				)
			);
		}


		public function get_widget_areas()
		{
			// If the single instance hasn't been set, set it now.
			if (!empty($this->widget_areas)) {
				return $this->widget_areas;
			}

			$db = get_option($this->widget_areas_key);

			if (!empty($db)) {
				$this->widget_areas = array_unique(array_merge($this->widget_areas, $db));
			}
			return $this->widget_areas;
		}

		public function register_custom_widget_areas()
		{
			// If the single instance hasn't been set, set it now.
			if (empty($this->widget_areas)) {
				$this->widget_areas = $this->get_widget_areas();
			}
			$args = array(
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h4 class="widget-title"><span>',
				'after_title'   => '</span></h4>',
			);

			$args = apply_filters('civi_custom_widget_args', $args);

			if (is_array($this->widget_areas)) {
				foreach (array_unique($this->widget_areas) as $widget_area) {
					$args['class']   = 'bin-widgets-custom';
					$args['name']    = $widget_area;
					$args['id']      = sanitize_key($widget_area);
					register_sidebar($args);
				}
			}
		}

		function save_widget_areas()
		{
			update_option($this->widget_areas_key, array_unique($this->widget_areas));
		}

		public function add_new_widget_area_box()
		{
			include_once plugin_dir_path(__FILE__) . 'views/widget-area-box.php';
		}

		public function add_widget_area_area()
		{
			if (!empty($_POST['bin-add-widget-input'])) {
				$this->widget_areas = $this->get_widget_areas();
				array_push($this->widget_areas, $this->check_widget_area_name($_POST['bin-add-widget-input']));
				$this->save_widget_areas();
				wp_redirect(admin_url('widgets.php'));
				die();
			}
		}

		public function check_widget_area_name($name)
		{
			if (empty($GLOBALS['wp_registered_widget_areas']))
				return $name;

			$taken = array();
			foreach ($GLOBALS['wp_registered_widget_areas'] as $widget_area) {
				$taken[] = $widget_area['name'];
			}

			$taken = array_merge($taken, $this->widget_areas);

			if (in_array($name, $taken)) {
				$counter  = substr($name, -1);
				$new_name = "";

				if (!is_numeric($counter)) {
					$new_name = $name . " 1";
				} else {
					$new_name = substr($name, 0, -1) . ((int) $counter + 1);
				}

				$name = $this->check_widget_area_name($new_name);
			}
			return $name;
		}

		function delete_widget_area()
		{
			if (!check_ajax_referer('bin-delete-widget-area-nonce', '_wpnonce')) return;
			if (!empty($_REQUEST['name'])) {
				$name = strip_tags((stripslashes($_REQUEST['name'])));
				$this->widget_areas = $this->get_widget_areas();
				$key = array_search($name, $this->widget_areas);
				if ($key >= 0) {
					unset($this->widget_areas[$key]);
					$this->save_widget_areas();
				}
				echo "widget-area-deleted";
			}
			die();
		}
	}
}
new CIVI_Widget_Areas();
