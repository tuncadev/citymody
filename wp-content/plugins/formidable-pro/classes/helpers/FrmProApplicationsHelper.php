<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

/**
 * @since 5.3
 */
class FrmProApplicationsHelper {

	/**
	 * @param int $application_id
	 * @return string
	 */
	public static function get_edit_url( $application_id ) {
		return admin_url( 'term.php?taxonomy=frm_application&tag_ID=' . $application_id );
	}

	/**
	 * @return bool
	 */
	public static function views_is_active_and_supports_applications() {
		return class_exists( 'FrmViewsApplicationsController' );
	}

	/**
	 * Search application items for connected items (whether embedded or used as a data source) and add them to the application as well.
	 *
	 * @param int $application_id
	 * @return array<array> summary of each page added, indexed by type and id with shortcode match details.
	 */
	public static function sync( $application_id ) {
		$page_data_summary  = self::search_pages_for_forms_and_views( $application_id );
		$field_data_summary = self::search_form_fields_for_source_forms( $application_id );

		$summary = array();
		$types   = array( 'form', 'view', 'page' );
		foreach ( $types as $type ) {
			$summary[ $type ]  = isset( $page_data_summary[ $type ] ) ? $page_data_summary[ $type ] : array();
			$summary[ $type ] += isset( $field_data_summary[ $type ] ) ? $field_data_summary[ $type ] : array();
		}

		return $summary;
	}

	/**
	 * Scan pages for embedded forms and views that match application.
	 *
	 * @param int $application_id
	 * @return array<array> summary of each page added, indexed by type and id with shortcode match details.
	 */
	private static function search_pages_for_forms_and_views( $application_id ) {
		$where     = array(
			'post_type'   => 'page',
			'post_status' => array( 'publish', 'private', 'draft' ),
		);
		$fields    = 'ID, post_title, post_content';
		$page_data = FrmDb::get_results( 'posts', $where, $fields );

		$page_ids          = FrmProApplication::get_posts_for_application( $application_id, array( 'page' ), array( 'fields' => 'ids' ) );
		$shortcode_matches = array();

		foreach ( $page_data as $page ) {
			$page_id = absint( $page->ID );
			$string  = $page->post_content;
			$result  = self::get_shortcode_matches_from_string( $string, array( __CLASS__, 'handle_shortcode' ) );

			if ( ! is_array( $result ) ) {
				continue;
			}

			$page_name = $page->post_title;
			foreach ( $result as $shortcode_match ) {
				$shortcode_matches[] = array_merge(
					$shortcode_match,
					array(
						'pageId'   => $page_id,
						'pageName' => $page_name,
					)
				);
			}
		}

		if ( ! $shortcode_matches ) {
			return array();
		}

		$form_ids = FrmProApplication::get_forms_for_application( $application_id, true );
		$view_ids = FrmProApplication::get_posts_for_application( $application_id, array( 'frm_display' ), array( 'fields' => 'ids' ) );

		$summary_by_form_id = array();
		$summary_by_view_id = array();
		$summary_by_page_id = array();

		foreach ( $shortcode_matches as $shortcode_match ) {
			$type      = $shortcode_match['type'];
			$page_id   = $shortcode_match['pageId'];
			$page_name = $shortcode_match['pageName'];

			unset( $shortcode_match['pageId'], $shortcode_match['pageName'] );
			$object_id = $shortcode_match['objectId'];

			$page_was_assigned_to_application = in_array( $page_id, $page_ids, true );

			if ( 'form' === $type ) {
				if ( ! is_int( $object_id ) ) {
					$object_id = FrmForm::get_id_by_key( $object_id );
					if ( ! $object_id ) {
						continue;
					}
				}

				if ( ! in_array( $object_id, $form_ids, true ) ) {
					if ( ! $page_was_assigned_to_application ) {
						continue;
					}

					$where     = array( 'id' => $object_id );
					$form_name = FrmDb::get_var( 'frm_forms', $where, 'name' );
					if ( ! $form_name ) {
						continue;
					}

					$shortcode_match['description'] = sprintf(
						/* translators: %s: name of page. */
						__( 'Form found in page "%s".', 'formidable-pro' ),
						$page_name
					);
					$summary_by_form_id[ $object_id ] = array(
						'name'    => $form_name,
						'matches' => array( $shortcode_match ),
					);
					FrmProApplication::add_form_to_application( $application_id, $object_id );
					continue;
				}

				/* translators: %s: name of form. */
				$shortcode_match['description'] = __( 'Page includes shortcode for form "%s".', 'formidable-pro' );
			} elseif ( 'view' === $type ) {
				if ( ! is_int( $object_id ) ) {
					if ( ! class_exists( 'FrmViewsDisplay' ) ) {
						continue;
					}

					$object_id = FrmViewsDisplay::get_id_by_key( $object_id );
					if ( ! $object_id ) {
						continue;
					}
				}

				if ( ! in_array( $object_id, $view_ids, true ) ) {
					if ( ! $page_was_assigned_to_application ) {
						continue;
					}

					global $wpdb;
					$where      = array( 'ID' => $object_id );
					$view_title = FrmDb::get_var( $wpdb->posts, $where, 'post_title' );
					if ( ! $view_title ) {
						continue;
					}

					$shortcode_match['description'] = sprintf(
						/* translators: %s: name of page. */
						__( 'View found in page "%s".', 'formidable-pro' ),
						$page_name
					);

					$summary_by_view_id[ $object_id ] = array(
						'name'    => $view_title,
						'matches' => array( $shortcode_match ),
					);
					FrmProApplication::add_post_to_application( $application_id, $object_id, 'view' );
					continue;
				}

				/* translators: %s: name of view. */
				$shortcode_match['description'] = __( 'Page includes shortcode for view "%s".', 'formidable-pro' );
			} else {
				continue;
			}

			if ( $page_was_assigned_to_application ) {
				continue;
			}

			if ( ! array_key_exists( $page_id, $summary_by_page_id ) ) {
				$summary_by_page_id[ $page_id ] = array(
					'name'    => $page_name,
					'matches' => array(),
				);
				FrmProApplication::add_post_to_application( $application_id, $page_id, 'page' );
			}

			$summary_by_page_id[ $page_id ]['matches'][] = $shortcode_match;
		}

		return array(
			'form' => $summary_by_form_id,
			'view' => $summary_by_view_id,
			'page' => $summary_by_page_id,
		);
	}

	/**
	 * @param int $application_id
	 * @return array<array> summary of each page added, indexed by page id with shortcode match details.
	 */
	private static function search_form_fields_for_source_forms( $application_id ) {
		$form_ids = FrmProApplication::get_forms_for_application( $application_id, true );
		if ( ! $form_ids ) {
			return array();
		}

		$fields = FrmField::getAll(
			array(
				'form_id' => $form_ids,
				'type'    => array( 'data', 'lookup', 'form' ),
			)
		);
		if ( ! $fields ) {
			return array();
		}

		$summary_by_form_id = array();
		foreach ( $fields as $field ) {
			switch ( $field->type ) {
				case 'lookup':
					$form_id = absint( $field->field_options['get_values_form'] );
					break;
				case 'data':
					$source_field_id = $field->field_options['form_select'];
					$form_id         = absint( FrmDb::get_var( 'frm_fields', array( 'id' => $source_field_id ), 'form_id' ) );
					break;
				case 'form':
					$form_id = absint( $field->field_options['form_select'] );
					break;
			}

			if ( in_array( $form_id, $form_ids, true ) ) {
				// Form already added.
				continue;
			}

			if ( ! array_key_exists( $form_id, $summary_by_form_id ) ) {
				$form_name = FrmDb::get_var( 'frm_forms', array( 'id' => $form_id ), 'name' );
				if ( ! $form_name ) {
					continue;
				}

				$summary_by_form_id[ $form_id ] = array(
					'name'    => $form_name,
					'matches' => array(),
				);
				FrmProApplication::add_form_to_application( $application_id, $form_id );
			}

			$summary_by_form_id[ $form_id ]['matches'][] = array(
				'type'        => 'form',
				'objectId'    => absint( $field->form_id ),
				/* translators: %s: Name of form with source data. */
				'description' => __( 'Form includes options from "%s".', 'formidable-pro' ),
			);
		}

		unset( $form_id );

		return array( 'form' => $summary_by_form_id );
	}

	/**
	 * @param string   $string
	 * @param callable $callback for handling the shortcode results
	 * @param array    $tags
	 * @return array
	 */
	public static function get_shortcode_matches_from_string( $string, $callback, $tags = array( 'formidable', 'display-frm-data' ) ) {
		$regex = '/' . get_shortcode_regex( $tags ) . '/';

		preg_match_all( $regex, $string, $matches );
		if ( empty( $matches[0] ) ) {
			return array();
		}

		$result = array();
		foreach ( $matches[0] as $index => $match ) {
			$id = self::parse_id_from_options( $matches[3][ $index ] );
			if ( false === $id ) {
				continue;
			}

			$shortcode_found   = $matches[2][ $index ]; // 'formidable', or 'display-frm-data'.
			$callback_response = $callback( $shortcode_found, $id );

			if ( is_array( $callback_response ) ) {
				$result[] = $callback_response;
			}
		}
		return $result;
	}

	/**
	 * @param string $options
	 * @return int|string|false form or view id (or key) found in shortcode options. False when there is no match.
	 */
	private static function parse_id_from_options( $options ) {
		$split = explode( ' ', $options );
		foreach ( $split as $current ) {
			if ( 0 === strpos( $current, 'id=' ) ) {
				$current = str_replace( '"', '', $current );
				$current = str_replace( "'", '', $current );

				$substr = substr( $current, 3 );
				if ( is_numeric( $substr ) ) {
					return intval( $substr );
				}

				return sanitize_key( $substr );
			}
		}
		return false;
	}

	/**
	 * @param string $shortcode_found
	 * @param int    $id_found
	 * @return array|false
	 */
	public static function handle_shortcode( $shortcode_found, $id_found ) {
		switch ( $shortcode_found ) {
			case 'formidable':
				return array( 'type' => 'form', 'objectId' => $id_found );
			case 'display-frm-data':
				return array( 'type' => 'view', 'objectId' => $id_found );
		}
		return false;
	}

	/**
	 * Get all applicatino ids for a single form.
	 *
	 * @param int $form_id
	 * @return array<int>
	 */
	public static function get_application_ids_for_form( $form_id ) {
		global $wpdb;
		return array_map(
			'intval',
			FrmDb::get_col(
				$wpdb->termmeta,
				array(
					'meta_key'   => '_frm_form_id',
					'meta_value' => $form_id,
				),
				'term_id'
			)
		);
	}

	/**
	 * Get tag HTML for applications to use in forms and views tables.
	 *
	 * @since 5.3.1
	 *
	 * @param array<int> $application_ids
	 * @return string
	 */
	public static function get_application_tags_html( $application_ids ) {
		$can_edit_applications = self::current_user_can_edit_applications();
		return array_reduce(
			$application_ids,
			function( $total, $application_id ) use ( $can_edit_applications ) {
				$application = get_term( $application_id, 'frm_application' );

				if ( ! ( $application instanceof WP_Term ) ) {
					return $total;
				}

				$href = FrmProApplicationsHelper::get_edit_url( $application_id );
				if ( $can_edit_applications ) {
					$link = '<a href="' . esc_attr( $href ) . '">' . esc_html( $application->name ) . '</a>';
				} else {
					$link = esc_html( $application->name );
				}

				return $total . '<div class="frm-grey-tag">' . $link . '</div>';
			},
			''
		);
	}

	/**
	 * Check Applications Templates permission and maybe die.
	 *
	 * @since 5.3.1
	 *
	 * @return void
	 */
	public static function templates_permission_check() {
		FrmAppHelper::permission_check( self::get_required_templates_capability() );
	}

	/**
	 * Check Custom Applications permission and maybe die.
	 *
	 * @since 5.3.1
	 *
	 * @return void
	 */
	public static function custom_application_permission_check() {
		FrmAppHelper::permission_check( self::get_custom_applications_capability() );
	}

	/**
	 * @since 5.3.1
	 *
	 * @return string
	 */
	public static function get_required_templates_capability() {
		$cap = 'frm_application_dashboard';
		if ( ! current_user_can( $cap ) && current_user_can( 'administrator' ) ) {
			// Make sure administrator can always access Applications.
			$cap = 'administrator';
		}
		return $cap;
	}

	/**
	 * @since 5.3.1
	 *
	 * @return string
	 */
	public static function get_custom_applications_capability() {
		return 'frm_edit_applications';
	}

	/**
	 * @since 5.3.1
	 *
	 * @return bool
	 */
	public static function current_user_can_edit_applications() {
		return current_user_can( self::get_custom_applications_capability() ) || current_user_can( 'administrator' );
	}
}
