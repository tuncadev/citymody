<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<div class="frm-star-group">
<?php
for ( $i = 1; $i <= $max; $i++ ) {

	$star_icon_svg_slug = null;

	if ( $i <= $numbers['value'] ) {
		$star_icon_svg_slug = 'frm-star-full-icon';
	} elseif ( $numbers['decimal'] && ( $i - 1 ) == $numbers['digit'] ) {
		$star_icon_svg_slug = 'frm-star-half-icon';
	} else {
		$star_icon_svg_slug = 'frm-star-icon';
	}

	FrmProAppHelper::get_svg_icon( $star_icon_svg_slug, 'frmsvg', array( 'echo' => true ) );

	?><?php
}
?>
</div>
<div class="frm_clear"></div>
