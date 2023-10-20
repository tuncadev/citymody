<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<div class="show_csv hide-if-js">
	<p>
		<label class="frm_left_label"><?php esc_html_e( 'CSV Delimiter', 'formidable-pro' ); ?></label>
		<input type="text" name="csv_del" value="<?php echo esc_attr( $csv_del ); ?>" />
	</p>

	<p>
		<label class="frm_left_label"><?php esc_html_e( 'Import Into Form', 'formidable-pro' ); ?></label>
		<select name="form_id">
		<?php
		foreach ( $forms as $form ) {
			if ( $form->is_template || $form->parent_form_id ) {
				continue;
			}
			?>
			<option value="<?php echo (int) $form->id; ?>"><?php echo $form->name == '' ? esc_html__( '(no title)' ) : esc_html( $form->name ); ?></option>
			<?php
		}
		?>
		</select>
	</p>

	<p class="howto"><?php esc_html_e( 'Note: Only entries can by imported via CSV.', 'formidable-pro' ); ?></p>
</div>
