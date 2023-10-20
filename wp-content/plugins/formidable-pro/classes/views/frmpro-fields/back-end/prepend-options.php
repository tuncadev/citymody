<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<p class="frm_form_field frm6">
	<label for="prepend_<?php echo absint( $field['id'] ); ?>">
		<?php esc_html_e( 'Before Input', 'formidable-pro' ); ?>
		<span class="frm_help frm_icon_font frm_tooltip_icon frm_tooltip_expand" data-placement="right" title="<?php esc_attr_e( 'A value entered here will show directly before the input box in the form.', 'formidable-pro' ); ?>"></span>
	</label>

	<input type="text" name="field_options[prepend_<?php echo absint( $field['id'] ); ?>]" id="prepend_<?php echo absint( $field['id'] ); ?>" value="<?php echo esc_attr( $field['prepend'] ); ?>" aria-invalid="false" />
</p>

<p class="frm_form_field frm6">
	<label for="append_<?php echo absint( $field['id'] ); ?>">
		<?php esc_html_e( 'After Input', 'formidable-pro' ); ?>
	</label>

	<input type="text" name="field_options[append_<?php echo absint( $field['id'] ); ?>]" id="append_<?php echo absint( $field['id'] ); ?>" value="<?php echo esc_attr( $field['append'] ); ?>" />
</p>
