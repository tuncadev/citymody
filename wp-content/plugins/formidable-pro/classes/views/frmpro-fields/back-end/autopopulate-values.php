<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<div class="frm_grid_container">
	<?php $field_obj->show_get_options( $field ); ?>

	<div class="frm_form_field">
		<label class="frm_primary_label">
			<?php esc_html_e( 'Watch Lookup Fields', 'formidable-pro' ); ?>
		</label>
		<div id="frm_watch_lookup_block_<?php echo absint( $field['id'] ); ?>" class="frm_add_remove"><?php
			if ( empty( $field['watch_lookup'] ) && ! empty( $lookup_fields ) ) {
				$field_id = $field['id'];
				$row_key = 0;
				$selected_field = '';
				include( FrmProAppHelper::plugin_path() . '/classes/views/lookup-fields/back-end/watch-row.php' );
			} elseif ( isset( $field['watch_lookup'] ) ) {
				$field_id = $field['id'];
				foreach ( $field['watch_lookup'] as $row_key => $selected_field ) {
					include( FrmProAppHelper::plugin_path() . '/classes/views/lookup-fields/back-end/watch-row.php' );
				}
			}
		?></div>
		<a href="#" id="frm_add_watch_lookup_link_<?php echo esc_attr( $field['id'] ); ?>" class="frm-small-add frm_add_watch_lookup_row frm_add_watch_lookup_link">
			<span class="frm_icon_font frm_add_tag"></span>
			<?php esc_html_e( 'Watch a Lookup Field', 'formidable-pro' ); ?>
		</a>
	</div>
</div>
<p>
	<label for="get_most_recent_value_<?php echo absint( $field['id'] ); ?>">
		<input type="checkbox" value="1" name="field_options[get_most_recent_value_<?php echo absint( $field['id'] ); ?>]" <?php checked( isset( $field['get_most_recent_value'] ) ? $field['get_most_recent_value'] : 0, 1 ); ?> id="get_most_recent_value_<?php echo absint( $field['id'] ); ?>" />
		<?php esc_html_e( 'Get only the most recent value', 'formidable-pro' ); ?>
	</label>
</p>
<p class="howto frm_no_bottom_margin">
	<?php esc_html_e( 'Dynamically retrieve the value of this field from a lookup field.', 'formidable-pro' ); ?>
</p>
