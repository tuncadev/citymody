<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<p id="frm_multiple_cont_<?php echo absint( $field['id'] ); ?>" class="frm6 frm_form_field frm_multiple_cont_<?php echo absint( $field['id'] ); ?> <?php echo ( $field['type'] == 'data' && ( ! isset( $field['data_type'] ) || $field['data_type'] != 'select' ) ) ? 'frm_hidden' : ''; ?>">
	<label for="multiple_<?php echo absint( $field['id'] ); ?>">
		<input type="checkbox" name="field_options[multiple_<?php echo absint( $field['id'] ); ?>]" id="multiple_<?php echo absint( $field['id'] ); ?>" value="1" class="frm_multiselect_opt" <?php checked( $field['multiple'], 1 ); ?> />
		<?php esc_html_e( 'Multiselect', 'formidable-pro' ); ?>
	</label>
</p>
<p class="frm6 frm_form_field frm_multiple_cont_<?php echo absint( $field['id'] ); ?> <?php echo esc_attr( FrmField::is_field_type( $field, 'select' ) ? '' : 'frm_hidden' ); ?>">
	<label for="autocom_<?php echo absint( $field['id'] ); ?>">
		<input type="checkbox" name="field_options[autocom_<?php echo absint( $field['id'] ); ?>]" id="autocom_<?php echo absint( $field['id'] ); ?>" value="1" <?php checked( $field['autocom'], 1 ); ?> />
		<?php esc_html_e( 'Autocomplete', 'formidable-pro' ); ?>
	</label>
</p>
