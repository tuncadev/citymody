<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

/**
 * @since 4.0.02
 */
class FrmProFieldDefault extends FrmFieldDefault {

	protected function field_settings_for_type() {
		$settings = parent::field_settings_for_type();
		FrmProFieldsHelper::fill_default_field_display( $settings );
		return $settings;
	}
}
