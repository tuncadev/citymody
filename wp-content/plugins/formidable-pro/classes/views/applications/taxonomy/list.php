<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<div class="frm_wrap">
	<?php FrmApplicationsController::render_applications_header( __( 'Applications', 'formidable-pro' ), 'list' ); ?>
	<div class="wrap">
		<div>
		</div>
		<div id="frm-custom-applications-index-container"></div>
	</div>
</div>
