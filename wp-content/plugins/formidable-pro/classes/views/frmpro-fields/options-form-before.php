<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<div class="frm_import_options frm_grid_container">
	<p class="frm6 frm_form_field">
		<label for="frm_tax_entry_field_<?php echo absint( $field['id'] ); ?>">
			<?php esc_html_e( 'Load Options From', 'formidable-pro' ); ?>
		</label>
		<select name="frm_tax_entry_field_<?php echo absint( $field['id'] ); ?>" id="frm_tax_entry_field_<?php echo absint( $field['id'] ); ?>" class="frm_tax_form_select">
			<option value=""><?php esc_html_e( '&mdash; Select &mdash;', 'formidable-pro' ); ?></option>
			<option value="form" <?php echo ( is_object( $selected_field ) ) ? 'selected="selected"' : ''; ?>>
				<?php esc_html_e( 'Form Entries', 'formidable-pro' ); ?>
			</option>
			<option value="taxonomy" <?php
			if ( ! is_object( $selected_field ) ) {
				selected( $selected_field, 'taxonomy' );
			}
			?>>
				<?php esc_html_e( 'Category/Taxonomy', 'formidable-pro' ); ?>
			</option>
		</select>
</p>

<p id="frm_show_selected_forms_<?php echo absint( $field['id'] ); ?>" class="frm6 frm_form_field <?php echo is_object( $selected_field ) ? '' : 'frm_hidden'; ?>">
	<label for="frm_options_field_<?php echo absint( $field['id'] ); ?>">
		<?php esc_html_e( 'Select a Form', 'formidable-pro' ); ?>
	</label>
<select class="frm_options_field_<?php echo absint( $field['id'] ); ?> frm_get_field_selection" id="frm_options_field_<?php echo absint( $field['id'] ); ?>">
	<option value="">&mdash; <?php esc_html_e( 'Select Form', 'formidable-pro' ); ?> &mdash;</option>
    <?php foreach ( $form_list as $form_opts ) { ?>
	<option value="<?php echo absint( $form_opts->id ); ?>" <?php selected( $form_opts->id, $selected_form_id ); ?>><?php echo FrmAppHelper::truncate( $form_opts->name, 30 ); ?></option>
    <?php } ?>
</select>
</p>

<p id="frm_show_selected_fields_<?php echo absint( $field['id'] ); ?>" class="<?php echo esc_attr( is_object( $selected_field ) ? 'frm6 frm_form_field' : '' ); ?>">
    <?php
    if ( is_object( $selected_field ) ) {
		?>
		<label>
			<?php esc_html_e( 'Select a Field', 'formidable-pro' ); ?>
		</label>
		<?php
        include( FrmProAppHelper::plugin_path() . '/classes/views/frmpro-fields/field-selection.php' );
	} elseif ( $selected_field == 'taxonomy' ) {
    ?>
	<div class="frm-inline-message">
		<?php esc_html_e( 'Select a taxonomy on the Form Actions tab of the Form Settings page', 'formidable-pro' ); ?>
	</div>
	<input type="hidden" name="field_options[form_select_<?php echo absint( $current_field_id ); ?>]" value="taxonomy" />
    <?php
    }
    ?>
</p>
<?php
// Option Order.
require( FrmProAppHelper::plugin_path() . '/classes/views/frmpro-fields/back-end/order.php' );
?>
</div>

<p>
	<label for="restrict_<?php echo absint( $field['id'] ); ?>">
		<input type="checkbox" name="field_options[restrict_<?php echo absint( $field['id'] ); ?>]" id="restrict_<?php echo absint( $field['id'] ); ?>" value="1" <?php checked( $field['restrict'], 1 ); ?>/>
		<?php esc_html_e( 'Limit options to those created by the current user', 'formidable-pro' ); ?>
	</label>
</p>
