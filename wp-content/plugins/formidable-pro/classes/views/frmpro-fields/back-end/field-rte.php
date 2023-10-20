<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
$editor_args = array(
	'textarea_name' => $field_name,
	'textarea_rows' => $field['max'],
	'media_buttons' => false,
);
wp_editor( $field['default_value'], $field['html_id'], $editor_args );
