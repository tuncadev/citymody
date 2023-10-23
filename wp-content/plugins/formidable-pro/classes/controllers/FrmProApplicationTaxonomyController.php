<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

/**
 * @since 5.3
 */
class FrmProApplicationTaxonomyController {

	/**
	 * @var FrmProApplicationRelationHelper $helper
	 */
	private static $helper;

	/**
	 * @return void
	 */
	public static function init() {
		self::register_application_taxonomy();
	}

	/**
	 * @return bool
	 */
	public static function maybe_include_embed_script() {
		if ( self::on_application_edit_page() ) {
			add_filter( 'frm_api_include_embed_form_script', '__return_true' );
		}
	}

	/**
	 * Check if active page is term.php?taxonomy=frm_application as Embed buttons are included in this table.
	 *
	 * @return bool
	 */
	private static function on_application_edit_page() {
		global $pagenow;
		return 'term.php' === $pagenow && 'frm_application' === FrmAppHelper::simple_get( 'taxonomy' );
	}

	/**
	 * @return void
	 */
	public static function admin_init() {
		$plugin_url      = FrmProAppHelper::plugin_url();
		$version         = FrmProDb::$plug_version;
		$js_dependencies = array( 'wp-hooks', 'formidable_dom', 'popper', 'bootstrap_tooltip', 'formidable_embed' );

		FrmProApplicationsController::register_common_js();
		FrmProApplicationsController::register_common_css();

		wp_register_script( 'formidable_edit_application_term', $plugin_url . '/js/admin/taxonomy/frm_application.js', array_merge( $js_dependencies, array( 'frm_applications_common' ) ), $version, true );
		wp_register_style( 'formidable_edit_application_term', $plugin_url . '/css/admin/taxonomy/frm_application.css', array( 'frm_applications_common' ), $version );

		wp_register_script( 'formidable_custom_applications_index', $plugin_url . '/js/admin/taxonomy/frm_application_list.js', $js_dependencies, $version, true );
		wp_register_style( 'formidable_custom_applications_index', $plugin_url . '/css/admin/taxonomy/frm_application_list.css', array(), $version );

		if ( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( 'formidable_edit_application_term', 'formidable-pro', FrmProAppHelper::plugin_path() . '/languages' );
			wp_set_script_translations( 'formidable_custom_applications_index', 'formidable-pro', FrmProAppHelper::plugin_path() . '/languages' );
		}

		if ( self::should_set_applications_in_submenu() ) {
			self::set_applications_active_in_submenu();
		}

		self::maybe_enqueue_assets();
	}

	/**
	 * Load the css in the head to prevent icon flash.
	 *
	 * @since 5.3.1
	 */
	private static function maybe_enqueue_assets() {
		if ( ! self::on_application_edit_page() ) {
			return;
		}

		wp_enqueue_style( 'formidable-admin' );
		wp_enqueue_style( 'formidable_edit_application_term' );

		global $wp_taxonomies;
		if ( isset( $wp_taxonomies['frm_application'] ) ) {
			$wp_taxonomies['frm_application']->show_ui = true;
		}
	}

	/**
	 * Add custom applications data to wp_ajax_frm_get_applications_data AJAX hook.
	 *
	 * @param array $data
	 * @return array
	 */
	public static function applications_data( $data ) {
		$view = FrmAppHelper::get_param( 'view', '', 'get', 'sanitize_text_field' );

		if ( 'templates' === $view ) {
			// Avoid adding applications for 'templates' view option.
			return $data;
		}

		$limit                = 'applications' === $view ? -1 : 8;
		$data['applications'] = self::get_custom_applications( $limit + 1 );

		if ( -1 !== $limit && count( $data['applications'] ) > $limit ) {
			$data['moreApplications'] = 1;
			array_pop( $data['applications'] );
		}

		return $data;
	}

	/**
	 * @param int $limit 0 for no limit.
	 * @return array
	 */
	private static function get_custom_applications( $limit = 0 ) {
		$terms        = get_terms(
			array(
				'taxonomy'   => 'frm_application',
				'hide_empty' => false,
			)
		);
		$applications = self::sort_custom_applications( $terms, $limit );
		unset( $terms );

		if ( $limit ) {
			$applications = array_slice( $applications, 0, $limit );
		}

		return array_reduce( $applications, array( __CLASS__, 'custom_application_reducer' ), array() );
	}

	/**
	 * @param array             $applications
	 * @param FrmProApplication $application
	 * @return array
	 */
	private static function custom_application_reducer( $applications, $application ) {
		$applications[] = $application->as_js_object();
		return $applications;
	}

	/**
	 * Sort applications, and wrap in FrmProApplication model.
	 *
	 * @param array<WP_Term> $terms
	 * @return array<FrmProApplication>
	 */
	private static function sort_custom_applications( $terms, $limit = 0 ) {
		if ( 0 === $limit ) {
			$sort_type = FrmAppHelper::get_param( 'sort', '', 'get', 'sanitize_text_field' );
			if ( $sort_type && 'alphabet' === $sort_type ) {
				usort(
					$terms,
					function( $a, $b ) {
						return strcmp( $a->name, $b->name );
					}
				);
				return array_map(
					function( $term ) {
						// Alphabet sorting is only used for dropdowns.
						// The timestamp is not passed in this case as it is not used in dropdowns.
						return new FrmProApplication( $term, '' );
					},
					$terms
				);
			}
		}

		$args = array(
			'order_by' => 'meta_value DESC',
		);
		if ( $limit ) {
			$args['limit'] = $limit;
		}

		$metas = FrmDb::get_results(
			'termmeta',
			array(
				'meta_key' => '_frm_updated_at',
			),
			'term_id, meta_value',
			$args
		);

		$terms_by_id = array_reduce(
			$terms,
			function( $total, $term ) {
				$total[ $term->term_id ] = $term;
				return $total;
			},
			array()
		);

		$applications = array();
		foreach ( $metas as $meta ) {
			if ( ! array_key_exists( $meta->term_id, $terms_by_id ) ) {
				continue;
			}

			$applications[] = new FrmProApplication( $terms_by_id[ $meta->term_id ], $meta->meta_value );
		}

		return $applications;
	}

	/**
	 * Returns whether or not the active page is related to Applications but not the index page which is highlighted by default.
	 * If this is true, the Applications submenu item will appear active for the page.
	 *
	 * @return bool
	 */
	private static function should_set_applications_in_submenu() {
		global $pagenow;
		return in_array( $pagenow, array( 'term.php', 'edit-tags.php' ), true ) && 'frm_application' === FrmAppHelper::simple_get( 'taxonomy' );
	}

	/**
	 * Adds filters that set Formidable to the active page with Applications as the active submenu page.
	 *
	 * @return void
	 */
	private static function set_applications_active_in_submenu() {
		add_filter(
			'parent_file',
			function() {
				return 'formidable';
			}
		);
		add_filter(
			'submenu_file',
			function() {
				return 'formidable-applications';
			}
		);
	}

	/**
	 * @return void
	 */
	private static function register_application_taxonomy() {
		$capability = FrmProApplicationsHelper::get_custom_applications_capability();

		if ( ! current_user_can( $capability ) && current_user_can( 'administrator' ) ) {
			$capability = 'administrator';
		}

		register_taxonomy(
			'frm_application',
			array( 'page', 'frm_display', 'frm_form' ),
			array(
				'hierarchical' => false,
				'labels'       => array(
					'name'                       => __( 'Applications' ),
					'singular_name'              => __( 'Application' ),
					'search_items'               => __( 'Search Applications' ),
					'popular_items'              => null,
					'all_items'                  => __( 'All Applications' ),
					'edit_item'                  => __( 'Edit Application' ),
					'update_item'                => __( 'Update Application' ),
					'add_new_item'               => __( 'Add New Application' ),
					'new_item_name'              => __( 'New Application Name' ),
					'separate_items_with_commas' => null,
					'add_or_remove_items'        => null,
					'choose_from_most_used'      => null,
					'back_to_items'              => __( '&larr; Go to Applications' ),
				),
				'capabilities' => array(
					'manage_terms' => $capability,
					'edit_terms'   => $capability,
					'delete_terms' => $capability,
					'assign_terms' => $capability,
				),
				'query_var'    => false,
				'rewrite'      => false,
				'public'       => false,
				'show_ui'      => false,
				'_builtin'     => true,
			)
		);
	}

	/**
	 * @return void
	 */
	public static function maybe_render_custom_applications_list() {
		global $pagenow;
		if ( 'edit-tags.php' !== $pagenow || 'frm_application' !== FrmAppHelper::simple_get( 'taxonomy' ) ) {
			return;
		}

		// Define a $plugin_page global value.
		// Without this, PHP8.2+ will throw a deprecated message because $plugin_page cannot be null.
		// The warning comes from a preg_replace call in wp-admin/includes/plugin.php
		global $plugin_page;
		$plugin_page = 'formidable'; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

		FrmProApplicationsHelper::custom_application_permission_check();

		wp_enqueue_style( 'formidable-admin' );
		wp_enqueue_script( 'formidable_custom_applications_index' );
		wp_enqueue_style( 'formidable_custom_applications_index' );
		FrmProApplicationsController::load_new_application_modal_assets();

		require_once ABSPATH . 'wp-admin/admin-header.php';
		FrmProAppHelper::include_svg(); // SVG needs to be included after the admin header.
		require_once FrmProAppHelper::plugin_path() . '/classes/views/applications/taxonomy/list.php';
		require_once ABSPATH . 'wp-admin/admin-footer.php';
		die();
	}

	/**
	 * Overwrite behaviour of URL when editing Term at /wp-admin/term.php?taxonomy=frm_application&tag_ID=XX for frm_application Taxonomy.
	 *
	 * @param WP_Term $tag
	 * @return void
	 */
	public static function pre_edit_form( $tag ) {
		if ( ! class_exists( 'FrmApplicationsController' ) ) {
			return;
		}

		FrmProApplicationsHelper::custom_application_permission_check();

		FrmAppHelper::include_svg();
		FrmProAppHelper::include_svg();

		wp_enqueue_style( 'formidable-admin' );

		$icons   = array(
			'form'          => 'frm_form_icon',
			'repeater form' => 'frm_file_text_icon',
			'embedded form' => 'frm_file_text_icon',
			'page'          => 'frm_page_icon',
		);
		$icons   = apply_filters( 'frm_application_term_icons', $icons );
		$js_vars = array(
			'id'          => $tag->term_id,
			'name'        => $tag->name,
			'icons'       => $icons,
			'exportNonce' => wp_create_nonce( 'export-xml-nonce' ),
		);
		wp_localize_script( 'formidable_edit_application_term', 'frmApplicationTerm', $js_vars );

		wp_enqueue_script( 'formidable_edit_application_term' );
		wp_enqueue_style( 'formidable_edit_application_term' );

		require_once FrmProAppHelper::plugin_path() . '/classes/views/applications/taxonomy/edit.php';
		require_once ABSPATH . 'wp-admin/admin-footer.php';
		die();
	}

	/**
	 * @return void
	 */
	public static function get_data_for_application() {
		FrmProApplicationsHelper::custom_application_permission_check();

		$id = FrmAppHelper::simple_get( 'id', 'absint' );
		if ( ! $id ) {
			wp_die();
		}

		$rows = self::get_table_data_for_application( $id );
		$data = compact( 'rows' );
		wp_send_json_success( $data );
	}

	/**
	 * @param int $id
	 * @return array
	 */
	private static function get_table_data_for_application( $id ) {
		$children = self::get_children_for_application( $id );
		return array_reduce( $children, array( __CLASS__, 'reduce_item' ) );
	}

	/**
	 * @param array $total
	 * @param WP_Post|stdClass $current
	 * @return array
	 */
	private static function reduce_item( $total, $current ) {
		if ( $current instanceof WP_Post ) {
			$name             = $current->post_title;
			$post_type_object = get_post_type_object( $current->post_type );
			$type_label       = $post_type_object->labels->singular_name;
			$type             = $current->post_type;
			$descriptive_type = $type;

			if ( 'frm_display' === $current->post_type ) {
				$type             = 'view';
				$descriptive_type = $type;

				if ( is_callable( 'FrmViewsDisplaysHelper::get_view_type' ) && is_callable( 'FrmViewsDisplaysHelper::get_view_type_label' ) ) {
					$view_type        = FrmViewsDisplaysHelper::get_view_type( $current );
					$type_label       = FrmViewsDisplaysHelper::get_view_type_label( $view_type ) . ' ' . $type_label;
					$descriptive_type = $view_type . ' ' . $descriptive_type;
				}
			}

			$object_id  = $current->ID;
			$object_key = $current->post_name;
			$is_draft   = 'draft' === $current->post_status;
		} elseif ( $current instanceof stdClass ) {
			$name = $current->name;
			$type = 'form';

			if ( ! empty( $current->parent_form_id ) ) {
				$name            .= ' ' . __( '(child)', 'formidable-pro' );
				$type_label       = __( 'Repeater Form', 'formidable-pro' );
				$descriptive_type = 'repeater ' . $type;
			} elseif ( self::$helper->form_is_an_embed_field_form( $current->id ) ) {
				$type_label       = __( 'Embedded Form', 'formidable-pro' );
				$descriptive_type = 'embedded ' . $type;
			} else {
				$type_label       = __( 'Form', 'formidable-pro' );
				$descriptive_type = $type;
			}

			$object_id  = (int) $current->id;
			$object_key = $current->form_key;
			$is_draft   = false;
		} else {
			return $total;
		}

		$edit_url = self::get_edit_url( $type, $object_id );
		$total[]  = array(
			'itemId'          => $object_id,
			'itemKey'         => $object_key,
			'name'            => $name,
			'type'            => $type,
			'descriptiveType' => $descriptive_type,
			'typeLabel'       => $type_label,
			'editUrl'         => $edit_url,
			'parentOf'        => self::get_parent_of_data( $current ),
			'embeddedIn'      => self::get_embedded_in_data( $current ),
			'isDraft'         => $is_draft,
		);
		return $total;
	}

	/**
	 * @param int $id
	 * @return array<WP_Post>|array<stdClass>
	 */
	private static function get_children_for_application( $id ) {
		$forms = FrmProApplication::get_forms_for_application( $id );
		$posts = FrmProApplication::get_posts_for_application( $id );
		self::maybe_sync_counts( $id, count( $forms ), $posts );
		self::$helper = new FrmProApplicationRelationHelper( $forms, $posts );
		return array_merge( $forms, $posts );
	}

	/**
	 * Maybe update form/view/page counts on application.
	 * They may go out of sync if a form or post gets moved to trash.
	 *
	 * @param int            $id Application id.
	 * @param int            $form_count
	 * @param array<WP_Post> $posts
	 * @return void
	 */
	private static function maybe_sync_counts( $id, $form_count, $posts ) {
		if ( $form_count !== self::get_count( $id, 'form' ) ) {
			FrmProApplication::update_form_count( $id );
		}

		$post_counts = array(
			'frm_display' => 0,
			'page'        => 0,
		);
		foreach ( $posts as $post ) {
			if ( isset( $post_counts[ $post->post_type ] ) ) {
				++$post_counts[ $post->post_type ];
			}
		}

		if ( $post_counts['frm_display'] !== self::get_count( $id, 'view' ) ) {
			FrmProApplication::update_view_count( $id );
		}

		if ( $post_counts['page'] !== self::get_count( $id, 'page' ) ) {
			FrmProApplication::update_page_count( $id );
		}
	}

	/**
	 * @param int    $id Application id.
	 * @param string $type supports 'view', 'page', and 'form'.
	 * @return int
	 */
	private static function get_count( $id, $type ) {
		$count = get_term_meta( $id, '_frm_' . $type . '_count', true );
		return is_numeric( $count ) ? absint( $count ) : 0;
	}

	/**
	 * @param WP_Post|stdClass $item
	 * @return array
	 */
	private static function get_parent_of_data( $item ) {
		return self::$helper->get_parent_of_data( $item );
	}

	/**
	 * @param WP_Post|stdClass $item
	 * @return array
	 */
	private static function get_embedded_in_data( $item ) {
		return self::$helper->get_embedded_in_data( $item );
	}

	/**
	 * Create empty application via AJAX action.
	 *
	 * @return void
	 */
	public static function create_application() {
		FrmProApplicationsHelper::custom_application_permission_check();
		check_ajax_referer( 'frm_ajax', 'nonce' );

		$name = FrmAppHelper::get_post_param( 'application_name', '', 'sanitize_text_field' );
		$term = FrmProApplication::create( $name );

		if ( ! is_array( $term ) ) {
			wp_send_json_error( __( 'Unable to create application', 'formidable-pro' ) );
			die();
		}

		$term_id  = $term['term_id'];
		$redirect = FrmProApplicationsHelper::get_edit_url( $term_id );
		$data     = compact( 'term_id', 'redirect' );
		wp_send_json_success( $data );
		die();
	}

	/**
	 * Delete application via AJAX action.
	 *
	 * @return void
	 */
	public static function delete_application() {
		FrmProApplicationsHelper::custom_application_permission_check();
		check_ajax_referer( 'frm_ajax', 'nonce' );

		$term_id = FrmAppHelper::get_post_param( 'term_id', '', 'absint' );
		if ( ! $term_id ) {
			die();
		}

		$term = get_term( $term_id, 'frm_application' );
		if ( ! ( $term instanceof WP_Term ) ) {
			wp_send_json_error( 'Application does not exist' );
			die();
		}

		wp_delete_term( $term_id, 'frm_application' );

		$data = array();
		wp_send_json_success( $data );
		die();
	}

	/**
	 * Add item to application via AJAX action.
	 *
	 * @return void
	 */
	public static function add_to_application() {
		FrmProApplicationsHelper::custom_application_permission_check();
		check_ajax_referer( 'frm_ajax', 'nonce' );

		$term_id = FrmAppHelper::get_post_param( 'term_id', 0, 'absint' );
		if ( ! $term_id ) {
			die();
		}

		$type = FrmAppHelper::get_post_param( 'type', '', 'sanitize_text_field' );
		if ( ! $type || ! in_array( $type, array( 'form', 'page', 'view' ), true ) ) {
			die();
		}

		$new = FrmAppHelper::get_post_param( 'new', 0, 'absint' );
		if ( 1 === $new ) {
			$redirect = self::get_new_item_redirect( $type, $term_id );
			if ( '' === $redirect ) {
				wp_send_json_error( 'Page name may be missing, or type is incorrect' );
			}
		} else {
			$item_id = FrmAppHelper::get_post_param( 'item_id', 0, 'absint' );
			if ( ! $item_id ) {
				die();
			}

			self::add_existing_item_to_application( $type, $term_id, $item_id );

			$data = array();
			wp_send_json_success( $data );
			die();
		}

		if ( empty( $redirect ) ) {
			die();
		}

		$data = compact( 'redirect' );
		wp_send_json_success( $data );
		die();
	}

	/**
	 * Maybe create an item (form, page, or view), or get the URL to the appropriate redirect.
	 *
	 * @param string $type supports 'form', 'page' and 'view'.
	 * @param int    $term_id
	 * @return string redirect url to edit item.
	 */
	private static function get_new_item_redirect( $type, $term_id ) {
		switch ( $type ) {
			case 'form':
				return admin_url( 'admin.php?page=formidable&triggerNewFormModal=1&applicationId=' . $term_id );
			case 'view':
				return admin_url( 'edit.php?post_type=frm_display&triggerNewViewModal=1&applicationId=' . $term_id );
			case 'page':
				$name = FrmAppHelper::get_post_param( 'name', '', 'sanitize_text_field' );
				if ( ! $name ) {
					return '';
				}

				$post_id = wp_insert_post(
					array(
						'post_type'   => 'page',
						'post_title'  => $name,
						'post_status' => 'private',
					)
				);
				FrmProApplication::add_post_to_application( $term_id, $post_id, $type );
				$object_id = $post_id;
				return self::get_edit_url( $type, $object_id );
			default:
				return '';
		}
	}

	/**
	 * @param string $type supports 'form', 'page' and 'view'.
	 * @param int    $term_id
	 * @param int    $item_id
	 * @return void
	 */
	private static function add_existing_item_to_application( $type, $term_id, $item_id ) {
		switch ( $type ) {
			case 'form':
				self::add_missing_view_ids_to_application( $term_id, $item_id );
				FrmProApplication::add_form_to_application( $term_id, $item_id );
				break;
			case 'view':
				$form_id = get_post_meta( $item_id, 'frm_form_id', true );
				if ( $form_id && is_numeric( $form_id ) ) {
					self::add_missing_form_id_to_application( $term_id, (int) $form_id );
				}
				// Fall through to page case, we want to add post to application.
			case 'page':
				FrmProApplication::add_post_to_application( $term_id, $item_id, $type );
				break;
		}
	}

	/**
	 * @param int $term_id
	 * @param int $form_id
	 * @return void
	 */
	private static function add_missing_view_ids_to_application( $term_id, $form_id ) {
		global $wpdb;
		$where        = array(
			'meta_key'   => 'frm_form_id',
			'meta_value' => $form_id,
		);
		$view_ids     = FrmDb::get_col( $wpdb->postmeta, $where, 'post_id' );
		$new_view_ids = array_diff(
			array_map( 'intval', $view_ids ),
			FrmProApplication::get_posts_for_application( $term_id, array( 'frm_display' ), array( 'fields' => 'ids' ) )
		);
		foreach ( $new_view_ids as $view_id ) {
			FrmProApplication::add_post_to_application( $term_id, $view_id, 'view' );
		}
	}

	/**
	 * @param int $term_id
	 * @param int $form_id
	 * @return void
	 */
	private static function add_missing_form_id_to_application( $term_id, $form_id ) {
		global $wpdb;
		$where       = array(
			'meta_key'   => '_frm_form_id',
			'meta_value' => $form_id,
			'term_id'    => $term_id,
		);
		if ( FrmDb::get_var( $wpdb->termmeta, $where, 'term_id' ) ) {
			return;
		}

		FrmProApplication::add_form_to_application( $term_id, $form_id );
	}

	/**
	 * @param string $type
	 * @param int    $object_id
	 * @return string
	 */
	private static function get_edit_url( $type, $object_id ) {
		switch ( $type ) {
			case 'form':
				return admin_url( 'admin.php?page=formidable&frm_action=edit&id=' . $object_id );
			default:
				return admin_url( 'post.php?post=' . $object_id . '&action=edit' );
		}
	}

	/**
	 * Remove item from application via AJAX action.
	 *
	 * @return void
	 */
	public static function remove_from_application() {
		FrmProApplicationsHelper::custom_application_permission_check();
		check_ajax_referer( 'frm_ajax', 'nonce' );

		$term_id = FrmAppHelper::get_post_param( 'term_id', 0, 'absint' );
		if ( ! $term_id ) {
			die();
		}

		$type = FrmAppHelper::get_post_param( 'type', '', 'sanitize_text_field' );
		if ( ! $type || ! in_array( $type, array( 'form', 'page', 'view' ), true ) ) {
			die();
		}

		$item_id = FrmAppHelper::get_post_param( 'item_id', 0, 'absint' );
		if ( ! $item_id ) {
			die();
		}

		switch ( $type ) {
			case 'form':
				FrmProApplication::remove_form_from_application( $term_id, $item_id );
				break;
			case 'view':
			case 'page':
				FrmProApplication::remove_post_from_application( $term_id, $item_id, $type );
				break;
		}

		$data = array();
		wp_send_json_success( $data );
		die();
	}

	/**
	 * Save application settings via AJAX action (Rename action on My Application page).
	 *
	 * @return void
	 */
	public static function save_application_settings() {
		FrmProApplicationsHelper::custom_application_permission_check();
		check_ajax_referer( 'frm_ajax', 'nonce' );

		$application_id = FrmAppHelper::get_post_param( 'term_id', 0, 'absint' );
		if ( ! $application_id ) {
			die();
		}

		$name = FrmAppHelper::get_post_param( 'name', '', 'sanitize_text_field' );
		if ( ! $name ) {
			die();
		}

		$args = compact( 'name' );
		wp_update_term( $application_id, 'frm_application', $args );
		FrmProApplication::update_timestamp( $application_id );

		$data = array();
		wp_send_json_success( $data );
		die();
	}

	/**
	 * Validate application name via AJAX action.
	 *
	 * @return void
	 */
	public static function validate_application_name() {
		FrmProApplicationsHelper::custom_application_permission_check();
		check_ajax_referer( 'frm_ajax', 'nonce' );

		$name = FrmAppHelper::get_post_param( 'name', '', 'sanitize_text_field' );
		if ( ! $name ) {
			die();
		}

		// Term id may be 0 if this is a new application (via the new Application Modal, or when installing a template).
		$application_id = FrmAppHelper::get_post_param( 'application_id', 0, 'absint' );

		$valid = ! FrmProApplication::name_is_taken( $name, $application_id );
		$data  = array( 'valid' => $valid );

		if ( $valid ) {
			wp_send_json_success( $data );
		} else {
			wp_send_json_error( $data );
		}

		die();
	}

	/**
	 * Sync via AJAX action.
	 *
	 * @return void
	 */
	public static function sync() {
		FrmProApplicationsHelper::custom_application_permission_check();
		check_ajax_referer( 'frm_ajax', 'nonce' );

		$application_id = FrmAppHelper::get_post_param( 'application_id', 0, 'absint' );
		if ( ! $application_id ) {
			wp_send_json_error( 'Missing required application id param' );
			die();
		}

		$summary = FrmProApplicationsHelper::sync( $application_id );
		$data    = compact( 'summary' );

		wp_send_json_success( $data );
		die();
	}

	/**
	 * Search via AJAX action. Used for adding forms with applications.
	 *
	 * @return void
	 */
	public static function search() {
		FrmProApplicationsHelper::custom_application_permission_check();

		check_ajax_referer( 'frm_ajax', 'nonce' );

		global $wpdb;
		$name  = FrmAppHelper::get_param( 'term', '', 'get', 'sanitize_text_field' );
		$terms = get_terms(
			array(
				'taxonomy'   => 'frm_application',
				'hide_empty' => false,
				'orderby'    => 'name',
				'number'     => 25,
				'name__like' => $name,
			)
		);

		$results = array();
		foreach ( $terms as $term ) {
			$results[] = array(
				'value' => $term->term_id,
				'label' => $term->name,
			);
		}

		wp_send_json( $results );
	}

	/**
	 * Add hook before creating a page with form or view shortcode.
	 *
	 * @return void
	 */
	public static function before_create_page_with_shortcode() {
		add_action( 'wp_insert_post', array( __CLASS__, 'after_create_page_with_shortcode' ), 10, 3 );
	}

	/**
	 * Add application relation after page is created with shortcode.
	 *
	 * @param int     $post_ID
	 * @param WP_Post $post
	 * @param bool    $update
	 * @return void
	 */
	public static function after_create_page_with_shortcode( $post_ID, $post, $update ) {
		if ( $update || 'page' !== $post->post_type ) {
			return;
		}
		$application_id = FrmAppHelper::get_post_param( 'application_id', 0, 'absint' );
		if ( ! $application_id ) {
			return;
		}
		FrmProApplication::add_post_to_application( $application_id, $post_ID, 'page' );
	}
}
