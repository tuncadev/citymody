<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<p class="frm4 frm_first frm_form_field">
	<label for="frm_progress_bg_color"><?php esc_html_e( 'BG Color', 'formidable-pro' ); ?></label>
	<input type="text" name="<?php echo esc_attr( $frm_style->get_field_name('progress_bg_color') ); ?>" id="frm_progress_bg_color" class="hex" value="<?php echo esc_attr( $style->post_content['progress_bg_color'] ); ?>" size="4" <?php do_action( 'frm_style_settings_input_atts', 'progress_bg_color' ); ?> />
</p>

<p class="frm4 frm_form_field">
	<label for="frm_progress_color"><?php esc_html_e( 'Text Color', 'formidable-pro' ); ?></label>
	<input type="text" name="<?php echo esc_attr( $frm_style->get_field_name('progress_color') ); ?>" id="frm_progress_color" class="hex" value="<?php echo esc_attr( $style->post_content['progress_color'] ); ?>" <?php do_action( 'frm_style_settings_input_atts', 'progress_color' ); ?> />
</p>

<p class="frm4 frm_first frm_form_field">
	<label for="frm_progress_active_bg_color"><?php esc_html_e( 'Active BG', 'formidable-pro' ); ?></label>
	<input type="text" name="<?php echo esc_attr( $frm_style->get_field_name('progress_active_bg_color') ); ?>" id="frm_progress_active_bg_color_color" class="hex" value="<?php echo esc_attr( $style->post_content['progress_active_bg_color'] ); ?>" size="4" <?php do_action( 'frm_style_settings_input_atts', 'progress_active_bg_color' ); ?> />
</p>

<p class="frm4 frm_form_field">
	<label for="frm_progress_active_color"><?php esc_html_e( 'Active Text', 'formidable-pro' ); ?></label>
	<input type="text" name="<?php echo esc_attr( $frm_style->get_field_name('progress_active_color') ); ?>" id="frm_progress_active_color" class="hex" value="<?php echo esc_attr( $style->post_content['progress_active_color'] ); ?>" size="4" <?php do_action( 'frm_style_settings_input_atts', 'progress_active_color' ); ?> />
</p>

<p class="frm4 frm_first frm_form_field">
	<label for="frm_progress_border_color"><?php esc_html_e( 'Border Color', 'formidable-pro' ); ?></label>
	<input type="text" name="<?php echo esc_attr( $frm_style->get_field_name('progress_border_color') ); ?>" id="frm_progress_border_color" class="hex" value="<?php echo esc_attr( $style->post_content['progress_border_color'] ); ?>" size="4" <?php do_action( 'frm_style_settings_input_atts', 'progress_border_color' ); ?> />
</p>

<p class="frm4 frm_form_field">
	<label for="frm_progress_border_size"><?php esc_html_e( 'Border Size', 'formidable-pro' ); ?></label>
	<input type="text" name="<?php echo esc_attr( $frm_style->get_field_name('progress_border_size') ); ?>" id="frm_progress_border_size" value="<?php echo esc_attr( $style->post_content['progress_border_size'] ); ?>" size="4" />
</p>

<p class="frm4 frm_form_field">
	<label for="frm_progress_size"><?php esc_html_e( 'Circle Size', 'formidable-pro' ); ?></label>
	<input type="text" name="<?php echo esc_attr( $frm_style->get_field_name('progress_size') ); ?>" id="frm_progress_size" value="<?php echo esc_attr( $style->post_content['progress_size'] ); ?>" size="4" />
</p>
