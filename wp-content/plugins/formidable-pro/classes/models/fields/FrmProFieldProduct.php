<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

/**
 * @since 4.04
 */
class FrmProFieldProduct extends FrmFieldType {

	protected $type = 'product';

	/**
	 * We use this because of the display cases of checkbox and radio. Dropdown can
	 * still manage with the aria-labelledby="field_[key]_label" in the custom html.
	 */
	protected $has_for_label = false;

	protected function input_html() {
		return $this->multiple_input_html();
	}

	protected function include_form_builder_file() {
		return $this->include_front_form_file();
	}

	protected function new_field_settings() {
		return array(
			'options' => serialize(
				array(
					'',
					__( 'Product 1', 'formidable-pro' ),
				)
			),
		);
	}

	protected function field_settings_for_type() {
		$settings = parent::field_settings_for_type();

		$settings['options']        = true;
		$settings['default_value']  = true;

		/**
		 * So that we can have placeholder and size in Advanced settings
		 * because of the possibility of displaying like a select field.
		 */
		$settings['clear_on_focus'] = true;
		$settings['size']           = true;

		FrmProFieldsHelper::fill_default_field_display( $settings );
		return $settings;
	}

	public function show_extra_field_choices( $args ) {
		$field = $args['field'];
		include( FrmProAppHelper::plugin_path() . '/classes/views/frmpro-fields/back-end/separate-values.php' );
		parent::show_extra_field_choices( $args );
	}

	public function show_primary_options( $args ) {
		$field = $args['field'];
		$data_types = $this->get_data_type_settings();
		include( FrmProAppHelper::plugin_path() . '/classes/views/frmpro-fields/back-end/product-options.php' );
		parent::show_primary_options( $args );
	}

	private function get_data_type_settings() {
		return array(
			'select'   => __( 'Dropdown', 'formidable-pro' ),
			'radio'    => __( 'Radio Buttons', 'formidable-pro' ),
			'checkbox' => __( 'Checkboxes', 'formidable-pro' ),
			'single'   => __( 'Single Product', 'formidable-pro' ),
			'user_def' => __( 'User Defined', 'formidable-pro' ),
		);
	}

	protected function extra_field_opts() {
		$form_id  = $this->get_field_column( 'form_id' );

		return array_merge(
			parent::extra_field_opts(),
			array(
				'data_type'  => 'select',
				/* 'align' is needed for the checkbox and radio 'data_type' cases. */
				'align'      => FrmStylesController::get_style_val( 'check_align', ( empty( $form_id ) ? 'default' : $form_id ) ),
			)
		);
	}

	public function displayed_field_type( $field ) {
		return array(
			$this->type => true,
		);
	}

	/**
	 * Remove the frm_opt_container class for dropdowns.
	 */
	protected function after_replace_html_shortcodes( $args, $html ) {
		$data_type = FrmField::get_option( $this->field, 'data_type' );
		if ( 'radio' !== $data_type && 'checkbox' !== $data_type ) {
			$html = str_replace( '"frm_opt_container', '"frm_data_container', $html );
		}

		$form_id = isset( $args['parent_form_id'] ) ? $args['parent_form_id'] : 0;
		if ( empty( $form_id ) ) {
			$form_id = $this->get_field_column( 'form_id' );
		}
		FrmProCurrencyHelper::add_currency_to_global( $form_id );

		return $html;
	}

	protected function include_front_form_file() {
		if ( is_array( $this->field ) && ! is_array( $this->field['options'] ) ) {
			$this->field['options'] = array();
		}

		$product_type = FrmField::get_option( $this->field, 'data_type' );

		$file = FrmProAppHelper::plugin_path() . '/classes/views/frmpro-fields/front-end/product-';
		if ( $product_type === 'checkbox' ) {
			$file .= 'radio.php';
		} elseif ( in_array( $product_type, array_keys( self::get_data_type_settings() ) ) ) {
			$file .= str_replace( '_', '-', $product_type ) . '.php';
		} else {
			$file .= 'select.php';
		}
		return $file;
	}

	protected function should_continue_to_field_options( $args ) {
		return true;
	}

	protected function get_bulk_edit_string() {
		return __( 'Bulk Edit Products', 'formidable-pro' );
	}

	protected function get_add_option_string() {
		return __( 'Add Product', 'formidable-pro' );
	}

	protected function show_single_option( $args ) {
		self::single_option( $args['field'] );
	}

	protected function extra_field_choices_class() {
		$data_type  = FrmField::get_option( $this->field, 'data_type' );
		$type_class = '';
		if ( 'single' === $data_type || 'user_def' === $data_type ) {
			$type_class = ' frm_prod_type_' . $data_type;
		}
		return ' frmjs_product_choices' . $type_class;
	}

	protected function field_choices_heading_attrs( $args ) {
		$data_type  = FrmField::get_option( $this->field, 'data_type' );
		$type_class = '';
		if ( 'user_def' === $data_type ) {
			$type_class = ' frm_prod_user_def';
		}
		echo ' class="frm_prod_options_heading' . esc_attr( $type_class ) . '"';
	}

	public function validate( $args ) {
		$parent_errors = parent::validate( $args );
		if ( ! empty( $parent_errors ) || empty( $args['value'] ) ) {
			return $parent_errors;
		}

		if ( FrmProEntryMeta::is_field_conditionally_hidden( $this->field ) ) {
			// If it's empty, don't include in the total validation.
			// Fields can be conditional or not required.
			return array();
		}

		global $frm_products;
		if ( empty( $frm_products ) ) {
			$frm_products = array();
		}

		$product_field_key = $this->get_field_column( 'id' ) . '_' . $args['parent_field_id'] . '_' . $args['key_pointer'];

		if ( ! isset( $frm_products[ $product_field_key ] ) || ! is_array( $frm_products[ $product_field_key ] ) ) {
			$frm_products[ $product_field_key ] = array();
		}
		$frm_products[ $product_field_key ]['price']           = $this->get_posted_price( $args['value'] );
		$frm_products[ $product_field_key ]['key_pointer']     = $args['key_pointer'];
		$frm_products[ $product_field_key ]['parent_field_id'] = $args['parent_field_id'];

		if ( 'user_def' === FrmField::get_option( $this->field, 'data_type' ) ) {
			$frm_products[ $product_field_key ]['user_def']    = true;
		}

		return array();
	}

	public function get_posted_price( $posted_value ) {
		if ( 'user_def' === FrmField::get_option( $this->field, 'data_type' ) ) {
			return $posted_value;
		}

		$price = 0;
		$options = $this->get_field_column( 'options' );
		if ( ! is_array( $options ) ) {
			return $price;
		}

		if ( is_array( $posted_value ) ) {
			$price = array();
			foreach ( $posted_value as $value ) {
				$this->get_price( $options, $value, $price );
			}
		} else {
			$this->get_price( $options, $posted_value, $price );
		}

		return $price;
	}

	/**
	 * @since 4.04
	 */
	private function get_price( $options, $value, &$price ) {
		foreach ( $options as $option ) {
			if ( ! is_array( $option ) || $option['value'] !== $value ) {
				continue;
			}

			if ( isset( $option['price'] ) && trim( $option['price'] ) ) {
				if ( is_array( $price ) ) {
					$price[] = trim( $option['price'] );
				} else {
					$price   = trim( $option['price'] );
				}
			}
			break;
		}
	}

	public static function single_option( $field ) {
		self::hidden_field_option( $field );

		if ( ! is_array( $field['options'] ) ) {
			return;
		}

		$base_name = 'default_value_' . $field['id'];
		$html_id    = isset( $field['html_id'] ) ? $field['html_id'] : FrmFieldsHelper::get_html_id( $field );

		$default_type = self::get_default_value_type( $field );

		foreach ( $field['options'] as $opt_key => $opt ) {
			$field_val = FrmFieldsHelper::get_value_from_array( $opt, $opt_key, $field );
			$price     = self::get_price_from_array( $opt, $opt_key, $field );
			$opt       = FrmFieldsHelper::get_label_from_array( $opt, $opt_key, $field );

			$field_name = $base_name . ( $default_type === 'checkbox' ? '[' . $opt_key . ']' : '' );

			$checked = ( isset( $field['default_value'] ) && ( ( ! is_array( $field['default_value'] ) && $field['default_value'] == $field_val ) || ( is_array( $field['default_value'] ) && in_array( $field_val, $field['default_value'] ) ) ) );

			require( FrmProAppHelper::plugin_path() . '/classes/views/frmpro-fields/back-end/product-single-option.php' );

			unset( $checked );
		}
	}

	private static function hidden_field_option( $field ) {
		$opt_key    = '000';
		$field_val  = __( 'New Product', 'formidable-pro' );
		$opt        = $field_val;
		$price      = '';
		$checked    = false;
		$field_name = 'default_value_' . $field['id'];
		$html_id    = isset( $field['html_id'] ) ? $field['html_id'] : FrmFieldsHelper::get_html_id( $field );

		$default_type = self::get_default_value_type( $field );
		$field_name  .= ( $default_type === 'checkbox' ? '[' . $opt_key . ']' : '' );

		require( FrmProAppHelper::plugin_path() . '/classes/views/frmpro-fields/back-end/product-single-option.php' );
	}

	private static function get_default_value_type( $field ) {
		$data_type = FrmField::get_option_in_array( $field, 'data_type' );
		if ( 'checkbox' !== $data_type ) {
			$data_type = 'radio';
		}
		return $data_type;
	}

	public static function get_price_from_array( $opt, $opt_key, $field ) {
		$opt = apply_filters( 'frm_field_price_saved', $opt, $opt_key, $field );

		if ( is_array( $opt ) ) {
			return isset( $opt['price'] ) ? $opt['price'] : '';
		}

		return '';
	}

	/**
	 * Format price when show=price.
	 *
	 * @since 4.05
	 */
	protected function prepare_display_value( $value, $atts ) {
		if ( ! isset( $atts['show'] ) || $atts['show'] !== 'price' ) {
			return $value;
		}

		$is_array = is_array( $value );
		if ( ! $is_array ) {
			$value = explode( $atts['sep'], $value );
		}

		foreach ( $value as $k => $v ) {
			$value[ $k ] = FrmProCurrencyHelper::format_amount_for_currency( $this->get_field_column( 'form_id' ), $v );
		}

		if ( ! $is_array ) {
			$value = implode( $atts['sep'], $value );
		}

		return $value;
	}
}
