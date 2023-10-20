<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<p class="howto">
	<?php esc_html_e( 'Select the messages you would like to see in your Formidable inbox. Not all types of messages can be disabled.', 'formidable-pro' ); ?>
</p>
<input type="hidden" name="frm_inbox[set]" value="<?php echo esc_attr( FrmProDb::$plug_version ); ?>" />
<div class="frm_grid_container">
<?php foreach ( $message_types as $type => $label ) { ?>
	<div class="frm_form_field">
	<?php
	$input_html = array();
	if ( $has_access ) {
		$checked = ! empty( $settings->inbox[ $type ] );
	} else {
		$checked    = true;
		$input_html['disabled'] = 'disabled';
	}
	FrmProHtmlHelper::admin_toggle(
		'frm_inbox_' . $type,
		'frm_inbox[' . esc_attr( $type ) . ']',
		array(
			'checked'     => $checked,
			'echo'        => true,
			'value'       => 1,
			'on_label'    => $label,
			'show_labels' => true,
			'input_html'  => $input_html,
		)
	);
	?>
</div>
<?php } ?>
</div>
