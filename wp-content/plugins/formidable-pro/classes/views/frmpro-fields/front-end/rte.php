<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

$e_args = array( 'textarea_name' => $field_name );
if ( $field['default_value'] !== '' ) {
	$e_args['editor_class'] = 'frm_has_default';
}

$rte_is_readonly = ! empty( $field['read_only'] );

if ( FrmAppHelper::is_admin() ) { ?>
	<div id="<?php echo user_can_richedit() ? 'postdivrich' : 'postdiv'; ?>" class="postarea frm_full_rte">
		<?php
		$e_args['dfw'] = true;

		if ( $rte_is_readonly ) {
			FrmProFieldRte::turnoff_tinymce_controls( $e_args );
		}

		wp_editor( str_replace( '&quot;', '"', $field['value'] ), $html_id, $e_args );
		?>
	</div>
<?php
// Rich text for front-end, including Preview page
} elseif ( $field['type'] === 'rte' ) {
	$e_args['media_buttons'] = false;
	if ( $field['max'] ) {
		$e_args['textarea_rows'] = $field['max'];
	}
	$e_args['tinymce'] = array(
		'init_instance_callback' => 'frmProForm.changeRte',
	);

	if ( $rte_is_readonly ) {
		FrmProFieldRte::turnoff_tinymce_controls( $e_args );
	}

	$e_args = apply_filters( 'frm_rte_options', $e_args, $field );
	FrmProFieldRte::maybe_print_media_templates( $e_args );

	if ( $field['size'] ) {
	?>
		<style type="text/css">#wp-field_<?php echo esc_attr( $field['field_key'] ); ?>-wrap{width:<?php echo esc_attr( $field['size'] ) . ( is_numeric( $field['size'] ) ? 'px' : '' ); ?>;}</style><?php
	}

	if ( FrmField::is_required( $field ) ) {
		$req_msg = FrmFieldsHelper::get_error_msg( $field, 'blank' );
		if ( $req_msg ) {
			global $frm_vars;
			if ( ! isset( $frm_vars['rte_reqmessages'] ) ) {
				$frm_vars['rte_reqmessages'] = array();
			}
			$frm_vars['rte_reqmessages'][ $html_id ] = $req_msg;
		}
	}

	wp_editor( FrmAppHelper::esc_textarea( $field['value'], true ), $html_id, $e_args );

	// If submitting with Ajax or on preview page and tinymce is not loaded yet, load it now

	unset( $e_args );
}

if ( $field['default_value'] !== '' ) {
	?>
	<input type="hidden" id="<?php echo esc_attr( $html_id ); ?>-frmval" value="<?php echo esc_attr( $field['default_value'] ); ?>"/>
	<?php
}
