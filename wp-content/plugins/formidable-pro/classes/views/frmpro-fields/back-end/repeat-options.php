<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<p class="frm6 frm_form_field">
	<label>
		<?php esc_html_e( 'Repeat Limit', 'formidable-pro' ); ?>
		<span class="frm_help frm_icon_font frm_tooltip_icon" data-placement="right" title="<?php esc_attr_e( 'The maximum number of times the end user is allowed to duplicate this section of fields in one entry', 'formidable-pro' ); ?>"></span>
	</label>
	<input type="number" class="frm_repeat_limit" name="field_options[repeat_limit_<?php echo absint( $field['id'] ); ?>]" value="<?php echo esc_attr( $field['repeat_limit'] ); ?>" size="3" min="2" step="1" max="999" />
</p>
