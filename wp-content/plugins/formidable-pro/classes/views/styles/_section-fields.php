<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
		<p class="frm4 frm_first frm_form_field">
			<label><?php esc_html_e( 'Color', 'formidable-pro' ); ?></label>
			<input type="text" name="<?php echo esc_attr( $frm_style->get_field_name('section_color') ); ?>" id="frm_section_color" class="hex" value="<?php echo esc_attr( $style->post_content['section_color'] ); ?>" <?php do_action( 'frm_style_settings_input_atts', 'section_color' ); ?> />
		</p>

		<p class="frm4 frm_form_field">
			<label><?php esc_html_e( 'Weight', 'formidable-pro' ); ?></label>
			<select name="<?php echo esc_attr( $frm_style->get_field_name('section_weight') ); ?>" id="frm_section_weight">
				<?php foreach ( FrmStyle::get_bold_options() as $value => $name ) { ?>
				<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $style->post_content['section_weight'], $value ); ?>><?php echo esc_html( $name ); ?></option>
				<?php } ?>
			</select>
		</p>

		<p class="frm4 frm_form_field">
			<label><?php esc_html_e( 'Size', 'formidable-pro' ); ?></label>
			<input type="text" name="<?php echo esc_attr( $frm_style->get_field_name('section_font_size') ); ?>" id="frm_section_font_size" value="<?php echo esc_attr( $style->post_content['section_font_size'] ); ?>" />
		</p>

		<p class="frm6 frm_first frm_form_field">
			<label class="background"><?php esc_html_e( 'BG color', 'formidable-pro' ); ?></label>
			<input type="text" name="<?php echo esc_attr( $frm_style->get_field_name('section_bg_color') ); ?>" id="frm_section_bg_color" class="hex" value="<?php echo esc_attr( $style->post_content['section_bg_color'] ); ?>" <?php do_action( 'frm_style_settings_input_atts', 'section_bg_color' ); ?> />
		</p>

		<p class="frm6 frm_form_field">
			<label><?php esc_html_e( 'Padding', 'formidable-pro' ); ?></label>
			<input type="text" name="<?php echo esc_attr( $frm_style->get_field_name('section_pad') ); ?>" id="frm_section_pad" value="<?php echo esc_attr( $style->post_content['section_pad'] ); ?>" />
		</p>

		<p class="frm6 frm_first frm_form_field">
			<label><?php esc_html_e( 'Top Margin', 'formidable-pro' ); ?></label>
			<input type="text" name="<?php echo esc_attr( $frm_style->get_field_name('section_mar_top') ); ?>" id="frm_section_mar_top" value="<?php echo esc_attr( $style->post_content['section_mar_top'] ); ?>" />
		</p>

		<p class="frm6 frm_form_field">
			<label><?php esc_html_e( 'Bottom Margin', 'formidable-pro' ); ?></label>
			<input type="text" name="<?php echo esc_attr( $frm_style->get_field_name('section_mar_bottom') ); ?>" id="frm_section_mar_bottom" value="<?php echo esc_attr( $style->post_content['section_mar_bottom'] ); ?>" />
		</p>

		<p class="frm4 frm_first frm_form_field">
			<label><?php esc_html_e( 'Border', 'formidable-pro' ); ?></label>
			<input type="text" name="<?php echo esc_attr( $frm_style->get_field_name('section_border_color') ); ?>" id="frm_section_border_color" class="hex" value="<?php echo esc_attr( $style->post_content['section_border_color'] ); ?>" <?php do_action( 'frm_style_settings_input_atts', 'section_border_color' ); ?> />
		</p>

		<p class="frm4 frm_form_field">
			<label><?php esc_html_e( 'Thickness', 'formidable-pro' ); ?></label>
			<input type="text" name="<?php echo esc_attr( $frm_style->get_field_name('section_border_width') ); ?>" id="frm_section_border_width" value="<?php echo esc_attr( $style->post_content['section_border_width'] ); ?>" />
		</p>

		<p class="frm4 frm_form_field">
			<label><?php esc_html_e( 'Style', 'formidable-pro' ); ?></label>
			<select name="<?php echo esc_attr( $frm_style->get_field_name('section_border_style') ); ?>" id="frm_section_border_style">
				<option value="solid" <?php selected( $style->post_content['section_border_style'], 'solid' ); ?>>
					<?php esc_html_e( 'solid', 'formidable-pro' ); ?>
				</option>
				<option value="dotted" <?php selected( $style->post_content['section_border_style'], 'dotted' ); ?>>
					<?php esc_html_e( 'dotted', 'formidable-pro' ); ?>
				</option>
				<option value="dashed" <?php selected( $style->post_content['section_border_style'], 'dashed' ); ?>>
					<?php esc_html_e( 'dashed', 'formidable-pro' ); ?>
				</option>
				<option value="double" <?php selected( $style->post_content['section_border_style'], 'double' ); ?>>
					<?php esc_html_e( 'double', 'formidable-pro' ); ?>
				</option>
			</select>
		</p>

		<p class="frm4 frm_first frm_form_field">
			<label><?php esc_html_e( 'Border Position', 'formidable-pro' ); ?></label>
			<select name="<?php echo esc_attr( $frm_style->get_field_name('section_border_loc') ); ?>" id="frm_section_border_loc">
				<option value="-top" <?php selected( $style->post_content['section_border_loc'], '-top' ); ?>>
					<?php esc_html_e( 'top', 'formidable-pro' ); ?>
				</option>
				<option value="-bottom" <?php selected( $style->post_content['section_border_loc'], '-bottom' ); ?>>
					<?php esc_html_e( 'bottom', 'formidable-pro' ); ?>
				</option>
				<option value="-left" <?php selected( $style->post_content['section_border_loc'], '-left' ); ?>>
					<?php esc_html_e( 'left', 'formidable-pro' ); ?>
				</option>
				<option value="-right" <?php selected( $style->post_content['section_border_loc'], '-right' ); ?>>
					<?php esc_html_e( 'right', 'formidable-pro' ); ?>
				</option>
				<option value="" <?php selected( $style->post_content['section_border_loc'], '' ); ?>>
					<?php esc_html_e( 'all', 'formidable-pro' ); ?>
				</option>
			</select>
		</p>

		<h4 class="frm_clear">
			<span><?php esc_html_e( 'Collapse Icon', 'formidable-pro' ); ?></span>
		</h4>
		<div class="frm6 frm_first frm_form_field">
			<label class="frm_primary_label"><?php esc_html_e( 'Icons', 'formidable-pro' ); ?></label>
			<?php FrmStylesHelper::bs_icon_select( $style, $frm_style, 'arrow' ); ?>
		</div>

		<p class="frm6 frm_form_field">
			<label><?php esc_html_e( 'Icon Position', 'formidable-pro' ); ?></label>
			<select name="<?php echo esc_attr( $frm_style->get_field_name('collapse_pos') ); ?>" id="frm_collapse_pos">
				<option value="after" <?php selected( $style->post_content['collapse_pos'], 'after' ); ?>>
					<?php esc_html_e( 'After Heading', 'formidable-pro' ); ?>
				</option>
				<option value="before" <?php selected( $style->post_content['collapse_pos'], 'before' ); ?>>
					<?php esc_html_e( 'Before Heading', 'formidable-pro' ); ?>
				</option>
			</select>
		</p>
