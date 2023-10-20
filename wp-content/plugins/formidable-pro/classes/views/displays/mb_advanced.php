<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

_deprecated_file( basename( __FILE__ ), '4.09', null, 'This file can be found in formidable-views/classes/views/displays/mb_advanced.php' );
wp_nonce_field('frm_save_display_nonce', 'frm_save_display');
?>

<div class="frm_grid_container limit_container <?php echo ( $post->frm_show_count == 'calendar' || $post->frm_show_count == 'one' ) ? 'frm_hidden' : ''; ?>">
	<p class="frm4 frm_form_field">
		<label>
			<?php esc_html_e( 'Limit', 'formidable-pro' ); ?>
			<span class="frm_help frm_icon_font frm_tooltip_icon" title="<?php esc_attr_e( 'If you don’t want all your entries displayed, you can insert the number limit here. Leave blank if you’d like all entries shown.', 'formidable-pro' ) ?>"></span>
		</label>
	</p>
	<p class="frm8 frm_form_field">
		<input type="text" id="limit" name="options[limit]" value="<?php echo esc_attr($post->frm_limit) ?>" size="4" />
	</p>

	<p class="frm4 frm_form_field">
		<label><?php esc_html_e( 'Page Size', 'formidable-pro' ); ?>
			<span class="frm_help frm_icon_font frm_tooltip_icon" title="<?php esc_attr_e( 'The number of entries to show per page. Leave blank to not use pagination.', 'formidable-pro' ) ?>"></span>
		</label>
	</p>
	<p class="frm8 frm_form_field">
		<input type="text" id="limit" name="options[page_size]" value="<?php echo esc_attr($post->frm_page_size) ?>" size="4" />
	</p>
</div>

<h3 class="frm-with-line">
	<span><?php esc_html_e( 'Sort & Filter', 'formidable-pro' ); ?></span>
</h3>

<div class="frm_grid_container" id="order_by_container">
	<div class="frm4 frm_form_field">
		<label><?php esc_html_e( 'Order', 'formidable-pro' ); ?></label>
	</div>
	<div class="frm8 frm_form_field">
            <div id="frm_order_options" class="frm_repeat_rows" style="padding-bottom:8px;">
				<a href="javascript:void(0)" class="frm_add_order_row button button-secondary frm-button-secondary <?php echo esc_attr( empty( $post->frm_order_by ) ? '' : 'frm_hidden' ); ?>">+ <?php esc_html_e( 'Add', 'formidable-pro' ); ?></a>
                <div class="frm_logic_rows frm_add_remove">
            <?php
			foreach ( $post->frm_order_by as $order_key => $order_by_field ) {
				if ( isset( $post->frm_order[ $order_key ] ) && isset( $post->frm_order_by[ $order_key ] ) ) {
                	FrmProDisplaysController::add_order_row( $order_key, $post->frm_form_id, $order_by_field, $post->frm_order[ $order_key ] );
				}
			}
            ?>
                </div>
            </div>
	</div>
</div>

<div class="frm_grid_container" id="where_container">
	<div class="frm4 frm_form_field">
		<label>
			<?php esc_html_e( 'Filter Entries', 'formidable-pro' ); ?>
			<span class="frm_help frm_icon_font frm_tooltip_icon" title="<?php esc_attr_e( 'Narrow down which entries will be used. The Unique options uses SQL GROUP BY to make sure only one entry is shown for each value in the selected field(s).', 'formidable-pro' ) ?>"></span>
		</label>
	</div>
	<div class="frm8 frm_form_field">
            <div id="frm_where_options" class="frm_repeat_rows">
				<a href="javascript:void(0)" class="frm_add_where_row button button-secondary frm-button-secondary <?php echo esc_attr( empty( $post->frm_where ) ? '' : 'frm_hidden' ); ?>">+ <?php esc_html_e( 'Add', 'formidable-pro' ); ?></a>
                <div class="frm_logic_rows frm_add_remove">
            <?php
				foreach ( $post->frm_where as $where_key => $where_field ) {
					if ( isset( $post->frm_where_is[ $where_key ] ) && isset( $post->frm_where_val[ $where_key ] ) ) {
						FrmProDisplaysController::add_where_row( $where_key, $post->frm_form_id, $where_field, $post->frm_where_is[ $where_key ], $post->frm_where_val[ $where_key ] );
					}
                }
            ?>
                </div>
            </div>
	</div>
</div>
<div class="frm_grid_container">
	<p class="frm4 frm_form_field">
		<label for="empty_msg">
			<?php esc_html_e( 'No Entries Message', 'formidable-pro' ); ?>
		</label>
	</p>
	<p class="frm8 frm_form_field">
		<textarea id="empty_msg" name="options[empty_msg]" class="frm_98_width" rows="5"><?php echo FrmAppHelper::esc_textarea( $post->frm_empty_msg ); ?></textarea>
	</p>

<h3 class="frm-with-line">
	<span><?php esc_html_e( 'Advanced', 'formidable-pro' ); ?></span>
</h3>

<p class="frm4 frm_form_field">
	<label>
		<?php esc_html_e( 'View Key', 'formidable-pro' ); ?>
	</label>
</p>
<p class="frm8 frm_form_field">
	<input type="text" name="post_name" value="<?php echo esc_attr( $post->post_name ); ?>" />
</p>

<p class="frm4 frm_form_field hide_dyncontent <?php echo in_array( $post->frm_show_count, array( 'dynamic', 'calendar' ) ) ? '' : 'frm_hidden'; ?>">
	<label>
		<?php esc_html_e( 'Detail Page Slug', 'formidable-pro' ); ?>
		<span class="frm_help frm_icon_font frm_tooltip_icon" title="<?php printf( esc_html__( 'Example: If parameter name is \'contact\', the url would be like %1$s/selected-page?contact=2. If this entry is linked to a post, the post permalink will be used instead.', 'formidable-pro' ), esc_html( FrmAppHelper::site_url() ) ); ?>" ></span>
	</label>
</p>
<p class="frm8 frm_form_field hide_dyncontent <?php echo in_array( $post->frm_show_count, array( 'dynamic', 'calendar' ) ) ? '' : 'frm_hidden'; ?>">
	<input type="text" id="param" name="param" value="<?php echo esc_attr($post->frm_param) ?>" />
</p>
<p class="frm4 frm_form_field hide_dyncontent <?php echo in_array( $post->frm_show_count, array( 'dynamic', 'calendar' ) ) ? '' : 'frm_hidden'; ?>">
	<label><?php esc_html_e( 'Parameter Value', 'formidable-pro' ); ?></label>
</p>
<p class="frm8 frm_form_field hide_dyncontent <?php echo in_array( $post->frm_show_count, array( 'dynamic', 'calendar' ) ) ? '' : 'frm_hidden'; ?>">
	<select id="type" name="type">
		<option value="id" <?php selected( $post->frm_type, 'id' ); ?>>
			<?php esc_html_e( 'ID', 'formidable-pro' ); ?>
		</option>
		<option value="display_key" <?php selected( $post->frm_type, 'display_key' ); ?>>
			<?php esc_html_e( 'Key', 'formidable-pro' ); ?>
		</option>
	</select>
</p>

    <?php
	if ( is_multisite() ) {
		if ( current_user_can( 'setup_network' ) ) {
		?>
<p>
				<label for="copy">
					<input type="checkbox" id="copy" name="options[copy]" value="1" <?php checked( $post->frm_copy, 1 ); ?> />
					<?php esc_html_e( 'Copy this View to other sites when Formidable Forms is activated. (Use only field keys in the content box(es) above.)', 'formidable-pro' ); ?>
				</label>
</p>
		<?php } else if ( $post->frm_copy ) { ?>
        <input type="hidden" id="copy" name="options[copy]" value="1" />
		<?php
        }
	}
	?>

</div>
