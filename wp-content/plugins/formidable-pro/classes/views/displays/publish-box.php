<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

_deprecated_file( basename( __FILE__ ), '4.09', null, 'This file can be found in formidable-views/classes/views/displays/publish-box.php' );
?>
<div id="publishing-action">
	<input name="original_publish" type="hidden" id="original_publish" value="<?php echo esc_attr( $label ); ?>" />
	<input type="submit" name="<?php echo esc_attr( $name ); ?>" id="publish" class="button button-primary frm-button-primary" value="<?php echo esc_attr( $label ); ?>" />
</div>

<div id="preview-action">
	<a class="preview button button-secondary frm-button-secondary" href="<?php echo esc_url( $preview_link ); ?>" target="wp-preview-<?php echo (int) $post->ID; ?>" id="post-preview">
		<?php
		printf(
			'%1$s<span class="screen-reader-text"> %2$s</span>',
			esc_html( $preview_button_text ),
			/* translators: accessibility text */
			esc_html__( '(opens in a new tab)' )
		);
		?>
	</a>
	<input type="hidden" name="wp-preview" id="wp-preview" value="" />
</div>
