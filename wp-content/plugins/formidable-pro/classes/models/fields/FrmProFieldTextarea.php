<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

/**
 * @since 3.0
 */
class FrmProFieldTextarea extends FrmFieldTextarea {

	protected function field_settings_for_type() {
		$settings = parent::field_settings_for_type();

		$settings['autopopulate'] = true;
		$settings['calc']         = true;
		$settings['read_only']    = true;
		$settings['unique']       = true;
		$settings['invalid']      = true;

		FrmProFieldsHelper::fill_default_field_display( $settings );
		return $settings;
	}

	/**
	 * @param array $args
	 * @param array $shortcode_atts
	 *
	 * @return string
	 */
	public function front_field_input( $args, $shortcode_atts ) {
		$input_html = parent::front_field_input( $args, $shortcode_atts );

		if ( ! empty( $this->field['auto_grow'] ) ) {
			$input_html = preg_replace( '/<textarea/', '<textarea data-auto-grow="' . esc_attr( $this->field['auto_grow'] ) . '" ', $input_html, 1 );
		}

		$value = $this->get_value();

		$max_limit = intval( FrmField::get_option( $this->field, 'max_limit' ) );
		if ( $max_limit ) {
			$max_limit_type = FrmField::get_option( $this->field, 'max_limit_type' );
			$content_length = self::get_content_length( $value );
			$class          = 'frm_pro_max_limit_desc';
			if ( $content_length > $max_limit ) {
				$class .= ' frm_error';
			}

			$input_html .= '<div class="' . esc_attr( $class ) . '" data-max="' . intval( $max_limit ) . '" data-max-type="' . esc_attr( $max_limit_type ) . '">';
			$input_html .= sprintf(
				// Translators: %1$s: the current content length, %2$s: the max limitation of content, %3$s: type of max limitation.
				esc_html( _x( '%1$s of %2$s max %3$s', 'content limitation description', 'formidable-pro' ) ),
				'<span id="frm_pro_content_length_' . intval( $this->field_id ) . '">' . intval( $content_length ) . '</span>',
				intval( $max_limit ),
				esc_html( 'word' === $max_limit_type ? __( 'words', 'formidable-pro' ) : __( 'characters', 'formidable-pro' ) )
			);
			$input_html .= '</div>';
		}

		return $input_html;
	}

	/**
	 * @param array $args
	 *
	 * @return array
	 */
	public function validate( $args ) {
		$max_limit = intval( FrmField::get_option( $this->field, 'max_limit' ) );
		if ( ! $max_limit ) {
			return array();
		}

		$length = $this->get_content_length( $args['value'] );
		if ( $length <= $max_limit ) {
			return array();
		}

		$message = '';
		if ( ! FrmField::is_option_empty( $this->field, 'invalid' ) ) {
			$message = FrmFieldsHelper::get_error_msg( $this->field, 'invalid' );
		}

		return array(
			'field' . $args['id'] => $message,
		);
	}

	/**
	 * Gets field value.
	 *
	 * @return string
	 */
	private function get_value() {
		if ( ! empty( $this->field['value'] ) ) {
			return $this->field['value'];
		}

		if ( ! empty( $this->field['default_value'] ) ) {
			return $this->field['default_value'];
		}

		return '';
	}

	/**
	 * Gets content length based on the content limitation options.
	 *
	 * @param string $content The content.
	 * @return int
	 */
	private function get_content_length( $content ) {
		if ( ! $content ) {
			return 0;
		}

		$max_type = FrmField::get_option( $this->field, 'max_limit_type' );

		if ( 'word' === $max_type ) {
			$words = preg_split( '/\s+/', $content );
			$words = array_filter( $words );
			return count( $words );
		}

		if ( function_exists( 'mb_strlen' ) ) {
			return mb_strlen( $content );
		}
		return strlen( $content );
	}
}
