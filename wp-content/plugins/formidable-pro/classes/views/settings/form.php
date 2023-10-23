<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

if ( ! is_callable( 'FrmSettingsController::message_settings' ) ) {
	// If these settings aren't included, don't lose them.
	?>
	<input type="hidden" name="frm_edit_msg" value="<?php echo esc_attr( $frmpro_settings->edit_msg ); ?>" />
	<input type="hidden" name="frm_update_value" value="<?php echo esc_attr( $frmpro_settings->update_value ); ?>" />
	<input type="hidden" name="frm_login_msg" value="<?php echo esc_attr( $frm_settings->login_msg ); ?>" />
	<input type="hidden" name="frm_already_submitted" value="<?php echo esc_attr( $frmpro_settings->already_submitted ); ?>" />
	<input type="hidden" name="frm_mu_menu" value="<?php echo esc_attr( $frm_settings->mu_menu ); ?>" />
	<input type="hidden" name="frm_menu_icon" value="<?php echo esc_attr( $frmpro_settings->menu_icon ); ?>" />
<?php } ?>

<p class="frm_grid_container">
	<label for="frm_date_format" class="frm4 frm_form_field">
		<?php esc_html_e( 'Date Format', 'formidable-pro' ); ?>
		<span class="frm_help frm_icon_font frm_tooltip_icon" title="<?php esc_attr_e( 'Change the format of the date used in the date field.', 'formidable-pro' ) ?>"></span>
	</label>
	<?php $formats = array_keys( FrmProAppHelper::display_to_datepicker_format() ); ?>
	<select id="frm_date_format" name="frm_date_format" class="frm8 frm_form_field">
		<?php foreach ( $formats as $f ) { ?>
			<option value="<?php echo esc_attr($f) ?>" <?php selected($frmpro_settings->date_format, $f); ?>>
				<?php echo esc_html( $f . ' &nbsp; &nbsp; ' . gmdate( $f ) ); ?>
			</option>
		<?php } ?>
	</select>
</p>

<?php if ( ! class_exists( 'FrmStrpLiteConnectHelper', false ) ) { ?>
	<?php
	/**
	 * Include the global currency setting for backward compatibility when Lite is not up to date.
	 * As of version x.x the global currency setting is now in Lite.
	 */
	?>
	<p class="frm_grid_container">
		<label for="frm_currency" class="frm4 frm_form_field frm_help" title="<?php esc_attr_e( 'Select the currency to be used by Formidable globally.', 'formidable-pro' ) ?>">
			<?php esc_html_e( 'Currency', 'formidable-pro' ); ?>
		</label>
		<select id="frm_currency" name="frm_currency" class="frm8 frm_form_field">
			<?php
				$c = empty( $frmpro_settings->currency ) ? 'USD' : strtoupper( $frmpro_settings->currency );
				foreach ( FrmProCurrencyHelper::get_currencies() as $code => $currency ) {
					?>
					<option value="<?php echo esc_attr( $code ); ?>"<?php selected( $c, strtoupper( $code ) ); ?>>
						<?php echo esc_html( $currency['name'] . ' (' . $code . ')' ); ?>
					</option>
			<?php } ?>
		</select>
	</p>
<?php } ?>
