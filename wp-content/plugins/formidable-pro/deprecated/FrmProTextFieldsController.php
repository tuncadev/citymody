<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

class FrmProTextFieldsController {

	/**
	 * Display the Pro field options for a Text field in the form builder
	 *
	 * @since 2.02.06
	 * @deprecated 4.0
	 *
	 * @param array $field
	 * @param array $display
	 */
	public static function show_field_options_in_form_builder( $field, $display ) {
		_deprecated_function( __FUNCTION__, '4.0', 'FrmFieldType->show_primary_options' );
	}
}
