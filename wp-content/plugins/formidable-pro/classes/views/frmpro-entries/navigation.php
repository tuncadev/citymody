<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<div class="frm-entry-navigation">
	<?php if ( $previous_entry_id ) : ?>
		<a class="frm-entry-link frm-prev-entry" href="<?php echo esc_url( $base_url . $previous_entry_id ); ?>">
			<?php FrmAppHelper::icon_by_class( 'frmfont frm_arrow_left_icon', array( 'aria-label' => __( 'Previous Entry', 'formidable-pro' ) ) ); ?>
		</a>
	<?php else : ?>
		<span class="frm-entry-link frm-prev-entry frm-disabled">
			<?php FrmAppHelper::icon_by_class( 'frmfont frm_arrow_left_icon', array( 'aria-label' => __( 'Previous Entry', 'formidable-pro' ) ) ); ?>
		</span>
	<?php endif; ?>

	<span class="frm-entry-count">
		<?php
		printf(
			/* translators: 1: current entry count, 2: total entries count */
			esc_html__( '%1$s of %2$s', 'formidable-pro' ),
			'<span class="current-page">' . esc_html( $current_entry_position + 1 ) . '</span>',
			'<span class="total-pages">' . esc_html( $total_entries_count ) . '</span>'
		);
		?>
	</span>

	<?php if ( $next_entry_id ) : ?>
		<a class="frm-entry-link frm-next-entry" href="<?php echo esc_url( $base_url . $next_entry_id ); ?>">
			<?php FrmAppHelper::icon_by_class( 'frmfont frm_arrow_right_icon', array( 'aria-label' => __( 'Next page', 'formidable-pro' ) ) ); ?>
		</a>
	<?php else : ?>
		<span class="frm-entry-link frm-next-entry frm-disabled">
			<?php FrmAppHelper::icon_by_class( 'frmfont frm_arrow_right_icon', array( 'aria-label' => __( 'Next page', 'formidable-pro' ) ) ); ?>
		</span>
	<?php endif; ?>
</div><!-- .frm-entry-navigation -->
