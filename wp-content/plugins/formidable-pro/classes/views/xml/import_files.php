<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<p>
	<label for="csv_files">
		<input type="checkbox" name="csv_files" id="csv_files" value="1" <?php checked( $csv_files, 1 ); ?> />
		<?php esc_html_e( 'Import files. If you would like to import files, check this box.', 'formidable-pro' ); ?>
	</label>
</p>
