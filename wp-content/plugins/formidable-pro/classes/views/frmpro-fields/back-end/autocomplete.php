<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<p class="frm6 frm_form_field">
	<label id="for_field_options_autocomplete_<?php echo absint( $field['id'] ); ?>" for="field_options_autocomplete_<?php echo absint( $field['id'] ); ?>">
		<?php esc_html_e( 'Autocomplete', 'formidable-pro' ); ?>
		<span class="frm_help frm_icon_font frm_tooltip_icon frm_tooltip_expand" data-placement="right" title="<?php esc_attr_e( 'The autocomplete attribute asks the browser to attempt autocompletion, based on user history.', 'formidable-pro' ); ?>"></span>
	</label>

	<?php
    if ( empty( $field['autocomplete'] ) ) {
		$field['autocomplete'] = '';
	}
	?>

    <select name="field_options[autocomplete_<?php echo absint( $field['id'] ); ?>]" id="field_options_autocomplete_<?php echo absint( $field['id'] ); ?>">
		<option value="" <?php selected( $field['autocomplete'], '' ); ?>><?php esc_html_e( 'Please select', 'formidable-pro' ); ?></option>
        <?php
		$field_obj = FrmFieldFactory::get_field_type( $field['type'] );

		$autocomplete_options = $field_obj->autocomplete_options();

		/**
		 * Allows modifying the list of autocomplete attribute options.
		 *
		 * @since 5.4.1
		 *
		 * @param array $field The form field.
		 * @param array $autocomplete_options The list of autocomplete attribute options.
		 */
        $autocomplete_options = apply_filters( 'frm_autocomplete_options', $autocomplete_options, $field );
        foreach ( $autocomplete_options as $value => $label ) {
        ?>
        	<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $field['autocomplete'], $value ); ?>>
                <?php echo esc_html( $label ); ?>
            </option>
        <?php
        }
        ?>
	</select>
</p>
