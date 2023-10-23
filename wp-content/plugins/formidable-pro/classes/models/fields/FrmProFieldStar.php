<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

/**
 * @since 3.0
 */
class FrmProFieldStar extends FrmFieldType {

	/**
	 * @var string
	 * @since 3.0
	 */
	protected $type = 'star';

	/**
	 * @var bool
	 */
	protected $array_allowed = false;

	protected function input_html() {
		return $this->multiple_input_html();
	}

	protected function include_form_builder_file() {
		return FrmProAppHelper::plugin_path() . '/classes/views/frmpro-fields/front-end/star.php';
	}

	protected function field_settings_for_type() {
		$settings = array(
			'unique'        => true,
		);

		FrmProFieldsHelper::fill_default_field_display( $settings );
		return $settings;
	}

	protected function extra_field_opts() {
		return array(
			'minnum' => 1,
			'maxnum' => 5,
		);
	}

	protected function new_field_settings() {
		return array(
			'options' => range( 1, 5 ),
		);
	}

	/**
	 * @since 4.0
	 * @param array $args - Includes 'field', 'display', and 'values'
	 */
	public function show_primary_options( $args ) {
		$field = $args['field'];
		include( FrmProAppHelper::plugin_path() . '/classes/views/frmpro-fields/back-end/star-options.php' );

		parent::show_primary_options( $args );
	}

	public function get_container_class() {
		// Add class to inline Scale field
		$class = '';
		if ( $this->field['label'] == 'inline' ) {
			$class = ' frm_scale_container';
		}
		return $class;
	}

	protected function include_front_form_file() {
		return FrmProAppHelper::plugin_path() . '/classes/views/frmpro-fields/front-end/star.php';
	}

	protected function prepare_display_value( $value, $atts ) {
		if ( ! isset( $atts['html'] ) || ! $atts['html'] ) {
			return $value;
		}

		FrmStylesController::enqueue_style();

		$max     = $this->get_max_star_rating();
		$numbers = $this->get_rounded_decimal( $value );

		ob_start();

		if ( $this->should_use_font_icons() ) {
			include FrmProAppHelper::plugin_path() . '/classes/views/frmpro-fields/star_disabled_font_icons.php';
		} else {
			include FrmProAppHelper::plugin_path() . '/classes/views/frmpro-fields/star_disabled.php';
		}

		$contents = ob_get_contents();
		ob_end_clean();

		return $contents;
	}

	/**
	 * FrmProEntriesController::filter_display_value sanitizes the HTML through wp_kses_post.
	 * So on some admin pages (currently just the entries list page) we'll use font icons instead.
	 *
	 * @since 6.4.2
	 *
	 * @return bool
	 */
	private function should_use_font_icons() {
		return FrmAppHelper::is_admin_page( 'formidable-entries' ) && in_array( FrmAppHelper::simple_get( 'frm_action' ), array( 'list', '' ), true );
	}

	/**
	 * Try to determine the maximum star rating value.
	 *
	 * @return int
	 */
	private function get_max_star_rating() {
		$options       = $this->get_field_column( 'options' );
		$field_options = $this->get_field_column( 'field_options' );
		$max_setting   = ! empty( $field_options['maxnum'] ) ? $field_options['maxnum'] : false;

		if ( is_array( $options ) ) {
			$max                 = max( $options );
			$options_are_default = 5 === $max && 5 === count( $options );
			if ( false !== $max_setting && $options_are_default ) {
				$max = $max_setting;
			}
		}

		if ( ! isset( $max ) || ! is_numeric( $max ) ) {
			$max = false !== $max_setting ? $max_setting : 5;
		}

		return $max;
	}

	/**
	 * @param mixed $value
	 * @return array
	 */
	private function get_rounded_decimal( $value ) {
		if ( is_array( $value ) ) {
			$value = 0;
		}

		$numbers = array(
			'decimal' => 0,
			'digit'   => $value,
			'value'   => $value,
		);

		if ( $value != floor( $value ) ) {
			$value = round( $value, 2 );
			list( $numbers['digit'], $numbers['decimal'] ) = explode( '.', $value );

			if ( strlen( $numbers['decimal'] ) === 1 ) {
				// make sure there are two digits after the decimal
				$numbers['decimal'] = $numbers['decimal'] * 10;
			}

			if ( $numbers['decimal'] < 25 ) {
				$numbers['decimal'] = 0;
			} elseif ( $numbers['decimal'] < 75 ) {
				$numbers['decimal'] = 5;
			} else {
				$numbers['decimal'] = 0;
				$numbers['digit'] ++;
			}

			$numbers['value'] = (float) ( $numbers['digit'] . '.' . $numbers['decimal'] );
		}

		return $numbers;
	}

	/**
	 * @since 4.0.04
	 */
	public function sanitize_value( &$value ) {
		FrmAppHelper::sanitize_value( 'sanitize_text_field', $value );
	}
}
