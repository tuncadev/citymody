<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<p class="frm_grid_container">
	<label for="frm_edit_msg" class="frm4 frm_form_field" >
		<?php esc_html_e( 'Edit Message', 'formidable-pro' ); ?>
		<span class="frm_help frm_icon_font frm_tooltip_icon" title="<?php esc_attr_e( 'The default message seen when after an entry is updated.', 'formidable-pro' ); ?>"></span>
	</label>
    <input type="text" id="frm_edit_msg" name="frm_edit_msg" class="frm8 frm_form_field" value="<?php echo esc_attr( $frmpro_settings->edit_msg ); ?>" />
</p>

<p class="frm_grid_container">
	<label for="frm_update_value" class="frm4 frm_form_field">
		<?php esc_html_e( 'Update Button', 'formidable-pro' ); ?>
		<span class="frm_help frm_icon_font frm_tooltip_icon" title="<?php esc_attr_e( 'The label on the submit button when editing and entry.', 'formidable-pro' ); ?>"></span>
	</label>
    <input type="text" id="frm_update_value" name="frm_update_value" class="frm8 frm_form_field" value="<?php echo esc_attr($frmpro_settings->update_value); ?>" />
</p>

<p class="frm_grid_container">
	<label for="frm_login_msg" class="frm4 frm_form_field">
		<?php esc_html_e( 'Login Message', 'formidable-pro' ); ?>
		<span class="frm_help frm_icon_font frm_tooltip_icon" title="<?php esc_attr_e( 'The message seen when a user who is not logged-in views a form only logged-in users can submit.', 'formidable-pro' ); ?>"   ></span>
	</label>
    <input type="text" id="frm_login_msg" name="frm_login_msg" class="frm8 frm_form_field" value="<?php echo esc_attr($frm_settings->login_msg); ?>" />
</p>

<p class="frm_grid_container">
	<label for="frm_already_submitted" class="frm4 frm_form_field" >
		<?php esc_html_e( 'Previously Submitted Message', 'formidable-pro' ); ?>
		<span class="frm_help frm_icon_font frm_tooltip_icon" title="<?php esc_attr_e( 'The message seen when a user attempts to submit a form for a second time if submissions are limited.', 'formidable-pro' ); ?>"></span>
	</label>
	<input type="text" id="frm_already_submitted" name="frm_already_submitted" class="frm8 frm_form_field" value="<?php echo esc_attr($frmpro_settings->already_submitted); ?>" />
</p>

<p>
	<label for="frm_repeater_row_delete_confirmation" class="frm_left_label"><?php esc_html_e( 'Repeater row delete confirmation', 'formidable-pro' ); ?>
			<span class="frm_help frm_icon_font frm_tooltip_icon"
				title="<?php esc_attr_e( 'The confirmation message displayed when deleting a Repeater row.', 'formidable-pro' ); ?>"></span>
		</label>
		<input type="text" id="frm_repeater_row_delete_confirmation" name="frm_repeater_row_delete_confirmation"
			class="frm_with_left_label"
			value="<?php echo esc_attr( $frmpro_settings->repeater_row_delete_confirmation ); ?>"/>
</p>
