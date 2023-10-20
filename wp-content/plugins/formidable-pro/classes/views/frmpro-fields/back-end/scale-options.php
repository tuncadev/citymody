<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<p class="frm8 frm_first frm_form_field frm-number-range">
	<label><?php esc_html_e( 'Range', 'formidable-pro' ); ?></label>

	<span class="frm_grid_container">
		<span class="frm5 frm_form_field frm-range-min">
			<input type="number"
				name="field_options[minnum_<?php echo absint( $field['id'] ); ?>]"
				value="<?php echo esc_attr( $field['minnum'] ); ?>"
				class="scale_minnum frm_scale_opt" id="scale_minnum_<?php echo absint( $field['id'] ); ?>"
				/>
		</span>
		<span class="frm5 frm_last frm_form_field">
			<input type="number"
				name="field_options[maxnum_<?php echo absint( $field['id'] ); ?>]"
				value="<?php echo esc_attr( $field['maxnum'] ); ?>"
				class="scale_maxnum frm_scale_opt" id="scale_maxnum_<?php echo absint( $field['id'] ); ?>"
				/>
		</span>
	</span>
</p>
<p class="frm3 frm_last frm_form_field frm-step">
	<label for="frm_step_<?php echo esc_attr( $field['id'] ); ?>">
		<?php esc_html_e( 'Step', 'formidable' ); ?>
	</label>
	<input type="number" name="field_options[step_<?php echo absint( $field['id'] ); ?>]" value="<?php echo esc_attr( $field['step'] ); ?>" id="frm_step_<?php echo esc_attr( $field['id'] ); ?>" class="frm_scale_opt" />
</p>
