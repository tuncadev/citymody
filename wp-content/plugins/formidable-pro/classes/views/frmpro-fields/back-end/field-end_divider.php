<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<span class="show_repeat_sec repeat_icon_links repeat_format<?php echo esc_attr( $field['format'] ); ?>">
	<a href="javascript:void(0)" class="frm_add_form_row <?php echo ( $field['format'] == '' ) ? '' : 'frm_button'; ?>">
		<?php FrmProAppHelper::icon_by_class( 'frmfont frm_plus2_icon' ); ?>
		<span id="<?php echo esc_attr( $field['field_key'] ); ?>_add_label" class="frm_repeat_label"><?php echo esc_html( $field['add_label'] ); ?></span>
	</a> &nbsp;
	<a href="javascript:void(0)" class="frm_remove_form_row <?php echo ( $field['format'] == '' ) ? '' : 'frm_button'; ?>">
		<?php FrmProAppHelper::icon_by_class( 'frmfont frm_minus2_icon' ); ?>
		<span id="<?php echo esc_attr( $field['field_key'] ); ?>_remove_label" class="frm_repeat_label"><?php echo esc_html( $field['remove_label'] ); ?></span>
	</a>
</span>
