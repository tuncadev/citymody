<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

/**
 * @since 5.3
 */
class FrmProApplicationsController {

	/**
	 * @return void
	 */
	public static function load_assets_for_applications_index() {
		$plugin_url      = FrmProAppHelper::plugin_url();
		$version         = FrmProDb::$plug_version;
		$js_dependencies = array( 'formidable_applications', 'popper', 'bootstrap_tooltip' );
		wp_register_script( 'formidable_pro_applications', $plugin_url . '/js/admin/applications/applications.js', $js_dependencies, $version, true );

		$can_add_views         = FrmProApplicationsHelper::views_is_active_and_supports_applications();
		$expected_views_folder = WP_PLUGIN_DIR . '/formidable-views/';
		$views_exists          = file_exists( $expected_views_folder . 'formidable-views.php' );
		$views_is_up_to_date   = $views_exists && file_exists( $expected_views_folder . 'classes/controllers/FrmViewsApplicationsController.php' );

		$js_vars = array(
			'allApplicationsUrl' => admin_url( 'edit-tags.php?taxonomy=frm_application' ),
			'canAddViews'        => $can_add_views,
			'canAddApplications' => FrmProApplicationsHelper::current_user_can_edit_applications(),
			'viewsIsUpdated'     => $views_is_up_to_date,
			'viewsExists'        => $views_exists,
		);

		if ( ! $can_add_views ) {
			$views_install_url = self::get_views_install_url();
			if ( is_string( $views_install_url ) ) {
				$js_vars['viewsInstallUrl'] = $views_install_url;
			}
		}

		wp_localize_script( 'formidable_pro_applications', 'frmProApplicationsVars', $js_vars );
		wp_enqueue_script( 'formidable_pro_applications' );

		self::load_new_application_modal_assets();

		wp_register_style( 'formidable_pro_applications', $plugin_url . '/css/admin/applications/applications.css', array(), $version );
		wp_enqueue_style( 'formidable_pro_applications' );
	}

	/**
	 * @return string|false
	 */
	private static function get_views_install_url() {
		$api             = new FrmFormApi();
		$addons          = $api->get_api_info();
		$visual_views_id = 28058856;

		if ( ! is_array( $addons ) || ! array_key_exists( $visual_views_id, $addons ) || empty( $addons[ $visual_views_id ]['url'] ) ) {
			return false;
		}

		return $addons[ $visual_views_id ]['url'];
	}

	/**
	 * @return void
	 */
	public static function load_new_application_modal_assets() {
		self::register_common_js();

		$plugin_url      = FrmProAppHelper::plugin_url();
		$version         = FrmProDb::$plug_version;
		$js_dependencies = array( 'formidable_dom', 'frm_applications_common' );
		wp_register_script( 'formidable_pro_new_application_modal', $plugin_url . '/js/admin/applications/new_application_modal.js', $js_dependencies, $version, true );

		$js_vars = array(
			'canAddViews'     => FrmProApplicationsHelper::views_is_active_and_supports_applications(),
			'viewsUpgradeUrl' => FrmAppHelper::admin_upgrade_link(
				array(
					'medium'  => 'application-views',
					'content' => 'applications',
				)
			),
			'importUrl'       => admin_url( 'admin.php?page=formidable-import' ),
		);
		wp_localize_script( 'formidable_pro_new_application_modal', 'frmNewApplicationModalVars', $js_vars );

		wp_enqueue_script( 'formidable_pro_new_application_modal' );

		wp_register_style( 'formidable_pro_new_application_modal', $plugin_url . '/css/admin/applications/new_application_modal.css', array( 'frm_applications_common' ), $version );
		wp_enqueue_style( 'formidable_pro_new_application_modal' );
	}

	/**
	 * @return void
	 */
	public static function register_common_js() {
		$plugin_url      = FrmProAppHelper::plugin_url();
		$version         = FrmProDb::$plug_version;
		$js_dependencies = array( 'formidable_dom' );
		wp_register_script( 'frm_applications_common', $plugin_url . '/js/admin/applications/common.js', $js_dependencies, $version, true );

		$js_vars = array(
			'proImagesUrl' => FrmProAppHelper::plugin_url() . '/images/',
			'canEditForms' => current_user_can( 'frm_edit_forms' ),
			'canEditViews' => current_user_can( 'frm_edit_displays' ),
			'canEditPages' => current_user_can( 'edit_pages' ),
		);
		wp_localize_script( 'frm_applications_common', 'frmCommonApplicationVars', $js_vars );
	}

	/**
	 * @return void
	 */
	public static function register_common_css() {
		$plugin_url = FrmProAppHelper::plugin_url();
		$version    = FrmProDb::$plug_version;
		wp_register_style( 'frm_applications_common', $plugin_url . '/css/admin/applications/common.css', array(), $version );
	}

	/**
	 * Extend application data so icon (application thumbnail) and url (link to xml) are included in dashboard JavaScript data.
	 *
	 * @param array $keys
	 * @return array
	 */
	public static function application_data_keys( $keys ) {
		array_push( $keys, 'icon', 'url' );
		return $keys;
	}

	/**
	 * Add application meta on XML import / Template install.
	 * Also adds summary of import to the response to show in modal.
	 *
	 * @param array $response
	 * @param array $args {
	 *     @type array $form
	 *     @type array $imported
	 * }
	 * @return array
	 */
	public static function xml_response( $response, $args ) {
		if ( empty( $args['imported'] ) || ! empty( $response['message'] ) ) {
			return $response;
		}

		FrmProAppController::create_taxonomies();

		$application_name = FrmAppHelper::get_post_param( 'application_name', '', 'sanitize_text_field' );

		if ( $application_name ) {
			// Create a new application with a posted application name.
			$term = FrmProApplication::create( $application_name );
			if ( ! is_array( $term ) ) {
				return $response;
			}

			$term_id = $term['term_id'];
		} else {
			// Use an existing posted application id.
			$application_id = FrmAppHelper::get_post_param( 'application_id', 0, 'absint' );
			if ( ! $application_id ) {
				return $response;
			}

			$term = get_term( $application_id, 'frm_application' );
			if ( ! ( $term instanceof WP_Term ) ) {
				return $response;
			}

			$term_id = $term->term_id;
		}

		$term_id                        = (int) $term_id;
		$imported                       = $args['imported'];
		$response['applicationSummary'] = array(
			'applicationId' => $term_id,
			'form'          => array(),
			'view'          => array(),
			'page'          => array(),
		);

		if ( ! empty( $imported['forms'] ) ) {
			$response['applicationSummary']['form'] = self::add_forms_to_application( $term_id, $imported['forms'] );
		}

		if ( ! empty( $imported['posts'] ) ) {
			$response['applicationSummary'] = array_merge(
				$response['applicationSummary'],
				self::add_posts_to_application( $term_id, $imported['posts'] )
			);
		}

		$response['redirect'] = FrmProApplicationsHelper::get_edit_url( $term_id );

		return $response;
	}

	/**
	 * Add forms to application and return a summary of imported form names.
	 *
	 * @param int        $term_id
	 * @param array<int> $form_ids
	 * @return array<array> Imported form details.
	 */
	private static function add_forms_to_application( $term_id, $form_ids ) {
		$where            = array(
			'id' => $form_ids,
		);
		$form_results     = FrmDb::get_results( 'frm_forms', $where, 'id, name' );
		$form_names_by_id = wp_list_pluck( $form_results, 'name', 'id' );

		$form_details = array();
		foreach ( $form_ids as $form_id ) {
			if ( ! array_key_exists( $form_id, $form_names_by_id ) ) {
				continue;
			}

			FrmProApplication::add_form_to_application( $term_id, $form_id );
			$form_details[] = array(
				'id'   => $form_id,
				'name' => $form_names_by_id[ $form_id ],
			);
		}

		return $form_details;
	}

	/**
	 * Add posts (views and pages) to application and return a summary of imported post names.
	 *
	 * @param int        $term_id
	 * @param array<int> $post_ids
	 * @return array<array> Imported post names as two arrays (views and pages).
	 */
	private static function add_posts_to_application( $term_id, $post_ids ) {
		$post_results     = FrmDb::get_results(
			'posts',
			array(
				'ID'            => $post_ids,
				'post_status !' => 'trash',
				'post_type'     => array( 'frm_display', 'page' ),
			),
			'ID, post_title, post_type'
		);
		$view_names_by_id = array();
		$page_names_by_id = array();

		foreach ( $post_results as $result ) {
			if ( 'frm_display' === $result->post_type ) {
				$view_names_by_id[ $result->ID ] = $result->post_title;
			} elseif ( 'page' === $result->post_type ) {
				$page_names_by_id[ $result->ID ] = $result->post_title;
			}
		}

		$view_details = array();
		$page_details = array();
		foreach ( $post_ids as $post_id ) {
			if ( isset( $view_names_by_id[ $post_id ] ) ) {
				$view_details[] = array(
					'id'   => $post_id,
					'name' => $view_names_by_id[ $post_id ],
				);
			} elseif ( isset( $page_names_by_id[ $post_id ] ) ) {
				$page_details[] = array(
					'id'   => $post_id,
					'name' => $page_names_by_id[ $post_id ],
				);
			} else {
				// Continue to avoid adding a post id that doesn't match results.
				// Styles and form actions get imported but should not get adding to taxonomy.
				continue;
			}

			FrmProApplication::add_post_to_application( $term_id, $post_id );
		}

		return array(
			'view' => $view_details,
			'page' => $page_details,
		);
	}

	/**
	 * Flag application pages as white pages (defines white background, some standard style rules).
	 *
	 * @param bool $is_white_page
	 * @return bool
	 */
	public static function is_white_page( $is_white_page ) {
		if ( $is_white_page ) {
			return true;
		}

		global $pagenow;
		switch ( $pagenow ) {
			case 'term.php':
			case 'edit-tags.php':
				return 'frm_application' === FrmAppHelper::simple_get( 'taxonomy' );
			default:
				return false;
		}
	}

	/**
	 * Render the New Application and Import buttons after header title.
	 *
	 * @param string $context possible values include 'index' and 'edit'.
	 * @return void
	 */
	public static function header_after_title( $context ) {
		if ( in_array( $context, array( 'index', 'list' ), true ) ) {
			FrmAppHelper::include_svg();

			if ( FrmProApplicationsHelper::current_user_can_edit_applications() ) {
				FrmAppHelper::add_new_item_link(
					array(
						'class' => 'frm-new-application-button',
					)
				);
			}

			// Import button
		} elseif ( 'edit' === $context ) {
			self::render_button( 'settings' );
		}
	}

	/**
	 * Maybe add an add button in page header title after span element.
	 *
	 * @param string $context possible values include 'index' and 'edit'.
	 * @return void
	 */
	public static function header_inside_title_after_span( $context ) {
		if ( 'edit' === $context ) {
			FrmAppHelper::add_new_item_link(
				array(
					'class'       => 'frm-applications-add-item-button',
					'button_text' => __( 'Add Item', 'formidable-pro' ),
				)
			);
		}
	}

	/**
	 * Render a button view file in the application buttons directory.
	 *
	 * @param string $filename
	 * @return void
	 */
	public static function render_button( $filename ) {
		require FrmProAppHelper::plugin_path() . '/classes/views/applications/buttons/' . $filename . '.php';
	}

	/**
	 * Add hooks before installing a form so the Application ID can be added to the new form.
	 *
	 * @return void
	 */
	public static function before_install_form() {
		$application_id = FrmAppHelper::get_post_param( 'application_id', 0, 'absint' );
		if ( ! $application_id ) {
			// No application id set. Do not add hooks.
			return;
		}

		add_action(
			'frm_build_new_form',
			function( $form_id ) use ( $application_id ) {
				FrmProApplication::add_form_to_application( $application_id, $form_id );
			}
		);
	}

	/**
	 * @return void
	 */
	public static function get_application_item_options() {
		FrmProApplicationsHelper::custom_application_permission_check();

		$type = FrmAppHelper::simple_get( 'type' );
		if ( ! $type ) {
			wp_die();
		}

		$options = self::get_item_options_for_type( $type );
		$data    = compact( 'options' );
		wp_send_json_success( $data );
	}

	/**
	 * @param string $type supports 'form', 'page', 'view'.
	 * @return array<string>
	 */
	private static function get_item_options_for_type( $type ) {
		switch ( $type ) {
			case 'form':
				$table   = 'frm_forms';
				$where   = array(
					'status'      => 'published',
					'is_template' => 0,
				);
				$columns = array( 'id', 'name' );
				break;
			case 'page':
			case 'view':
				$post_type = 'view' === $type ? 'frm_display' : $type;
				$table     = 'posts';
				$where     = array(
					'post_type'   => $post_type,
					'post_status' => array( 'private', 'publish', 'draft' ),
				);
				$columns   = array( 'ID', 'post_title' );
				break;
			default:
				return array();
		}

		list( $id_column, $title_column ) = $columns;
		$results                          = FrmDb::get_results( $table, $where, implode( ',', $columns ) );

		$titles = array_column( $results, $title_column );
		array_multisort( $titles, SORT_ASC, SORT_NATURAL | SORT_FLAG_CASE, $results );

		$output = array();
		foreach ( $results as $result ) {
			$output[] = array(
				'value' => (int) $result->$id_column,
				'label' => $result->$title_column,
			);
		}

		return $output;
	}

	/**
	 * Get meta about an Application template via AJAX action.
	 *
	 * @return void
	 */
	public static function get_application_template_meta() {
		FrmProApplicationsHelper::templates_permission_check();
		check_ajax_referer( 'frm_ajax', 'nonce' );

		$url = FrmAppHelper::get_param( 'xml', '', 'post', 'esc_url_raw' );
		if ( ! $url ) {
			die( 0 );
		}

		if ( is_callable( 'FrmAppHelper::validate_url_is_in_s3_bucket' ) && ! FrmAppHelper::validate_url_is_in_s3_bucket( $url, 'xml' ) ) {
			die( esc_html__( 'The template you are trying to install could not be validated.', 'formidable-pro' ) );
		}

		$response = wp_remote_get( $url ); // Note: If Query Monitor is active, I see 502 errors when trying to call wp_remote_get in Docker.
		$body     = wp_remote_retrieve_body( $response );
		$xml      = simplexml_load_string( $body );

		if ( ! $xml ) {
			wp_send_json_error( __( 'There was an error reading the form template', 'formidable' ) );
			die();
		}

		if ( $xml instanceof SimpleXMLElement && isset( $xml->Code ) && in_array( (string) $xml->Code, array( 'NoSuchKey', 'AccessDenied' ), true ) ) { // phpcs:ignore WordPress.NamingConventions
			wp_send_json_error( (string) $xml->Message ); // phpcs:ignore WordPress.NamingConventions
			die();
		}

		$found = array(
			'form' => array(),
			'view' => array(),
			'page' => array(),
		);

		if ( isset( $xml->form ) ) {
			foreach ( $xml->form as $form ) {
				if ( ! empty( $form->parent_form_id ) ) {
					// Do not show repeater forms in the list of forms.
					continue;
				}
				$found['form'][] = (string) $form->name;
			}
		}

		if ( isset( $xml->view ) ) {
			foreach ( $xml->view as $view ) {
				if ( isset( $view->status ) && 'trash' === (string) $view->status ) {
					// If template view happens to be trash, don't mention it.
					continue;
				}
				if ( ! isset( $view->post_type ) || ! in_array( (string) $view->post_type, array( 'frm_display', 'page' ), true ) ) {
					// Only include views, skip any false positives.
					continue;
				}

				if ( 'frm_display' === (string) $view->post_type ) {
					$found['view'][] = (string) $view->title;
				} else {
					$found['page'][] = (string) $view->title;
				}
			}
		}

		$data = compact( 'found' );
		wp_send_json_success( $data );
		die();
	}
}
