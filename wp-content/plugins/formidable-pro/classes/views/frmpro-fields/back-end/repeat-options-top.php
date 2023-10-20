<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<p>
	<label for="field_options_format_<?php echo absint( $field['id'] ); ?>">
		<?php esc_html_e( 'Repeat Layout', 'formidable-pro' ); ?>
	</label>
	<select name="field_options[format_<?php echo absint( $field['id'] ); ?>]" id="field_options_format_<?php echo absint( $field['id'] ); ?>">
		<option value=""><?php esc_html_e( 'Default: No automatic formatting', 'formidable-pro' ); ?></option>
		<option value="inline" <?php selected( $field['format'], 'inline' ); ?>>
			<?php esc_html_e( 'Inline: Display each field and label in one row', 'formidable-pro' ); ?>
		</option>
		<option value="grid" <?php selected( $field['format'], 'grid' ); ?>>
			<?php esc_html_e( 'Grid: Display labels as headings above rows of fields', 'formidable-pro' ); ?>
		</option>
	</select>
	<input type="hidden" name="field_options[form_select_<?php echo absint( $field['id'] ); ?>]" value="<?php echo esc_attr( $field['form_select'] ); ?>" />
</p>
