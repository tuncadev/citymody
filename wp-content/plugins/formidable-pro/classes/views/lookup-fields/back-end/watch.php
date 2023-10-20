<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<label class="frm_primary_label">
	<?php esc_html_e( 'Filter by Lookup Fields', 'formidable-pro' ); ?>
</label>
<div id="frm_watch_lookup_block_<?php echo esc_attr( $field['id'] ); ?>" class="frm_add_remove <?php echo esc_attr( empty( $field['watch_lookup'] ) ? 'frm_hidden' : '' ); ?>"><?php
	$field_id = $field['id'];
	foreach ( $field['watch_lookup'] as $row_key => $selected_field ) {
		include( FrmProAppHelper::plugin_path() . '/classes/views/lookup-fields/back-end/watch-row.php' );
	}
?></div>
<p>
	<a href="#" id="frm_add_watch_lookup_link_<?php echo esc_attr( $field['id'] ); ?>" class="frm-small-add frm_add_watch_lookup_row frm_add_watch_lookup_link">
		<span class="frm_icon_font frm_add_tag"></span>
		<?php esc_html_e( 'Watch a Lookup Field', 'formidable-pro' ); ?>
	</a>
</p>
