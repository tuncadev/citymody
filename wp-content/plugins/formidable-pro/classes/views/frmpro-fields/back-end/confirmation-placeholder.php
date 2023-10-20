<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<p class="frm-conf-box-<?php echo esc_attr( $field['id'] . ( empty( $field['conf_field'] ) ? ' frm_hidden' : '' ) ); ?>">
	<label for="field_options_conf_input_<?php echo esc_attr( $field['id'] ); ?>">
		<?php esc_html_e( 'Confirmation Placeholder', 'formidable-pro' ); ?>
	</label>
	<input type="text" name="field_options[conf_input_<?php echo absint( $field['id'] ); ?>]" id="field_options_conf_input_<?php echo esc_attr( $field['id'] ); ?>" value="<?php echo esc_attr( $field['conf_input'] ); ?>" />
</p>
<p class="frm-conf-box-<?php echo esc_attr( $field['id'] . ( empty( $field['conf_field'] ) ? ' frm_hidden' : '' ) ); ?>">
	<label for="field_options_conf_desc_<?php echo esc_attr( $field['id'] ); ?>">
		<?php esc_html_e( 'Confirmation Description', 'formidable-pro' ); ?>
	</label>
	<textarea name="field_options[conf_desc_<?php echo absint( $field['id'] ); ?>]" id="field_options_conf_desc_<?php echo esc_attr( $field['id'] ); ?>" class="frm_long_input"><?php
		echo FrmAppHelper::esc_textarea( $field['conf_desc'] ); // WPCS: XSS ok.
		?></textarea>
</p>
