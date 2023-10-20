<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

class FrmProEntryMeta {

	/**
	 * @since 2.0.11
	 */
	public static function update_single_field( $atts ) {
		if ( empty( $atts['entry_id'] ) ) {
			return;
		}

		$field = $atts['field_id'];
		FrmField::maybe_get_field( $field );
		if ( ! $field ) {
			return;
		}

		if ( isset( $field->field_options['post_field'] ) && ! empty( $field->field_options['post_field'] ) ) {
			$post_id = FrmDb::get_var( 'frm_items', array( 'id' => $atts['entry_id'] ), 'post_id' );
		} else {
			$post_id = false;
		}

		global $wpdb;
		if ( ! $post_id ) {
			$updated = FrmEntryMeta::update_entry_meta( $atts['entry_id'], $field->id, null, $atts['value'] );

			if ( ! $updated ) {
				$wpdb->query( $wpdb->prepare("DELETE FROM {$wpdb->prefix}frm_item_metas WHERE item_id = %d and field_id = %d", $atts['entry_id'], $field->id ) );
				$updated = FrmEntryMeta::add_entry_meta( $atts['entry_id'], $field->id, '', $atts['value'] );
			}
			wp_cache_delete( $atts['entry_id'], 'frm_entry' );
		} else {
			switch ( $field->field_options['post_field'] ) {
				case 'post_custom':
					$updated = update_post_meta( $post_id, $field->field_options['custom_field'], maybe_serialize( $atts['value'] ) );
					break;
				case 'post_category':
					$taxonomy = ( ! FrmField::is_option_empty( $field, 'taxonomy' ) ) ? $field->field_options['taxonomy'] : 'category';
					$updated = wp_set_post_terms( $post_id, $atts['value'], $taxonomy );
					break;
				default:
					$post = get_post( $post_id, ARRAY_A );
					$post[ $field->field_options['post_field'] ] = maybe_serialize( $atts['value'] );
					$updated = wp_insert_post( $post );
			}
		}

		if ( $updated ) {
			// set updated_at time
			$wpdb->update( $wpdb->prefix . 'frm_items',
				array( 'updated_at' => current_time('mysql', 1), 'updated_by' => get_current_user_id() ),
				array( 'id' => $atts['entry_id'] )
			);
		}

		$atts['field_id'] = $field->id;
		$atts['field'] = $field;

		do_action( 'frm_after_update_field', $atts );
		return $updated;
	}

	public static function validate( $errors, $field, $value, $args ) {
		$field->temp_id = $args['id'];

		// Keep current value for "Other" fields because it is needed for correct validation
		if ( ! $args['other'] ) {
			FrmEntriesHelper::get_posted_value($field, $value, $args);
		}

		if ( $field->type === 'form' || FrmField::is_repeating_field( $field ) ) {
			self::validate_embedded_form( $errors, $field, $args['exclude'] );

			// get any values updated during nested validation
			FrmEntriesHelper::get_posted_value( $field, $value, $args );
		}

		// don't validate if going backwards
		if ( FrmProFormsHelper::going_to_prev($field->form_id) ) {
			return array();
		}

		// clear any existing errors if draft
		if ( FrmProFormsHelper::saving_draft() && isset( $errors[ 'field' . $field->temp_id ] ) ) {
			unset( $errors[ 'field' . $field->temp_id ] );
		}

		// if saving draft, only check confirmation field since the confirmation field value is not saved
		if ( FrmProFormsHelper::saving_draft() ) {

			// Check confirmation field if saving a draft
			self::validate_confirmation_field($errors, $field, $value, $args);

			return $errors;
		}

		self::validate_no_input_fields($errors, $field);

		if ( empty( $args['parent_field_id'] ) && ! isset( $_POST['item_meta'][ $field->id ] ) ) {
			return $errors;
		}

		if ( ( ( $field->type != 'tag' && $value == 0 ) || ( $field->type == 'tag' && $value == '' ) ) && isset( $field->field_options['post_field'] ) && $field->field_options['post_field'] == 'post_category' && $field->required == '1' ) {
			$errors[ 'field' . $field->temp_id ] = self::get_blank_message( $field );
		}

		$field_is_hidden = false;

		// Don't require fields hidden with shortcode fields="25,26,27"
		if ( isset( $errors[ 'field' . $field->temp_id ] ) && self::is_field_hidden_by_shortcode( $field ) ) {
			unset( $errors[ 'field' . $field->temp_id ] );
			$value           = '';
			$field_is_hidden = true;
		}

		// Don't require a conditionally hidden field
		self::clear_errors_and_value_for_conditionally_hidden_field( $field, $errors, $value );

		if ( ! $field_is_hidden ) {
			$field_is_hidden = self::field_is_hidden_by_form_state( $field );
		}

		// make sure the [auto_id] is still unique
		self::validate_auto_id($field, $value);

		// check uniqueness
		self::validate_unique_field( $errors, $field, $value );
		self::set_post_fields( $field, $value, $errors );

		if ( $field_is_hidden || self::has_invisible_errors( $field ) ) {
			$field_is_hidden = true;
		} else {
			self::validate_confirmation_field( $errors, $field, $value, $args );
		}

		FrmEntriesHelper::set_posted_value( $field, $value, $args );

		if ( $field_is_hidden ) {
			unset( $errors[ 'field' . $field->temp_id ] );
		} else {
			$errors = self::check_for_required_field_after_sanitizing( $field, $value, $errors );
		}

		return $errors;
	}

	/**
	 * Check if this form has form state data passed from the exclude_fields/fields shortcode attributes.
	 *
	 * @param stdClass $field
	 * @return bool
	 */
	private static function field_is_hidden_by_form_state( $field ) {
		$exclude_fields = FrmProFormState::get_from_request( 'exclude_fields', array() );
		if ( $exclude_fields ) {
			foreach ( $exclude_fields as $exclude_field ) {
				if ( (int) $exclude_field === (int) $field->id || (string) $exclude_field === $field->field_key ) {
					return true;
				}
			}
		}

		$include_fields = FrmProFormState::get_from_request( 'include_fields', array() );
		if ( $include_fields ) {
			foreach ( $include_fields as $include_field ) {
				if ( (int) $include_field === (int) $field->id || (string) $include_field === $field->field_key ) {
					return false;
				}
			}
			return true;
		}

		return false;
	}

	/**
	 * A text field could be empty but validate the first time (if the string was unsafe).
	 * The same applies for a file field, if a file id was being passed that wasn't allowed.
	 *
	 * @param object $field
	 * @param mixed $value
	 * @param array $errors
	 * @return array errors.
	 */
	private static function check_for_required_field_after_sanitizing( $field, $value, $errors ) {
		if ( ! in_array( $field->type, array( 'file', 'text', 'textarea' ), true ) || $field->required !== '1' || $value !== '' || self::has_invisible_errors( $field ) ) {
			return $errors;
		}

		if ( 'file' === $field->type && ! self::file_field_uses_dropzone( $field ) ) {
			// if a file field does not use dropzone (disabled with the frm_load_dropzone hook), do not require here.
			return $errors;
		}

		$errors[ 'field' . $field->temp_id ] = self::get_blank_message( $field );
		self::clear_errors_and_value_for_conditionally_hidden_field( $field, $errors, $value );
		return $errors;
	}

	/**
	 * @param object $field
	 * @return bool false if the field is included in the list of file_fields that are set when dropzone is disabled.
	 */
	private static function file_field_uses_dropzone( $field ) {
		global $frm_vars;
		if ( empty( $frm_vars['file_fields'] ) ) {
			return true;
		}
		return empty( $frm_vars['file_fields'][ $field->id ] ) && empty( $frm_vars['file_fields'][ $field->temp_id ] );
	}

	/**
	 * Get the blank message for a required field. If it isn't set, use the string from settings.
	 *
	 * @param object $field
	 * @return string
	 */
	private static function get_blank_message( $field ) {
		if ( empty( $field->field_options['blank'] ) || 'Untitled cannot be blank' === $field->field_options['blank'] ) {
			$frm_settings = FrmAppHelper::get_settings();
			return $frm_settings->blank_msg;
		}
		return $field->field_options['blank'];
	}

	public static function validate_embedded_form( &$errors, $field, $exclude = array() ) {
		// Check if this section is conditionally hidden before validating the nested fields
		self::validate_no_input_fields( $errors, $field );

		$subforms = array();
		FrmProFieldsHelper::get_subform_ids($subforms, $field);

		if ( empty($subforms) ) {
			return;
		}

		$where = array( 'fi.form_id' => $subforms );
		if ( ! empty( $exclude ) ) {
			$where['fi.type not'] = $exclude;
		}

		$subfields = FrmField::getAll($where, 'field_order');
		unset($where);

		self::validate_subfields( $errors, $field, $subfields, $subforms );

		self::maybe_trim_excess_rows( $field );
	}

	private static function validate_subfields( &$errors, $field, $subfields, $subforms ) {
		$repeat_limit = absint( FrmField::get_option_in_object( $field, 'repeat_limit' ) );

		foreach ( $subfields as $subfield ) {
			if ( isset( $_POST['item_meta'][ $field->id ] ) && self::has_at_least_a_row_submitted( $_POST['item_meta'][ $field->id ] ) ) {
				// The value of the hidden input that represents which subform is contained within this section can be changed by the
				// user to something nasty & that will affect our validation of the subfields & error display, so reset it to be sure:
				$_POST['item_meta'][ $field->id ]['form'] = $subforms[0];

				$posted_values = $_POST['item_meta'][ $field->id ];

				$row_count = 0;
				foreach ( $posted_values as $k => $values ) {
					if ( ! empty( $k ) && in_array( $k, array( 'form', 'row_ids' ) ) ) {
						continue;
					}

					++$row_count;
					if ( $repeat_limit && $row_count > $repeat_limit ) {
						break;
					}

					$subfield->temp_id = $subfield->id . '-' . $field->id . '-' . $k;

					FrmEntryValidate::validate_field(
						$subfield,
						$errors,
						( isset( $values[ $subfield->id ] ) ? $values[ $subfield->id ] : '' ),
						array(
							'parent_field_id'  => $field->id,
							'key_pointer'   => $k,
							'id'            => $subfield->temp_id,
						)
					);

					unset($k, $values);
				}
			} else {
				// All rows or the whole section was removed.
				self::validate_no_repeater_rows( $errors, $field, $subforms, $subfield );
			}
		}
	}

	private static function validate_no_repeater_rows( &$errors, $field, $subforms, $subfield ) {
		// Use key_pointer 0 to mimic one submitted row so that we can validate & thus be able to show appropriate
		// errors. Also mimic that hidden input that represents which subform is contained within this section.
		$_POST['item_meta'][ $field->id ]         = array();
		$_POST['item_meta'][ $field->id ]['form'] = $subforms[0];

		FrmEntryValidate::validate_field(
			$subfield,
			$errors,
			'',
			array(
				'parent_field_id' => $field->id,
				'key_pointer'     => 0,
				'id'              => $subfield->id . '-' . $field->id . '-0',
			)
		);
	}

	private static function maybe_trim_excess_rows( $field ) {
		$repeat_limit = absint( FrmField::get_option_in_object( $field, 'repeat_limit' ) );
		if ( $repeat_limit && self::has_at_least_a_row_submitted( $_POST['item_meta'][ $field->id ] ) ) {
			$total_limit = $repeat_limit + 2; // 2 = 'form' + 'row_ids'
			// trim off excess rows
			$_POST['item_meta'][ $field->id ] = array_slice( $_POST['item_meta'][ $field->id ], 0, $total_limit, true );
		}
	}

	/**
	 * @since 4.01
	 *
	 * Checks if a repeater field has at least a row submitted.
	 */
	private static function has_at_least_a_row_submitted( $arr ) {
		if ( ! is_array( $arr ) || empty( $arr ) ) {
			return false;
		}

		$row_keys = array_filter(
			array_keys( $arr ),
			'FrmProEntryMeta::matches_repeater_index_regex'
		);

		return ! empty( $row_keys );
	}

	/**
	 * @since 4.01
	 */
	private static function matches_repeater_index_regex( $key ) {
		return 1 === preg_match( '/^i?\d+$/', $key );
	}

	/**
	 * Remove any errors set on fields with no input
	 * Also set global to indicate whether section is hidden
	 */
	public static function validate_no_input_fields( &$errors, $field ) {
		if ( ! in_array( $field->type, array( 'break', 'html', 'divider', 'end_divider', 'form' ) ) ) {
			return;
		}

		if ( $field->type == 'break' ) {
			global $frm_hidden_break;

			$frm_hidden_break = self::is_individual_field_conditionally_hidden( $field );

		} else if ( $field->type == 'divider' ) {
			global $frm_hidden_divider, $frm_invisible_divider;

			$frm_hidden_divider = self::is_individual_field_conditionally_hidden( $field );
			$frm_invisible_divider = ! FrmProFieldsHelper::is_field_visible_to_user( $field );

		} else if ( $field->type == 'form' ) {
			global $frm_hidden_form;

			if ( self::is_individual_field_conditionally_hidden( $field ) ) {
				$frm_hidden_form = $field->field_options['form_select'];
			} else {
				$frm_hidden_form = false;
			}
		} else if ( $field->type == 'end_divider' ) {
			global $frm_hidden_divider, $frm_invisible_divider;

			$frm_hidden_divider    = false;
			$frm_invisible_divider = false;
		}

		if ( isset( $errors[ 'field' . $field->temp_id ] ) ) {
			unset( $errors[ 'field' . $field->temp_id ] );
		}
	}

	public static function validate_hidden_shortcode_field( &$errors, $field, &$value ) {
		if ( ! isset( $errors[ 'field' . $field->temp_id ] ) ) {
			return;
		}

		// Don't require fields hidden with shortcode fields="25,26,27"
		if ( self::is_field_hidden_by_shortcode( $field ) ) {
			unset( $errors[ 'field' . $field->temp_id ] );
			$value = '';
		}
	}

	/**
	 * @since 2.0.6
	 *
	 * @param stdClass $field
	 * @return bool
	 */
	private static function is_field_hidden_by_shortcode( $field ) {
		return $field->required == '1' && ! FrmProGlobalVarsHelper::get_instance()->field_is_visible( $field );
	}

	/**
	 * Clear a field's errors and value when it is conditionally hidden
	 *
	 * @since 2.03.08
	 *
	 * @param stdClass $field
	 * @param array $errors
	 * @param mixed $value
	 */
	private static function clear_errors_and_value_for_conditionally_hidden_field( $field, &$errors, &$value ) {
		// TODO: prevent additional validation when field is conditionally hidden

		if ( ! isset( $errors[ 'field' . $field->temp_id ] ) && $value === '' ) {
			return;
		}

		if ( self::is_field_conditionally_hidden( $field ) ) {

			if ( self::is_field_on_skipped_page() && $field->type == 'user_id' ) {
				// Leave value alone
			} else {
				$value = '';
			}

			if ( isset( $errors[ 'field' . $field->temp_id ] ) ) {
				unset( $errors[ 'field' . $field->temp_id ] );
			}
		}
	}

	/**
	 * Check if a field is conditionally hidden during validation
	 *
	 * @since 2.03.08
	 *
	 * @param stdClass $field
	 *
	 * @return bool
	 */
	public static function is_field_conditionally_hidden( $field ) {
		return self::is_individual_field_conditionally_hidden( $field )
			   || self::is_field_in_hidden_section( $field )
			   || self::is_field_in_hidden_embedded_form( $field )
			   || self::is_field_on_skipped_page();
	}

	/**
	 * Check if an individual field has logic that is hiding it
	 *
	 * @since 2.03.08
	 *
	 * @param stdClass $field
	 *
	 * @return bool
	 */
	private static function is_individual_field_conditionally_hidden( $field ) {
		return FrmProFieldsHelper::is_field_hidden( $field, wp_unslash( $_POST ) );
	}

	/**
	 * Check if a field is within a conditionally hidden section
	 *
	 * @since 2.03.08
	 *
	 * @param stdClass $field
	 *
	 * @return bool
	 */
	private static function is_field_in_hidden_section( $field ) {
		global $frm_hidden_divider;

		return $frm_hidden_divider;

	}

	/**
	 * Check if an field is in a conditionally hidden embedded form
	 *
	 * @since 2.03.08
	 *
	 * @param stdClass $field
	 *
	 * @return bool
	 */
	private static function is_field_in_hidden_embedded_form( $field ) {
		global $frm_hidden_form;

		return $frm_hidden_form && $frm_hidden_form == $field->form_id;
	}

	/**
	 * Check if a field is on a page skipped with conditional logic
	 *
	 * @since 2.03.08
	 *
	 * @return bool
	 */
	private static function is_field_on_skipped_page() {
		global $frm_hidden_break;

		return $frm_hidden_break;
	}

	/**
	 * Make sure the [auto_id] is still unique
	 */
	public static function validate_auto_id( $field, &$value ) {
		if ( empty( $field->default_value ) || is_array( $field->default_value ) || empty( $value ) || strpos( $field->default_value, '[auto_id' ) === false ) {
			return;
		}

		//make sure we are not editing
		if ( ( $_POST && ! isset($_POST['id']) ) || ! is_numeric($_POST['id']) ) {
			$value = FrmProFieldsHelper::get_default_value($field->default_value, $field);
		}
	}

	/**
	 * Make sure this value is unique
	 */
	public static function validate_unique_field( &$errors, $field, $value ) {
		if ( empty( $value ) || ! FrmField::is_option_true( $field, 'unique' ) ) {
			return;
		}

		$entry_id = self::get_validated_entry_id( $field );
		$field_obj = FrmFieldFactory::get_field_object( $field );
		if ( $field_obj->is_not_unique( $value, $entry_id ) ) {
			$errors[ 'field' . $field->temp_id ] = FrmFieldsHelper::get_error_msg( $field, 'unique_msg' );
		}
	}

	public static function get_validated_entry_id( $field ) {
		$entry_id = ( $_POST && isset($_POST['id']) ) ? absint( $_POST['id'] ) : 0;

		// get the child entry id for embedded or repeated fields
		if ( isset( $field->temp_id ) ) {
			$temp_id_parts = explode( '-i', $field->temp_id );
			if ( isset( $temp_id_parts[1] ) ) {
				$entry_id = $temp_id_parts[1];
			}
		}

		return $entry_id;
	}

	public static function add_field_to_query( $value, &$query ) {
		if ( is_numeric( $value ) ) {
			$query['it.field_id'] = $value;
		} else {
			$query['fi.field_key'] = $value;
		}
	}

	public static function validate_confirmation_field( &$errors, $field, $value, $args ) {
		//Make sure confirmation field matches original field
		if ( ! FrmField::is_option_true( $field, 'conf_field' ) ) {
			return;
		}

		if ( FrmProFormsHelper::saving_draft() ) {
			//Check confirmation field if saving a draft
			$args['action'] = ( $_POST['frm_action'] == 'create' ) ? 'create' : 'update';
			self::validate_check_confirmation_field($errors, $field, $value, $args);
			return;
		}

		$args['action'] = ( $_POST['frm_action'] == 'update' ) ? 'update' : 'create';

		self::validate_check_confirmation_field($errors, $field, $value, $args);
	}

	public static function validate_check_confirmation_field( &$errors, $field, $value, $args ) {
		$conf_val = '';

		// Temporarily swtich $field->id in order to get and set the value posted in confirmation field
		$field_id = $field->id;
		$field->id = 'conf_' . $field_id;
		FrmEntriesHelper::get_posted_value($field, $conf_val, $args);

		// Switch $field->id back to original id
		$field->id = $field_id;
		unset($field_id);

		//If editing entry or if user hits Next/Submit on a draft
		if ( $args['action'] == 'update' ) {
			//If in repeating section
			if ( isset( $args['key_pointer'] ) && ( $args['key_pointer'] || $args['key_pointer'] === 0 ) ) {
				$entry_id = str_replace( 'i', '', $args['key_pointer'] );
			} else {
				$entry_id = ( $_POST && isset($_POST['id']) ) ? $_POST['id'] : false;
			}

			$prev_value = FrmEntryMeta::get_entry_meta_by_field($entry_id, $field->id);

			if ( $prev_value != $value && $conf_val != $value ) {
				$errors[ 'fieldconf_' . $field->temp_id ] = FrmFieldsHelper::get_error_msg( $field, 'conf_msg' );
				$errors[ 'field' . $field->temp_id ] = '';
			}
		} else if ( $args['action'] == 'create' && $conf_val != $value ) {
			//If creating entry
			$errors[ 'fieldconf_' . $field->temp_id ] = FrmFieldsHelper::get_error_msg( $field, 'conf_msg' );
			$errors[ 'field' . $field->temp_id ] = '';
		}
	}

	public static function skip_required_validation( $field ) {
		return FrmProFormsHelper::going_to_prev( $field->form_id )
			|| FrmProFormsHelper::saving_draft()
			|| self::is_field_conditionally_hidden( $field )
			|| self::has_invisible_errors( $field )
			|| self::field_is_hidden_by_form_state( $field );
	}

	/**
	 * Get metas for post or non-post fields
	 *
	 * @return string|array
	 *
	 * @since 2.0
	 */
	public static function get_all_metas_for_field( $field, $args = array() ) {
		global $wpdb;

		$where = array(
			'e.form_id' => $field->form_id,
			'e.is_draft' => 0,
		);

		if ( ! FrmField::is_option_true( $field, 'post_field' ) ) {
			// If field is not a post field
			$get_field = 'em.meta_value';
			$get_table = $wpdb->prefix . 'frm_item_metas em INNER JOIN ' . $wpdb->prefix . 'frm_items e ON (e.id=em.item_id)';
			$where['em.field_id'] = $field->id;

		} elseif ( $field->field_options['post_field'] === 'post_custom' ) {
			// If field is a custom field
			$get_field = 'pm.meta_value';
			$get_table = $wpdb->postmeta . ' pm INNER JOIN ' . $wpdb->prefix . 'frm_items e ON pm.post_id=e.post_id';
			$where['pm.meta_key'] = $field->field_options['custom_field'];

		} elseif ( $field->field_options['post_field'] !== 'post_category' ) {
			// If field is a non-category post field
			$get_field = 'p.' . sanitize_title( $field->field_options['post_field'] );
			$get_table = $wpdb->posts . ' p INNER JOIN ' . $wpdb->prefix . 'frm_items e ON p.ID=e.post_id';

		} else {
			// If field is a category field
			$post_ids = self::get_all_post_ids_for_form( $field->form_id, $args );

			$get_field = 'terms.term_id';
			$get_table = $wpdb->terms . ' AS terms INNER JOIN ' . $wpdb->term_taxonomy . '  AS tt ON tt.term_id = terms.term_id INNER JOIN ' . $wpdb->term_relationships . ' AS tr ON tr.term_taxonomy_id = tt.term_taxonomy_id';
			$where = array( 'tt.taxonomy' => $field->field_options['taxonomy'], 'tr.object_id' => $post_ids );

			if ( $field->field_options['exclude_cat'] ) {
				$where['terms.term_id NOT'] = $field->field_options['exclude_cat'];
			}

			$args = array();
		}

		self::add_to_where_query( $args, $where );
		$query_args = self::setup_args_for_frmdb_query( $args );

		// Get the metas
		$metas = FrmDb::get_col( $get_table, $where, $get_field, $query_args );

		// Maybe unserialize
		foreach ( $metas as $k => $v ) {
			$metas[ $k ] = $v;
			FrmProAppHelper::unserialize_or_decode( $metas[ $k ] );
			unset($k, $v);
		}

		$metas = wp_unslash( $metas );

		return $metas;
	}

	/**
	 * Get all post IDs for form
	 *
	 * @since 2.02.06
	 * @param int $form_id
	 * @param array $args
	 * @return mixed
	 */
	private static function get_all_post_ids_for_form( $form_id, $args ) {
		$where = array(
			'e.form_id' => $form_id,
			'e.is_draft' => 0,
		);

		self::add_to_where_query( $args, $where );

		$query_args = self::setup_args_for_frmdb_query( $args );

		global $wpdb;
		$table = $wpdb->prefix . 'frm_items e';

		return FrmDb::get_col( $table, $where, 'e.post_id', $query_args );
	}

	/**
	 * Get the associative array values for a single field
	 *
	 * @since 2.02.05
	 * @param object $field
	 * @param array $atts
	 * @return array
	 */
	public static function get_associative_array_values_for_field( $field, $atts ) {
		global $wpdb;

		$get_column = 'e.id,';

		$where = self::base_query( $field );
		if ( empty( $where['e.form_id'] ) ) {
			return array();
		}

		if ( ! FrmField::is_option_true( $field, 'post_field' ) ) {
			// If field is not a post field
			$get_column .= 'em.meta_value as meta_value';
			$get_table = $wpdb->prefix . 'frm_item_metas em INNER JOIN ' . $wpdb->prefix . 'frm_items e ON (e.id=em.item_id)';
			$where['em.field_id'] = $field->id;

		} else if ( $field->field_options['post_field'] === 'post_custom' ) {
			// If field is a custom field
			$get_column .= 'pm.meta_value as meta_value';
			$get_table = $wpdb->postmeta . ' pm INNER JOIN ' . $wpdb->prefix . 'frm_items e ON pm.post_id=e.post_id';
			$where['pm.meta_key'] = $field->field_options['custom_field'];

		} else if ( $field->field_options['post_field'] !== 'post_category' ) {
			// If field is a non-category post field
			$get_column .= 'p.' . sanitize_title( $field->field_options['post_field'] ) . ' as meta_value';
			$get_table = $wpdb->posts . ' p INNER JOIN ' . $wpdb->prefix . 'frm_items e ON p.ID=e.post_id';

		} else {
			// If field is a category field
			//TODO: Make this work
			return array();
		}

		// Add filtering attributes
		self::add_to_where_query( $atts, $where );

		return FrmDb::get_associative_array_results( $get_column, $get_table, $where );
	}

	/**
	 * Get the associative array values for a column in the frm_items table
	 *
	 * @since 2.02.05
	 * @param string $column
	 * @param array $atts
	 * @return array
	 */
	public static function get_associative_array_values_for_frm_items_column( $column, $atts ) {
		global $wpdb;

		$columns = 'e.id,e.' . $column . ' as meta_value';
		$table = $wpdb->prefix . 'frm_items e';
		$where = array(
			'e.form_id' => $atts['form_id'],
			'e.is_draft' => 0,
		);

		// Add filtering attributes
		self::add_to_where_query( $atts, $where );

		return FrmDb::get_associative_array_results( $columns, $table, $where );
	}

	/**
	 * Get all entry IDs for a specific field and value
	 *
	 * @since 2.01.0
	 * @param object $field
	 * @param string|array $value
	 * @param array $args
	 * @return array
	 */
	public static function get_entry_ids_for_field_and_value( $field, $value, $args = array() ) {
		global $wpdb;

		$where = self::base_query( $field );
		if ( empty( $where['e.form_id'] ) ) {
			return array();
		}

		$operator = self::get_operator_for_query( $args );

		if ( strpos( $operator, 'LIKE' ) === false && ! empty( $field ) ) {
			$num_query = FrmProAppHelper::maybe_query_as_number( $field->type );
			$operator = $num_query . $operator;
		}

		$get_field = 'em.item_id';
		$get_table = $wpdb->prefix . 'frm_item_metas em INNER JOIN ' . $wpdb->prefix . 'frm_items e ON (e.id=em.item_id)';

		if ( empty( $field ) ) {
			// If the extra meta if being searched ie Comments.
			$where['em.field_id'] = 0;
			$where[ 'em.meta_value' . $operator ] = $value;
		} elseif ( ! FrmField::is_option_true( $field, 'post_field' ) ) {
			// If field is not a post field
			$where['em.field_id'] = $field->id;

			if ( '' === $operator ) {
				$where[] = array(
					'or'                 => 1,
					'em.meta_value'      => $value,
					'em.meta_value LIKE' => ':"' . $value . '"',
				);
			} else {
				$where[ 'em.meta_value' . $operator ] = $value;
			}
		} else if ( $field->field_options['post_field'] == 'post_custom' ) {
			// If field is a custom field
			$get_field = 'e.id';
			$get_table = $wpdb->postmeta . ' pm INNER JOIN ' . $wpdb->prefix . 'frm_items e ON pm.post_id=e.post_id';

			$where['pm.meta_key'] = $field->field_options['custom_field'];
			$where[ 'pm.meta_value' . $operator ] = $value;

		} else if ( $field->field_options['post_field'] != 'post_category' ) {
			// If field is a non-category post field
			$get_field = 'e.id';
			$get_table = $wpdb->prefix . 'frm_items e LEFT OUTER JOIN ' . $wpdb->posts . ' p ON e.post_id=p.ID';

			$where[] = array(
				'or'                 => 1,
				'p.' . sanitize_title( $field->field_options['post_field'] ) . $operator => $value,
				'e.name' . $operator => $value,
			);
		} else {
			// If field is a category field
			//TODO: Make this work
			return array();
		}

		self::add_to_where_query( $args, $where );

		return FrmDb::get_col( $get_table, $where, $get_field );
	}

	/**
	 * Get the base query for the entry search.
	 *
	 * @since 4.04.03
	 *
	 * @return array
	 */
	private static function base_query( $field ) {
		$where = array(
			'e.form_id'  => empty( $field ) ? 0 : $field->form_id,
			'e.is_draft' => 0,
		);

		if ( ! empty( $field ) ) {
			return $where;
		}

		$form_id = FrmAppHelper::get_param( 'form', 0, 'get', 'absint' );
		if ( ! empty( $form_id ) ) {
			$where['e.form_id'] = $form_id;
		}

		return $where;
	}

	/**
	 * Get the operator from the given comparison type
	 *
	 * @since 2.02.05
	 * @param $args
	 * @return string
	 */
	private static function get_operator_for_query( $args ) {
		$operator = '';
		if ( isset( $args['comparison_type'] ) ) {
			if ( 'like' === $args['comparison_type'] ) {
				$operator = ' LIKE';
			} elseif ( '>' === $args['comparison_type'] ) {
				$operator = ' >-';
			} elseif ( '<' === $args['comparison_type'] ) {
				$operator = ' <-';
			} elseif ( '>=' === $args['comparison_type'] ) {
				$operator = '>';
			} elseif ( '<=' === $args['comparison_type'] ) {
				$operator = '<';
			}
		}

		return $operator;
	}

	/**
	 * Add entry_ids, user_id, start_date, end_date, and is_draft arguments to WHERE query
	 *
	 * @param $args
	 * @param $where
	 */
	private static function add_to_where_query( $args, &$where ) {

		// If entry IDs is set
		if ( isset( $args['entry_ids'] ) ) {
			$where['e.id'] = $args['entry_ids'];
		}

		// If user ID is set
		if ( isset( $args['user_id'] ) ) {
			$where['e.user_id'] = $args['user_id'];
		}

		// If start date is set.
		if ( isset( $args['start_date'] ) ) {
			$where['e.created_at >'] = gmdate( 'Y-m-d 00:00:00', strtotime( $args['start_date'] ) );
		}

		// If end date is set.
		if ( isset( $args['end_date'] ) ) {
			$where['e.created_at <'] = gmdate( 'Y-m-d 23:59:59', strtotime( $args['end_date'] ) );
		}

		// If is_draft is set
		if ( isset( $args['is_draft'] ) ) {
			if ( 'both' === $args['is_draft'] ) {
				unset( $where['e.is_draft'] );
			} else {
				$where['e.is_draft'] = $args['is_draft'];
			}
		}
	}

	/**
	 * Convert args to usable query args for FrmDb::get_col function
	 *
	 * @since 2.01.0
	 * @param array $args
	 * @return array
	 */
	private static function setup_args_for_frmdb_query( $args ) {
		$query_args = array();

		if ( isset( $args['limit'] ) ) {
			$query_args['limit'] = $args['limit'];
		}

		if ( isset( $args['order_by'] ) ) {
			$query_args['order_by'] = $args['order_by'];
		}

		return $query_args;
	}

	public static function set_post_fields( $field, $value, &$errors ) {
		$errors = FrmProEntryMetaHelper::set_post_fields($field, $value, $errors);
		return $errors;
	}

	public static function add_post_value_to_entry( $field, &$entry ) {
		if ( $entry->post_id && ( $field->type == 'tag' || ( isset( $field->field_options['post_field'] ) && $field->field_options['post_field'] ) ) ) {
			$p_val = FrmProEntryMetaHelper::get_post_value(
				$entry->post_id,
				$field->field_options['post_field'],
				$field->field_options['custom_field'],
				array(
					'truncate' => ( $field->field_options['post_field'] == 'post_category' ),
					'form_id' => $entry->form_id,
					'field' => $field,
					'type' => $field->type,
					'exclude_cat' => ( isset( $field->field_options['exclude_cat'] ) ? $field->field_options['exclude_cat'] : 0 ),
				)
			);
			if ( $p_val != '' ) {
				$entry->metas[ $field->id ] = $p_val;
			}
		}
	}

	/**
	 * Remove errors for invisible fields and sections
	 * We shouldn't validate admin only fields that can't be seen
	 *
	 * @since 3.0
	 */
	private static function has_invisible_errors( $field ) {
		global $frm_invisible_divider;
		return ( $frm_invisible_divider || ! FrmProFieldsHelper::is_field_visible_to_user( $field ) );
	}

	public static function add_repeating_value_to_entry( $field, &$entry ) {
		// If field is in a repeating section
		if ( $entry->form_id != $field->form_id ) {
			// get entry ids linked through repeat field or embeded form
			$child_entries = FrmProEntry::get_sub_entries( $entry->id, true );
			$val = FrmProEntryMetaHelper::get_sub_meta_values( $child_entries, $field );
			if ( ! empty( $val ) ) {
				//Flatten multi-dimensional arrays
				if ( is_array( $val ) ) {
					$val = FrmAppHelper::array_flatten( $val );
				}
				$entry->metas[ $field->id ] = $val;
			}
		} else {
			$val = '';
			FrmProEntriesHelper::get_dynamic_list_values( $field, $entry, $val );
			$entry->metas[ $field->id ] = $val;
		}
	}
}
