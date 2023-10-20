<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

class FrmProFormsHelper {

	public static function setup_new_vars( $values ) {

		foreach ( self::get_default_opts() as $var => $default ) {
			$values[ $var ] = FrmAppHelper::get_param( $var, $default, 'post', 'sanitize_text_field' );
		}
		return $values;
	}

	public static function setup_edit_vars( $values ) {
		$record = FrmForm::getOne($values['id']);
		foreach ( array( 'logged_in' => $record->logged_in, 'editable' => $record->editable ) as $var => $default ) {
			$values[ $var ] = FrmAppHelper::get_param( $var, $default, 'get', 'sanitize_text_field' );
		}

		foreach ( self::get_default_opts() as $opt => $default ) {
			if ( ! isset( $values[ $opt ] ) ) {
				$values[ $opt ] = ( $_POST && isset( $_POST['options'][ $opt ] ) ) ? sanitize_text_field( $_POST['options'][ $opt ] ) : $default;
			}

			unset($opt, $default);
		}

		return $values;
	}

	public static function load_chosen_js( $frm_vars ) {
		if ( isset( $frm_vars['chosen_loaded'] ) && $frm_vars['chosen_loaded'] ) {
			$original_js = 'allow_single_deselect:true';
			$chosen_js = apply_filters( 'frm_chosen_js', $original_js );
			if ( $original_js != $chosen_js ) {
				?>__frmChosen=<?php echo json_encode( $chosen_js ); ?>;<?php
			}
		}
	}

	/**
	 * Load the conditional field IDs for JavaScript
	 *
	 * @since 2.01.0
	 * @param array $frm_vars
	 */
	public static function load_hide_conditional_fields_js( $frm_vars ) {

		if ( self::is_initial_load_for_at_least_one_form( $frm_vars ) ) {
			// Check the logic on all dependent fields
			if ( isset( $frm_vars['dep_logic_fields'] ) && ! empty( $frm_vars['dep_logic_fields'] ) ) {
				// TODO: when this is missing and only Dynamic fields on page, problems happen.

				echo 'var frmHide=' . json_encode( $frm_vars['dep_logic_fields'] ) . ';';
				echo 'if(typeof __frmHideOrShowFields == "undefined"){__frmHideOrShowFields=frmHide;}';
				echo 'else{__frmHideOrShowFields=__frmHideOrShowFields.concat(frmHide);}';
			}
		} else {
			// Save time and just hide the fields that are in frm_hide_fields
			echo '__frmHideFields=true;';
		}

		// Check dependent Dynamic fields
		if ( isset( $frm_vars['dep_dynamic_fields'] ) && ! empty( $frm_vars['dep_dynamic_fields'] ) ) {
			echo '__frmDepDynamicFields=' . json_encode( $frm_vars['dep_dynamic_fields'] ) . ';';
		}
	}

	/**
	 * Check if at least one form is loading for the first time
	 *
	 * @since 2.01.0
	 * @param array $frm_vars
	 * @return bool
	 */
	private static function is_initial_load_for_at_least_one_form( $frm_vars ) {
		if ( ! isset( $_POST['form_id'] ) || ! $frm_vars['forms_loaded'] ) {
			return true;
		}

		foreach ( $frm_vars['forms_loaded'] as $form ) {
			if ( ! is_object( $form ) ) {
				continue;
			}

			$form_details_present = isset( $frm_vars['prev_page'][ $form->id ] ) || self::going_to_prev( $form->id ) || self::saving_draft();
			if ( ! $form_details_present ) {
				return true;
			}
		}

		return isset( $frm_vars['rules'] ) && self::rules_array_includes_embedded_or_repeating_fields( $frm_vars['rules'] );
	}

	/**
	 * Check rules for any conditional embedded or repeating fields.
	 * If any exist, we want to include the __frmHideOrShowFields variable.
	 *
	 * @param array $rules
	 * @return bool
	 */
	private static function rules_array_includes_embedded_or_repeating_fields( $rules ) {
		foreach ( $rules as $rule ) {
			if ( ! empty( $rule['inEmbedForm'] ) || ! empty( $rule['isRepeating'] ) ) {
				return true;
			}
		}
		return false;
	}

	public static function load_dropzone_js( $frm_vars ) {
		if ( ! isset( $frm_vars['dropzone_loaded'] ) || empty( $frm_vars['dropzone_loaded'] ) || ! is_array( $frm_vars['dropzone_loaded'] ) ) {
			return;
		}

		$load_dropzone = apply_filters( 'frm_load_dropzone', true );
		if ( ! $load_dropzone ) {
			return;
		}

		$js = array();
		foreach ( $frm_vars['dropzone_loaded'] as $field_id => $options ) {
			$js[] = $options;
		}
		echo '__frmDropzone=' . json_encode( $js ) . ';';
	}

	/**
	 * @param array $frm_vars
	 * @return void
	 */
	public static function load_datepicker_js( $frm_vars ) {
		if ( empty( $frm_vars['datepicker_loaded'] ) || ! is_array( $frm_vars['datepicker_loaded'] ) ) {
			return;
		}

		$frmpro_settings = FrmProAppHelper::get_settings();

		reset( $frm_vars['datepicker_loaded'] );
		$datepicker   = key( $frm_vars['datepicker_loaded'] );
		$loaded_langs = array();

		$datepicker_js = array();
		foreach ( $frm_vars['datepicker_loaded'] as $date_field_id => $options ) {
			if ( empty( $date_field_id ) ) {
				continue;
			}

			if ( strpos($date_field_id, '^') === 0 ) {
				// this is a repeating field
				$trigger_id = 'input[id^="' . str_replace( '^', '', esc_attr( $date_field_id ) ) . '"]';
			} else {
				$trigger_id = '#' . esc_attr( $date_field_id );
			}

			$custom_options = self::get_custom_date_js( $date_field_id, $options );

			$date_options = array(
				'triggerID' => $trigger_id,
				'locale'    => $options['locale'],
				'options'   => array(
					'dateFormat'  => $frmpro_settings->cal_date_format,
					'changeMonth' => 'true',
					'changeYear'  => 'true',
					'yearRange'   => $options['start_year'] . ':' . $options['end_year'],
					'defaultDate' => empty( $options['default_date'] ) ? '' : $options['default_date'],
					'beforeShowDay' => null,
				),
				'customOptions'  => $custom_options,
			);
			$date_options = apply_filters( 'frm_date_field_options', $date_options, array( 'field_id' => $date_field_id, 'options' => $options ) );

			if ( empty( $custom_options ) ) {
				$datepicker_js[] = $date_options;
			} else {
				$custom_options .= ',beforeShow:frmProForm.addFormidableClassToDatepicker';
				$custom_options .= ',onClose:frmProForm.removeFormidableClassFromDatepicker';

				$change_month = self::adjust_value_for_js_boolean( $date_options['options'], 'changeMonth' );
				$change_year  = self::adjust_value_for_js_boolean( $date_options['options'], 'changeYear' );
				?>
jQuery(document).ready(function($){
$('<?php echo $trigger_id; ?>').addClass('frm_custom_date');
$(document).on('focusin','<?php echo $trigger_id; ?>', function(){
$.datepicker.setDefaults($.datepicker.regional['']);
$(this).datepicker($.extend($.datepicker.regional['<?php echo esc_js( $options['locale'] ); ?>'],{dateFormat:'<?php echo esc_js( $frmpro_settings->cal_date_format ); ?>',changeMonth:<?php echo esc_html( $change_month ); ?>,changeYear:<?php echo esc_html( $change_year ); ?>,yearRange:'<?php echo esc_js( $date_options['options']['yearRange'] ); ?>',defaultDate:'<?php echo esc_js( $date_options['options']['defaultDate'] ); ?>'<?php
echo $custom_options;
?>}));
});
});
<?php
			}

			if ( ! empty( $options['locale'] ) && ! in_array( $options['locale'], $loaded_langs, true ) ) {
				if ( ! $loaded_langs ) {
					// this was enqueued late, so make sure it gets printed
					add_action( 'wp_footer', 'print_footer_scripts', 21 );
					add_action( 'admin_print_footer_scripts', 'print_footer_scripts', 99 );
				}

				$loaded_langs[] = $options['locale'];
				wp_enqueue_script( 'jquery-ui-i18n-' . $options['locale'], FrmProAppHelper::plugin_url() . '/js/jquery-ui-i18n/datepicker-' . $options['locale'] . '.min.js', array( 'jquery-ui-core', 'jquery-ui-datepicker' ), '1.13.2' );
			}
		}

		if ( ! empty( $datepicker_js ) ) {
			echo 'var frmDates=' . json_encode( $datepicker_js ) . ';';
			echo 'if(typeof __frmDatepicker == "undefined"){__frmDatepicker=frmDates;}';
			echo 'else{__frmDatepicker=jQuery.extend(__frmDatepicker,frmDates);}';
		}

		FrmProTimeFieldsController::load_timepicker_js( $datepicker );
	}

	/**
	 * @param array  $options
	 * @param string $key
	 * @param string $default
	 * @return string
	 */
	private static function adjust_value_for_js_boolean( $options, $key, $default = 'true' ) {
		if ( ! isset( $options[ $key ] ) ) {
			return $default;
		}
		$value = $options[ $key ];
		if ( ! $value || 'false' === $value ) {
			return 'false';
		}
		return 'true';
	}

	private static function get_custom_date_js( $date_field_id, $options ) {
		ob_start();
		do_action( 'frm_date_field_js', $date_field_id, $options );
		$custom_options = ob_get_contents();
		ob_end_clean();

		return $custom_options;
	}

	public static function load_calc_js( $frm_vars ) {
		if ( ! isset($frm_vars['calc_fields']) || empty($frm_vars['calc_fields']) ) {
			return;
		}

		$calc_rules = array(
			'fields'    => array(),
			'calc'      => array(),
			'fieldKeys' => array(),
			'fieldsWithCalc' => array(),
		);

		$triggers = array();
		$options  = array();

		foreach ( $frm_vars['calc_fields'] as $result => $field ) {
			$calc_rules['fieldsWithCalc'][ $field['field_id'] ] = $result;
			$calc                                               = $field['calc'];
			FrmProFieldsHelper::replace_non_standard_formidable_shortcodes( array( 'field' => $field['field_id'] ), $calc );

			preg_match_all("/\[(.?)\b(.*?)(?:(\/))?\]/s", $calc, $matches, PREG_PATTERN_ORDER);

			$field_keys  = array();
			$calc_fields = array();

			foreach ( $matches[0] as $match_key => $val ) {
				$val  = trim( trim( $val, '[' ), ']' );
				$show = false !== strpos( $val, ' show=' );
				if ( $show ) {
					$before = $val;
					$val    = preg_replace( '/ show=("|\'){0,1}label("|\'){0,1}/', '', $val, 1 );
					if ( $val !== $before ) {
						$show = 'label';
					}

					if ( ! is_string( $show ) ) {
						$show = false; // fallback to value if the show value did not match a previous check.
						$val  = preg_replace( '/ show=("|\'){0,1}value("|\'){0,1}/', '', $val, 1 ); // treat show="value" as if no attribute was set.
					}
				}

				$calc_fields[ $val ] = FrmField::getOne( $val );
				if ( ! $calc_fields[ $val ] ) {
					unset( $calc_fields[ $val ] );
					continue;
				}

				$field_keys[ $calc_fields[ $val ]->id ] = self::get_field_call_for_calc( $calc_fields[ $val ], $field['parent_form_id'] );

				$calc_rules['fieldKeys'] = $calc_rules['fieldKeys'] + $field_keys;

				if ( $show && is_array( reset( $calc_fields[ $val ]->options ) ) ) {
					$calc                                = str_replace( $matches[0][ $match_key ], '[' . $calc_fields[ $val ]->id . ' show=' . $show . ']', $calc );
					$options[ $calc_fields[ $val ]->id ] = array_column( $calc_fields[ $val ]->options, 'label', 'value' );
				} else {
					$calc = str_replace( $matches[0][ $match_key ], '[' . $calc_fields[ $val ]->id . ']', $calc );
					$show = false;
				}

				// Prevent invalid decrement error for -- in calcs
				if ( $field['calc_type'] !== 'text' ) {
					$calc = str_replace( '-[', '- [', $calc );
				}
			}

			if ( strpos( $calc, '[' ) !== false ) {
				// check for WP shortcodes if there are any left
				$calc = do_shortcode( $calc );
			}

			$triggers[]                              = reset( $field_keys );
			$calc_rules['calc'][ $result ]           = self::get_calc_rule_for_field(
				array(
					'field'    => $field,
					'calc'     => $calc,
					'field_id' => $field['field_id'],
					'form_id'  => $field['parent_form_id'],
				)
			);
			$calc_rules['calc'][ $result ]['fields'] = array();

			unset( $field );

			foreach ( $calc_fields as $calc_field ) {
				$calc_rules['calc'][ $result ]['fields'][] = $calc_field->id;
				if ( isset( $calc_rules['fields'][ $calc_field->id ] ) ) {
					$calc_rules['fields'][ $calc_field->id ]['total'][] = $result;
				} else {
					$calc_rules['fields'][ $calc_field->id ] = array(
						'total' => array( $result ),
						'type'  => ( $calc_field->type == 'lookup' || $calc_field->type == 'product' ) ? $calc_field->field_options['data_type'] : $calc_field->type,
						'key'   => $field_keys[ $calc_field->id ],
					);
				}

				if ( $calc_field->type == 'date' ) {
					if ( ! isset($frmpro_settings) ) {
						$frmpro_settings = FrmProAppHelper::get_settings();
					}
					$calc_rules['date'] = $frmpro_settings->cal_date_format;
				}
				unset( $calc_field );
			}
		}

		// trigger calculations on page load
		if ( ! empty($triggers) ) {
			$triggers = array_filter( array_unique( $triggers ) );
			$calc_rules['triggers'] = array_values( $triggers );
		}

		if ( $options ) {
			$calc_rules['options'] = $options;
		}

		echo 'var frmcalcs=' . json_encode( $calc_rules ) . ";\n";
		echo 'if(typeof __FRMCALC == "undefined"){__FRMCALC=frmcalcs;}';
		echo 'else{__FRMCALC=jQuery.extend(true,{},__FRMCALC,frmcalcs);}';
	}

	public static function get_calc_rule_for_field( $atts ) {
		$field = $atts['field'];

		$rule = array(
			'calc'          => isset( $atts['calc'] ) ? $atts['calc'] : $field['calc'],
			'calc_dec'      => $field['calc_dec'],
			'calc_type'     => $field['calc_type'],
			'form_id'       => $atts['form_id'],
			'field_id'      => isset( $atts['field_id'] ) ? $atts['field_id'] : $field['id'],
			'in_section'    => isset( $field['in_section'] ) ? $field['in_section'] : '0',
			'in_embed_form' => isset( $field['in_embed_form'] ) ? $field['in_embed_form'] : '0',
		);

		$rule['inSection']   = $rule['in_section'];
		$rule['inEmbedForm'] = $rule['in_embed_form'];

		if ( isset( $atts['parent_form_id'] ) ) {
			$rule['parent_form_id'] = $atts['parent_form_id'];
		}

		self::add_is_currency_calc_rule_for_field( $rule, $field );

		return $rule;
	}

	/**
	 * Adds `is_currency` rule for field if applicable.
	 *
	 * @since 5.2.06
	 *
	 * @param array $rule Calculation rule.
	 * @param array $field Field array.
	 */
	private static function add_is_currency_calc_rule_for_field( &$rule, $field ) {
		if ( empty( $field['is_currency'] ) ) {
			return;
		}

		// If field is invisible and converted to <input type="hidden">, treat it as a number field instead of price.
		if ( ! FrmProFieldsHelper::is_field_visible_to_user( $field ) ) {
			return;
		}

		$rule['is_currency'] = true;
		if ( ! empty( $field['custom_currency'] ) ) {
			$rule['custom_currency'] = self::prepare_custom_currency( $field );
		}
	}

	/**
	 * @since 5.0.16
	 *
	 * @param array $field
	 * @return array
	 */
	public static function prepare_custom_currency( $field ) {
		if ( is_array( $field['custom_currency'] ) ) {
			return $field['custom_currency'];
		}
		return FrmProCurrencyHelper::get_custom_currency( $field );
	}

	/**
	 * Get the field call for a calc field
	 *
	 * @since 2.01.0
	 *
	 * @param object $calc_field
	 * @param int $parent_form_id
	 * @return string $field_call
	 */
	private static function get_field_call_for_calc( $calc_field, $parent_form_id ) {
		$html_field_id = '="field_' . $calc_field->field_key;

		// If field is inside of repeating section/embedded form or it is a radio, scale, or checkbox field
		$in_child_form = $parent_form_id != $calc_field->form_id;
		if ( self::has_variable_html_id( $calc_field ) || $in_child_form ) {
			$html_field_id = '^' . $html_field_id . '-';
		} else if ( $calc_field->type == 'select' ) {
			$is_multiselect = FrmField::get_option( $calc_field, 'multiple' );
			if ( $is_multiselect ) {
				$html_field_id = '^' . $html_field_id;
			}
		} elseif ( $calc_field->type == 'time' && ! FrmField::is_option_true( $calc_field, 'single_time' ) ) {
			$html_field_id = '^' . $html_field_id . '_';
		}

		$field_call = '[id' . $html_field_id . '"]';

		return $field_call;
	}

	/**
	 * @since 5.0.10
	 *
	 * @param array $frm_vars
	 * @return void
	 */
	public static function load_rte_js( $frm_vars ) {
		if ( empty( $frm_vars['rte_reqmessages'] ) ) {
			return;
		}
		echo 'var rteReqmessages = ' . json_encode( $frm_vars['rte_reqmessages'] ) . ";\n";
		echo 'if(typeof __FRMRTEREQMESSAGES == "undefined"){__FRMRTEREQMESSAGES=rteReqmessages;}';
		echo 'else{__FRMRTEREQMESSAGES=jQuery.extend(true,{},__FRMRTEREQMESSAGES,rteReqmessages);}';
	}

	/**
	 * Check if a field has a variable HTML ID
	 *
	 * @since 2.03.07
	 *
	 * @param stdClass $field
	 *
	 * @return bool
	 */
	private static function has_variable_html_id( $field ) {
		$is_radio_check = in_array( $field->type, self::radio_similar_field_types(), true );
		$is_other_radio = in_array( $field->type, array( 'lookup', 'product' ), true ) && in_array( $field->field_options['data_type'], array( 'radio', 'checkbox' ), true );

		$has_variable_html_id = $is_radio_check || $is_other_radio;

		/**
		 * Allows modifying a field has variable HTML id or not.
		 *
		 * @since 5.0.02
		 *
		 * @param array $has_variable_html_id Has variable HTML id or not.
		 * @param array $args                 Arguments. Contains `field`.
		 */
		return apply_filters( 'frm_pro_field_has_variable_html_id', $has_variable_html_id, compact( 'field' ) );
	}

	/**
	 * Gets field types that are similar to radio field.
	 *
	 * @since 5.4
	 *
	 * @return array
	 */
	public static function radio_similar_field_types() {
		/**
		 * Allows modifying radio similar field types.
		 *
		 * @since 5.4
		 *
		 * @param array $field_types Field types.
		 */
		return apply_filters( 'frm_pro_radio_similar_field_types', array( 'radio', 'scale', 'star', 'checkbox' ) );
	}

	/**
	 * @since 4.04
	 *
	 * @param array $frm_vars
	 * @return void
	 */
	public static function load_currency_js( $frm_vars ) {
		if ( empty( $frm_vars['currency'] ) ) {
			return;
		}

		echo 'var frmcurrency=' . json_encode( $frm_vars['currency'] ) . ";\n";
		echo 'if(typeof __FRMCURR == "undefined"){__FRMCURR=frmcurrency;}';
		echo 'else{__FRMCURR=jQuery.extend(true,{},__FRMCURR,frmcurrency);}';
	}

	public static function load_input_mask_js() {
		global $frm_input_masks;
		if ( empty($frm_input_masks) ) {
			return;
		}

		$masks = array();
		foreach ( (array) $frm_input_masks as $f_key => $mask ) {
			if ( ! $mask ) {
				continue;
			} else if ( $mask !== true ) {
				// this isn't used in the plugin, but is here for those using the mask filter
				$masks[] = array(
					'trigger' => is_numeric( $f_key ) ? 'input[name="item_meta[' . $f_key . ']"]' : '#field_' . $f_key,
					'mask'    => $mask,
				);
			}
			unset($f_key, $mask);
		}

		if ( ! empty( $masks ) ) {
			echo '__frmMasks=' . json_encode( $masks ) . ';';
		}
	}

	public static function get_default_opts() {
		$frmpro_settings = FrmProAppHelper::get_settings();

		$settings = array(
			'edit_value'           => $frmpro_settings->update_value,
			'edit_msg'             => $frmpro_settings->edit_msg,
			'edit_action'          => 'message',
			'edit_url'             => '',
			'edit_page_id'         => 0,
			'logged_in'            => 0,
			'logged_in_role'       => '',
			'editable'             => 0,
			'save_draft'           => 0,
			'draft_msg'            => __( 'Your draft has been saved.', 'formidable-pro' ),
			'editable_role'        => '',
			'open_editable_role'   => '-1',
			'copy'                 => 0,
			'single_entry'         => 0,
			'single_entry_type'    => 'user',
			'success_page_id'      => '',
			'success_url'          => '',
			'ajax_submit'          => 0,
			'cookie_expiration'    => 8000,
			'prev_value'           => __( 'Previous', 'formidable-pro' ),
			'draft_label'          => __( 'Save Draft', 'formidable-pro' ),
			'transition'           => '',
			'submit_align'         => '',
			'submit_conditions'    => array(
				'show_hide'        => 'show',
				'any_all'          => 'all',
				'hide_field'       => array(),
				'hide_opt'         => array(),
				'hide_field_cond'  => array(),
			),
			'open_status'          => '',
			'closed_msg'           => '<p>' . __( 'This form is currently closed for submissions.', 'formidable-pro' ) . '</p>',
			'open_date'            => current_time( 'Y-m-d H:i' ),
			'close_date'           => '',
			'max_entries'          => '',
			'protect_files'        => 0,
			'noindex_files'        => 0,
			'rootline'             => '',
			'pagination_position'  => '',
			'rootline_titles_on'   => 0,
			'rootline_titles'      => array(),
			'rootline_lines_off'   => 0,
			'rootline_numbers_off' => 0,
		);

		/**
		 * @since 5.0.15
		 */
		return apply_filters( 'frm_pro_default_form_settings', $settings );
	}

	public static function get_taxonomy_count( $taxonomy, $post_categories, $tax_count = 0 ) {
		if ( isset( $post_categories[ $taxonomy . $tax_count ] ) ) {
			$tax_count++;
			$tax_count = self::get_taxonomy_count( $taxonomy, $post_categories, $tax_count );
		}
		return $tax_count;
	}

	/**
	 * @since 2.0.8
	 */
	public static function can_submit_form_now( $errors, $values ) {
		global $frm_vars;

		$form             = FrmForm::getOne( $values['form_id'] );
		$params           = ( isset( $frm_vars['form_params'] ) && is_array( $frm_vars['form_params'] ) && isset( $frm_vars['form_params'][ $values['form_id'] ] ) ) ? $frm_vars['form_params'][ $values['form_id'] ] : FrmForm::get_params( $values['form_id'] );
		$values['action'] = $params['action'];

		if ( self::visitor_already_submitted( $form, $errors ) || self::check_if_form_is_closed_and_cannot_be_submitted( $form, $errors ) ) {
			self::stop_form_submit();
			return $errors;
		}

		if ( $params['action'] != 'create' ) {
			if ( self::has_another_page( $values['form_id'] ) ) {
				self::stop_submit_if_more_pages( $values, $errors );
			}
			return $errors;
		}

		if ( self::has_another_page( $values['form_id'] ) ) {
			self::stop_submit_if_more_pages( $values, $errors );
		} elseif ( self::user_allowed_one_editable_entry( $form, $errors ) ) {
			self::stop_form_submit();
		}

		return $errors;
	}

	/**
	 * @since 3.04
	 *
	 * @param object $form
	 * @param array $errors
	 *
	 * @return bool and $errors by reference
	 */
	public static function visitor_already_submitted( $form, &$errors ) {
		$has_error = false;
		if ( ! empty( $form->options['single_entry'] ) && ! self::user_can_submit_form( $form ) ) {
			$frmpro_settings = FrmProAppHelper::get_settings();
			$k = is_numeric( $form->options['single_entry_type'] ) ? 'field' . $form->options['single_entry_type'] : 'single_entry';
			$errors[ $k ] = $frmpro_settings->already_submitted;
			$has_error = true;
		}
		return $has_error;
	}

	/**
	 * @since 3.04
	 *
	 * @param object $form
	 * @param array $errors
	 *
	 * @return bool and $errors by reference
	 */
	private static function user_allowed_one_editable_entry( $form, &$errors ) {
		$has_error = false;
		$user_ID = get_current_user_id();
		$user_limited_entry = $user_ID && $form->editable && isset( $form->options['single_entry'] ) && $form->options['single_entry'] && $form->options['single_entry_type'] == 'user' && ! FrmAppHelper::is_admin();
		if ( $user_limited_entry ) {
			$entry_id = FrmDb::get_var(
				'frm_items',
				array(
					'user_id' => $user_ID,
					'form_id' => $form->id,
				)
			);

			if ( $entry_id ) {
				$frmpro_settings = FrmProAppHelper::get_settings();
				$errors['single_entry'] = $frmpro_settings->already_submitted;
				$has_error = true;
			}
		}
		return $has_error;
	}

	/**
	 * Check if a form is closed and cannot be submitted.
	 * Some users are allowed to submit closed forms from the back end when editing entries.
	 *
	 * @since 3.04
	 *
	 * @param object $form
	 * @param array  $errors passed by reference, updated if the form cannot be submitted.
	 *
	 * @return bool true if the form is closed and cannot be submitted.
	 */
	private static function check_if_form_is_closed_and_cannot_be_submitted( $form, &$errors ) {
		$has_error = false;
		if ( ! self::logged_in_user_can_submit_closed_form() && ! FrmProForm::is_open( $form ) ) {
			$errors['open_status'] = do_shortcode( $form->options['closed_msg'] );
			$has_error             = true;
		}
		return $has_error;
	}

	/**
	 * Check if the logged in user is editing an entry from the back end.
	 *
	 * @return bool
	 */
	private static function logged_in_user_can_submit_closed_form() {
		$can_submit = false;
		$id         = FrmAppHelper::get_param( 'id', '', 'post', 'absint' );
		$action     = FrmAppHelper::get_param( 'frm_action', '', 'post', 'sanitize_key' );
		if ( $id && 'update' === $action ) {
			if ( current_user_can( 'frm_edit_entries' ) ) {
				$can_submit = true;
			} else {
				$entry = FrmEntry::getOne( $id );
				if ( $entry && ! empty( $entry->is_draft ) ) {
					$can_submit = true;
				}
			}
		}
		return $can_submit;
	}

	/**
	 * @since 2.0.8
	 */
	public static function stop_submit_if_more_pages( $values, &$errors ) {
		if ( self::going_to_prev( $values['form_id'] ) ) {
			$errors = array();
			self::stop_form_submit();
		} else if ( $values['action'] == 'create' ) {
			self::stop_form_submit();
		}
	}

	/**
	 * @since 2.0.8
	 */
	public static function stop_form_submit() {
		add_filter( 'frm_continue_to_create', '__return_false' );
	}

	/**
	 * @since 2.0.8
	 * @return boolean
	 */
	public static function user_can_submit_form( $form ) {
		if ( $form->logged_in && ! is_user_logged_in() ) {
			return false;
		}

		$can_submit  = true;
		$admin_entry = FrmAppHelper::is_admin();

		if ( $admin_entry && current_user_can( 'frm_create_entries' ) ) {
			return $can_submit;
		}

		$cookie_limit = $form->options['single_entry_type'] === 'cookie' && isset( $_COOKIE[ 'frm_form' . $form->id . '_' . COOKIEHASH ] );
		if ( $cookie_limit ) {
			$can_submit = $admin_entry ? true : false;
		} elseif ( $form->options['single_entry_type'] === 'ip' ) {
			if ( ! $admin_entry ) {
				$prev_entry = FrmEntry::getAll( array( 'it.form_id' => $form->id, 'it.ip' => FrmAppHelper::get_ip_address() ), '', 1 );
				if ( $prev_entry ) {
					$can_submit = false;
				}
			}
		} elseif ( $form->options['single_entry_type'] === 'user' || ! empty( $form->options['save_draft'] ) ) {
			$user_ID = get_current_user_id();
			if ( $user_ID ) {
				global $frm_vars;
				$params   = ( isset( $frm_vars['form_params'] ) && is_array( $frm_vars['form_params'] ) && isset( $frm_vars['form_params'][ $form->id ] ) ) ? $frm_vars['form_params'][ $form->id ] : FrmForm::get_params( $form->id );
				$action   = $params['action'];
				$is_draft = $form->options['single_entry_type'] !== 'user';
				$meta     = FrmProEntriesHelper::check_for_user_entry( $user_ID, $form, $is_draft );

				if ( 'create' !== $action && ! $is_draft && $meta ) {
					if ( $form->editable || FrmDb::get_var( 'frm_items', array( 'id' => reset( $meta ) ), 'is_draft' ) ) {
						$meta = false;
					}
				}

				if ( $meta ) {
					$can_submit = false;
				}
			}
		}

		return $can_submit;
	}

	/**
	 * @since 2.3
	 */
	public static function get_the_page_number( $form_id ) {
		$page_num = 1;
		if ( self::going_to_prev( $form_id ) ) {
			self::prev_page_num( $form_id, $page_num );
		} elseif ( self::going_to_next( $form_id ) ) {
			self::next_page_num( $form_id, $page_num );
		}
		return $page_num;
	}

	/**
	 * @since 2.3
	 */
	private static function next_page_num( $form_id, &$page_num ) {
		$next_page = FrmAppHelper::get_post_param( 'frm_page_order_' . $form_id, 0, 'absint' );
		if ( $next_page ) {
			$page_breaks = FrmField::get_all_types_in_form( $form_id, 'break' );
			foreach ( $page_breaks as $page_break ) {
				$page_num++;
				if ( $page_break->field_order >= $next_page ) {
					break;
				}
			}
		}
	}

	/**
	 * @since 2.3
	 */
	private static function prev_page_num( $form_id, &$page_num ) {
		$next_page = FrmAppHelper::get_post_param( 'frm_next_page', 0, 'absint' );
		if ( $next_page ) {
			$page_breaks = FrmField::get_all_types_in_form( $form_id, 'break' );
			$page_num = count( $page_breaks );
			$page_breaks = array_reverse( $page_breaks );
			foreach ( $page_breaks as $page_break ) {
				if ( $page_break->field_order <= $next_page ) {
					break;
				}
				$page_num--;
			}
		}
	}

	/**
	 * @since 2.0.8
	 */
	public static function has_another_page( $form_id ) {
		$more_pages = false;
		if ( ! self::saving_draft() ) {
			if ( self::going_to_prev( $form_id ) ) {
				$more_pages = true;
			} else {
				$more_pages = self::going_to_next( $form_id );
			}
		}

		return $more_pages;
	}

	/**
	 * @return boolean
	 */
	public static function going_to_prev( $form_id ) {
		$back = false;
		$next_page = FrmAppHelper::get_post_param( 'frm_next_page', 0, 'absint' );
		if ( $next_page ) {
			$prev_page = FrmAppHelper::get_post_param( 'frm_page_order_' . $form_id, 0, 'absint' );
			if ( ! $prev_page || ( $next_page < $prev_page ) ) {
				$back = true;
			}
		}
		return $back;
	}

	/**
	 * @since 2.0.8
	 * @return boolean
	 */
	public static function going_to_next( $form_id ) {
		$next_page = FrmAppHelper::get_post_param( 'frm_page_order_' . $form_id, 0, 'absint' );
		$more_pages = false;

		if ( $next_page ) {
			$more_pages = true;
			$page_breaks = FrmField::get_all_types_in_form( $form_id, 'break' );

			$previous_page = new stdClass();
			$previous_page->field_order = 0;

			foreach ( $page_breaks as $page_break ) {
				if ( $page_break->field_order >= $next_page ) {
					$current_page = apply_filters( 'frm_get_current_page', $previous_page, $page_breaks, false );
					if ( ! is_object( $current_page ) && $current_page == -1 ) {
						unset( $_POST[ 'frm_page_order_' . $form_id ] );
						$more_pages = false;
					}
					break;
				}
				$previous_page = $page_break;
			}
		}

		return $more_pages;
	}

	public static function get_prev_button( $form, $class = '' ) {
		$html = '[if back_button]<input type="submit" value="[back_label]" name="frm_prev_page" formnovalidate="formnovalidate" class="frm_prev_page ' . esc_attr( $class ) . '" [back_hook] />[/if back_button]';
		return self::get_draft_button( $form, $class, $html, 'back_button' );
	}

	/**
	 * Check if this entry is currently being saved as a draft
	 */
	public static function saving_draft() {
		$saving_draft = FrmAppHelper::get_post_param( 'frm_saving_draft', '', 'sanitize_title' );
		$saving = ( $saving_draft == '1' && is_user_logged_in() );
		return $saving;
	}

	public static function save_draft_msg( &$message, $form, $record = false ) {
		if ( ! self::saving_draft() ) {
			return;
		}

		$message = isset($form->options['draft_msg']) ? $form->options['draft_msg'] : __( 'Your draft has been saved.', 'formidable-pro' );
	}

	public static function get_draft_button( $form, $class = '', $html = '', $button_type = 'save_draft' ) {
		if ( empty( $html ) ) {
			$html = '[if save_draft]<input type="submit" value="[draft_label]" name="frm_save_draft" formnovalidate="formnovalidate" class="frm_save_draft ' . esc_attr( $class ) . '" [draft_hook] />[/if save_draft]';
		}

		$html = FrmProFormsController::replace_shortcodes($html, $form);
		if ( strpos( $html, '[if ' . $button_type . ']') !== false ) {
			$html = preg_replace( '/(\[if\s+' . $button_type . '\])(.*?)(\[\/if\s+' . $button_type . '\])/mis', '', $html );
		}
		return $html;
	}

	/**
	 * Check if we're on the final page of a given form
	 *
	 * @since 2.03.07
	 *
	 * @param int|string $form_id
	 *
	 * @return bool
	 */
	public static function is_final_page( $form_id ) {
		global $frm_vars;
		return ( ! isset( $frm_vars['next_page'][ $form_id ] ) );
	}

	/**
	 * Add a class to the form's Submit button
	 *
	 * @since 2.03.07
	 *
	 * @param array $classes
	 * @param stdClass $form
	 *
	 * @return array
	 */
	public static function add_submit_button_class( $classes, $form ) {
		if ( self::is_final_page( $form->id ) ) {
			$classes[] = 'frm_final_submit';
		}

		return $classes;
	}

	public static function get_draft_link( $form ) {
		return self::get_draft_button( $form, '', FrmFormsHelper::get_draft_link() );
	}

	/**
	 * Gets HTML of start over button.
	 *
	 * @since 5.3.1
	 *
	 * @param object $form Form object.
	 * @return string
	 */
	public static function get_start_over_html( $form ) {
		if ( ! method_exists( 'FrmFormsHelper', 'get_start_over_shortcode' ) ) {
			return '';
		}

		return self::get_draft_button( $form, '', FrmFormsHelper::get_start_over_shortcode(), 'start_over' );
	}

	public static function is_show_data_field( $field ) {
		return $field['type'] == 'data' && ( $field['data_type'] == '' || $field['data_type'] == 'data' );
	}

	public static function has_field( $type, $form_id, $single = true ) {
		if ( $single ) {
			$included = FrmDb::get_var( 'frm_fields', array( 'form_id' => $form_id, 'type' => $type ) );
			if ( $included ) {
				$included = FrmField::getOne($included);
			}
		} else {
			$included = FrmField::get_all_types_in_form( $form_id, $type );
		}

		return $included;
	}

	/**
	 * @since 2.0
	 * @return array of repeatable section fields
	 */
	public static function has_repeat_field( $form_id, $single = true ) {
		$fields = self::has_field('divider', $form_id, $single);
		if ( ! $fields ) {
			return $fields;
		}

		$repeat_fields = array();
		foreach ( $fields as $field ) {
			if ( FrmField::is_repeating_field($field) ) {
				$repeat_fields[] = $field;
			}
		}

		return $repeat_fields;
	}

	/**
	 * @param array $atts - includes form_id, setting_name, and expected_setting
	 * @since 2.0.8
	 */
	public static function has_form_setting( $atts ) {
		$form = FrmForm::getOne( $atts['form_id'] );
		return ( isset( $form->options[ $atts['setting_name'] ] ) && $form->options[ $atts['setting_name'] ] == $atts['expected_setting'] );
	}

	public static function &post_type( $form ) {
		if ( is_numeric($form) ) {
			$form_id = $form;
		} else {
			$form_id = (array) $form['id'];
		}

		$action = FrmFormAction::get_action_for_form( $form_id, 'wppost' );
		$action = reset( $action );

		if ( ! $action || ! isset($action->post_content['post_type']) ) {
			$type = 'post';
		} else {
			$type = $action->post_content['post_type'];
		}

		return $type;
	}

	/**
	 * Require Ajax submission when a form is edited inline
	 *
	 * @since 2.03.02
	 *
	 * @param object $form
	 *
	 * @return object
	 */
	public static function prepare_inline_edit_form( $form ) {
		global $frm_vars;
		if ( ! empty( $frm_vars['inplace_edit'] ) ) {
			$form->options['ajax_submit'] = '1';
		}

		return $form;
	}

	/**
	 * @param int $form_id
	 */
	public static function maybe_init_antispam( $form_id ) {
		if ( is_callable( 'FrmAntiSpam::maybe_init' ) ) {
			FrmAntiSpam::maybe_init( $form_id );
		}
	}

	/**
	 * @param int $form_id
	 */
	public static function maybe_echo_antispam_token( $form_id ) {
		if ( is_callable( 'FrmAntiSpam::maybe_echo_token' ) ) {
			FrmAntiSpam::maybe_echo_token( $form_id );
		}
	}

	/**
	 * Check if Lite has been updated to support AJAX Submit (v6.2+).
	 *
	 * @since 6.2
	 *
	 * @return bool
	 */
	public static function lite_supports_ajax_submit() {
		return is_callable( 'FrmForm::is_ajax_on' );
	}

	public static function get_sub_form( $field_name, $field, $args = array() ) {
		_deprecated_function( __FUNCTION__, '2.02.06', 'FrmProNestedFormsController::display_front_end_nested_form' );
		FrmProNestedFormsController::display_front_end_nested_form( $field, $field_name, $args );
	}

	public static function repeat_field_set() {
		_deprecated_function( __FUNCTION__, '2.02.06', 'FrmProNestedFormsController::display_front_end_nested_form' );
	}

	public static function repeat_buttons() {
		_deprecated_function( __FUNCTION__, '2.02.06', 'FrmProNestedFormsController::display_front_end_nested_form' );
	}

	public static function repeat_button_html() {
		_deprecated_function( __FUNCTION__, '2.02.06', 'FrmProNestedFormsController::display_front_end_nested_form' );
	}
}
