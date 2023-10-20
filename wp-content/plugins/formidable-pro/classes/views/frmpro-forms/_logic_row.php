<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<div id="<?php echo esc_attr( $id ); ?>" class="frm_logic_row frm_logic_row_<?php echo esc_attr( $key ); ?> frm_grid_container">
	<p class="frm3 frm_form_field">
		<select
			name="<?php echo esc_attr( $names['hide_field'] ); ?>"
			<?php if ( ! empty( $onchange ) ) { ?>
				onchange="<?php echo $onchange; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"
			<?php } ?>
			>
			<option value=""><?php esc_html_e( '&mdash; Select &mdash;' ); ?></option>
			<?php
			foreach ( $form_fields as $ff ) {
				if ( is_array( $ff ) ) {
					$ff = (object) $ff;
				}

				if ( in_array( $ff->type, $exclude_fields, true ) || FrmProField::is_list_field( $ff ) ) {
					continue;
				}

				$selected = isset( $condition['hide_field'] ) && (int) $ff->id === (int) $condition['hide_field'];
				?>
				<option value="<?php echo esc_attr( $ff->id ); ?>"<?php selected( $selected ); ?>><?php echo esc_html( $ff->name ); ?></option>
				<?php
				unset( $ff );
			}
			?>
		</select>
	</p>
	<p class="frm2 frm_form_field">
		<select name="<?php echo esc_attr( $names['hide_field_cond'] ); ?>">
			<option value="==" <?php selected( $condition['hide_field_cond'], '==' ); ?>>
				<?php esc_html_e( 'is equal to', 'formidable-pro' ); ?>
			</option>
			<option value="!=" <?php selected( $condition['hide_field_cond'], '!=' ); ?>>
				<?php esc_html_e( 'is NOT equal to', 'formidable-pro' ); ?> &nbsp;
			</option>
			<option value=">" <?php selected( $condition['hide_field_cond'], '>' ); ?>>
				<?php esc_html_e( 'greater than', 'formidable-pro' ); ?>
			</option>
			<option value="<" <?php selected( $condition['hide_field_cond'], '<' ); ?>>
				<?php esc_html_e( 'less than', 'formidable-pro' ); ?>
			</option>
			<option value="LIKE" <?php selected( $condition['hide_field_cond'], 'LIKE' ); ?>>
				<?php esc_html_e( 'is like', 'formidable-pro' ); ?>
			</option>
			<option value="not LIKE" <?php selected( $condition['hide_field_cond'], 'not LIKE' ); ?>>
				<?php esc_html_e( 'is not like', 'formidable-pro' ); ?>
			</option>
			<?php if ( version_compare( FrmAppHelper::plugin_version(), '5.0.04', '>=' ) ) { ?>
				<option value="LIKE%" <?php selected( $condition['hide_field_cond'], 'LIKE%' ); ?>>
					<?php esc_html_e( 'starts with', 'formidable-pro' ); ?>
				</option>
				<option value="%LIKE" <?php selected( $condition['hide_field_cond'], '%LIKE' ); ?>>
					<?php esc_html_e( 'ends with', 'formidable-pro' ); ?>
				</option>
			<?php } ?>
		</select>
	</p>
	<p class="frm6 frm_form_field">
		<span id="frm_show_selected_values_<?php echo esc_attr( $key . '_' . $meta_name ); ?>"><?php
			$selector_field_id = ( $condition['hide_field'] && is_numeric( $condition['hide_field'] ) ) ? (int) $condition['hide_field'] : 0;
			$selector_args     = array(
				'html_name' => $names['hide_opt'],
				'value'     => isset( $condition['hide_opt'] ) ? $condition['hide_opt'] : '',
				'source'    => 'form_actions',
			);
			FrmFieldsHelper::display_field_value_selector( $selector_field_id, $selector_args );
		?></span>
	</p>
	<p class="frm1 frm_form_field">
		<a href="javascript:void(0)" class="frm_remove_tag frm_icon_font" data-removeid="<?php echo esc_attr( $id ); ?>" <?php echo ! empty( $showlast ) ? 'data-showlast="' . esc_attr( $showlast ) . '"' : ''; ?> <?php echo empty( $hidelast ) ? '' : 'data-hidelast="' . esc_attr( $hidelast ) . '"'; ?>></a>
		<a href="javascript:void(0)" class="frm_add_tag frm_icon_font frm_add_<?php echo esc_attr( $type ); ?>_logic" data-emailkey="<?php echo esc_attr( $key ); ?>"></a>
	</p>
</div>
