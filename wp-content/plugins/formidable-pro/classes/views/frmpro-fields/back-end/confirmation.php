<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<p class="frm6 frm_form_field">
	<label for="frm_conf_field_<?php echo absint( $field['id'] ); ?>">
		<?php esc_html_e( 'Require Confirmation', 'formidable-pro' ); ?>
	</label>
	<select name="field_options[conf_field_<?php echo absint( $field['id'] ); ?>]" class="conf_field" id="frm_conf_field_<?php echo absint( $field['id'] ); ?>">
		<option value="" <?php selected( $field['conf_field'], '' ); ?>>
			<?php esc_html_e( 'No Confirmation', 'formidable-pro' ); ?>
		</option>
		<option value="inline" <?php selected( $field['conf_field'], 'inline' ); ?>>
			<?php esc_html_e( 'Inline', 'formidable-pro' ); ?>
		</option>
		<option value="below" <?php selected( $field['conf_field'], 'below' ); ?>>
			<?php esc_html_e( 'Below Field', 'formidable-pro' ); ?>
		</option>
	</select>
</p>
