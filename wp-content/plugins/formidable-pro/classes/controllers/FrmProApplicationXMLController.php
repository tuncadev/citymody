<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

/**
 * @since 5.3
 */
class FrmProApplicationXMLController {

	/**
	 * Add support for "application_xml" XML format that exports a whole Application based off of an application_id value.
	 *
	 * @return void
	 */
	public static function export_xml() {
		$application_id = FrmAppHelper::get_post_param( 'application_id', 0, 'absint' );
		if ( ! $application_id ) {
			return;
		}

		$application = get_term( $application_id, 'frm_application' );
		if ( ! ( $application instanceof WP_Term ) ) {
			wp_die( 0 );
		}

		global $frm_inc_tax;
		if ( empty( $frm_inc_tax ) ) {
			$frm_inc_tax = array();
		}

		$frm_inc_tax[] = $application_id; // Do not export application taxonomy with each view.

		add_filter( 'wp_get_object_terms', array( __CLASS__, 'remove_applications_from_object_terms_results' ) );

		add_action(
			'frm_xml_export_before_types_loop',
			function() use ( $application ) {
				$name = $application->name;
				// Include application tag at beginning of XML.
				include FrmProAppHelper::plugin_path() . '/classes/views/applications/xml/applications_xml.php';
			}
		);

		$post_ids = FrmProApplication::get_posts_for_application( $application->term_id, array( 'page', 'frm_display' ), array( 'fields' => 'ids' ) );
		if ( $post_ids ) {
			// Include application pages at end of exported application XML
			add_action(
				'frm_xml_export_after_types_loop',
				function() use ( $post_ids ) {
					// posts_xml.php references $wpdb->posts and needs to be set in this scope.
					global $wpdb;

					$item_ids = $post_ids;
					include FrmAppHelper::plugin_path() . '/classes/views/xml/posts_xml.php';
				}
			);
		}

		$form_ids = FrmProApplication::get_forms_for_application( $application_id, true );
		if ( ! $form_ids ) {
			// Make sure there is at least a form id set to avoid all form ids getting included instead.
			$form_ids[] = -1;
		}

		add_filter(
			'frm_xml_filename',
			function() use ( $application ) {
				return sanitize_title_with_dashes( $application->name ) . '.xml';
			}
		);

		$args = array( 'ids' => $form_ids );
		FrmXMLController::generate_xml( array( 'forms', 'items' ), $args );
	}

	/**
	 * Remove applications from object terms results when wp_get_object_terms is called.
	 * This is necessary in order to exclude redundant <category> tags for frm_application taxonomies on export.
	 *
	 * @param array<WP_Term> $terms
	 * @return array<WP_Term>
	 */
	public static function remove_applications_from_object_terms_results( $terms ) {
		return array_filter(
			$terms,
			function( $term ) {
				return 'frm_application' !== $term->taxonomy;
			}
		);
	}

	/**
	 * Add import support for applications that triggers after an XML is imported.
	 * This function also extends the import summary.
	 *
	 * @param array            $imported
	 * @param SimpleXMLElement $xml
	 * @return array
	 */
	public static function importing_xml( $imported, $xml ) {
		if ( ! isset( $xml->application ) || self::is_creating_application_from_template() ) {
			return $imported;
		}

		$name = self::guarantee_unique_name( (string) $xml->application->name );
		$term = FrmProApplication::create( $name );

		if ( ! is_array( $term ) || empty( $term['term_id'] ) ) {
			$imported['error'] = __( 'There was an error creating the application.', 'formidable-pro' );
			return $imported;
		}

		$use_application_id = $term['term_id'];

		foreach ( $imported['forms'] as $form_id ) {
			FrmProApplication::add_form_to_application( $use_application_id, $form_id );
		}

		foreach ( $imported['posts'] as $post_id ) {
			FrmProApplication::add_post_to_application( $use_application_id, $post_id );
		}

		$imported['imported']['applications'] = 1;
		$imported['applications']             = array( $use_application_id );
		return $imported;
	}

	/**
	 * An imported application template that was exported as an application has an <application> tag it will pass the first "isset( $xml->application )" check.
	 * Fixes issue #3905.
	 *
	 * @since 5.5.3
	 *
	 * @return bool
	 */
	private static function is_creating_application_from_template() {
		$application_name = FrmAppHelper::get_post_param( 'application_name', '', 'sanitize_text_field' );
		return '' !== $application_name;
	}

	/**
	 * @param string $name
	 * @return string
	 */
	private static function guarantee_unique_name( $name ) {
		$use_name = $name;
		$count    = 2;

		while ( FrmProApplication::name_is_taken( $use_name, 0 ) ) {
			$use_name = $name . ' ' . $count;
			++$count;
		}

		return $use_name;
	}

	/**
	 * @param string $message
	 * @param array  $result
	 * @return string
	 */
	public static function xml_parsed_message( $message, $result ) {
		if ( empty( $result['imported']['applications'] ) || 1 !== $result['imported']['applications'] ) {
			return $message;
		}

		$application_id = reset( $result['applications'] );
		$message       .= '<li><a href="' . esc_url( FrmProApplicationsHelper::get_edit_url( $application_id ) ) . '">' . esc_html__( 'Go to imported application', 'formidable-pro' ) . '</a></li>';

		return $message;
	}

	/**
	 * Define string for imported applications count message.
	 *
	 * @param string $string
	 * @param int    $m
	 * @return string
	 */
	public static function applications_count_message( $string, $m ) {
		/* translators: %1$s: Number of applications */
		return sprintf( _n( '%1$s Application', '%1$s Applications', $m, 'formidable-pro' ), $m );
	}
}
