<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

/**
 * @since 5.4.2
 */
class FrmProSiteHealthController {

	/**
	 * Adds an Formidable section to Site Health info tab.
	 *
	 * @param array $info
	 * @return array
	 */
	public static function debug_information( $info ) {
		$expired            = FrmProAddonsController::is_license_expired();
		$info['formidable'] = array(
			'label'      => self::get_name(),
			'show_count' => false,
			'fields'     => array(
				'license' => array(
					'label' => __( 'License Status', 'formidable-pro' ),
					'value' => esc_html(
						sprintf(
							/* translators: %1$s: License status (ie. Expired, Active), %2$s: License Type (ie. Elite, Business, Plus) */
							__( '%1$s (%2$s)', 'formidable-pro' ),
							$expired ? __( 'Expired', 'formidable-pro' ) : __( 'Active', 'formidable-pro' ),
							FrmProAddonsController::get_readable_license_type()
						)
					),
				),
			),
		);
		return $info;
	}

	/**
	 * Adds tests for Site Health status tab.
	 *
	 * @param array $tests
	 * @return array
	 */
	public static function site_status_tests( $tests ) {
		$tests['direct']['formidable-pro'] = array(
			'label' => self::get_name(),
			'test'  => array( __CLASS__, 'license_test' ),
		);
		return $tests;
	}

	/**
	 * Test for valid Formidablie Pro license.
	 *
	 * @return array
	 */
	public static function license_test() {
		$result = array(
			'badge'  => array(
				'label' => FrmAppHelper::get_menu_name(),
				'color' => 'blue',
			),
			'actions' => '',
			'test'    => 'frm_is_license_expired',
		);

		$status = FrmProAddonsController::get_license_status();

		$using_old_version = FrmProAddonsController::pro_is_behind_latest_version();

		if ( 'active' === $status ) {
			if ( $using_old_version ) {
				$result['status']         = 'critical';
				$result['badge']['color'] = 'red';
				$result['label']          = self::get_name() . ' needs to be updated';
				$result['description']    = '<p>An update is available for ' . self::get_name() . '. Upgrade to the latest version to receive all the latest features, bug fixes and security improvements.</p>';
				$result['actions']        = '<a href="' . esc_url( admin_url( 'plugins.php' ) ) . '">' . esc_html__( 'Go to Plugins', 'formidable-pro' ) . '</a>';
				return $result;
			}

			$result['status'] = 'good';
			$result['label']  = sprintf(
				/* translators: %s: Label for plugin (default is Formidable Forms) */
				esc_html__( 'Your version of %s is up to date and your license is active', 'formidable-pro' ),
				self::get_name()
			);
			$result['description'] = sprintf(
				'<p>%s</p>',
				esc_html(
					sprintf(
						/* translators: %1$s: Label for plugin, %2$s: License Type (ie. Elite, Business, Plus) */
						__( 'You\'re using %1$s %2$s. Enjoy!', 'formidable-pro' ),
						self::get_name(),
						FrmProAddonsController::get_readable_license_type()
					)
				)
			);
			return $result;
		}

		// License is expired or expiring.
		$utc_medium  = FrmProAddonsController::get_utc_medium_for_license_status( $status );
		$expired     = 'expiring' !== $status;
		$is_critical = $expired || $using_old_version;

		$result['status']         = $is_critical ? 'critical' : 'recommended';
		$result['badge']['color'] = $is_critical ? 'red' : 'orange';
		$result['label']          = self::get_name() . ' license is ' . ( $expired ? 'expired' : 'expiring' );

		if ( $using_old_version ) {
			$result['label'] .= ' and needs to be updated';
		}

		$result['description']    = sprintf( '<p>%s</p>', FrmProAddonsController::message_text_for_license_status() );
		$result['actions']        = '<a href="' . esc_url( FrmAppHelper::admin_upgrade_link( $utc_medium, 'account/downloads/' ) ) . '">' . esc_html__( 'Renew Now', 'formidable' ) . '</a>';
		return $result;
	}

	/**
	 * Get white labelled name used for Site Health page.
	 *
	 * @return string
	 */
	private static function get_name() {
		$name    = FrmAppHelper::get_menu_name();
		if ( 'Formidable' === $name ) {
			$name .= ' ' . __( 'Forms', 'formidable' );
		}
		return $name;
	}
}
