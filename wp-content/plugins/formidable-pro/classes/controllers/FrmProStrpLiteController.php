<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

/**
 * @since 6.5
 */
class FrmProStrpLiteController {

	/**
	 * Register additional scripts to support Pro functionality with Stripe Lite.
	 * This includes:
	 * - Saving drafts, and going to the previous page in a multiple-page form without processing a payment.
	 * - Adding address field meta to a Stripe card object.
	 * - Enabling the submit button when both the Stripe elements are complete and the button is conditionally enabled.
	 *
	 * @return void
	 */
	public static function maybe_register_stripe_scripts() {
		if ( class_exists( 'FrmStrpHooksController', false ) ) {
			// There is no need for these scripts when the Stripe add on is active.
			// These scripts are only for supporting Stripe Lite.
			return;
		}

		if ( ! class_exists( 'FrmStrpLiteHooksController', false ) ) {
			// Only register stripe scripts if Stripe Lite is available.
			return;
		}

		if ( ! wp_script_is( 'formidable-stripe', 'enqueued' ) ) {
			// Only register scripts if Stripe Lite has registered scripts.
			return;
		}

		$suffix = FrmAppHelper::js_suffix();
		if ( '.min' === $suffix && is_readable( FrmAppHelper::plugin_path() . '/js/frmstrp.min.js' ) ) {
			// If the combined Stripe JS file is available, exit early.
			return;
		}

		if ( ! $suffix && ! is_readable( FrmProAppHelper::plugin_path() . '/js/frmstrp.js' ) ) {
			// The unminified file is not included in releases so force the minified script.
			$suffix = '.min';
		}

		$dependencies = array( 'formidable' );
		if ( ! $suffix || ! FrmFormsController::has_combo_js_file() ) {
			$dependencies[] = 'formidablepro';
		}

		wp_register_script(
			'formidablepro_stripe',
			FrmProAppHelper::plugin_url() . '/js/frmstrp' . $suffix . '.js',
			$dependencies,
			FrmProDb::$plug_version,
			true
		);
		wp_enqueue_script( 'formidablepro_stripe' );
	}

	/**
	 * Insert an address field in the Stripe Lite customer info section after the email field.
	 *
	 * @since 6.5
	 *
	 * @param array $args {
	 *     @type FrmFormAction $action_control
	 *     @type array         $field_dropdown_atts
	 * }
	 * @return void
	 */
	public static function customer_info_after_email( $args ) {
		$action_control      = $args['action_control'];
		$field_dropdown_atts = $args['field_dropdown_atts'];

		require FrmProAppHelper::plugin_path() . '/classes/views/frmpro-form-actions/_stripe_address.php';
	}

	/**
	 * Load required registation add on hooks for Stripe Lite.
	 *
	 * @return void
	 */
	public static function add_registration_hooks() {
		// Registation add on support.
		if ( ! class_exists( 'FrmRegHooksController', false ) ) {
			return;
		}

		// Add payment success event to registration actions.
		add_filter(
			'frm_register_action_options',
			/**
			 * @param array $options
			 * @return array
			 */
			function( $options ) {
				$options['event'][] = 'payment-success';
				return $options;
			}
		);
	}

	/**
	 * @param array $files
	 * @return array
	 */
	public static function combine_stripe_js_files( $files ) {
		$files[] = FrmProAppHelper::plugin_url() . '/js/frmstrp.min.js';
		return $files;
	}

	/**
	 * Define a default for the Address field in Stripe Lite payment actions.
	 *
	 * @param array $defaults
	 * @return array
	 */
	public static function add_payment_action_defaults( $defaults ) {
		$defaults['billing_address'] = '';
		return $defaults;
	}

	/**
	 * Add address info for the frm_stripe_vars JS global.
	 *
	 * @param array   $settings
	 * @param WP_Post $action
	 * @return array
	 */
	public static function add_settings_for_js( $settings, $action ) {
		$settings['address'] = $action->post_content['billing_address'];
		return $settings;
	}
}
