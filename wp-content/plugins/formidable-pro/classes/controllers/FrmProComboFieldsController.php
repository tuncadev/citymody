<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

class FrmProComboFieldsController {

	public static function show_in_form_builder( $field, $name = '', $sub_fields = array() ) {
		$frm_settings = FrmAppHelper::get_settings();
		$frm_settings->use_html = false;

		// Generate field name and HTML id
		$field_name = 'item_meta[' . $field['id'] . ']';
		$html_id = 'field_' . $field['field_key'];

		include( FrmProAppHelper::plugin_path() . '/classes/views/combo-fields/input-form-builder.php' );
	}

	public static function get_sub_fields( $field ) {
		return array();
	}

	public static function fill_values( &$value, $defaults ) {
		if ( empty( $value ) ) {
			$value = $defaults;
		} else {
			$value = array_merge( $defaults, (array) $value );
		}
	}

	public static function include_placeholder( $default_value, $sub_field, $field = array() ) {
		if ( ! empty( $field ) ) {
			$use_placeholder = ! FrmField::is_option_empty( $field, 'placeholder' );
			if ( ( ! $use_placeholder || empty( $default_value[ $sub_field ] ) ) && $sub_field == 'line1' ) {
				// Allow for 'inside' label position.
				$default_value[ $sub_field ] = FrmFieldsController::get_default_value_from_name( $field );
				$use_placeholder = ( $default_value[ $sub_field ] != '' );
			}

			if ( ! $use_placeholder ) {
				return;
			}
		}

		if ( empty( $default_value ) ) {
			return;
		}

		$placeholder = isset( $default_value[ $sub_field ] ) ? $default_value[ $sub_field ] : '';
		echo ' placeholder="' . esc_attr( $placeholder ) . '" ';
	}

	public static function get_dropdown_label( $atts ) {
		$default = isset( $atts['sub_field']['placeholder'] ) ? $atts['sub_field']['placeholder'] : ' ';
		return apply_filters( 'frm_combo_dropdown_label', $default, $atts );
	}

	public static function add_atts_to_input( $atts ) {
		$placeholder   = isset( $atts['field']['placeholder'] ) ? $atts['field']['placeholder'] : '';
		$default_value = $atts['field']['default_value'];

		$field_obj   = FrmFieldFactory::get_field_type( $atts['field']['type'], $atts['field'] );
		if ( $atts['field']['type'] === 'address' ) {
			$placeholder = $field_obj->address_string_to_array( $placeholder );
			$default_value = $field_obj->address_string_to_array( $default_value );
		}

		self::include_placeholder( $placeholder, $atts['key'], $atts['field'] );
		$atts['field']['placeholder'] = '';

		if ( isset( $default_value[ $atts['key'] ] ) ) {
			$atts['field']['default_value'] = $default_value[ $atts['key'] ];
		} else {
			$atts['field']['default_value'] = '';
		}

		if ( 'select' === $atts['sub_field']['type'] && ! empty( $atts['field']['read_only'] ) ) {
			$atts['sub_field']['atts']['disabled'] = 'disabled';
		}

		if ( isset( $atts['sub_field']['optional'] ) && $atts['sub_field']['optional'] ) {
			add_filter( 'frm_field_classes', 'FrmProAddressesController::add_optional_class', 20, 2 );
			do_action( 'frm_field_input_html', $atts['field'] );
			remove_filter( 'frm_field_classes', 'FrmProAddressesController::add_optional_class', 20 );
		} else {
			do_action( 'frm_field_input_html', $atts['field'] );
		}

		if ( isset( $atts['sub_field']['atts'] ) ) {
			foreach ( $atts['sub_field']['atts'] as $att_name => $att_value ) {
				echo ' ' . esc_attr( $att_name ) . '="' . esc_attr( $att_value ) . '"';
			}
		}
	}

	public static function include_sub_label( $atts ) {
		$is_form_builder = FrmAppHelper::is_admin_page('formidable' );
		$ajax_action = FrmAppHelper::get_param( 'action', '', 'get', 'sanitize_text_field' );
		$is_new_field = FrmAppHelper::doing_ajax() && ( $ajax_action == 'frm_insert_field' || $ajax_action == 'frm_load_field' );

		self::show_sub_label( $atts );
	}

	public static function show_sub_label( $atts ) {
		$field = $atts['field'];
		$option_name = $atts['option_name'];
		if ( $field[ $option_name ] !== '' ) {
			echo '<div class="frm_description">' . wp_kses_post( $field[ $option_name ] ) . '</div>';
		}
	}

	public static function maybe_add_error_class( $atts ) {
		$temp_id   = ! empty( $atts['atts']['field_id'] ) ? $atts['atts']['field_id'] : $atts['field']['id'];
		$has_error = isset( $atts['errors'][ 'field' . $temp_id . '-' . $atts['key'] ]  );
		if ( $has_error ) {
			echo ' frm_blank_field';
		}
	}

	/**
	 * @deprecated 4.0
	 */
	public static function include_inplace_sub_label( $atts ) {
		_deprecated_function( __METHOD__, '4.0', __CLASS__ . '::show_sub_label' );
		self::show_sub_label( $atts );
	}

	/**
	 * @codeCoverageIgnore
	 */
	public static function add_default_options( $options ) {
		_deprecated_function( __METHOD__, '3.0', 'FrmProField{type} Modals' );
		return $options;
	}

	private static function empty_value_array() {
		return array();
	}
}
