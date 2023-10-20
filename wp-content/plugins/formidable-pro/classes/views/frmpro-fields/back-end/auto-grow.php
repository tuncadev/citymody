<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<p class="frm4 frm_form_field frm_auto_grow_option">
	<input type="checkbox" id="frm_auto_grow_field_<?php echo esc_attr( $field['id'] ); ?>" name="field_options[auto_grow_<?php echo esc_attr( $field['id'] ); ?>]" value="1" <?php checked( $field['auto_grow'], 1 ); ?> />
	<label id="for_frm_auto_grow_field_<?php echo esc_attr( $field['id'] ); ?>" for="frm_auto_grow_field_<?php echo esc_attr( $field['id'] ); ?>">
		<?php esc_html_e( 'Auto Grow', 'formidable-pro' ); ?>
		<span class="frm_help frm_icon_font frm_tooltip_icon frm_tooltip_expand" data-placement="left" title="<?php esc_attr_e( 'Auto Grow: Automatically expand the height of the field when the text reaches the maximum rows', 'formidable-pro' ); ?>"></span>
	</label>
</p>
