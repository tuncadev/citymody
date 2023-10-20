<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

/**
 * Show the radio field on the front-end.
 * Extra line breaks show as space on the front-end when
 * the form is double filtered and not minimized.
 *
 * @phpcs:disable Generic.WhiteSpace.ScopeIndent
 */

if ( empty( $field['options'] ) ) {
	return;
}

FrmAppHelper::include_svg();

$image_size  = empty( $field['image_size'] ) ? FrmProImages::get_default_size() : $field['image_size'];
$image_class = empty( $field['image_options'] ) ? '' : ' frm_image_option frm_image_' . $image_size;

foreach ( $field['options'] as $opt_key => $opt ) {
	if ( isset( $shortcode_atts ) && isset( $shortcode_atts['opt'] ) && ( $shortcode_atts['opt'] !== $opt_key ) ) {
		continue;
	}

	$return    = array( 'label' );
	$image     = FrmProImages::single_option_details( compact( 'opt', 'opt_key', 'field', 'return' ) );
	$field_val = FrmFieldsHelper::get_value_from_array( $opt, $opt_key, $field );
	$opt       = FrmFieldsHelper::get_label_from_array( $opt, $opt_key, $field );
	$checked   = FrmAppHelper::check_selected( $field['value'], $field_val ) ? ' checked="checked" ' : ' ';

	?>
	<div class="<?php echo esc_attr( apply_filters( 'frm_' . $field['type'] . '_class', 'frm_' . $field['type'], $field, $field_val ) ); ?> <?php echo esc_attr( $image_class ); ?>" id="<?php echo esc_attr( FrmFieldsHelper::get_checkbox_id( $field, $opt_key, $field['type'] ) ); ?>"><?php

	if ( ! isset( $shortcode_atts ) || ! isset( $shortcode_atts['label'] ) || $shortcode_atts['label'] ) {
		?><label for="<?php echo esc_attr( $html_id . '-' . $opt_key ); ?>"><?php
	}
	?>
	<input type="<?php echo esc_attr( $field['type'] ); ?>" name="<?php echo esc_attr( $field_name . ( $field['type'] === 'checkbox' ? '[]' : '' ) ); ?>" id="<?php echo esc_attr( $html_id . '-' . $opt_key ); ?>" value="<?php echo esc_attr( $field_val ); ?>"
	<?php
	echo $checked; // WPCS: XSS ok.
	do_action( 'frm_field_input_html', $field );
	?>/><?php

	if ( ! isset( $shortcode_atts ) || ! isset( $shortcode_atts['label'] ) || $shortcode_atts['label'] ) {
		echo ' ' . FrmAppHelper::kses( $image['label'], 'all' ) . '</label>'; // WPCS: XSS ok.
	}

	unset( $checked );

	?></div>
<?php
}
