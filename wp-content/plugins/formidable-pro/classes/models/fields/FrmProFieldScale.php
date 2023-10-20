<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

/**
 * @since 3.0
 */
class FrmProFieldScale extends FrmFieldType {

	/**
	 * @var string
	 * @since 3.0
	 */
	protected $type = 'scale';

	/**
	 * @var bool
	 */
	protected $array_allowed = false;

	protected function input_html() {
		return $this->multiple_input_html();
	}

	protected function include_form_builder_file() {
		return FrmProAppHelper::plugin_path() . '/classes/views/frmpro-fields/10radio.php';
	}

	protected function field_settings_for_type() {
		$settings = array(
			'unique'        => true,
		);

		FrmProFieldsHelper::fill_default_field_display( $settings );
		return $settings;
	}

	protected function extra_field_opts() {
		$opts = array(
			'minnum' => 1,
			'maxnum' => 10,
			'step'   => 1,
		);

		$options = $this->get_field_column('options');
		if ( ! empty( $options ) ) {
			$range = $options;
			FrmProAppHelper::unserialize_or_decode( $range );

			$opts['minnum'] = reset( $range );
			$opts['maxnum'] = end( $range );
		}

		return $opts;
	}

	protected function new_field_settings() {
		return array(
			'options' => range( 1, 10 ),
		);
	}

	/**
	 * @since 4.0
	 * @param array $args - Includes 'field', 'display', and 'values'
	 */
	public function show_primary_options( $args ) {
		$field = $args['field'];
		include( FrmProAppHelper::plugin_path() . '/classes/views/frmpro-fields/back-end/scale-options.php' );

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
		return FrmProAppHelper::plugin_path() . '/classes/views/frmpro-fields/10radio.php';
	}

	/**
	 * @since 4.0.04
	 */
	public function sanitize_value( &$value ) {
		FrmAppHelper::sanitize_value( 'sanitize_text_field', $value );
	}

	/**
	 * Echo the option label.
	 *
	 * @since 6.3.2
	 *
	 * @param string $opt
	 *
	 * @return void
	 */
	public function echo_option_label( $opt ) {
		echo esc_html( $opt );
	}
}
