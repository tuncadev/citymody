<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

class FrmProXMLHelper {

	/**
	 * @var bool $legacy_import_format Used for legacy format. Set to true when uploading a file with -legacy at the end of the name.
	 */
	private static $legacy_import_format = false;

	/**
	 * @param array<stdClass> $entries
	 * @param array           $imported
	 * @return array
	 */
	public static function import_xml_entries( $entries, $imported ) {
		global $frm_duplicate_ids;

		$saved_entries   = array();
		$track_child_ids = array();

		// Import all child entries first
		self::put_child_entries_first( $entries );

		foreach ( $entries as $item ) {
			$entry = array(
				'id'             => (int) $item->id,
				'item_key'       => (string) $item->item_key,
				'name'           => (string) $item->name,
				'description'    => FrmAppHelper::maybe_json_decode( (string) $item->description ),
				'ip'             => (string) $item->ip,
				'form_id'        => ( isset( $imported['forms'][ (int) $item->form_id ] ) ? $imported['forms'][ (int) $item->form_id ] : (int) $item->form_id ),
				'post_id'        => ( isset( $imported['posts'][ (int) $item->post_id ] ) ? $imported['posts'][ (int) $item->post_id ] : (int) $item->post_id ),
				'user_id'        => FrmAppHelper::get_user_id_param( (string) $item->user_id ),
				'parent_item_id' => (int) $item->parent_item_id,
				'is_draft'       => (int) $item->is_draft,
				'updated_by'     => FrmAppHelper::get_user_id_param( (string) $item->updated_by ),
				'created_at'     => (string) $item->created_at,
				'updated_at'     => (string) $item->updated_at,
			);

			$metas = array();
			foreach ( $item->item_meta as $meta ) {
				$field_id = (int) $meta->field_id;
				if ( is_array( $frm_duplicate_ids ) && isset( $frm_duplicate_ids[ $field_id ] ) ) {
					$field_id = $frm_duplicate_ids[ $field_id ];
				}
				$field = FrmField::getOne( $field_id );

				if ( ! $field ) {
					continue;
				}

				$metas[ $field_id ] = FrmAppHelper::maybe_json_decode( (string) $meta->meta_value );
				$metas[ $field_id ] = apply_filters( 'frm_import_val', $metas[ $field_id ], $field );

				self::convert_field_values( $field, $field_id, $metas, $saved_entries );

				if ( $field->type === 'user_id' && $metas[ $field_id ] && is_numeric( $metas[ $field_id ] ) ) {
					$entry['frm_user_id'] = $metas[ $field_id ];
				}

				unset( $field, $meta );
			}

			unset( $item );

			$entry['item_meta'] = $metas;
			unset( $metas );

			// Edit entry if the key and created time match.
			$editing = FrmDb::get_var(
				'frm_items',
				array(
					'item_key'   => $entry['item_key'],
					'created_at' => gmdate( 'Y-m-d H:i:s', strtotime( $entry['created_at'] ) ),
				)
			);

			if ( $editing ) {
				FrmEntry::update_entry_from_xml( $entry['id'], $entry );
				$imported['updated']['items']++;
				$saved_entries[ $entry['id'] ] = $entry['id'];
			} else {
				$e = FrmEntry::create_entry_from_xml( $entry );
				if ( $e ) {
					$saved_entries[ $entry['id'] ] = $e;
					$imported['imported']['items']++;
				}
			}

			if ( array_key_exists( $entry['id'], $saved_entries ) ) {
				self::track_imported_child_entries( $saved_entries[ $entry['id'] ], $entry['parent_item_id'], $track_child_ids );
			}

			unset( $entry );
		}

		self::update_parent_item_ids( $track_child_ids, $saved_entries );

		unset( $entries );

		return $imported;
	}

	/**
	 * @param array $entries
	 * @return void
	 */
	private static function put_child_entries_first( &$entries ) {
		$child_entries   = array();
		$regular_entries = array();

		foreach ( $entries as $item ) {
			$parent_item_id = (int) $item->parent_item_id;

			if ( $parent_item_id ) {
				$child_entries[] = $item;
			} else {
				$regular_entries[] = $item;
			}
		}

		$entries = array_merge( $child_entries, $regular_entries );
	}

	/**
	 *
	 * Track imported entries if they have a parent_item_id
	 * Use the old parent_item_id as the array key and set the array value to an array of child IDs
	 *
	 * @param int|boolean $child_id
	 * @param int         $parent_id
	 * @param array       $track_child_ids - pass by reference
	 * @return void
	 */
	private static function track_imported_child_entries( $child_id, $parent_id, &$track_child_ids ) {
		if ( ! $parent_id ) {
			return;
		}

		if ( ! isset( $track_child_ids[ $parent_id ] ) ) {
			$track_child_ids[ $parent_id ] = array();
		}

		$track_child_ids[ $parent_id ][] = $child_id;
	}

	/**
	 *
	 * Update imported child entries so their parent_item_ids match any imported parent entries
	 *
	 * @since 2.0.12
	 *
	 *  @param array $track_child_ids
	 * @param array $saved_entries
	 */
	private static function update_parent_item_ids( $track_child_ids, $saved_entries ) {
		global $wpdb;

		foreach ( $track_child_ids as $old_parent_id => $new_child_ids ) {
			if ( ! isset( $saved_entries[ $old_parent_id ] ) ) {
				continue;
			}

			$new_parent_id = $saved_entries[ $old_parent_id ];
			$new_child_ids = '(' . implode( ',', $new_child_ids ) . ')';

			// This parent entry was imported and the parent_item_id column needs to be updated on all children
			$wpdb->query(
				$wpdb->prepare(
					'UPDATE ' . $wpdb->prefix . 'frm_items SET parent_item_id = %d WHERE id IN ' . $new_child_ids,
					$new_parent_id
				)
			);
		}
	}

	public static function import_csv( $path, $form_id, $field_ids, $entry_key = 0, $start_row = 2, $del = ',', $max = 250 ) {
		if ( ! defined( 'WP_IMPORTING' ) ) {
			define( 'WP_IMPORTING', true );
		}

		$form_id = (int) $form_id;
		if ( ! $form_id ) {
			return $start_row;
		}

		// Remove time limit to execute this function
		set_time_limit( 0 );

		$field_ids = array_filter( $field_ids );

		/**
		 * Allows adding fixed meta values.
		 *
		 * Some field types might not appear in the CSV file (Example: Likert field), but we need to set the meta value
		 * for them when importing.
		 *
		 * The return value should be an empty array or an array like this:
		 * array(
		 *     13 => 'meta value', // 13 is the field ID, this field is outside of Repeater or Embed Form.
		 *     '13_10' => 'meta value', // 13 is the field ID, 10 is the Repeater or Embed Form ID which that field is inside.
		 * )
		 *
		 * @since 5.4
		 *
		 * @param array $fixed_meta_values Fixed meta values.
		 * @param array $args              Contains `form_id`.
		 */
		$fixed_meta_values = apply_filters( 'frm_pro_csv_import_fixed_meta_values', array(), compact( 'form_id' ) );

		self::check_csv_filename_for_legacy_format( $path );

		$f = fopen( $path, 'r' );
		if ( $f ) {
			unset( $path );
			$row = 0;
			while ( ( $data = fgetcsv( $f, 100000, $del ) ) !== false ) {
				$row++;
				if ( $start_row > $row ) {
					continue;
				}

				$values = array(
					'form_id'   => $form_id,
					'item_meta' => array(),
				);

				foreach ( $field_ids as $key => $field_id ) {
					self::csv_to_entry_value( $key, $field_id, $data, $values );
					unset( $key, $field_id );
				}

				self::maybe_add_fixed_meta_values( $fixed_meta_values, $values );
				self::convert_db_cols( $values );
				self::convert_timestamps( $values );
				self::save_or_edit_entry( $values );

				unset( $_POST, $values );
				$_POST['form_id'] = $form_id; // $form_id is set from $_POST['form_id'], so set it back again after the unset line above.

				if ( ( $row - $start_row ) >= $max ) {
					fclose( $f );
					return $row;
				}
			}

			fclose( $f );
			return $row;
		}
	}

	/**
	 * Maybe add fixed meta values to entry meta.
	 *
	 * @since 5.4.1
	 *
	 * @param array $fixed_meta_values Fixed meta values.
	 * @param array $values            Entry data.
	 */
	private static function maybe_add_fixed_meta_values( $fixed_meta_values, &$values ) {
		if ( ! $fixed_meta_values ) {
			return;
		}

		foreach ( $fixed_meta_values as $fixed_meta_field => $fixed_meta_value ) {
			if ( is_numeric( $fixed_meta_field ) ) {
				$values['item_meta'][ $fixed_meta_field ] = $fixed_meta_value;
				continue;
			}

			list( $field_id, $section_id ) = explode( '_', $fixed_meta_field );

			foreach ( $values['item_meta'] as $meta_field => $meta_value ) {
				if ( intval( $meta_field ) !== intval( $section_id ) || ! is_array( $meta_value ) ) {
					continue;
				}

				foreach ( $meta_value as $key => $value ) {
					if ( ! is_numeric( $key ) || ! is_array( $value ) ) {
						continue;
					}
					$values['item_meta'][ $meta_field ][ $key ][ $field_id ] = $fixed_meta_value;
				}
			}
		}
	}

	/**
	 * Before support for importing into a repeater with multiple rows, it was possible to import csv values from a single row.
	 * To support this previous format backwards, a file uploaded with -legacy in the filename will import differently.
	 *
	 * @param string $path the path we're importing. If it includes -legacy, the flag will be set to true.
	 */
	private static function check_csv_filename_for_legacy_format( $path ) {
		self::$legacy_import_format = false !== strpos( basename( $path ), '-legacy' );
	}

	private static function csv_to_entry_value( $key, $field_id, $data, &$values ) {
		$data[ $key ] = isset( $data[ $key ] ) ? $data[ $key ] : '';

		if ( is_numeric( $field_id ) ) {
			self::set_values_for_fields( $key, $field_id, $data, $values );
			return;
		}

		if ( is_array( $field_id ) ) {
			self::set_values_for_data_fields( $key, $field_id, $data, $values );
			return;
		}

		// If this has format `{field_id_number}_{subfield_name}`, this is the combo subfield.
		$check_combo = self::check_combo_field_export_col( $field_id );
		if ( $check_combo ) {
			self::set_values_for_combo_fields( $data[ $key ], $check_combo[0], $check_combo[1], $values );
			return;
		}

		$values[ $field_id ] = $data[ $key ];
	}

	/**
	 * Checks if the given column id is a column of combo field.
	 *
	 * @since 4.10.02
	 *
	 * @param string $col_id Columm ID.
	 * @return array|false Return array with first item is the field ID and second item is subfield name.
	 */
	private static function check_combo_field_export_col( $col_id ) {
		if ( ! is_string( $col_id ) ) {
			return false;
		}

		$sep   = '_';
		$parts = explode( $sep, $col_id );
		if ( 2 > count( $parts ) ) {
			return false;
		}

		if ( empty( $parts[0] ) || empty( $parts[1] ) || ! is_numeric( $parts[0] ) ) {
			return false;
		}

		$field_id = array_shift( $parts );

		return array( $field_id, implode( $sep, $parts ) );
	}

	/**
	 * Called by self::csv_to_entry_value
	 */
	private static function set_values_for_fields( $key, $field_id, $data, &$values ) {
		$field = self::get_field( $field_id );

		/**
		 * Allows modifying field to be imported.
		 *
		 * @since 5.4
		 *
		 * @param object $field Field object.
		 * @param array  $args  Contains `field_id`.
		 */
		$field = apply_filters( 'frm_pro_get_field_for_import', $field, compact( 'field_id' ) );

		$section_id = self::check_field_for_section_id( $field );

		$values['item_meta'][ $field_id ] = apply_filters( 'frm_import_val', $data[ $key ], $field );
		self::convert_field_values( $field, $field_id, $values['item_meta'] );
		$value = $values['item_meta'][ $field_id ];

		if ( $field->type === 'user_id' ) {
			$_POST['frm_user_id']  = $value;
			$values['frm_user_id'] = $value;
		}

		if ( $section_id ) {
			self::set_section_field_value( $section_id, $field->form_id, $field_id, $value, $values );
			return;
		}

		$is_array_type = $field->type === 'checkbox' || ( $field->type === 'data' && $field->field_options['data_type'] !== 'checkbox' );
		if ( $value && $is_array_type && ! empty( $_POST['item_meta'][ $field_id ] ) ) {
			$value = array_merge( (array) $_POST['item_meta'][ $field_id ], (array) $value );
		}

		$values['item_meta'][ $field_id ] = $value;
		$_POST['item_meta'][ $field_id ]  = $value;
	}

	/**
	 * Sets values for combo fields.
	 *
	 * @since 4.11.0
	 *
	 * @param string $value          Value get from CSV.
	 * @param int    $field_id       Field ID.
	 * @param string $sub_field_name Subfield name.
	 * @param array  $values         Import values.
	 */
	private static function set_values_for_combo_fields( $value, $field_id, $sub_field_name, &$values ) {
		$field      = self::get_field( $field_id );
		$section_id = self::check_field_for_section_id( $field );

		if ( ! $section_id ) {
			if ( ! isset( $values['item_meta'][ $field_id ] ) || ! is_array( $values['item_meta'][ $field_id ] ) ) {
				$values['item_meta'][ $field_id ] = array();
			}

			$values['item_meta'][ $field_id ][ $sub_field_name ] = $value;
			$_POST['item_meta'][ $field_id ]                     = $values['item_meta'][ $field_id ];
			return;
		}

		if ( ! isset( $values['item_meta'][ $section_id ] ) ) {
			$values['item_meta'][ $section_id ] = array( 'form' => $field->form_id );
			$index                              = 0;
		} else {
			$index = count( $values['item_meta'][ $section_id ] ) - 2; // Because of 'form' element.
		}

		if ( ! isset( $values['item_meta'][ $section_id ][ $index ][ $field_id ][ $sub_field_name ] ) ) {
			$values['item_meta'][ $section_id ][ $index ][ $field_id ][ $sub_field_name ] = $value;
		} else {
			$index++;
			$values['item_meta'][ $section_id ][ $index ]                                 = array();
			$values['item_meta'][ $section_id ][ $index ][ $field_id ]                    = array();
			$values['item_meta'][ $section_id ][ $index ][ $field_id ][ $sub_field_name ] = $value;
		}

		$_POST['item_meta'][ $section_id ] = $values['item_meta'][ $section_id ];
	}

	/**
	 * Gets field for import.
	 *
	 * @since 5.4 This method is public.
	 *
	 * @param int $field_id Field ID.
	 * @return object|null|false
	 */
	public static function get_field( $field_id ) {
		global $importing_fields;

		if ( ! $importing_fields ) {
			$importing_fields = array();
		}

		if ( isset( $importing_fields[ $field_id ] ) ) {
			$field = $importing_fields[ $field_id ];
		} else {
			$field                         = FrmField::getOne( $field_id );
			$importing_fields[ $field_id ] = $field;
		}

		return $field;
	}

	/**
	 * @param object $field
	 * @return int|false
	 */
	private static function check_field_for_section_id( $field ) {
		if ( self::is_the_child_of_a_repeater( $field ) ) {
			return $field->field_options['in_section'];
		}

		if ( self::field_is_embedded( $field ) ) {
			return self::get_section_id_from_form_fields( $_POST['form_id'], $field->form_id );
		}

		return false;
	}

	/**
	 * @param object $field
	 * @return bool
	 */
	private static function field_is_embedded( $field ) {
		return isset( $_POST['form_id'] ) && $field->form_id !== $_POST['form_id'];
	}

	private static function get_section_id_from_form_fields( $parent_form_id, $embedded_form_id ) {
		$fields = FrmField::get_all_types_in_form( $parent_form_id, 'form' );
		foreach ( $fields as $parent_form_field ) {
			if ( ! empty( $parent_form_field->field_options['form_select'] ) && (int) $parent_form_field->field_options['form_select'] === (int) $embedded_form_id ) {
				return $parent_form_field->id;
			}
		}
		return false;
	}

	/**
	 * @param object $field
	 * @return bool
	 */
	private static function is_the_child_of_a_repeater( $field ) {
		$form_id = isset( $_POST['form_id'] ) ? $_POST['form_id'] : false;

		if ( $field->form_id === $form_id || empty( $field->field_options['in_section'] ) ) {
			return false;
		}

		$section_id = $field->field_options['in_section'];
		$section    = self::get_field( $section_id );

		if ( ! $section ) {
			return false;
		}

		return FrmField::is_repeating_field( $section );
	}

	/**
	 * Update section data when importing, for populating repeater fields.
	 *
	 * @param int   $section_id
	 * @param int   $form_id
	 * @param int   $field_id
	 * @param mixed $value
	 * @param array $values
	 */
	private static function set_section_field_value( $section_id, $form_id, $field_id, $value, &$values ) {
		if ( ! empty( self::$legacy_import_format ) ) {
			$section_data = self::get_new_section_data_for_legacy_format( $section_id, $form_id, $field_id, $value );
		} else {
			$section_data = self::get_new_section_data_for_multiple_row_format( $section_id, $form_id, $field_id, $value );
		}
		$values['item_meta'][ $section_id ] = $section_data;
		$_POST['item_meta'][ $section_id ]  = $section_data;
		unset( $values['item_meta'][ $field_id ] );
	}

	/**
	 * Get the new section data for legacy single-row format.
	 *
	 * @param int   $section_id
	 * @param int   $form_id
	 * @param int   $field_id
	 * @param mixed $value
	 */
	private static function get_new_section_data_for_legacy_format( $section_id, $form_id, $field_id, $value ) {
		$value = array_map( 'trim', explode( ',', $value ) );
		foreach ( $value as $index => $current ) {
			if ( isset( $_POST['item_meta'][ $section_id ] ) ) {
				$section_data = $_POST['item_meta'][ $section_id ];
			} else {
				$section_data = array( 'form' => $form_id );
			}

			foreach ( $value as $index => $current ) {
				if ( ! isset( $section_data[ $index ] ) ) {
					$section_data[ $index ] = array();
				}
				$section_data[ $index ][ $field_id ] = $current;
			}
		}
		return $section_data;
	}

	/**
	 * Get the new section data for current multiple row format.
	 *
	 * @param int   $section_id
	 * @param int   $form_id
	 * @param int   $field_id
	 * @param mixed $value
	 */
	private static function get_new_section_data_for_multiple_row_format( $section_id, $form_id, $field_id, $value ) {
		if ( ! isset( $_POST['item_meta'][ $section_id ] ) ) {
			return array(
				'form' => $form_id,
				array( $field_id => $value ),
			);
		}

		$section_data = $_POST['item_meta'][ $section_id ];
		$index        = 0;
		while ( true ) {
			if ( ! array_key_exists( $index, $section_data ) ) {
				$section_data[ $index ] = array();
				break;
			}

			if ( ! array_key_exists( $field_id, $section_data[ $index ] ) ) {
				break;
			}

			$index ++;
		}

		$section_data[ $index ][ $field_id ] = $value;
		return $section_data;
	}

	/**
	 * Called by self::csv_to_entry_value
	 */
	private static function set_values_for_data_fields( $key, $field_id, $data, &$values ) {
		$field_type = isset( $field_id['type'] ) ? $field_id['type'] : false;

		if ( $field_type !== 'data' ) {
			return;
		}

		$linked   = isset( $field_id['linked'] ) ? $field_id['linked'] : false;
		$field_id = $field_id['field_id'];

		if ( $linked ) {
			$entry_id = FrmDb::get_var(
				'frm_item_metas',
				array(
					'meta_value' => $data[ $key ],
					'field_id'   => $linked,
				),
				'item_id'
			);
		} else {
			//get entry id of entry with item_key == $data[$key]
			$entry_id = FrmDb::get_var( 'frm_items', array( 'item_key' => $data[ $key ] ) );
		}

		if ( $entry_id ) {
			$values['item_meta'][ $field_id ] = $entry_id;
		}
	}

	private static function convert_field_values( $field, $field_id, &$metas, $saved_entries = array() ) {
		$field_obj          = FrmFieldFactory::get_field_object( $field );
		$metas[ $field_id ] = $field_obj->get_import_value( $metas[ $field_id ], array( 'ids' => $saved_entries ) );
	}

	/**
	 * Convert timestamps to the database format
	 */
	private static function convert_timestamps( &$values ) {
		$offset = get_option( 'gmt_offset' ) * 60 * 60;

		$frmpro_settings = FrmProAppHelper::get_settings();
		foreach ( array( 'created_at', 'updated_at' ) as $stamp ) {
			if ( ! isset( $values[ $stamp ] ) ) {
				continue;
			}

			// adjust the date format if it starts with the day
			if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}/', trim( $values[ $stamp ] ) ) && substr( $frmpro_settings->date_format, 0, 1 ) === 'd' ) {
				$reg_ex = str_replace(
					array( '/', '.', '-', 'd', 'j', 'm', 'y', 'Y' ),
					array( '\/', '\.', '\-', '\d{2}', '\d', '\d{2}', '\d{2}', '\d{4}' ),
					$frmpro_settings->date_format
				);

				if ( preg_match( '/^' . $reg_ex . '/', trim( $values[ $stamp ] ) ) ) {
					$values[ $stamp ] = FrmProAppHelper::convert_date( $values[ $stamp ], $frmpro_settings->date_format, 'Y-m-d H:i:s' );
				}
			}

			$values[ $stamp ] = gmdate( 'Y-m-d H:i:s', ( strtotime( $values[ $stamp ] ) - $offset ) );

			unset( $stamp );
		}
	}

	/**
	 * Make sure values are in the format they should be saved in
	 */
	private static function convert_db_cols( &$values ) {
		if ( empty( $values['item_key'] ) ) {
			global $wpdb;
			$values['item_key'] = FrmAppHelper::get_unique_key( '', $wpdb->prefix . 'frm_items', 'item_key' );
		}

		if ( isset( $values['user_id'] ) ) {
			$values['user_id'] = FrmAppHelper::get_user_id_param( $values['user_id'] );
		}

		if ( isset( $values['updated_by'] ) ) {
			$values['updated_by'] = FrmAppHelper::get_user_id_param( $values['updated_by'] );
		}

		if ( isset( $values['is_draft'] ) ) {
			$values['is_draft'] = (int) $values['is_draft'];
		}

		if ( isset( $values['ip'] ) ) {
			$values['ip'] = sanitize_text_field( $values['ip'] );
		}
	}

	/**
	 * Save the entry after checking if it should be created or updated
	 */
	private static function save_or_edit_entry( $values ) {
		$editing = self::get_entry_to_edit( $values );
		if ( $editing ) {
			FrmEntry::update( $editing, $values );
		} else {
			FrmEntry::create( $values );
		}
	}

	/**
	 * Editing CSV entries on import based on id or key
	 *
	 * @since 3.01.03
	 *
	 * @param array $values
	 * @return int
	 */
	private static function get_entry_to_edit( $values ) {
		$entry_id = 0;
		$query    = array();

		if ( ! empty( $values['id'] ) ) {
			$query['id'] = $values['id'];
		}

		if ( ! empty( $values['item_key'] ) ) {
			$query['item_key'] = $values['item_key'];
		}

		if ( $query ) {
			if ( count( $query ) === 2 ) {
				$query = array_merge( array( 'or' => 1 ), $query );
			}

			$query    = array(
				'form_id' => $values['form_id'],
				$query,
			);
			$entry_id = FrmDb::get_var( 'frm_items', $query );
		}

		/**
		 * When importing entries via CSV set the id of the entry that should be edited
		 *
		 * @since 3.01.03
		 * @param int $entry_id - The ID of the entry to edit. 0 means a new entry will be created.
		 * @param array $values - The mapped values for this entry
		 */
		return (int) apply_filters( 'frm_editing_entry_by_csv', absint( $entry_id ), $values );
	}

	/**
	 * Converted an imported XML value to an array
	 *
	 * @since 2.03.08
	 *
	 * @param mixed $imported_value
	 *
	 * @return mixed
	 */
	public static function convert_imported_value_to_array( $imported_value ) {
		if ( is_string( $imported_value ) && strpos( $imported_value, ',' ) !== false ) {
			FrmProAppHelper::unserialize_or_decode( $imported_value );

			if ( ! is_array( $imported_value ) ) {
				$imported_value = explode( ',', $imported_value );
			}
		} else {
			$imported_value = (array) $imported_value;
		}

		return $imported_value;
	}

	/**
	 * Update field settings before it's saved.
	 *
	 * @since 4.0
	 *
	 * @param array $field
	 * @return array
	 */
	public static function run_field_migrations( $field ) {
		$update = self::migrate_dyn_default_value( $field['type'], $field['field_options'] );
		foreach ( $update as $k => $v ) {
			$field[ $k ] = $v;
		}

		self::migrate_lookup_checkbox_setting( $field['field_options'] );
		self::migrate_lookup_placeholder( $field['field_options'] );

		return $field;
	}

	/**
	 * @since 4.0
	 * @param array $field_options
	 * @return void
	 */
	public static function migrate_lookup_placeholder( &$field_options ) {
		if ( empty( $field_options['lookup_placeholder_text'] ) ) {
			return;
		}

		if ( isset( $field_options['placeholder'] ) && ! empty( $field_options['placeholder'] ) ) {
			// Don't overwrite an existing value.
			return;
		}

		$field_options['placeholder'] = $field_options['lookup_placeholder_text'];
		unset( $field_options['lookup_placeholder_text'] );
	}

	/**
	 * @since 4.0
	 * @return void
	 */
	public static function migrate_lookup_checkbox_setting( &$field_options ) {
		$is_not_selected = empty( $field_options['get_values_field'] );
		$is_on           = ! isset( $field_options['autopopulate_value'] ) || ! empty( $field_options['autopopulate_value'] );

		if ( $is_not_selected || $is_on ) {
			return;
		}

		// Remove the unused settings used when a field selected, but autopopulate is off.
		$field_options['get_values_field'] = '';
		$field_options['get_values_form']  = '';
		unset( $field_options['autopopulate_value'] );
	}

	/**
	 * @since 4.0
	 *
	 * @param string $type
	 * @param array  $field_options
	 * @return array
	 */
	public static function migrate_dyn_default_value( $type, $field_options ) {
		$field_types = array( 'file', 'range', 'scale', 'star', 'time', 'toggle', 'user_id' );
		$has_default = isset( $field_options['dyn_default_value'] ) && ! empty( $field_options['dyn_default_value'] );
		if ( ! in_array( $type, $field_types, true ) || ! $has_default ) {
			return array();
		}

		$default_value                      = $field_options['dyn_default_value'];
		$field_options['dyn_default_value'] = '';
		return compact( 'field_options', 'default_value' );
	}

	/**
	 * Perform an action after a field is imported
	 *
	 * @since 2.0.25
	 *
	 * @param array $field_array
	 * @param int $field_id
	 * @return void
	 */
	public static function after_field_is_imported( $field_array, $field_id ) {
		self::add_in_section_value_to_repeating_fields( $field_array, $field_id );
		self::update_page_titles( $field_array, $field_id );
	}

	/**
	 * Update page title indexes after import
	 *
	 * @since 2.03.06
	 *
	 * @param array $field_array
	 * @param int|string $new_id
	 * @return void
	 */
	private static function update_page_titles( $field_array, $new_id ) {
		if ( $field_array['type'] === 'break' ) {
			$form   = FrmForm::getOne( $field_array['form_id'] );
			$old_id = $field_array['id'];
			if ( isset( $form->options['rootline_titles'][ $old_id ] ) ) {
				$form->options['rootline_titles'][ $new_id ] = $form->options['rootline_titles'][ $old_id ];
				unset( $form->options['rootline_titles'][ $old_id ] );
				FrmForm::update( $form->id, array( 'options' => $form->options ) );
			}
		}
	}

	/**
	 * Add the in_section value to fields in a repeating section
	 *
	 * @since 2.0.25
	 * @param array $f
	 * @param int $section_id
	 * @return void
	 */
	private static function add_in_section_value_to_repeating_fields( $f, $section_id ) {
		if ( $f['type'] === 'divider'
			&& FrmField::is_option_true( $f['field_options'], 'repeat' )
			&& FrmField::is_option_true( $f['field_options'], 'form_select' )
		) {
			$new_form_id  = $f['field_options']['form_select'];
			$child_fields = FrmDb::get_col( 'frm_fields', array( 'form_id' => $new_form_id ), 'id' );

			if ( ! $child_fields ) {
				return;
			}

			self::add_in_section_value_to_field_ids( $child_fields, $section_id );
		}
	}

	/**
	 * Add specific in_section value to an array of field IDs
	 *
	 * @since 2.0.25
	 * @param array $field_ids
	 * @param int $section_id
	 * @return void
	 */
	public static function add_in_section_value_to_field_ids( $field_ids, $section_id ) {
		foreach ( $field_ids as $child_id ) {
			$child_field_options = FrmDb::get_var( 'frm_fields', array( 'id' => $child_id ), 'field_options' );
			FrmProAppHelper::unserialize_or_decode( $child_field_options );
			$child_field_options['in_section'] = $section_id;

			// Update now
			$update_values = array( 'field_options' => $child_field_options );
			FrmField::update( $child_id, $update_values );
		}
	}

	/**
	 * Update the in_section value after all of the fields have been imported
	 * In come cases the in_section value will be lost otherwise
	 * And the repeater data will not properly associate with the repeating field data
	 *
	 * @param int $child_form_id
	 * @param int $parent_form_id
	 * @return void
	 */
	public static function maybe_update_in_section_variables_for_repeater_children( $child_form_id, $parent_form_id ) {
		$child_form_fields = FrmField::get_all_for_form( $child_form_id );
		foreach ( $child_form_fields as $child_field ) {
			if ( $child_field->field_options['in_section'] ) {
				continue;
			}

			if ( ! isset( $dividers ) ) {
				$dividers = FrmField::get_all_types_in_form( $parent_form_id, 'divider' );
			}

			foreach ( $dividers as $divider ) {
				if ( FrmField::is_repeating_field( $divider ) && (int) $divider->field_options['form_select'] === (int) $child_form_id ) {
					$child_field->field_options['in_section'] = $divider->id;
					self::add_in_section_value_to_field_ids( array( $child_field->id ), $divider->id );
					break;
				}
			}
		}
	}

	/**
	 * @deprecated 3.0
	 */
	public static function get_file_id( $value ) {
		_deprecated_function( __FUNCTION__, '3.0', 'FrmProFieldFile->get_file_id' );
		$field_obj = FrmFieldFactory::get_field_type( 'file' );
		return $field_obj->get_file_id( $value );
	}

	/**
	 * @deprecated 3.0
	 */
	public static function get_date( $value ) {
		_deprecated_function( __FUNCTION__, '3.0', 'FrmProFieldFile->get_file_id' );
		$field_obj = FrmFieldFactory::get_field_type( 'file' );
		return $field_obj->get_import_value( $value );
	}

	/**
	 * @deprecated 3.0
	 */
	public static function get_multi_opts( $value, $field ) {
		_deprecated_function( __FUNCTION__, '3.0', 'FrmProFieldFile->get_import_value' );
		$field_obj = FrmFieldFactory::get_field_object( $field );
		return $field_obj->get_import_value( $value );
	}

	/**
	 * @deprecated 2.03.08 This function is still being used in the API add on so it cannot be removed safely.
	 */
	public static function get_dfe_id( $value, $field, $ids = array() ) {
		_deprecated_function( __FUNCTION__, '2.03.08' );
		$field_obj = FrmFieldFactory::get_field_object( $field );
		return $field_obj->get_import_value( $value, compact( 'ids' ) );
	}
}
