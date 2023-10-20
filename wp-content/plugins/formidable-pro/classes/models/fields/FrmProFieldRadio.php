<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

/**
 * @since 3.0
 */
class FrmProFieldRadio extends FrmFieldRadio {

	protected function field_settings_for_type() {
		$settings = parent::field_settings_for_type();

		$settings['read_only'] = true;
		$settings['default_value'] = true;

		FrmProFieldsHelper::fill_default_field_display( $settings );
		return $settings;
	}

	protected function extra_field_opts() {
		return array_merge(
			parent::extra_field_opts(),
			array(
				'limit_selections' => '',
				'image_options'   => 0,
				'hide_image_text' => 0,
				'image_size'      => '',
			)
		);
	}

	/**
	 * @since 4.0
	 *
	 * @param array $args - Includes 'field', 'display', and 'values'
	 */
	public function show_extra_field_choices( $args ) {
		$field      = $args['field'];
		$hide_other = $field['other'] == true;
		if ( isset( $field['post_field'] ) && $field['post_field'] === 'post_category' ) {
			return;
		}

		include( FrmProAppHelper::plugin_path() . '/classes/views/frmpro-fields/back-end/other-option.php' );
	}

	/**
	 * @since 4.06
	 *
	 * @param array $args - Includes 'field', 'display', and 'values'
	 */
	public function show_priority_field_choices( $args = array() ) {
		FrmProImages::show_image_choices( $args );
	}

	/**
	 * @since 4.06
	 */
	protected function include_front_form_file() {
		$has_images  = FrmField::get_option( $this->field, 'image_options' );
		$is_post_cat = FrmField::get_option( $this->field, 'post_field' ) === 'post_category';

		if ( $has_images && ! $is_post_cat ) {
			return FrmProAppHelper::plugin_path() . '/classes/views/frmpro-fields/front-end/image-options.php';
		} else {
			return parent::include_front_form_file();
		}
	}

	/**
	 * Format image options.
	 *
	 * @since 4.06
	 */
	protected function prepare_display_value( $value, $atts ) {
		$value = parent::prepare_display_value( $value, $atts );
		if ( FrmProImages::has_image_option_markup( $value ) ) {
			$value = '<div class="frm_has_image_options">' . $value . ' </div>';
		}
		return $value;
	}
}
