<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<form enctype="multipart/form-data" method="post" id="form_<?php echo esc_attr( $form->form_key ); ?>" class="frm-show-form" <?php FrmProFormsHelper::maybe_echo_antispam_token( $form->id ); ?>>
<div id="form_entries_page" class="frm_wrap frm_single_entry_page">
	<div class="frm_forms" id="frm_form_<?php echo (int) $form->id; ?>_container">

		<?php
		FrmAppHelper::get_admin_header(
			array(
				'label'       => __( 'Edit Entry', 'formidable-pro' ),
				'link_hook'   => array(
					'hook' => 'frm_entry_inside_h2',
					'param' => $form,
				),
				'form'        => $form,
				'hide_title'  => true,
				'close'       => '?page=formidable-entries&form=' . $form->id,
				'publish'     => array( 'FrmProEntriesController::edit_entry_button', compact( 'form', 'values', 'entry' ) ),
			)
		);
		?>

		<div class="columns-2">

		<div id="post-body-content">
			<div class="frm-entry-container frm-fields <?php echo FrmFormsHelper::get_form_style_class($values); ?>">
			<h2 class="frm-entry-title">
				<span><?php esc_html_e( 'Edit Entry', 'formidable-pro' ); ?></span>
				<span class="frm-sub-label">
					<?php
					printf(
						/* translators: %d: Entry ID */
						esc_html__( '(ID %d)', 'formidable' ),
						$entry->id
					);
					?>
				</span>
				<?php
				FrmAppHelper::add_new_item_link(
					array(
						'new_link' => admin_url( 'admin.php?page=formidable-entries&frm_action=new&form=' . $form->id ),
					)
				);
				FrmProEntriesHelper::get_entry_navigation( $id, $form->id, 'edit' );
				?>
			</h2>

			<?php
			include( FrmAppHelper::plugin_path() . '/classes/views/frm-entries/errors.php' );

			$form_action = 'update';
			require( FrmAppHelper::plugin_path() . '/classes/views/frm-entries/form.php' );
			?>

			<p>
				<?php echo FrmProFormsHelper::get_prev_button( $form, 'button-secondary' ); ?>
				<input class="button-primary" type="submit" value="<?php echo esc_attr( $submit ); ?>" <?php do_action('frm_submit_button_action', $form, $form_action); ?> />
			</p>
			<div class="clear"></div>
			</div>
		</div>

		<?php
		$record = $entry;
		require( FrmProAppHelper::plugin_path() . '/classes/views/frmpro-entries/sidebar-edit.php' );
		?>

		</div>
	</div>
</div>
</form>
