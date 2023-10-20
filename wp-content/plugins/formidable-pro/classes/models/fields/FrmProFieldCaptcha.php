<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

/**
 * @since 3.0
 */
class FrmProFieldCaptcha extends FrmFieldCaptcha {

	private static $checked;

	protected function field_settings_for_type() {
		$settings = parent::field_settings_for_type();

		FrmProFieldsHelper::fill_default_field_display( $settings );
		return $settings;
	}

	public function front_field_input( $args, $shortcode_atts ) {
		if ( self::checked() ) {
			return '';
		}

		return parent::front_field_input( $args, $shortcode_atts );
	}

	/**
	 * @since 4.07
	 * @param array $args
	 * @return array
	 */
	public function validate( $args ) {
		if ( ! $this->should_validate() ) {
			return array();
		}

		if ( ! is_callable( array( $this, 'validate_against_api' ) ) ) {
			return parent::validate( $args );
		}

		if ( isset( $_POST['g-recaptcha-response'] ) || isset( $_POST['h-captcha-response'] ) ) {
			$errors = $this->validate_against_api( $args );

			if ( $errors ) {
				return $errors;
			}

			$this->maybe_set_captcha_score_in_form_state();
			self::$checked = wp_create_nonce( 'frm_captcha' );
			return array();
		}

		if ( self::validate_checked() ) {
			$this->maybe_pull_captcha_score_from_form_state();
			return array();
		}

		return array( 'field' . $args['id'] => __( 'The captcha is missing from this form', 'formidable-pro' ) );
	}

	/**
	 * As the reCAPTCHA is no longer validated once we use the recaptcha_checked nonce, set the score in the state field.
	 *
	 * @since 6.2
	 *
	 * @return void
	 */
	private function maybe_set_captcha_score_in_form_state() {
		global $frm_vars;
		$form_id = $this->get_form_id();
		if ( ! empty( $frm_vars['captcha_scores'][ $form_id ] ) ) {
			FrmProFormState::set_initial_value( 'captcha', $frm_vars['captcha_scores'][ $form_id ] );
		}
	}

	/**
	 * Get the captcha score from the state and put it back into the $frm_vars['captcha_scores'] global.
	 *
	 * @since 6.2
	 *
	 * @return void
	 */
	private function maybe_pull_captcha_score_from_form_state() {
		$score = FrmProFormState::get_from_request( 'captcha', false );
		if ( ! is_numeric( $score ) ) {
			return;
		}

		global $frm_vars;
		if ( ! isset( $frm_vars['captcha_scores'] ) ) {
			$frm_vars['captcha_scores'] = array();
		}
		$frm_vars['captcha_scores'][ $this->get_form_id() ] = $score;
	}

	/**
	 * @since 6.2
	 *
	 * @return int
	 */
	private function get_form_id() {
		$form_id = is_object( $this->field ) ? $this->field->form_id : $this->field['form_id'];
		return (int) $form_id;
	}

	/**
	 * @since 4.07
	 * @return mixed
	 */
	private static function validate_checked() {
		if ( isset( $_POST['recaptcha_checked'] ) ) {
			$nonce = FrmAppHelper::get_param( 'recaptcha_checked', '', 'post', 'sanitize_text_field' );
			if ( wp_verify_nonce( $nonce, 'frm_captcha' ) ) {
				self::$checked = wp_create_nonce( 'frm_captcha' );
				return self::$checked;
			}
		}

		return false;
	}

	/**
	 * @since 4.07
	 * @return mixed
	 */
	public static function checked() {
		if ( isset( self::$checked ) ) {
			return self::$checked;
		}

		// pass along recaptcha_checked even if there is no captcha being validated
		// (which would happen if we're going to a previous page without a captcha)
		return self::validate_checked();
	}

	/**
	 * @since 4.07
	 * @return bool
	 */
	public static function posting_captcha_data() {
		return isset( $_POST['g-recaptcha-response'] ) || isset( $_POST['recaptcha_checked'] );
	}

	/**
	 * @since 4.07
	 */
	public static function render_checked_response() {
		global $frm_vars;
		$is_in_place_edit = ! empty( $frm_vars['inplace_edit'] );
		if ( $is_in_place_edit ) {
			self::$checked = wp_create_nonce( 'frm_captcha' );
		}
		if ( self::posting_captcha_data() || $is_in_place_edit ) {
			$checked = self::checked();
			if ( $checked ) {
				?>
				<input type="hidden" name="recaptcha_checked" value="<?php echo esc_attr( $checked ); ?>" />
				<?php
			}
		}
	}
}
