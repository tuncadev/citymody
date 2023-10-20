<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<!-- Add email attachment row -->
<div class="frm_email_row">

	<h3>
		<?php esc_html_e( 'Attachment', 'formidable-pro' ); ?>
	</h3>

	<!-- Attachment control container -->
	<div class="frm_email_add_attachment_container frm_image_styling_frame" style="margin-left:0">
		<p class="frm-m-0 frm-mb-sm">
			<a href="#" class="<?php echo esc_attr( $has_attachment ? 'frm_hidden' : '' ); ?> frm_email_add_attachment button frm-button-secondary frm-button-sm">
				<?php esc_html_e( 'Add Attachment', 'formidable-pro' ); ?>
			</a>
		</p>

		<span class="frm_email_attachment_icon">
			<?php
			if ( $has_attachment ) {
				echo wp_get_attachment_image( $form_action->post_content['email_attachment_id'], array( '20', '20' ), true, array( 'class' => 'frm_image_preview' ) );
			}
			?>
		</span>
		<div class="frm_image_data">
			<div class="frm_email_attachment_name">
				<?php
				if ( $has_attachment ) {
					echo esc_html( basename( get_attached_file( $form_action->post_content['email_attachment_id'] ) ) );
				}
				?>
			</div>

			<?php
			/**
			 * Anchor link to remove the attachment.
			 */
			printf(
				'<a class="%s frm_email_remove_attachment frm_remove_image_option" href="#" title="%s">%s %s</a>',
				( $has_attachment ? '' : 'frm_hidden' ),
				esc_attr__( 'Remove file', 'formidable-pro' ),
				FrmAppHelper::icon_by_class( 'frm_icon_font frm_delete_icon', array( 'echo' => false ) ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				esc_html__( 'Delete', 'formidable' )
			);
			?>
		</div>
		<input class="frm_email_attachment" type="hidden" name="<?php echo esc_attr( $pass_args['action_control']->get_field_name( 'email_attachment_id' ) ); ?>" value="<?php echo esc_attr( isset( $form_action->post_content['email_attachment_id'] ) ? $form_action->post_content['email_attachment_id'] : '' ); ?>" />
	</div>
	<!-- Attachment control container end. -->

	<?php
	if ( ! empty( $can_generate_csv_file ) ) {
		FrmProHtmlHelper::admin_toggle(
			'frm_attach_csv',
			$pass_args['action_control']->get_field_name( 'attach_csv' ),
			array(
				'div_class' => 'with_frm_style frm_toggle',
				'checked'   => ! empty( $form_action->post_content['attach_csv'] ),
				'echo'      => true,
			)
		);
		?>
		<label id="frm_attach_csv_label" for="frm_attach_csv">
			<?php esc_html_e( 'Attach CSV export of entry to email', 'formidable-pro' ); ?>
		</label>
		<?php
	}

	FrmProFormActionsController::show_disabled_pdf_attachment_option();

	/**
	 * Fires after the email attachment setting row.
	 *
	 * @since 5.4.2
	 *
	 * @param array $args {
	 *     The args.
	 *
	 *     @type FrmFormAction $form_action Form action object.
	 *     @type array         $pass_args   Pass args. See {@see FrmProFormActionsController::add_file_attachment_field()}.
	 * }
	 */
	do_action( 'frm_pro_after_email_attachment_row', compact( 'form_action', 'pass_args' ) );
	?>
<!-- Add email attachment row end. -->
