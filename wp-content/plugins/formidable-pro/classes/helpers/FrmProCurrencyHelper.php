<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

class FrmProCurrencyHelper {

	/**
	 * @since 4.04
	 */
	public static function get_currency( $form ) {
		$frm_settings = FrmProAppHelper::get_settings();
		$currency     = trim( $frm_settings->currency );
		if ( ! $currency ) {
			$currency = 'USD';
		}
		$currency = self::get_currencies( $currency );
		/**
		 * Allow custom code to change the currency for different currencies per form.
		 *
		 * @since 4.04
		 * @param array      $currency  The currency information.
		 * @param int|object $form      The ID of the form or the form object.
		 */
		$currency = apply_filters( 'frm_currency', $currency, $form );

		return $currency;
	}

	/**
	 * If the currency is needed for this form, add it to the global.
	 * This is later included in the footer.
	 *
	 * @since 4.04
	 */
	public static function add_currency_to_global( $form_id ) {
		global $frm_vars;
		if ( ! isset( $frm_vars['currency'] ) ) {
			$frm_vars['currency'] = array();
		}

		if ( ! isset( $frm_vars['currency'][ $form_id ] ) ) {
			$frm_vars['currency'][ $form_id ] = self::normalize_decimal_separators( self::get_currency( $form_id ) );
		}
	}

	/**
	 * Avoid blank decimal separators causing calculated values to be multipled by 100.
	 *
	 * @since 5.0.16
	 *
	 * @param array $currency
	 * @return array
	 */
	private static function normalize_decimal_separators( $currency ) {
		$currency['decimal_separator'] = trim( $currency['decimal_separator'] );
		if ( ! $currency['decimal_separator'] && 0 === (int) $currency['decimals'] ) {
			$currency['decimal_separator'] = '.';
		}
		return $currency;
	}

	/**
	 * This function is triggered on the frm_display_value hook
	 * which is run any time the value is displayed.
	 *
	 * @since 4.06.01
	 *
	 * @param string|array $value
	 * @param object|array $field
	 * @param array $atts
	 *
	 * @return string
	 */
	public static function maybe_format_currency( $value, $field, $atts ) {
		if ( is_array( $value ) || $value === '' ) {
			return $value;
		}

		$format      = isset( $atts['format'] ) ? $atts['format'] : '';
		$is_currency = FrmField::get_option( $field, 'is_currency' ) || $format === 'currency';
		if ( ! $is_currency || $format === 'number' ) {
			return $value;
		}

		$form_id  = is_object( $field ) ? $field->form_id : $field['form_id'];
		$currency = self::get_currency_for_field( $field );

		return self::format_amount_for_currency( $form_id, $value, $currency );
	}

	/**
	 * Maybe get the custom currency for a field.
	 *
	 * @since 5.5.4 This was moved from self::maybe_format_currency.
	 *
	 * @param object|array $field
	 * @return array|null An array is only returned for a custom currency.
	 */
	private static function get_currency_for_field( $field ) {
		if ( ! FrmField::get_option( $field, 'custom_currency' ) ) {
			// There is a is_null check in self::format_amount_for_currency that will call self::get_currency to resolve this.
			return null;
		}
		return self::get_custom_currency( is_object( $field ) ? $field->field_options : $field );
	}

	/**
	 * @since 5.0.16
	 *
	 * @param array $field_options
	 * @return array
	 */
	public static function get_custom_currency( $field_options ) {
		return array(
			'thousand_separator' => $field_options['custom_thousand_separator'],
			'decimal_separator'  => $field_options['custom_decimal_separator'],
			'decimals'           => (int) $field_options['custom_decimals'],
			'symbol_left'        => $field_options['custom_symbol_left'],
			'symbol_right'       => $field_options['custom_symbol_right'],
			'symbol_padding'     => '',
		);
	}

	/**
	 * @since 4.04
	 *
	 * @param object|int   $form   Form object or ID.
	 * @param string|float $amount The string could contain the currency symbol.
	 * @param array|null   $currency
	 * @return string|float
	 */
	public static function format_amount_for_currency( $form = null, $amount = 0, $currency = null ) {
		if ( null === $form ) {
			return $amount;
		}

		if ( is_null( $currency ) ) {
			$currency = self::get_currency( $form );
		}

		if ( is_string( $amount ) ) {
			$amount = floatval( self::prepare_price( $amount, $currency ) );
		}

		$amount       = number_format( $amount, $currency['decimals'], $currency['decimal_separator'], $currency['thousand_separator'] );
		$left_symbol  = $currency['symbol_left'] . $currency['symbol_padding'];
		$right_symbol = $currency['symbol_padding'] . $currency['symbol_right'];
		$amount       = $left_symbol . $amount . $right_symbol;

		return $amount;
	}

	/**
	 * @since 4.04
	 */
	public static function prepare_price( $price, $currency ) {
		$price = trim( $price );
		if ( ! $price ) {
			return 0;
		}

		preg_match_all( '/[\-]*[0-9,.]*\.?\,?[0-9]+/', $price, $matches );
		$price = $matches ? end( $matches[0] ) : 0;
		if ( $price ) {
			$price = self::maybe_use_decimal( $price, $currency );
			$price = str_replace( $currency['decimal_separator'], '.', str_replace( $currency['thousand_separator'], '', $price ) );
		}
		return $price;
	}

	/**
	 * @since 4.04
	 */
	private static function maybe_use_decimal( $amount, $currency ) {
		if ( $currency['thousand_separator'] == '.' ) {
			$amount_parts = explode( '.', $amount );
			$used_for_decimal = ( count( $amount_parts ) == 2 && strlen( $amount_parts[1] ) == 2 );
			if ( $used_for_decimal ) {
				$amount = str_replace( '.', $currency['decimal_separator'], $amount );
			}
		}
		return $amount;
	}

	/**
	 * @since 4.04
	 */
	public static function get_currencies( $currency = false ) {
		$currencies = array(
			'AUD' => array(
				'name' => __( 'Australian Dollar', 'formidable-pro' ),
				'symbol_left' => '$',
				'symbol_right' => '',
				'symbol_padding' => ' ',
				'thousand_separator' => ',',
				'decimal_separator' => '.',
				'decimals' => 2,
			),
			'BDT' => array(
				'name' => __( 'Bangladeshi Taka', 'formidable-pro' ),
				'symbol_left'        => '৳',
				'symbol_right'       => '',
				'symbol_padding'     => ' ',
				'thousand_separator' => ',',
				'decimal_separator'  => '.',
				'decimals'           => 2,
			),
			'BRL' => array(
				'name' => __( 'Brazilian Real', 'formidable-pro' ),
				'symbol_left' => 'R$',
				'symbol_right' => '',
				'symbol_padding' => ' ',
				'thousand_separator' => '.',
				'decimal_separator' => ',',
				'decimals' => 2,
			),
			'CAD' => array(
				'name' => __( 'Canadian Dollar', 'formidable-pro' ),
				'symbol_left' => '$',
				'symbol_right' => 'CAD',
				'symbol_padding' => ' ',
				'thousand_separator' => ',',
				'decimal_separator' => '.',
				'decimals' => 2,
			),
			'CNY' => array(
				'name'               => __( 'Chinese Renminbi Yuan', 'formidable-pro' ),
				'symbol_left'        => '¥',
				'symbol_right'       => '',
				'symbol_padding'     => ' ',
				'thousand_separator' => ',',
				'decimal_separator'  => '.',
				'decimals'           => 2,
			),
			'CZK' => array(
				'name' => __( 'Czech Koruna', 'formidable-pro' ),
				'symbol_left' => '',
				'symbol_right' => '&#75;&#269;',
				'symbol_padding' => ' ',
				'thousand_separator' => ' ',
				'decimal_separator' => ',',
				'decimals' => 2,
			),
			'DKK' => array(
				'name' => __( 'Danish Krone', 'formidable-pro' ),
				'symbol_left' => 'Kr',
				'symbol_right' => '',
				'symbol_padding' => ' ',
				'thousand_separator' => '.',
				'decimal_separator' => ',',
				'decimals' => 2,
			),
			'EUR' => array(
				'name' => __( 'Euro', 'formidable-pro' ),
				'symbol_left' => '',
				'symbol_right' => '&#8364;',
				'symbol_padding' => ' ',
				'thousand_separator' => '.',
				'decimal_separator' => ',',
				'decimals' => 2,
			),
			'HKD' => array(
				'name' => __( 'Hong Kong Dollar', 'formidable-pro' ),
				'symbol_left' => 'HK$',
				'symbol_right' => '',
				'symbol_padding' => '',
				'thousand_separator' => ',',
				'decimal_separator' => '.',
				'decimals' => 2,
			),
			'HUF' => array(
				'name' => __( 'Hungarian Forint', 'formidable-pro' ),
				'symbol_left' => '',
				'symbol_right' => 'Ft',
				'symbol_padding' => ' ',
				'thousand_separator' => '.',
				'decimal_separator' => ',',
				'decimals' => 2,
			),
			'INR' => array(
				'name'               => __( 'Indian Rupee', 'formidable-pro' ),
				'symbol_left'        => '&#8377;',
				'symbol_right'       => '',
				'symbol_padding'     => ' ',
				'thousand_separator' => ',',
				'decimal_separator'  => '.',
				'decimals'           => 2,
			),
			'ILS' => array(
				'name' => __( 'Israeli New Sheqel', 'formidable-pro' ),
				'symbol_left' => '&#8362;',
				'symbol_right' => '',
				'symbol_padding' => ' ',
				'thousand_separator' => ',',
				'decimal_separator' => '.',
				'decimals' => 2,
			),
			'JPY' => array(
				'name' => __( 'Japanese Yen', 'formidable-pro' ),
				'symbol_left' => '&#165;',
				'symbol_right' => '',
				'symbol_padding' => ' ',
				'thousand_separator' => ',',
				'decimal_separator' => '',
				'decimals' => 0,
			),
			'MYR' => array(
				'name' => __( 'Malaysian Ringgit', 'formidable-pro' ),
				'symbol_left' => '&#82;&#77;',
				'symbol_right' => '',
				'symbol_padding' => ' ',
				'thousand_separator' => ',',
				'decimal_separator' => '.',
				'decimals' => 2,
			),
			'MXN' => array(
				'name' => __( 'Mexican Peso', 'formidable-pro' ),
				'symbol_left' => '$',
				'symbol_right' => '',
				'symbol_padding' => ' ',
				'thousand_separator' => ',',
				'decimal_separator' => '.',
				'decimals' => 2,
			),
			'NOK' => array(
				'name' => __( 'Norwegian Krone', 'formidable-pro' ),
				'symbol_left' => 'Kr',
				'symbol_right' => '',
				'symbol_padding' => ' ',
				'thousand_separator' => '.',
				'decimal_separator' => ',',
				'decimals' => 2,
			),
			'NZD' => array(
				'name' => __( 'New Zealand Dollar', 'formidable-pro' ),
				'symbol_left' => '$',
				'symbol_right' => '',
				'symbol_padding' => ' ',
				'thousand_separator' => ',',
				'decimal_separator' => '.',
				'decimals' => 2,
			),
			'PKR' => array(
				'name'               => __( 'Pakistani Rupee', 'formidable-pro' ),
				'symbol_left'        => '₨',
				'symbol_right'       => '',
				'symbol_padding'     => ' ',
				'thousand_separator' => '',
				'decimal_separator'  => '.',
				'decimals'           => 2,
			),
			'PHP' => array(
				'name' => __( 'Philippine Peso', 'formidable-pro' ),
				'symbol_left' => 'Php',
				'symbol_right' => '',
				'symbol_padding' => ' ',
				'thousand_separator' => ',',
				'decimal_separator' => '.',
				'decimals' => 2,
			),
			'PLN' => array(
				'name' => __( 'Polish Zloty', 'formidable-pro' ),
				'symbol_left' => '&#122;&#322;',
				'symbol_right' => '',
				'symbol_padding' => ' ',
				'thousand_separator' => '.',
				'decimal_separator' => ',',
				'decimals' => 2,
			),
			'GBP' => array(
				'name' => __( 'Pound Sterling', 'formidable-pro' ),
				'symbol_left' => '&#163;',
				'symbol_right' => '',
				'symbol_padding' => ' ',
				'thousand_separator' => ',',
				'decimal_separator' => '.',
				'decimals' => 2,
			),
			'SGD' => array(
				'name' => __( 'Singapore Dollar', 'formidable-pro' ),
				'symbol_left' => '$',
				'symbol_right' => '',
				'symbol_padding' => ' ',
				'thousand_separator' => ',',
				'decimal_separator' => '.',
				'decimals' => 2,
			),
			'ZAR' => array(
				'name'               => __( 'South African Rand', 'formidable-pro' ),
				'symbol_left'        => 'R',
				'symbol_right'       => '',
				'symbol_padding'     => ' ',
				'thousand_separator' => ' ',
				'decimal_separator'  => '.',
				'decimals'           => 2,
			),
			'LKR' => array(
				'name'               => __( 'Sri Lankan Rupee', 'formidable-pro' ),
				'symbol_left'        => '₨',
				'symbol_right'       => '',
				'symbol_padding'     => ' ',
				'thousand_separator' => '',
				'decimal_separator'  => '.',
				'decimals'           => 2,
			),
			'SEK' => array(
				'name' => __( 'Swedish Krona', 'formidable-pro' ),
				'symbol_left' => '',
				'symbol_right' => 'Kr',
				'symbol_padding' => ' ',
				'thousand_separator' => ' ',
				'decimal_separator' => ',',
				'decimals' => 2,
			),
			'CHF' => array(
				'name' => __( 'Swiss Franc', 'formidable-pro' ),
				'symbol_left' => 'Fr.',
				'symbol_right' => '',
				'symbol_padding' => ' ',
				'thousand_separator' => "'",
				'decimal_separator' => '.',
				'decimals' => 2,
			),
			'TWD' => array(
				'name' => __( 'Taiwan New Dollar', 'formidable-pro' ),
				'symbol_left' => '$',
				'symbol_right' => '',
				'symbol_padding' => ' ',
				'thousand_separator' => ',',
				'decimal_separator' => '.',
				'decimals' => 2,
			),
			'THB' => array(
				'name' => __( 'Thai Baht', 'formidable-pro' ),
				'symbol_left' => '&#3647;',
				'symbol_right' => '',
				'symbol_padding' => ' ',
				'thousand_separator' => ',',
				'decimal_separator' => '.',
				'decimals' => 2,
			),
			'TRY' => array(
				'name' => __( 'Turkish Liras', 'formidable-pro' ),
				'symbol_left' => '',
				'symbol_right' => '&#8364;',
				'symbol_padding' => ' ',
				'thousand_separator' => '.',
				'decimal_separator' => ',',
				'decimals' => 2,
			),
			'USD' => array(
				'name' => __( 'U.S. Dollar', 'formidable-pro' ),
				'symbol_left' => '$',
				'symbol_right' => '',
				'symbol_padding' => '',
				'thousand_separator' => ',',
				'decimal_separator' => '.',
				'decimals' => 2,
			),
			'UYU' => array(
				'name' => __( 'Uruguayan Peso', 'formidable-pro' ),
				'symbol_left' => '$U',
				'symbol_right' => '',
				'symbol_padding' => '',
				'thousand_separator' => '.',
				'decimal_separator' => ',',
				'decimals' => 0,
			),
		);

		$currencies = apply_filters( 'frm_currencies', $currencies );
		if ( $currency ) {
			$currency = strtoupper( $currency );
			if ( isset( $currencies[ $currency ] ) ) {
				$currencies = $currencies[ $currency ];
			}
		}

		return $currencies;
	}
}
