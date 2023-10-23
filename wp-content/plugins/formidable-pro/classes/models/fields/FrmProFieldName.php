<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

/**
 * @since 6.4
 */
class FrmProFieldName extends FrmFieldName {

	protected function field_settings_for_type() {
		$settings = parent::field_settings_for_type();
		$settings['read_only'] = true;

		return $settings;
	}
}
