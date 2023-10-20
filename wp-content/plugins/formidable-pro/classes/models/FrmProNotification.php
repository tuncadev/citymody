<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

class FrmProNotification {

	private static $form_is_protected = false;

	public static function add_attachments( $attachments, $form, $args ) {
		self::$form_is_protected = FrmProFileField::get_option( $form->parent_form_id ? $form->parent_form_id : $form->id, 'protect_files', 0 );

		if ( ! empty( $args['settings']['attach_csv'] ) ) {
			$action_id   = isset( $args['action_id'] ) ? $args['action_id'] : 0;
			$attachments = self::add_csv_attachment( $attachments, $form, $args['entry'], $action_id );
		}

		$defaults = array(
			'entry'     => false,
			'email_key' => '',
		);
		$args     = wp_parse_args( $args, $defaults );

		$file_fields = FrmField::get_all_types_in_form( $form->id, 'file', '', 'include' );

		foreach ( $file_fields as $file_field ) {
			$file_options = $file_field->field_options;

			// Only go through code if file is supposed to be attached to email
			if ( empty( $file_options['attach'] ) ) {
				continue;
			}

			$field_value = new FrmProFieldValue( $file_field, $args['entry'] );
			$file_ids    = $field_value->get_saved_value();

			//Only proceed if there is actually an uploaded file
			if ( empty( $file_ids ) ) {
				continue;
			}

			// Get each file in this field
			foreach ( (array) $file_ids as $file_id ) {
				if ( ! $file_id ) {
					continue;
				}

				// For multi-file upload fields in repeating sections
				if ( is_array( $file_id ) ) {
					foreach ( $file_id as $f_id ) {
						// Add attachments
						self::add_to_attachments( $attachments, $f_id );
					}
					continue;
				}

				// Add the attachments now
				self::add_to_attachments( $attachments, $file_id );
			}
		}

		/**
		 * Add email attachment to attachment array.
		 */
		if ( ! isset( $args['settings']['email_attachment_id'] ) ) {
			return $attachments;
		}

		self::add_to_attachments( $attachments, $args['settings']['email_attachment_id'] );

		return $attachments;
	}

	/**
	* Add to email attachments
	*
	* @since 2.0
	* Called by add_attachments in FrmProNotification
	*/
	private static function add_to_attachments( &$attachments, $file_id ) {
		if ( ! $file_id ) {
			return;
		}

		// Get the file
		$file = get_post_meta( $file_id, '_wp_attached_file', true);
		if ( $file ) {
			$uploads = wp_upload_dir();
			$path    = $uploads['basedir'] . '/' . $file;
			if ( self::$form_is_protected ) {
				FrmProFileField::chmod( $path, 0400 );
				add_action(
					'frm_notification',
					function() use ( $path ) {
						FrmProFileField::chmod( $path, 0200 );
					}
				);
			}

			$attachments[] = $path;
		}
	}

	/**
	 * @since 5.0.16
	 *
	 * @param array    $attachments
	 * @param stdClass $form
	 * @param stdClass $entry
	 * @param int      $action_id
	 * @return array
	 */
	private static function add_csv_attachment( $attachments, $form, $entry, $action_id ) {
		if ( ! is_callable( 'FrmXMLController::get_fields_for_csv_export' ) ) {
			return $attachments;
		}

		$unique_filename_filter = function( $filename, $form, $args ) use ( $action_id, $entry ) {
			if ( ! empty( $args['meta'] ) && array_key_exists( 'action_id', $args['meta'] ) && (int) $action_id === (int) $args['meta']['action_id'] ) {
				$split = explode( '.', $filename, 2 );
				if ( 2 === count( $split ) ) {
					$filename = $split[0] . '_' . $entry->item_key . '.' . $split[1];
				}
			}
			return $filename;
		};

		add_filter( 'frm_csv_filename', $unique_filename_filter, 1, 3 );

		$csv_path = FrmCSVExportHelper::generate_csv(
			array(
				'mode'      => 'file',
				'form'      => $form,
				'entry_ids' => array( $entry->id ),
				'form_cols' => FrmXMLController::get_fields_for_csv_export( $form->id, $form ),
				'context'   => 'email',
				'meta'      => compact( 'action_id' ),
			)
		);

		$attachments[] = $csv_path;

		remove_filter( 'frm_csv_filename', $unique_filename_filter, 1 );

		add_action(
			'frm_notification',
			function() use ( $csv_path ) {
				if ( file_exists( $csv_path ) ) {
					unlink( $csv_path );
				}
			}
		);

		return $attachments;
	}

	/**
	 * @deprecated 2.03.04
	 */
	public static function entry_created( $entry_id, $form_id ) {
		$new_function = 'FrmFormActionsController::trigger_actions("create", ' . $form_id . ', ' . $entry_id . ', "email")';
		_deprecated_function( __FUNCTION__, '2.03.04', esc_html( $new_function ) );
		FrmFormActionsController::trigger_actions( 'create', $form_id, $entry_id, 'email' );
	}
}
