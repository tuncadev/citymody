<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

class FrmProDisplay {

	/**
	 * Check for a qualified view.
	 *
	 * @param array $args
	 * @return object|false
	 */
	public static function get_auto_custom_display( $args ) {
		return FrmProDisplaysController::deprecated_function( __METHOD__, 'FrmViewsDisplay::get_auto_custom_display', $args );
	}

	public static function getOne( $id, $blog_id = false, $get_meta = false, $atts = array() ) {
		return FrmProDisplaysController::deprecated_function( __METHOD__, 'FrmViewsDisplay::getOne', $id, $blog_id, $get_meta, $atts );
	}

	public static function getAll( $where = array(), $order_by = 'post_date', $limit = 99 ) {
		return FrmProDisplaysController::deprecated_function( __METHOD__, 'FrmViewsDisplay::getAll', $where, $order_by, $limit );
	}

	/**
	 * @deprecated 4.09
	 */
	public static function duplicate( $id, $copy_keys = false, $blog_id = false ) {
		return FrmProDisplaysController::deprecated_function( __METHOD__, 'FrmViewsDisplay::duplicate', $id, $copy_keys, $blog_id );
	}

	/**
	 * @deprecated 4.09
	 */
	public static function update( $id, $values ) {
		return FrmProDisplaysController::deprecated_function( __METHOD__, 'FrmViewsDisplay::update', $id, $values );
	}

	/**
	 * @deprecated 4.09
	 */
	public static function get_display_ids_by_form( $form_id ) {
		return FrmProDisplaysController::deprecated_function( __METHOD__, 'FrmViewsDisplay::get_display_ids_by_form', $form_id );
	}

	/**
	 * @deprecated 4.09
	 */
	public static function get_form_custom_display( $form_id ) {
		return FrmProDisplaysController::deprecated_function( __METHOD__, 'FrmViewsDisplay::get_form_custom_display', $form_id );
	}

	/**
	 * @deprecated 4.09
	 */
	public static function get_id_by_key( $key ) {
		return FrmProDisplaysController::deprecated_function( __METHOD__, 'FrmViewsDisplay::get_id_by_key', $key );
	}
}
