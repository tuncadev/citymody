<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<div class="frm_wrap">
	<?php FrmApplicationsController::render_applications_header( $tag->name, 'edit' ); ?>
	<div id="frm_application_content" class="wrap">
		<div id="frm_application_header" class="frm-display-flex frm-align-items-center frm_hidden">
			<?php
			require FrmProAppHelper::plugin_path() . '/classes/views/applications/buttons/sync.php';
			require FrmProAppHelper::plugin_path() . '/classes/views/applications/buttons/export.php';
			?>
			<div class="frm-flex"></div>
			<div id="frm_application_item_search_wrapper"></div>
		</div>
		<div id="frm_edit_application_container"></div>
	</div>
</div>
