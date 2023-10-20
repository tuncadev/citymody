<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

_deprecated_file( basename( __FILE__ ), '4.09', null, 'This file can be found in formidable-views/classes/views/displays/mb_form_disp_type.php' );
?>
<div class="frm_grid_container">
	<p class="frm4 frm_form_field">
		<label>
			<?php esc_html_e( 'Use Entries from Form', 'formidable-pro' ); ?>
		</label>
	</p>
	<p class="frm8 frm_form_field">
		<?php FrmFormsHelper::forms_dropdown( 'form_id', $post->frm_form_id, array( 'inc_children' => 'include' ) ); ?>
	</p>

	<p class="frm4 frm_form_field">
		<label>
			<?php esc_html_e( 'View Format', 'formidable-pro' ); ?>
		</label>
	</p>
	<div class="frm8 frm_form_field">
            <fieldset>
				<p>
					<label for="all">
						<input type="radio" value="all" id="all" <?php checked( $post->frm_show_count, 'all' ); ?> name="show_count" />
						<?php esc_html_e( 'All Entries &mdash; list all entries in the specified form', 'formidable-pro' ); ?>.
					</label>
				</p>
				<p>
					<label for="one">
						<input type="radio" value="one" id="one" <?php checked( $post->frm_show_count, 'one' ); ?> name="show_count" /> <?php esc_html_e( 'Single Entry &mdash; display one entry', 'formidable-pro' ); ?>.
					</label>
				</p>
				<p>
					<label for="dynamic">
						<input type="radio" value="dynamic" id="dynamic" <?php checked( $post->frm_show_count, 'dynamic' ); ?> name="show_count" /> <?php esc_html_e( 'Both (Dynamic) &mdash; list the entries that will link to a single entry page', 'formidable-pro' ); ?>.
					</label>
				</p>
				<p>
					<label for="calendar">
						<input type="radio" value="calendar" id="calendar" <?php checked( $post->frm_show_count, 'calendar' ); ?> name="show_count" />
						<?php esc_html_e( 'Calendar &mdash; insert entries into a calendar', 'formidable-pro' ); ?>.
					</label>
				</p>
            </fieldset>

            <div id="date_select_container" class="frm_indent_opt <?php echo ( $post->frm_show_count == 'calendar' ) ? '' : 'frm_hidden'; ?>">
                <?php include(FrmProAppHelper::plugin_path() . '/classes/views/displays/_calendar_options.php'); ?>
            </div>
	</div>
</div>
