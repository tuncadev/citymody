<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<tr class="edit_action_message_box edit_action_box <?php echo ( $values['edit_action'] == 'message' && $values['editable'] == 1 ) ? '' : 'frm_hidden'; ?>">
    <td class="frm_has_shortcodes frm_has_textarea">
		<label for="edit_msg">
			<?php esc_html_e( 'On Update', 'formidable-pro' ); ?>
		</label>
        <textarea name="options[edit_msg]" id="edit_msg" cols="50" rows="2" class="frm_long_input"><?php echo FrmAppHelper::esc_textarea($values['edit_msg']); ?></textarea>
    </td>
</tr>
