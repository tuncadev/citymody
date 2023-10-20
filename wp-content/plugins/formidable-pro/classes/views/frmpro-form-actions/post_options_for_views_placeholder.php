<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<p class="frm6 frm_form_field frm_dyncontent_opt frm_hidden">
	<label><?php esc_html_e( 'Select View', 'formidable-views' ); ?></label>
	<a href="<?php echo esc_url( $link ); ?>" target="_blank" class="frm_pro_tip">
		<?php FrmAppHelper::icon_by_class( 'frmfont frm_star_full_icon', array( 'aria-hidden' => 'true' ) ); ?>
		<span class="pro-tip">
			<?php esc_html_e( 'Pro Tip:', 'formidable-pro' ); ?>
		</span>
		<?php esc_html_e( 'Need to customize post content?', 'formidable-pro' ); ?>
		<span class="frm-tip-cta">
			<?php esc_html_e( 'Add views', 'formidable-pro' ); ?>
		</span>
	</a>
</p>
<input id="<?php echo esc_attr( $display_id_field_name ); ?>" name="<?php echo esc_attr( $display_id_field_name ); ?>" type="hidden" value="<?php echo esc_attr( $display_id ); ?>" />
