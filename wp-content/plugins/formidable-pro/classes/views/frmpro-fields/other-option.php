<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<li id="frm_delete_field_<?php echo esc_attr( $field['id'] . '-' . $opt_key ); ?>_container" data-optkey="<?php echo esc_attr( $opt_key ); ?>" class="frm_single_option frm_other_option">
	<?php FrmProAppHelper::icon_by_class( 'frm_icon_font frm_drag_icon frm-drag' ); ?>

	<?php
	$default_type = isset( $default_type ) ? $default_type : $field['type'];

	if ( empty( $checked ) ) {
		// Get string for Other text field.
		$other_val = FrmFieldsHelper::get_other_val( compact( 'opt_key', 'field' ) );

		$checked = $other_val ? ' checked="checked"' : '';
	}
	?>
	<input type="<?php echo esc_attr( $default_type ); ?>" name="<?php echo esc_attr( $field_name ); ?>" <?php echo $checked; // WPCS: XSS ok. ?> value="<?php echo esc_attr( $field_val ); ?>" />


	<span class="frm-with-right-icon">
		<input type="text" name="field_options[options_<?php echo esc_attr( $field['id'] ); ?>][<?php echo esc_attr( $opt_key ); ?>]" value="<?php echo esc_attr( $opt ); ?>" class="field_<?php echo esc_attr( $field['id'] ); ?>_option <?php echo esc_attr( $field['separate_value'] ? 'frm_with_key' : '' ); ?>" id="<?php echo esc_attr( $html_id . '-' . $opt_key ); ?>" data-frmchange="updateDefault" />
		<?php
		FrmProAppHelper::icon_by_class(
			'frm_icon_font frm_pencil_icon',
			array(
				'title' => __( '"Other" option', 'formidable-pro' ),
			)
	 	);
		?>
	</span>

	<a href="javascript:void(0)" class="frm_icon_font frm_remove_tag" data-fid="<?php echo esc_attr( $field['id'] ); ?>" data-removeid="frm_delete_field_<?php echo esc_attr( $field['id'] . '-' . $opt_key ); ?>_container" data-removemore="#frm_<?php echo esc_attr( $default_type . '_' . $field['id'] . '-' . $opt_key ); ?>" data-showlast="#frm_add_opt_<?php echo esc_attr( $field['id'] ); ?>"></a>

</li>

<?php
unset( $field_val, $opt, $opt_key, $other_val );
