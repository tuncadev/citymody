<?php

if (!defined('ABSPATH')) {
	exit;
}

if (!class_exists('Civi_Core')) {
	/**
	 *  The core plugin class
	 *  Class Civi_Core
	 */
	class Civi_Core
	{

		/**
		 * Instance variable for singleton pattern
		 */
		private static $instance = null;

		/**
		 * Return class instance
		 */
		public static function instance()
		{
			if (null == self::$instance) {
				self::$instance = new self;
			}
			return self::$instance;
		}

		/**
		 * Define the core functionality of the plugin
		 */
		public function __construct()
		{
			$this->include_library();
			$this->template_hooks();
			$this->admin_hooks();
		}

		/**
		 * Load the required dependencies for this plugin
		 */
		private function include_library()
		{
			require_once CIVI_PLUGIN_DIR . 'includes/civi-helper.php';
			require_once CIVI_PLUGIN_DIR . 'includes/civi-util.php';

			require_once CIVI_PLUGIN_DIR . 'includes/class-civi-capability.php';
			require_once CIVI_PLUGIN_DIR . 'includes/class-civi-template-loader.php';
			require_once CIVI_PLUGIN_DIR . 'includes/class-civi-shortcodes.php';
			require_once CIVI_PLUGIN_DIR . 'includes/class-civi-ajax.php';
			require_once CIVI_PLUGIN_DIR . 'includes/class-civi-user.php';
			require_once CIVI_PLUGIN_DIR . 'includes/class-civi-breadcrumb.php';
			require_once CIVI_PLUGIN_DIR . 'includes/class-civi-footer.php';

			// Mega Menu
			require_once CIVI_PLUGIN_DIR . 'includes/mega-menu/class-mega-menu.php';
			require_once CIVI_PLUGIN_DIR . 'includes/mega-menu/class-walker-nav-menu.php';

			// Google Review
			include_once CIVI_PLUGIN_DIR . 'includes/google-review/class-google-review.php';

			// Export
			require_once CIVI_PLUGIN_DIR . 'includes/import/class-exporter.php';

			// Import
			require_once ABSPATH . '/wp-admin/includes/class-wp-importer.php';
			require_once CIVI_PLUGIN_DIR . 'includes/import/wp-importer/WXRImporter.php';
			require_once CIVI_PLUGIN_DIR . 'includes/import/wp-importer/WPImporterLogger.php';
			require_once CIVI_PLUGIN_DIR . 'includes/import/wp-importer/WPImporterLoggerCLI.php';
			require_once CIVI_PLUGIN_DIR . 'includes/import/class-wxrimporter.php';
			require_once CIVI_PLUGIN_DIR . 'includes/import/class-import-logger.php';
			require_once CIVI_PLUGIN_DIR . 'includes/import/class-importer.php';
			require_once CIVI_PLUGIN_DIR . 'includes/import/class-content-importer.php';
			require_once CIVI_PLUGIN_DIR . 'includes/import/class-widgets-importer.php';
			require_once CIVI_PLUGIN_DIR . 'includes/admin/class-civi-import.php';
			Civi_Importer::instance();

			// Update
			require_once CIVI_PLUGIN_DIR . 'includes/admin/class-civi-updater.php';

			// Admin
			require_once CIVI_PLUGIN_DIR . 'includes/admin/class-civi-plugins.php';
			require_once CIVI_PLUGIN_DIR . 'includes/admin/class-civi-admin-setup.php';
			require_once CIVI_PLUGIN_DIR . 'includes/admin/class-civi-admin.php';
			require_once CIVI_PLUGIN_DIR . 'includes/admin/class-civi-admin-package.php';
			require_once CIVI_PLUGIN_DIR . 'includes/admin/class-civi-admin-user-package.php';
			require_once CIVI_PLUGIN_DIR . 'includes/admin/class-civi-admin-invoice.php';
			require_once CIVI_PLUGIN_DIR . 'includes/admin/class-civi-metaboxes.php';
			require_once CIVI_PLUGIN_DIR . 'includes/admin/class-civi-profile.php';
			require_once CIVI_PLUGIN_DIR . 'includes/admin/class-civi-location.php';
			require_once CIVI_PLUGIN_DIR . 'includes/admin/class-civi-schedule.php';
			require_once CIVI_PLUGIN_DIR . 'includes/admin/class-civi-rest-api.php';
			require_once CIVI_PLUGIN_DIR . 'includes/admin/class-civi-admin-applicants.php';
			require_once CIVI_PLUGIN_DIR . 'includes/admin/class-civi-admin-meetings.php';
            require_once CIVI_PLUGIN_DIR . 'includes/admin/class-civi-admin-messages.php';
            require_once CIVI_PLUGIN_DIR . 'includes/admin/class-civi-admin-notification.php';
			require_once CIVI_PLUGIN_DIR . 'includes/admin/class-civi-admin-jobs.php';
			require_once CIVI_PLUGIN_DIR . 'includes/admin/class-civi-admin-job-alerts.php';
			require_once CIVI_PLUGIN_DIR . 'includes/admin/class-civi-admin-company.php';
			require_once CIVI_PLUGIN_DIR . 'includes/admin/class-civi-admin-candidate.php';

			// Partials
			include_once CIVI_PLUGIN_DIR . 'includes/partials/package/class-civi-package.php';
			include_once CIVI_PLUGIN_DIR . 'includes/partials/payment/class-civi-payment.php';
			include_once CIVI_PLUGIN_DIR . 'includes/partials/payment/class-civi-trans-log.php';
			include_once CIVI_PLUGIN_DIR . 'includes/partials/invoice/class-civi-invoice.php';
			include_once CIVI_PLUGIN_DIR . 'includes/partials/applicants/class-civi-applicants.php';
			include_once CIVI_PLUGIN_DIR . 'includes/partials/meetings/class-civi-meetings.php';
            include_once CIVI_PLUGIN_DIR . 'includes/partials/messages/class-civi-messages.php';
            include_once CIVI_PLUGIN_DIR . 'includes/partials/notification/class-civi-notification.php';
			include_once CIVI_PLUGIN_DIR . 'includes/partials/jobs/class-civi-jobs.php';
			include_once CIVI_PLUGIN_DIR . 'includes/partials/company/class-civi-company.php';
			include_once CIVI_PLUGIN_DIR . 'includes/partials/candidate/class-civi-candidate.php';
		}

		/**
		 * Register all of the hooks related to the admin area functionality
		 */
		private function admin_hooks()
		{
			/**
			 * Hook Civi_Admin_Setup
			 */
			if (is_admin()) {
				$setup_page = new Civi_Admin_Setup();
				add_action('admin_menu', array($setup_page, 'admin_menu'), 12);
				add_action( 'admin_menu', array($setup_page, 'reorder_admin_menu'), 999 );
			}

			/**
			 * Hook Civi_Admin
			 */
			$civi_admin = new Civi_Admin();
			add_filter('glf_meta_box_config', array($civi_admin, 'register_meta_boxes'));
			add_filter('glf_register_post_type', array($civi_admin, 'register_post_type'));
			add_filter('glf_register_taxonomy', array($civi_admin, 'register_taxonomy'));
			add_filter('glf_register_term_meta', array($civi_admin, 'register_term_meta'));

			add_filter('glf_option_config', array($civi_admin, 'register_options_config'));
			add_action('init', array($civi_admin, 'register_post_status'));
			add_action('after_setup_theme', array($civi_admin, 'remove_admin_bar'));

			/**
			 * Hook Civi_Admin_Jobs
			 */
			$civi_admin_jobs = new Civi_Admin_Jobs();
			add_filter('civi_jobs_slug', array($civi_admin_jobs, 'modify_jobs_slug'));
			add_filter('civi_jobs_has_archive', array($civi_admin_jobs, 'modify_jobs_has_archive'));
			add_filter('civi_jobs_type_slug', array($civi_admin_jobs, 'modify_jobs_type_slug'));
			add_filter('civi_jobs_tags_slug', array($civi_admin_jobs, 'modify_jobs_tags_slug'));
			add_filter('civi_jobs_categories_slug', array($civi_admin_jobs, 'modify_jobs_categories_slug'));
			add_filter('civi_jobs_skills_slug', array($civi_admin_jobs, 'modify_jobs_skills_slug'));
			add_filter('civi_jobs_location_slug', array($civi_admin_jobs, 'modify_jobs_location_slug'));
			add_filter('civi_jobs_career_slug', array($civi_admin_jobs, 'modify_jobs_career_slug'));
			add_filter('civi_jobs_experience_slug', array($civi_admin_jobs, 'modify_jobs_experience_slug'));
			add_filter('civi_jobs_qualification_slug', array($civi_admin_jobs, 'modify_jobs_qualification_slug'));
			add_action('restrict_manage_posts', array($civi_admin_jobs, 'filter_restrict_manage_jobs'));
			add_filter('parse_query', array($civi_admin_jobs, 'jobs_filter'));
			add_action('admin_init', array($civi_admin_jobs, 'approve_jobs'));
			add_action('admin_init', array($civi_admin_jobs, 'expire_jobs'));
			add_action('admin_init', array($civi_admin_jobs, 'hidden_jobs'));
			add_action('admin_init', array($civi_admin_jobs, 'show_jobs'));

			add_action('wp_ajax_civi_action_claim_listing', array($civi_admin_jobs, 'action_claim_listing'));
			add_action('wp_ajax_nopriv_civi_action_claim_listing', array($civi_admin_jobs, 'action_claim_listing'));

			$civi_admin_job_alerts = new Civi_Admin_Job_Alerts();

			/**
			 * Hook Civi_Package_Admin
			 */
			$civi_admin_package = new Civi_Admin_Package();
			add_filter('civi_package_slug', array($civi_admin_package, 'modify_package_slug'));

			// Agent Packages Post Type
			$civi_user_package_admin = new Civi_User_Package_Admin();
			add_filter('civi_user_package_slug', array($civi_user_package_admin, 'modify_user_package_slug'));
			add_action('restrict_manage_posts', array($civi_user_package_admin, 'filter_restrict_manage_user_package'));
			add_action('before_delete_post', array($civi_user_package_admin, 'action_delete_post'));
			add_filter('parse_query', array($civi_user_package_admin, 'user_package_filter'));

			/**
			 * Hook Civi_Invoice_Admin
			 */
			$civi_admin_invoice = new Civi_Admin_Invoice();
			add_action('civi_invoice_slug', array($civi_admin_invoice, 'modify_invoice_slug'));
			add_action('restrict_manage_posts', array($civi_admin_invoice, 'filter_restrict_manage_invoice'));
			add_action('parse_query', array($civi_admin_invoice, 'invoice_filter'));

			/**
			 * Hook Civi_Admin_Applicants
			 */
			$civi_admin_applicants = new Civi_Admin_Applicants();
			add_action('civi_applicants_slug', array($civi_admin_applicants, 'modify_applicants_slug'));
			add_action('restrict_manage_posts', array($civi_admin_applicants, 'filter_restrict_manage_applicants'));
			add_action('parse_query', array($civi_admin_applicants, 'applicants_filter'));

			/**
			 * Hook Civi_Meetings_Admin
			 */
			$civi_admin_meetings = new Civi_Admin_Meetings();
			add_action('civi_meetings_slug', array($civi_admin_meetings, 'modify_meetings_slug'));
			add_action('restrict_manage_posts', array($civi_admin_meetings, 'filter_restrict_manage_meetings'));
			add_action('parse_query', array($civi_admin_meetings, 'meetings_filter'));

            /**
             * Hook Civi_Admin_Messages
             */
            $civi_admin_messages = new Civi_Admin_Messages();
            add_action('parse_query', array($civi_admin_messages, 'messages_filter'));

            /**
             * Hook Civi_Admin_Notification
             */
            $civi_admin_notification = new Civi_Admin_Notification();
            add_action('parse_query', array($civi_admin_notification, 'notification_filter'));

			/**
			 * Hook Civi_Commany_Admin
			 */
			$civi_admin_company = new Civi_Admin_Company();
			add_filter('civi_company_slug', array($civi_admin_company, 'modify_company_url_slug'));
			add_filter('civi_company_has_archive', array($civi_admin_company, 'modify_company_has_archive'));
			add_filter('civi_company_categories_slug', array($civi_admin_company, 'modify_company_categories_url_slug'));
			add_filter('civi_company_location_slug', array($civi_admin_company, 'modify_company_location_url_slug'));
			add_filter('civi_company_size_slug', array($civi_admin_company, 'modify_company_size_url_slug'));
			add_action('restrict_manage_posts', array($civi_admin_company, 'filter_restrict_manage_company'));
			add_action('parse_query', array($civi_admin_company, 'company_filter'));
			add_action('admin_init', array($civi_admin_company, 'approve_company'));

			/**
			 * Hook Civi_Admin_Candidate
			 */
			$civi_admin_candidate = new Civi_Admin_Candidate();
			add_filter('civi_candidate_slug', array($civi_admin_candidate, 'modify_candidate_slug'));
			add_filter('civi_candidate_has_archive', array($civi_admin_candidate, 'modify_candidate_has_archive'));
			add_filter('civi_candidate_categories_slug', array($civi_admin_candidate, 'modify_candidate_categories_url_slug'));
			add_filter('civi_candidate_ages_slug', array($civi_admin_candidate, 'modify_candidate_ages_url_slug'));
			add_filter('civi_candidate_languages_slug', array($civi_admin_candidate, 'modify_candidate_languages_url_slug'));
			add_filter('civi_candidate_qualification_slug', array($civi_admin_candidate, 'modify_candidate_qualification_url_slug'));
			add_filter('civi_candidate_yoe_slug', array($civi_admin_candidate, 'modify_candidate_yoe_url_slug'));
			add_filter('civi_candidate_salary_types_slug', array($civi_admin_candidate, 'modify_candidate_salary_types_url_slug'));
			add_filter('civi_candidate_education_levels_slug', array($civi_admin_candidate, 'modify_candidate_education_levels_url_slug'));
			add_filter('civi_candidate_skills_slug', array($civi_admin_candidate, 'modify_candidate_skills_url_slug'));
			add_filter('civi_candidate_locations_slug', array($civi_admin_candidate, 'modify_candidate_locations_url_slug'));
			add_action('restrict_manage_posts', array($civi_admin_candidate, 'filter_restrict_manage_candidate'));
			add_action('parse_query', array($civi_admin_candidate, 'candidate_filter'));
			add_action('admin_init', array($civi_admin_candidate, 'show_candidates'));

			/**
			 * Hook Civi_Rest_API
			 */
			$civi_rest_api = new Civi_Rest_API();
			add_action('rest_api_init', array($civi_rest_api, 'register_fields_api'));

			$profile = new Civi_Profile();
			add_filter('show_user_profile', array($profile, 'custom_user_profile_fields'));
			add_filter('edit_user_profile', array($profile, 'custom_user_profile_fields'));
			add_action('edit_user_profile_update', array($profile, 'update_custom_user_profile_fields'));
			add_action('personal_options_update', array($profile, 'update_custom_user_profile_fields'));
			add_action('admin_head', array($profile, 'my_profile_upload_js'));

			/**
			 * Hook Civi_Plugins
			 */
			$civi_plugins = new Civi_Plugins();
			add_action('wp_ajax_process_plugin_actions', array($civi_plugins, 'process_plugin_actions'));
			add_action('wp_ajax_nopriv_process_plugin_actions', array($civi_plugins, 'process_plugin_actions'));

			/**
			 * Hook Civi_Metaboxes
			 */
			$civi_metaboxes = new Civi_Metaboxes();
			add_action('load-post.php', array($civi_metaboxes, 'meta_boxes_setup'));
			add_action('load-post-new.php', array($civi_metaboxes, 'meta_boxes_setup'));

			/**
			 * Hook Civi_Location
			 */
			$civi_location = new Civi_Location();
			add_action('admin_menu', array($civi_location, 'countries_create_menu'));
			add_filter('admin_init', array($civi_location, 'countries_register_setting'));

			$civi_schedule = new Civi_Schedule();
			register_deactivation_hook(__FILE__, array($civi_schedule, 'civi_per_listing_check_expire'));
			add_action('init', array($civi_schedule, 'scheduled_hook'));
			add_action('civi_per_listing_check_expire', array($civi_schedule, 'per_listing_check_expire'));

			if (is_admin()) {
				global $pagenow;

				// candidates custom columns
				if ($pagenow == 'edit.php' && isset($_GET['post_type']) && esc_attr($_GET['post_type']) == 'candidate') {
					add_filter('manage_edit-candidate_columns', array($civi_admin_candidate, 'register_custom_column_titles'));
					add_action('manage_posts_custom_column', array($civi_admin_candidate, 'display_custom_column'));
					add_filter('manage_edit-candidate_sortable_columns', array($civi_admin_candidate, 'sortable_columns'));
				}

				// jobs custom columns
				if ($pagenow == 'edit.php' && isset($_GET['post_type']) && esc_attr($_GET['post_type']) == 'jobs') {
					add_filter('manage_edit-jobs_columns', array($civi_admin_jobs, 'register_custom_column_titles'));
					add_action('manage_posts_custom_column', array($civi_admin_jobs, 'display_custom_column'));
					add_filter('manage_edit-jobs_sortable_columns', array($civi_admin_jobs, 'sortable_columns'));
					add_filter('request', array($civi_admin_jobs, 'column_orderby'));
					add_filter('post_row_actions', array($civi_admin_jobs, 'modify_list_row_actions'), 10, 2);
				}

				// job alerts custom columns
				if ($pagenow == 'edit.php' && isset($_GET['post_type']) && esc_attr($_GET['post_type']) == 'job_alerts') {
					add_filter('manage_edit-job_alerts_columns', array($civi_admin_job_alerts, 'register_custom_column_titles'));
					add_action('manage_posts_custom_column', array($civi_admin_job_alerts, 'display_custom_column'));
					add_filter('manage_edit-job_alerts_sortable_columns', array($civi_admin_job_alerts, 'sortable_columns'));
				}

				// package custom columns
				if ($pagenow == 'edit.php' && isset($_GET['post_type']) && esc_attr($_GET['post_type']) == 'package') {
					add_filter('manage_edit-package_columns', array($civi_admin_package, 'register_custom_column_titles'));
					add_action('manage_posts_custom_column', array($civi_admin_package, 'display_custom_column'));
					add_filter('post_row_actions', array($civi_admin_package, 'modify_list_row_actions'), 10, 2);
				}

				// agent package custom columns
				if ($pagenow == 'edit.php' && isset($_GET['post_type']) && esc_attr($_GET['post_type']) == 'user_package') {
					add_filter('manage_edit-user_package_columns', array($civi_user_package_admin, 'register_custom_column_titles'));
					add_action('manage_posts_custom_column', array($civi_user_package_admin, 'display_custom_column'));
					add_action('before_delete_post', array($civi_user_package_admin, 'action_delete_post'));
					add_filter('post_row_actions', array($civi_user_package_admin, 'modify_list_row_actions'), 10, 2);
				}

				// Invoice custom columns
				if ($pagenow == 'edit.php' && isset($_GET['post_type']) && esc_attr($_GET['post_type']) == 'invoice') {
					add_filter('manage_edit-invoice_columns', array($civi_admin_invoice, 'register_custom_column_titles'));
					add_action('manage_posts_custom_column', array($civi_admin_invoice, 'display_custom_column'));
					add_filter('manage_edit-invoice_sortable_columns', array($civi_admin_invoice, 'sortable_columns'));
					add_filter('request', array($civi_admin_invoice, 'column_orderby'));
					add_filter('post_row_actions', array($civi_admin_invoice, 'modify_list_row_actions'), 10, 2);
				}

				// Company custom columns
				if ($pagenow == 'edit.php' && isset($_GET['post_type']) && esc_attr($_GET['post_type']) == 'company') {
					add_filter('manage_edit-company_columns', array($civi_admin_company, 'register_custom_column_titles'));
					add_action('manage_posts_custom_column', array($civi_admin_company, 'display_custom_column'));
					add_filter('manage_edit-company_sortable_columns', array($civi_admin_company, 'sortable_columns'));
					add_filter('post_row_actions', array($civi_admin_company, 'modify_list_row_actions'), 10, 2);
				}

				// Applicants custom columns
				if ($pagenow == 'edit.php' && isset($_GET['post_type']) && esc_attr($_GET['post_type']) == 'applicants') {
					add_filter('manage_edit-applicants_columns', array($civi_admin_applicants, 'register_custom_column_titles'));
					add_action('manage_posts_custom_column', array($civi_admin_applicants, 'display_custom_column'));
					add_filter('manage_edit-applicants_sortable_columns', array($civi_admin_applicants, 'sortable_columns'));
					add_filter('request', array($civi_admin_applicants, 'column_orderby'));
					add_filter('post_row_actions', array($civi_admin_applicants, 'modify_list_row_actions'), 10, 2);
				}

				// Meetings custom columns
				if ($pagenow == 'edit.php' && isset($_GET['post_type']) && esc_attr($_GET['post_type']) == 'meetings') {
					add_filter('manage_edit-meetings_columns', array($civi_admin_meetings, 'register_custom_column_titles'));
					add_action('manage_posts_custom_column', array($civi_admin_meetings, 'display_custom_column'));
					add_filter('manage_edit-meetings_sortable_columns', array($civi_admin_meetings, 'sortable_columns'));
					add_filter('request', array($civi_admin_meetings, 'column_orderby'));
					add_filter('post_row_actions', array($civi_admin_meetings, 'modify_list_row_actions'), 10, 2);
				}

                // Messages custom columns
                if ($pagenow == 'edit.php' && isset($_GET['post_type']) && esc_attr($_GET['post_type']) == 'messages') {
                    add_filter('manage_edit-messages_columns', array($civi_admin_messages, 'register_custom_column_titles'));
                    add_action('manage_posts_custom_column', array($civi_admin_messages, 'display_custom_column'));
                    add_filter('manage_edit-messages_sortable_columns', array($civi_admin_messages, 'sortable_columns'));
                    add_filter('request', array($civi_admin_messages, 'column_orderby'));
                    add_filter('post_row_actions', array($civi_admin_messages, 'modify_list_row_actions'), 10, 2);
                }

                // Notification custom columns
                if ($pagenow == 'edit.php' && isset($_GET['post_type']) && esc_attr($_GET['post_type']) == 'notification') {
                    add_filter('manage_edit-notification_columns', array($civi_admin_notification, 'register_custom_column_titles'));
                    add_action('manage_posts_custom_column', array($civi_admin_notification, 'display_custom_column'));
                    add_filter('manage_edit-notification_sortable_columns', array($civi_admin_notification, 'sortable_columns'));
                    add_filter('request', array($civi_admin_notification, 'column_orderby'));
                    add_filter('post_row_actions', array($civi_admin_notification, 'modify_list_row_actions'), 10, 2);
                }
			}
		}

		/**
		 * Register all of the hooks related to the public-facing functionality
		 */
		private function template_hooks()
		{
			/**
			 * Hook Civi_Template_Loader
			 */
			$civi_template_loader = new Civi_Template_Loader();

			add_action('civi_apply_single_jobs', array($civi_template_loader, 'civi_form_apply_jobs'), 1);
            add_action('wp_footer', array($civi_template_loader, 'civi_form_setting_meetings'));
			add_action('wp_footer', array($civi_template_loader, 'civi_form_reschedule_meeting'));
            add_action('wp_footer', array($civi_template_loader, 'civi_form_setting_messages'));
			add_action('wp_footer', array($civi_template_loader, 'civi_form_invite_candidate'));
            add_action('wp_footer', array($civi_template_loader, 'civi_form_mess_applicants'));

			add_action('post_type_link', array($civi_template_loader, 'wpa_show_permalinks'), 1, 2);
			add_action('init', array($civi_template_loader, 'generated_rewrite_rules'));

			add_filter('template_include', array($civi_template_loader, 'template_loader'));
			add_action('admin_enqueue_scripts', array($civi_template_loader, 'admin_enqueue'));
			add_action('wp_enqueue_scripts', array($civi_template_loader, 'enqueue_styles'));
			add_action('wp_enqueue_scripts', array($civi_template_loader, 'enqueue_scripts'));

			/**
			 * Hook Civi_Ajax
			 */
			$civi_ajax = new Civi_Ajax();

			add_action('wp_ajax_preview_job', array($civi_ajax, 'preview_job'));
			add_action('wp_ajax_nopriv_preview_job', array($civi_ajax, 'preview_job'));

			add_action('wp_ajax_civi_jobs_archive_ajax', array($civi_ajax, 'civi_jobs_archive_ajax'));
			add_action('wp_ajax_nopriv_civi_jobs_archive_ajax', array($civi_ajax, 'civi_jobs_archive_ajax'));

			add_action('wp_ajax_civi_company_archive_ajax', array($civi_ajax, 'civi_company_archive_ajax'));
			add_action('wp_ajax_nopriv_civi_company_archive_ajax', array($civi_ajax, 'civi_company_archive_ajax'));

			add_action('wp_ajax_civi_filter_jobs_dashboard', array($civi_ajax, 'civi_filter_jobs_dashboard'));
			add_action('wp_ajax_nopriv_civi_filter_jobs_dashboard', array($civi_ajax, 'civi_filter_jobs_dashboard'));

			add_action('wp_ajax_civi_filter_applicants_dashboard', array($civi_ajax, 'civi_filter_applicants_dashboard'));
			add_action('wp_ajax_nopriv_civi_filter_applicants_dashboard', array($civi_ajax, 'civi_filter_applicants_dashboard'));

			add_action('wp_ajax_civi_filter_my_wishlist', array($civi_ajax, 'civi_filter_my_wishlist'));
			add_action('wp_ajax_nopriv_civi_filter_my_wishlist', array($civi_ajax, 'civi_filter_my_wishlist'));

			add_action('wp_ajax_civi_filter_my_follow', array($civi_ajax, 'civi_filter_my_follow'));
			add_action('wp_ajax_nopriv_civi_filter_my_follow', array($civi_ajax, 'civi_filter_my_follow'));

			add_action('wp_ajax_civi_filter_my_review', array($civi_ajax, 'civi_filter_my_review'));
			add_action('wp_ajax_nopriv_civi_filter_my_review', array($civi_ajax, 'civi_filter_my_review'));

			add_action('wp_ajax_civi_filter_my_invite', array($civi_ajax, 'civi_filter_my_invite'));
			add_action('wp_ajax_nopriv_civi_filter_my_invite', array($civi_ajax, 'civi_filter_my_invite'));

			add_action('wp_ajax_civi_filter_follow_candidate', array($civi_ajax, 'civi_filter_follow_candidate'));
			add_action('wp_ajax_nopriv_civi_filter_follow_candidate', array($civi_ajax, 'civi_filter_follow_candidate'));

			add_action('wp_ajax_civi_filter_invite_candidate', array($civi_ajax, 'civi_filter_invite_candidate'));
			add_action('wp_ajax_nopriv_civi_filter_invite_candidate', array($civi_ajax, 'civi_filter_invite_candidate'));

			add_action('wp_ajax_civi_filter_my_apply', array($civi_ajax, 'civi_filter_my_apply'));
			add_action('wp_ajax_nopriv_civi_filter_my_apply', array($civi_ajax, 'civi_filter_my_apply'));

			add_action('wp_ajax_civi_filter_company_dashboard', array($civi_ajax, 'civi_filter_company_dashboard'));
			add_action('wp_ajax_nopriv_civi_filter_company_dashboard', array($civi_ajax, 'civi_filter_company_dashboard'));

			add_action('wp_ajax_civi_company_related', array($civi_ajax, 'civi_company_related'));
			add_action('wp_ajax_nopriv_civi_company_related', array($civi_ajax, 'civi_company_related'));

			add_action('wp_ajax_civi_filter_candidates_dashboard', array($civi_ajax, 'civi_filter_candidates_dashboard'));
			add_action('wp_ajax_nopriv_civi_filter_candidates_dashboard', array($civi_ajax, 'civi_filter_candidates_dashboard'));

			add_action('wp_ajax_civi_update_profile_ajax', array($civi_ajax, 'civi_update_profile_ajax'));
			add_action('wp_ajax_nopriv_civi_update_profile_ajax', array($civi_ajax, 'civi_update_profile_ajax'));

			add_action('wp_ajax_civi_change_password_ajax', array($civi_ajax, 'civi_change_password_ajax'));
			add_action('wp_ajax_nopriv_civi_change_password_ajax', array($civi_ajax, 'civi_change_password_ajax'));

			add_action('wp_ajax_getpost', array($civi_ajax, 'get_post_by_catid'));
			add_action('wp_ajax_nopriv_getpost', array($civi_ajax, 'get_post_by_catid'));

			//chart jobs
			add_action('wp_ajax_civi_chart_ajax', array($civi_ajax, 'civi_chart_ajax'));
			add_action('wp_ajax_nopriv_civi_chart_ajax', array($civi_ajax, 'civi_chart_ajax'));

			//chart employer
			add_action('wp_ajax_civi_chart_employer_ajax', array($civi_ajax, 'civi_chart_employer_ajax'));
			add_action('wp_ajax_nopriv_civi_chart_employer_ajax', array($civi_ajax, 'civi_chart_employer_ajax'));

			//chart candidate
			add_action('wp_ajax_civi_chart_candidate_ajax', array($civi_ajax, 'civi_chart_candidate_ajax'));
			add_action('wp_ajax_nopriv_civi_chart_candidate_ajax', array($civi_ajax, 'civi_chart_candidate_ajax'));

			// Add to wishlist
			add_action('wp_ajax_civi_add_to_wishlist', array($civi_ajax, 'civi_add_to_wishlist'));
			add_action('wp_ajax_nopriv_civi_add_to_wishlist', array($civi_ajax, 'civi_add_to_wishlist'));

			// Add to follow company
			add_action('wp_ajax_civi_add_to_follow', array($civi_ajax, 'civi_add_to_follow'));
			add_action('wp_ajax_nopriv_civi_add_to_follow', array($civi_ajax, 'civi_add_to_follow'));

			// Add to follow candidate
			add_action('wp_ajax_civi_add_to_follow_candidate', array($civi_ajax, 'civi_add_to_follow_candidate'));
			add_action('wp_ajax_nopriv_civi_add_to_follow_candidate', array($civi_ajax, 'civi_add_to_follow_candidate'));

			// Add to apply
			add_action('wp_ajax_jobs_add_to_apply', array($civi_ajax, 'jobs_add_to_apply'));
			add_action('wp_ajax_nopriv_jobs_add_to_apply', array($civi_ajax, 'jobs_add_to_apply'));

			// Add to invite
			add_action('wp_ajax_civi_add_to_invite', array($civi_ajax, 'civi_add_to_invite'));
			add_action('wp_ajax_nopriv_civi_add_to_invite', array($civi_ajax, 'civi_add_to_invite'));

			// Ajax search
			add_action('wp_ajax_civi_search_jobs_ajax', array($civi_ajax, 'civi_search_jobs_ajax'));
			add_action('wp_ajax_nopriv_civi_search_jobs_ajax', array($civi_ajax, 'civi_search_jobs_ajax'));

			// Ajax Search Candidate
			add_action('wp_ajax_civi_candidate_archive_ajax', array($civi_ajax, 'civi_candidate_archive_ajax'));
			add_action('wp_ajax_nopriv_civi_candidate_archive_ajax', array($civi_ajax, 'civi_candidate_archive_ajax'));

			// Ajax Thumbnail
            add_action('wp_ajax_civi_thumbnail_upload_ajax', array($civi_ajax, 'civi_thumbnail_upload_ajax'));
            add_action('wp_ajax_nopriv_civi_thumbnail_upload_ajax', array($civi_ajax, 'civi_thumbnail_upload_ajax'));

            add_action('wp_ajax_civi_thumbnail_remove_ajax', array($civi_ajax, 'civi_thumbnail_remove_ajax'));
            add_action('wp_ajax_nopriv_civi_thumbnail_remove_ajax', array($civi_ajax, 'civi_thumbnail_remove_ajax'));

            // Ajax Avatar
            add_action('wp_ajax_civi_avatar_upload_ajax', array($civi_ajax, 'civi_avatar_upload_ajax'));
            add_action('wp_ajax_nopriv_civi_avatar_upload_ajax', array($civi_ajax, 'civi_avatar_upload_ajax'));

            add_action('wp_ajax_civi_avatar_remove_ajax', array($civi_ajax, 'civi_avatar_remove_ajax'));
            add_action('wp_ajax_nopriv_civi_avatar_remove_ajax', array($civi_ajax, 'civi_avatar_remove_ajax'));

            // Ajax Gallery
            add_action('wp_ajax_civi_gallery_upload_ajax', array($civi_ajax, 'civi_gallery_upload_ajax'));
            add_action('wp_ajax_nopriv_civi_gallery_upload_ajax', array($civi_ajax, 'civi_gallery_upload_ajax'));

            add_action('wp_ajax_civi_gallery_remove_ajax', array($civi_ajax, 'civi_gallery_remove_ajax'));
            add_action('wp_ajax_nopriv_civi_gallery_remove_ajax', array($civi_ajax, 'civi_agallery_remove_ajax'));

            // Ajax Elementor
            add_action('wp_ajax_civi_el_jobs_pagination_ajax', array($civi_ajax, 'civi_el_jobs_pagination_ajax'));
            add_action('wp_ajax_nopriv_civi_el_jobs_pagination_ajax', array($civi_ajax, 'civi_el_jobs_pagination_ajax'));

			/**
			 * Hook Civi_Jobs
			 */
			$civi_jobs = new Civi_Jobs();
			add_filter('civi_single_jobs_before', array($civi_jobs, 'civi_set_jobs_view_date'));
			add_filter('civi_single_jobs_before', array($civi_jobs, 'civi_jobs_breadcrumb'));

			add_action('wp_ajax_jobs_submit_ajax', array($civi_jobs, 'jobs_submit_ajax'));
			add_action('wp_ajax_nopriv_jobs_submit_ajax', array($civi_jobs, 'jobs_submit_ajax'));

			add_action('wp_ajax_civi_contact_agent_ajax', array($civi_jobs, 'contact_agent_ajax'));
			add_action('wp_ajax_nopriv_civi_contact_agent_ajax', array($civi_jobs, 'contact_agent_ajax'));

			/**
			 * Hook Civi_company
			 */
			$civi_company = new Civi_Company();
			add_action('civi_single_company_before', array($civi_company, 'civi_company_breadcrumb'), 5);

			add_action('wp_ajax_civi_company_submit_review_ajax', array($civi_company, 'submit_review_ajax'));
			add_action('wp_ajax_nopriv_civi_company_submit_review_ajax', array($civi_company, 'submit_review_ajax'));

			add_filter('civi_company_rating_meta', array($civi_company, 'rating_meta_filter'), 4, 9);

			add_action('wp_ajax_civi_company_submit_reply_ajax', array($civi_company, 'submit_reply_ajax'));
			add_action('wp_ajax_nopriv_company_submit_reply_ajax', array($civi_company, 'submit_reply_ajax'));

			add_action('wp_ajax_company_submit_ajax', array($civi_company, 'company_submit_ajax'));
			add_action('wp_ajax_nopriv_company_submit_ajax', array($civi_company, 'company_submit_ajax'));

			/**
			 * Hook Civi_Payment
			 */
			$civi_payment = new Civi_Payment();
			add_action('wp_ajax_civi_paypal_payment_per_package_ajax', array($civi_payment, 'paypal_payment_per_package_ajax'));
			add_action('wp_ajax_nopriv_civi_paypal_payment_per_package_ajax', array($civi_payment, 'paypal_payment_per_package_ajax'));

			add_action('wp_ajax_civi_wire_transfer_per_package_ajax', array($civi_payment, 'wire_transfer_per_package_ajax'));
			add_action('wp_ajax_nopriv_civi_wire_transfer_per_package_ajax', array($civi_payment, 'wire_transfer_per_package_ajax'));

			add_action('wp_ajax_civi_free_package_ajax', array($civi_payment, 'free_package_ajax'));
			add_action('wp_ajax_nopriv_civi_free_package_ajax', array($civi_payment, 'free_package_ajax'));

            add_action('wp_ajax_civi_woocommerce_payment_per_package_ajax', array( $civi_payment, 'woocommerce_payment_per_package_ajax') );
            add_action('wp_ajax_nopriv_civi_woocommerce_payment_per_package_ajax', array( $civi_payment, 'woocommerce_payment_per_package_ajax') );

			/**
			 * Hook Civi_Candidate
			 */
			$civi_candidate = new Civi_Candidate();
			add_action('civi_single_candidate_before', array($civi_candidate, 'civi_candidate_breadcrumb'), 5);
			add_filter('civi_single_candidate_before', array($civi_candidate, 'civi_set_candidate_view_date'));

			add_action('wp_ajax_civi_candidate_submit_review_ajax', array($civi_candidate, 'submit_review_ajax'));
			add_action('wp_ajax_nopriv_civi_candidate_submit_review_ajax', array($civi_candidate, 'submit_review_ajax'));

			add_filter('civi_candidate_rating_meta', array($civi_candidate, 'rating_meta_filter'), 4, 9);

			add_action('wp_ajax_civi_candidate_submit_reply_ajax', array($civi_company, 'submit_reply_ajax'));
			add_action('wp_ajax_nopriv_candidate_submit_reply_ajax', array($civi_company, 'submit_reply_ajax'));

			add_action('wp_ajax_upload_candidate_attachment_ajax', array($civi_candidate, 'upload_candidate_attachment_ajax'));
			add_action('wp_ajax_nopriv_upload_candidate_attachment_ajax', array($civi_candidate, 'upload_candidate_attachment_ajax'));

			add_action('wp_ajax_remove_candidate_attachment_ajax', array($civi_candidate, 'remove_candidate_attachment_ajax'));
			add_action('wp_ajax_nopriv_remove_candidate_attachment_ajax', array($civi_candidate, 'remove_candidate_attachment_ajax'));

			add_action('wp_ajax_candidate_submit_ajax', array($civi_candidate, 'candidate_submit_ajax'));
			add_action('wp_ajax_nopriv_candidate_submit_ajax', array($civi_candidate, 'candidate_submit_ajax'));

			add_filter('update_civi_candidate_meta_rating', array($civi_candidate, 'update_rating_meta'), 4, 9);

			/**
			 * Hook Civi_Meetings
			 */
			$civi_meetings = new Civi_Meetings();
			add_action('wp_ajax_civi_meetings_settings', array($civi_meetings, 'civi_meetings_settings'));
			add_action('wp_ajax_nopriv_civi_meetings_settings', array($civi_meetings, 'civi_meetings_settings'));

			add_action('wp_ajax_civi_meetings_reschedule_ajax', array($civi_meetings, 'civi_meetings_reschedule_ajax'));
			add_action('wp_ajax_nopriv_civi_meetings_reschedule_ajax', array($civi_meetings, 'civi_meetings_reschedule_ajax'));

			add_action('wp_ajax_civi_meetings_upcoming_dashboard', array($civi_meetings, 'civi_meetings_upcoming_dashboard'));
			add_action('wp_ajax_nopriv_civi_meetings_upcoming_dashboard', array($civi_meetings, 'civi_meetings_upcoming_dashboard'));

			add_action('wp_ajax_civi_meetings_completed_dashboard', array($civi_meetings, 'civi_meetings_completed_dashboard'));
			add_action('wp_ajax_nopriv_civi_meetings_completed_dashboard', array($civi_meetings, 'civi_meetings_completed_dashboard'));

			add_action('wp_ajax_civi_meetings_candidate_dashboard', array($civi_meetings, 'civi_meetings_candidate_dashboard'));
			add_action('wp_ajax_nopriv_civi_meetings_candidate_dashboard', array($civi_meetings, 'civi_meetings_candidate_dashboard'));

            /**
             * Hook Civi_Messages
             */
            $civi_messages = new Civi_Messages();
            add_action('wp_ajax_civi_send_messages', array($civi_messages, 'civi_send_messages'));
            add_action('wp_ajax_nopriv_civi_send_messages', array($civi_messages, 'civi_send_messages'));

            add_action('wp_ajax_civi_write_messages', array($civi_messages, 'civi_write_messages'));
            add_action('wp_ajax_nopriv_civi_write_messages', array($civi_messages, 'civi_write_messages'));

            add_action('wp_ajax_civi_messages_list_user', array($civi_messages, 'civi_messages_list_user'));
            add_action('wp_ajax_nopriv_civi_messages_list_user', array($civi_messages, 'civi_messages_list_user'));

            add_action('wp_ajax_civi_refresh_messages', array($civi_messages, 'civi_refresh_messages'));
            add_action('wp_ajax_nopriv_civi_refresh_messages', array($civi_messages, 'civi_refresh_messages'));

            /**
             * Hook Civi_Notification
             */
            $civi_notification = new Civi_Notification();
            add_action('wp_ajax_civi_refresh_notification', array($civi_notification, 'civi_refresh_notification'));
            add_action('wp_ajax_nopriv_civi_refresh_notification', array($civi_notification, 'civi_refresh_notification'));
		}

		/**
		 * Get template path
		 */
		public function template_path()
		{
			return apply_filters('civi_template_path', 'civi-framework/');
		}
	}
}

if (!function_exists('CIVI')) {
	function CIVI()
	{
		return Civi_Core::instance();
	}
}
// Global for backwards compatibility.
$GLOBALS['Civi_Core'] = CIVI();
