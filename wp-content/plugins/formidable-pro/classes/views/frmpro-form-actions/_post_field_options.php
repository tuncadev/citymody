<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

if ( empty( $values['fields'] ) ) {
	return;
}

foreach ( $values['fields'] as $fo_key => $fo ) {

	if ( isset( $post_field ) && ! in_array( $fo['type'], $post_field, true ) ) {
		continue;
	}

	if ( isset( $post_field_only_if_selected ) && in_array( $fo['type'], $post_field_only_if_selected, true ) && $form_action->post_content[ $post_key ] != $fo['id'] ) {
		continue;
	}

	// don't include repeatable fields
	if ( FrmField::is_no_save_field( $fo['type'] ) || $fo['type'] === 'form' || $fo['form_id'] != $values['id'] ) {
		continue;
	}

	if ( $fo['post_field'] === $post_key ) {
		$values[ $post_key ] = $fo['id'];
	}
	?>
	<option value="<?php echo esc_attr( $fo['id'] ); ?>" <?php selected( $form_action->post_content[ $post_key ], $fo['id'] ); ?>>
		<?php echo esc_html( FrmAppHelper::truncate( $fo['name'], 50 ) ); ?>
	</option>
<?php
	unset( $fo );
}
