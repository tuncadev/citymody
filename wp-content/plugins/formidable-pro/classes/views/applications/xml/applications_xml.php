<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>	<application>
		<name><?php echo FrmXMLHelper::cdata( $name ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></name>
	</application>
