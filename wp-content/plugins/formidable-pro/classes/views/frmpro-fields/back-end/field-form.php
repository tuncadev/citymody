<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
$form = empty( $field['form_select'] ) ? '' : FrmForm::getOne( $field['form_select'] );
?>
<span class="frm-with-left-icon frm-not-set <?php if ( ! empty( $form ) ) { ?>
		frm_hidden
	<?php } ?>" id="setup-message-<?php echo esc_attr( $field['id'] ); ?>">
	<?php FrmProAppHelper::icon_by_class( 'frmfont frm_report_problem_solid_icon' ); ?>
	<input type="text" value="<?php esc_attr_e( 'This field is not set up yet.', 'formidable-pro' ); ?>" disabled />
</span>

<div class="frm-embed-field-placeholder frm_grid_container <?php if ( empty( $form ) ) { ?>
		frm_hidden
	<?php } ?>">
	<div class="frm-fake-field"></div>
	<div class="frm-fake-field"></div>
	<div class="frm-fake-field frm6 frm_form_field"></div>
	<div class="frm-fake-field frm6 frm_form_field"></div>
	<div class="frm-embed-message" data-embedmsg="<?php echo esc_attr( __( 'Embedded Form: ', 'formidable-pro' ) ); ?>">
		<?php
		echo esc_html(
			sprintf(
				/* translators: %1$s: Form name */
				__( 'Embedded Form: %1$s', 'formidable-pro' ),
				empty( $form ) ? '' : $form->name
			)
		);
		?>
	</div>
</div>
