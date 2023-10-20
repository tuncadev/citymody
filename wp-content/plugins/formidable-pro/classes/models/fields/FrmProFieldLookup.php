<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

/**
 * @since 3.0
 */
class FrmProFieldLookup extends FrmFieldType {

	/**
	 * @var string
	 * @since 3.0
	 */
	protected $type = 'lookup';

	public function show_on_form_builder( $name = '' ) {
		$field = FrmFieldsHelper::setup_edit_vars( $this->field );
		FrmProLookupFieldsController::show_lookup_field_input_on_form_builder( $field );
	}

	protected function field_settings_for_type() {
		$settings = array(
			'read_only'      => true,
			'default_value'  => true,
			'unique'         => true,
			'clear_on_focus' => true,
		);

		FrmProFieldsHelper::fill_default_field_display( $settings );
		return $settings;
	}

	/**
	 * @since 4.0
	 * @param array $args - Includes 'field', 'display', and 'values'
	 */
	public function show_primary_options( $args ) {
		$field = $args['field'];
		$field_types = FrmProLookupFieldsController::get_lookup_field_data_types();
		include( FrmProAppHelper::plugin_path() . '/classes/views/frmpro-fields/back-end/dynamic-field.php' );

		parent::show_primary_options( $args );
	}

	/**
	 * @since 4.0
	 * @param array $args - Includes 'field', 'display', and 'values'
	 */
	public function show_extra_field_choices( $args ) {
		$field     = $args['field'];
		$data_type = FrmField::get_option( $field, 'data_type' );

		$this->show_get_options( $field );

		if ( $data_type !== 'text' ) {
			// Option Order.
			require( FrmProAppHelper::plugin_path() . '/classes/views/frmpro-fields/back-end/order.php' );

			// Watch Lookup Fields.
			$lookup_fields = FrmProLookupFieldsController::get_lookup_fields_for_watch_row( $field );
			$field['watch_lookup'] = array_filter( $field['watch_lookup'] );
			include( FrmProAppHelper::plugin_path() . '/classes/views/lookup-fields/back-end/watch.php' );
			unset( $lookup_fields );
		}

		// Filter options.
		require( FrmProAppHelper::plugin_path() . '/classes/views/lookup-fields/back-end/filter.php' );

		if ( $data_type === 'select' ) {
			include( FrmProAppHelper::plugin_path() . '/classes/views/frmpro-fields/back-end/multi-select.php' );

			$this->auto_width_setting( $args );
		}
	}

	/**
	 * @since 4.0
	 * @param array $args - Includes 'field', 'display'.
	 */
	public function show_after_default( $args ) {
		$field = $args['field'];
		if ( $field['data_type'] !== 'text' ) {
			return;
		}

		// Field size.
		$display_max = true;
		include( FrmAppHelper::plugin_path() . '/classes/views/frm-fields/back-end/pixels-wide.php' );

		FrmFieldsController::show_format_option( $field );
	}

	/**
	 * Show the 'Get options from' settings above a lookup field's Field Options
	 *
	 * @since 4.0
	 * @param array $field
	 */
	public function show_get_options( $field ) {
		$lookup_args = $this->get_args_for_get_options_setting( $field );
		if ( $field['data_type'] == 'text' ) {
			$opt_label = __( 'Search In', 'formidable-pro' );
		} else {
			$opt_label = __( 'Get Options From', 'formidable-pro' );
		}

		require( FrmProAppHelper::plugin_path() . '/classes/views/lookup-fields/back-end/get-options-from.php' );
	}

	/**
	 * Get the form_list and form_fields for the Get Values From/Get Options From option
	 *
	 * @since 4.0
	 * @param array $field
	 * @return array $lookup_args
	 */
	private function get_args_for_get_options_setting( $field ) {
		$lookup_args = array();

		// Get all forms for the -select form- option
		$lookup_args['form_list'] = FrmForm::get_published_forms();

		if ( isset( $field['get_values_form'] ) && is_numeric( $field['get_values_form'] ) ) {
			$lookup_args['form_fields'] = $this->get_fields_for_get_values_field_dropdown( $field['get_values_form'], $field['type'] );

		} else {
			$lookup_args['form_fields'] = array();
		}

		return $lookup_args;
	}

	/**
	 * Get the fields fot the get_values_field dropdown
	 *
	 * @since 4.0
	 *
	 * @param int $form_id
	 * @param string $field_type
	 * @return array $form_fields
	 */
	public function get_fields_for_get_values_field_dropdown( $form_id, $field_type ) {
		if ( in_array( $field_type, array( 'lookup', 'text', 'hidden' ) ) ) {
			$form_fields = FrmField::get_all_for_form( $form_id );
		} else {
			$where = array( 'type' => $field_type );
			$where[] = array( 'or' => 1, 'fi.form_id' => $form_id, 'fr.parent_form_id' => $form_id );

			$form_fields = FrmField::getAll( $where );
		}

		/**
		 * Allows modifying fields in field selection of Lookup field.
		 *
		 * @since 5.0.04
		 *
		 * @param array $fields The fields.
		 * @param array $args   Includes `form_id`, `field_type`.
		 */
		$form_fields = apply_filters( 'frm_pro_fields_in_lookup_selection', $form_fields, compact( 'form_id', 'field_type' ) );

		return $form_fields;
	}

	public function prepare_front_field( $values, $atts ) {
		FrmProLookupFieldsController::maybe_get_initial_lookup_field_options( $values );
		return $values;
	}

	public function front_field_input( $args, $shortcode_atts ) {
		ob_start();

		FrmProLookupFieldsController::get_front_end_lookup_field_html( $this->field, $args['field_name'], $args['html_id'] );
		$input_html = ob_get_contents();
		ob_end_clean();

		return $input_html;
	}

	/**
	 * Make sure a lookup field has options before marking it required.
	 *
	 * @since 4.01
	 */
	public function validate( $args ) {
		$errors = array();

		$is_required = FrmField::is_required( (array) $this->field );
		$is_empty    = ! is_array( $args['value'] ) && trim( $args['value'] ) == '';
		if ( ! $is_required || ! $is_empty ) {
			return $errors;
		}

		$type  = FrmField::get_option( $this->field, 'data_type' );
		$watch = FrmField::get_option( $this->field, 'watch_lookup' );
		if ( $type === 'text' || $type === 'data' || empty( $watch ) ) {
			return $errors;
		}

		$required_msg = FrmFieldsHelper::get_error_msg( $this->field, 'blank' );
		if ( ! isset( $args['errors'][ 'field' . $args['id'] ] ) || $args['errors'][ 'field' . $args['id'] ] !== $required_msg ) {
			return $errors;
		}

		add_filter( 'frm_validate_lookup_field_entry', array( $this, 'maybe_remove_error' ), 20, 4 );

		return $errors;
	}

	/**
	 * If the field has an error message, check to see if it has options.
	 *
	 * @since 4.01
	 *
	 * @param array $errors
	 * @param object $field
	 * @param mixed $value
	 * @param array $args
	 */
	public function maybe_remove_error( $errors, $field, $value, $args ) {
		remove_filter( 'frm_validate_lookup_field_entry', array( $this, 'maybe_remove_error' ), 20, 4 );

		$options = $this->get_dependent_options( $args );
		if ( empty( $options ) ) {
			unset( $errors[ 'field' . $args['id'] ] );
		}

		return $errors;
	}

	/**
	 * @since 4.01
	 *
	 * @param array $args
	 */
	private function get_dependent_options( $args ) {
		$parent_args = array(
			'parent_field_ids' => array(),
			'parent_vals'      => array(),
		);

		$watch = FrmField::get_option( $this->field, 'watch_lookup' );
		foreach ( $watch as $parent ) {
			$parent_args['parent_field_ids'][] = $parent;
			$value = '';
			FrmEntriesHelper::get_posted_value( $parent, $value, $args );
			$parent_args['parent_vals'][] = $value;
		}

		return FrmProLookupFieldsController::get_filtered_values_for_dependent_lookup_field( $parent_args, $this->field );
	}

	protected function prepare_import_value( $value, $atts ) {
		if ( FrmField::get_option( $this->field, 'data_type' ) == 'checkbox' ) {
			$value = FrmProXMLHelper::convert_imported_value_to_array( $value );
		}
		return $value;
	}

	protected function extra_field_opts() {
		return array(
			'data_type'                  => 'select',
			'watch_lookup'               => array(),
			'get_values_form'            => '',
			'get_values_field'           => '',
			'lookup_filter_current_user' => false,
			'lookup_option_order'        => 'ascending',
		);
	}
}
