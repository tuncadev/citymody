<?php
/*
Plugin Name: Formidable Forms Pro
Description: Add more power to your forms, and bring your reports and data management to the front-end.
Version: 6.5.2
Plugin URI: https://formidableforms.com/
Author URI: https://formidableforms.com/
Author: Strategy11
Text Domain: formidable-pro
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

if ( ! function_exists( 'load_formidable_pro' ) ) {

	add_action( 'plugins_loaded', 'load_formidable_pro', 1 );
	function load_formidable_pro() {
		$is_free_installed = function_exists( 'load_formidable_forms' );
		if ( $is_free_installed ) {
			// Add the autoloader
			spl_autoload_register( 'frm_pro_forms_autoloader' );

			FrmProHooksController::load_pro();
		} else {
			add_action( 'admin_notices', 'frm_pro_forms_incompatible_version' );
		}
	}

	/**
	 * @since 3.0
	 */
	function frm_pro_forms_autoloader( $class_name ) {
		// Only load Frm classes here
		if ( ! preg_match( '/^FrmPro.+$/', $class_name ) ) {
			return;
		}

		$filepath = dirname( __FILE__ );
		if ( frm_pro_is_deprecated_class( $class_name ) ) {
			$filepath .= '/deprecated/' . $class_name . '.php';
			if ( file_exists( $filepath ) ) {
				require $filepath;
			}
		} else {
			frm_class_autoloader( $class_name, $filepath );
		}
	}

	function frm_pro_is_deprecated_class( $class ) {
		$deprecated = array(
			'FrmProCreditCard',
			'FrmProAddress',
			'FrmProDisplay',
			'FrmProDisplaysController',
			'FrmProDropdownFieldsController',
			'FrmProTimeField',
			'FrmProPhoneFieldsController',
			'FrmProTextFieldsController',
		);
		return in_array( $class, $deprecated );
	}

	/**
	 * If the site is running Formidable Pro 1.x, this plugin will not work.
	 * Show a notification.
	 *
	 * @since 3.0
	 */
	function frm_pro_forms_incompatible_version() {
		$ran_auto_install = get_option( 'frm_ran_auto_install' );
		if ( false === $ran_auto_install ) {
			global $pagenow;

			if ( 'update.php' !== $pagenow && 'update-core.php' !== $pagenow ) {
				update_option( 'frm_ran_auto_install', true, 'no' );

				include_once( dirname( __FILE__ ) . '/classes/models/FrmProInstallPlugin.php' );

				$plugin_helper = new FrmProInstallPlugin(
					array(
						'plugin_file'  => 'formidable/formidable.php',
					)
				);
				$plugin_helper->maybe_install_and_activate();
			}
		}

		?>
		<div class="error">
			<p><?php esc_html_e( 'Formidable Forms Premium requires Formidable Forms Lite to be installed.', 'formidable-pro' ); ?></p>
		</div>
		<?php
	}
}

/**
 * Handles plugin activation.
 *
 * This hook is executed upon plugin activation.
 */
register_activation_hook(
	__FILE__,
	function() {
		// Check if free version of Formidable Forms is installed.
		$is_free_installed = function_exists( 'load_formidable_forms' );
		if ( ! $is_free_installed ) {
			return;
		}

		// Register autoloader for Formidable Pro classes.
		spl_autoload_register( 'frm_pro_forms_autoloader' );

		// Updates the default stylesheet.
		FrmProHooksController::load_pro();
		FrmProAppController::update_stylesheet();
	}
);

/**
 * Handles plugin deactivation.
 *
 * This hook is executed upon plugin deactivation.
 */
register_deactivation_hook(
	__FILE__,
	function() {
		if ( ! class_exists( 'FrmProCronController', false ) ) {
			// Avoid using FrmProAppHelper::plugin_path to avoid a "PHP Fatal error:  Uncaught Error: Class 'FrmProAppHelper' not found" error.
			require_once __DIR__ . '/classes/controllers/FrmProCronController.php';
		}

		// Remove any scheduled cron jobs associated with the plugin.
		FrmProCronController::remove_cron();

		// Check if free version of Formidable Forms is installed.
		$is_free_installed = function_exists( 'load_formidable_forms' );
		if ( ! $is_free_installed ) {
			return;
		}

		// Register autoloader for Formidable Pro classes.
		spl_autoload_register( 'frm_pro_forms_autoloader' );

		// Updates the default stylesheet.
		remove_action( 'frm_include_front_css', 'FrmProStylesController::include_front_css' );
		remove_filter( 'frm_default_style_settings', 'FrmProStylesController::add_defaults' );
		remove_filter( 'frm_override_default_styles', 'FrmProStylesController::override_defaults' );
		FrmProAppController::update_stylesheet();
	}
);
