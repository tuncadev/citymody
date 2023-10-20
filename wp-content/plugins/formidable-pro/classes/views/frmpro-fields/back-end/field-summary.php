<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<div class="frm-embed-field-placeholder frm_grid_container">
	<div class="frm-fake-field"></div>
	<div class="frm-fake-field"></div>
	<div class="frm-fake-field frm6 frm_form_field"></div>
	<div class="frm-fake-field frm6 frm_form_field"></div>
	<div class="frm-summary-message frm-embed-message">
		<?php
		esc_html_e( 'Summary: This field displays a summary of values entered on previous pages.', 'formidable-pro' );
		?>
	</div>
</div>
