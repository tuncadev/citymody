<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

_deprecated_file( basename( __FILE__ ), '4.09', null, 'This file can be found in formidable-views/classes/views/displays/submitbox_actions.php' );
?>
<div class="misc-pub-section">
	<span id="frm_shortcode">
		<?php FrmProAppHelper::icon_by_class( 'frmfont frm_code_icon frm_svg20' ); ?>
		<?php esc_html_e( 'View', 'formidable-pro' ); ?>
		<strong><?php esc_html_e( 'Shortcodes', 'formidable-pro' ); ?></strong>
	</span>
    <a href="#edit_frm_shortcode" class="edit-frm_shortcode hide-if-no-js" tabindex='4'>
		<?php esc_html_e( 'Show', 'formidable-pro' ); ?>
	</a>
    <div id="frm_shortcodediv" class="hide-if-js">
		<p><input type="text" readonly="readonly" class="frm_select_box frm_98_width" value="[display-frm-data id=<?php echo ( isset( $post->ID ) ) ? $post->ID : esc_html__( 'Save to get ID', 'formidable-pro' ) ?> filter=limited]" />
    	<?php if ( isset($post->post_name) && ! empty( $post->post_name ) && ( $post->post_name != $post->ID ) ) { ?>
		<input type="text" style="margin-top:4px;" readonly="readonly" class="frm_select_box frm_98_width" value="[display-frm-data id=<?php echo ( isset( $post->post_name ) && $post->post_name != '' ) ? $post->post_name : '??'; ?> filter=limited]" />
    	<?php } ?>
    	</p>

		<p class="howto"><?php esc_html_e( 'Insert with PHP', 'formidable-pro' ); ?>:</p>
		<p>
			<input type="text" style="font-size:10px;font-weight:normal" readonly="readonly" class="frm_select_box frm_98_width" value="&lt;?php echo FrmProDisplaysController::get_shortcode( array( 'id' => <?php echo ( isset( $post->ID ) ) ? $post->ID : '??' ?>) ) ?&gt;" />
		</p>

		<p>
			<a href="#edit_frm_shortcode" class="cancel-frm_shortcode hide-if-no-js">
				<?php esc_html_e( 'Hide', 'formidable-pro' ); ?>
			</a>
		</p>
    </div>
</div>
