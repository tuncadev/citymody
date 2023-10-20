<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<?php _deprecated_file( esc_html( basename( __FILE__ ) ), '4.0', 'FrmProEntriesController::save_new_entry_button' ); ?>
<div id="postbox-container-1" class="postbox-container frm-right-panel">
    <div id="submitdiv" class="postbox">
    <div class="inside">
        <div class="submitbox">
        <div id="major-publishing-actions">
    	    <div id="publishing-action">
				<?php echo FrmProFormsHelper::get_draft_button( $form, 'button-secondary' ); ?>
				<?php submit_button( $submit, 'primary', '', false ); ?>
            </div>
            <div class="clear"></div>
        </div>
        </div>
    </div>
    </div>
</div>
