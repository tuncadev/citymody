<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<p class="frm8 frm_first frm_form_field frm-number-range">
	<label><?php esc_html_e( 'Time Range', 'formidable-pro' ); ?></label>

	<span class="frm_grid_container">
		<span class="frm5 frm_form_field frm-range-min">
			<input type="text" name="field_options[start_time_<?php echo absint( $field['id'] ); ?>]" id="start_time_<?php echo absint( $field['id'] ); ?>" value="<?php echo esc_attr( $field['start_time'] ); ?>" size="5"/>
		</span>
		<span class="frm5 frm_last frm_form_field">
			<input type="text" name="field_options[end_time_<?php echo absint( $field['id'] ); ?>]" id="end_time_<?php echo absint( $field['id'] ); ?>" value="<?php echo esc_attr( $field['end_time'] ); ?>" size="5"/>
		</span>
	</span>
</p>

<p class="frm3 frm_last frm_form_field">
	<label for="frm_step_<?php echo esc_attr( $field['field_key'] ); ?>">
		<?php esc_html_e( 'Step (min)', 'formidable' ); ?>
	</label>
	<input type="text" name="field_options[step_<?php echo absint( $field['id'] ); ?>]" value="<?php echo esc_attr( $field['step'] ); ?>" id="frm_step_<?php echo esc_attr( $field['field_key'] ); ?>" min="1" max="1440" />
</p>

<p class="frm4 frm_form_field">
	<label><?php esc_html_e( 'Time Format', 'formidable-pro' ); ?></label>

	<select name="field_options[clock_<?php echo absint( $field['id'] ); ?>]">
		<option value="12" <?php selected( $field['clock'], 12 ); ?>>
			<?php esc_html_e( '12 hour clock', 'formidable-pro' ); ?>
		</option>
		<option value="24" <?php selected( $field['clock'], 24 ); ?>>
			<?php esc_html_e( '24 hour clock', 'formidable-pro' ); ?>
		</option>
	</select>
</p>

<p class="frm8 frm_form_field">
	<label for="single_time_<?php echo esc_attr( $field['id'] ); ?>">
		<input type="checkbox" name="field_options[single_time_<?php echo esc_attr( $field['id'] ); ?>]" id="single_time_<?php echo esc_attr( $field['id'] ); ?>" value="1" <?php echo FrmField::is_option_true( $field, 'single_time' ) ? 'checked="checked"' : ''; ?> />
		<?php esc_html_e( 'show a single time dropdown', 'formidable-pro' ); ?>
	</label>
</p>
