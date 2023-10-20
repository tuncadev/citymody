<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<p class="howto">
	<?php esc_html_e( 'Remove references to Formidable Forms to provide an unbranded experience for your clients.', 'formidable-pro' ); ?>
</p>
<p>
	<label for="frm_menu" class="frm_left_label"><?php esc_html_e( 'Plugin Label', 'formidable-pro' ); ?></label>
	<input type="text" name="frm_menu" id="frm_menu" value="<?php echo esc_attr( $frm_settings->menu ); ?>" />
	<?php if ( is_multisite() && current_user_can( 'setup_network' ) ) { ?>
		<label for="frm_mu_menu">
			<input type="checkbox" name="frm_mu_menu" id="frm_mu_menu" value="1" <?php checked( $frm_settings->mu_menu, 1 ); ?> />
			<?php esc_html_e( 'Use this menu name site-wide', 'formidable-pro' ); ?>
		</label>
	<?php } ?>
</p>

<p class="frm_insert_form">
	<label class="frm_left_label">
		<?php esc_html_e( 'Plugin Icon', 'formidable-pro' ); ?>
	</label>
	<?php foreach ( array( '', 'frmfont frm_white_label_icon', 'dashicons dashicons-feedback' ) as $icon ) { ?>
		<label class="frm-example-icon">
			<input type="radio" name="frm_menu_icon" value="<?php echo esc_attr( $icon ); ?>" <?php checked( $frmpro_settings->menu_icon, $icon ); ?> />
			<?php
			if ( empty( $icon ) ) {
				echo FrmAppHelper::svg_logo(
					array(
						'height' => 22,
						'width'  => 22,
					)
				);
			} else {
				FrmProAppHelper::icon_by_class( $icon );
			}
			?>
		</label>
	<?php } ?>
</p>
