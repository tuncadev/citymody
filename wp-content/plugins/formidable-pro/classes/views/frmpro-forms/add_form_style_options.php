<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<tr>
    <td>
		<label><?php esc_html_e( 'Page Turn Transitions', 'formidable-pro' ); ?></label>
    </td>
    <td>
        <select name="options[transition]">
			<option value=""><?php esc_html_e( 'None', 'formidable-pro' ); ?></option>
			<option value="slidein" <?php selected( $values['transition'], 'slidein' ); ?>>
				<?php esc_html_e( 'Slide horizontally', 'formidable-pro' ); ?>
			</option>
			<option value="slideup" <?php selected( $values['transition'], 'slideup' ); ?>>
				<?php esc_html_e( 'Slide vertically', 'formidable-pro' ); ?>
			</option>
        </select>
    </td>
</tr>
