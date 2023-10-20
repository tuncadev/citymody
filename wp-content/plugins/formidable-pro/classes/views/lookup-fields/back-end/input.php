<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

if ( empty( $field['get_values_field'] ) ) {
	?>
	<span class="frm-with-left-icon frm-not-set" id="setup-message-<?php echo esc_attr( $field['id'] ); ?>">
		<?php FrmProAppHelper::icon_by_class( 'frmfont frm_report_problem_solid_icon' ); ?>
		<input type="text" value="<?php esc_attr_e( 'This field is not set up yet.', 'formidable-pro' ); ?>" disabled />
	</span>
	<input type="hidden" name="<?php echo esc_attr( $field_name ); ?>" value="" />
	<?php
	return;
}

// Lookup Field Dropdown
if ( 'select' == $field['data_type'] ) {
?>
<select name="<?php echo esc_attr( $field_name ); ?>" id="<?php echo esc_attr( $html_id ); ?>"
	<?php echo FrmField::is_multiple_select( $field ) ? 'multiple="multiple" ' : ''; ?>>
<?php
	foreach ( $field['options'] as $opt ) {
		$opt_value = ( $opt == $field['placeholder'] ) ? '' : $opt;
		$selected = ( in_array( $opt_value, $saved_value_array ) && $opt_value !== '' ) ? ' selected="selected"' : '';
?><option value="<?php echo esc_attr( $opt_value ); ?>"<?php echo $selected; ?>><?php
	echo ( $opt == '' ) ? ' ' : esc_html( $opt );
?></option>
<?php
	}
?>
</select>

<?php

} else if ( 'radio' == $field['data_type'] || 'checkbox' == $field['data_type'] ) {
	// Checkbox and Radio Lookup Fields

	if ( empty( $field['options'] ) ) {
		?>
		<span id="setup-message-<?php echo esc_attr( $field['id'] ); ?>">
			<input type="text" value="<?php esc_attr_e( 'No options found', 'formidable-pro' ); ?>" disabled />
		</span>
		<?php
	} else if ( count( $field['options'] ) == 1 && reset( $field['options'] ) == '' ) {
		?>
		<span id="setup-message-<?php echo esc_attr( $field['id'] ); ?>">
			<input type="text" value="<?php esc_attr_e( 'This field content is dynamic', 'formidable-pro' ); ?>" disabled />
		</span>
		<?php
	} else {
		?>
		<ul id="frm_field_<?php echo esc_attr( $field['id'] ); ?>_opts"
			class="frm_sortable_field_opts frm_clear<?php echo ( count( $field['options'] ) > 10 ) ? ' frm_field_opts_list' : ''; ?>"><?php
		foreach ( $field['options'] as $opt_key => $opt_value ) {
			$checked = ( in_array( $opt_value, $saved_value_array ) ) ? ' checked="checked"' : '';

			?>
			<li class="frm_single_option">
			<input type="<?php echo esc_attr( $field['data_type'] ); ?>" name="<?php echo esc_attr( $field_name ) ?>"
				   value="<?php echo esc_attr( $opt_value ) ?>"<?php echo $checked ?>/>
			<label class="frm_ipe_field_option field_<?php echo esc_attr( $field['id'] ) ?>_option"
				   id="<?php echo esc_attr( $html_id . '-' . $opt_key ) ?>"><?php echo FrmAppHelper::kses( $opt_value, 'all' ); // WPCS: XSS ok. ?></label>
			</li><?php
		}
		unset( $opt_key, $checked, $opt_value );
		?>
		</ul><?php
	}
} else if ( 'text' == $field['data_type'] ) {
	// Text Lookup Field

	?><input type="text" id="<?php echo esc_attr( $html_id ) ?>" name="<?php echo esc_attr( $field_name ) ?>" value="<?php echo esc_attr( $field['default_value'] ) ?>"<?php echo $width_string ?> class="dyn_default_value" /><?php
} elseif ( 'data' === $field['data_type'] ) {
	?>
	<span id="setup-message-<?php echo esc_attr( $field['id'] ); ?>">
		<input type="text" value="<?php esc_attr_e( 'This field content is dynamic', 'formidable-pro' ); ?>" disabled />
	</span>
	<?php
}
