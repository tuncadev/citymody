<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

/**
 * @since 3.06.04
 */
class FrmProUsageController {

	/**
	 * Add Pro settings to the settings array.
	 *
	 * @since 3.06.04
	 * @return array
	 */
	public static function settings( $settings ) {
		$setting_list  = FrmProAppHelper::get_settings();
		$pass_settings = array( 'menu_icon', 'date_format' );

		foreach ( $pass_settings as $setting ) {
			$settings[ $setting ] = $setting_list->{$setting};
		}

		$messages = array( 'edit_msg', 'update_value', 'already_submitted' );
		foreach ( $messages as $message ) {
			$settings['messages'][ $message ] = $setting_list->{$message};
		}

		return $settings;
	}

	/**
	 * Combine the rootline settings for usage analysis.
	 *
	 * @since 3.06.04
	 * @return array
	 */
	public static function form( $form, $atts ) {
		$saved_form = $atts['form'];
		if ( isset( $saved_form->options['rootline'] ) && ! empty( $saved_form->options['rootline'] ) ) {
			$form['rootline'] = array(
				'type'        => $saved_form->options['rootline'],
				'titles_on'   => $saved_form->options['rootline_titles_on'],
				'lines_off'   => $saved_form->options['rootline_lines_off'],
				'numbers_off' => $saved_form->options['rootline_numbers_off'],
			);
			$form['rootline'] = json_encode( $form['rootline'] );
		}

		return $form;
	}
}
