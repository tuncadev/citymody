<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

class FrmProAddonsController extends FrmAddonsController {

	/**
	 * Render a conditional action button for a specified plugin
	 *
	 * @since 4.09.01
	 *
	 * @param string $plugin
	 * @param array|string $upgrade_link_args
	 * @return void
	 */
	public static function conditional_action_button( $plugin, $upgrade_link_args ) {
		if ( ! is_callable( self::class . '::get_addon' ) ) {
			// FrmAddonsController may not have this function depending on version.
			return;
		}

		$addon = self::get_addon( $plugin );
		$atts = array(
			'addon'         => $addon,
			'license_type'  => self::get_license_type(),
			'plan_required' => FrmFormsHelper::get_plan_required( $addon ),
			'upgrade_link'  => FrmAppHelper::admin_upgrade_link( $upgrade_link_args ),
		);

		self::show_conditional_action_button( $atts );
	}

	/**
	 * Render a conditional action button for an add on
	 *
	 * @since 4.09
	 *
	 * @param array $atts {
	 *     @type array        $addon
	 *     @type string|false $license_type
	 *     @type string       $plan_required
	 *     @type string       $upgrade_link
	 * }
	 * @return void
	 */
	public static function show_conditional_action_button( $atts ) {
		$addon         = $atts['addon'];
		$license_type  = $atts['license_type'];
		$plan_required = $atts['plan_required'];
		$upgrade_link  = $atts['upgrade_link'];
		if ( ! $addon ) {
			self::addon_upgrade_link( $addon, $upgrade_link );

		} elseif ( $addon['status']['type'] === 'installed' ) {
			?>
			<a href="#" rel="<?php echo esc_attr( $addon['plugin'] ); ?>" class="button button-primary frm-button-primary frm-button-sm frm-activate-addon <?php echo esc_attr( empty( $addon['activate_url'] ) ? 'frm_hidden' : '' ); ?>">
				<?php esc_html_e( 'Activate', 'formidable' ); ?>
			</a>
			<?php
		} elseif ( ! empty( $addon['url'] ) ) {
			?>
			<a href="#" class="frm-install-addon button button-primary frm-button-primary frm-button-sm" rel="<?php echo esc_attr( $addon['url'] ); ?>" aria-label="<?php esc_attr_e( 'Install', 'formidable' ); ?>">
				<?php esc_html_e( 'Install', 'formidable' ); ?>
			</a>
			<?php
		} elseif ( $license_type && $license_type === strtolower( $plan_required ) ) {
			?>
			<a class="install-now button button-secondary frm-button-secondary frm-button-sm" href="<?php echo esc_url( FrmAppHelper::admin_upgrade_link( 'addons', 'account/downloads/' ) . '&utm_content=' . $addon['slug'] ); ?>" target="_blank" aria-label="<?php esc_attr_e( 'Upgrade Now', 'formidable' ); ?>">
				<?php esc_html_e( 'Renew Now', 'formidable' ); ?>
			</a>
			<?php
		} else {
			self::addon_upgrade_link( $addon, $upgrade_link );
		}
	}

	/**
	 * @since 4.06
	 * @since 5.0.03 added $force_type parameter.
	 *
	 * @param bool $force_type return type instead of checking expiration or code so "expired" or "grandfathered" are never returned.
	 * @return string
	 */
	public static function license_type( $force_type = false ) {
		$api    = new FrmFormApi();
		$addons = $api->get_api_info();
		$type   = 'free';

		if ( isset( $addons['error'] ) ) {
			if ( ! $force_type && isset( $addons['error']['code'] ) && $addons['error']['code'] === 'expired' ) {
				return $addons['error']['code'];
			}
			$type = isset( $addons['error']['type'] ) ? $addons['error']['type'] : $type;
		}

		if ( ! is_callable( array( __CLASS__, 'get_pro_from_addons' ) ) ) {
			$pro = isset( $addons['93790'] ) ? $addons['93790'] : array();
		} else {
			$pro = self::get_pro_from_addons( $addons );
		}

		if ( $type === 'free' ) {
			$type = isset( $pro['type'] ) ? $pro['type'] : $type;
			if ( $type === 'free' ) {
				return $type;
			}
		}

		if ( $force_type ) {
			return strtolower( $type );
		}

		if ( isset( $pro['code'] ) && $pro['code'] === 'grandfathered' ) {
			return $pro['code'];
		}

		$expires = isset( $pro['expires'] ) ? $pro['expires'] : '';
		$expired = $expires ? $expires < time() : false;
		return $expired ? 'expired' : strtolower( $type );
	}

	/**
	 * @since 5.0.03
	 *
	 * @return string "Basic", "Plus", "Starter", "Business" or "Elite" depending on license type. "Premium" by default if type can not be determined.
	 */
	public static function get_readable_license_type() {
		$license_type = self::license_type( true );
		if ( 0 === strpos( $license_type, 'views-' ) ) {
			// Remove "views-" from license type if it exists.
			$license_type = substr( $license_type, 6 );
		}

		if ( in_array( $license_type, array( 'personal', 'creator' ), true ) ) {
			$license_type = 'plus';
		} elseif ( $license_type === 'free' ) {
			$license_type = 'lite';
		} elseif ( ! in_array( $license_type, array( 'basic', 'elite', 'business', 'plus', 'starter' ), true ) ) {
			$license_type = 'premium';
		}

		return ucfirst( $license_type );
	}

	/**
	 * @since 4.08
	 *
	 * @return boolean|int false or the number of days until expiration.
	 */
	public static function is_license_expiring() {
		$version_info = self::get_primary_license_info();
		if ( ! isset( $version_info['active_sub'] ) || $version_info['active_sub'] !== 'no' ) {
			// Check for a subscription first.
			return false;
		}

		if ( isset( $version_info['error'] ) || empty( $version_info['expires'] ) ) {
			// It's either invalid or already expired.
			return false;
		}

		$expiration = $version_info['expires'];
		$days_left  = ( $expiration - time() ) / DAY_IN_SECONDS;
		if ( $days_left > 30 || $days_left < 0 ) {
			return false;
		}

		return $days_left;
	}

	/**
	 * Get the timestamp for expiration.
	 *
	 * @since 5.4.2
	 */
	private static function license_expiration() {
		$version_info = self::get_primary_license_info();
		return empty( $version_info['expires'] ) ? '' : $version_info['expires'];
	}

	/**
	 * Print out an renewal message for admin banner if applicable for expired, expiring, and grace period statuses.
	 *
	 * @since 5.4.2
	 *
	 * @return bool True if a message is shown.
	 */
	public static function admin_banner() {
		$status = self::get_license_status();
		if ( self::should_skip_renewal_message( $status ) ) {
			return false;
		}

		$show_close_icon = 'expiring' === $status && current_user_can( 'administrator' );

		if ( 'expired' === $status ) {
			$wrapper_class   = 'frm-upgrade-bar';
		} else { // $status is 'expiring' or 'grace'.
			$wrapper_class   = 'frm-banner-alert ' . ( 'expiring' === $status ? 'frm_warning_style' : 'frm_error_style' );
		}

		$wrapper_class .= ' frm_previous_install'; // Errors with frm_previous_install do not get hidden on the Form builder page. See issue #3803.

		$utc_medium = self::get_utc_medium_for_license_status( $status );
		?>
		<div class="<?php echo esc_attr( $wrapper_class ); ?>">
			<?php
			FrmAppHelper::icon_by_class( 'frmfont frm_alert_icon' );
			echo '&nbsp;';
			?>
			<span><?php self::message_text_for_license_status( true, $status ); ?></span>

			<a href="<?php echo esc_url( FrmAppHelper::admin_upgrade_link( $utc_medium, 'account/downloads/' ) ); ?>">
				<?php esc_html_e( 'Renew Now', 'formidable-pro' ); ?>
			</a>

			<?php if ( $show_close_icon ) { ?>
				<a style="float: right; margin-right: 30px; --primary-color: var(--dark-grey);" href="<?php echo esc_url( self::get_dismiss_renewal_message_action_url() ); ?>">
					<?php FrmAppHelper::icon_by_class( 'frmfont frm_close_icon', array( 'aria-label' => __( 'Close', 'formidable' ) ) ); ?>
				</a>
			<?php } ?>
		</div>
		<?php

		return true;
	}

	/**
	 * Get the active license status.
	 *
	 * @since 5.4.2
	 *
	 * @return string either 'grace', 'expired', 'expiring', or 'active'.
	 */
	public static function get_license_status() {
		if ( self::is_license_expired() ) {
			return self::check_grace_period() ? 'grace' : 'expired';
		}
		return self::is_license_expiring() ? 'expiring' : 'active';
	}

	/**
	 * Get or echo the message text for active license status.
	 *
	 * @since 5.4.2
	 *
	 * @param bool         $echo
	 * @param string|false $status
	 * @return string|void
	 */
	public static function message_text_for_license_status( $echo = false, $status = false ) {
		if ( ! is_callable( 'FrmAppHelper::clip' ) ) {
			if ( $echo ) {
				return;
			}
			return '';
		}

		if ( false === $status ) {
			$status = self::get_license_status();
		}

		$echo_function = __CLASS__ . '::print_' . $status;

		if ( ! is_callable( $echo_function ) ) {
			$echo_function = function() {};
		}

		return FrmAppHelper::clip( $echo_function, $echo );
	}

	/**
	 * Print grace period message
	 *
	 * @since 5.4.2
	 *
	 * @return void
	 */
	public static function print_grace() {
		echo 'Your account license has expired. Access to pro features will be limited ';

		$grace_period = self::get_grace_period();
		if ( 0 === $grace_period ) {
			echo 'soon.';
			return;
		}

		$time_remaining = FrmAppHelper::human_time_diff( $grace_period );
		echo 'in <strong>' . esc_html( $time_remaining ) . '</strong>.';
	}

	/**
	 * Print expired status message.
	 *
	 * @since 5.4.2
	 *
	 * @return void
	 */
	public static function print_expired() {
		esc_html_e( 'Your account license has expired and is no longer qualified for important security updates.', 'formidable-pro' );
	}

	/**
	 * Print expiring status message.
	 *
	 * @since 5.4.2
	 *
	 * @return void
	 */
	public static function print_expiring() {
		$expires  = self::license_expiration();
		$expiring = FrmAppHelper::human_time_diff( $expires );

		printf(
			/* translators: %s: Duration until license expires (ie 5 days, 1 hour) */
			esc_html__( 'Your account license expires in %s.', 'formidable-pro' ),
			'<strong>' . esc_html( $expiring ) . '</strong>'
		);
	}

	/**
	 * @since 5.4.2
	 *
	 * @param string $status
	 * @return bool
	 */
	private static function should_skip_renewal_message( $status ) {
		// No banner for active status.
		if ( 'active' === $status ) {
			return true;
		}

		// Exit early if the user has dismissed the expiring license warning within the last day.
		if ( 'expiring' === $status ) {
			$dismissed_renewal_message = get_option( 'frm_dismissed_renewal_message' );
			if ( false !== $dismissed_renewal_message && time() - (int) $dismissed_renewal_message < DAY_IN_SECONDS ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @since 5.4.2
	 *
	 * @return string
	 */
	private static function get_dismiss_renewal_message_action_url() {
		return wp_nonce_url( admin_url( 'admin-ajax.php?action=frm_dismiss_renewal_message' ) );
	}

	/**
	 * Dismiss renewal message via AJAX request.
	 *
	 * @return void
	 */
	public static function dismiss_renewal_message() {
		FrmAppHelper::permission_check( 'administrator' );

		if ( ! wp_verify_nonce( FrmAppHelper::simple_get( '_wpnonce', '', 'sanitize_text_field' ) ) ) {
			$frm_settings = FrmAppHelper::get_settings();
			die( esc_html( $frm_settings->admin_permission ) );
		}

		update_option( 'frm_dismissed_renewal_message', time(), 'no' );
		wp_safe_redirect( self::get_after_dismiss_redirect_url() );
	}

	/**
	 * @since 5.4.2
	 *
	 * @return string URL to redirect to after dismissing renewal message.
	 */
	private static function get_after_dismiss_redirect_url() {
		$referer = FrmAppHelper::get_server_value( 'HTTP_REFERER' );
		if ( ! $referer ) {
			return self::get_default_dismiss_redirect_url();
		}

		$parsed = parse_url( $referer );
		if ( ! is_array( $parsed ) || empty( $parsed['query'] ) || empty( $parsed['path'] ) ) {
			return self::get_default_dismiss_redirect_url();
		}

		$parts = explode( '/', $parsed['path'] );
		$path  = end( $parts );
		if ( ! in_array( $path, array( 'edit.php', 'admin.php' ), true ) ) {
			return self::get_default_dismiss_redirect_url();
		}

		$query = $parsed['query'];
		return admin_url( $path . '?' . $query );
	}

	/**
	 * @since 5.4.2
	 *
	 * @return string
	 */
	private static function get_default_dismiss_redirect_url() {
		return admin_url( 'admin.php?page=formidable' );
	}

	/**
	 * @param string $status
	 * @return string
	 */
	public static function get_utc_medium_for_license_status( $status ) {
		return 'expiring' === $status ? 'form-renew' : 'form-expired';
	}

	/**
	 * @since 5.4.2
	 *
	 * @return bool True if within grace period.
	 */
	private static function check_grace_period() {
		$grace_period = self::get_grace_period();
		return 0 === $grace_period || time() < $grace_period;
	}

	/**
	 * @since 5.4.2
	 *
	 * @return int
	 */
	private static function get_grace_period() {
		$info = self::get_primary_license_info();

		foreach ( array( 'grace', 'expires' ) as $key ) {
			if ( ! isset( $info[ $key ] ) || ! is_numeric( $info[ $key ] ) ) {
				return 0;
			}
		}

		$grace   = intval( $info['grace'] );
		$expires = intval( $info['expires'] );

		if ( $grace < $expires ) {
			return 0;
		}

		return $grace;
	}

	/**
	 * @since 4.06.02
	 */
	public static function ajax_multiple_addons() {
		self::install_addon_permissions();

		// Set the current screen to avoid undefined notices.
		global $hook_suffix;
		set_current_screen();

		$free_plugin_supports_current_plugin_var = is_callable( 'self::get_current_plugin' );

		$download_urls = explode( ',', FrmAppHelper::get_param( 'plugin', '', 'post' ) );
		FrmAppHelper::sanitize_value( 'esc_url_raw', $download_urls );

		foreach ( $download_urls as $download_url ) {
			if ( $free_plugin_supports_current_plugin_var ) {
				self::$plugin = $download_url;
			} else {
				$_POST['plugin'] = $download_url;
			}

			if ( strpos( $download_url, 'http' ) !== false ) {
				// Installing.
				self::maybe_show_cred_form();

				$installed = self::install_addon();
				self::maybe_activate_addon( $installed );
			} else {
				// Activating.
				self::maybe_activate_addon( $download_url );
			}
		}

		echo json_encode( __( 'Your plugins have been installed and activated.', 'formidable' ) );

		wp_die();
	}

	/**
	 * @since 5.4.2
	 *
	 * @return bool
	 */
	public static function is_expired_outside_grace_period() {
		return self::is_license_expired() && ! self::check_grace_period();
	}

	/**
	 * @since 5.4.2
	 *
	 * @return bool
	 */
	public static function pro_is_behind_latest_version() {
		$version = FrmProDb::$plug_version;
		$addons = self::get_primary_license_info();

		if ( ! is_callable( 'self::get_pro_from_addons' ) ) {
			return false;
		}

		$pro = self::get_pro_from_addons( $addons );
		if ( ! $pro ) {
			return false;
		}

		return version_compare( $version, $pro['version'], '<' );
	}

	/**
	 * @since 4.09.01
	 * @deprecated 6.0
	 *
	 * @return void
	 */
	public static function show_expired_message() {
		_deprecated_function( __METHOD__, '6.0' );
	}

	/**
	 * @since 4.08
	 * @deprecated 6.0
	 *
	 * @return void
	 */
	public static function expiring_message() {
		_deprecated_function( __METHOD__, '6.0', 'FrmProAddonsController::admin_banner' );
		self::admin_banner();
	}

	/**
	 * @since 4.07
	 * @deprecated 6.0
	 *
	 * @return void
	 */
	public static function renewal_message() {
		// This function gets called from lite for the frm_page_footer action.
		// This function does nothing rather than call admin_banner to avoid classes like "frm-banner-alert" and "frm-upgrade-bar"
		// from appearing in the footer.
		// _deprecated_function isn't called as this function gets called from old versions of lite.
	}

	/**
	 * @since 5.5.1
	 *
	 * @return void
	 */
	public static function maybe_disable_form_actions() {
		if ( ! self::is_expired_outside_grace_period() || 'settings' !== FrmAppHelper::get_param( 'frm_action' ) ) {
			return;
		}

		add_filter(
			'frm_registered_form_actions',
			function( $actions ) {
				self::add_filters_to_disable_registered_actions( $actions );
				return $actions;
			},
			99
		);
	}

	/**
	 * @since 5.5.1
	 *
	 * @param array<string,string> $actions
	 * @return void
	 */
	private static function add_filters_to_disable_registered_actions( $actions ) {
		$keys = array_keys( $actions );

		foreach ( $keys as $key ) {
			if ( 'email' === $key ) {
				continue;
			}

			add_filter(
				'frm_' . $key . '_action_options',
				/**
				 * @param array $options
				 * @return array
				 */
				function( $options ) {
					$options['active']   = false;
					if ( false === strpos( $options['classes'], 'frm_show_upgrade' ) ) {
						$options['classes'] .= ' frm_show_upgrade';
					}
					$options['classes'] .= ' frm_show_expired_modal';
					return $options;
				},
				99
			);
		}

		add_filter(
			'frm_action_groups',
			/**
			 * @param array $groups
			 * @return array
			 */
			function( $groups ) {
				if ( isset( $groups['payment'] ) && is_array( $groups['payment'] ) && isset( $groups['payment']['icon'] ) ) {
					$groups['payment']['icon'] .= ' frm_show_expired_modal';
				}
				return $groups;
			}
		);
	}

	/**
	 * @return void
	 */
	public static function before_add_form_action() {
		if ( self::is_expired_outside_grace_period() ) {
			$action_type = FrmAppHelper::get_param( 'type', '', 'post', 'sanitize_text_field' );
			if ( 'email' !== $action_type ) {
				wp_die( -1 );
			}
		}
	}
}
