<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
_deprecated_file( basename( __FILE__ ), '4.0.01', null, 'frmpro-fields/10radio.php' );

if ( is_array( $field['options'] ) ) {
	if ( ! isset( $field['value'] ) ) {
		$field['value'] = $field['default_value'];
		FrmProAppHelper::unserialize_or_decode( $field['value'] );
	}

	$star = FrmField::is_option_true( $field, 'star' );
	foreach ( $field['options'] as $opt_key => $opt ) {
		$opt = apply_filters( 'frm_field_label_seen', $opt, $opt_key, $field );

		if ( ! $star ) {
			?><div class="frm_scale"><label for="<?php echo esc_attr( $html_id ); ?>-<?php echo esc_attr( $opt_key ); ?>"><?php
		}
		?>
		<input type="hidden" name="field_options[options_<?php echo esc_attr( $field['id'] ); ?>][<?php echo esc_attr( $opt ); ?>]" value="<?php echo esc_attr( $opt ); ?>">
		<input type="radio" name="<?php echo esc_attr( $field_name ); ?>" id="<?php echo esc_attr( $html_id . '-' . $opt_key ); ?>" value="<?php echo esc_attr( $opt ); ?>" <?php
		checked( $field['value'], $opt ) . ' ';
		?> />
		<?php
		if ( ! $star ) {
			echo esc_html( $opt );
			?></label>
			</div>
		<?php
		}
	}
}
