<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<p class="frm6">
	<label for="<?php echo esc_attr( $action_control->get_field_id( 'billing_address' ) ); ?>">
		<?php esc_html_e( 'Address', 'formidable-pro' ); ?>
	</label>
	<?php
	$action_control->show_fields_dropdown(
		$field_dropdown_atts,
		array(
			'name'           => 'billing_address',
			'allowed_fields' => 'address',
		)
	);
	?>
</p>
