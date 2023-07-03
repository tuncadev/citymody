<?php

/**
 * Meta Boxes
 *
 */
if (!class_exists('GLF_Meta_Boxes')) {
	class GLF_Meta_Boxes
	{

		public static $instance;

		/**
		 * list post type apply meta box
		 */
		public $post_types = array();

		/**
		 * Init GLF_Meta_Boxes
		 * *******************************************************
		 */
		public static function init()
		{
			if (self::$instance == NULL) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor GLF_Meta_Boxes
		 * *******************************************************
		 */
		public function __construct()
		{
			add_action( 'add_meta_boxes', array($this, 'register_meta_boxes'));
			add_action( 'save_post', array($this, 'save_meta_box'));

			// Enqueue common styles and scripts
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ) );
			add_action( 'admin_footer', array( $this, 'admin_enqueue_scripts' ), 15 );

			add_filter( 'glf_meta_box_get_value', array($this, 'meta_box_get_value'), 10, 2);
			add_filter( 'glf_meta_box_get_clone_count', array($this, 'meta_box_get_clone_count'), 10, 2);
			add_filter( 'glf_meta_box_get_panel_count', array($this, 'meta_box_get_panel_count'), 10, 2);
		}

		public function admin_enqueue_styles() {
			if ( !$this->is_edit_screen() ) {
				return;
			}
			wp_enqueue_style( GLF_BASE_RESOURCE_PREFIX . 'field_css', GLF_BASE_URL . 'assets/css/field-css.css', array(), GLF_VER );
		}

		public function admin_enqueue_scripts() {
			if ( !$this->is_edit_screen() ) {
				return;
			}
			wp_enqueue_script( GLF_BASE_RESOURCE_PREFIX . 'media', GLF_BASE_URL . 'assets/js/media.js', array(), GLF_VER, true );
			wp_enqueue_script( GLF_BASE_RESOURCE_PREFIX . 'field_config', GLF_BASE_URL . 'assets/js/field-config.js', array(), false, true );
			wp_localize_script( GLF_BASE_RESOURCE_PREFIX . 'field_config' , 'GlfMetaData' , array(
				'ajax_url' => admin_url( 'admin-ajax.php')
			) );
		}

		/**
		 * Register meta boxes
		 * *******************************************************
		 */
		public function register_meta_boxes()
		{
			$meta_configs = &glf_get_meta_boxes_config();
			foreach ($meta_configs as $meta_id => $configs) {
				if (!is_array($configs)) {
					continue;
				}
				$meta_name = isset($configs['name']) ? $configs['name'] : $meta_id;
				$post_type = isset($configs['post_type']) ? $configs['post_type'] : array();
				$this->post_types = array_merge($this->post_types, $post_type );
				$this->post_types = array_unique($this->post_types);

				add_meta_box($meta_id, $meta_name, array($this, 'meta_box_display_callback'), $post_type, 'advanced', 'default', $configs);
			}
		}

		/**
		 * Meta box display callback.
		 *
		 * @param WP_Post $post Current post object.
		 * *******************************************************
		 */
		public function meta_box_display_callback($post, $args)
		{
			$config = &$args['args'];
			glf_set_config_layout(isset($config['layout']) ? $config['layout'] : '');

			$list_section = array();
			if (isset($config['section'])) {
				foreach ($config['section'] as $tab) {
					$list_section[] = array(
						'id'    => $tab['id'],
						'title' => $tab['title'],
						'icon'  => isset($tab['icon']) ? $tab['icon'] : 'dashicons-admin-generic',
					);
				}
			}

			glf_get_template('templates/meta-box-start', array('list_section' => $list_section));
			if (isset($config['section'])) {
				$tab_index = 0;
				foreach ($config['section'] as $tabs) {
					echo sprintf('<div id="section_%s" class="glf-section-container" %s>', $tabs['id'], $tab_index == 0 ? 'style="display:block"' : 'style="display:none"');
					if (isset($tabs['fields'])) {
						$this->meta_box_display_fields($tabs['fields']);
					}
					echo '</div>';
					$tab_index++;
				}
			}
			else {
				$this->meta_box_display_fields($config['fields']);
			}
			glf_get_template('templates/meta-box-end');
		}

		/**
		 * Display Listing Fields
		 * *******************************************************
		 */
		public function meta_box_display_fields(&$fields, $parent_type = '') {
			foreach ($fields as $field) {
				$this->meta_box_display_field($field, $parent_type);
			}
		}

		/**
		 * Display Field
		 * *******************************************************
		 */
		public function meta_box_display_field(&$field, $parent_type = '') {
			if (!isset($field['type'])) {
				return;
			}

			$class_field = glf_get_field_class_name($field['type']);
			$meta = new $class_field($field, $parent_type, 12);
			$meta->render();
		}

		/**
		 * Save meta box content.
		 *
		 * @param int $post_id Post ID
		 * *******************************************************
		 */
		public function save_meta_box($post_id) {
			if ( empty($_POST) ) {
				return;
			}
			$meta_configs = &glf_get_meta_boxes_config();
			$meta_field_keys = $this->get_config_keys($meta_configs);
			foreach ($meta_field_keys as $meta_id => $field_value) {
				$is_clone = $field_value['clone'];
				$meta_value = isset($_POST[$meta_id]) ? stripslashes_deep( $_POST[$meta_id] ) : ($is_clone ? array() : '');
				if ($is_clone && is_array($meta_value)) {
					$max = false;
					foreach ($meta_value as $index_key => &$value) {
						if (!is_int($index_key)) {
							$max = false;
							break;
						}
						$max = $index_key;
					}

					if (($max !== false) && (count($meta_value) - 1 < $max) && ($max < 200)) {
						$newKeys = array_fill_keys(range(0, $max), array());
						$meta_value += $newKeys;
						ksort($meta_value);
					}
				}
				update_post_meta($post_id, $meta_id, $meta_value);
			}
			// exit( '<pre>' . print_r($meta_field_keys, true) . '</pre>'. '<pre>' . print_r($_POST, true) . '</pre>' );
		}

		/**
		 * Get config keys
		 *
		 * @return Array
		 */
		public function get_config_keys($configs) {
			$field_keys = array();
			foreach ($configs as $meta_id => $config) {
				if (!is_array($config)) {
					continue;
				}
				$post_type = isset($config['post_type']) ? $config['post_type'] : array();

				$screen = '';
				if ( is_admin() ) {
      				$screen = get_current_screen();
      			}
				if (($screen != null) && in_array($screen->post_type, $post_type)) {
					if (isset($config['section'])) {
						foreach ($config['section'] as $tabs) {
							if (isset($tabs['fields'])) {
								$field_keys = array_merge($field_keys, glf_get_config_field_keys($tabs['fields'], '', $tabs['id']));
							}
						}
					} else {

						if (isset($config['fields'])) {
							$field_keys = array_merge($field_keys, glf_get_config_field_keys($config['fields'], '', ''));
						}
					}
				}
			}

			return $field_keys;
		}

		/**
		 * Check post is saved
		 * *******************************************************
		 */
		public function is_saved($meta_key, $post_id)
		{
			if (glf_is_edit_page('new')) {
				return false;
			}
			if (!isset($GLOBALS['glf_db_meta_key_' . $post_id])) {
				$GLOBALS['glf_db_meta_key'] = array();
				global $wpdb;
				$rows = $wpdb->get_results($wpdb->prepare("SELECT meta_key FROM $wpdb->postmeta WHERE post_id = %d", $post_id));
				foreach ($rows as $row) {
					$GLOBALS['glf_db_meta_key_' . $post_id][] = $row->meta_key;
				}
			}

			return in_array($meta_key, $GLOBALS['glf_db_meta_key_' . $post_id]);
		}

		/**
		 * Get Meta Box Value
		 *
		 * @param $value
		 * @param $field
		 * @return array|mixed|string
		 */
		public function meta_box_get_value($value, $field) {
			$is_single = !($field->is_clone() || ($field->parent_type === 'repeater'));
			if (!isset($field->params['id'])) {
				return $is_single ? '' : array();
			}

			/**
			 * If field in panel
			 */
			if (!empty($field->panel_id)) {
				$id = get_the_ID();
				$meta_key = $field->panel_id;
				$current_key = $field->params['id'];


				$is_saved = $this->is_saved($meta_key, $id);
				$value = get_post_meta($id, $meta_key, true);
				if (empty($value) && !$is_saved) {
					$value = isset($field->params['default']) ? $field->params['default'] : $value;
				} else {
					if ($field->is_clone() || ($field->parent_type === 'repeater')) {
						$value = isset($value[$field->panel_index]) && isset($value[$field->panel_index][$current_key]) && isset($value[$field->panel_index][$current_key][$field->index])
							? $value[$field->panel_index][$current_key][$field->index] : '';
					}
					else {
						$value = isset($value[$field->panel_index]) && isset($value[$field->panel_index][$current_key])
							? $value[$field->panel_index][$current_key]
							: '';
					}
				}

				return $value;
			}
			/**
			 * If field not in panel
			 */
			$id = get_the_ID();
			$meta_key = $field->params['id'];

			$is_saved = $this->is_saved($meta_key, $id);

			$value = get_post_meta($id, $meta_key, true);
			if (empty($value) && !$is_saved) {
				$value = isset($field->params['default']) ? $field->params['default'] : $value;
			} else {
				if ($field->is_clone() || ($field->parent_type === 'repeater')) {
					$value = isset($value[$field->index]) ? $value[$field->index] : '';
				}
			}

			return $value;
		}

		/**
		 * Get Clone Field Count
		 *
		 * @param $value
		 * @param $field
		 * @return int
		 */
		public function meta_box_get_clone_count($value, $field) {
			$current_key = $field->params['id'];
			if (!empty($field->panel_id)) {
				$meta_key = $field->panel_id;
				$value = get_post_meta(get_the_ID(), $meta_key, true);
				$value = isset($value[$field->panel_index]) ? $value[$field->panel_index] : array();
				$value = isset($value[$current_key]) ? $value[$current_key] : array();
			}
			else {
				$value = get_post_meta(get_the_ID(), $current_key, true);
			}
			return count($value);
		}

		/**
		 * Get Panel Count
		 *
		 * @param $value
		 * @param $field
		 * @return int
		 */
		public function meta_box_get_panel_count($value, $field) {
			$meta_key = $field->params['id'];
			$value = get_post_meta(get_the_ID(), $meta_key, true);
			return is_array($value) ? count($value) : 0;
		}

		/**
		 * method helper
		 * *******************************************************
		 */
		function is_edit_screen( $screen = null )
		{
			if ( ! ( $screen instanceof WP_Screen ) )
			{
				$screen = get_current_screen();
			}
			return 'post' == $screen->base && in_array( $screen->post_type, $this->post_types );
		}

	}

	/**
	 * Instantiate the Meta boxes
	 */
	GLF_Meta_Boxes::init();
}
