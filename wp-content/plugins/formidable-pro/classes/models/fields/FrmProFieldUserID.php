<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

/**
 * @since 3.0
 */
class FrmProFieldUserID extends FrmFieldUserID {

	protected function field_settings_for_type() {
		$settings = parent::field_settings_for_type();

		$settings['autopopulate'] = true;
		$settings['visibility'] = false;
		$settings['default'] = true;
		$settings['logic'] = false;

		FrmProFieldsHelper::fill_default_field_display( $settings );
		return $settings;
	}

	public function prepare_front_field( $values, $atts ) {
		if ( ! $this->show_admin_field() || ! FrmProFieldsHelper::field_on_current_page( $this->field ) ) {
			return $values;
		}

		if ( is_callable( array( $this, 'get_field_value' ) ) ) {
			// Use autocompleted if available.
			$values['custom_html'] = FrmFieldsHelper::get_default_html( 'text' );
		} else {
			$values['type']    = 'select';
			$values['options'] = $this->get_options( $values );
			$values['use_key'] = true;
			$values['custom_html'] = FrmFieldsHelper::get_default_html( 'select' );
		}

		return $values;
	}

	/**
	 * @since 4.03.06
	 */
	public function prepare_field_html( $args ) {
		$args = $this->fill_display_field_values( $args );

		if ( ! $this->show_admin_field() || ! is_callable( array( $this, 'get_field_value' ) ) || ! FrmProFieldsHelper::field_on_current_page( $this->field['id'] ) ) {
			return parent::prepare_field_html( $args );
		}

		$args['html']      = $this->before_replace_html_shortcodes( $args, FrmFieldsHelper::get_default_html( 'text' ) );
		$args['errors']    = is_array( $args['errors'] ) ? $args['errors'] : array();
		$args['field_obj'] = $this;

		$label = FrmFieldsHelper::label_position( $this->field['label'], $this->field, $args['form'] );
		$this->set_field_column( 'label', $label );

		$html_shortcode = new FrmFieldFormHtml( $args );
		$html           = $html_shortcode->get_html();
		$html           = $this->after_replace_html_shortcodes( $args, $html );
		$html_shortcode->remove_collapse_shortcode( $html );

		return $html;
	}

	/**
	 * @since 4.03.06
	 */
	public function front_field_input( $args, $shortcode_atts ) {
		$value = $this->get_field_value( $args );
		$this->field['value'] = $value;
		$display = $this->prepare_display_value( $value, array() );

		wp_enqueue_script( 'jquery-ui-autocomplete' );

		$input = parent::front_field_input( $args, $shortcode_atts );
		$input = str_replace( ' type="user_id"', ' type="hidden"', $input );
		$input = '<input type="text" class="frm-user-search" value="' . esc_attr( $display ) . '" placeholder="' . esc_html__( 'Select a User', 'formidable' ) . '" />' . $input;

		return $input;
	}

	/**
	 * @since 4.03.06
	 */
	private function show_admin_field() {
		$show_admin_field = FrmAppHelper::is_admin() && current_user_can( 'frm_edit_entries' ) && ! FrmAppHelper::is_admin_page( 'formidable' );
		return $show_admin_field;
	}

	public function get_options( $values ) {
		$users = get_users(
			array(
				'fields'  => array( 'ID', 'user_login', 'display_name' ),
				'blog_id' => $GLOBALS['blog_id'],
				'orderby' => 'display_name',
			)
		);

		$options = array( '' => '' );
		foreach ( $users as $user ) {
			$options[ $user->ID ] = ( ! empty( $user->display_name ) ? $user->display_name : $user->user_login );
		}
		return $options;
	}
}
