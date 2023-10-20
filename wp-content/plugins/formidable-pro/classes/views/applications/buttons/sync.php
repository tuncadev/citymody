<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
$tooltip = __( 'Clicking sync will check your site for forms, Views, and pages that contain embedded items or that are used as data sources, and adds them to this application.', 'formidable-pro' );
?>
<a href="#" class="button-secondary frm-button-secondary frm-button-sm frm-sync-application-button" title="<?php echo esc_attr( $tooltip ); ?>" data-placement="right">
	<?php esc_html_e( 'Sync', 'formidable-pro' ); ?>
</a>
