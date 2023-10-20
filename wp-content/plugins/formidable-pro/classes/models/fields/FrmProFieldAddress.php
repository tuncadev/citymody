<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

/**
 * @since 3.0
 */
class FrmProFieldAddress extends FrmFieldType {

	/**
	 * @var string
	 * @since 3.0
	 */
	protected $type = 'address';

	/**
	 * @var bool
	 * @since 3.0
	 */
	protected $has_for_label = false;

	protected function field_settings_for_type() {
		$settings = array(
			'clear_on_focus' => false, // Don't use the regular placeholder option.
			'default_value'  => true,
			'description'    => false,
			'visibility'     => true,
			'read_only'      => true,
		);

		if ( is_array( $this->default_value_to_array() ) ) {
			$settings['default'] = false;
		}

		FrmProFieldsHelper::fill_default_field_display( $settings );
		return $settings;
	}

	protected function extra_field_opts() {
		$options = array(
			'address_type' => 'international',
		);

		$default_labels = $this->default_labels();
		foreach ( $default_labels as $key => $label ) {
			$options[ $key . '_desc' ] = $label;
		}

		return $options;
	}

	/**
	 * @since 4.0
	 * @param array $args - Includes 'field', 'display', and 'values'.
	 */
	public function show_primary_options( $args ) {
		$field = $args['field'];
		include( FrmProAppHelper::plugin_path() . '/classes/views/combo-fields/addresses/back-end-field-opts.php' );

		parent::show_primary_options( $args );
	}

	/**
	 * Include the settings for placeholder, default value, and sub labels for each
	 * of the individual field labels.
	 *
	 * @since 4.0
	 * @param array $args - Includes 'field', 'display'.
	 */
	public function show_after_default( $args ) {
		$field         = $args['field'];
		$default_value = $this->default_value_to_array();
		$sub_fields    = $this->all_default_labels();

		foreach ( $sub_fields as $name => $field_label ) {
			include( FrmProAppHelper::plugin_path() . '/classes/views/frmpro-fields/back-end/default-placeholder.php' );
		}

		if ( ! empty( $field['description'] ) ) {
			// This is here only for reverse compatibility.
			include( FrmAppHelper::plugin_path() . '/classes/views/frm-fields/back-end/field-description.php' );
		}
	}

	/**
	 * @since 3.06.01
	 */
	public function translatable_strings() {
		$strings   = parent::translatable_strings();
		$strings[] = 'line1_desc';
		$strings[] = 'line2_desc';
		$strings[] = 'city_desc';
		$strings[] = 'state_desc';
		$strings[] = 'zip_desc';
		$strings[] = 'country_desc';
		return $strings;
	}

	private function default_labels() {
		return array(
			'line1' => '',
			'line2' => '',
			'city'  => __( 'City', 'formidable-pro' ),
			'state' => __( 'State/Province', 'formidable-pro' ),
			'zip'   => __( 'Zip/Postal', 'formidable-pro' ),
			'country' => __( 'Country', 'formidable-pro' ),
		);
	}

	/**
	 * @since 4.0
	 */
	private function all_default_labels() {
		$labels = $this->default_labels();
		$labels['line1']   = __( 'Line 1', 'formidable-pro' );
		$labels['line2']   = __( 'Line 2', 'formidable-pro' );
		$labels['country'] = __( 'Country', 'formidable-pro' );
		return $labels;
	}

	public function show_on_form_builder( $name = '' ) {
		$field = FrmFieldsHelper::setup_edit_vars( $this->field );

		$defaults = $this->empty_value_array();
		$this->fill_values( $field['default_value'], $defaults );

		$field['value'] = $field['default_value'];
		$sub_fields = FrmProAddressesController::get_sub_fields( $field );
		$field_name = $this->html_name( $name );
		$html_id = $this->html_id();

		include( FrmProAppHelper::plugin_path() . '/classes/views/combo-fields/input-form-builder.php' );
	}

	public function front_field_input( $args, $shortcode_atts ) {
		$pass_args = array( 'errors' => $args['errors'], 'html_id' => $args['html_id'], 'field_id' => $args['field_id'] );
		ob_start();
		FrmProAddressesController::show_in_form( $this->field, $args['field_name'], $pass_args );
		$input_html = ob_get_contents();
		ob_end_clean();

		return $input_html;
	}

	protected function fill_default_atts( &$atts ) {
		parent::fill_default_atts( $atts );
		$defaults = array(
			'line_sep'    => ' <br/>',
			'force_array' => false,
		);
		$atts = wp_parse_args( $atts, $defaults );
	}

	protected function prepare_display_value( $value, $atts ) {
		if ( ! is_array( $value ) ) {
			return $value;
		}

		$new_value = '';
		if ( ! empty( $value['line1'] ) ) {
			$new_value = $this->format_address_for_display( $value, $atts );
		}

		return $new_value;
	}

	/**
	 * @since 3.0.06
	 *
	 * @param array $value
	 * @param array $atts
	 */
	public function format_address_for_display( $value, $atts = array() ) {
		$defaults = $this->empty_value_array();
		$this->fill_values( $value, $defaults );

		$this->fill_default_atts( $atts );

		if ( $atts['force_array'] ) {
			return $value;
		}

		$format = $this->address_format_for_display( $atts );
		foreach ( $defaults as $k => $part ) {
			$format = str_replace( '[' . $k . ']', $value[ $k ], $format );
		}
		$format = str_replace( $atts['line_sep'] . $atts['line_sep'], $atts['line_sep'], $format );

		return $format;
	}

	/**
	 * @since 3.0.06
	 * @param array $atts
	 */
	private function address_format_for_display( $atts ) {
		if ( isset( $atts['show'] ) && ! empty( $atts['show'] ) ) {
			return '[' . $atts['show'] . ']';
		}

		$line_sep = $atts['line_sep'];
		$address_type = FrmField::get_option( $this->field, 'address_type' );

		$address_format = '[line1]' . $line_sep . '[line2]' . $line_sep;
		if ( 'europe' === $address_type ) {
			$address_format .= '[zip] [city]';
		} else {
			$address_format .= '[city], [state] [zip]';
		}
		$address_format .= $line_sep . '[country]';

		/**
		 * Change the format of a displayed address
		 *
		 * @since 3.0.06
		 */
		return apply_filters( 'frm_address_format', $address_format, array( 'field' => $this->field ) );
	}

	/**
	 * @since 4.0
	 * @return array|string
	 */
	private function default_value_to_array() {
		$default_value = $this->get_field_column( 'default_value' );
		if ( empty( $default_value ) ) {
			$default_value = array();
		} elseif ( ! is_array( $default_value ) && strpos( $default_value, ']' ) === false ) {
			// This is a default value without a shortcode so tear it up.
			$default_value = $this->address_string_to_array( $default_value );
		}

		if ( is_array( $default_value ) ) {
			$defaults = $this->empty_value_array();
			$this->fill_values( $default_value, $defaults );
		}

		return $default_value;
	}

	public function address_string_to_array( $value ) {
		if ( is_array( $value ) ) {
			return $value;
		}

		$value = array_map( 'trim', explode( ',', $value ) );

		$empty_array = array_keys( $this->empty_value_array() );

		$array_length_diff = count( $empty_array ) - count( $value );
		if ( $array_length_diff === 1 ) {
			$value[] = '';
		} elseif ( $array_length_diff > 1 ) {
			$filler_array = array_fill( 0, $array_length_diff, '' );
			$value = array_merge( $value, $filler_array );
		} elseif ( $array_length_diff < 0 ) {
			$value = array_slice( $value, 0, count( $empty_array ) );
		}

		return array_combine( $empty_array, $value );
	}

	/**
	 * @return array
	 */
	private function empty_value_array() {
		return FrmProAddressesController::empty_value_array();
	}

	/**
	 * Convert comma-separated address values to an associative array
	 *
	 * @since 2.02.13
	 *
	 * @param string|array $value
	 *
	 * @return array
	 */
	protected function prepare_import_value( $value, $atts ) {
		if ( is_array( $value ) ) {
			return $value;
		}

		$sep = apply_filters( 'frm_csv_sep', ', ' );
		$value = explode( $sep, $value );

		$count = count( $value );

		if ( $count < 4 || $count > 6 ) {
			return $value;
		}

		$new_value = $this->empty_value_array();

		$new_value['line1'] = $value[0];

		$last_item = end( $value );

		if ( $count == 6 || ( $count == 5 && is_numeric( $last_item ) ) ) {
			$new_value['line2'] = $value[1];
			$new_value['city']  = $value[2];
			$new_value['state'] = $value[3];
			$new_value['zip']   = $value[4];

			if ( $count == 6 ) {
				$new_value['country'] = $value[5];
			}
		} else {
			$new_value['city']  = $value[1];
			$new_value['state'] = $value[2];
			$new_value['zip']   = $value[3];

			if ( $count == 5 ) {
				$new_value['country'] = $value[4];
			}
		}

		return $new_value;
	}

	public function validate( $args ) {
		$this->field->temp_id = $args['id'];

		$errors = array();
		self::validate_required_fields( $errors, $args );
		self::validate_zip( $errors, $args );

		return $errors;
	}

	private function validate_required_fields( &$errors, $args ) {
		if ( $this->field->required ) {

			if ( FrmProEntryMeta::skip_required_validation( $this->field ) ) {
				return;
			}

			$values = $args['value'];
			if ( $values == '' ) {
				$values = FrmProAddressesController::empty_value_array();
			}

			$blank_msg = FrmFieldsHelper::get_error_msg( $this->field, 'blank' );

			foreach ( $values as $key => $value ) {
				if ( empty( $value ) && $key != 'line2' ) {
					$errors[ 'field' . $args['id'] . '-' . $key ] = '';
					$errors[ 'field' . $args['id'] ] = $blank_msg;
				}
			}
		}
	}

	private function validate_zip( &$errors, $args ) {
		$values = $args['value'];
		if ( isset( $values['zip'] ) && ! empty( $values['zip'] ) ) {
			$address_type = FrmField::get_option( $this->field, 'address_type' );
			$format = '';
			if ( $address_type === 'us' ) {
				$format = '/^[0-9]{5}(?:-[0-9]{4})?$/';
			}
			$format = apply_filters( 'frm_zip_format', $format, array( 'field' => $this->field ) );
			if ( ! empty( $format ) && ! preg_match( $format, $values['zip'] ) ) {
				$errors[ 'field' . $args['id'] . '-zip' ] = __( 'This value is invalid', 'formidable-pro' );
			}
		}
	}

	/**
	 * @since 4.0.04
	 */
	public function sanitize_value( &$value ) {
		FrmAppHelper::sanitize_value( 'sanitize_text_field', $value );
	}
}
