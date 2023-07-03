<?php

/**
 * Smart Framework: Meta Boxes
 *
 * @package SmartFramework
 * @subpackage MetaBox
 * @since 1.0
 */
if (!class_exists('IS_Term_Meta')) {
	class IS_Term_Meta
	{

		public static $instance;

		/**
		 * Init IS_Term_Meta
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
		 * Constructor IS_Term_Meta
		 * *******************************************************
		 */
		public function __construct()
		{
			add_action('admin_init', array($this, 'register_term_meta'));
			add_action('wp_ajax_glf_tax_meta_form', array($this, 'ajax_tax_meta_form'));

			add_filter('glf_term_meta_get_value', array($this, 'meta_box_get_value'), 10, 2);
			add_filter('glf_term_meta_get_clone_count', array($this, 'meta_box_get_clone_count'), 10, 2);
			add_filter('glf_term_meta_get_panel_count', array($this, 'meta_box_get_panel_count'), 10, 2);
		}

		public function admin_enqueue_styles()
		{
			wp_enqueue_style(GLF_BASE_RESOURCE_PREFIX . 'field_css', GLF_BASE_URL . 'assets/css/field-css.css', array(), GLF_VER);
		}

		public function admin_enqueue_scripts()
		{
			wp_enqueue_script('jquery-ui-core');
			wp_enqueue_script('jquery-ui-sortable');
			wp_enqueue_script('jquery-ui-autocomplete');

			wp_enqueue_script(GLF_BASE_RESOURCE_PREFIX . 'media', GLF_BASE_URL . 'assets/js/media.js', array(), GLF_VER, true);
			wp_enqueue_script(GLF_BASE_RESOURCE_PREFIX . 'field_config', GLF_BASE_URL . 'assets/js/field-config.js', array(), GLF_VER, true);
			wp_enqueue_script(GLF_BASE_RESOURCE_PREFIX . 'term_meta', GLF_BASE_URL . 'assets/js/term-meta.js', array(), GLF_VER, true);
			wp_localize_script(GLF_BASE_RESOURCE_PREFIX . 'field_config', 'glfMetaData', array(
				'ajax_url' => admin_url('admin-ajax.php')
			));
		}

		/**
		 * Register meta boxes
		 * *******************************************************
		 */
		public function register_term_meta()
		{
			$meta_configs = &glf_get_term_meta_config();
			foreach ($meta_configs as $meta_id => $config) {
				if (!is_array($config)) {
					continue;
				}

				$taxonomies = isset($config['taxonomy']) ? $config['taxonomy'] : array();
				if (!is_array($taxonomies)) {
					$taxonomies = array($taxonomies);
				}
				$taxonomy_priority = isset($config['priority']) ? $config['priority'] : 10;
				foreach ($taxonomies as $taxonomy) {
					add_action($taxonomy . '_add_form_fields', array($this, 'term_meta_add_display'), $taxonomy_priority, 2);
					add_action($taxonomy . '_edit_form_fields', array($this, 'term_meta_edit_display'), $taxonomy_priority, 2);

					add_action('created_' . $taxonomy, array($this, 'save_term_meta'), $taxonomy_priority, 2);
					add_action('edited_' . $taxonomy, array($this, 'save_term_meta'), $taxonomy_priority, 2);
				}
			}
		}

		/**
		 * Set current term id to $GLOBALS variable
		 *
		 * @param $id
		 */
		public function set_current_term_id($id)
		{
			$GLOBALS['glf_current_term_id'] = $id;
		}

		/**
		 * Get current term id from $GLOBALS
		 *
		 * @return int
		 */
		public function get_current_term_id()
		{
			return $GLOBALS['glf_current_term_id'];
		}


		public function term_meta_add_display($taxonomy)
		{
			$this->set_current_term_id(0);
			/**
			 * Set layout to default for list
			 */
			glf_set_config_layout('');
			$this->term_meta_display($taxonomy, true);
		}

		public function term_meta_edit_display($term, $taxonomy)
		{
			$this->set_current_term_id($term->term_id);
?>
			<tr class="form-field term-group-wrap">
				<td colspan="2">
					<?php $this->term_meta_display($taxonomy, false); ?>
				</td>
			</tr>
		<?php
		}

		public function term_meta_display($taxonomy, $is_new)
		{
			wp_enqueue_media();
			// Enqueue common styles and scripts
			add_action('admin_footer', array($this, 'admin_enqueue_styles'));
			add_action('admin_footer', array($this, 'admin_enqueue_scripts'), 15);


			echo sprintf('<div class="glf-term-meta-wrapper" data-taxonomy="%s">', $taxonomy);
			$this->bind_tax_meta_form($taxonomy, $is_new);
			echo '</div>';
		}

		public function bind_tax_meta_form($taxonomy, $is_new)
		{
			/**
			 * Set config type: for prefix or affix filter, action
			 */
			glf_set_config_type('term_meta');

			$meta_configs = &glf_get_term_meta_config();
		?>
			<div class="glf-meta-config-wrapper">
				<?php
				foreach ($meta_configs as $config) {
					$taxonomies = isset($config['taxonomy']) ? $config['taxonomy'] : array();
					if (in_array($taxonomy, $taxonomies)) {
						$term_meta_wrapper_class = 'glf-term-meta-item-wrapper';
						if (!$is_new) {
							glf_set_config_layout(isset($config['layout']) ? $config['layout'] : '');
							$term_meta_wrapper_class .= ' glf-term-meta-edit-page';
						}

						echo sprintf('<div class="%s">', $term_meta_wrapper_class);
				?>
						<?php if (!empty($config['name'])) : ?>
							<h3 class="glf-taxonomy-title"><?php esc_html_e($config['name']); ?></h3>
						<?php endif; ?>
				<?php
						glf_get_template('templates/meta-box-start', array('list_section' => array()));
						if (isset($config['section'])) {
							$tab_index = 0;
							foreach ($config['section'] as $tabs) {
								echo sprintf('<div id="section_%s" class="glf-section-container">', $tabs['id']);
								if (isset($tabs['fields'])) {
									$this->term_meta_display_fields($tabs['fields']);
								}
								echo '</div>';
								$tab_index++;
							}
						} else {
							$this->term_meta_display_fields($config['fields']);
						}
						glf_get_template('templates/meta-box-end');
						echo '</div>';
					}
				}
				?>
			</div><!-- /.glf-meta-config-wrapper -->
<?php
		}

		/**
		 * Display Listing Fields
		 * *******************************************************
		 */
		public function term_meta_display_fields(&$fields, $parent_type = '')
		{
			foreach ($fields as $field) {
				$this->term_meta_display_field($field, $parent_type);
			}
		}

		public function ajax_tax_meta_form()
		{
			/**
			 * Set config type: for prefix or affix filter, action
			 */
			glf_set_config_type('term_meta');
			$taxonomy = isset($_REQUEST['taxonomy']) ? glf_clean(wp_unslash($_REQUEST['taxonomy'])) : '';
			$this->bind_tax_meta_form($taxonomy, true);
			die();
		}

		/**
		 * Display Field
		 * *******************************************************
		 */
		public function term_meta_display_field(&$field, $parent_type = '')
		{
			if (!isset($field['type'])) {
				return;
			}

			$class_field = glf_get_field_class_name($field['type']);
			$meta = new $class_field($field, $parent_type, 12);
			$meta->render();
		}


		/**
		 * Save term meta
		 *
		 * @param $term_id
		 * @param $tag_id
		 */
		public function save_term_meta($term_id, $tag_id)
		{
			if (empty($_POST)) {
				return;
			}
			/**
			 * Set config type: for prefix or affix filter, action
			 */
			glf_set_config_type('term_meta');
			$taxonomy  = isset($_POST['taxonomy']) ? glf_clean(wp_unslash($_POST['taxonomy'])) : '';

			$meta_configs = &glf_get_term_meta_config();
			$meta_field_keys = $this->get_config_keys($meta_configs, $taxonomy);
			foreach ($meta_field_keys as $meta_id => $field_value) {
				$is_clone = $field_value['clone'];
				$meta_value = isset($_POST[$meta_id]) ? glf_clean(wp_unslash($_POST[$meta_id]))  : ($is_clone ? array() : '');

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
				update_term_meta($term_id, $meta_id, $meta_value);
			}

			// 			$terms_pl = get_terms( array(
			// 			    'taxonomy' => 'place-categories',
			// 			    'hide_empty' => false,
			// 			) );

			// 			foreach ($terms_pl as $key => $term) {
			// 				update_term_meta($term->term_id, 'place_categories_icon_marker', '');
			// 			}

			// 			$terms_pa = get_terms( array(
			// 			    'taxonomy' => 'place-amenities',
			// 			    'hide_empty' => false,
			// 			) );

			// 			foreach ($terms_pa as $key => $term) {
			// 				update_term_meta($term->term_id, 'place_amenities_icon', '');
			// 			}

			// 			$terms_pc = get_terms( array(
			// 			    'taxonomy' => 'place-city',
			// 			    'hide_empty' => false,
			// 			) );

			// 			foreach ($terms_pc as $key => $term) {
			// 				update_term_meta($term->term_id, 'place_city_featured_image', '');
			// 				update_term_meta($term->term_id, 'place_city_banner_image', '');
			// 			}

			// 			$terms_pc = get_terms( array(
			// 			    'taxonomy' => 'place-city',
			// 			    'hide_empty' => false,
			// 			) );

			// 			foreach ($terms_pc as $key => $term) {
			// 				update_term_meta($term->term_id, 'place_city_featured_image', '');
			// 				update_term_meta($term->term_id, 'place_city_banner_image', '');
			// 			}

			// 			$args = array(
			// 				'post_type'			=> array( 'post', 'place', 'product'),
			// 				'posts_per_page'	=> -1,
			// 			);

			// 			// The Query
			// 			$the_query = new WP_Query( $args );

			// 			// The Loop
			// 			if ( $the_query->have_posts() ) {
			// 			    while ( $the_query->have_posts() ) {
			// 			        $the_query->the_post();
			// 			        update_post_meta( get_the_ID(), 'civi-place_booking_banner', '' );
			// 			        update_post_meta( get_the_ID(), 'civi-place_images', '' );
			// 			        delete_post_thumbnail( get_the_ID() );
			// 			    }
			// 			}
			// 			/* Restore original Post Data */
			// 			wp_reset_postdata();
		}


		/**
		 * Get config keys
		 *
		 * @return Array
		 */
		public function get_config_keys($configs, $taxonomy = '')
		{
			$field_keys = array();
			foreach ($configs as $meta_id => $config) {
				if (!is_array($config)) {
					continue;
				}
				$taxonomies = isset($config['taxonomy']) ? $config['taxonomy'] : array();
				if (!empty($taxonomy)) {
					$screen = get_current_screen();
					if ($screen) {
						$taxonomy = $screen->taxonomy;
					}
				}

				if (in_array($taxonomy, $taxonomies)) {
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
		public function is_saved($meta_key, $term_id)
		{
			$screen = get_current_screen();
			if (($screen->base === 'edit-tags')) {
				return false;
			}

			if (!isset($GLOBALS['glf_db_meta_key_' . $term_id])) {
				$GLOBALS['glf_db_meta_key_' . $term_id] = array();
				global $wpdb;
				$rows = $wpdb->get_results($wpdb->prepare("SELECT meta_key FROM $wpdb->termmeta WHERE term_id = %d", $term_id));
				foreach ($rows as $row) {
					$GLOBALS['glf_db_meta_key_' . $term_id][] = $row->meta_key;
				}
			}

			return in_array($meta_key, $GLOBALS['glf_db_meta_key_' . $term_id]);
		}

		/**
		 * Get Meta Box Value
		 *
		 * @param $value
		 * @param $field
		 * @return array|mixed|string
		 */
		public function meta_box_get_value($value, $field)
		{
			$is_single = !($field->is_clone() || ($field->parent_type === 'repeater'));
			if (!isset($field->params['id'])) {
				return $is_single ? '' : array();
			}

			/**
			 * If field in panel
			 */
			if (!empty($field->panel_id)) {
				$id = $this->get_current_term_id();
				$meta_key = $field->panel_id;
				$current_key = $field->params['id'];


				$is_saved = $this->is_saved($meta_key, $id);
				$value = get_term_meta($id, $meta_key, true);
				if (empty($value) && !$is_saved) {
					$value = isset($field->params['default']) ? $field->params['default'] : $value;
				} else {
					if ($field->is_clone() || ($field->parent_type === 'repeater')) {
						$value = isset($value[$field->panel_index]) && isset($value[$field->panel_index][$current_key]) && isset($value[$field->panel_index][$current_key][$field->index])
							? $value[$field->panel_index][$current_key][$field->index] : '';
					} else {
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
			$id = $this->get_current_term_id();
			$meta_key = $field->params['id'];

			$is_saved = $this->is_saved($meta_key, $id);

			$value = get_term_meta($id, $meta_key, true);
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
		public function meta_box_get_clone_count($value, $field)
		{
			$current_key = $field->params['id'];
			$term_id = $this->get_current_term_id();
			if (!empty($field->panel_id)) {
				$meta_key = $field->panel_id;
				$value = get_term_meta($term_id, $meta_key, true);
				$value = isset($value[$field->panel_index]) ? $value[$field->panel_index] : array();
				$value = isset($value[$current_key]) ? $value[$current_key] : array();
			} else {
				$value = get_term_meta($term_id, $current_key, true);
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
		public function meta_box_get_panel_count($value, $field)
		{
			$meta_key = $field->params['id'];
			$term_id = $this->get_current_term_id();
			$value = get_term_meta($term_id, $meta_key, true);
			return is_array($value) ?  count($value) : 0;
		}
	}

	/**
	 * Instantiate the Meta boxes
	 */
	IS_Term_Meta::init();
}
