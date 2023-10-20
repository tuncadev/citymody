<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<p class="frm6 frm_form_field">
	<label id="for_field_options_admin_only_<?php echo absint( $field['id'] ); ?>" for="field_options_admin_only_<?php echo absint( $field['id'] ); ?>">
		<?php esc_html_e( 'Visibility', 'formidable-pro' ); ?>
		<span class="frm_help frm_icon_font frm_tooltip_icon frm_tooltip_expand" data-placement="right" title="<?php esc_attr_e( 'Determines who can see this field.', 'formidable-pro' ); ?>"></span>
	</label>

	<?php
	if ( $field['admin_only'] == 1 ) {
		$field['admin_only'] = 'administrator';
	} else if ( empty($field['admin_only']) ) {
		$field['admin_only'] = '';
	}
	?>

	<select name="field_options[admin_only_<?php echo absint( $field['id'] ); ?>][]" id="field_options_admin_only_<?php echo absint( $field['id'] ); ?>" multiple="multiple" class="frm_multiselect">
		<option value="" <?php FrmProAppHelper::selected( $field['admin_only'], '' ); ?>><?php esc_html_e( 'Everyone', 'formidable-pro' ); ?></option>
		<?php FrmAppHelper::roles_options($field['admin_only']); ?>
		<option value="loggedin" <?php FrmProAppHelper::selected( $field['admin_only'], 'loggedin' ); ?>>
			<?php esc_html_e( 'Logged-in Users', 'formidable-pro' ); ?>
		</option>
		<option value="loggedout" <?php FrmProAppHelper::selected( $field['admin_only'], 'loggedout' ); ?>>
			<?php esc_html_e( 'Logged-out Users', 'formidable-pro' ); ?>
		</option>
	</select>
</p>
