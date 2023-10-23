<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

class FrmProFieldsController {

	/**
	 * @var array|null $visible_embedded_form_ids used to determine if a field has been made visible by its embedded form parent.
	 */
	private static $visible_embedded_form_ids;

	public static function &change_type( $type, $field ) {
		global $frm_vars;

		remove_filter( 'frm_field_type', 'FrmFieldsController::change_type' );

		// Don't change user ID fields or repeating sections to hidden
		if ( ! ( $type === 'divider' && FrmField::is_option_true( $field, 'repeat' ) ) && $type !== 'user_id' && ! FrmProGlobalVarsHelper::get_instance()->field_is_visible( $field ) ) {
			$type = 'hidden';
		}

		if ( $type === '10radio' ) {
			$type = 'scale';
		}

		if ( ! FrmAppHelper::is_admin() && $type !== 'hidden' && $type !== 'divider' && ! FrmProFieldsHelper::is_field_visible_to_user( $field ) ) {
			$type = 'hidden';
		}

		return $type;
	}

	public static function use_field_key_value( $opt, $opt_key, $field ) {
		//if(in_array($field['post_field'], array( 'post_category', 'post_status')) or ($field['type'] == 'user_id' and is_admin() and current_user_can('administrator')))
		if ( FrmField::is_option_true( $field, 'use_key' ) ||
			( isset($field['type']) && $field['type'] == 'data' ) ||
			( isset($field['post_field']) && $field['post_field'] == 'post_status' )
		) {
			$opt = $opt_key;
		}

		return $opt;
	}

	/**
	 * @param array    $field
	 * @param stdClass $form
	 * @param int      $parent_form_id
	 * @return void
	 */
	public static function show_field( $field, $form, $parent_form_id ) {
		global $frm_vars;

		$is_custom_range = self::field_is_custom_format_range( $field );

		if ( empty( $field['calc'] ) && ! $is_custom_range ) {
			return;
		}

		/**
		 * We should always pass this check for 'range' field to avoid an issue for ajax
		 * form submission in multi-page forms when slider is formatted as currency.
		 */
		if ( $field['type'] !== 'range' && FrmProForm::is_ajax_on( $form ) && FrmAppHelper::doing_ajax() && empty( $frm_vars['inplace_edit'] ) ) {
			return;
		}

		if ( ! isset( $frm_vars['calc_fields'] ) ) {
			$frm_vars['calc_fields'] = array();
		}

		$attributes = array(
			'field'          => $field,
			'form_id'        => $form->id,
			'parent_form_id' => $parent_form_id,
		);
		if ( $is_custom_range ) {
			$attributes['calc'] = '[' . absint( $field['id'] ) . ']';

			if ( empty( $field['custom_currency'] ) ) {
				// Make sure default currency is loaded if field does not use a custom currency.
				FrmProCurrencyHelper::add_currency_to_global( $field['parent_form_id'] );
			}
		}

		$frm_vars['calc_fields'][ $field['field_key'] ] = FrmProFormsHelper::get_calc_rule_for_field( $attributes );
	}

	/**
	 * @since 5.4.3
	 *
	 * @param array $field
	 * @return bool
	 */
	private static function field_is_custom_format_range( $field ) {
		return 'range' === $field['type'] && ! empty( $field['is_currency'] );
	}

	public static function build_field_class( $classes, $field ) {
		if ( 'inline' === $field['conf_field'] ) {
			$classes .= ' frm_conf_inline';
		} elseif ( 'below' === $field['conf_field'] ) {
			$classes .= ' frm_conf_below';
		}

		$columns = '';
		if ( FrmField::is_field_type( $field, 'checkbox' ) || FrmField::is_field_type( $field, 'radio' ) ) {
			if ( isset( $field['align'] ) ) {
				$columns = $field['align'];
			}

			if ( ! empty( $columns ) ) {
				$field_obj = FrmFieldFactory::get_field_type( $field['type'] );
				if ( is_callable( array( $field_obj, 'prepare_align_class' ) ) ) {
					$field_obj->prepare_align_class( $columns );
				}
			}
		}

		$classes = str_replace( ' frmstart ', ' frmstart ' . $columns . ' ', $classes );

		self::add_pro_field_class( $field, $classes );

		return $classes;
	}

	/**
	 * @since 6.5.1
	 *
	 * @param array  $field
	 * @param string $classes
	 *
	 * @return void
	 */
	private static function add_pro_field_class( $field, &$classes ) {
		$pro_fields = FrmField::pro_field_selection();
		if ( isset( $pro_fields[ $field['type'] ] ) && FrmProAddonsController::is_expired_outside_grace_period() ) {
			$classes .= ' frm_noallow frm_show_upgrade frm_show_expired_modal';
		}
	}

	public static function input_html( $field, $echo = true ) {
		$add_html = '';

		self::add_readonly_input_attributes( $field, $add_html );

		self::maybe_add_data_attribute_for_section( $field, $add_html );

		self::add_multiple_select_attribute( $field, $add_html );
		self::add_select_placeholder( $field, $add_html );

		if ( FrmAppHelper::is_admin_page('formidable' ) ) {
			if ( $echo ) {
				echo $add_html;
			}

			//don't continue if we are on the form builder page
			return $add_html;
		}

		FrmProLookupFieldsController::maybe_add_lookup_input_html( $field, $add_html );

		self::add_html5_input_attributes( $field, $add_html );
		self::add_checkbox_limit( $field, $add_html );

		$add_html .= self::setup_input_masks( $field );

		self::add_currency_field_attributes( $field, $add_html );

		self::add_html_autocomplete( $field, $add_html );

		if ( $echo ) {
			echo $add_html;
		}

		return $add_html;
	}

	/**
	 * Add autocomplete attribute to the field html.
	 *
	 * @since 5.4.1
	 *
	 * @param array  $field The field properties.
	 * @param string $add_html The field html.
	 * @return void
	 */
	private static function add_html_autocomplete( $field, &$add_html ) {
		if ( ! empty( $field['autocomplete'] ) ) {
			$add_html .= 'autocomplete="' . esc_attr( $field['autocomplete'] ) . '"';
		}
	}

	public static function setup_input_masks( $field ) {
		$html = '';
		$text_lookup = $field['type'] == 'lookup' && $field['data_type'] == 'text';
		$is_format_field = in_array( $field['type'], array( 'phone', 'text' ) ) || $text_lookup;
		if ( FrmProField::is_format_option_true_with_no_regex( $field ) && $is_format_field ) {
			$html = self::setup_input_mask( $field['format'] );
		}

		return $html;
	}

	public static function setup_input_mask( $format ) {
		global $frm_input_masks;
		$frm_input_masks[] = true;
		return ' data-frmmask="' . esc_attr( preg_replace( '/\d/', '9', $format ) ) . '"';
	}

	/**
	 * Add product attributes to fields in multi-paged forms.
	 *
	 * @since 4.04
	 */
	public static function add_currency_field_attributes( $field, &$add_html, $parent = null ) {

		$type = isset( $field['original_type'] ) ? $field['original_type'] : $field['type'];
		$is_product_field = in_array( $type, array( 'total', 'quantity', 'product' ) );
		if ( ! $is_product_field ) {
			return;
		}

		if ( $type === 'total' ) {
			$add_html .= ' data-frmtotal ';

		} elseif ( $type === 'quantity' ) {
			$product_field = FrmField::get_option( $field, 'product_field' );
			$add_html     .= ' data-frmproduct="' . esc_attr( json_encode( $product_field ) ) . '" ';

		} elseif ( $type === 'product' && 'hidden' === $field['type'] ) {
			// We want to do this only for fields that are hidden because it's
			// not their page, hence the check : 'hidden' === $field['type'].
			$price = empty( $field['value'] ) ? 0 : self::get_product_price( $field );

			/**
			 * This helps to know if this field should be included in the total calc of the current page by JS.
			 * Fields on higher pages aren't included, else you get error of invalid total on submission.
			 *
			 * For fields in a repeater or embedded form, their parents are used instead, else the page check may be incorrect.
			 */
			$use_this  = null === $parent ? $field : $parent;
			$higher_pg = FrmProFieldsHelper::field_on_page( $use_this, 'higher' ) ? 'data-frmhigherpg ' : '';

			$add_html .= ' data-frmprice="' . esc_attr( $price ) . '" ' . $higher_pg;
		}

		if ( FrmProFieldsHelper::is_on_skipped_page( $field, $parent ) ) {
			$add_html .= ' data-frmhidden="1" ';
		}
	}

	private static function get_product_price( $field ) {
		if ( is_array( $field['value'] ) ) {
			// '' is unlikely though, let's just do it to prevent warnings
			$value = isset( $field['opt_key'] ) && isset( $field['value'][ $field['opt_key'] ] ) ?
						$field['value'][ $field['opt_key'] ] : '';
		} else {
			$value = $field['value'];
		}

		$field_obj = FrmFieldFactory::get_field_object( $field['id'] );
		$price     = $field_obj->get_posted_price( $value );
		unset( $field_obj ); // lighten up on memory
		return $price;
	}

	/**
	 * Add readonly/disabled input attributes
	 *
	 * @since 2.02.06
	 * @param array $field
	 * @param string $add_html
	 */
	private static function add_readonly_input_attributes( $field, &$add_html ) {
		if ( FrmField::is_option_true( $field, 'read_only' ) && $field['type'] != 'hidden' && $field['type'] != 'lookup' ) {
			global $frm_vars;

			if ( ( isset( $frm_vars['readonly'] ) && $frm_vars['readonly'] == 'disabled' ) || ( current_user_can( 'frm_edit_entries' ) && FrmAppHelper::is_admin() ) ) {
				//not read only
			} elseif ( in_array( $field['type'], array( 'select', 'radio', 'checkbox', 'time' ) ) ) {
				$add_html .= ' disabled="disabled" ';
			} else {
				$add_html .= ' readonly="readonly" ';
			}
		}
	}

	/**
	 * Add multiple select attribute
	 *
	 * @since 2.02.06
	 * @param array $field
	 * @param string $add_html
	 */
	private static function add_multiple_select_attribute( $field, &$add_html ) {
		if ( FrmField::is_multiple_select( $field ) ) {
			$add_html .= ' multiple="multiple" ';
		}
	}

	/**
	 * @since 4.0
	 */
	private static function add_select_placeholder( $field, &$add_html ) {
		if ( ! FrmField::is_field_type( $field, 'select' ) ) {
			return;
		}

		$placeholder  = FrmField::get_option( $field, 'placeholder' );
		$autocomplete = FrmField::get_option( $field, 'autocom' );
		if ( $placeholder === '' && empty( $autocomplete ) ) {
			// The field doesn't need a placeholder.
			return;
		}

		$default = FrmField::is_multiple_select( $field ) ? __( 'Select options', 'formidable-pro' ) : __( 'Select an option', 'formidable-pro' );

		$add_html .= ' data-placeholder="' . esc_attr( ! empty( $placeholder ) ? $placeholder : $default ) . '" ';
	}

	/**
	 * Add a few HTML5 input attributes
	 *
	 * @since 2.02.06
	 * @param array $field
	 * @param string $add_html
	 */
	private static function add_html5_input_attributes( $field, &$add_html ) {
		global $frm_vars;
		$frm_settings = FrmAppHelper::get_settings();

		if ( $frm_settings->use_html ) {
			if ( FrmField::is_option_true( $field, 'autocom' ) && $field['type'] !== 'hidden' && FrmField::is_field_type( $field, 'select' ) ) {
				//add label for autocomplete fields
				$add_html .= ' data-placeholder=" "';
			}

			if ( in_array( $field['type'], array( 'url', 'email' ) ) ) {
				if ( ( ! isset($frm_vars['novalidate']) || ! $frm_vars['novalidate'] ) && ( $field['type'] != 'email' || ( isset($field['value']) && $field['default_value'] == $field['value'] ) ) ) {
					// add novalidate for drafts
					$frm_vars['novalidate'] = true;
				}
			}
		}
	}

	/**
	 * If the field has a limit set, add it to the HTML.
	 *
	 * @since 4.02
	 */
	private static function add_checkbox_limit( $field, &$add_html ) {
		$selections_limit = FrmField::get_option( $field, 'limit_selections' );

		if ( $selections_limit ) {
			$add_html .= ' data-frmlimit="' . esc_attr( $selections_limit ) . '" ';
		}
	}

	/**
	 * Add data-sectionid attribute for fields in section
	 *
	 * @since 2.01.0
	 * @param array $field
	 * @param string $add_html
	 */
	private static function maybe_add_data_attribute_for_section( $field, &$add_html ) {
		if ( FrmField::is_option_true_in_array( $field, 'in_section' ) ) {
			$add_html .= ' data-sectionid="' . $field['in_section'] . '"';
		}

		// TODO: Add data attribute for embedded form fields as well
	}

	public static function add_field_class( $class, $field ) {
		if ( FrmAppHelper::is_admin() || FrmField::is_read_only( $field ) ) {
			return $class;
		}

		$is_hidden = FrmField::get_field_type( $field ) === 'hidden';
		if ( ! $is_hidden && FrmField::is_option_true( $field, 'autocom' ) && FrmField::is_field_type( $field, 'select' ) && ! empty( $field['options'] ) ) {
			self::add_autocomplete_classes( $field, $class );
		}

		return $class;
	}

	/**
	* Add the autocomplete classes to a $class string
	*
	* @since 2.01.0
	*
	* @param array $field
	* @param string $class
	*/
	public static function add_autocomplete_classes( $field, &$class ) {
		 global $frm_vars;
		 $frm_vars['chosen_loaded'] = true;
		 $class .= ' frm_chzn';

		 $style = FrmStylesController::get_form_style( $field['form_id'] );
		 if ( $style && 'rtl' == $style->post_content['direction'] ) {
			$class .= ' chosen-rtl';
		 }
	}

	/**
	 * Add Other Option after click.
	 *
	 * @since 4.0
	 */
	public static function add_other_option() {
		FrmAppHelper::permission_check( 'frm_edit_forms' );
		check_ajax_referer( 'frm_ajax', 'nonce' );

		$id       = FrmAppHelper::get_post_param( 'field_id', 0, 'absint' );
		$opt_type = FrmAppHelper::get_post_param( 'opt_type', '', 'sanitize_text_field' );
		$opt_key  = FrmAppHelper::get_post_param( 'opt_key', 0, 'absint' );

		$field = FrmField::getOne( $id );
		$field_data = $field;
		$field      = (array) $field;

		$field['separate_value'] = isset( $field_data->field_options['separate_value'] ) ? $field_data->field_options['separate_value'] : 0;
		unset( $field_data );

		$field['html_name'] = 'item_meta[' . $field['id'] . ']';
		$field['options']   = array( 'other_' . $opt_key => __( 'Other', 'formidable-pro' ) );
		FrmFieldsHelper::show_single_option( $field );

		wp_die();
	}

	public static function options_form_before( $field ) {
		include( FrmProAppHelper::plugin_path() . '/classes/views/frmpro-fields/back-end/options-before.php' );
	}

	public static function options_form_top( $field, $display, $values ) {
		if ( $display['conf_field'] && ! in_array( $field['type'], array( 'email', 'password' ) ) ) {
			_deprecated_function( __FUNCTION__, '4.0', 'FrmFieldType->show_primary_options' );
			include( FrmProAppHelper::plugin_path() . '/classes/views/frmpro-fields/back-end/confirmation.php' );
		}
	}

	/**
	 * @since 4.0
	 *
	 * @param array $args {
	 *     @type array $display
	 *     @type array $field
	 * }
	 * @return void
	 */
	public static function advanced_field_options( $args ) {
		$display = $args['display'];
		$field   = $args['field'];

		$is_checkbox = FrmField::is_field_type( $field, 'checkbox' );
		$is_radio    = FrmField::is_field_type( $field, 'radio' );
		if ( $display['type'] === 'radio' || $display['type'] === 'checkbox' || $is_radio || $is_checkbox ) {
			self::alignment_setting( $args );
		}

		if ( ! empty( $display['prefix'] ) ) {
			include FrmProAppHelper::plugin_path() . '/classes/views/frmpro-fields/back-end/prepend-options.php';
		}

		if ( $field['type'] === 'divider' && ! empty( $field['repeat'] ) ) {
			include FrmProAppHelper::plugin_path() . '/classes/views/frmpro-fields/back-end/repeat-options.php';
		}

		if ( 'textarea' === $field['type'] ) {
			include FrmProAppHelper::plugin_path() . '/classes/views/frmpro-fields/back-end/auto-grow.php';
			include FrmProAppHelper::plugin_path() . '/classes/views/frmpro-fields/back-end/content-limit.php';
		}

		if ( ! empty( $display['autocomplete'] ) ) {
			self::show_autocomplete_option( $args['field'] );
		}

		if ( ! empty( $display['visibility'] ) ) {
			self::show_visibility_option( $args['field'] );
		}
	}

	/**
	 * @since 4.0
	 * @param array $args - includes 'field'
	 */
	public static function alignment_setting( $args ) {
		$field   = $args['field'];
		$columns = array(
			'block'         => __( 'One Column', 'formidable-pro' ),
			'frm_two_col'   => __( 'Two Columns', 'formidable-pro' ),
			'frm_three_col' => __( 'Three Columns', 'formidable-pro' ),
			'frm_four_col'  => __( 'Four Columns', 'formidable-pro' ),
			'inline'        => __( 'Inline Options', 'formidable-pro' ),
		);

		include( FrmProAppHelper::plugin_path() . '/classes/views/frmpro-fields/back-end/alignment.php' );
	}

	/**
	 * @since 4.0
	 * @param array $args - includes 'field', 'display', and 'values'
	 */
	public static function options_form_after( $args ) {
		if ( isset( $args['display']['logic'] ) && $args['display']['logic'] ) {
			self::show_conditional_logic_option( $args['field'] );
		}
	}

	/**
	 * Add calc details.
	 *
	 * @since 4.0
	 * @param array $types
	 * @param array $atts Includes 'display'
	 */
	public static function default_value_types( $types, $atts ) {
		$types['calc']['class'] = ''; // Remove upgrade links.
		$types['calc']['data']  = array(
			'frmshow' => '#calc-for-',
		);

		if ( ! isset( $atts['display']['calc'] ) || ! $atts['display']['calc'] ) {
			unset( $types['calc'] );
		}

		$types['get_values_field']['class'] = 'frm-show-inline-modal'; // Remove upgrade links.
		$types['get_values_field']['data']  = array(
			'open'    => 'frm-lookup-box-',
			'frmshow' => '.frm-lookup-box-',
			'frmhide' => '.frm-inline-modal,.default-value-section-',
		);

		if ( ! isset( $atts['display']['autopopulate'] ) || ! $atts['display']['autopopulate'] ) {
			unset( $types['get_values_field'] );
		}

		// In-product education for Date calculation.
		if ( ! function_exists( 'frm_dates_autoloader' ) && isset( $atts['display']['type'] ) && 'date' === $atts['display']['type'] ) {
			$types['date_calc'] = array(
				'class' => 'frm_noallow',
				'title' => __( 'Default Value (Date Calculation)', 'formidable-pro' ),
				'icon'  => 'frm_icon_font frm_calculator_icon',
				'data'  => FrmProFieldDate::get_dates_add_on_upgrade_link_data(),
			);
		}

		return $types;
	}

	/**
	 * @since 4.0
	 * @param array $args - includes 'field', 'display', 'default_value_types'.
	 */
	public static function more_default_values( $args ) {
		$field               = $args['field'];
		$default_value_types = $args['default_value_types'];

		self::maybe_include_default_values( $field );

		if ( ! empty( $args['display']['calc'] ) ) {
			include FrmProAppHelper::plugin_path() . '/classes/views/frmpro-fields/back-end/calculations.php';
		}

		if ( ! empty( $args['display']['autopopulate'] ) ) {
			include FrmProAppHelper::plugin_path() . '/classes/views/frmpro-fields/back-end/autopopulate.php';
		}
	}

	/**
	 * Use a list of values in the field instead of smart values for
	 * post categories and dynamic fields.
	 *
	 * @since 4.01.02
	 *
	 * @param array $field
	 */
	private static function maybe_include_default_values( $field ) {
		$is_taxonomy = isset( $field['post_field'] ) && $field['post_field'] === 'post_category';
		$is_dynamic  = $field['type'] === 'data';

		if ( ! $is_taxonomy && ! $is_dynamic ) {
			return;
		}

		FrmFieldsHelper::inline_modal(
			array(
				'title'    => __( 'Default Value', 'formidable' ),
				'callback' => array( 'FrmProFieldsController', 'default_dynamic_options' ),
				'args'     => $field,
				'id'       => 'frm-tax-box-' . $field['id'],
				'class'    => 'frm-tax-modal frm-tax-box-' . $field['id'],
			)
		);
	}

	/**
	 * Show a list of options in a dynamic field or category field
	 * in order to set the default value.
	 *
	 * @since 4.01.02
	 *
	 * @param array $field
	 */
	public static function default_dynamic_options( $field ) {
		$tags = $field['options'];
		include( FrmProAppHelper::plugin_path() . '/classes/views/frmpro-fields/back-end/default-terms.php' );
	}

	/**
	 * @since 4.0
	 * @param array $args - includes 'field'
	 */
	public static function calculation_settings( $args ) {
		$field = $args['field'];
		include( FrmProAppHelper::plugin_path() . '/classes/views/frmpro-fields/back-end/calculation-settings.php' );
	}

	/**
	 * Display the visibility option
	 *
	 * @since 2.02.06
	 * @param array $field
	 */
	public static function show_visibility_option( $field ) {
		include( FrmProAppHelper::plugin_path() . '/classes/views/frmpro-fields/back-end/visibility.php' );
	}

	/**
	 * Display the autocomplete option
	 *
	 * @since 5.4.1
	 *
	 * @param array $field
	 * @return void
	 */
	public static function show_autocomplete_option( $field ) {
		include FrmProAppHelper::plugin_path() . '/classes/views/frmpro-fields/back-end/autocomplete.php';
	}

	/**
	 * Display the conditional logic option
	 *
	 * @since 2.02.06
	 * @param array $field
	 */
	public static function show_conditional_logic_option( $field ) {
		$form_fields = false;
		if ( ! empty( $field['hide_field'] ) && is_array( $field['hide_field'] ) ) {
			$form_id = isset( $field['parent_form_id'] ) ? $field['parent_form_id'] : $field['form_id'];
			$form_fields = FrmField::get_all_for_form( $form_id );
		}
		include( FrmProAppHelper::plugin_path() . '/classes/views/frmpro-fields/back-end/logic.php' );
	}

	public static function get_field_selection() {
		FrmAppHelper::permission_check('frm_view_forms');
		check_ajax_referer( 'frm_ajax', 'nonce' );

		$current_field_id = FrmAppHelper::get_post_param( 'field_id', '', 'absint' );
		$form_id = FrmAppHelper::get_post_param( 'form_id', '', 'sanitize_text_field' );

		if ( is_numeric( $form_id ) ) {
			$selected_field = '';
			$fields = self::get_field_selection_fields( $form_id );
			if ( $fields ) {
				require( FrmProAppHelper::plugin_path() . '/classes/views/frmpro-fields/field-selection.php' );
			}
		} else {
			$selected_field = $form_id;

			if ( $selected_field == 'taxonomy' ) {
				echo '<div class="frm-inline-message">' . esc_html__( 'Select a taxonomy on the Form Actions tab of the Form Settings page', 'formidable-pro' ) . '</div>';
				echo '<input type="hidden" name="field_options[form_select_' . esc_attr( $current_field_id ) . ']" value="taxonomy" />';
			}
		}

		wp_die();
	}

	/**
	 * Gets fields for field selection.
	 *
	 * @since 5.0.04
	 *
	 * @param int $form_id Form ID.
	 * @return object[]
	 */
	public static function get_field_selection_fields( $form_id ) {
		$fields = FrmField::get_all_for_form( $form_id );
		if ( $fields ) {
			/**
			 * Allows modifying fields in Field selection of Dynamic field.
			 *
			 * @since 5.0.04
			 *
			 * @param array $fields The fields.
			 * @param array $args   Includes `form_id`.
			 */
			$fields = apply_filters( 'frm_pro_fields_in_dynamic_selection', $fields, compact( 'form_id' ) );
		}

		return $fields;
	}

	/**
	 * Get the field value selector for field or action logic
	 */
	public static function get_field_values() {
		FrmAppHelper::permission_check('frm_view_forms');
		check_ajax_referer( 'frm_ajax', 'nonce' );

		$selector_args = array(
			'value' => '',
		);

		$selector_args['html_name'] = sanitize_text_field( $_POST['name'] );
		if ( empty( $selector_args['html_name'] ) || $selector_args['html_name'] == 'undefined' ) {
			$selector_args['html_name'] = 'field_options[hide_opt_' . absint( $_POST['current_field'] ) . '][]';
		}

		if ( FrmAppHelper::get_param( 'form_action', '', 'get', 'sanitize_text_field' ) == 'update_settings' ) {
			$selector_args['source'] = 'form_actions';
		} else {
			$field_type = sanitize_text_field( $_POST['t'] );
			$selector_args['source'] = ! empty( $field_type ) ? $field_type : 'unknown';
		}

		FrmFieldsHelper::display_field_value_selector( absint( $_POST['field_id'] ), $selector_args );

		wp_die();
	}

	public static function get_dynamic_widget_opts() {
		check_ajax_referer( 'frm_ajax', 'nonce' );

		$form_id = get_post_meta( (int) $_POST['display_id'], 'frm_form_id', true );
		if ( ! $form_id ) {
			wp_die();
		}

		$fields = FrmField::getAll( array( 'fi.type not' => FrmField::no_save_fields(), 'fi.form_id' => $form_id ), 'field_order');

		$options = array(
			'titleValues'   => array(),
			'catValues'     => array(),
		);

		foreach ( $fields as $field ) {
			$options['titleValues'][ $field->id ] = $field->name;
			if ( $field->type == 'select' || $field->type == 'radio' ) {
				$options['catValues'][ $field->id ] = $field->name;
			}
			unset($field);
		}

		echo json_encode($options);

		wp_die();
	}

	/**
	 * @param string $field_id
	 * @param array  $options
	 * @return void
	 */
	public static function date_field_js( $field_id, $options ) {
		if ( empty( $options['unique'] ) ) {
			return;
		}

		$defaults = array(
			'entry_id'   => 0,
			'start_year' => '-10',
			'end_year'   => '+10',
			'locale'     => '',
			'unique'     => 0,
			'field_id'   => 0,
		);

		$options = wp_parse_args( $options, $defaults );

		global $wpdb;

		$field = FrmField::getOne( $options['field_id'] );

		if ( isset( $field->field_options['post_field'] ) && $field->field_options['post_field'] != '' ) {
			$query = array( 'post_status' => array( 'publish', 'draft', 'pending', 'future', 'private' ) );
			if ( $field->field_options['post_field'] === 'post_custom' ) {
				$get_field             = 'meta_value';
				$get_table             = $wpdb->postmeta . ' pm LEFT JOIN ' . $wpdb->posts . ' p ON (p.ID=pm.post_id)';
				$query['meta_value !'] = '';
				$query['meta_key']     = $field->field_options['custom_field'];
			} else {
				$get_field = sanitize_title( $field->field_options['post_field'] );
				$get_table = $wpdb->posts;
			}

			$post_dates = FrmDb::get_col( $get_table, $query, $get_field );
		}

		if ( ! is_numeric( $options['entry_id'] ) ) {
			$disabled = wp_cache_get($options['field_id'], 'frm_used_dates');
		}

		if ( ! isset($disabled) || ! $disabled ) {
			$disabled = FrmDb::get_col( $wpdb->prefix . 'frm_item_metas', array( 'field_id' => $options['field_id'], 'item_id !' => $options['entry_id'] ), 'meta_value' );
		}

		if ( isset($post_dates) && $post_dates ) {
			$disabled = array_unique(array_merge( (array) $post_dates, (array) $disabled ));
		}

		/**
		 * Allows additional logic to be added to selectable dates
		 * To prevent weekends from being selectable, 'true' would be changed to '(day != 0 && day != 6)'
		 *
		 * @since 2.0
		 */
		$selectable_response = apply_filters( 'frm_selectable_dates', 'true', compact( 'field', 'options' ) );

		$disabled = apply_filters('frm_used_dates', $disabled, $field, $options);

		if ( ! is_numeric( $options['entry_id'] ) && ! empty( $disabled ) ) {
			wp_cache_set( $options['field_id'], $disabled, 'frm_used_dates' );
		}

		// This function is added in v1.04 of the Dates add on. Before that version, these filters are not supported.
		if ( is_callable( 'FrmDatesAppHelper::plugin_version' ) ) {

			add_filter(
				'frm_dates_selectable_response',
				function( $is_enabled, $filter_field ) use ( $selectable_response, $field ) {

					if ( ! $field->field_options['unique'] ) {
						return $is_enabled;
					}
					return $filter_field->field_key !== $field->field_key ? $is_enabled : $selectable_response;
				},
				10,
				2
			);

			add_filter(
				'frm_dates_disabled',
				function( $blackout_dates, $filter_field ) use ( $disabled, $field ) {
					return $filter_field->field_key !== $field->field_key ? $blackout_dates : array_merge( $blackout_dates, $disabled );
				},
				10,
				2
			);

			return;
		}

		$js_vars = 'var m=(date.getMonth()+1),d=date.getDate(),y=date.getFullYear(),day=date.getDay();';
		if ( empty( $disabled ) ) {
			if ( $selectable_response != 'true' ) {
				// If the filter has been used, include it
				echo ',beforeShowDay:function(date){' . $js_vars . 'return [' . $selectable_response . '];}';
			}

			return;
		}

		$formatted = array();
		foreach ( $disabled as $dis ) {
			// format to match javascript dates
			$formatted[] = gmdate( 'Y-n-j', strtotime( $dis ) );
		}

		$disabled = $formatted;
		unset( $formatted );

		echo ',beforeShowDay: function(date){' . $js_vars . 'var disabled=' . json_encode( $disabled ) . ';if($.inArray(y+"-"+m+"-"+d,disabled) != -1){return [false];} return [' . $selectable_response . '];}';
	}

	/**
	 * @since 2.0.23
	 */
	public static function maybe_make_field_optional( $required, $field ) {
		if ( $required && ! FrmAppHelper::is_admin_page('formidable' ) ) {
			global $frm_vars;
			$is_editing = isset( $frm_vars['editing_entry'] ) && $frm_vars['editing_entry'] && is_numeric( $frm_vars['editing_entry'] );
			if ( $is_editing ) {
				$optional_on_edit = apply_filters( 'frm_optional_fields_on_edit', array( 'password', 'credit_card' ) );
				if ( in_array( $field['type'], (array) $optional_on_edit ) ) {
					$entry = FrmEntry::getOne( $frm_vars['editing_entry'] );
					if ( $entry && $entry->form_id === $field['form_id'] && ! $entry->is_draft ) {
						$required = false;
					}
				}
			}
		}
		return $required;
	}

	/**
	 * @since 5.2.04
	 */
	public static function ajax_get_data_arr() {
		$post_data = FrmAppHelper::get_param( 'postData' );
		$response  = array();

		foreach ( $post_data as $data ) {
			if ( ! isset( $data['entry_id'] ) || ! isset( $data['current_field'] ) || ! isset( $data['hide_id'] ) ) {
				continue;
			}

			$result_str = '';

			$entry_id        = is_array( $data['entry_id'] ) ? array_map( 'intval', $data['entry_id'] ) : intval( $data['entry_id'] );
			$current_field   = intval( $data['current_field'] );
			$hidden_field_id = sanitize_text_field( wp_unslash( $data['hide_id'] ) );

			$current    = FrmField::getOne( $current_field );
			$data_field = FrmField::getOne( $current->field_options['form_select'] );

			$meta_value = self::get_meta_value_for_ajax_handler( $entry_id, $data_field );

			if ( $meta_value === null ) {
				$response[] = $result_str;
				continue;
			}

			$data_display_opts = apply_filters( 'frm_display_data_opts', array( 'html' => true, 'wpautop' => false ) );

			$value = FrmFieldsHelper::get_display_value( $meta_value, $data_field, $data_display_opts );
			if ( is_array( $value ) ) {
				$value = implode( ', ', $value );
			}

			if ( is_array( $meta_value ) ) {
				$meta_value = implode( ', ', $meta_value );
			}

			$current_field = (array) $current;
			foreach ( $current->field_options as $o => $v ) {
				if ( ! isset( $current_field[ $o ] ) ) {
					$current_field[ $o ] = $v;
				}
				unset($o, $v);
			}

			// Set up HTML ID and HTML name
			$html_id = '';
			$field_name = 'item_meta';
			FrmProFieldsHelper::get_html_id_from_container($field_name, $html_id, (array) $current, $hidden_field_id);

			$on_current_page = $data['on_current_page'];
			$on_current_page = ( 'true' === $on_current_page );

			if ( $on_current_page && FrmProFieldsHelper::is_field_visible_to_user( $current ) ) {
				if ( FrmAppHelper::is_not_empty_value( $value ) && $value !== false ) {
					$result_str .= apply_filters(
						'frm_show_it',
						'<p class="frm_show_it">' . $value . "</p>\n",
						$value,
						array(
							'field'    => $data_field,
							'value'    => $meta_value,
							'entry_id' => $entry_id,
						)
					);
				}
				$result_str .= '<input type="hidden" id="' . esc_attr( $html_id ) . '" name="' . esc_attr( $field_name ) . '" value="' . esc_attr( $value ) . '" ' . do_action( 'frm_field_input_html', $current_field, false ) . '/>';
			} else {
				$result_str .= esc_attr( $value );
			}

			$response[] = $result_str;

		}

		wp_send_json( $response );
	}

	/**
	 * @deprecated 5.2.03
	 */
	public static function ajax_get_data() {
		_deprecated_function( __METHOD__, '5.2.03' );
		//check_ajax_referer( 'frm_ajax', 'nonce' );

		$entry_id = self::get_posted_entry_ids();
		$current_field = FrmAppHelper::get_param( 'current_field', '', 'get', 'absint' );
		$hidden_field_id = FrmAppHelper::get_param( 'hide_id', '', 'get', 'sanitize_text_field' );

		self::get_data_for_ajax_handler( $entry_id, $current_field, $hidden_field_id );

		wp_die();
	}

	/**
	 * Gets meta value for ajax handler.
	 *
	 * @since 5.2.04
	 * @since 5.2.05 The first parameter can be int or array.
	 *
	 * @param int|array $entry_id   Entry ID or array of entry IDs.
	 * @param object    $data_field The data field.
	 * @return string|array
	 */
	private static function get_meta_value_for_ajax_handler( $entry_id, $data_field ) {
		if ( is_array( $entry_id ) ) {
			$meta_value = array();
			foreach ( $entry_id as $eid ) {
				$new_meta = FrmProEntryMetaHelper::get_post_or_meta_value( $eid, $data_field );
				if ( $new_meta ) {
					foreach ( (array) $new_meta as $nm ) {
						array_push( $meta_value, $nm );
						unset( $nm );
					}
				}
				unset( $new_meta, $eid );
			}
			$meta_value = array_unique( $meta_value );
		} else {
			$meta_value = FrmProEntryMetaHelper::get_post_or_meta_value( $entry_id, $data_field );
		}

		return $meta_value;
	}

	/**
	 * @since 5.2.04
	 */
	private static function get_data_for_ajax_handler( $entry_id, $current_field, $hidden_field_id ) {
		$current    = FrmField::getOne( $current_field );
		$data_field = FrmField::getOne( $current->field_options['form_select'] );

		$meta_value = self::get_meta_value_for_ajax_handler( $entry_id, $data_field );

		if ( null === $meta_value ) {
			wp_die();
		}

		$data_display_opts = apply_filters( 'frm_display_data_opts', array( 'html' => true, 'wpautop' => false ) );
		$value             = FrmFieldsHelper::get_display_value( $meta_value, $data_field, $data_display_opts );
		if ( is_array( $value ) ) {
			$value = implode( ', ', $value );
		}

		if ( is_array( $meta_value ) ) {
			$meta_value = implode( ', ', $meta_value );
		}

		$current_field = (array) $current;
		foreach ( $current->field_options as $o => $v ) {
			if ( ! isset( $current_field[ $o ] ) ) {
				$current_field[ $o ] = $v;
			}
			unset( $o, $v );
		}

		// Set up HTML ID and HTML name
		$html_id    = '';
		$field_name = 'item_meta';
		FrmProFieldsHelper::get_html_id_from_container( $field_name, $html_id, (array) $current, $hidden_field_id );

		$on_current_page = FrmAppHelper::get_param( 'on_current_page', 'true', 'get', 'sanitize_text_field' );
		$on_current_page = ( $on_current_page == 'true' );

		if ( $on_current_page && FrmProFieldsHelper::is_field_visible_to_user( $current ) ) {
			if ( FrmAppHelper::is_not_empty_value( $value ) && $value !== false ) {
				echo apply_filters( 'frm_show_it', "<p class='frm_show_it'>" . $value . "</p>\n", $value, array( 'field' => $data_field, 'value' => $meta_value, 'entry_id' => $entry_id ) );
			}
			echo '<input type="hidden" id="' . esc_attr( $html_id ) . '" name="' . esc_attr( $field_name ) . '" value="' . esc_attr( $value ) . '" ' . do_action( 'frm_field_input_html', $current_field, false ) . '/>';
		} else {
			echo esc_attr( $value );
		}
	}

	/**
	 * @since 2.05.04
	 */
	private static function get_posted_entry_ids() {
		$entry_id = FrmAppHelper::get_param( 'entry_id', '', 'get', 'sanitize_text_field' );
		if ( is_array( $entry_id ) ) {
			$entry_id = implode(',', $entry_id);
		}
		return trim( $entry_id, ',' );
	}

	/**
	* Get the HTML for a dependent Dynamic field when the parent changes
	*/
	public static function ajax_data_options() {
		//check_ajax_referer( 'frm_ajax', 'nonce' );

		$args = array(
			'trigger_field_id' => FrmAppHelper::get_param( 'trigger_field_id', '', 'post', 'absint' ),
			'entry_id' => FrmAppHelper::get_param( 'entry_id', '', 'post', 'sanitize_text_field' ),
			'field_id' => FrmAppHelper::get_param( 'field_id', '', 'post', 'absint' ),
			'container_id' => FrmAppHelper::get_param( 'container_id', '', 'post', 'sanitize_title' ),
			'default_value' => FrmAppHelper::get_param( 'default_value', '', 'post', 'sanitize_title' ),
			'prev_val' => FrmAppHelper::get_param( 'prev_val', '', 'post', 'absint' ),
		);

		if ( $args['entry_id'] == '' ) {
			wp_die();
		}

		if ( ! is_array( $args['entry_id'] ) ) {
			$entry_id = explode( ',', $args['entry_id'] );
		}

		$args['field_data'] = FrmField::getOne( $args['field_id'] );

		$field = self::initialize_dependent_dynamic_field( $args );

		if ( is_numeric( $args['field_data']->field_options['form_select'] ) ) {
			// If Dynamic field is pulling options from a regular field
			self::get_dependent_dynamic_field_options( $args, $field );

		} else if ( $args['field_data']->field_options['form_select'] == 'taxonomy' ) {
			// If Dynamic field is pulling options from a taxonomy
			self::get_dependent_category_field_options( $args, $field );

		}

		self::get_dependent_dynamic_field_value( $args['prev_val'], $field );

		// Set up HTML ID and HTML name
		$input_args = array(
			'field_name'    => 'item_meta',
			'field_id'      => $args['field_data']->id,
			'field_plus_id' => '',
			'section_id'    => '',
			'html_id'       => '',
		);

		FrmProFieldsHelper::get_html_id_from_container( $input_args['field_name'], $input_args['html_id'], $field, $args['container_id'] );

		if ( FrmField::is_multiple_select( $args['field_data'] ) ) {
			$input_args['field_name'] .= '[]';
		}

		$field_obj = FrmFieldFactory::get_field_type( 'data', $field );
		echo $field_obj->include_front_field_input( $input_args, array() );

		wp_die();
	}

	/**
	* Initialize the field array for a dependent dynamic field
	*
	* @param array $args
	* @return array $field
	*/
	private static function initialize_dependent_dynamic_field( $args ) {
		$field = FrmProFieldsHelper::initialize_array_field( $args['field_data'], $args );
		return $field;
	}

	/**
	* Get the options for a dependent Dynamic field
	*
	* @since 2.0.16
	* @param array $args
	* @param array $field
	*/
	private static function get_dependent_dynamic_field_options( $args, &$field ) {
		$linked_field = FrmField::getOne( $args['field_data']->field_options['form_select'] );

		$field['options'] = array();

		$metas = array();
		FrmProEntryMetaHelper::meta_through_join( $args['trigger_field_id'], $linked_field, $args['entry_id'], $args['field_data'], $metas );
		$metas = wp_unslash( $metas );

		if ( FrmProDynamicFieldsController::include_blank_option( $metas, $args['field_data'] ) ) {
			$field['options'][''] = '';
		}

		foreach ( $metas as $meta ) {
			$field['options'][ $meta->item_id ] = FrmEntriesHelper::display_value(
					$meta->meta_value,
					$linked_field,
					array(
						'type'          => $linked_field->type,
						'show_icon'     => true,
						'show_filename' => false,
					)
			);
			unset($meta);
		}

		// change the form_select value so the filter doesn't override the values
		$args['field_data']->field_options['form_select'] = 'filtered_' . $args['field_data']->field_options['form_select'];

		FrmFieldsHelper::prepare_new_front_field( $field, $args['field_data'] );

		// Sort the options
		$pass_args = array( 'metas' => $metas, 'field' => $linked_field, 'dynamic_field' => $field );
		$field['options'] = apply_filters( 'frm_data_sort', $field['options'], $pass_args );
	}

	/**
	* Get the options for a dependent Dynamic category field
	*
	* @since 2.0.16
	* @param array $args
	* @param array $field
	*/
	private static function get_dependent_category_field_options( $args, &$field ) {
		if ( $args['entry_id'] == 0 ) {
			wp_die();
		}

		if ( is_array( $args['entry_id'] ) ) {
			$zero = array_search(0, $args['entry_id']);
			if ( $zero !== false ) {
				unset( $args['entry_id'][ $zero ] );
			}
			if ( empty( $args['entry_id'] ) ) {
				wp_die();
			}
		}

		FrmFieldsHelper::prepare_new_front_field( $field, $args['field_data'] );

		$cat_ids = array_keys($field['options']);

		$cat_args = array( 'include' => implode(',', $cat_ids), 'hide_empty' => false);

		$post_type = FrmProFormsHelper::post_type( $args['field_data']->form_id );
		$cat_args['taxonomy'] = FrmProAppHelper::get_custom_taxonomy($post_type, $args['field_data']);
		if ( ! $cat_args['taxonomy'] ) {
			wp_die();
		}

		$cats = get_categories($cat_args);
		foreach ( $cats as $cat ) {
			if ( ! in_array( $cat->parent, (array) $args['entry_id'] ) ) {
				unset( $field['options'][ $cat->term_id ] );
			}
		}

		if ( count($field['options']) == 1 && reset($field['options']) == '' ) {
			wp_die();
		}

		// Sort the options
		$field['options'] = apply_filters( 'frm_data_sort', $field['options'], array( 'dynamic_field' => $field ) );
	}

	/**
	* Get the field value for a dependent dynamic field
	*
	* @since 2.0.16
	* @param array $prev_val
	* @param array $field
	*/
	private static function get_dependent_dynamic_field_value( $prev_val, &$field ) {

		// Set the value to the previous value if it was set. Otherwise, set to default value.
		if ( $prev_val ) {
			$prev_val = array_unique( $prev_val );
			$field['value'] = $prev_val;
		} else {
			$field['value'] = $field['default_value'];
		}

		// Unset the field value if it isn't an option
		if ( $field['value'] ) {
			$field['value'] = (array) $field['value'];
			foreach ( $field['value'] as $key => $field_val ) {
				if ( ! array_key_exists( $field_val, $field['options'] ) ) {
					unset( $field['value'][ $key ] );
				}
			}
		}

		if ( is_array( $field['value'] ) && empty( $field['value'] ) ) {
			$field['value'] = '';
		}

		// If we have a radio field, set the field value to a string
		if ( $field['data_type'] == 'radio' && is_array( $field['value'] ) ) {
			$field['value'] = reset( $field['value'] );
		}
	}

	/**
	 * Order the values in a Dynamic or Lookup field.
	 *
	 * @since 5.5.2
	 *
	 * @param array $options The field options (choices)
	 * @param array $args
	 * @return array
	 */
	public static function order_values( $options, $args = array() ) {
		if ( ! $options || empty( $args['dynamic_field'] ) || ! isset( $args['dynamic_field']['option_order'] ) ) {
			return $options;
		}

		$order = $args['dynamic_field']['option_order'];
		if ( $order === 'ascending' || $order === 'descending' ) {
			if ( class_exists( 'Collator' ) ) {
				$locale   = get_locale();
				$collator = new Collator( $locale );
				$collator->asort( $options );
			} else {
				natcasesort( $options );
			}

			if ( $order === 'descending' ) {
				$options = array_reverse( $options, true );
			}
		}

		return $options;
	}

	/**
	 * Add an option at the top of the media library page
	 * to show the unattached Formidable files based on user role.
	 *
	 * @since 2.02
	 */
	public static function filter_media_library_link() {
		global $current_screen;
		if ( $current_screen && 'upload' == $current_screen->base && current_user_can('frm_edit_entries') ) {
			echo '<label for="frm-attachment-filter" class="screen-reader-text">';
			echo esc_html__( 'Show form uploads', 'formidable-pro' );
			echo '</label>';

			$filtered = FrmAppHelper::get_param( 'frm-attachment-filter', '', 'get', 'absint' );
			echo '<select name="frm-attachment-filter" id="frm-attachment-filter">';
			echo '<option value="">' . esc_html__( 'Hide form uploads', 'formidable-pro' ) . '</option>';
			echo '<option value="1" ' . selected( $filtered, 1 ) . '>' . esc_html__( 'Show form uploads', 'formidable-pro' ) . '</option>';
			echo '</select>';
		}
	}

	/**
	 * If this file is a Formidable file,
	 * temp redirect to the home page
	 *
	 * @since 2.02
	 */
	public static function redirect_attachment() {
		global $post;

		if ( ! is_object( $post ) || ! is_attachment() || absint( $post->post_parent ) >= 1 || ! FrmProFileField::is_formidable_file( $post->ID ) ) {
			return;
		}

		$user_has_file_access = current_user_can('frm_edit_entries') && FrmProFileField::user_has_permission( $post->ID );

		if ( ! $user_has_file_access ) {
			wp_redirect( get_bloginfo('wpurl'), 302 );
			die();
		}
	}

	/**
	 * Check for old temp files and delete them
	 *
	 * @since 2.02
	 */
	public static function delete_temp_files() {
		remove_action( 'pre_get_posts', 'FrmProFileField::filter_media_library', 99 );

		$cutoff_period    = apply_filters( 'frm_delete_temp_files_period', '-3 hours' );
		$timestamp_cutoff = gmdate( 'Y-m-d H:i:s', strtotime( $cutoff_period ) );
		$old_uploads      = get_posts(
			array(
				'post_type'      => 'attachment',
				'posts_per_page' => 50,
				'date_query'     => array(
					'column' => 'post_date_gmt',
					'before' => $timestamp_cutoff,
				),
				'meta_query'     => array(
					array(
						'key'     => '_frm_temporary',
						'compare' => 'EXISTS',
					),
				),
				'post_parent'    => 0,
			)
		);

		foreach ( $old_uploads as $upload ) {
			// double check in case other plugins have changed the query
			if ( FrmProFileField::file_is_temporary( $upload->ID ) ) {
				wp_delete_attachment( $upload->ID, true );
			}
		}

		add_action( 'pre_get_posts', 'FrmProFileField::filter_media_library', 99 );
	}

	public static function ajax_upload() {
		// Skip nonce for caching.
		$response = FrmProFileField::ajax_upload();

		if ( ! empty( $response['errors'] ) ) {
			status_header( 403 );
			$status = 403;
			echo implode( ' ', $response['errors'] );
		} else {
			$status = 200;
			echo json_encode( $response['media_ids'] );
		}

		wp_die( '', '', array( 'response' => $status ) );
	}

	/**
	 * Allow more field types for switching.
	 *
	 * @since 4.05
	 *
	 * @return array
	 */
	public static function single_input_fields( $fields ) {
		$fields[] = 'range';
		return $fields;
	}

	public static function _logic_row() {
		check_ajax_referer( 'frm_ajax', 'nonce' );
		FrmAppHelper::permission_check('frm_edit_forms', 'show');

		$meta_name = FrmAppHelper::get_post_param( 'meta_name', '', 'absint' );
		$field_id = FrmAppHelper::get_post_param( 'field_id', '', 'absint');
		$form_id = FrmAppHelper::get_post_param( 'form_id', '', 'absint' );
		$hide_field = '';

		$field = FrmField::getOne($field_id);
		$field = FrmFieldsHelper::setup_edit_vars($field);

		if ( $field['form_id'] != $form_id ) {
			$field['parent_form_id'] = $form_id;
		}

		if ( ! isset( $field['hide_field_cond'][ $meta_name ] ) ) {
			$field['hide_field_cond'][ $meta_name ] = '==';
		}

		$form_fields = self::get_live_fields( $form_id );

		include(FrmProAppHelper::plugin_path() . '/classes/views/frmpro-fields/_logic_row.php');
		wp_die();
	}

	/**
	 * Merge fields from DB with live field list.
	 *
	 * @since 4.0
	 */
	private static function get_live_fields( $form_id ) {
		$form_fields = FrmField::get_all_for_form( $form_id );
		$field_names = FrmAppHelper::get_param( 'fields', '', 'post', 'sanitize_text_field' );
		if ( empty( $field_names ) ) {
			return $form_fields;
		}

		$fields = array();
		foreach ( $field_names as $field ) {
			if ( ! empty( $field->type ) ) {
				$fields[ $field['fieldId'] ] = $field;
			}
		}

		foreach ( $form_fields as $k => $form_field ) {
			if ( ! isset( $fields[ $form_field->id ] ) ) {
				continue;
			}
			$form_fields[ $k ]->type = $fields[ $form_field->id ]['fieldType'];
			$form_fields[ $k ]->name = $fields[ $form_field->id ]['fieldName'];
		}

		return $form_fields;
	}

	public static function create_multiple_fields( $new_field, $form_id ) {
		// $args = compact('field_data', 'form_id', 'field');
		if ( empty($new_field) || $new_field['type'] != 'divider' ) {
			return;
		}

		// Add an "End section" when a section field is created
		FrmFieldsController::include_new_field('end_divider', $form_id);
	}

	/**
	 * Set the form id for the repeating section and any fields inside it
	 *
	 * @since 2.0
	 * @deprecated 3.06.01
	 */
	public static function toggle_repeat() {
		_deprecated_function( __METHOD__, '3.06.01' );

		wp_die();
	}

	/**
	 * Update a field after dragging and dropping it on the form builder page
	 *
	 * @since 2.0.24
	 */
	public static function update_field_after_move() {
		FrmAppHelper::permission_check( 'frm_edit_forms' );
		check_ajax_referer( 'frm_ajax', 'nonce' );

		$field_id         = FrmAppHelper::get_post_param( 'field', 0, 'absint' );
		$form_id          = FrmAppHelper::get_post_param( 'form_id', 0, 'absint' );
		$section_id       = FrmAppHelper::get_post_param( 'section_id', 0, 'absint' );
		$previous_form_id = FrmAppHelper::get_post_param( 'previous_form_id', 0, 'absint' );

		if ( ! $field_id ) {
			wp_die();
		}

		$update_values = array();

		$field_options = FrmDb::get_var( 'frm_fields', array( 'id' => $field_id ), 'field_options' );
		FrmProAppHelper::unserialize_or_decode( $field_options );

		// Update the in_section value
		if ( ! isset( $field_options['in_section'] ) || $field_options['in_section'] != $section_id ) {
			$field_options['in_section']    = $section_id;
			$update_values['field_options'] = $field_options;
			self::maybe_place_field_before_end_divider( $field_id );
		}

		// Update the form_id value
		if ( $form_id ) {
			$update_values['form_id'] = $form_id;
		}

		FrmField::update( $field_id, $update_values );

		if ( $previous_form_id ) {
			// Prevent field cache from showing fields inside of a repeater that were moved outside of the section.
			FrmField::delete_form_transient( $previous_form_id );
		}

		wp_die();
	}

	/**
	 * Make sure that when a new field is dragged into a repeater that it is at least before the end_divider so that it doesn't end up appearing outside of the repeater.
	 *
	 * @param int $field_id
	 */
	private static function maybe_place_field_before_end_divider( $field_id ) {
		$row = FrmDb::get_row( 'frm_fields', array( 'id' => $field_id ), 'field_order, form_id' );

		if ( ! $row ) {
			return;
		}

		$field_order          = $row->field_order;
		$form_id              = $row->form_id;
		$field_order_before   = $field_order - 1;
		$where_end_divider    = array(
			'field_order' => $field_order_before,
			'type'        => 'end_divider',
			'form_id'     => $form_id,
		);
		$end_divider_field_id = FrmDb::get_var( 'frm_fields', $where_end_divider );

		if ( ! $end_divider_field_id ) {
			return;
		}

		FrmField::update( $field_id, array( 'field_order' => $field_order_before ) );
		FrmField::update( $end_divider_field_id, array( 'field_order' => $field_order ) );
	}

	public static function duplicate_section( $section_field, $form_id ) {
		FrmAppHelper::permission_check( 'frm_edit_forms' );
		check_ajax_referer( 'frm_ajax', 'nonce' );

		global $wpdb, $frm_duplicate_ids;

		if ( isset( $_POST['children'] ) ) {
			$children = array_filter( (array) $_POST['children'], 'is_numeric' );
			$fields = FrmField::getAll( array( 'fi.id' => $children ), 'field_order' );
		} else {
			$fields = array();
		}

		array_unshift( $fields, $section_field );

		$order_query       = array( 'field_order >' => $section_field->field_order, 'form_id' => $form_id, 'type' => 'end_divider' );
		$end_section_order = FrmDb::get_var( 'frm_fields', $order_query, 'field_order', array( 'order_by' => 'field_order ASC' ) );
		$field_order       = max( $section_field->field_order, $end_section_order );

		$ended = false;

		if ( ! empty( $section_field->field_options['repeat'] ) ) {
			// create the repeatable form
			$new_form_id = FrmProField::create_repeat_form( 0, array( 'parent_form_id' => $form_id, 'field_name' => $section_field->name ) );
		} else {
			$new_form_id = $form_id;
		}

		$grid_helper = class_exists( 'FrmFieldGridHelper' ) ? new FrmFieldGridHelper() : false;

		foreach ( $fields as $field ) {
			// keep the current form id or give it the id of the newly created form
			$this_form_id = $field->form_id == $form_id ? $form_id : $new_form_id;

			$values = array();
			FrmFieldsHelper::fill_field( $values, $field, $this_form_id );
			if ( FrmField::is_repeating_field( $field ) ) {
				$values['field_options']['form_select'] = $new_form_id;
			}

			$values['field_order'] = $field_order;
			$field_order++;

			$values   = apply_filters( 'frm_duplicated_field', $values );
			$field_id = FrmField::create( $values );

			$copy_field = $field;
			do_action( 'frm_after_duplicate_field', compact( 'field_id', 'values', 'copy_field', 'form_id' ) );

			if ( ! $field_id ) {
				continue;
			}

			$frm_duplicate_ids[ $field->id ]        = $field_id;
			$frm_duplicate_ids[ $field->field_key ] = $field_id;

			if ( 'end_divider' === $field->type ) {
				$ended = true;
			}

			$duplicated_field_object = FrmField::getOne( $field_id );

			if ( $grid_helper instanceof FrmFieldGridHelper ) {
				$grid_helper->set_field( $duplicated_field_object );
				$grid_helper->maybe_begin_field_wrapper();
			}

			$values['id'] = $this_form_id;
			FrmFieldsController::load_single_field( $duplicated_field_object, $values );

			if ( $grid_helper instanceof FrmFieldGridHelper ) {
				$grid_helper->sync_list_size();
			}

			unset( $field, $duplicated_field_object );
		}

		if ( ! $ended ) {
			//make sure the section is ended
			self::create_multiple_fields( (array) $section_field, $form_id );
		}

		// Prevent the function in the free version from completing
		wp_die();
	}

	/**
	 *
	 * Update the repeating form name when a repeating section name is updated
	 *
	 * @since 3.0.03
	 *
	 * @param array $values
	 * @return array $values
	 */
	public static function update_repeater_form_name( $values ) {
		if ( isset( $values['field_options']['repeat'] ) && $values['field_options']['repeat'] ) {
			FrmForm::update( $values['field_options']['form_select'], array( 'name' => $values['name'] ) );
		}

		return $values;
	}

	/**
	 * Setup each field's array when an entry is being edited
	 * Similar to FrmAppHelper::fill_field_defaults
	 *
	 * @since 2.01.0
	 *
	 * @param object $entry
	 * @param array $fields
	 * @param array $args (always contains 'parent_form_id')
	 * If field is repeating, $args includes 'repeating', 'parent_field_id' and 'key_pointer'
	 * If field is embedded, $args includes 'in_embed_form'
	 * @return array
	*/
	public static function setup_field_data_for_editing_entry( $entry, $fields, $args ) {
		$new_fields = array();

		foreach ( $fields as $field ) {
			$default_value = apply_filters('frm_get_default_value', $field->default_value, $field, true );

			$field_value = self::get_posted_or_saved_value( $entry, $field, $args );

			$field_array = array(
				'id'            => $field->id,
				'value'         => $field_value,
				'default_value' => $default_value,
				'name'          => $field->name,
				'description'   => $field->description,
				'type'          => apply_filters('frm_field_type', $field->type, $field, $field_value),
				'options'       => $field->options,
				'required'      => $field->required,
				'field_key'     => $field->field_key,
				'field_order'   => $field->field_order,
				'form_id'       => $field->form_id,
				'parent_form_id' => $args['parent_form_id'],
				'in_embed_form' => isset( $args['in_embed_form'] ) ? $args['in_embed_form'] : '0',
			);

			FrmFieldsHelper::prepare_edit_front_field( $field_array, $field, $entry->id, $args );

			if ( ! isset( $field_array['unique'] ) || ! $field_array['unique'] ) {
				$field_array['unique_msg'] = '';
			}

			$field_array = array_merge( $field->field_options, $field_array );

			$values['fields'][ $field->id ] = $field_array;

			$new_fields[ $field->id ] = $field_array;
		}

		return $new_fields;
	}

	/**
	* If the field has a posted value, get it. Otherwise, get the saved field value
	*
	* @since 2.01.0
	* @param object $entry
	* @param object $field
	* @param array $args (if repeating, this includes 'repeating', 'parent_field_id', and 'key_pointer')
	* @return string|array $field_value
	*/
	private static function get_posted_or_saved_value( $entry, $field, $args ) {
		if ( isset( $args['save_draft_click'] ) && $args['save_draft_click'] && FrmField::is_repeating_field( $field ) ) {
			// If save draft was just clicked, and this is a repeating section, get the saved value
			$field_value = self::get_saved_value( $entry, $field );

		} else if ( FrmEntriesHelper::value_is_posted( $field, $args ) ) {
			$field_value = '';
			FrmEntriesHelper::get_posted_value( $field, $field_value, $args );

		} else {
			$field_value = self::get_saved_value( $entry, $field );
		}

		return $field_value;
	}

	/**
	 * Get the saved value for a field
	 *
	 * @since 2.02.05
	 * @param object $entry
	 * @param object $field
	 * @return array|bool|mixed|string
	 */
	private static function get_saved_value( $entry, $field ) {
		$pass_args = array(
			'links' => false,
			'truncate' => false,
		);
		return FrmProEntryMetaHelper::get_post_or_meta_value( $entry, $field, $pass_args );
	}

	/**
	 * Product Bulk Edit
	 *
	 * @since 4.04
	 */
	public static function bulk_edit_products() {
		FrmAppHelper::permission_check( 'frm_edit_forms' );
		check_ajax_referer( 'frm_ajax', 'nonce' );

		$field_id = FrmAppHelper::get_param( 'field_id', '', 'post', 'absint' );
		$field    = FrmField::getOne( $field_id );

		if ( ! $field || 'product' !== $field->type ) {
			wp_die();
		}

		$field = FrmFieldsHelper::setup_edit_vars( $field );

		$separate = FrmAppHelper::get_param( 'separate', '', 'post', 'sanitize_text_field' );
		$field['separate_value'] = ( $separate === 'true' );

		$field['options'] = self::product_strings_to_array();

		FrmProFieldProduct::single_option( $field );

		wp_die();
	}

	/**
	 * When bulk editing, convert | to array.
	 *
	 * @since 4.04
	 */
	private static function product_strings_to_array() {
		$opts  = FrmAppHelper::get_param( 'opts', '', 'post', 'wp_kses_post' );
		$opts  = explode( "\n", rtrim( $opts, "\n" ) );
		$opts  = array_map( 'trim', $opts );

		foreach ( $opts as $opt_key => $opt ) {
			if ( empty( $opt ) ) {
				unset( $opts[ $opt_key ] );
				continue;
			}

			if ( strpos( $opt, '|' ) === false ) {
				continue;
			}

			$vals  = explode( '|', $opt );
			$count = count( $vals );
			$label = isset( $vals[0] ) ? trim( $vals[0] ) : '';

			// only product name is available
			$opts[ $opt_key ] = array(
				'label' => $label,
				'value' => $label,
				'price' => '',
			);

			if ( 2 === $count ) {
				// product name & price
				$opts[ $opt_key ]['price'] = trim( $vals[1] );
			} elseif ( 3 === $count ) {
				// product name, separate value & price
				$opts[ $opt_key ]['value'] = trim( $vals[1] );
				$opts[ $opt_key ]['price'] = trim( $vals[2] );
			}
			unset( $vals, $opt_key, $opt );
		}

		// just to renumber indices from 0.
		return array_values( $opts );
	}

	/*
	 * Autocomplete users admin ajax endpoint
	 *
	 * @since 4.03.06
	 */
	public static function user_search() {
		FrmAppHelper::permission_check( 'frm_edit_entries' );
		check_ajax_referer( 'frm_ajax', 'nonce' );

		global $wpdb;

		$term = FrmAppHelper::get_param( 'term', '', 'get', 'sanitize_text_field' );
		if ( empty( $term ) ) {
			$args  = array( 'limit' => 20, 'order_by' => 'display_name' );
			$items = FrmDb::get_results( $wpdb->users, array(), 'ID, display_name', $args );
		} else {
			$items = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT ID, display_name FROM {$wpdb->users}
					WHERE ID = %d OR user_email LIKE %s OR user_login LIKE %s OR display_name LIKE %s LIMIT 25",
					absint( $term ),
					'%' . $wpdb->esc_like( $term ) . '%',
					'%' . $wpdb->esc_like( $term ) . '%',
					'%' . $wpdb->esc_like( $term ) . '%'
				)
			);
		}

		$results = array();
		foreach ( $items as $item ) {
			$results[] = array(
				'value' => $item->ID,
				'label' => $item->display_name,
			);
		}

		wp_send_json( $results );
	}

	/**
	 * Changes options of Display format setting of Radio field.
	 *
	 * @since 6.3.3
	 *
	 * @param array $options The options.
	 * @return array
	 */
	public static function change_field_display_format_options( $options ) {
		if ( isset( $options['1']['addon'] ) ) {
			unset( $options['1']['addon'] );
		}

		return $options;
	}

	/**
	 * Changes arguments of Display format setting of Radio field.
	 *
	 * @since 5.0
	 *
	 * @param array $args        The arguments.
	 * @param array $method_args The arguments from the method. Contains `field`, `options`.
	 * @return array
	 */
	public static function change_radio_display_format_args( $args, array $method_args ) {
		return self::change_field_display_format_args( $args, $method_args );
	}

	/**
	 * Changes arguments of Display format setting of Checkbox field.
	 *
	 * @since 6.4.1
	 *
	 * @param array $args        The arguments.
	 * @param array $method_args The arguments from the method. Contains `field`, `options`.
	 * @return array
	 */
	public static function change_checkbox_display_format_args( $args, $method_args ) {
		return self::change_field_display_format_args( $args, $method_args );
	}

	/**
	 * Changes arguments of Display format setting of a field.
	 *
	 * @since 6.4.1
	 *
	 * @param array $args        The arguments.
	 * @param array $method_args The arguments from the method. Contains `field`, `options`.
	 *
	 * @return array
	 */
	private static function change_field_display_format_args( $args, $method_args ) {
		$field            = $method_args['field'];
		$args['selected'] = ! empty( $field['image_options'] ) ? '1' : '0';

		return $args;
	}

	/**
	 * Prevent field creation when license is expired.
	 *
	 * @since 5.4.2
	 *
	 * @param string $field_type
	 * @return void
	 */
	public static function before_create_field( $field_type ) {
		if ( ! FrmProAddonsController::is_expired_outside_grace_period() ) {
			return;
		}

		$pro_fields            = FrmField::pro_field_selection();
		$field_type_is_allowed = ! array_key_exists( $field_type, $pro_fields );

		if ( ! $field_type_is_allowed ) {
			// Die early instead of adding a pro field for an expired license.
			wp_die( -1 );
		}
	}

	/**
	 * Turn off sanitizing in lite for specific pro field option strings.
	 * This way the sanitizing can be handled in Pro for a specific option key in the FrmProField::update function.
	 *
	 * @since 5.5.6
	 *
	 * @param bool   $should_sanitize
	 * @param string $opt
	 * @return bool
	 */
	public static function should_sanitize_field_opt_string( $should_sanitize, $opt ) {
		if ( 'custom_thousand_separator' === $opt ) {
			// Override sanitizing for the custom thousand separator so it doesn't get trimmed in FrmForm::sanitize_field_opt.
			// This way we can support a ' ' space thousand separator.
			return false;
		}
		return $should_sanitize;
	}

	/**
	 * Adds show password HTML to the backend conf input.
	 *
	 * @since 6.3.1
	 *
	 * @param string $input_html Input HTML.
	 * @param array  $args       Contains `field` array.
	 * @return string
	 */
	public static function add_show_password_html_to_backend_conf_input( $input_html, $args ) {
		if ( 'password' === $args['field']['type'] ) {
			$input_html = FrmProFieldsHelper::add_show_password_html( $input_html );
		}
		return $input_html;
	}

	/**
	 * @deprecated 4.0
	 */
	public static function populate_calc_dropdown() {
		_deprecated_function( __METHOD__, '4.0', 'javascript' );

		check_ajax_referer( 'frm_ajax', 'nonce' );
		FrmAppHelper::permission_check('frm_edit_forms');

		if ( isset( $_POST['form_id'] ) && isset( $_POST['field_id'] ) ) {
			echo FrmProFieldsHelper::get_shortcode_select( sanitize_text_field( $_POST['form_id'] ), 'frm_calc_' . sanitize_text_field( $_POST['field_id'] ), 'calc' );
		}
		wp_die();
	}

	/**
	 * @deprecated 4.0
	 */
	public static function options_form( $field, $display, $values ) {
		_deprecated_function( __METHOD__, '4.0' );
	}

	/**
	 * Display the Dynamic Values section
	 *
	 * @since 2.02.06
	 * @deprecated 4.0
	 *
	 * @param array $field
	 * @param array $display
	 */
	public static function show_dynamic_values_options( $field, $display ) {
		_deprecated_function( __METHOD__, '4.0', __CLASS__ . '::more_default_values' );
	}

	/**
	 * @deprecated 4.0
	 */
	public static function add_separate_value_opt_label( $field ) {
		_deprecated_function( __METHOD__, '4.0' );
	}

	/**
	 * @deprecated 3.0
	 * @codeCoverageIgnore
	 */
	public static function show_normal_field( $show ) {
		_deprecated_function( __METHOD__, '3.0' );
		return $show;
	}

	/**
	 * @deprecated 3.0
	 * @codeCoverageIgnore
	 */
	public static function &normal_field_html( $show ) {
		_deprecated_function( __METHOD__, '3.0' );
		return $show;
	}

	/**
	 * @deprecated 3.0
	 * @codeCoverageIgnore
	 */
	public static function show_other() {
		_deprecated_function( __METHOD__, '3.0' );
	}

	/**
	 * @deprecated 3.0
	 * @codeCoverageIgnore
	 */
	public static function show( $field, $name = '' ) {
		_deprecated_function( __FUNCTION__, '3.0', 'FrmFieldType::show_on_form_builder' );
	}

	/**
	 * Display the format option
	 *
	 * @since 2.02.06
	 * @param array $field
	 *
	 * @deprecated 3.0
	 * @codeCoverageIgnore
	 */
	public static function show_format_option( $field ) {
		_deprecated_function( __FUNCTION__, '3.0', 'FrmFieldsController::show_format_option' );
		FrmFieldsController::show_format_option( $field );
	}

	/**
	 * @deprecated 2.05
	 */
	public static function &label_position( $position ) {
		_deprecated_function( __METHOD__, '2.05', 'FrmFieldsHelper::label_position' );
		return $position;
	}

	/**
	 * @deprecated 3.0
	 * @codeCoverageIgnore
	 */
	public static function display_field_options( $display ) {
		_deprecated_function( __FUNCTION__, '3.0', 'FrmFieldType Modals' );
		return $display;
	}

	/**
	 * @deprecated 3.0
	 * @codeCoverageIgnore
	 */
	public static function form_fields( $field, $field_name, $atts ) {
		_deprecated_function( __FUNCTION__, '3.0', 'FrmFieldType Modals' );
	}

	/**
	 * Changes options of Display format setting of Radio field.
	 *
	 * @since 5.0
	 *
	 * @param array $options The options.
	 * @return array
	 */
	public static function change_radio_display_format_options( $options ) {
		_deprecated_function( __METHOD__, '6.3.3', __CLASS__ . '::change_field_display_format_options' );
		return self::change_field_display_format_options( $options );
	}
}
