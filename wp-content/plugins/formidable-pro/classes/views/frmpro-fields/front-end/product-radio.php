<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

/**
 * Show the product field as radio button or checkbox on the front-end.
 * Extra line breaks show as space on the front-end when
 * the form is double filtered and not minimized.
 *
 * @phpcs:disable Generic.WhiteSpace.ScopeIndent
 */

$display_type = $field['data_type'];
if ( $display_type !== 'radio' && $display_type !== 'checkbox' ) {
	return;
}

if ( $display_type === 'checkbox' ) {
	$field_name .= '[]';
}

foreach ( $field['options'] as $opt_key => $opt ) {
	if ( isset( $shortcode_atts ) && isset( $shortcode_atts['opt'] ) && ( $shortcode_atts['opt'] !== $opt_key ) ) {
		continue;
	}

	$field_val = FrmFieldsHelper::get_value_from_array( $opt, $opt_key, $field );
	$price     = FrmProFieldProduct::get_price_from_array( $opt, $opt_key, $field );
	$checked   = FrmAppHelper::check_selected( $field['value'], $field_val ) ? ' checked="checked" ' : ' ';
	$opt       = FrmFieldsHelper::get_label_from_array( $opt, $opt_key, $field );
	?>
	<div class="<?php echo esc_attr( apply_filters( 'frm_' . $display_type . '_class', 'frm_' . $display_type, $field, $field_val ) ); ?>" id="<?php echo esc_attr( FrmFieldsHelper::get_checkbox_id( $field, $opt_key, $display_type ) ); ?>"><?php

	if ( ! isset( $shortcode_atts ) || ! isset( $shortcode_atts['label'] ) || $shortcode_atts['label'] ) {
		?><label for="<?php echo esc_attr( $html_id ); ?>-<?php echo esc_attr( $opt_key ); ?>"><?php
	}
	?>
	<input type="<?php echo esc_attr( $display_type ); ?>" name="<?php echo esc_attr( $field_name ); ?>" id="<?php echo esc_attr( $html_id . '-' . $opt_key ); ?>" value="<?php echo esc_attr( $field_val ); ?>"<?php
	echo $checked . ' '; // WPCS: XSS ok.
	?> data-frmprice="<?php echo esc_attr( $price ); ?>" <?php do_action( 'frm_field_input_html', $field ); ?> /><?php

	if ( ! isset( $shortcode_atts ) || ! isset( $shortcode_atts['label'] ) || $shortcode_atts['label'] ) {
		echo ' ' . FrmAppHelper::kses( $opt, 'all' ) . '</label>'; // WPCS: XSS ok.
	}
	?></div>
<?php
}
