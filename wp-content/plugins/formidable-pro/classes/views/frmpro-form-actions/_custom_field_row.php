<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<?php $sanitized_name = sanitize_title_with_dashes($custom_data['meta_name']); ?>

<div id="frm_postmeta_<?php echo esc_attr( $sanitized_name ) ?>" class="frm_postmeta_row frm_grid_container">
	<div class="frm4 frm_form_field">
		<label class="screen-reader-text">
			<?php esc_html_e( 'Name' ); ?>
		</label>
    <?php
    if ( isset($cf_keys) && $echo && $custom_data['meta_name'] != '' && ! in_array($custom_data['meta_name'], (array) $cf_keys) ) {
        $cf_keys[] = $custom_data['meta_name'];
    }

	if ( ! isset( $cf_keys ) || empty( $cf_keys ) ) {
	?>
	<input type="text" value="<?php echo esc_attr( $echo ? $custom_data['meta_name'] : '' ) ?>" name="<?php echo esc_attr( $action_control->get_field_name( 'post_custom_fields' ) ) ?>[<?php echo esc_attr( $sanitized_name ) ?>][meta_name]" class="frm_enternew frm_custom_field_key" />
    <?php } else { ?>
    <select name="<?php echo esc_attr( $action_control->get_field_name( 'post_custom_fields' ) ) ?>[<?php echo esc_attr( $sanitized_name ) ?>][meta_name]" class="frm_cancelnew frm_custom_field_key">
		<option value=""><?php esc_html_e( '&mdash; Select &mdash;' ); ?></option>
		<?php foreach ( $cf_keys as $cf_key ) { ?>
    	<option value="<?php echo esc_attr($cf_key) ?>"><?php echo esc_html($cf_key) ?></option>
    	<?php
    		unset($cf_key);
    	}
        ?>
    </select>
	<input type="text" class="hide-if-js frm_enternew frm_custom_field_key" name="<?php echo esc_attr( $action_control->get_field_name( 'post_custom_fields' ) ) ?>[<?php echo esc_attr( $sanitized_name ) ?>][custom_meta_name]" value="" />
    <?php } ?>

    <?php if ( isset( $cf_keys ) && ! empty( $cf_keys ) ) { ?>
    <div class="clear"></div>
    <div style="margin-left:8px;">
        <a href="javascript:void(0)" class="hide-if-no-js frm_toggle_cf_opts">
			<span class="frm_cancelnew"><?php esc_html_e( 'Enter new' ); ?></span>
			<span class="frm_enternew frm_hidden"><?php esc_html_e( 'Cancel', 'formidable-pro' ); ?></span>
        </a>
    </div>
    <?php } ?>
	</div>
	<div class="frm7 frm_form_field">
		<label class="screen-reader-text"><?php esc_html_e( 'Value', 'formidable-pro' ); ?></label>
		<select name="<?php echo esc_attr( $action_control->get_field_name( 'post_custom_fields' ) ) ?>[<?php echo esc_attr( $sanitized_name ) ?>][field_id]" class="frm_single_post_field">
		<option value=""><?php esc_html_e( '&mdash; Select Field &mdash;', 'formidable-pro' ); ?></option>
        <?php
        if ( ! empty($values['fields']) ) {
            if ( ! isset($custom_data['field_id']) ) {
                $custom_data['field_id'] = '';
            }

		foreach ( $values['fields'] as $fo ) {
            $fo = (array) $fo;
			if ( ! FrmField::is_no_save_field( $fo['type'] ) ) {
			?>
			<option value="<?php echo esc_attr( $fo['id'] ) ?>" <?php selected( $custom_data['field_id'], $fo['id'] ); ?>>
				<?php echo FrmAppHelper::truncate( $fo['name'], 50 ); ?>
			</option>
			<?php
            }
            unset($fo);
        }
		}
		?>
    </select>
	</div>
	<div class="frm1 frm_form_field frm-inline-select">
		<a href="javascript:void(0)" class="frm_remove_tag frm_icon_font" data-removeid="frm_postmeta_<?php echo esc_attr( $sanitized_name ) ?>" data-hidelast="#frm_form_action_<?php echo esc_attr( $action_control->number ) ?> .frm_name_value" data-showlast="#frm_form_action_<?php echo esc_attr( $action_control->number ) ?> .frm_add_postmeta_row"></a>
		<a href="javascript:void(0)" class="frm_add_tag frm_icon_font frm_add_postmeta_row"></a>
	</div>
</div>
<?php
unset( $sanitized_name );
