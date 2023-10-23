<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
$file_size_range = $this->get_file_size_range();
?>
<div class="frm_dropzone dz-clickable">
	<div class="dz-message">
		<span class="frm_icon_font frm_upload_icon"></span>
		<?php echo esc_html( $field['drop_msg'] ); ?>
		<div class="frm_small_text">
			<p><?php echo esc_html( $this->get_range_string( $file_size_range ) ); ?></p>
		</div>
	</div>
</div>
<input type="hidden" name="<?php echo esc_attr( $field_name ); ?>" />
