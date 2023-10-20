<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<div class="frm_wrap">
    <div id="frm_top_bar">
        <a href="<?php echo esc_url( admin_url( 'admin.php?page=formidable' ) ); ?>" class="frm-header-logo">
            <?php FrmAppHelper::show_header_logo(); ?>
        </a>
        <div class="frm_top_left">
            <h1>
                <?php esc_html_e( 'Import/Export', 'formidable-pro' ); ?>
            </h1>
        </div>
    </div>
    <div class="wrap">
        <?php include FrmAppHelper::plugin_path() . '/classes/views/shared/errors.php'; ?>

        <div id="poststuff" class="metabox-holder">
            <div id="post-body">
                <div id="post-body-content">
                    <h2 class="frm-h2"><?php esc_html_e( 'Importing CSV', 'formidable-pro' ); ?></h2>
                    <div class="inside">
                        <div class="with_frm_style" id="frm_import_message">
                            <span class="frm_message" style="display: inline-block; padding: 7px;">
                                <?php printf( __( '%1$s entries are importing', 'formidable-pro' ), '<span class="frm_csv_remaining">' . $left . '</span>' ); ?>
                            </span>
                        </div>

                        <div class="frm_admin_progress">
                            <div class="frm_admin_progress_bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="<?php echo esc_attr( $left ); ?>" style="width:0%;">
                            </div>
                        </div>

                        <?php if ( $left > 250 ) { ?>
                            <div class="frm_warning_style" role="alert">
                                <?php
                                FrmAppHelper::icon_by_class( 'frmfont frm_alert_icon' );
                                echo ' ';
                                esc_html_e( 'Entries are imported in batches. Please wait for all entries to import before leaving the page.', 'formidable-pro' );
                                ?>
                            </div>
                        <?php } ?>

<script type="text/javascript">
/*<![CDATA[*/
__FRMURLVARS="<?php echo $url_vars; ?>";
jQuery(document).ready(function(){frmImportCsv(<?php echo (int) $form_id; ?>);window.addEventListener( 'beforeunload', frmConfirmImportExit );});
function frmConfirmImportExit( event ) {
    if ( document.getElementById( 'frm_import_message' ).innerHTML !== frm_admin_js.import_complete ) {
        event.preventDefault();
        event.returnValue = '';
    }
}
/*]]>*/
</script>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
