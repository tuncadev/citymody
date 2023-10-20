<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<p class="frm-has-modal default-value-section-<?php echo esc_attr( $field['id'] . ( isset( $default_value_types['calc']['current'] ) ? '' : ' frm_hidden' ) ); ?>" id="calc-for-<?php echo esc_attr( $field['id'] ); ?>">
	<label for="frm_default_value_<?php echo esc_attr( $field['id'] ); ?>">
		<?php esc_html_e( 'Default Value', 'formidable-pro' ); ?>
		<span class="frm-sub-label">
			(<?php esc_html_e( 'Calculation', 'formidable-pro' ); ?>)
		</span>
	</label>
	<span class="frm-with-right-icon">
		<?php
		$calc = isset( $field['field_options']['calc'] ) ? $field['field_options']['calc'] : $field['calc'];
		FrmProAppHelper::icon_by_class(
			'frm_icon_font frm_more_horiz_solid_icon frm-show-inline-modal frm-open-calc',
			array(
				'data-open' => 'frm-calc-box-' . $field['id'],
			)
	 	);
		?>
		<input type="text" value="<?php echo esc_attr( $calc ); ?>" id="frm_calc_<?php echo esc_attr( $field['id'] ); ?>" name="field_options[calc_<?php echo absint( $field['id'] ); ?>]" class="frm-calc-field" />
	</span>
</p>

<?php
FrmFieldsHelper::inline_modal(
	array(
		'title'    => __( 'Calculate Default Value', 'formidable-pro' ),
		'callback' => array( 'FrmProFieldsController', 'calculation_settings' ),
		'args'     => compact( 'field' ),
		'id'       => 'frm-calc-box-' . $field['id'],
	)
);
