<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<p class="frm4 frm_first frm_form_field">
	<label for="frm_progress_bg_color"><?php esc_html_e( 'Color', 'formidable-pro' ); ?></label>
	<input type="text" name="<?php echo esc_attr( $frm_style->get_field_name( 'slider_color' ) ); ?>" id="frm_slider_color" class="hex" value="<?php echo esc_attr( $style->post_content['slider_color'] ); ?>" size="4" <?php do_action( 'frm_style_settings_input_atts', 'slider_color' ); ?> />
</p>

<p class="frm4 frm_form_field">
	<label for="frm_progress_color"><?php esc_html_e( 'Bar Color', 'formidable-pro' ); ?></label>
	<input type="text" name="<?php echo esc_attr( $frm_style->get_field_name( 'slider_bar_color' ) ); ?>" id="frm_slider_bar_color" class="hex" value="<?php echo esc_attr( $style->post_content['slider_bar_color'] ); ?>" <?php do_action( 'frm_style_settings_input_atts', 'slider_bar_color' ); ?> />
</p>

<p class="frm4 frm_form_field">
	<label><?php esc_html_e( 'Font Size', 'formidable' ); ?></label>
	<input type="text" name="<?php echo esc_attr( $frm_style->get_field_name( 'slider_font_size' ) ); ?>" id="frm_slider_font_size" value="<?php echo esc_attr( $style->post_content['slider_font_size'] ); ?>" size="3" />
</p>
