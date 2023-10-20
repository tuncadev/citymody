<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

class FrmProFileImport {

	/**
	 * @param mixed    $val
	 * @param stdClass $field
	 * @return mixed
	 */
	public static function import_attachment( $val, $field ) {
		if ( $field->type !== 'file' || is_numeric( $val ) || ! $val ) {
			return $val;
		}

		if ( ! self::should_import_files() ) {
			return $val;
		}

		// Set up global vars to track uploaded files
		self::setup_global_media_import_vars( $field );

		// set the form id for the upload path
		$_POST['form_id'] = $field->form_id;

		global $wpdb, $frm_vars;

		$vals = self::convert_to_array( $val );

		$new_val = array();
		foreach ( (array) $vals as $v ) {
			$v = trim( $v );

			//check to see if the attachment already exists on this site
			$exists = $wpdb->get_var( $wpdb->prepare( 'SELECT ID FROM ' . $wpdb->posts . ' WHERE guid = %s', $v ) );
			if ( $exists ) {
				$new_val[] = $exists;
			} else {
				// Get media ID for newly uploaded image
				$mid = self::curl_file( $v, $field );
				$new_val[] = $mid;
				if ( is_numeric( $mid ) ) {
					// Add newly uploaded images to the global media IDs for this field.
					$frm_vars['media_id'][ $field->id ][] = $mid;
				}
			}
			unset( $v );
		}

		$val = self::convert_to_string( $new_val );

		return $val;
	}

	/**
	 * @since 5.4.4
	 *
	 * @return bool
	 */
	private static function should_import_files() {
		$should_import_files = (bool) FrmAppHelper::get_param( 'csv_files', '', 'REQUEST', 'absint' );

		/**
		 * @since 5.4.4
		 *
		 * @param bool $should_import_files
		 */
		return apply_filters( 'frm_should_import_files', $should_import_files );
	}

	/**
	 * Return true when this filter is set. frm_should_import_files is false by default and can be temporarily toggled on with this filter.
	 * To revert this filter after use make sure to also use remove_filter( 'frm_should_import_files', 'FrmProFileImport::allow_file_import' );
	 *
	 * @since 5.4.4
	 *
	 * @return true
	 */
	public static function allow_file_import() {
		return true;
	}

	/**
	 * Set up global media_id vars. This will be used for post fields.
	 */
	private static function setup_global_media_import_vars( $field ) {
		global $frm_vars;

		// If it hasn't been set yet, set it now
		if ( ! isset( $frm_vars['media_id'] ) ) {
			$frm_vars['media_id'] = array();
		}

		// Clear out old values
		$frm_vars['media_id'][ $field->id ] = array();
	}

	private static function convert_to_array( $val ) {
		if ( is_array( $val ) ) {
			$vals = $val;
		} else {
			$vals = str_replace( '<br/>', ',', $val );
			$vals = explode( ',', $vals );
		}
		return $vals;
	}

	private static function convert_to_string( $val ) {
		if ( count( $val ) == 1 ) {
			$val = reset( $val );
		} else {
			$val = implode( ',', $val );
		}
		return $val;
	}

	/**
	 * Import a file from a target URL.
	 *
	 * @param string   $url   The URL we're downloading a file from.
	 * @param stdClass $field The target field for the imported file.
	 * @return string|int     An integer Post ID is returned when a new attachment is created. Otherwise a string URL is returned.
	 */
	private static function curl_file( $url, $field ) {
		if ( 'file' !== $field->type || ! self::validate_file_url( $url, $field ) ) {
			return $url;
		}

		$ch       = curl_init( str_replace( array( ' ' ), array( '%20' ), $url ) );
		$uploads  = self::get_upload_dir();
		$filename = wp_unique_filename( $uploads['path'], basename( $url ) );
		$path     = trailingslashit( $uploads['path'] );

		$fp = fopen( $path . $filename, 'wb' );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_FILE, $fp );
		curl_setopt( $ch, CURLOPT_HEADER, 0 );
		$user_agent = apply_filters( 'http_headers_useragent', 'WordPress/' . get_bloginfo( 'version' ) . '; ' . get_bloginfo( 'url' ) );
		curl_setopt( $ch, CURLOPT_USERAGENT, $user_agent );
		curl_setopt( $ch, CURLOPT_REFERER, FrmAppHelper::site_url() );
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		$result = curl_exec( $ch );
		curl_close( $ch );
		fclose( $fp );

		if ( $result ) {
			$url = self::attach_existing_image( $filename );
		} else {
			// Remove the file if it fails to attach.
			unlink( $path . $filename );
		}

		return $url;
	}

	/**
	 * Check that a target file URL is valid before trying to download it.
	 * This is done by checking against the allow mime type extensions for the target file field we're uploading for.
	 *
	 * @since 5.5.6
	 *
	 * @param string   $url
	 * @param stdClass $field
	 * @return bool
	 */
	private static function validate_file_url( $url, $field ) {
		$parsed = parse_url( $url );
		if ( ! is_array( $parsed ) ) {
			// URL is malformed.
			return false;
		}

		$path = $parsed['path'];
		$ext  = strtolower( pathinfo( $path, PATHINFO_EXTENSION ) );

		$allowed_mimes = FrmProFileField::get_allowed_mimes( $field );
		if ( is_null( $allowed_mimes ) ) {
			// File type is not restricted so allow what WordPress allows.
			$allowed_mimes = get_allowed_mime_types();
		}

		$allowed_extensions = array_reduce(
			array_keys( $allowed_mimes ),
			function( $total, $current ) {
				// Explode on | because some mime types use keys like jpg|jpeg|jpe.
				$total = array_merge( $total, explode( '|', $current ) );
				return $total;
			},
			array()
		);

		return in_array( $ext, $allowed_extensions, true );
	}

	/**
	 * Get the upload directory for the current form
	 *
	 * @since 3.04.03
	 */
	private static function get_upload_dir() {
		add_filter( 'upload_dir', array( 'FrmProFileField', 'upload_dir' ) );
		$uploads = wp_upload_dir();
		remove_filter( 'upload_dir', array( 'FrmProFileField', 'upload_dir' ) );
		return $uploads;
	}

	private static function attach_existing_image( $filename ) {
		$attachment = array();
		self::prepare_attachment( $filename, $attachment );

		$uploads = self::get_upload_dir();
		$file = $uploads['path'] . '/' . $filename;

		$id = wp_insert_attachment( $attachment, $file );

		if ( ! function_exists('wp_generate_attachment_metadata') ) {
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
		}

		wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $file ) );

		return $id;
	}

	/**
	 * Construct the attachment array
	 */
	private static function prepare_attachment( $filename, &$attachment ) {
		$uploads = self::get_upload_dir();
		$attachment = array(
			'guid'           => $uploads['url'] . '/' . $filename,
			'post_content'   => '',
		);

		$file = $uploads['path'] . '/' . $filename;

		self::get_mime_type( $file, $attachment );
		self::get_attachment_name( $file, $attachment );
	}

	private static function get_mime_type( $file, &$attachment ) {
		if ( function_exists('finfo_file') ) {
			$finfo = finfo_open( FILEINFO_MIME_TYPE ); // return mime type ala mimetype extension
			$type = finfo_file( $finfo, $file );
			finfo_close( $finfo );
			unset( $finfo );
		} else {
			$type = mime_content_type( $file );
		}
		$attachment['post_mime_type'] = $type;
	}

	private static function get_attachment_name( $file, &$attachment ) {
		$name_parts = pathinfo( $file );
		$name = trim( substr( $name_parts['basename'], 0, - ( 1 + strlen( $name_parts['extension'] ) ) ) );
		$attachment['post_title'] = $name;
	}
}
