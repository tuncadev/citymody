<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

/**
 * @since 4.03
 */
class FrmProFieldSummary extends FrmFieldType {

	protected $type = 'summary';

	protected $has_for_label = false;

	public function default_html() {
		$default_html = <<<DEFAULT_HTML
<div id="frm_field_[id]_container" class="frm_form_field form-field">
	<h3 class="frm_pos_[label_position]">[field_name]</h3>
	[if description]<div class="frm_description" id="frm_desc_field_[key]">[description]</div>[/if description]
    [input]
</div>
DEFAULT_HTML;
		return $default_html;
	}

	protected function field_settings_for_type() {
		$settings = array(
			'required'       => false,
			'visibility'     => false,
			'label_position' => true,
			'options'        => true,
			'default'        => false,
		);
		FrmProFieldsHelper::fill_default_field_display( $settings );
		return $settings;
	}

	/**
	 * @param array $args - Includes 'field', 'display', and 'values'
	 */
	public function show_extra_field_choices( $args ) {
		$field = $args['field'];
		include( FrmProAppHelper::plugin_path() . '/classes/views/frmpro-fields/back-end/summary-options.php' );

		parent::show_extra_field_choices( $args );
	}

	protected function extra_field_opts() {
		return array_merge(
			parent::extra_field_opts(),
			array(
				'exclude_fields' => '',
				'include_extras' => array(),
				'label'          => 'none',
			)
		);
	}

	protected function include_form_builder_file() {
		return FrmProAppHelper::plugin_path() . '/classes/views/frmpro-fields/back-end/field-summary.php';
	}

	public function displayed_field_type( $field ) {
		return array(
			$this->type => true,
		);
	}

	protected function get_excluded_ids() {
		$ids = trim( FrmField::get_option( $this->field, 'exclude_fields' ) );
		if ( ! empty( $ids ) ) {
			$ids = explode( ',', $ids );
			// trim to avoid mismatch - due to empty space - when doing in_array.
			// array_filter to remove empty spaces caused by e.g. trailing comma.
			$ids = array_filter( array_map( 'trim', $ids ) );
			return $ids;
		} else {
			return array();
		}
	}

	public function front_field_input( $args, $shortcode_atts ) {
		if ( ! isset( $_POST ) || empty( $_POST ) ) {
			// e.g. when the AMP add-on (which removes page breaks) is active, so no POSTed values.
			return;
		}

		$atts['excluded_ids']   = $this->get_excluded_ids();
		$atts['excluded_types'] = array_merge(
			self::excluded_field_types(),
			$this->get_auto_excluded_types()
		);

		$formatter = new FrmProSummaryFormatter( FrmAppHelper::get_post_param( 'form_id', '', 'absint' ), $atts );
		return $formatter->get_formatted_entry_values();
	}

	protected function get_auto_excluded_types() {
		return array_diff(
			array_keys( self::include_extra_field_types() ),
			(array) FrmField::get_option( $this->field, 'include_extras' )
		);
	}

	/*
	 * Fields that are always excluded from display in the summary. These include field types & field options.
	 */
	public static function excluded_field_types() {
		/*
		 * Square brackets mean field options. Fields without a value are also included, though difficult to
		 * represent in the array here, the check is always done during the display of summary.
		 */
		return array(
			'gateway',
			'captcha',
			'[hide_field][]',
		);
	}

	/*
	 * Fields that are hidden from display in the summary by default but are allowed to be displayed.
	 * These include field types & field options.
	 */
	public static function include_extra_field_types() {
		/*
		 * Square brackets mean field options. Fields without a value are also included, though difficult to
		 * represent in the array here, the check is always done during the display of summary.
		 *
		 * admin_only is what is used to set visibility based on roles.
		 */
		return array(
			'html'         => 'HTML',
			'hidden'       => 'Hidden',
			'user_id'      => 'User ID',
			'password'     => 'Password',
			'[admin_only]' => 'Fields hidden with Visibility',
		);
	}

	/**
	 * @param array $args - includes 'field'
	 */
	public static function exclude_fields_settings( $args ) {
		$field = $args['field'];
		include( FrmProAppHelper::plugin_path() . '/classes/views/frmpro-fields/back-end/summary-exclude-fields-settings.php' );
	}

	/**
	 * Get a list of field types that cannot be picked in the 'Exclude Fields' settings.
	 *
	 * @return array
	 */
	public static function remove_from_exclude_field_settings() {
		return array_merge(
			array( 'end_divider', 'break', 'summary' ),
			self::excluded_field_types(),
			array_keys( self::include_extra_field_types() )
		);
	}

	public static function maybeAddBreakFieldBeforeSummary( $field_type, $form_id ) {
		if ( 'summary' !== $field_type ) {
			return;
		}

		$has_break = FrmAppHelper::get_post_param( 'has_break', '', 'sanitize_text_field' );
		if ( ! $has_break ) {
			FrmFieldsController::include_new_field( 'break', $form_id );
		}
	}
}
