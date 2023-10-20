<?php
/**
 * @subpackage  Upgrader Skin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'FrmInstallerSkin' ) ) {
	return;
}

class FrmProInstallerSkin extends FrmInstallerSkin {

	public function error( $errors ) {
		if ( $this->check_errors_for_failed_view_migration( $errors ) ) {
			return;
		}

		return parent::error( $errors );
	}

	/**
	 * Check errors for a fail view migration
	 * If this is true we can return gracefully instead of dying with a JSON encoded error
	 *
	 * @param array $errors
	 * @return bool
	 */
	private function check_errors_for_failed_view_migration( $errors ) {
		if ( ! empty( $errors->errors['mkdir_failed_destination'] ) && ! empty( $errors->error_data['mkdir_failed_destination'] ) ) {
			if ( false !== strpos( $errors->error_data['mkdir_failed_destination'], 'plugins/formidable-views/' ) ) {
				return true;
			}
		}
		return false;
	}

}
