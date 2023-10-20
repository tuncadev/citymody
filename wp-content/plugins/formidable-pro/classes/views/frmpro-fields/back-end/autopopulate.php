<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<p class="frm-has-modal default-value-section-<?php echo esc_attr( $field['id'] . ( isset( $default_value_types['get_values_field']['current'] ) ? '' : ' frm_hidden' ) ); ?> frm-lookup-box-label frm-lookup-box-<?php echo esc_attr( $field['id'] ); ?>" style="padding-bottom:0;">
	<label for="frm_default_value_<?php echo esc_attr( $field['id'] ); ?>">
		<?php esc_html_e( 'Default Value', 'formidable' ); ?>
		<span class="frm-sub-label">
			(<?php esc_html_e( 'Lookup', 'formidable-pro' ); ?>)
		</span>
	</label>
</p>

<?php
FrmFieldsHelper::inline_modal(
	array(
		'title'    => __( 'Lookup Default Value', 'formidable' ),
		'callback' => array( 'FrmProLookupFieldsController', 'show_autopopulate_value_section_in_form_builder' ),
		'args'     => $field,
		'id'       => 'frm-lookup-box-' . $field['id'],
		'class'    => 'frm-lookup-modal frm-lookup-box-' . $field['id'],
		'show'     => ! empty( $field['get_values_field'] ),
	)
);
