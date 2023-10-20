<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>

<div class="<?php echo esc_attr( $field['calc_type'] === 'text' ? 'frm_hidden' : '' ); ?>">
	<p class="frm_form_field">
		<label class="frm_primary_label">
			<input type="checkbox" value="1" name="field_options[is_currency_<?php echo esc_attr( $field['id'] ); ?>]" <?php checked( $field['is_currency'], 1 ); ?> />
			<?php
			printf(
				/* translators: %s item that will be formatted as currency (calculation, number). */
				esc_html__( 'Format %s as currency', 'formidable-pro' ),
				isset( $type ) ? esc_html( $type ) : esc_html__( 'calculation', 'formidable-pro' )
			);
			?>
		</label>
	</p>

	<p class="frm_form_field <?php echo esc_attr( empty( $field['is_currency'] ) ? 'frm_hidden' : '' ); ?>">
		<label class="frm_primary_label">
			<input type="checkbox" value="1" name="field_options[custom_currency_<?php echo esc_attr( $field['id'] ); ?>]" <?php checked( isset( $field['custom_currency'] ) ? $field['custom_currency'] : 0, 1 ); ?> />
			<?php esc_html_e( 'Use custom currency format', 'formidable-pro' ); ?>
		</label>
	</p>

	<div class="<?php echo esc_attr( empty( $field['custom_currency'] ) ? 'frm_hidden' : '' ); ?> frm_grid_container frm_custom_currency_options_wrapper">

		<p class="frm_form_field frm6">
			<label class="frm_primary_label">
				<input type="text" value="<?php echo isset( $field['custom_thousand_separator'] ) ? esc_attr( $field['custom_thousand_separator'] ) : ''; ?>" name="field_options[custom_thousand_separator_<?php echo esc_attr( $field['id'] ); ?>]" />
				<?php esc_html_e( 'Thousand separator', 'formidable-pro' ); ?>
			</label>
		</p>

		<p class="frm_form_field frm6">
			<label class="frm_primary_label">
				<input type="text" value="<?php echo isset( $field['custom_decimal_separator'] ) ? esc_attr( $field['custom_decimal_separator'] ) : ''; ?>" name="field_options[custom_decimal_separator_<?php echo esc_attr( $field['id'] ); ?>]" />
				<?php esc_html_e( 'Decimal separator', 'formidable-pro' ); ?>
			</label>
		</p>

		<p class="frm_form_field frm4">
			<label class="frm_primary_label">
				<select name="field_options[custom_decimals_<?php echo esc_attr( $field['id'] ); ?>]">
					<option value="0" <?php selected( isset( $field['custom_decimals'] ) ? $field['custom_decimals'] : 0, 0 ); ?>>0</option>
					<option value="2" <?php selected( isset( $field['custom_decimals'] ) ? $field['custom_decimals'] : 0, 2 ); ?>>2</option>
				</select>

				<?php esc_html_e( 'Decimals', 'formidable-pro' ); ?>
			</label>
		</p>

		<p class="frm_form_field frm4">
			<label class="frm_primary_label">
				<input type="text" value="<?php echo isset( $field['custom_symbol_left'] ) ? esc_attr( $field['custom_symbol_left'] ) : ''; ?>" name="field_options[custom_symbol_left_<?php echo esc_attr( $field['id'] ); ?>]" />
				<?php esc_html_e( 'Left symbol', 'formidable-pro' ); ?>
			</label>
		</p>

		<p class="frm_form_field frm4">
			<label class="frm_primary_label">
				<input type="text" value="<?php echo isset( $field['custom_symbol_right'] ) ? esc_attr( $field['custom_symbol_right'] ) : ''; ?>" name="field_options[custom_symbol_right_<?php echo esc_attr( $field['id'] ); ?>]" />
				<?php esc_html_e( 'Right symbol', 'formidable-pro' ); ?>
			</label>
		</p>
	</div>
</div>
