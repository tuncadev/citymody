<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<p class="frm6 frm_form_field">
	<label for="radio_maxnum_<?php echo absint( $field['id'] ); ?>">
		<?php esc_html_e( 'Maximum Rating', 'formidable-pro' ); ?>
	</label>

	<input type="number" name="field_options[maxnum_<?php echo absint( $field['id'] ); ?>]" class="radio_maxnum" id="radio_maxnum_<?php echo absint( $field['id'] ); ?>" value="<?php echo esc_attr( $field['maxnum'] ); ?>" min="1" max="50" step="1" />
	<input type="hidden" name="field_options[minnum_<?php echo absint( $field['id'] ); ?>]" value="1" />
</p>
