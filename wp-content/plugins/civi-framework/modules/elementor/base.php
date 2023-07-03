<?php

if (!class_exists('Civi_Base_Elementor_Widget')) {

	class Civi_Base_Elementor_Widget
	{

		function __construct()
		{
			add_action('elementor/init', array($this, 'widget_add_section'));
			add_action('elementor/widgets/register', array($this, 'widget_register'));
			add_action('elementor/frontend/after_enqueue_scripts', array($this, 'enqueue_script'), 10);
			add_action('elementor/frontend/after_enqueue_styles', array($this, 'enqueue_frontend_styles'), 10);
			add_action('elementor/editor/after_enqueue_styles', array($this, 'elementor_editor_styles'));

			add_action('elementor/widget/posts/skins_init', array($this, 'add_skin'));

			add_filter('elementor/icons_manager/additional_tabs', array($this, 'add_icons_library'));

			add_filter('elementor/shapes/additional_shapes', array($this, 'add_shapes_devide'));

			add_action('wp_ajax_civi_job_alerts_action', array($this, 'job_alerts_ajax'));
			add_action('wp_ajax_nopriv_civi_job_alerts_action', array($this, 'job_alerts_ajax'));

			// Register the send_email function to be called when the 'send_email_event' hook is triggered
			add_action('ja_send_email_event', array($this, 'ja_send_email'), 10, 3);

			// Register the new cron schedule
			add_filter('cron_schedules', array($this, 'add_every_month_cron_schedule'));
		}

		public static function send_email_job_alerts($post_id)
		{

			$list_job_alerts = array();

			$email = get_post_meta($post_id, CIVI_METABOX_PREFIX . 'job_alerts_email', true);
			$location = get_post_meta($post_id, CIVI_METABOX_PREFIX . 'job_alerts_location', true);
			$categories = get_post_meta($post_id, CIVI_METABOX_PREFIX . 'job_alerts_categories', true);
			$experience = get_post_meta($post_id, CIVI_METABOX_PREFIX . 'job_alerts_experience', true);
			$frequency = get_post_meta($post_id, CIVI_METABOX_PREFIX . 'job_alerts_frequency', true);
			$skill = get_post_meta($post_id, CIVI_METABOX_PREFIX . 'job_alerts_skill', true);
			$type = get_post_meta($post_id, CIVI_METABOX_PREFIX . 'job_alerts_type', true);
			if ($location === '' && $categories === '' && $experience === '' && $frequency === '' && $skill === '' && $type === '') {
			} else {
				$list_job_alerts[$post_id] = array(
					'email' => $email,
					'location' => $location,
					'categories' => $categories,
					'experience' => $experience,
					'frequency' => $frequency,
					'skill' => $skill,
					'type' => $type,
				);
			}

			if ($list_job_alerts) {
				$job_by_email = array();
				foreach ($list_job_alerts as $key => $val) {
					if ($val['email'] != '') {
						$job_by_email[$val['email']] = array(
							'ids' => array(),
							'frequency' => ''
						);
						$tax_query = array(
							'relation'	=> 'AND',
						);
						$date_query = array(
							'relation'	=> 'AND',
						);
						if ($val['location']) {
							$tax_query[] = array(
								'taxonomy' => 'jobs-location',
								'field'    => 'term_id',
								'terms'    => $val['location'],
							);
						}
						if ($val['categories']) {
							$tax_query[] = array(
								'taxonomy' => 'jobs-categories',
								'field'    => 'term_id',
								'terms'    => $val['categories'],
							);
						}
						if ($val['experience']) {
							$tax_query[] = array(
								'taxonomy' => 'jobs-experience',
								'field'    => 'term_id',
								'terms'    => $val['experience'],
							);
						}
						if ($val['skill']) {
							$tax_query[] = array(
								'taxonomy' => 'jobs-skills',
								'field'    => 'term_id',
								'terms'    => $val['skill'],
							);
						}
						if ($val['type']) {
							$tax_query[] = array(
								'taxonomy' => 'jobs-type',
								'field'    => 'term_id',
								'terms'    => $val['type'],
							);
						}
						if ($val['frequency'] == 'daily') {
							$date_query[] = array(
								'after' => '1 day ago',
							);
						} elseif ($val['frequency'] == 'weekly') {
							$date_query[] = array(
								'after' => '1 week ago',
							);
						} elseif ($val['frequency'] == 'monthly') {
							$date_query[] = array(
								'after' => '1 month ago',
							);
						}
						if ($val['frequency']) {
							$job_by_email[$val['email']]['frequency'] = $val['frequency'];
						}
						$recent_posts_args = array(
							'post_type'	=> 'jobs',
							'posts_per_page' => -1,
							'post_status' => 'publish',
							'tax_query'	=> $tax_query,
							'date_query' => $date_query,
						);

						// The Query
						$recent_posts = new WP_Query($recent_posts_args);

						if ($recent_posts->have_posts()) {
							while ($recent_posts->have_posts()) {
								$recent_posts->the_post();
								$job_by_email[$val['email']]['ids'][] = get_the_ID();
							}
							wp_reset_postdata();
						}
					}
				}

				if ($job_by_email) {
					foreach ($job_by_email as $key => $value) {
						$to = $key;
						$subject = esc_html('Job alert result for ', 'civi-framework') . $key;
						$message = esc_html('Hello, ', 'civi-framework') . '<br>';

						if (count($value['ids']) < 1) {
							$message .= esc_html('Thank you for signing up, you will receive ', 'civi-framework') . $value['frequency'] . esc_html(' job related information.', 'civi-framework') . '<br>';
						} else {
							$message .= esc_html('There are ', 'civi-framework') . count($value['ids']) . esc_html(' jobs found at your request, job listing below:', 'civi-framework') . '<br>';

							foreach ($value['ids'] as $val) {
								$message .= '<a href="' . get_the_permalink($val) . '">' . get_the_title($val) . '</a><br>';
							}
						}

						$message .= 'Best regards,';
						// Use the WordPress built-in scheduler to schedule the email to be sent
						$timestamp = wp_next_scheduled('ja_send_email_event', array($to, $subject, $message));
						if ($timestamp == false) {
							wp_schedule_event(time(), $value['frequency'], 'ja_send_email_event', array($to, $subject, $message));
						}
					}
				}
			}
		}

		// Define the function that will actually send the email
		public function ja_send_email($to, $subject, $message)
		{
			$headers = array('Content-Type: text/html; charset=UTF-8');
			// Use the wp_mail() function to send the email
			wp_mail($to, $subject, $message, $headers);
		}


		// Define the cron schedule for every 5 minutes
		public function add_every_month_cron_schedule($schedules)
		{
			$schedules['monthly'] = array(
				'interval' => 2592000,
				'display' => __('Every month'),
			);
			return $schedules;
		}

		/**
		 * Register Widgets
		 *
		 * Register new Elementor widgets.
		 */
		public function widget_register()
		{
			require_once(CIVI_PLUGIN_DIR . 'modules/elementor/includes/jobs.php');
			require_once(CIVI_PLUGIN_DIR . 'modules/elementor/includes/job-alerts.php');
			require_once(CIVI_PLUGIN_DIR . 'modules/elementor/includes/jobs-category.php');
			require_once(CIVI_PLUGIN_DIR . 'modules/elementor/includes/jobs-apply.php');
			require_once(CIVI_PLUGIN_DIR . 'modules/elementor/includes/jobs-animation.php');
			require_once(CIVI_PLUGIN_DIR . 'modules/elementor/includes/jobs-location.php');
			require_once(CIVI_PLUGIN_DIR . 'modules/elementor/includes/companies.php');
			require_once(CIVI_PLUGIN_DIR . 'modules/elementor/includes/companies-category.php');
			require_once(CIVI_PLUGIN_DIR . 'modules/elementor/includes/search-horizontal.php');
			require_once(CIVI_PLUGIN_DIR . 'modules/elementor/includes/search-vertical.php');
			require_once(CIVI_PLUGIN_DIR . 'modules/elementor/includes/package.php');
		}

		/**
		 * Sections
		 *
		 * Create new section on elementor
		 */
		public function widget_add_section()
		{

			Elementor\Plugin::instance()->elements_manager->add_category(
				'civi-framework',
				array(
					'title'  => __('Civi Framework', 'civi-framework'),
					'active' => false,
				),
				1
			);
		}

		public function enqueue_script()
		{
			wp_enqueue_script('widget-scripts', CIVI_PLUGIN_URL . 'modules/elementor/assets/js/widget.min.js', array('jquery', 'slick'),  false, true);
		}

		public function elementor_editor_styles()
		{
			wp_enqueue_style('editor-style', CIVI_PLUGIN_URL . 'modules/elementor/assets/css/editor.min.css', array(), CIVI_THEME_VERSION);
		}

		public function enqueue_frontend_styles()
		{
			wp_enqueue_style('widget-style', CIVI_PLUGIN_URL . 'modules/elementor/assets/css/widget.min.css', array(), CIVI_THEME_VERSION);
		}

		public function add_skin($widget)
		{

			require_once(CIVI_PLUGIN_DIR . 'modules/elementor/classes/posts-categories.php');

			$widget->add_skin(new Civi_Posts_Categories($widget));
		}

		public function add_icons_library()
		{
			return [
				'la' => [
					'name'          => 'line_awesome',
					'label'         => __('Line Awesome', 'civi-framework'),
					'url'           => CIVI_PLUGIN_URL . 'assets/libs/line-awesome/css/line-awesome.min.css',
					'enqueue'       => [CIVI_PLUGIN_URL . 'assets/libs/line-awesome/css/line-awesome.min.css'],
					'prefix'        => '',
					'displayPrefix' => '',
					'labelIcon'     => '',
					'ver'           => '1.0.1',
					'fetchJson'     =>  CIVI_PLUGIN_URL . 'assets/libs/line-awesome/line-awesome.json',
					'native'        => true,
				]
			];
		}

		public function add_shapes_devide()
		{
			$additional_shapes['oval'] = [
				'title'        => _x('Oval', 'Shapes', 'civi-framework'),
				'has_negative' => true,
				'path'         => CIVI_PLUGIN_DIR . 'modules/elementor/assets/images/oval.svg',
				'url'          => CIVI_PLUGIN_URL . 'modules/elementor/assets/images/oval.svg',
			];

			return $additional_shapes;
		}

		//////////////////////////////////////////////////////////////////
		// Ajax Job Alerts
		//////////////////////////////////////////////////////////////////
		public function job_alerts_ajax()
		{
			$name  = isset($_REQUEST['name']) ? civi_clean(wp_unslash($_REQUEST['name'])) : '';
			$email  = isset($_REQUEST['email']) ? civi_clean(wp_unslash($_REQUEST['email'])) : '';
			$skills  = isset($_REQUEST['skills']) ? civi_clean(wp_unslash($_REQUEST['skills'])) : '';
			$location  = isset($_REQUEST['location']) ? civi_clean(wp_unslash($_REQUEST['location'])) : '';
			$category  = isset($_REQUEST['category']) ? civi_clean(wp_unslash($_REQUEST['category'])) : '';
			$experience  = isset($_REQUEST['experience']) ? civi_clean(wp_unslash($_REQUEST['experience'])) : '';
			$types  = isset($_REQUEST['types']) ? civi_clean(wp_unslash($_REQUEST['types'])) : '';
			$frequency  = isset($_REQUEST['frequency']) ? civi_clean(wp_unslash($_REQUEST['frequency'])) : '';
			$post_type = 'job_alerts';
			$post_title = wp_strip_all_tags($name) ? wp_strip_all_tags($name) : $email;
			$existing_post = get_page_by_title($post_title, 'OBJECT', $post_type);

			if ($existing_post) {
				echo json_encode(
					array(
						'success' => false,
						'class' => 'warning',
						'message' => esc_html('A post with the same title already exists.', 'civi-framework'),
					)
				);
			} else {
				// Create post object
				$new_post = array(
					'post_title'    => $post_title,
					'post_status'   => 'publish',
					'post_type'     => $post_type,
				);

				// Insert the post into the database
				$post_id = wp_insert_post($new_post);

				if ($post_id) {
					setcookie('cookie_job_alerts', 'yes', time() + 365 * 86400, COOKIEPATH, COOKIE_DOMAIN);
				}

				if ($email) {
					update_post_meta($post_id, CIVI_METABOX_PREFIX . 'job_alerts_email', $email);
				}
				if ($skills) {
					update_post_meta($post_id, CIVI_METABOX_PREFIX . 'job_alerts_skill', $skills);
				}
				if ($location) {
					update_post_meta($post_id, CIVI_METABOX_PREFIX . 'job_alerts_location', $location);
				}
				if ($category) {
					update_post_meta($post_id, CIVI_METABOX_PREFIX . 'job_alerts_categories', $category);
				}
				if ($experience) {
					update_post_meta($post_id, CIVI_METABOX_PREFIX . 'job_alerts_experience', $experience);
				}
				if ($types) {
					update_post_meta($post_id, CIVI_METABOX_PREFIX . 'job_alerts_type', $types);
				}
				if ($frequency) {
					update_post_meta($post_id, CIVI_METABOX_PREFIX . 'job_alerts_frequency', $frequency);
				}

				self::send_email_job_alerts($post_id);

				echo json_encode(
					array(
						'success' => true,
						'class' => 'success',
						'id'	=> $post_id,
						'message' => esc_html('Congratulations! You have successfully registered.', 'civi-framework'),
					)
				);
			}
			wp_die();
		}
	}

	new Civi_Base_Elementor_Widget();
}
