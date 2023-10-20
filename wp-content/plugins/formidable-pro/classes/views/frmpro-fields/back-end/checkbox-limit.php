<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<p class="frm6 frm_form_field frm_first">
	<label>
		<?php esc_html_e( 'Limit Selections', 'formidable-pro' ); ?>
		<span class="frm_help frm_icon_font frm_tooltip_icon" data-placement="right" title="<?php esc_attr_e( 'The maximum number of options in this field that the end user is allowed to select', 'formidable-pro' ); ?>"></span>
	</label>
	<input type="number" class="frm_js_checkbox_limit" name="field_options[limit_selections_<?php echo absint( $field['id'] ); ?>]" value="<?php
		if ( isset( $field['limit_selections'] ) ) {
			echo esc_attr( $field['limit_selections'] );
		}
		?>" size="3" min="2" step="1" max="999" />
</p>
