<?php
/**
 * Content limitation options
 *
 * @package FormidablePro
 *
 * @var array $field Field array.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

?>
<p class="frm6 frm_form_field">
	<label for="frm_pro_max_limit"><?php esc_html_e( 'Limit length', 'formidable-pro' ); ?></label>
	<input
		type="number"
		min="0"
		step="1"
		id="frm_pro_max_limit"
		name="field_options[max_limit_<?php echo esc_attr( $field['id'] ); ?>]"
		value="<?php echo intval( $field['max_limit'] ); ?>"
	/>
</p>

<p class="frm6 frm_form_field">
	<label for="frm_pro_max_limit_type">&nbsp;</label>
	<select id="frm_pro_max_limit_type" name="field_options[max_limit_type_<?php echo esc_attr( $field['id'] ); ?>]">
		<option value="char" <?php selected( $field['max_limit_type'], 'char' ); ?>><?php esc_html_e( 'Characters', 'formidable-pro' ); ?></option>
		<option value="word" <?php selected( $field['max_limit_type'], 'word' ); ?>><?php esc_html_e( 'Words', 'formidable-pro' ); ?></option>
	</select>
</p>
