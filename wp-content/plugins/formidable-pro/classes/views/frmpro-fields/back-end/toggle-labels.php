<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<p>
	<label>
		<input type="checkbox" name="field_options[show_label_<?php echo absint( $field['id'] ); ?>]" value="1" <?php checked( $field['show_label'], 1 ); ?> id="field_options_show_label_<?php echo absint( $field['id'] ); ?>" />
		<?php esc_html_e( 'Show Labels', 'formidable-pro' ); ?>
	</label>
</p>
<p>
	<label for="field_options_toggle_on_<?php echo absint( $field['id'] ); ?>">
		<?php esc_html_e( 'Active Label', 'formidable-pro' ); ?>
	</label>
	<input type="text" name="field_options[toggle_on_<?php echo absint( $field['id'] ); ?>]" value="<?php echo esc_attr( $field['toggle_on'] ); ?>" id="field_options_toggle_on_<?php echo absint( $field['id'] ); ?>" />
</p>

<p>
	<label for="field_options_toggle_off_<?php echo absint( $field['id'] ); ?>">
		<?php esc_html_e( 'Inactive Label', 'formidable-pro' ); ?>
	</label>
	<input type="text" name="field_options[toggle_off_<?php echo absint( $field['id'] ); ?>]" value="<?php echo esc_attr( $field['toggle_off'] ); ?>" id="field_options_toggle_off_<?php echo absint( $field['id'] ); ?>" />
</p>
