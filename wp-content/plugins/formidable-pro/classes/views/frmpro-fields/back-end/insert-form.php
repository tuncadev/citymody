<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<p>
	<label><?php esc_html_e( 'Insert Form', 'formidable-pro' ); ?></label>
	<?php
	FrmFormsHelper::forms_dropdown( 'field_options[form_select_' . $field['id'] . ']', $field['form_select'], array(
		'exclude' => $field['form_id'],
	) );
	?>
</p>
