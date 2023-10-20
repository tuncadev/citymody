<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<form enctype="multipart/form-data" method="post" id="form_<?php echo esc_attr( $form->form_key ); ?>" class="frm-show-form" <?php FrmProFormsHelper::maybe_echo_antispam_token( $form->id ); ?>>
<div id="form_entries_page" class="frm_wrap frm_single_entry_page frm-new-entry">
	<div class="frm_forms" id="frm_form_<?php echo (int) $form->id; ?>_container">

		<?php
		FrmAppHelper::get_admin_header(
			array(
				'label'       => __( 'Add New Entry', 'formidable-pro' ),
				'form'        => $form,
				'hide_title'  => true,
				'close'       => '?page=formidable-entries&form=' . $form->id,
				'publish'     => array( 'FrmProEntriesController::save_new_entry_button', compact( 'form', 'values' ) ),
			)
		);
		?>

		<div class="columns-2">

		<div id="post-body-content">
			<div class="frm-entry-container frm-fields <?php echo FrmFormsHelper::get_form_style_class( $values ); ?>">
			<h2><?php esc_html_e( 'Add New Entry', 'formidable-pro' ); ?></h2>
			<?php if ( empty( $values ) ) { ?>
				<p class="frm_error_style frm_form_fields">
					<strong><?php esc_html_e( 'Oops!', 'formidable-pro' ); ?></strong>
					<?php printf( __( 'You did not add any fields to your form. %1$sGo back%2$s and add some.', 'formidable-pro' ), '<br/><a href="' . esc_url( admin_url('?page=formidable&frm_action=edit&id=' . $form->id ) ) . '">', '</a>'); ?>
				</p>
				<?php
        	} else {

				include( FrmAppHelper::plugin_path() . '/classes/views/frm-entries/errors.php' );

				$form_action = 'create';
				require( FrmAppHelper::plugin_path() . '/classes/views/frm-entries/form.php' );
				?>

			<p>
				<?php echo FrmProFormsHelper::get_prev_button($form, 'button-secondary'); ?>
				<input class="button-primary" type="submit" value="<?php echo esc_attr($submit); ?>" <?php do_action('frm_submit_button_action', $form, $form_action); ?> />
				<?php echo FrmProFormsHelper::get_draft_link($form); ?>
				<?php echo FrmProFormsHelper::get_start_over_html( $form ); ?>
			</p>
			<div class="clear"></div>
				<?php
			}
			?>
			</div>
			</div>
		</div>
	</div>
</div>
</form>
