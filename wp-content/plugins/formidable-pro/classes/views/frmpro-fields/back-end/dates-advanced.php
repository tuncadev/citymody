<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<tr>
	<td>
		<label for="field_options_locale_<?php echo absint( $field['id'] ); ?>">
			<?php esc_html_e( 'Calendar Localization', 'formidable-pro' ); ?>
		</label>
	</td>
	<td>
		<select name="field_options[locale_<?php echo absint( $field['id'] ); ?>]" id="field_options_locale_<?php echo absint( $field['id'] ); ?>" class="auto_width">
			<?php
			foreach ( $locales as $locale_key => $locale ) {
				$selected = ( isset( $field['locale'] ) && $field['locale'] == $locale_key ) ? ' selected="selected"' : '';
				?>
				<option value="<?php echo esc_attr( $locale_key ); ?>"<?php echo $selected; ?>>
					<?php echo esc_html( $locale ); ?>
				</option>
			<?php } ?>
		</select>
	</td>
</tr>

<?php if ( isset( $install_data ) && isset( $requires ) ) { ?>
<tr>
	<td>
		<?php esc_attr_e( 'Blackout Dates, Inline Datepicker, and Dynamic Start and End Dates', 'formidable-pro' ); ?>
	</td>
	<td>
		<a href="javascript:void(0)" class="frm_show_upgrade<?php echo esc_attr( $class ); ?>" data-upgrade="<?php esc_attr_e( 'Extra Datepicker options', 'formidable' ); ?>" data-medium="datepicker-options" data-oneclick="<?php echo esc_attr( $install_data ); ?>" data-requires="<?php echo esc_attr( $requires ); ?>">
			<?php FrmProAppHelper::icon_by_class( 'frm_icon_font frm_plus1_icon frm_add_tag frm_svg14' ); ?>
			<?php esc_html_e( 'More Datepicker Options', 'formidable' ); ?>
		</a>
	</td>
</tr>
<?php } ?>
