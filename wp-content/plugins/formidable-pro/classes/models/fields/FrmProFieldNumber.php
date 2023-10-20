<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

/**
 * @since 3.0
 */
class FrmProFieldNumber extends FrmFieldNumber {
	use FrmProFieldAutocompleteField;

	protected function field_settings_for_type() {
		$settings = parent::field_settings_for_type();

		$settings['autopopulate'] = true;
		$settings['calc']         = true;
		$settings['unique']       = true;
		$settings['read_only']    = true;
		$settings['prefix']       = true;
		$settings['autocomplete'] = true;

		FrmProFieldsHelper::fill_default_field_display( $settings );
		return $settings;
	}

	/**
	 * @return array
	 */
	protected function get_filter_keys() {
		return array( 'on', 'off', 'bday-day', 'bday-year', 'postal-code', 'transaction-amount', 'tel-extension' );
	}

	/**
	 * @since 4.05
	 */
	protected function builder_text_field( $name = '' ) {
		$html  = FrmProFieldsHelper::builder_page_prepend( $this->field );
		$field = parent::builder_text_field( $name );
		return str_replace( '[input]', $field, $html );
	}

	protected function prepare_display_value( $value, $atts ) {

		$new_val = array();
		$value = array_filter( (array) $value, 'strlen' );

		foreach ( $value as $v ) {
			if ( strpos( $v, $atts['sep'] ) ) {
				$v = explode( $atts['sep'], $v );
			}

			foreach ( (array) $v as $n ) {
				if ( ! isset( $atts['decimal'] ) ) {
					$num = explode( '.', $n );
					$atts['decimal'] = isset( $num[1] ) ? strlen( $num[1] ) : 0;
				}

				if ( is_numeric( $n ) ) {
					$n = $this->number_format_and_maintain_leading_zeroes( $n, $atts );
				}

				$new_val[] = $n;
			}

			unset( $v );
		}
		$new_val = array_filter( (array) $new_val, 'strlen' );

		return implode( $atts['sep'], $new_val );
	}

	/**
	 * Filter value through number_format a value, but maintain any leading zeros as well
	 *
	 * @param string $number
	 * @param array $atts
	 * @return string
	 */
	private function number_format_and_maintain_leading_zeroes( $number, $atts ) {
		$number_of_leading_zeroes = $this->count_number_of_leading_zeroes( $number );
		$number_formatted_value   = number_format( $number, $atts['decimal'], $atts['dec_point'], $atts['thousands_sep'] );
		return str_repeat( '0', $number_of_leading_zeroes ) . $number_formatted_value;
	}

	/**
	 * @param string $number
	 * @return int
	 */
	private function count_number_of_leading_zeroes( $number ) {
		if ( '' === $number ) {
			return 0;
		}

		$split          = explode( '.', $number );
		$leading_length = $split ? strlen( $split[0] ) : 0;

		if ( ! $leading_length ) {
			return 0;
		}

		$total_length    = strlen( $number );
		$trailing_length = 2 === count( $split ) ? strlen( $split[1] ) + 1 : 0;
		$max_length      = $total_length - $trailing_length;
		$index           = 0;

		while ( $index < $max_length && '0' === $number[ $index ] ) {
			$index ++;
		}

		if ( $index === $max_length ) {
			return $max_length - 1;
		}

		return $index;
	}

	protected function fill_default_atts( &$atts ) {
		$defaults = array(
			'dec_point' => '.',
			'thousands_sep' => '',
			'sep'       => ', ',
		);
		$atts = wp_parse_args( $atts, $defaults );
	}

	protected function prepare_import_value( $value, $atts ) {
		if ( is_numeric( $value ) ) {
			$value = (string) $value;
		}
		return $value;
	}
}
