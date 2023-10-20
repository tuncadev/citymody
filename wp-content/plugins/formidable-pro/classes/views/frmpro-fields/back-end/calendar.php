<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<p class="frm8 frm_first frm_form_field">
	<label>
		<?php esc_html_e( 'Year Range', 'formidable-pro' ); ?>
		<span class="frm_help frm_icon_font frm_tooltip_icon" data-placement="right" title="<?php esc_attr_e( 'Use four digit years or +/- years to make it dynamic. For example, use -5 for the start year and +5 for the end year.', 'formidable-pro' ); ?>"></span>
	</label>

	<span class="frm_grid_container">
		<span class="frm5 frm_form_field frm-range-min">
			<input type="text" name="field_options[start_year_<?php echo absint( $field['id'] ); ?>]" value="<?php echo esc_attr( isset( $field['start_year'] ) ? $field['start_year'] : '' ); ?>" placeholder="<?php esc_attr_e( 'Start', 'formidable-pro' ); ?>" size="4"/>

		</span>
		<span class="frm5 frm_last frm_form_field">
			<input type="text" name="field_options[end_year_<?php echo absint( $field['id'] ); ?>]" value="<?php echo esc_attr( isset( $field['end_year'] ) ? $field['end_year'] : '' ); ?>" placeholder="<?php esc_attr_e( 'End', 'formidable-pro' ); ?>" size="4"/>
		</span>
	</span>
</p>
