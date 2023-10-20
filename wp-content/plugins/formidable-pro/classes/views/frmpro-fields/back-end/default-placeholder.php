<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<label id="<?php echo esc_attr( $name . '_' . $field['id'] ); ?>" class="frm_primary_label">
	<?php echo esc_html( $field_label ); ?>
</label>

<?php

if ( isset( $default_value ) && is_array( $default_value ) ) {
	?>
	<p class="frm6 frm_form_field">
		<span class="frm-with-right-icon">
			<?php
			FrmProAppHelper::icon_by_class(
				'frm_icon_font frm_more_horiz_solid_icon frm-show-inline-modal',
				array(
					'data-open' => 'frm-smart-values-box',
				)
		 	);
			?>
			<input type="text" name="default_value_<?php echo esc_attr( $field['id'] ); ?>[<?php echo esc_attr( $name ); ?>]" id="default_value_<?php echo esc_attr( $name . '_' . $field['id'] ); ?>" value="<?php echo esc_attr( isset( $default_value[ $name ] ) ? $default_value[ $name ] : '' ); ?>" aria-labelledby="<?php echo esc_attr( $name . '_' . $field['id'] ); ?> label_default_<?php echo esc_attr( $name . '_' . $field['id'] ); ?>" data-changeme="field_<?php echo esc_attr( $field['field_key'] . '_' . $name ); ?>" data-changeatt="value" />
		</span>
		<span class="frm_description" id="label_default_<?php echo esc_attr( $name . '_' . $field['id'] ); ?>">
			<?php esc_html_e( 'Default Value', 'formidable-pro' ); ?>
		</span>
	</p>
	<?php
}

$sub     = 'placeholder';
$label   = __( 'Placeholder Text', 'formidable-pro' );
$subname = $name . '_' . $sub;
?>
<p class="frm6 frm_form_field">
	<input type="text" name="field_options[<?php echo esc_attr( $sub . '_' . $field['id'] ); ?>][<?php echo esc_attr( $name ); ?>]" id="field_options_<?php echo esc_attr( $subname . '_' . $field['id'] ); ?>" value="<?php echo esc_attr( isset( $field[ $sub ][ $name ] ) ? $field[ $sub ][ $name ] : '' ); ?>" aria-labelledby="<?php echo esc_attr( $name . '_' . $field['id'] ); ?> label_<?php echo esc_attr( $subname . '_' . $field['id'] ); ?>" data-changeme="field_<?php echo esc_attr( $field['field_key'] . '_' . $name ); ?>" data-changeatt="placeholder" />
	<span class="frm_description" id="label_<?php echo esc_attr( $subname . '_' . $field['id'] ); ?>">
		<?php echo esc_html( $label ); ?>
	</span>
</p>

<?php
$sub     = 'desc';
$label   = __( 'Description', 'formidable-pro' );
$subname = $name . '_' . $sub;
?>
<p class="frm6 frm_form_field">
	<input type="text" name="field_options[<?php echo esc_attr( $subname . '_' . $field['id'] ); ?>]" id="field_options_<?php echo esc_attr( $subname . '_' . $field['id'] ); ?>" value="<?php echo esc_attr( isset( $field[ $subname ] ) ? $field[ $subname ] : '' ); ?>" aria-labelledby="<?php echo esc_attr( $name . '_' . $field['id'] ); ?> label_<?php echo esc_attr( $subname . '_' . $field['id'] ); ?>" data-changeme="field_<?php echo esc_attr( $subname . '_' . $field['id'] ); ?>" />
	<span class="frm_description" id="label_<?php echo esc_attr( $subname . '_' . $field['id'] ); ?>">
		<?php echo esc_html( $label ); ?>
	</span>
</p>
