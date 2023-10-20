<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

/**
 * @since 3.0
 */
class FrmProFieldText extends FrmFieldText {
	use FrmProFieldAutocompleteField;

	protected function field_settings_for_type() {
		$settings = parent::field_settings_for_type();

		$settings['autopopulate'] = true;
		$settings['calc']         = true;
		$settings['read_only']    = true;
		$settings['unique']       = true;
		$settings['prefix']       = true;
		$settings['autocomplete'] = true;

		FrmProFieldsHelper::fill_default_field_display( $settings );
		return $settings;
	}

	/**
	 * @since 4.05
	 */
	protected function builder_text_field( $name = '' ) {
		$html  = FrmProFieldsHelper::builder_page_prepend( $this->field );
		$field = parent::builder_text_field( $name );
		return str_replace( '[input]', $field, $html );
	}
}
