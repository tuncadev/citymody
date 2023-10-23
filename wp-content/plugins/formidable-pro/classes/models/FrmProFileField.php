<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

class FrmProFileField {

	/**
	 * @since 5.4.3
	 */
	const WRITE_ONLY = 0200;

	/**
	 * @since 5.4.3
	 */
	const READ_ONLY = 0400;

	/**
	 * @var array $all_file_upload_field_ids
	 */
	private static $all_file_upload_field_ids;

	/**
	 * @var array $all_file_upload_item_metas
	 */
	private static $all_file_upload_item_metas;

	/**
	 * @var bool $uploading_temporary_files
	 */
	private static $uploading_temporary_files = false;

	/**
	 * @var stdClass $active_upload_field
	 */
	private static $active_upload_field;

	/**
	 * @var array $new_temporary_file_ids
	 */
	private static $new_temporary_file_ids;

	/**
	 * @var bool Used in allow_xlsx_files_from_google_sheets, so the filter only gets added the first time upload_file gets called.
	 */
	private static $added_xlsx_filter = false;

	/**
	 * @var int The maximum number of chars allowed for the 'post_name' field.
	 */
	private static $post_name_limit = 200;

	/**
	 * @param array $field (no array for field options)
	 * @param array $atts
	 */
	public static function setup_dropzone( $field, $atts ) {
		global $frm_vars;

		$is_multiple = FrmField::is_option_true( $field, 'multiple' );

		if ( ! isset( $frm_vars['dropzone_loaded'] ) || ! is_array( $frm_vars['dropzone_loaded'] ) ) {
			$frm_vars['dropzone_loaded'] = array();
		}

		$the_id = $atts['file_name'];
		if ( ! isset( $frm_vars['dropzone_loaded'][ $the_id ] ) ) {
			if ( $is_multiple ) {
				$max = empty( $field['max'] ) ? 99 : absint( $field['max'] );
			} else {
				$max = 1;
			}

			$file_size = self::get_max_file_size( $field['size'] );
			$form_id   = isset( $field['parent_form_id'] ) ? $field['parent_form_id'] : $field['form_id'];

			$frm_vars['dropzone_loaded'][ $the_id ] = array(
				'maxFilesize'      => round( $file_size, 2 ),
				'minFilesize'      => $field['min_size'],
				'maxFiles'         => $max,
				'htmlID'           => $the_id,
				'label'            => $atts['html_id'],
				'uploadMultiple'   => $is_multiple,
				'fieldID'          => $field['id'],
				'formID'           => $field['form_id'],
				'parentFormID'     => $form_id,
				'fieldName'        => $atts['field_name'],
				'mockFiles'        => array(),
				'defaultMessage'   => __( 'Drop files here to upload', 'formidable-pro' ),
				'fallbackMessage'  => __( 'Your browser does not support drag and drop file uploads.', 'formidable-pro' ),
				'fallbackText'     => __( 'Please use the fallback form below to upload your files like in the olden days.', 'formidable-pro' ),
				/* translators: %s: File size limit (Megabytes). */
				'fileTooBig'       => sprintf( __( 'That file is too big. It must be less than %sMB.', 'formidable-pro' ), '{{maxFilesize}}' ),
				/* translators: %s: File minimum size limit (Megabytes). */
				'fileTooSmall'     => sprintf( __( 'That file is too small. It must be greater than %sMB.', 'formidable-pro' ), '{{minFilesize}}' ),
				'invalidFileType'  => self::get_invalid_file_type_message( $field['name'], $field['invalid'] ),
				/* translators: %s: Status code */
				'responseError'    => sprintf( __( 'Server responded with %s code.', 'formidable-pro' ), '{{statusCode}}' ),
				'cancel'           => __( 'Cancel upload', 'formidable-pro' ),
				'cancelConfirm'    => __( 'Are you sure you want to cancel this upload?', 'formidable-pro' ),
				'remove'           => __( 'Remove file', 'formidable-pro' ),
				'maxFilesExceeded' => self::get_max_file_limit_error( $max ),
				'resizeHeight'     => null,
				'resizeWidth'      => null,
				'timeout'          => self::get_timeout(),
			);

			if ( array_key_exists( 'honeypot', $frm_vars ) && array_key_exists( $form_id, $frm_vars['honeypot'] ) ) {
				$frm_vars['dropzone_loaded'][ $the_id ]['checkHoneypot'] = 'strict' === $frm_vars['honeypot'][ $form_id ];
			}

			$file_types = self::get_allowed_mimes( $field );
			if ( ! empty( $file_types ) ) {
				// Expected formats: image/*,application/pdf,.psd
				$frm_vars['dropzone_loaded'][ $the_id ]['acceptedFiles'] = implode( ',', array_unique( $file_types ) );
				foreach ( $file_types as $file_type => $mime ) {
					$file_type = explode( '|', $file_type );
					$frm_vars['dropzone_loaded'][ $the_id ]['acceptedFiles'] .= ',.' . implode( ',.', $file_type );
				}
			}

			if ( $field['resize'] && ! empty( $field['new_size'] ) ) {
				$setting_name = 'resize' . ucfirst( $field['resize_dir'] );
				$frm_vars['dropzone_loaded'][ $the_id ][ $setting_name ] = $field['new_size'];
			}

			if ( strpos( $the_id, '-i' ) ) {
				// we are editing, so get the base settings added too
				$id_parts = explode( '-i', $the_id );
				$base_id = $id_parts[0] . '-0';
				$base_settings = $frm_vars['dropzone_loaded'][ $the_id ];
				if ( ! isset( $frm_vars['dropzone_loaded'][ $base_id ] ) && strpos( $base_settings['fieldName'], '[i' . $id_parts[1] . ']' ) ) {
					$base_settings['htmlID'] = $base_id;
					$base_settings['fieldName'] = str_replace( '[i' . $id_parts[1] . ']', '[0]', $base_settings['fieldName'] );
					$frm_vars['dropzone_loaded'][ $base_id ] = $base_settings;
				}
			}

			self::add_mock_files( $field['value'], $frm_vars['dropzone_loaded'][ $the_id ]['mockFiles'] );
		}
	}

	/**
	 * @since 5.0.09
	 *
	 * @param int $max
	 * @return string
	 */
	private static function get_max_file_limit_error( $max ) {
		/* translators: %d: File limit number */
		return sprintf( __( 'You have uploaded more than %d file(s).', 'formidable-pro' ), $max );
	}

	/**
	 * Increase the default timeout from 30 based on server limits
	 *
	 * @since 3.01.02
	 */
	private static function get_timeout() {
		$timeout = absint( ini_get( 'max_execution_time' ) );
		if ( $timeout <= 1 ) {
			// allow for -1 or 0 for unlimited
			$timeout = 5000 * 1000;
		} elseif ( $timeout > 30 ) {
			$timeout = $timeout * 1000;
		} else {
			$timeout = 30000;
		}
		return $timeout;
	}

	/**
	 * @param array $media_ids
	 * @param array $mock_files
	 * @return void
	 */
	private static function add_mock_files( $media_ids, &$mock_files ) {
		FrmProAppHelper::unserialize_or_decode( $media_ids );
		if ( ! $media_ids ) {
			return;
		}

		foreach ( (array) $media_ids as $media_id ) {
			$file = self::get_mock_file( $media_id );
			if ( $file ) {
				$mock_files[] = $file;
			}
		}
	}

	/**
	 * @param int $media_id
	 * @return array
	 */
	public static function get_mock_file( $media_id ) {
		$file_url  = self::get_file_url( $media_id );
		$path      = get_attached_file( $media_id );
		$file_type = wp_check_filetype( $path );
		$file      = array(
			'name'       => basename( $path ),
			'url'        => self::get_file_url( $media_id, 'thumbnail' ),
			'id'         => $media_id,
			'file_url'   => $file_url,
			'accessible' => self::user_has_permission( $media_id ),
			'ext'        => $file_type['ext'],
			'type'       => $file_type['type'],
		);

		if ( file_exists( $path ) ) {
			$file['size'] = filesize( $path );
		}

		return $file;
	}

	/**
	 * Get path to use for file that checks for permissions first before trying to show files the user cannot access.
	 *
	 * @since 5.4.1
	 *
	 * @param array $file Mock file data.
	 * @return string
	 */
	public static function get_safe_file_icon( $file ) {
		if ( ! empty( $file['accessible'] ) && self::file_type_matches_image( $file['type'] ) ) {
			return $file['url'];
		}

		// Use a placeholder for type instead.
		return self::get_icon_for_file_type( $file['ext'] );
	}

	/**
	 * @param string $extension
	 *
	 * @return string
	 */
	private static function get_icon_for_file_type( $extension ) {
		$images_url = FrmProAppHelper::plugin_url() . '/images/';

		if ( in_array( $extension, array( 'pdf', 'doc', 'xls', 'docx', 'xlsx' ), true ) ) {
			$ext = substr( $extension, 0, 3 );

			return $images_url . $ext . '.svg';
		}

		return $images_url . 'doc.svg';
	}

	/**
	 * Always hide the temp files from queries.
	 * Hide all unattached form uploads from those without permission.
	 *
	 * @param WP_Query $query
	 */
	public static function filter_media_library( $query ) {
		if ( 'attachment' === $query->get( 'post_type' ) ) {
			$meta_query = $query->get( 'meta_query' );
			self::query_to_exclude_files( $meta_query );

			$query->set( 'meta_query', $meta_query );
		}
	}

	private static function query_to_exclude_files( &$meta_query ) {
		if ( current_user_can( 'frm_edit_entries' ) ) {
			$show = FrmAppHelper::get_param( 'frm-attachment-filter', '', 'get', 'absint' );
		} else {
			$show = false;
		}

		if ( ! is_array( $meta_query ) ) {
			$meta_query = array();
		} else {
			$continue = self::nest_attachment_query( $meta_query );
			if ( ! $continue ) {
				return;
			}
		}

		$meta_query[] = array(
			'relation' => 'AND',
			array(
				'key'     => '_frm_temporary',
				'compare' => 'NOT EXISTS',
			),
			array(
				'key'     => '_frm_file',
				'compare' => $show ? 'EXISTS' : 'NOT EXISTS',
			),
		);
	}

	/**
	 * If a query uses OR, adding to it will return unexpected results
	 * Move the OR query into a subquery
	 *
	 * @return boolean true to continue adding the extra query
	 */
	private static function nest_attachment_query( &$meta_query ) {
		if ( ! isset( $meta_query['relation'] ) || 'or' !== strtolower( $meta_query['relation'] ) ) {
			return true;
		}

		$temp_group = array();
		foreach ( $meta_query as $k => $meta ) {
			// if looking for a Formidable file, don't exclude it
			if ( isset( $meta['value'] ) && strpos( $meta['value'], 'formidable' ) !== false ) {
				return false;
			}
			$temp_group[] = $meta;
			unset( $meta_query[ $k ] );
		}
		$meta_query[] = $temp_group;

		return true;
	}

	public static function filter_api_attachments( $args ) {
		add_action( 'pre_get_posts', 'FrmProFileField::filter_media_library', 99 );
		return $args;
	}

	/**
	 * Validate a file upload field if file was not uploaded with Ajax
	 *
	 * @since 2.03.08
	 *
	 * @param array $errors
	 * @param stdClass $field
	 * @param array $values
	 * @param array $args
	 *
	 * @return array
	 */
	public static function no_js_validate( $errors, $field, $values, $args ) {
		$field->temp_id = $args['id'];

		$args['file_name'] = self::get_file_name( $field, $args );

		if ( isset( $_FILES[ $args['file_name'] ] ) ) {
			self::validate_file_upload( $errors, $field, $args, $values );
			self::add_file_fields_to_global_variable( $field, $args );
		}

		return $errors;
	}

	/**
	 * @since 3.0
	 *
	 * @param object $field
	 * @param array $args
	 */
	private static function get_file_name( $field, $args ) {
		$file_name = 'file' . $field->id;
		if ( isset( $args['key_pointer'] ) && ( $args['key_pointer'] || $args['key_pointer'] === 0 ) ) {
			$file_name .= '-' . $args['key_pointer'];
		}
		return $file_name;
	}

	/**
	 * Add file upload field information to global variable
	 *
	 * @since 2.03.08
	 *
	 * @param stdClass $field
	 * @param array $args
	 */
	private static function add_file_fields_to_global_variable( $field, $args ) {
		global $frm_vars;
		if ( ! isset( $frm_vars['file_fields'] ) ) {
			$frm_vars['file_fields'] = array();
		}

		$frm_vars['file_fields'][ $field->temp_id ]               = $args;
		$frm_vars['file_fields'][ $field->temp_id ]['field_id'] = $field->id;
	}

	/**
	 * Upload files when dropzone is disabled (triggered when submitting a form).
	 *
	 * @since 2.03.08
	 *
	 * @param array $errors
	 * @return array
	 */
	public static function upload_files_no_js( $errors ) {
		global $frm_vars;

		if ( ! empty( $errors ) || ! isset( $frm_vars['file_fields'] ) ) {
			return $errors;
		}

		foreach ( $frm_vars['file_fields'] as $unique_file_id => $file_args ) {
			if ( ! isset( $_FILES[ $file_args['file_name'] ] ) ) {
				continue;
			}

			$file_field = FrmField::getOne( $file_args['field_id'] );
			if ( ! self::should_allow_upload_for_field( $file_field ) ) {
				continue;
			}

			$file_field->temp_id = $file_field->id;
			self::maybe_upload_temp_file( $errors, $file_field, $file_args );
		}

		return $errors;
	}

	/**
	 * If blank errors are set, remove them if a file was uploaded in the field.
	 * It still needs some checks in case there are multiple file fields
	 *
	 * @since 3.0.03
	 *
	 * @param array    $errors
	 * @param stdClass $field
	 * @param mixed    $value unused in this function but always passed into the frm_validate_file_field_entry filter.
	 * @param array    $args
	 * @return array
	 */
	public static function remove_error_message( $errors, $field, $value, $args ) {
		if ( ! isset( $errors[ 'field' . $field->temp_id ] ) || $errors[ 'field' . $field->temp_id ] != FrmFieldsHelper::get_error_msg( $field, 'blank' ) ) {
			return $errors;
		}

		$file_name = self::get_file_name( $field, $args );
		if ( ! isset( $_FILES[ $file_name ] ) ) {
			return $errors;
		}

		$file_uploads = $_FILES[ $file_name ]; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( self::file_was_selected( $file_uploads ) ) {
			unset( $errors[ 'field' . $field->temp_id ] );
		}

		return $errors;
	}

	/**
	 * @param array    $errors
	 * @param stdClass $field
	 * @param array    $args
	 * @param array    $values
	 * @return void
	 */
	public static function validate_file_upload( &$errors, $field, $args, $values = array() ) {
		if ( ! isset( $_FILES[ $args['file_name'] ] ) ) {
			return;
		}

		$file_uploads = $_FILES[ $args['file_name'] ]; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		if ( self::file_was_selected( $file_uploads ) ) {

			add_filter( 'frm_validate_file_field_entry', 'FrmProFileField::remove_error_message', 10, 4 );

			self::validate_file_size( $errors, $field, $args );
			self::validate_file_count( $errors, $field, $args, $values );
			self::validate_file_type( $errors, $field, $args );
			self::validate_file_spam( $errors, $field, $args );
			$errors = apply_filters( 'frm_validate_file', $errors, $field, $args );

		} elseif ( empty( $values ) ) {
			$skip_required = FrmProEntryMeta::skip_required_validation( $field );
			if ( $field->required && ! $skip_required ) {
				$errors[ 'field' . $field->temp_id ] = FrmFieldsHelper::get_error_msg( $field, 'blank' );
			}
		}
	}

	/**
	 * @param array $file_uploads
	 * @return bool
	 */
	private static function file_was_selected( $file_uploads ) {
		// if the field is a file upload, check for a file
		if ( empty( $file_uploads['name'] ) ) {
			return false;
		}

		$filled = true;
		if ( is_array( $file_uploads['name'] ) ) {
			$filled = false;
			foreach ( $file_uploads['name'] as $n ) {
				if ( ! empty( $n ) ) {
					$filled = true;
					break;
				}
			}
		}

		return $filled;
	}

	/**
	 * @since 2.02
	 */
	public static function validate_file_size( &$errors, $field, $args ) {
		if ( ! isset( $_FILES[ $args['file_name'] ] ) ) {
			return;
		}

		$mb_limit     = FrmField::get_option( $field, 'size' );
		$size_limit   = self::get_max_file_size( $mb_limit );
		$min_size_limit = FrmField::get_option( $field, 'min_size' );
		$file_uploads = (array) $_FILES[ $args['file_name'] ]; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		foreach ( (array) $file_uploads['name'] as $k => $name ) {

			// Check allowed file size
			if ( ! empty( $file_uploads['error'] ) && in_array( 1, (array) $file_uploads['error'] ) ) {
				// Hit a server limit (upload exceeds upload_max_filesize PHP directive).
				$server_limit = self::get_server_filesize_limit();

				$errors[ 'field' . $field->temp_id ] = sprintf(
					// translators: %s: File size limit (Megabytes)
					__( 'That file is too big. It must be less than %sMB.', 'formidable-pro' ),
					false !== $server_limit ? $server_limit : $size_limit
				);
			}

			if ( empty( $name ) ) {
				continue;
			}

			$this_file_size = is_array( $file_uploads['size'] ) ? $file_uploads['size'][ $k ] : $file_uploads['size'];
			$this_file_size = $this_file_size / 1000000; // compare in MB

			if ( $this_file_size > $size_limit ) {
				// translators: %s: File size limit (Megabytes).
				$errors[ 'field' . $field->temp_id ] = sprintf( __( 'That file is too big. It must be less than %sMB.', 'formidable-pro' ), $size_limit );
			}

			if ( $this_file_size < $min_size_limit ) {
				// translators: %s: File minimum size limit (Megabytes).
				$errors[ 'field' . $field->temp_id ] = sprintf( __( 'That file is too small. It must be greater than %sMB.', 'formidable-pro' ), $min_size_limit );
			}

			unset( $name );
		}
	}

	/**
	 * Get the filesize limit in Megabytes.
	 *
	 * @since 6.3
	 *
	 * @return int|float|false
	 */
	private static function get_server_filesize_limit() {
		$limit = ini_get( 'upload_max_filesize' );
		if ( ! $limit ) {
			return false;
		}

		return self::convert_size_to_mb( $limit );
	}

	/**
	 * @since 6.3
	 *
	 * @param string $limit
	 * @return int|float|false
	 */
	private static function convert_size_to_mb( $limit ) {
		$value = substr( $limit, 0, -1 );
		if ( ! is_numeric( $value ) ) {
			return false;
		}

		$value = (int) $value;
		$unit  = $limit[ strlen( $limit ) - 1 ];
		switch ( $unit ) {
			case 'M':
				return $value;
			case 'K':
				return $value / 1024;
			case 'G':
				return $value * 1024;
		}

		return $value;
	}

	/**
	 * @param int $mb_limit
	 * @return int
	 */
	public static function get_max_file_size( $mb_limit = 256 ) {
		if ( ! $mb_limit || ! is_numeric( $mb_limit ) ) {
			$mb_limit = 516;
		}

		$mb_limit   = (float) $mb_limit;
		$upload_max = wp_max_upload_size() / 1000000;

		return round( min( $upload_max, $mb_limit ), 2 );
	}

	/**
	 * @since 2.02
	 *
	 * @param array    $errors
	 * @param stdClass $field
	 * @param array    $args
	 * @param array    $values
	 * @return void
	 */
	private static function validate_file_count( &$errors, $field, $args, $values ) {
		$multiple_files_allowed = FrmField::get_option( $field, 'multiple' );
		$file_count_limit       = (int) FrmField::get_option( $field, 'max' );
		if ( ! $multiple_files_allowed || empty( $file_count_limit ) ) {
			return;
		}

		$total_upload_count = self::get_new_and_old_file_count( $field, $args );
		if ( $total_upload_count > $file_count_limit ) {
			$errors[ 'field' . $field->temp_id ] = self::get_max_file_limit_error( $file_count_limit );
		}
	}

	/**
	 * Count the number of new files uploaded
	 * along with any previously uploaded files
	 *
	 * @since 2.02
	 *
	 * @param stdClass $field
	 * @param array $args
	 * @return int
	 */
	private static function get_new_and_old_file_count( $field, $args ) {
		if ( isset( $_FILES[ $args['file_name'] ] ) ) {
			$file_uploads   = (array) $_FILES[ $args['file_name'] ]; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$uploaded_count = count( array_filter( $file_uploads['tmp_name'] ) );
		} else {
			$uploaded_count = 0;
		}

		$previous_uploads      = (array) self::get_file_posted_vals( $field->id, $args );
		$previous_upload_count = count( array_filter( $previous_uploads ) );

		$total_upload_count = $uploaded_count + $previous_upload_count;
		return $total_upload_count;
	}

	/**
	 * @since 2.02
	 *
	 * @param array    $errors
	 * @param stdClass $field
	 * @param array    $args {
	 *     @type string $file_name
	 * }
	 * @return void
	 */
	public static function validate_file_type( &$errors, $field, $args ) {
		if ( isset( $errors[ 'field' . $field->temp_id ] ) ) {
			return;
		}

		if ( ! isset( $_FILES[ $args['file_name'] ] ) ) {
			return;
		}

		$mimes = self::get_allowed_mimes( $field );

		$file_uploads = $_FILES[ $args['file_name'] ]; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		foreach ( (array) $file_uploads['name'] as $name ) {
			if ( empty( $name ) ) {
				continue;
			}

			//check allowed mime types for this field
			$file_type = wp_check_filetype( $name, $mimes );
			unset($name);

			if ( ! $file_type['ext'] ) {
				break;
			}
		}

		if ( isset( $file_type ) && ! $file_type['ext'] ) {
			$errors[ 'field' . $field->temp_id ] = self::get_invalid_file_type_message( $field->name, $field->field_options['invalid'] );
		}
	}

	/**
	 * @param array|stdClass $field
	 * @return array|null
	 */
	public static function get_allowed_mimes( $field ) {
		$mimes    = FrmField::get_option( $field, 'ftypes' );
		$restrict = FrmField::is_option_true( $field, 'restrict' ) && ! empty( $mimes );
		if ( ! $restrict ) {
			$mimes = null;
		}
		return $mimes;
	}

	/**
	 * @param string $field_name
	 * @param string $field_invalid_msg
	 * @return string
	 */
	private static function get_invalid_file_type_message( $field_name, $field_invalid_msg ) {
		$default_invalid_messages = array( '' );
		$default_invalid_messages[] = __( 'This field is invalid', 'formidable-pro' );
		$default_invalid_messages[] = $field_name . ' ' . __( 'is invalid', 'formidable-pro' );
		$is_default_message = in_array( $field_invalid_msg, $default_invalid_messages );

		$invalid_type = __( 'Sorry, this file type is not permitted.', 'formidable-pro' );
		$invalid_message = $is_default_message ? $invalid_type : $field_invalid_msg;

		return $invalid_message;
	}

	/**
	 * @since 4.10.02
	 *
	 * @param array  $errors
	 * @param object $field
	 * @param array  $args
	 */
	private static function validate_file_spam( &$errors, $field, $args ) {
		if ( isset( $errors[ 'field' . $field->temp_id ] ) || ! isset( $_FILES[ $args['file_name'] ] ) || ! isset( $_FILES[ $args['file_name'] ]['name'] ) ) {
			return;
		}

		$file_names = array_map(
			function( $file_name ) {
				return sanitize_option( 'upload_path', $file_name );
			},
			(array) $_FILES[ $args['file_name'] ]['name'] // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		);

		$file_is_spam  = false;
		$spam_keywords = apply_filters( 'frm_filename_spam_keywords', self::get_default_filename_spam_keywords() );

		if ( $spam_keywords && is_array( $spam_keywords ) ) {
			foreach ( $file_names as $file_name ) {
				foreach ( $spam_keywords as $keyword ) {
					if ( false !== strpos( $file_name, $keyword ) ) {
						$file_is_spam = true;
						break;
					}
				}
			}
			unset( $file_name );
		}

		$values = array(
			'item_meta' => array( $field->id => $file_names ),
		);
		if ( FrmEntryValidate::blacklist_check( $values ) ) {
			$file_is_spam = true;
		}

		if ( $file_is_spam ) {
			$errors[ 'field' . $field->temp_id ] = __( 'File is spam', 'formidable-pro' );
		}
	}

	/**
	 * @return array
	 */
	private static function get_default_filename_spam_keywords() {
		return array( 'fortnite', 'vbucks', 'roblox', 'robux' );
	}

	/**
	 * Upload new files, delete removed files
	 *
	 * @since 2.0
	 * @param array|string $meta_value (the posted value)
	 * @param int $field_id
	 * @param int $entry_id
	 * @return array|string $meta_value
	 */
	public static function prepare_data_before_db( $meta_value, $field_id, $entry_id, $atts ) {
		_deprecated_function( __FUNCTION__, '3.0', 'FrmFieldType::get_value_to_save' );
		$atts['field_id'] = $field_id;
		$atts['entry_id'] = $entry_id;
		$field_obj = FrmFieldFactory::get_field_object( $atts['field'] );
		return $field_obj->get_value_to_save( $meta_value, $atts );
	}

	/**
	* Get media ID(s) to be saved to database and set global media ID values
	*
	* @since 2.0
	* @param array|string $prev_value (posted value)
	* @param object $field
	* @param integer $entry_id
	* @return array|string $meta_value
	*/
	public static function prepare_file_upload_meta( $prev_value, $field, $entry_id ) {
		global $frm_vars;

		if ( ! empty( $frm_vars['checking_duplicates'] ) ) {
			// this function is called in the FrmEntry::is_duplicate check as well so it shouldn't always update the database when this function is called.
			return $prev_value;
		}

		// remove temp tag on uploads
		self::remove_meta_from_media( $prev_value, $field->form_id );
		$last_saved_value = self::get_previous_file_ids( $field, $entry_id );
		self::delete_removed_files( $last_saved_value, $prev_value, $field );

		return $prev_value;
	}

	/**
	 * @param array    $errors
	 * @param stdClass $field
	 * @param array    $args
	 * @return void
	 */
	private static function maybe_upload_temp_file( &$errors, $field, $args ) {
		if ( ! isset( $_FILES[ $args['file_name'] ] ) ) {
			return;
		}

		$file_uploads = $_FILES[ $args['file_name'] ]; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash

		if ( ! self::file_was_selected( $file_uploads ) ) {
			return;
		}

		$response = array(
			'errors'    => array(),
			'media_ids' => array(),
		);
		self::upload_temp_files( $args['file_name'], $response, $field );

		if ( ! empty( $response['media_ids'] ) ) {
			$previous_value = self::get_file_posted_vals( $field->id, $args );
			$new_value      = self::set_new_file_upload_meta_value( $field, $response['media_ids'], $previous_value );
			self::set_file_posted_vals( $field->id, $new_value, $args );
		}

		if ( ! empty( $response['errors'] ) ) {
			$errors[ 'field' . $field->temp_id ] = implode( ' ', $response['errors'] );
		}
	}

	public static function ajax_upload() {
		$response = array(
			'errors'    => array(),
			'media_ids' => array(),
		);
		$field_id = FrmAppHelper::get_param( 'field_id', '', 'post', 'absint' );

		if ( empty( $_FILES ) || ! $field_id ) {
			return $response;
		}

		$field = FrmField::getOne( $field_id, true );
		if ( ! self::should_allow_upload_for_field( $field ) ) {
			return $response;
		}

		$field->temp_id = $field->id;
		$is_spam        = ! self::ajax_upload_includes_valid_antispam_token( $field, $response['errors'] );

		if ( ! $is_spam ) {
			foreach ( $_FILES as $file_name => $file ) {
				$args = array( 'file_name' => $file_name );
				self::validate_file_type( $response['errors'], $field, $args );
				self::validate_file_size( $response['errors'], $field, $args );
				self::validate_file_spam( $response['errors'], $field, $args );
				$response['errors'] = apply_filters( 'frm_validate_file', $response['errors'], $field, $args );

				if ( empty( $response['errors'] ) ) {
					self::upload_temp_files( $file_name, $response, $field );
				}
			}
		}

		$response = apply_filters( 'frm_response_after_upload', $response, $field );
		return $response;
	}

	/**
	 * @param stdClass|null $field
	 * @return bool
	 */
	private static function should_allow_upload_for_field( $field ) {
		if ( ! $field || 'file' !== $field->type ) {
			return false;
		}

		$form_info = FrmDb::get_row( 'frm_forms', array( 'id' => $field->form_id ), 'status, logged_in' );
		if ( ! $form_info || 'trash' === $form_info->status ) {
			return false;
		}

		if ( $form_info->logged_in && ! is_user_logged_in() ) {
			return false;
		}

		return true;
	}

	/**
	 * @since 4.11
	 *
	 * @param object $field
	 * @param array  $errors passed by reference.
	 * @return bool  True if a token is passed and it is valid, or if antispam is turned off or does not exist.
	 */
	private static function ajax_upload_includes_valid_antispam_token( $field, &$errors ) {
		$valid = true;

		if ( ! class_exists( 'FrmAntiSpam' ) ) {
			return $valid;
		}

		$aspm           = new FrmAntiSpam( $field->form_id );
		$antispam_check = $aspm->validate();
		$valid          = ! is_string( $antispam_check );

		if ( ! $valid ) {
			$errors[ 'field' . $field->temp_id ] = esc_html__( 'File is spam', 'formidable' );
		}

		return $valid;
	}

	/**
	 * @param string $file_name
	 * @param array $response
	 * @param object $field
	 */
	private static function upload_temp_files( $file_name, &$response, $field ) {
		self::$uploading_temporary_files = true;
		self::$active_upload_field       = $field;
		self::$new_temporary_file_ids    = array();
		add_action( 'add_post_meta', 'FrmProFileField::add_frm_temporary_meta', 10, 3 );

		$new_media_ids = self::upload_file( $file_name );

		$new_media_ids = (array) $new_media_ids;
		$errors        = array_filter( $new_media_ids, 'is_wp_error' );
		$new_media_ids = array_filter( $new_media_ids, 'is_numeric' );

		if ( ! $new_media_ids ) {
			if ( $errors ) {
				$errors = array_map(
					function( $error ) {
						return $error->get_error_message();
					},
					$errors
				);
				$response['errors'] = array_merge( $response['errors'], $errors );
			} else {
				$response['errors'][] = __( 'File upload failed', 'formidable-pro' );
			}
		} else {
			$missing_file_ids = array_diff( $new_media_ids, self::$new_temporary_file_ids );
			if ( $missing_file_ids ) {
				self::add_meta_to_media( $missing_file_ids, 'temporary', $field->id );
			}
			$response['media_ids'] = $response['media_ids'] + $new_media_ids;
			self::sort_errors_from_ids( $response );
		}

		remove_action( 'add_post_meta', 'FrmProFileField::add_frm_temporary_meta', 10 );
		self::$uploading_temporary_files = false;
		self::$active_upload_field       = null;
	}

	/**
	 * @param int    $object_id
	 * @param string $meta_key
	 * @param string $meta_value
	 * @return void
	 */
	public static function add_frm_temporary_meta( $object_id, $meta_key, $meta_value ) {
		if ( '_wp_attached_file' !== $meta_key || ! self::$uploading_temporary_files || empty( self::$active_upload_field ) ) {
			return;
		}

		$field                = self::$active_upload_field;
		$upload_dir           = self::get_upload_dir_for_form( $field->form_id );
		$is_in_formidable_dir = '' !== $upload_dir && 0 === strpos( $meta_value, $upload_dir );

		if ( $is_in_formidable_dir ) {
			self::$new_temporary_file_ids[] = $object_id;
			self::add_meta_to_media( $object_id, 'temporary', $field->id );
		}
	}

	/**
	 * @param int $attachment_id Original attachment id.
	 * @param int $duplicated_attachment_id Attachment copy id.
	 */
	public static function remove_frm_temporary_meta( $attachment_id, $duplicated_attachment_id ) {
		delete_post_meta( $duplicated_attachment_id, '_frm_temporary' );
	}

	/**
	 * Let WordPress process the uploads
	 *
	 * @param string|array $file_id Index (or array of Indices) of the $_FILES array that the file was sent.
	 * @param bool         $sideload If True upload is handled with media_handle_sideload instead of media_handle_upload.
	 */
	public static function upload_file( $file_id, $sideload = false ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';

		self::allow_xlsx_files_from_google_sheets();

		$response = array( 'media_ids' => array(), 'errors' => array() );
		add_filter( 'upload_dir', array( 'FrmProFileField', 'upload_dir' ) );

		if ( ! $sideload && isset( $_FILES[ $file_id ] ) && isset( $_FILES[ $file_id ]['name'] ) && is_array( $_FILES[ $file_id ]['name'] ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			$file_keys = array_keys( $_FILES[ $file_id ]['name'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

			foreach ( $file_keys as $k ) {
				if ( empty( $_FILES[ $file_id ]['name'][ $k ] ) || ! isset( $_FILES[ $file_id ]['type'][ $k ] ) || ! isset( $_FILES[ $file_id ]['tmp_name'][ $k ] ) || ! isset( $_FILES[ $file_id ]['error'][ $k ] ) || ! isset( $_FILES[ $file_id ]['size'][ $k ] ) ) {
					continue;
				}

				$f_id            = $file_id . $k;
				$_FILES[ $f_id ] = array(
					'name'     => self::maybe_truncate_long_file_name( sanitize_file_name( wp_unslash( $_FILES[ $file_id ]['name'][ $k ] ) ) ),
					'type'     => sanitize_mime_type( wp_unslash( $_FILES[ $file_id ]['type'][ $k ] ) ),
					'tmp_name' => sanitize_option( 'upload_path', $_FILES[ $file_id ]['tmp_name'][ $k ] ), // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
					'error'    => absint( wp_unslash( $_FILES[ $file_id ]['error'][ $k ] ) ),
					'size'     => absint( wp_unslash( $_FILES[ $file_id ]['size'][ $k ] ) ),
				);

				unset( $k );

				self::handle_upload( $f_id, $response );
			}
		} else {
			if ( is_string( $file_id ) && isset( $_FILES[ $file_id ] ) && isset( $_FILES[ $file_id ]['name'] ) && is_string( $_FILES[ $file_id ]['name'] ) ) {
				$_FILES[ $file_id ]['name'] = self::maybe_truncate_long_file_name( sanitize_file_name( wp_unslash( $_FILES[ $file_id ]['name'] ) ) );
			}
			self::handle_upload( $file_id, $response, $sideload );
		}

		remove_filter( 'upload_dir', array( 'FrmProFileField', 'upload_dir' ) );

		self::prepare_upload_response( $response );

		return $response;
	}

	/**
	 * Treat Google sheet xlsx files as the expected xlsx mime type.
	 * Otherwise it isn't possible possible to upload an xlsx file downloaded from Google sheets.
	 *
	 * @since 6.1.2
	 *
	 * @return void
	 */
	private static function allow_xlsx_files_from_google_sheets() {
		if ( self::$added_xlsx_filter ) {
			return;
		}

		add_filter(
			'wp_check_filetype_and_ext',
			/**
			 * @param array         $wp_check_filetype_and_ext
			 * @param string        $file
			 * @param string        $filename
			 * @param array<string> $mimes
			 * @param string        $real_mime
			 * @return array
			 */
			function( $wp_check_filetype_and_ext, $file, $filename, $mimes, $real_mime ) {
				if ( 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheetapplication/vnd.openxmlformats-officedocument.spreadsheetml.sheet' === $real_mime ) {
					$wp_check_filetype_and_ext['ext']  = 'xlsx';
					$wp_check_filetype_and_ext['type'] = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
				}
				return $wp_check_filetype_and_ext;
			},
			10,
			5
		);
		self::$added_xlsx_filter = true;
	}

	/**
	 * @since 5.0.03
	 * @param string $name
	 * @return string
	 */
	private static function maybe_truncate_long_file_name( $name ) {
		$max_filename_length = apply_filters( 'frm_max_filename_length', 100, compact( 'name' ) );

		if ( strlen( $name ) < $max_filename_length && strlen( rawurlencode( $name ) ) <= self::$post_name_limit ) {
			return $name;
		}

		$split     = explode( '.', $name );
		$extension = array_pop( $split );
		$name      = implode( '.', $split );
		$name      = substr( $name, 0, $max_filename_length - strlen( $extension ) - 1 );

		self::check_filename_against_max_post_name( $name, $extension );

		return $name . '.' . $extension;
	}

	/**
	 * Checks a file name against WP post_name limit and truncates it if needed.
	 *
	 * @since 6.4
	 *
	 * @param string $name
	 * @param string $extension
	 *
	 * @return void
	 */
	private static function check_filename_against_max_post_name( &$name, $extension ) {
		$max_name_part_length = self::$post_name_limit - strlen( $extension ) - 1;
		$encoded_name_length  = strlen( rawurlencode( $name ) );

		while ( $encoded_name_length > $max_name_part_length ) {
			$name                = mb_substr( $name, 0, -1 );
			$encoded_name        = rawurlencode( $name );
			$encoded_name_length = strlen( $encoded_name );
		}
	}


	/**
	 * @param string|array $file_id Index (or array of Indices) of the $_FILES array that the file was sent.
	 * @param array        $response
	 * @param bool         $sideload If True upload is handled with media_handle_sideload instead of media_handle_upload.
	 */
	private static function handle_upload( $file_id, &$response, $sideload = false ) {
		add_filter( 'wp_insert_attachment_data', 'FrmProFileField::change_attachment_slug', 10, 2 );

		$resize = false;
		if ( 'frm_submit_dropzone' === FrmAppHelper::get_param( 'action' ) && ! empty( self::$active_upload_field->field_options['resize'] ) ) {
			add_filter( 'wp_image_maybe_exif_rotate', 'FrmProFileField::disable_exif_rotation' );
			add_filter( 'wp_image_editors', 'FrmProFileField::force_gd_editor' );
			$resize = true;
		}

		$media_id = $sideload ? media_handle_sideload( $file_id, 0 ) : media_handle_upload( $file_id, 0 );
		remove_filter( 'wp_insert_attachment_data', 'FrmProFileField::change_attachment_slug' );

		if ( is_numeric( $media_id ) ) {
			$response['media_ids'][] = $media_id;
			$form_id                 = FrmAppHelper::get_param( 'form_id', '', 'post', 'absint' );

			if ( $resize ) {
				$file   = get_attached_file( $media_id );
				$editor = wp_get_image_editor( $file );
				if ( ! is_wp_error( $editor ) ) {
					$editor->save( $file );
				}

				remove_filter( 'wp_image_maybe_exif_rotate', 'FrmProFileField::disable_exif_rotation' );
				remove_filter( 'wp_image_editors', 'FrmProFileField::force_gd_editor' );
			}

			self::maybe_set_chmod(
				array(
					'file_id'   => $media_id,
					'form_id'   => $form_id,
					'protected' => self::file_is_protected( $file_id, $form_id ),
				)
			);
			self::add_meta_to_media( $media_id, 'file' );
		} else {
			$response['errors'][] = $media_id;
		}
	}

	/**
	 * The wp_image_maybe_exif_rotate logic in WordPress has conflicts with the resize functionality in Dropzone.
	 * When uploading with dropzone, with resizae is on, disable the exif rotation. The file is saved again after it is inserted.
	 *
	 * @since 5.1
	 *
	 * @return false
	 */
	public static function disable_exif_rotation() {
		return false;
	}

	/**
	 * On sites with ImageMagick installed the image still gets flipped, so force GD.
	 *
	 * @since 5.1
	 *
	 * @return array
	 */
	public static function force_gd_editor() {
		return array( 'WP_Image_Editor_GD' );
	}

	/**
	 * Prevent attachments from using valuable top-level slug names
	 *
	 * @param array $data
	 * @param array $post
	 * @return array
	 */
	public static function change_attachment_slug( $data, $post ) {
		$slug              = 'frm-' . sanitize_title( $data['post_name'] );
		$post_id           = $post['ID'];
		$post_status       = $data['post_status'];
		$post_type         = $data['post_type'];
		$post_parent       = $data['post_parent'];
		$data['post_name'] = wp_unique_post_slug( $slug, $post_id, $post_status, $post_type, $post_parent );
		return $data;
	}

	private static function prepare_upload_response( &$response ) {
		if ( empty( $response['media_ids'] ) ) {
			$response = $response['errors'];
		} else {
			$response = $response['media_ids'];
			if ( count( $response ) == 1 ) {
				$response = reset( $response );
			}
		}
	}

	/**
	* Get the final media IDs
	*
	* @since 2.0
	* @param array  $response
	* @return array media ids.
	*/
	private static function sort_errors_from_ids( &$response ) {
		$mids = array();
		foreach ( (array) $response['media_ids'] as $media_id ) {
			if ( is_numeric( $media_id ) ) {
				$mids[] = $media_id;
			} else {
				foreach ( $media_id->errors as $error ) {
					if ( ! is_array( $error[0] ) ) {
						$response['errors'][] = $error[0];
					}
					unset( $error );
				}
			}
			unset( $media_id );
		}

		$response['media_ids'] = array_filter( $mids );
	}

	/**
	 * Set _frm_temporary and _frm_file metas
	 * to use for media library filtering
	 *
	 * @param array $media_ids
	 * @param string $type
	 * @param mixed $value
	 */
	private static function add_meta_to_media( $media_ids, $type = 'temporary', $value = 1 ) {
		foreach ( (array) $media_ids as $media_id ) {
			if ( is_numeric( $media_id ) ) {
				update_post_meta( $media_id, '_frm_' . $type, $value );
			}
		}
	}

	/**
	 * When an entry is saved, remove the temp flag
	 *
	 * @param int|array $media_ids
	 * @param int       $form_id
	 * @return void
	 */
	private static function remove_meta_from_media( $media_ids, $form_id ) {
		$form_is_protected    = self::folder_is_protected( $form_id );
		$unprotected_file_ids = array();

		foreach ( (array) $media_ids as $media_id ) {
			if ( ! is_numeric( $media_id ) ) {
				continue;
			}

			if ( ! $form_is_protected ) {
				$unprotected_file_ids[] = $media_id;
			}

			delete_post_meta( $media_id, '_frm_temporary' );
		}

		if ( $unprotected_file_ids ) {
			self::maybe_set_chmod(
				array(
					'dir'       => self::upload_dir_path( $form_id ),
					'form_id'   => $form_id,
					'file_ids'  => $unprotected_file_ids,
					'protected' => false,
				)
			);
		}
	}

	/**
	 * Upload files into "formidable" subdirectory
	 */
	public static function upload_dir( $uploads ) {
		$form_id = FrmAppHelper::get_post_param( 'form_id', 0, 'absint' );
		if ( ! $form_id ) {
			$form_id = FrmAppHelper::simple_get( 'form', 'absint', 0 );
		}

		$relative_path = self::get_upload_dir_for_form( $form_id );

		if ( ! empty( $relative_path ) ) {
			$uploads['path'] = $uploads['basedir'] . '/' . $relative_path;
			$uploads['url'] = $uploads['baseurl'] . '/' . $relative_path;
			$uploads['subdir'] = '/' . $relative_path;

			self::create_index( $uploads, $relative_path );
		}

		return $uploads;
	}

	/**
	 * Create an index.php in the folders where files are being uploaded.
	 *
	 * @since 3.06.01
	 * @param array  $uploads Info about file locations.
	 * @param string $path The folder where files will be saved.
	 */
	private static function create_index( $uploads, $path ) {
		if ( file_exists( $uploads['path'] . $uploads['subdir'] . '/index.php' ) ) {
			return;
		}

		remove_filter( 'upload_dir', array( 'FrmProFileField', 'upload_dir' ) );

		$file_atts = array(
			'file_name'   => 'index.php',
			'folder_name' => $path,
		);

		$file_content = '<?php' . "\r\n";
		$new_file     = new FrmCreateFile( $file_atts );
		$new_file->create_file( $file_content );

		add_filter( 'upload_dir', array( 'FrmProFileField', 'upload_dir' ) );
	}

	public static function get_upload_dir_for_form( $form_id ) {
		$base = 'formidable';
		if ( $form_id ) {
			$base .= '/' . $form_id;
		}

		$relative_path = apply_filters( 'frm_upload_folder', $base, compact( 'form_id' ) );
		$relative_path = untrailingslashit( $relative_path );

		return $relative_path;
	}

	/**
	 * Automatically delete files when an entry is deleted.
	 * If the "Delete all entries" button is used, entries will not be deleted
	 *
	 * @since 2.0.22
	 */
	public static function delete_files_with_entry( $entry_id, $entry = false ) {
		if ( empty( $entry ) ) {
			return;
		}

		$upload_fields = FrmField::getAll( array( 'fi.type' => 'file', 'fi.form_id' => $entry->form_id ) );
		foreach ( $upload_fields as $field ) {
			self::delete_files_from_field( $field, $entry );
			unset( $field );
		}
	}

	/**
	 * @since 2.0.22
	 */
	public static function delete_files_from_field( $field, $entry ) {
		if ( self::should_delete_files( $field ) ) {
			$media_ids = self::get_previous_file_ids( $field, $entry );
			self::delete_files_now( $media_ids );
		}
	}

	private static function should_delete_files( $field ) {
		$auto_delete = FrmField::get_option_in_object( $field, 'delete' );
		return ! empty( $auto_delete );
	}

	/**
	 * @since 2.0.22
	 */
	private static function get_previous_file_ids( $field, $entry_id ) {
		return FrmProEntryMetaHelper::get_post_or_meta_value( $entry_id, $field );
	}

	private static function delete_removed_files( $old_value, $new_value, $field ) {
		if ( self::should_delete_files( $field ) ) {
			$media_ids = self::get_removed_file_ids( $old_value, $new_value );
			self::delete_files_now( $media_ids );
		}
	}

	/**
	 * @since 2.0.22
	 */
	private static function get_removed_file_ids( $old_value, $new_value ) {
		$media_ids = array_diff( (array) $old_value, (array) $new_value );
		return $media_ids;
	}

	/**
	 * @since 2.0.22
	 */
	private static function delete_files_now( $media_ids ) {
		if ( empty( $media_ids ) ) {
			return;
		}

		FrmProAppHelper::unserialize_or_decode( $media_ids );
		foreach ( (array) $media_ids as $m ) {
			if ( is_numeric( $m ) ) {
				wp_delete_attachment( $m, true );
			}
		}
	}

	/**
	 * @since 2.02
	 *
	 * @param int   $field_id
	 * @param array $args
	 * @return array|int
	 */
	private static function get_file_posted_vals( $field_id, $args ) {
		if ( self::is_field_repeating( $field_id, $args ) ) {
			if ( isset( $_POST['item_meta'][ $args['parent_field_id'] ][ $args['key_pointer'] ][ $field_id ] ) ) {
				if ( is_array( $_POST['item_meta'][ $args['parent_field_id'] ][ $args['key_pointer'] ][ $field_id ] ) ) {
					$value = array_map( 'absint', wp_unslash( $_POST['item_meta'][ $args['parent_field_id'] ][ $args['key_pointer'] ][ $field_id ] ) );
				} else {
					$value = absint( wp_unslash( $_POST['item_meta'][ $args['parent_field_id'] ][ $args['key_pointer'] ][ $field_id ] ) );
				}
			}
		} elseif ( isset( $_POST['item_meta'][ $field_id ] ) ) {
			if ( is_array( $_POST['item_meta'][ $field_id ] ) ) {
				$value = array_map( 'absint', wp_unslash( $_POST['item_meta'][ $field_id ] ) );
			} else {
				$value = absint( wp_unslash( $_POST['item_meta'][ $field_id ] ) );
			}
		}
		if ( ! isset( $value ) ) {
			$value = array();
		}
		return $value;
	}

	/**
	*
	* @since 2.0
	* @param int $field_id
	* @param $new_value to set
	* @param array $args array with repeating, key_pointer, and parent_field
	*/
	private static function set_file_posted_vals( $field_id, $new_value, $args ) {
		if ( self::is_field_repeating( $field_id, $args ) ) {
			$_POST['item_meta'][ $args['parent_field_id'] ][ $args['key_pointer'] ][ $field_id ] = $new_value;
		} else {
			$_POST['item_meta'][ $field_id ] = $new_value;
		}
	}

	/**
	* Get the final value for a file upload field
	*
	* @since 2.0.19
	*
	* @param object        $field
	* @param array         $new_mids
	* @param array|string  $prev_value
	* @return array|string $new_value
	*/
	private static function set_new_file_upload_meta_value( $field, $new_mids, $prev_value ) {
		// If no media IDs to upload, end now
		if ( empty( $new_mids ) ) {
			$new_value = $prev_value;
		} elseif ( FrmField::is_option_true( $field, 'multiple' ) ) {
			// Multi-file upload fields
			if ( $prev_value ) {
				$new_value = array_merge( (array) $prev_value, $new_mids );
			} else {
				$new_value = $new_mids;
			}
		} else {
			// Single file upload fields
			$new_value = reset( $new_mids );
		}

		return $new_value;
	}

	/**
	 * @param int   $field_id
	 * @param array $args
	 * @return bool
	 */
	private static function is_field_repeating( $field_id, $args ) {
		// Assume this field is not repeating
		$repeating = false;

		if ( ! empty( $args['parent_field_id'] ) && isset( $args['key_pointer'] ) ) {
			// Check if the current field is inside of the parent/pointer
			$repeating = isset( $_POST['item_meta'][ $args['parent_field_id'] ][ $args['key_pointer'] ][ $field_id ] );
		}

		return $repeating;
	}

	/**
	 * @since 3.01.03
	 */
	public static function duplicate_files_with_entry( $entry_id, $form_id, $args ) {
		$old_entry_id  = ! empty( $args['old_id'] ) ? $args['old_id'] : 0;
		$upload_fields = FrmField::getAll( array( 'fi.type' => 'file', 'fi.form_id' => $form_id ) );

		if ( ! $old_entry_id || ! $upload_fields ) {
			return;
		}

		include_once ABSPATH . 'wp-admin/includes/file.php';

		$form_is_protected = self::folder_is_protected( $form_id );
		foreach ( $upload_fields as $field ) {
			$attachments = self::get_previous_file_ids( $field, $old_entry_id );
			FrmProAppHelper::unserialize_or_decode( $attachments );
			if ( empty( $attachments ) ) {
				continue;
			}

			$new_media_ids = array();

			foreach ( (array) $attachments as $attachment_id ) {
				$orig_path = get_attached_file( $attachment_id );

				if ( ! file_exists( $orig_path ) ) {
					continue;
				}

				// Copy path to a temp location because wp_handle_sideload() deletes the original.
				$tmp_path = wp_tempnam();
				if ( ! $tmp_path ) {
					continue;
				}

				if ( $form_is_protected ) {
					// Temporarily allow access to file so it can be copied.
					self::set_to_read_only( $orig_path );
				}

				$read_file     = new FrmCreateFile(
					array(
						'new_file_path' => dirname( $orig_path ),
						'file_name'     => basename( $orig_path ),
					)
				);
				$file_contents = $read_file->get_file_contents();

				if ( ! $file_contents || false === file_put_contents( $tmp_path, $file_contents ) ) { // phpcs:ignore WordPress.VIP.FileSystemWritesDisallow.file_ops_file_put_contents,
					@unlink( $tmp_path );
					continue;
				}

				if ( $form_is_protected ) {
					// Protect the file that was temporarily been readable.
					self::set_to_write_only( $orig_path );
				}

				$file_arr = array(
					'name'     => basename( $orig_path ),
					'size'     => @filesize( $tmp_path ),
					'tmp_name' => $tmp_path,
					'error'    => 0,
				);
				$response = self::upload_file( $file_arr, true );

				foreach ( (array) $response as $r ) {
					if ( is_numeric( $r ) ) {
						$new_media_ids[] = $r;
					}
				}
			}

			if ( 1 === count( $new_media_ids ) ) {
				$new_meta = reset( $new_media_ids );
			} else {
				$new_meta = $new_media_ids;
			}

			FrmEntryMeta::update_entry_meta( $entry_id, $field->id, null, $new_meta );
		}
	}

	/**
	 * @since 5.4.3
	 *
	 * @param string $path
	 */
	private static function set_to_read_only( $path ) {
		self::chmod( $path, self::READ_ONLY );
	}

	/**
	 * @since 5.4.3
	 *
	 * @param string $path
	 */
	private static function set_to_write_only( $path ) {
		self::chmod( $path, self::WRITE_ONLY );
	}

	/**
	 * Check if a file is currently protected (true for protected forms and also temporary files).
	 *
	 * @since 5.0.09
	 *
	 * @param int $file_id
	 * @param int $form_id
	 * @return bool
	 */
	public static function file_is_protected( $file_id, $form_id ) {
		if ( ! self::file_is_temporary( $file_id ) ) {
			return self::folder_is_protected( $form_id );
		}

		/**
		 * By default files are uploaded as chmod 200 to prevent public access.
		 * This also can cause conflicts with other plugins that try to make updates to files immediately on upload.
		 * It is not recommended to turn this off as it will make files public. Only do this for forms where you can trust the uploaded files.
		 *
		 * @since 5.0.12
		 */
		return apply_filters( 'frm_protect_temporary_file', true, compact( 'file_id', 'form_id' ) );
	}

	/**
	 * @since 5.0.09
	 *
	 * @param int $file_id ID of the attachment.
	 * @return bool
	 */
	public static function file_is_temporary( $file_id ) {
		return get_post_meta( $file_id, '_frm_temporary', true );
	}

	/**
	 * @since 5.0.09
	 *
	 * @param int $file_id ID of the attachment.
	 * @return bool
	 */
	public static function file_is_temporary_and_a_blocked_file_type( $file_id ) {
		if ( ! self::file_is_temporary( $file_id ) ) {
			return false;
		}
		// Allow access to images so previews do not break but block other file types.
		return ! self::file_is_an_image( $file_id );
	}

	/**
	 * @since 5.0.09
	 *
	 * @param int $file_id
	 * @return bool
	 */
	public static function file_is_an_image( $file_id ) {
		$file      = get_attached_file( $file_id );
		$file_type = wp_check_filetype( $file );
		return self::file_type_matches_image( $file_type['type'] );
	}

	/**
	 * @since 5.0.09
	 *
	 * @param string $type
	 * @return bool
	 */
	public static function file_type_matches_image( $type ) {
		return is_string( $type ) && 0 === strpos( $type, 'image/' );
	}

	/**
	 * @param int $file_id
	 * @return bool
	 */
	public static function is_formidable_file( $file_id ) {
		$meta = get_post_meta( $file_id, '_frm_file', true );
		return ! is_array( $meta ) && $meta;
	}

	/**
	 * @return bool
	 */
	public static function server_supports_htaccess() {
		return strpos( FrmAppHelper::get_server_value( 'SERVER_SOFTWARE' ), 'nginx' ) === false && self::files_can_be_modified_on_server();
	}

	/**
	 * @return bool
	 */
	private static function files_can_be_modified_on_server() {
		ob_start();
		$credentials = request_filesystem_credentials( add_query_arg( array( 'page' => 'formidable-settings' ), admin_url( 'admin.php' ) ) );
		ob_end_clean();
		return $credentials !== false;
	}

	/**
	 * Check if the current user has permission to access a specific file
	 *
	 * @param int $id
	 * @return bool
	 */
	public static function user_has_permission( $id ) {
		if ( ! self::check_temporary_file_access( $id ) ) {
			return false;
		}

		$form_id = self::get_form_id_from_file_id( $id );
		if ( ! self::folder_is_protected( $form_id ) ) {
			return true;
		}

		$protect_files_roles = self::get_option( $form_id, 'protect_files_role', 0 );

		if ( ! $protect_files_roles ) {
			return true;
		}

		return FrmProFieldsHelper::user_has_permission( $protect_files_roles );
	}

	/**
	 * @since 5.0.09
	 *
	 * @param int $file_id
	 * @return bool false if the user fails the temporary access check. true if the file is not temporary.
	 */
	public static function check_temporary_file_access( $file_id ) {
		return self::logged_in_user_can_access_temporary_files() || ! self::file_is_temporary_and_a_blocked_file_type( $file_id );
	}

	/**
	 * @since 5.0.09
	 *
	 * @return bool
	 */
	public static function logged_in_user_can_access_temporary_files() {
		return current_user_can( 'frm_edit_entries' );
	}

	/**
	 * @param array $args
	 * @return int
	 */
	public static function get_chmod( $args ) {
		if ( isset( $args['file'] ) ) {
			$path = $args['file'];
		} elseif ( isset( $args['file_id'] ) ) {
			$path = get_attached_file( $args['file_id'] );
		} else {
			return -1;
		}

		clearstatcache();
		return fileperms( $path ) & 0777;
	}

	/**
	 * @param array $args
	 */
	public static function maybe_set_chmod( $args ) {
		$args = self::fill_missing_chmod_args( $args );

		if ( ! $args ) {
			return;
		}

		if ( isset( $args['file_id'] ) ) {
			self::set_file_protection( get_attached_file( $args['file_id'] ), $args['protected'] );
			return;
		}

		$dir             = $args['dir'];
		$file_ids        = array_filter( array_map( 'absint', $args['file_ids'] ) );
		$files_to_update = array();
		foreach ( $file_ids as $file_id ) {
			$files_to_update[] = basename( get_attached_file( $file_id ) );

			$metadata = wp_get_attachment_metadata( $file_id );
			if ( is_array( $metadata ) && isset( $metadata['sizes'] ) ) {
				$files_to_update = array_merge( $files_to_update, array_column( $metadata['sizes'], 'file' ) );
			}
		}

		foreach ( $files_to_update as $file ) {
			$path = "$dir/$file";
			if ( is_file( $path ) ) {
				self::set_file_protection( $path, $args['protected'] );
			}
		}
	}

	/**
	 * Fill missing chmod keys with function calls based off of other data provided in $args
	 * Also performs some light clean up and validation. If data cannot be filled properly, $args returned will be false
	 *
	 * @param array $args
	 * @return array|false
	 */
	private static function fill_missing_chmod_args( $args ) {
		$is_single_file = isset( $args['file_id'] ) && is_numeric( $args['file_id'] );
		$is_folder      = isset( $args['file_ids'] ) && is_array( $args['file_ids'] );

		if ( ! $is_single_file && ! $is_folder ) {
			return false;
		}

		$missing_args = ! isset( $args['protected'] );
		if ( $is_folder ) {
			$missing_args = $missing_args || ! isset( $args['dir'] );
		}

		if ( ! $missing_args ) {
			return self::cleanup_chmod_args( $args );
		}

		if ( ! isset( $args['form_id'] ) ) {
			$file_id         = isset( $args['file_id'] ) ? $args['file_id'] : reset( $args['file_ids'] );
			$args['form_id'] = self::get_form_id_from_file_id( $file_id );
		}

		if ( ! $args['form_id'] || -1 === $args['form_id'] ) {
			return false;
		}

		if ( ! isset( $args['protected'] ) ) {
			if ( isset( $args['file_id'] ) ) {
				$args['protected'] = self::file_is_protected( $args['file_id'], $args['form_id'] );
			} else {
				$args['protected'] = self::folder_is_protected( $args['form_id'] );
			}
		}

		if ( $is_folder && ! isset( $args['dir'] ) ) {
			$args['dir'] = self::upload_dir_path( $args['form_id'] );
		}

		return self::cleanup_chmod_args( $args );
	}

	private static function cleanup_chmod_args( $args ) {
		if ( isset( $args['dir'] ) ) {
			$args['dir'] = untrailingslashit( $args['dir'] );
			if ( ! file_exists( $args['dir'] ) ) {
				return false;
			}
		}

		if ( isset( $args['file_ids'] ) ) {
			$args['file_ids'] = array_filter( array_map( 'absint', $args['file_ids'] ) );
			if ( ! $args['file_ids'] ) {
				return false;
			}
		}

		return $args;
	}

	/**
	 * @param int               $id Post attachment ID.
	 * @param string|int[]|bool $size
	 * @param array             $args supported keys include "url" and "leave_size_out_of_payload"
	 * @return string a maybe-protected url to use for our specified file id and size.
	 */
	public static function get_file_url( $id, $size = false, $args = array() ) {
		$form_id = self::get_form_id_from_file_id( $id );
		$url     = isset( $args['url'] ) ? $args['url'] : false;
		$builder = new FrmProFilePayloadBuilder( $id, $size, $url );

		if ( -1 === $form_id ) {
			return $builder->get_url();
		}

		$protected    = self::file_is_protected( $id, $form_id );
		$chmod_params = array( 'file_id' => $id, 'form_id' => $form_id, 'protected' => $protected );

		if ( false === $size && wp_attachment_is_image( $id ) && ! get_post_meta( $id, '_wp_attachment_metadata', true ) ) {
			self::delay_file_protection_for_image( $chmod_params );
		} else {
			// Protect non-images immediately.
			self::maybe_set_chmod( $chmod_params );
		}

		if ( ! $protected ) {
			return $builder->get_url();
		}

		$leave_filesize_out_of_payload = ! empty( $args['leave_size_out_of_payload'] );
		return $builder->get_protected_url( self::file_protocol(), $leave_filesize_out_of_payload );
	}

	/**
	 * Images are not protected immediately so that thumbnails may be generated.
	 *
	 * @param array $chmod_params {
	 *     @type int  $file_id
	 *     @type int  $form_id
	 *     @type bool $protected
	 * }
	 * @return void
	 */
	private static function delay_file_protection_for_image( $chmod_params ) {
		add_filter(
			'wp_generate_attachment_metadata',
			/**
			 * @param array  $metadata
			 * @param int    $attachment_id
			 * @param string $context
			 * @return array
			 */
			function( $metadata, $attachment_id, $context ) use ( $chmod_params ) {
				if ( 'create' === $context && $attachment_id === $chmod_params['file_id'] ) {
					self::maybe_set_chmod( $chmod_params );
				}
				return $metadata;
			},
			10,
			3
		);
	}

	/**
	 * @param int $form_id
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	public static function get_option( $form_id, $key, $default ) {
		$options = FrmDb::get_var( 'frm_forms', array( 'id' => $form_id ), 'options' );
		FrmProAppHelper::unserialize_or_decode( $options );
		return isset( $options[ $key ] ) ? $options[ $key ] : $default;
	}

	/**
	 * Check REQUEST_URI for protected file download details
	 *
	 * @return void
	 */
	public static function check_for_download() {
		$payload = self::get_file_payload();

		if ( ! $payload ) {
			return;
		}

		$download = self::get_download_filepath( $payload );

		if ( ! isset( $download['code'] ) ) {
			return;
		}

		if ( 200 !== $download['code'] ) {
			self::handle_download_error( $download );
			return;
		}

		// Temporary allow file access.
		self::set_to_read_only( $download['path'] );

		// If wp_filesystem->chmod didn't work, try using chmod.
		if ( ! is_readable( $download['path'] ) ) {
			chmod( $download['path'], self::READ_ONLY );
		}

		$mime_type   = FrmProAppHelper::get_mime_type( $download['path'] );
		$disposition = self::get_disposition( $mime_type );

		// Extend time limit in case for a large file that may require additional time to download.
		if ( ! ini_get( 'safe_mode' ) ) {
			set_time_limit( 0 );
		}

		header( FrmAppHelper::get_server_value('SERVER_PROTOCOL') . ' 200 OK');
		header( 'Cache-Control: public' ); // needed for internet explorer
		header( 'Content-Type: ' . $mime_type );
		header( 'Content-Transfer-Encoding: Binary' );
		header( 'Content-Length:' . filesize( $download['path'] ) );
		header( 'Content-Disposition: ' . esc_attr( $disposition ) . '; filename=' . esc_attr( $download['name'] ) );

		if ( ! empty( $download['is_temporary'] ) || self::noindex_setting_is_on_for_file( $download['form_id'] ) ) {
			header( 'X-Robots-Tag: noindex' );
		}

		// Clear the output buffer.
		// Without this some servers will load a corrupted file instead.
		while ( ob_get_level() ) {
			ob_end_clean();
		}

		@readfile( $download['path'] ); // Hide any errors to prevent issues with downloading an error message as a file.

		// Set the protection back after download.
		self::set_to_write_only( $download['path'] );
		die();
	}

	/**
	 * @since 5.2.02
	 *
	 * @param array $download
	 * @return void
	 */
	private static function handle_download_error( $download ) {
		status_header( $download['code'] );

		if ( 404 === $download['code'] ) {
			$message = __( 'Oops! That file no longer exists', 'formidable-pro' );
		} else {
			$message = __( 'Oops! That file is protected', 'formidable-pro' );
		}

		$title = is_user_logged_in() ? $download['message'] : '';

		wp_die(
			'<h1>' . esc_html( $message ) . '</h1>',
			'<p>' . esc_html( $title ) . '</p>',
			absint( $download['code'] )
		);
	}

	/**
	 * @since 5.0.09
	 *
	 * @param int $form_id
	 * @return int 1 or 0, 1 if the file in the form should not be indexed.
	 */
	private static function noindex_setting_is_on_for_file( $form_id ) {
		return self::get_option( $form_id, 'noindex_files', 0 );
	}

	/**
	 * Determine Content-Disposition based on $mime_type
	 * We want to inline PDF and images
	 *
	 * @param string $mime_type
	 * @return string
	 */
	private static function get_disposition( $mime_type ) {
		$is_pdf   = 'application/pdf' === $mime_type;
		$is_image = 0 === strpos( $mime_type, 'image/' );
		if ( $is_pdf || $is_image ) {
			return 'inline';
		}
		return 'attachment';
	}

	/**
	 * @param int $form_id
	 * @return string
	 */
	private static function upload_dir_url( $form_id ) {
		return trailingslashit( wp_upload_dir()['baseurl'] ) . self::get_upload_dir_for_form( $form_id );
	}

	/**
	 * @param int $form_id
	 * @return string
	 */
	private static function upload_dir_path( $form_id ) {
		return trailingslashit( wp_upload_dir()['basedir'] ) . self::get_upload_dir_for_form( $form_id );
	}

	/**
	 * @param int $file_id
	 * @return int
	 */
	private static function get_form_id_from_file_id( $file_id ) {
		$meta = get_post_meta( $file_id, '_frm_file', true );

		if ( ! $meta ) {
			$path = get_attached_file( $file_id );
			if ( self::file_is_in_the_formidable_uploads_dir( $path ) ) {
				$meta = 1;
			} else {
				return -1;
			}
		}

		if ( is_array( $meta ) ) {
			return -1;
		}

		$meta = (int) $meta;

		if ( $meta !== 1 ) {
			return $meta;
		}

		$file_upload_field_ids = self::get_all_file_upload_field_ids();

		$form_id_from_metas = self::search_item_meta_for_file( $file_id );
		if ( false !== $form_id_from_metas ) {
			update_post_meta( $file_id, '_frm_file', $form_id_from_metas );
			return $form_id_from_metas;
		}

		$path = get_attached_file( $file_id );
		if ( self::file_is_in_the_formidable_uploads_dir( $path ) ) {
			$relative_path = str_replace( self::default_formidable_uploads_dir(), '', $path );
			$split         = explode( '/', $relative_path );

			if ( 2 === count( $split ) && is_numeric( $split[0] ) ) {
				$form_id = $split[0];
				update_post_meta( $file_id, '_frm_file', $form_id );
				return $form_id;
			}
		}

		return -1;
	}

	/**
	 * @param int $file_id
	 * @return int|false form id
	 */
	private static function search_item_meta_for_file( $file_id ) {
		$metas = self::get_all_file_upload_metas();

		if ( ! $metas ) {
			return false;
		}

		$string_file_id = "{$file_id}";
		$string_check   = ':"' . $file_id . '"';
		$int_check      = 'i:' . $file_id . ';';

		foreach ( $metas as $meta ) {
			if ( $string_file_id === $meta->meta_value || false !== strpos( $meta->meta_value, $string_check ) || false !== strpos( $meta->meta_value, $int_check ) ) {
				return self::get_form_id_from_field_id( $meta->field_id );
			}
		}

		return false;
	}

	private static function get_form_id_from_field_id( $field_id ) {
		return FrmDb::get_var( 'frm_fields', array( 'id' => $field_id ), 'form_id' );
	}

	private static function get_all_file_upload_metas() {
		$field_ids = self::get_all_file_upload_field_ids();

		if ( ! $field_ids ) {
			return array();
		}

		if ( ! isset( self::$all_file_upload_item_metas ) ) {
			self::$all_file_upload_item_metas = FrmDb::get_results(
				'frm_item_metas',
				array(
					'field_id' => $field_ids,
				),
				'field_id, meta_value'
			);
		}

		return self::$all_file_upload_item_metas;
	}

	private static function get_all_file_upload_field_ids() {
		if ( ! isset( self::$all_file_upload_field_ids ) ) {
			self::$all_file_upload_field_ids = FrmDb::get_col( 'frm_fields', array( 'type' => 'file' ) );
		}
		return self::$all_file_upload_field_ids;
	}

	/**
	 * As a fallback, if the _frm_file meta is missing for whatever reason, still check for files in the formidable uploads dir
	 *
	 * @param string $path
	 * @return bool
	 */
	private static function file_is_in_the_formidable_uploads_dir( $path ) {
		$file_folder            = dirname( $path );
		$formidable_uploads_dir = self::default_formidable_uploads_dir();
		$dir_length             = strlen( $formidable_uploads_dir );
		if ( strlen( $file_folder ) < $dir_length ) {
			return false;
		}
		return $formidable_uploads_dir === substr( $file_folder, 0, $dir_length );
	}

	public static function default_formidable_uploads_dir() {
		return trailingslashit( wp_upload_dir()['basedir'] ) . 'formidable/';
	}

	/**
	 * Check if the current user has permission to access a specific form
	 *
	 * @param int $form_id
	 * @return bool
	 */
	private static function folder_is_protected( $form_id ) {
		if ( ! $form_id || $form_id === -1 ) {
			return false;
		}

		$form = FrmForm::getOne( $form_id );
		if ( ! $form ) {
			return false;
		}

		return self::get_option( $form->parent_form_id ? $form->parent_form_id : $form_id, 'protect_files', 0 );
	}

	/**
	 * @param string $file path
	 * @param bool protected
	 */
	private static function set_file_protection( $file, $protected ) {
		if ( ! file_exists( $file ) ) {
			return;
		}

		$chmod = $protected ? self::WRITE_ONLY : 0644;
		$leave = array( $chmod );

		if ( $protected ) {
			$leave[] = self::READ_ONLY;
		}

		if ( ! in_array( self::get_chmod( array( 'file' => $file ) ), $leave, true ) ) {
			self::chmod( $file, $chmod );
		}
	}

	/**
	 * @param string $file
	 * @param int $mode
	 */
	public static function chmod( $file, $mode ) {
		self::setup_wp_filesystem();
		global $wp_filesystem;
		if ( ! is_null( $wp_filesystem ) ) {
			$wp_filesystem->chmod( $file, $mode );
		}
	}

	private static function setup_wp_filesystem() {
		new FrmCreateFile( array( 'file_name' => '' ) );
	}

	/**
	 * @return string
	 */
	private static function file_protocol() {
		return get_option( 'permalink_structure' ) ? '/frm_file/' : '?frm_file=';
	}

	/**
	 * Attempt to get a protected file from a /frm_file/ url
	 *
	 * @param string $payload
	 * @return array
	 */
	private static function get_download_filepath( $payload ) {
		/**
		 *  $decoded needs to match id:|filename:|size: pattern (size is optional though)
		 *  if it does not, return without a code (to ignore the request, just in case there is another /frm_file/ implementation on their website)
		*/
		$decoded  = base64_decode( $payload );
		$split    = preg_split( '/[:|]+/', $decoded );
		$count    = count( $split );
		$home_url = home_url();

		if ( ! in_array( $count, array( 4, 6 ), true ) ) {
			return array( 'message' => __( 'payload is not the right size', 'formidable-pro' ) );
		}

		if ( $split[0] !== 'id' || $split[2] !== 'filename' ) {
			return array( 'message' => __( 'payload does not match the expected format', 'formidable-pro' ) );
		}

		$file_id  = absint( $split[1] );
		$filename = $split[3];

		if ( empty( $file_id ) || empty( $filename ) ) {
			return array(
				'code'    => 403,
				'message' => __( 'if sanitized data is empty, do not try to download', 'formidable-pro' ),
			);
		}

		$is_temporary = self::file_is_temporary( $file_id );
		if ( $is_temporary && ! self::file_is_an_image( $file_id ) && ! self::logged_in_user_can_access_temporary_files() ) {
			return array(
				'code'    => 403,
				'message' => __( 'temporary files can only be accessed by privileged users', 'formidable-pro' ),
			);
		}

		$form_id             = self::get_form_id_from_file_id( $file_id );
		$folder_is_protected = self::folder_is_protected( $form_id );
		$require_login       = $folder_is_protected;

		/**
		 * Allows updating a flag for doing referer check.
		 *
		 * @since 5.5
		 *
		 * @param bool $check_referer
		 */
		$check_referer = apply_filters( 'frm_check_file_referer', true );
		// only do the referer checks if the user is a guest
		if ( $check_referer ) {
			if ( $require_login && ! is_user_logged_in() ) {
				$referer = FrmAppHelper::get_server_value( 'HTTP_REFERER' );

				if ( ! $referer ) {
					return array(
						'code'    => 403,
						'message' => __( 'referer value either does not exist or it is unusable', 'formidable-pro' ),
					);
				}

				$referer = wp_parse_url( $referer );
				$home    = wp_parse_url( $home_url );

				if ( $referer['host'] !== $home['host'] ) {
					return array(
						'code'    => 403,
						'message' => __( 'referer check failed', 'formidable-pro' ),
					);
				}
			}
		}

		$is_image = wp_attachment_is_image( $file_id );

		if ( $is_image ) {
			if ( $count === 6 && $split[4] !== 'size' ) {
				return array( 'message' => __( 'payload does not match the expected format', 'formidable-pro' ) );
			}

			$size = $count === 6 ? $split[5] : 'full';
		}

		$get_file_url_args = array();
		if ( 4 === $count ) {
			$get_file_url_args['leave_size_out_of_payload'] = true;
		}

		$expected_url         = self::get_file_url( $file_id, $is_image && $count === 6 ? $split[5] : false, $get_file_url_args );
		$expected_request_uri = str_replace( $home_url, '', $expected_url );

		if ( self::file_protocol() . $payload !== $expected_request_uri ) {
			// prevent urls like /something/frm_file/ from triggering a download
			// in this case, leave out the code so the url just continues gracefully
			return array( 'message'  => __( 'url is not an exact match', 'formidable-pro' ) );
		}

		$original_file = get_attached_file( $file_id );

		if ( ! $original_file ) {
			return array(
				'code'    => 404,
				'message' => __( 'no file found', 'formidable-pro' ),
			);
		}

		$original_filename = basename( $original_file );

		if ( $original_filename !== $filename ) {
			return array(
				'code'    => 403,
				'message' => __( 'if the filename requested does not match our filename, do not return the file', 'formidable-pro' ),
			);
		}

		if ( $form_id === -1 ) {
			return array(
				'code'    => 403,
				'message' => __( 'prevent downloads for other uploads. We only want to allow valid connected formidable data', 'formidable-pro' ),
			);
		}

		if ( $folder_is_protected && ! self::user_has_permission( $file_id ) ) {
			return array(
				'code'    => 403,
				'message' => __( 'user does not fit any of the set roles, do not serve a file', 'formidable-pro' ),
			);
		}

		$directory  = dirname( get_attached_file( $file_id ) );
		$final_path = trailingslashit( $directory );

		self::remove_protected_file_filters();

		if ( $is_image ) {
			$split = array_filter( array_map( 'absint', explode( 'x', $size ) ) );
			if ( 2 === count( $split ) ) {
				$size = $split;
			}
			$image_src = wp_get_attachment_image_src( $file_id, $size );
		}

		$final_path .= ! empty( $image_src ) ? basename( reset( $image_src ) ) : $filename;

		if ( ! file_exists( $final_path ) ) {
			$url = apply_filters( 'wp_get_attachment_url', '', $file_id );

			if ( self::file_might_be_on_another_server( $url ) ) {
				wp_redirect( $url );
				exit;
			}

			self::put_back_protected_file_filters();

			$meta_path = self::check_post_meta_for_path( $file_id );
			if ( $meta_path ) {
				$filename   = basename( $meta_path );
				$final_path = $meta_path;
			} else {
				return array(
					'code'    => 404,
					'message' => __( 'file does not exist', 'formidable-pro' ),
				);
			}
		}

		self::put_back_protected_file_filters();

		return array(
			'code'         => 200,
			'name'         => $filename,
			'path'         => $final_path,
			'is_temporary' => $is_temporary,
			'form_id'      => $form_id,
		);
	}

	private static function remove_protected_file_filters() {
		remove_filter( 'wp_get_attachment_url', 'FrmProFileField::filter_attachment_url' );
		remove_filter( 'wp_get_attachment_image_src', 'FrmProFileField::filter_attachment_image_src' );
	}

	private static function put_back_protected_file_filters() {
		add_filter( 'wp_get_attachment_url', 'FrmProFileField::filter_attachment_url', 10, 2 );
		add_filter( 'wp_get_attachment_image_src', 'FrmProFileField::filter_attachment_image_src', 10, 4 );
	}

	/**
	 * It seems that get_attached_file doesn't always get the proper path to the file.
	 * As a fallback, check the post meta for _wp_attached_file, and use that if the file exists.
	 *
	 * @param int $file_id
	 * @return string|false
	 */
	private static function check_post_meta_for_path( $file_id ) {
		// $post_meta is saved like formidable/form_id/filename.extension
		$post_meta = get_post_meta( $file_id, '_wp_attached_file', true );

		if ( ! $post_meta ) {
			return false;
		}

		$meta_path = trailingslashit( wp_upload_dir()['basedir'] ) . $post_meta;
		if ( ! file_exists( $meta_path ) ) {
			return false;
		}

		return $meta_path;
	}

	/**
	 * If the url is not pointing to the uploads dir, it might be on another server (like S3, or Google's Cloud)
	 *
	 * @param string $url
	 * @return bool
	 */
	private static function file_might_be_on_another_server( $url ) {
		return $url && false === strpos( $url, wp_upload_dir()['baseurl'] );
	}

	/**
	 * Check REQUEST_URI (or any uri) for protected file download details
	 *
	 * @param string|bool $uri
	 * @return string|bool false if no payload exists
	 */
	private static function get_file_payload( $uri = false ) {
		if ( ! $uri ) {
			$uri = FrmAppHelper::get_server_value('REQUEST_URI');
		}

		$pattern  = self::file_protocol();
		$position = strpos( $uri, $pattern );

		if ( $position === false ) {
			return false;
		}

		return substr( $uri, $position + strlen( $pattern ) );
	}

	/**
	 * @param string $url
	 *
	 * @since 6.1.2
	 *
	 * @return bool
	 */
	private static function is_formidable_icon( $url ) {
		$images_url = FrmProAppHelper::plugin_url() . '/images/';
		if ( strpos( $url, $images_url ) === false ) {
			return false;
		}

		$file_types = array( 'pdf', 'doc', 'xls' );
		foreach ( $file_types as $file_type ) {
			if ( $url === $images_url . $file_type . '.svg' ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @param string $url
	 * @param int $attachment_id
	 * @return bool
	 */
	private static function should_filter_url( $url, $attachment_id ) {
		if ( self::is_formidable_icon( $url ) ) {
			return false;
		}

		$form_id = self::get_form_id_from_file_id( $attachment_id );
		if ( $form_id === -1 ) {
			// do not touch non-formidable urls
			return false;
		}

		if ( self::url_is_already_protected( $url ) ) {
			return false;
		}

		return true;
	}

	/**
	 * @param string $url
	 * @return bool
	 */
	private static function url_is_already_protected( $url ) {
		return strpos( $url, self::file_protocol() ) !== false;
	}

	/**
	 * @param string $url
	 * @param int $attachment_id
	 * @return string
	 */
	public static function filter_attachment_url( $url, $attachment_id ) {
		if ( ! self::should_filter_url( $url, $attachment_id ) ) {
			return $url;
		}
		return self::get_file_url( $attachment_id, false, compact( 'url' ) );
	}

	/**
	 * @param array|false $image
	 * @param int $attachment_id
	 * @param string|int[] $size
	 * @param bool $icon
	 * @return array
	 */
	public static function filter_attachment_image_src( $image, $attachment_id, $size, $icon ) {
		if ( is_array( $image ) && isset( $image[0] ) && self::should_filter_url( $image[0], $attachment_id ) ) {
			if ( ! $icon ) {
				$image[0] = self::get_file_url( $attachment_id, $size, array( 'url' => $image[0] ) );
			} else {
				self::set_formidable_icon( $image, $attachment_id );
			}
		}
		return $image;
	}

	/**
	 * Use a Formidable icon instead of a default WordPress icon for docx, xlsx, and pdf types.
	 *
	 * @since 6.1.2
	 *
	 * @param array $image
	 * @param int $attachment_id
	 * @return void
	 */
	private static function set_formidable_icon( &$image, $attachment_id ) {
		$path      = get_attached_file( $attachment_id );
		$file_type = wp_check_filetype( $path );

		if ( 'pdf' === $file_type['ext'] && false === strpos( $image[0], 'document.png' ) ) {
			// Some servers allow PDF previews. We do not want to place an ICON if the PDF icon is not document.png.
			return;
		}

		$image[0]  = self::get_icon_for_file_type( $file_type['ext'] );
	}

	/**
	 * @param array $attr
	 * @param WP_Post $attachment
	 * @param string|int[] $size
	 * @return array
	 */
	public static function filter_attachment_image_attributes( $attr, $attachment, $size = 'thumbnail' ) {
		if ( self::should_filter_attachment_image_attributes( $attachment->ID ) ) {
			$attr['src'] = self::get_file_url( $attachment->ID, $size );
		}
		return $attr;
	}

	/**
	 * Remove attachment metadata for PDF file it is a formidable file as well.
	 *
	 * @param array $data
	 * @param int   $attachment_id
	 *
	 * @return array $data
	 */
	public static function maybe_turnoff_attachment_meta( $data, $attachment_id ) {
		if ( self::is_formidable_file( $attachment_id ) && get_post_mime_type( $attachment_id ) === 'application/pdf' ) {
			$form_id = self::get_form_id_from_file_id( $attachment_id );
			if ( self::file_is_protected( $attachment_id, $form_id ) ) {
				return array();
			}
		}

		return $data;
	}

	/**
	 * @param int $attachment_id
	 * @return bool
	 */
	private static function should_filter_attachment_image_attributes( $attachment_id ) {
		return self::is_formidable_file( $attachment_id ) && wp_attachment_is_image( $attachment_id );
	}

	/**
	 * Protect generated attachments that get generated for thumbnails and other image sizes
	 *
	 * @param mixed $metadata
	 * @param int $attachment_id
	 * @param string $context
	 * @return array
	 */
	public static function protect_metadata_attachments( $metadata, $attachment_id, $context = 'create' ) {
		if ( 'create' === $context ) {
			self::maybe_protect_metadata_file( $metadata, $attachment_id );
		}
		return $metadata;
	}

	/**
	 * @param array $metadata
	 * @param int $attachment_id
	 * @return void
	 */
	private static function maybe_protect_metadata_file( $metadata, $attachment_id ) {
		$no_file_meta_exists = ! is_array( $metadata ) || ! isset( $metadata['file'] );
		if ( $no_file_meta_exists ) {
			return;
		}

		if ( self::is_formidable_file( $attachment_id ) ) {
			$form_id = self::get_form_id_from_file_id( $attachment_id );
		} else {
			$form_id = self::get_request_form_id();
			if ( ! $form_id || ! self::path_matches_form( $form_id, $metadata['file'] ) ) {
				return;
			}
		}

		if ( ! self::file_is_protected( $attachment_id, $form_id ) ) {
			return;
		}

		$upload_directory = trailingslashit( self::upload_dir_path( $form_id ) );
		foreach ( $metadata['sizes'] as $size_meta ) {
			$path = $upload_directory . $size_meta['file'];
			self::set_file_protection( $path, true );
		}
	}

	/**
	 * @return int 0 if request has no form id
	 */
	private static function get_request_form_id() {
		$form_id = FrmAppHelper::get_post_param( 'form_id', 0, 'absint' );
		if ( ! $form_id ) {
			$form_id = FrmAppHelper::simple_get( 'form', 'absint', 0 );
		}
		return $form_id;
	}

	/**
	 * @param int $form_id
	 * @param string $path
	 * @return bool
	 */
	private static function path_matches_form( $form_id, $path ) {
		$form_directory = self::upload_dir_path( $form_id );
		$path           = wp_upload_dir()['basedir'] . '/' . $path;
		return 0 === strpos( $path, $form_directory );
	}

	/**
	 * @deprecated 2.03.08
	 */
	public static function validate( $errors, $field, $values, $args ) {
		_deprecated_function( __FUNCTION__, '2.03.08', 'FrmProFileField::no_js_validate' );

		return self::no_js_validate( $errors, $field, $values, $args );
	}
}

