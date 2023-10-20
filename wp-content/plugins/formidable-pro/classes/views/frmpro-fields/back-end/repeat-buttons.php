<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<p>
	<label><?php esc_html_e( 'Repeat Links', 'formidable-pro' ); ?></label>
	<select class="frm_repeat_format" name="field_options[format_<?php echo absint( $field['id'] ); ?>]">
		<option value=""><?php esc_html_e( 'Icons', 'formidable-pro' ); ?></option>
		<option value="text" <?php selected( $field['format'], 'text' ); ?>>
			<?php esc_html_e( 'Text links', 'formidable-pro' ); ?>
		</option>
		<option value="both" <?php selected( $field['format'], 'both' ); ?>>
			<?php esc_html_e( 'Text links with icons', 'formidable-pro' ); ?>
		</option>
	</select>
</p>

<p class="frm6 frm_form_field frm_repeat_text <?php echo ( $field['format'] == '' ) ? 'hide-if-js' : ''; ?>">
	<label><?php esc_html_e( 'Add New Label', 'formidable-pro' ); ?></label>
	<input type="text" name="field_options[add_label_<?php echo absint( $field['id'] ); ?>]" value="<?php echo esc_attr( $field['add_label'] ); ?>" data-changeme="<?php echo esc_attr( $field['field_key'] ); ?>_add_label" />
</p>

<p class="frm6 frm_form_field frm_repeat_text <?php echo ( $field['format'] == '' ) ? 'hide-if-js' : ''; ?>">
	<label><?php esc_html_e( 'Remove Label', 'formidable-pro' ); ?></label>
	<input type="text" name="field_options[remove_label_<?php echo absint( $field['id'] ); ?>]" value="<?php echo esc_attr( $field['remove_label'] ); ?>" data-changeme="<?php echo esc_attr( $field['field_key'] ); ?>_remove_label" />
</p>
