<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

/**
 * @since 5.3.1
 */
class FrmProFormsListHelper extends FrmFormsListHelper {

	/**
	 * @param array $args
	 * @return void
	 */
	public function __construct( $args ) {
		parent::__construct( $args );
		wp_enqueue_style( 'frm_applications_common' );
	}

	/**
	 * @param stdClass $form
	 * @return string
	 */
	public function column_application( $form ) {
		global $wpdb;
		$where           = array(
			'meta_key'   => '_frm_form_id',
			'meta_value' => $form->id,
		);
		$application_ids = FrmDb::get_col( $wpdb->termmeta, $where, 'term_id' );
		return FrmProApplicationsHelper::get_application_tags_html( array_unique( $application_ids ) );
	}
}
