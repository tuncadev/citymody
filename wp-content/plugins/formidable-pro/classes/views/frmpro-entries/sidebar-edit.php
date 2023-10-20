<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<div class="frm-right-panel">
	<?php if ( has_action( 'frm_edit_entry_publish_box', $record ) ) { ?>
		<div id="submitdiv" class="postbox ">
			<div class="inside">
				<div class="submitbox">
					<div id="minor-publishing" style="border:none;">
						<div class="misc-pub-section frm-postbox-no-h3">
							<?php do_action( 'frm_edit_entry_publish_box', $record ); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php } ?>

	<?php
	do_action( 'frm_edit_entry_sidebar', $record );
    FrmEntriesController::entry_sidebar($record);
    ?>
</div>
