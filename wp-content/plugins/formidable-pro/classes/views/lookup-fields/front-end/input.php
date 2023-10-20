<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

// If read-only, add hidden fields to hold the values
if ( $disabled && $field['data_type'] !== 'text' ) {
	foreach ( $saved_value_array as $v ) { ?>
<input name="<?php echo esc_attr( $field_name ); ?>" type="hidden" value="<?php echo esc_attr( $v ); ?>" <?php do_action('frm_field_input_html', $field); ?> />
<?php
	}
}

// Lookup Field Dropdown
if ( 'select' === $field['data_type'] ) {

	// If there are field options, show them in a dropdown
	if ( ! empty( $field['options'] ) ) {
		?>
<select <?php echo $disabled; ?> name="<?php echo esc_attr( $field_name ); ?>" id="<?php echo esc_attr( $html_id ); ?>" <?php do_action('frm_field_input_html', $field); ?>>
<?php
		$placeholder = FrmField::get_option( $field, 'placeholder' );
		foreach ( $field['options'] as $opt ) {
			$is_placeholder = ( $opt == $placeholder );
			if ( $is_placeholder && ( $field['multiple'] || $field['autocom'] ) ) {
				$opt = '';
			}

			$opt_value = $is_placeholder ? '' : $opt;
			$selected  = ( in_array( $opt_value, $saved_value_array ) && $opt_value !== '' ) ? ' selected="selected"' : '';
			?>
<option value="<?php echo esc_attr( $opt_value ); ?>"<?php echo $selected; ?>><?php
	echo ( $opt == '' ) ? ' ' : esc_html( $opt );
?></option>
<?php
		}
?>
</select>
<?php
    }
} elseif ( 'radio' === $field['data_type'] ) {
	// Radio Button Lookup Field

	if ( ! empty( $field['options'] ) ) {
		// If there are field options, show them in a radio button field

		?><div class="frm_opt_container"><?php
		require FrmProAppHelper::plugin_path() . '/classes/views/lookup-fields/front-end/radio-rows.php';
		?></div><?php
    }
} elseif ( 'checkbox' === $field['data_type'] ) {
	// Checkbox Lookup Field

	if ( ! empty( $field['options'] ) ) {

		?><div class="frm_opt_container"><?php
		require FrmProAppHelper::plugin_path() . '/classes/views/lookup-fields/front-end/checkbox-rows.php';
		?></div><?php
	}
} elseif ( 'text' === $field['data_type'] ) {
	// Text Lookup Field

	$value = is_array( $field['value'] ) ? reset( $field['value'] ) : $field['value'];
	?><input type="text" id="<?php echo esc_attr( $html_id ); ?>" name="<?php echo esc_attr( $field_name ); ?>" value="<?php echo esc_attr( $value ); ?>" <?php do_action('frm_field_input_html', $field); ?><?php echo $disabled; ?>/><?php
} elseif ( $field['data_type'] === 'data' && ! empty( $field['watch_lookup'] ) && is_numeric( $field['get_values_field'] ) ) {
	$value = implode( ', ', $saved_value_array );
	?>
	<p>
		<?php echo wp_kses_post( $value ); ?>
	</p>
	<input type="hidden" value="<?php echo esc_attr( $value ); ?>" name="<?php echo esc_attr( $field_name ); ?>" id="<?php echo esc_attr( $html_id ); ?>" />
	<?php
}
