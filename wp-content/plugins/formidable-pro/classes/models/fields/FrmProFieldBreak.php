<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

/**
 * @since 3.0
 */
class FrmProFieldBreak extends FrmFieldType {

	/**
	 * @var string
	 * @since 3.0
	 */
	protected $type = 'break';

	/**
	 * @var bool
	 * @since 3.0
	 */
	protected $has_input = false;

	/**
	 * @var bool
	 * @since 3.0
	 */
	protected $has_html = false;

	protected function get_new_field_name() {
		return __( 'Next', 'formidable-pro' );
	}

	protected function field_settings_for_type() {
		$settings = array(
			'required'      => false,
			'visibility'    => false,
			'description'   => false,
			'label_position' => false,
			'css'           => false,
			'options'       => true,
			'default'       => false,
		);
		FrmProFieldsHelper::fill_default_field_display( $settings );
		return $settings;
	}

	protected function extra_field_opts() {
		return array(
			'show_hide' => 'hide',
		);
	}

	/**
	 * @since 4.0
	 */
	protected function include_form_builder_file() {
		return FrmProAppHelper::plugin_path() . '/classes/views/frmpro-fields/back-end/field-' . $this->type . '.php';
	}

	/**
	 * Define parameters and include the field on form builder
	 *
	 * @since 3.0
	 *
	 * @param string $name
	 * @param array $field
	 */
	protected function include_on_form_builder( $name, $field ) {
		$form     = FrmForm::getOne( $field['form_id'] );
		$previous = isset( $form->options['prev_value'] ) ? $form->options['prev_value'] : __( 'Previous', 'formidable-pro' );
		unset( $form );

		include( $this->include_form_builder_file() );
	}

	/**
	 * @since 3.06.01
	 */
	public function translatable_strings() {
		return array( 'name' );
	}

	public function prepare_field_html( $args ) {
		global $frm_vars;

		$args = $this->fill_display_field_values( $args );

		FrmProFieldsHelper::set_field_js( $this->field );

		$post_form_id = FrmAppHelper::get_post_param( 'form_id', 0, 'absint' );
		$current_page = isset( $frm_vars['prev_page'][ $this->field['form_id'] ] ) ? $frm_vars['prev_page'][ $this->field['form_id'] ] : 0;
		$is_current_page = $current_page == $this->field['field_order'];

		$should_scroll = $is_current_page || ! isset( $frm_vars['scrolled'] );
		if ( $this->field['form_id'] == $post_form_id && ! defined('DOING_AJAX') && $should_scroll ) {
			$frm_vars['scrolled'] = true;
			//scroll to the form when we move to the next page
			FrmFormsHelper::get_scroll_js( $this->field['form_id'] );
		}

		if ( $is_current_page ) {
			$html = parent::prepare_field_html( $args );
		} else {
			$html = '<input type="hidden" name="frm_page_order_' . esc_attr( $this->field['form_id'] ) . '" id="frm_page_order_' . esc_attr( $this->field['form_id'] ) . '" value="' . esc_attr( $this->field['field_order'] ) . '" />';
		}

		return $html;
	}

	public function get_label_class() {
		return $this->get_field_column( 'label' );
	}

	public function front_field_input( $args, $shortcode_atts ) {
		global $frm_vars;
		$current_page = isset( $frm_vars['prev_page'][ $this->field['form_id'] ] ) ? $frm_vars['prev_page'][ $this->field['form_id'] ] : 0;
		return '<input type="hidden" name="frm_next_page" class="frm_next_page" id="frm_next_p_' . esc_attr( $current_page ) . '" value="" />';
	}

	/**
	 * @since 5.5.2
	 *
	 * @param string|array $value
	 * @param array        $atts
	 * @return string
	 */
	public function get_display_value( $value, $atts = array() ) {
		if ( ! empty( $atts['plain_text'] ) ) {
			return "\r\n"; // Another line break is also added in FrmEntryFormatter::prepare_plain_text_display_value_for_extra_fields.
		}
		return '<br/><br/>';
	}
}
