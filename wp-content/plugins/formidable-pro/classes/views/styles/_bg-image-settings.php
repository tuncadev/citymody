<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<div class="frm6">
	<div class="<?php echo esc_attr( $class ); ?>">
		<label><?php esc_html_e( 'Image Opacity', 'formidable-pro' ); ?></label>
		<input type="text" name="frm_style_setting[post_content][bg_image_opacity]" value="<?php echo esc_attr( $bg_image_opacity ); ?>" />
	</div>
</div>
