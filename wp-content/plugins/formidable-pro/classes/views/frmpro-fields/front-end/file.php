<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

$is_multiple = FrmField::is_option_true( $field, 'multiple' );
$is_required = FrmField::is_required( $field );
$media_ids   = $field['value'];
FrmProAppHelper::unserialize_or_decode( $media_ids );
if ( $is_multiple ) {
	$media_ids = array_map( 'trim', array_filter( (array) $media_ids, 'is_numeric' ) );
} elseif ( is_array( $media_ids ) ) {
	$media_ids = reset( $media_ids );
}
$field['value'] = $media_ids;

$input_name = $field_name . ( $is_multiple ? '[]' : '' );

if ( FrmField::is_read_only( $field ) ) {
	// Read only file upload field shows the entry without an upload button
	foreach ( (array) $media_ids as $media_id ) {
?>
<input type="hidden" value="<?php echo esc_attr( $media_id ); ?>" name="<?php echo esc_attr( $input_name ); ?>" />
<div class="frm_show_file_icon"><?php echo FrmProFieldsHelper::get_file_icon( $media_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
<?php
	}
} else {
    FrmProFileField::setup_dropzone( $field, compact( 'field_name', 'html_id', 'file_name' ) );

	$extra_atts   = '';
	$required_att = '';
	$hidden_value = $media_ids;

	if ( $is_multiple ) {
		$hidden_value = '';
		$extra_atts   = ' data-frmfile="' . esc_attr( $field['id'] ) . '" multiple="multiple" ';
	}

	if ( $is_required ) {
		$required_message = FrmFieldsHelper::get_error_msg( $field, 'blank' );
		$required_att     = ' data-reqmsg="' . esc_attr( $required_message ) . '" ';
	}

	global $frm_vars;
	$file_settings   = $frm_vars['dropzone_loaded'][ $file_name ];
	$file_size_range = $this->get_file_size_range( $file_settings['minFilesize'], $file_settings['maxFilesize'] );
?>
<input type="hidden" name="<?php echo esc_attr( $input_name ); ?>" <?php echo $required_att; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> value="<?php echo esc_attr( $hidden_value ); ?>" data-frmfile="<?php echo esc_attr( $field['id'] ); ?>" />

<div class="frm_dropzone frm_<?php echo esc_attr( $file_settings['maxFiles'] == 1 ? 'single' : 'multi' ); ?>_upload frm_clearfix" id="<?php echo esc_attr( $file_name ); ?>_dropzone" role="group" <?php echo strip_tags( $aria ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class="fallback">
		<input type="file" name="<?php echo esc_attr( $file_name . ( $is_multiple ? '[]' : '' ) ); ?>" id="<?php echo esc_attr( $html_id ); ?>"
			<?php
			echo $extra_atts; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			do_action( 'frm_field_input_html', $field );
			?>
			/>
		<?php foreach ( $file_settings['mockFiles'] as $file ) { ?>
			<div class="dz-preview dz-complete dz-image-preview frm_clearfix">
				<div class="dz-image">
					<?php $src = FrmProFileField::get_safe_file_icon( $file ); ?>
					<img src="<?php echo esc_attr( $src ); ?>" alt="<?php echo esc_attr( $file['name'] ); ?>" />
				</div>
				<div class="dz-column">
					<div class="dz-details">
						<div class="dz-filename">
							<span data-dz-name="">
								<?php if ( ! empty( $file['accessible'] ) ) { ?>
									<a href="<?php echo esc_attr( $file['file_url'] ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $file['name'] ); ?></a>
								<?php } else { ?>
									<?php echo esc_html( $file['name'] ); ?>
								<?php } ?>
							</span>
						</div>
						<a class="dz-remove frm_remove_link" href="javascript:undefined;" data-frm-remove="<?php echo esc_attr( $field_name ); ?>" title="<?php esc_attr_e( 'Remove file', 'formidable-pro' ); ?>">
							<?php FrmProAppHelper::get_svg_icon( 'frm-cancel1-icon', 'frmsvg frm-svg-icon', array( 'echo' => true ) ); ?>
						</a>
						<?php if ( $is_multiple ) { ?>
							<input type="hidden" name="<?php echo esc_attr( $field_name ); ?>[]" value="<?php echo esc_attr( $file['id'] ); ?>" />
						<?php } ?>
					</div>
				</div>
			</div>
		<?php } ?>
		<div class="frm_clearfix <?php echo is_admin() ? 'clear' : ''; ?>"></div>
	</div>
	<div class="dz-message needsclick">
		<?php FrmProAppHelper::get_svg_icon( 'frm-upload-icon', 'frmsvg frm-svg-icon', array( 'echo' => true ) ); ?>
		<span class="frm_upload_text"><button type="button"><?php echo esc_html( $field['drop_msg'] ); ?></button></span>
		<span class="frm_compact_text"><button type="button"><?php echo esc_html( $field['choose_msg'] ); ?></button></span>
		<div class="frm_small_text">
			<p><?php echo esc_html( $this->get_range_string( $file_size_range ) ); ?></p>
		</div>
	</div>
</div>
<?php
}
