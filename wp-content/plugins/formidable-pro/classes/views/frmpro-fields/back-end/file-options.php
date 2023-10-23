<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<h3>
	<?php esc_html_e( 'File Upload Options', 'formidable-pro' ); ?>
	<?php FrmAppHelper::icon_by_class( 'frmfont frm_arrowdown6_icon', array( 'aria-hidden' => 'true' ) ); ?>
</h3>
<div class="frm_grid_container frm-collapse-me" role="group">
	<?php if ( ! empty( $public_files_tooltip ) && ! empty( $settings_url ) ) { ?>
		<div class="frm_warning_style frm-mt-0" style="padding: 10px;">
			<?php
			FrmAppHelper::icon_by_class( 'frmfont frm_alert_icon' );
			echo '&nbsp';

			printf(
				'%1$s %2$s.',
				esc_html__( 'Uploads are public. File access can be updated in ', 'formidable-pro' ),
				'<a href="' . esc_attr( $settings_url ) . '">' . esc_html__( 'Form Permissions Settings', 'formidable-pro' ) . '</a>'
			);
			?>
			<span class="frm_help frm_icon_font frm_tooltip_icon" title="<?php echo esc_attr( $public_files_tooltip ); ?>"></span>
		</div>
	<?php } ?>
	<p>
		<label for="multiple_<?php echo esc_attr( $field['id'] ); ?>">
			<input type="checkbox" name="field_options[multiple_<?php echo esc_attr( $field['id'] ); ?>]" id="multiple_<?php echo esc_attr( $field['id'] ); ?>" value="1" <?php echo checked( $field['multiple'], 1 ); ?> onchange="frm_show_div('limit_file_count_cont_<?php echo absint( $field['id'] ); ?>',this.checked,true,'#')" />
			<?php esc_html_e( 'Allow multiple files to be uploaded', 'formidable-pro' ); ?>
		</label>
	</p>
	<p>
		<label for="attach_<?php echo esc_attr( $field['id'] ); ?>">
			<input type="checkbox" id="attach_<?php echo esc_attr( $field['id'] ); ?>" name="field_options[attach_<?php echo esc_attr( $field['id'] ); ?>]" value="1" <?php echo ( isset( $field['attach'] ) && $field['attach'] ) ? 'checked="checked"' : ''; ?> />
			<?php esc_html_e( 'Attach this file to the email notification', 'formidable-pro' ); ?>
		</label>
	</p>
	<p>
		<label for="delete_<?php echo esc_attr( $field['id'] ); ?>">
			<input type="checkbox" name="field_options[delete_<?php echo esc_attr( $field['id'] ); ?>]" id="delete_<?php echo esc_attr( $field['id'] ); ?>" value="1" <?php echo ( isset( $field['delete'] ) && $field['delete'] ) ? 'checked="checked"' : ''; ?> />
			<?php esc_html_e( 'Permanently delete old files when replaced or when the entry is deleted', 'formidable-pro' ); ?>
		</label>
	</p>
	<p>
		<label>
			<input type="checkbox" id="resize_<?php echo esc_attr( $field['id'] ); ?>" name="field_options[resize_<?php echo esc_attr( $field['id'] ); ?>]" value="1" onchange="frm_show_div('resize_file_<?php echo absint( $field['id'] ); ?>',this.checked,1,'.')" <?php checked( $field['resize'], 1 ); ?> />
			<?php esc_html_e( 'Automatically resize files before upload', 'formidable-pro' ); ?>
			<span class="frm_help frm_icon_font frm_tooltip_icon" title="<?php esc_attr_e( 'When a large image is uploaded, resize it before you save it to your site.', 'formidable-pro' ); ?>"></span>
		</label>
	</p>
	<p class="resize_file_<?php echo esc_attr( $field['id'] ); ?> <?php echo esc_attr( $field['resize'] == 1 ? '' : 'frm_hidden' ); ?>">
		<label><?php esc_html_e( 'New file size', 'formidable-pro' ); ?></label>

		<label id="new_size_<?php echo esc_attr( $field['id'] ); ?>">
			<span class="frm_screen_reader"><?php esc_html_e( 'The size the image should be resized to', 'formidable-pro' ); ?></span>
			<input type="text" id="new_size_<?php echo esc_attr( $field['id'] ); ?>" name="field_options[new_size_<?php echo esc_attr( $field['id'] ); ?>]" value="<?php echo esc_attr( absint( $field['new_size'] ) ); ?>" size="5" />
			<span class="howto"><?php esc_html_e( 'px', 'formidable-pro' ); ?></span>
		</label>

		<label id="resize_dir_<?php echo esc_attr( $field['id'] ); ?>">
			<span class="frm_screen_reader"><?php esc_html_e( 'Resize the image by width or height', 'formidable-pro' ); ?></span>
			<select name="field_options[resize_dir_<?php echo esc_attr( $field['id'] ); ?>]">
				<option value="width" <?php selected( $field['resize_dir'], 'width' ); ?>>
					<?php esc_html_e( 'wide', 'formidable-pro' ); ?>
				</option>
				<option value="height" <?php selected( $field['resize_dir'], 'height' ); ?>>
					<?php esc_html_e( 'high', 'formidable-pro' ); ?>
				</option>
			</select>
		</label>
	</p>
	<?php if ( $mimes ) { ?>
	<h4><?php esc_html_e( 'Allowed file types', 'formidable-pro' ); ?></h4>
	<p class="frm6 frm_form_field">
		<label for="restrict_<?php echo esc_html( $field['id'] ); ?>_0">
			<input type="radio" name="field_options[restrict_<?php echo esc_html( $field['id'] ); ?>]" id="restrict_<?php echo esc_html( $field['id'] ); ?>_0" value="0" <?php FrmAppHelper::checked( $field['restrict'], 0 ); ?> onclick="frm_show_div('restrict_box_<?php echo absint( $field['id'] ); ?>',0,1,'.')" />
			<?php esc_html_e( 'Allow all file types', 'formidable-pro' ); ?>
		</label>
	</p>
	<p class="frm6 frm_form_field">
		<label for="restrict_<?php echo esc_html( $field['id'] ); ?>_1">
			<input type="radio" name="field_options[restrict_<?php echo esc_html( $field['id'] ); ?>]" id="restrict_<?php echo esc_html( $field['id'] ); ?>_1" value="1" <?php FrmAppHelper::checked( $field['restrict'], 1 ); ?> onclick="frm_show_div('restrict_box_<?php echo absint( $field['id'] ); ?>',1,1,'.')" />
			<?php esc_html_e( 'Specify allowed types', 'formidable-pro' ); ?>
		</label>
	</p>
	<p class="frm_form_field restrict_box_<?php echo absint( $field['id'] ); ?> <?php echo ( $field['restrict'] == 1 ? '' : 'frm_hidden' ); ?>">
		<select name="field_options[ftypes_<?php echo esc_attr( $field['id'] ); ?>][]" multiple="multiple" class="frm_multiselect">
			<?php foreach ( $mimes as $ext_preg => $mime ) { ?>
				<option value="<?php echo esc_attr( $ext_preg . '|||' . $mime ); ?>" <?php echo isset( $field['ftypes'][ $ext_preg ] ) ? ' selected="selected"' : ''; ?>>
					<?php echo esc_html( str_replace( '|', ', ', $ext_preg ) ); ?>
				</option>
			<?php } ?>
		</select>
	</p>
	<?php } ?>

	<h4><?php esc_html_e( 'File size limits', 'formidable-pro' ); ?></h4>
	<p class="frm6 frm_form_field">
		<label for="min_size_<?php echo esc_attr( $field['id'] ); ?>">
			<?php esc_html_e( 'Min file size (MB)', 'formidable-pro' ); ?>
			<span class="frm_help frm_icon_font frm_tooltip_icon" title="<?php echo esc_attr( __( 'Set the minimum file size limit for each file uploaded.', 'formidable-pro' ) ); ?>"></span>
		</label>
		<input type="text" id="min_size_<?php echo esc_attr( $field['id'] ); ?>" name="field_options[min_size_<?php echo esc_attr( $field['id'] ); ?>]" value="<?php echo esc_attr( $field['min_size'] ); ?>" size="5" />
	</p>
	<p class="frm6 frm_form_field">
		<label for="size_<?php echo esc_attr( $field['id'] ); ?>">
			<?php esc_html_e( 'Max file size (MB)', 'formidable-pro' ); ?>
			<span class="frm_help frm_icon_font frm_tooltip_icon" title="<?php echo esc_attr( sprintf( __( 'Set the file size limit for each file uploaded. Your server settings allow a maximum of %d MB.', 'formidable-pro' ), FrmProFileField::get_max_file_size() ) ); ?>"></span>
		</label>
		<input type="text" id="size_<?php echo esc_attr( $field['id'] ); ?>" name="field_options[size_<?php echo esc_attr( $field['id'] ); ?>]" value="<?php echo esc_attr( $field['size'] ); ?>" size="5" />
	</p>

	<p class="frm6 frm_form_field <?php echo esc_attr( $field['multiple'] == 1 ? '' : 'frm_hidden' ); ?>" id="limit_file_count_cont_<?php echo esc_attr( $field['id'] ); ?>">
		<label for="max_<?php echo esc_attr( $field['id'] ); ?>" id="limit_file_count_<?php echo esc_attr( $field['id'] ); ?>">
			<?php esc_html_e( 'Max files per entry', 'formidable-pro' ); ?>
		</label>
		<input type="text" id="max_<?php echo esc_attr( $field['id'] ); ?>" name="field_options[max_<?php echo esc_attr( $field['id'] ); ?>]" value="<?php echo esc_attr( $field['max'] ); ?>" size="5" />
	</p>
	<p>
		<label for="drop_msg_<?php echo esc_attr( $field['id'] ); ?>">
			<?php esc_html_e( 'Upload text', 'formidable-pro' ); ?>
		</label>
		<input type="text" id="drop_msg_<?php echo esc_attr( $field['id'] ); ?>" class="frm_long_input" name="field_options[drop_msg_<?php echo esc_attr( $field['id'] ); ?>]" value="<?php echo esc_attr( $field['drop_msg'] ); ?>" />
	</p>
	<p>
		<label for="choose_msg_<?php echo esc_attr( $field['id'] ); ?>">
			<?php esc_html_e( 'Compact upload text', 'formidable-pro' ); ?>
			<span class="frm_help frm_icon_font frm_tooltip_icon" title="<?php esc_attr_e( 'The label shown when the file upload field is compacted with the frm_compact CSS layout class.', 'formidable-pro' ); ?>"></span>
		</label>
		<input type="text" id="choose_msg_<?php echo esc_attr( $field['id'] ); ?>" class="frm_long_input" name="field_options[choose_msg_<?php echo esc_attr( $field['id'] ); ?>]" value="<?php echo esc_attr( $field['choose_msg'] ); ?>" />
	</p>
</div>
