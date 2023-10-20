<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>

<p class="frm6 frm_form_field">
	<label><?php esc_html_e( 'Display Type', 'formidable-pro' ); ?></label>
	<select name="field_options[data_type_<?php echo absint( $field['id'] ); ?>]">
		<?php
		foreach ( $field_types as $type_key => $type_name ) {
			$selected = ( isset( $field['data_type'] ) && $field['data_type'] == $type_key ) ? ' selected="selected"' : '';
			?>
			<option value="<?php echo esc_attr( $type_key ); ?>"<?php echo $selected; ?>>
				<?php echo esc_html( $type_name ); ?>
			</option>
		<?php } ?>
	</select>
</p>
