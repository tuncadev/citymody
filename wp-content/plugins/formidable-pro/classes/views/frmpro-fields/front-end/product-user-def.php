<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

if ( empty( FrmField::get_option( $field, 'placeholder' ) ) ) {
	$placeholder = FrmFieldsController::get_default_value_from_name( $field );
	$field['placeholder'] = $placeholder ? $placeholder : FrmProCurrencyHelper::format_amount_for_currency( $this->get_field_column( 'form_id' ), 0 );
}

$value = trim( $field['value'] );
$price = $value ? $value : 0;
?>
<input type="text" name="<?php echo esc_attr( $field_name ); ?>" id="<?php echo esc_attr( $html_id ); ?>" value="<?php echo esc_attr( $value ); ?>" data-frmprice="<?php echo esc_attr( $price ); ?>" <?php do_action( 'frm_field_input_html', $field ); ?> />
