<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<div class="frm4 frm_form_field">
	<label class="frm_primary_label"><?php esc_html_e( 'Icons', 'formidable-pro' ); ?></label>
	<?php FrmStylesHelper::bs_icon_select( $style, $frm_style, 'minus' ); ?>
</div>
<div class="frm6 frm_form_field">
	<label class="frm_primary_label"><?php esc_html_e( 'Icon Color', 'formidable-pro' ); ?></label>
	<input type="text" name="<?php echo esc_attr( $frm_style->get_field_name( 'repeat_icon_color' ) ); ?>" id="frm_repeat_icon_color" class="hex" value="<?php echo esc_attr( $style->post_content['repeat_icon_color'] ); ?>" <?php do_action( 'frm_style_settings_input_atts', 'repeat_icon_color' ); ?> />
</div>
