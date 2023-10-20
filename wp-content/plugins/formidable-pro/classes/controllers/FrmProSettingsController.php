<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

class FrmProSettingsController {

	/**
	 * Print out the license form for users with Pro installed.
	 * This includes the license type information, and actions to disconnect and clear the API cache.
	 *
	 * @return void
	 */
	public static function license_box() {
		$edd_update      = FrmProAppHelper::get_updater();
		$a               = FrmAppHelper::simple_get( 't', 'sanitize_title', 'general_settings' );
		$show_creds_form = self::show_license_form( $edd_update );
		$errors          = array();

		if ( ! empty( $edd_update->license ) ) {
			if ( class_exists( 'FrmFormApi' ) ) {
				$api    = new FrmFormApi( $edd_update->license );
				$errors = $api->error_for_license();
			} elseif ( is_callable( 'FrmProAddonsController::error_for_license' ) ) {
				$errors = FrmProAddonsController::error_for_license( $edd_update->license );
			}
		}

		self::display_errors( $errors );

		if ( $show_creds_form ) {
			$edd_update->pro_cred_form();
		}
	}

	/**
	 * Display license errors, but without messages from the frm_message_list filter.
	 *
	 * @since 6.0
	 *
	 * @param array $errors
	 * @return void
	 */
	private static function display_errors( $errors ) {
		add_filter( 'frm_message_list', '__return_empty_array', 99 );
		include FrmAppHelper::plugin_path() . '/classes/views/shared/errors.php';
		remove_filter( 'frm_message_list', '__return_empty_array', 99 );
	}

	public static function standalone_license_box() {
		$edd_update = FrmProAppHelper::get_updater();
		if ( self::show_license_form( $edd_update ) ) {
			include( FrmProAppHelper::plugin_path() . '/classes/views/settings/standalone_license_box.php' );
		}
	}

	/**
	 * @param FrmProEddController $edd_update
	 * @return bool
	 */
	private static function show_license_form( $edd_update ) {
		return ( ! is_multisite() || current_user_can( 'setup_network' ) || ! get_site_option( $edd_update->pro_wpmu_store ) );
	}

	/**
	 * @since 4.0
	 */
	public static function add_settings_section( $sections ) {
		add_action( 'frm_style_general_settings', 'FrmProSettingsController::general_style_settings' );
		add_action( 'frm_messages_settings_form', 'FrmProSettingsController::message_settings' );
		add_action( 'frm_settings_form', 'FrmProSettingsController::more_settings', 1 );

		$sections['white_label'] = array(
			'class'    => __CLASS__,
			'function' => 'white_label_settings',
			'name'     => isset( $sections['white_label'] ) ? $sections['white_label']['name'] : __( 'White Labeling', 'formidable' ),
			'icon'     => isset( $sections['white_label'] ) ? $sections['white_label']['icon'] : 'frm_icon_font frm_ghost_icon',
		);

		$sections['inbox'] = array(
			'class'    => __CLASS__,
			'function' => 'inbox_settings',
			'name'     => isset( $sections['inbox'] ) ? $sections['inbox']['name'] : __( 'Inbox', 'formidable' ),
			'icon'     => isset( $sections['inbox'] ) ? $sections['inbox']['icon'] : 'frm_icon_font frm_email_icon',
		);

		return $sections;
	}

	public static function general_style_settings( $frm_settings ) {
		include( FrmProAppHelper::plugin_path() . '/classes/views/settings/general_style.php' );
	}

	/**
	 * @since 4.0
	 */
	public static function message_settings( $frm_settings ) {
		$frmpro_settings = FrmProAppHelper::get_settings();
		require( FrmProAppHelper::plugin_path() . '/classes/views/settings/messages.php' );
	}

	public static function more_settings( $frm_settings ) {
		$frmpro_settings = FrmProAppHelper::get_settings();
		require( FrmProAppHelper::plugin_path() . '/classes/views/settings/form.php' );
	}

	/**
	 * @since 4.0
	 */
	public static function white_label_settings() {
		$frm_settings    = FrmAppHelper::get_settings();
		$frmpro_settings = FrmProAppHelper::get_settings();
		include( FrmProAppHelper::plugin_path() . '/classes/views/settings/white-label.php' );
	}

	/**
	 * @since 4.06.01
	 */
	public static function inbox_settings() {
		$settings      = FrmProAppHelper::get_settings();
		$message_types = $settings->inbox_types();
		$has_access    = self::has_current_access();
		include( FrmProAppHelper::plugin_path() . '/classes/views/settings/inbox.php' );
	}

	/**
	 * @since 4.06.01
	 */
	private static function has_current_access() {
		$user_type = FrmProAddonsController::license_type();
		return in_array( $user_type, array( 'elite', 'business', 'personal', 'grandfathered' ) );
	}

	/**
	 * @since 4.06.01
	 *
	 * @param array $messages
	 */
	public static function filter_inbox( $messages ) {
		if ( empty( $messages ) ) {
			return $messages;
		}

		$excluded = self::excluded_messages();
		if ( empty( $excluded ) ) {
			return $messages;
		}

		foreach ( $messages as $k => $message ) {
			if ( isset( $message['type'] ) && in_array( $message['type'], $excluded ) ) {
				unset( $messages[ $k ] );
			}
		}
		return $messages;
	}

	/**
	 * @since 4.06.01
	 */
	private static function excluded_messages() {
		$excluded = array();
		if ( ! self::has_current_access() ) {
			return $excluded;
		}

		$settings = FrmProAppHelper::get_settings();
		$types    = $settings->inbox_types();
		foreach ( $types as $type => $label ) {
			if ( ! empty( $settings->inbox ) && ! isset( $settings->inbox[ $type ] ) ) {
				$excluded[] = $type;
			}
		}

		return $excluded;
	}

	/**
	 * @since 4.06.01
	 */
	public static function inbox_badge( $count ) {
		$settings = FrmProAppHelper::get_settings();
		$off      = ! empty( $settings->inbox ) && ! isset( $settings->inbox['badge'] );
		if ( $off && self::has_current_access() ) {
			$count = ' <em class="frm_inbox_unread"></em>';
		}
		return $count;
	}

	public static function update( $params ) {
		global $frmpro_settings;
		$frmpro_settings = new FrmProSettings();
		$frmpro_settings->update( $params );
	}

	public static function store() {
		global $frmpro_settings;
		$frmpro_settings->store();
	}

	/**
	 * Add values to the advanced helpers on the settings/views pages
	 *
	 * @since 3.04.01
	 *
	 * @param array $helpers
	 * @param array $atts
	 */
	public static function advanced_helpers( $helpers, $atts ) {
		$repeat_field  = 0;
		$dynamic_field = 0;
		$linked_field  = 0;
		$file_field    = 0;

		foreach ( $atts['fields'] as $field ) {
			if ( empty( $repeat_field ) && FrmField::is_repeating_field( $field ) ) {
				$repeat_field = $field->id;
			} elseif ( empty( $dynamic_field ) && $field->type === 'data' && isset( $field->field_options['form_select'] ) && is_numeric( $field->field_options['form_select'] ) ) {
				add_action( 'frm_field_code_tab', 'FrmProSettingsController::field_sidebar' );
				$dynamic_field = $field->id;
				$linked_field  = $field->field_options['form_select'];
			} elseif ( empty( $file_field ) && $field->type === 'file' ) {
				$file_field = $field;
			}
			unset( $field );
		}

		if ( ! empty( $repeat_field ) ) {
			$helpers['repeat'] = array(
				'heading' => '',
				'codes'   => array(
					'foreach ' . $repeat_field . '][/foreach' => __( 'For Each', 'formidable-pro' ),
				),
			);
		}

		if ( ! empty( $dynamic_field ) ) {
			$helpers['dynamic'] = array(
				'heading' => '',
				'codes'   => array(
					$dynamic_field . ' show="created-at"' => __( 'Creation Date', 'formidable-pro' ),
					$dynamic_field . ' show="' . $linked_field . '"' => __( 'Field From Entry', 'formidable-pro' ),
				),
			);
		}

		if ( ! empty( $file_field ) ) {
			$helpers['default']['codes'][ $file_field->id . ' show_image=1' ]    = __( 'Show image', 'formidable-pro' );
			$helpers['default']['codes'][ $file_field->id . ' show=id' ]         = __( 'Image ID', 'formidable-pro' );
			$helpers['default']['codes'][ $file_field->id . ' show_filename=1' ] = __( 'Image Name', 'formidable-pro' );
		}

		return $helpers;
	}

	/**
	 * Add extra field shortcodes in the shortcode lists
	 *
	 * @since 3.04.01
	 */
	public static function field_sidebar( $atts ) {
		$field = $atts['field'];
		if ( $field->type !== 'data' || ! isset( $field->field_options['form_select'] ) || ! is_numeric( $field->field_options['form_select'] ) ) {
			return;
		}

		//get all fields from linked form
		$linked_form = FrmDb::get_var( 'frm_fields', array( 'id' => $field->field_options['form_select'] ), 'form_id' );

		$linked_fields = FrmField::getAll(
			array(
				'fi.type not' => FrmField::no_save_fields(),
				'fi.form_id'  => $linked_form,
			)
		);

		if ( $linked_fields ) {
			foreach ( $linked_fields as $linked_field ) {
				$class = is_callable( 'FrmFormsHelper::insert_opt_html' ) ? 'FrmFormsHelper' : 'FrmAppHelper';

				$class::insert_opt_html(
					array(
						'id'   => $field->id . ' show=' . $linked_field->id,
						'key'  => $field->field_key . ' show=' . $linked_field->field_key,
						'name' => $linked_field->name,
						'type' => $linked_field->type,
						'class' => 'frm-customize-list',
					)
				);
			}
		}
	}
}
