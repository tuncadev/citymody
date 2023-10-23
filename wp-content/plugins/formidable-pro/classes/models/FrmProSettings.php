<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

#[\AllowDynamicProperties]
class FrmProSettings extends FrmSettings {
	public $option_name = 'frmpro_options';

	// options
	public $edit_msg;
	public $update_value;
	public $already_submitted;
	public $cal_date_format;
	public $date_format;
	public $menu_icon;
	public $inbox;
	public $repeater_row_delete_confirmation;

	/**
	 * @return array
	 */
	public function default_options() {
		return array(
			'edit_msg'          => __( 'Your submission was successfully saved.', 'formidable-pro' ),
			'update_value'      => __( 'Update', 'formidable-pro' ),
			'already_submitted' => __( 'You have already submitted that form', 'formidable-pro' ),
			'date_format'       => 'm/d/Y',
			'cal_date_format'   => $this->get_cal_date(),
			'menu_icon'         => '',
			'currency'          => 'USD',
			'inbox'             => array(
				'set'      => FrmProDb::$plug_version,
				'badge'    => 1,
				'promo'    => 1,
				'news'     => 1,
				'feedback' => 1,
			),
			'repeater_row_delete_confirmation' => __( 'Are you sure you want to delete this row?', 'formidable-pro' ),
		);
	}

	public function set_default_options() {
		$this->fill_with_defaults();
	}

	/**
	 * @since 4.06.01
	 */
	public function fill_with_defaults( $params = array() ) {
		$params['additional_filter_keys'] = array( 'edit_msg', 'update_value', 'already_submitted', 'repeater_row_delete_confirmation' );
		parent::fill_with_defaults( $params );
		$this->fill_inbox_defaults();
	}

	/**
	 * Since inbox settings are on by default, add any new options to
	 * prevent it from being off when added.
	 *
	 * @since 4.06.01
	 */
	private function fill_inbox_defaults() {
		$added = array(
			'feedback' => '4.06.01',
		);
		foreach ( $added as $type => $v ) {
			if ( ! isset( $this->inbox[ $type ] ) && version_compare( $this->inbox['set'], $v, '<' ) ) {
				$this->inbox[ $type ] = 1;
			}
		}
	}

	public function update( $params ) {
		if ( isset( $params['frm_date_format'] ) ) {
			$this->date_format = $params['frm_date_format'];
		}
		$this->get_cal_date();

		$this->fill_with_defaults($params);
	}

	/**
	 * Get the conversions from php date format to datepicker
	 * Set the cal_date_format to make sure it's not empty
	 *
	 * @since 2.0.2
	 */
	public function get_cal_date() {
		$formats = FrmProAppHelper::display_to_datepicker_format();
		if ( isset( $formats[ $this->date_format ] ) ) {
			$this->cal_date_format = $formats[ $this->date_format ];
		} else {
			$this->cal_date_format = 'mm/dd/yy';
		}
	}

	/**
	 * @since 4.06.01
	 */
	public function inbox_types() {
		return array(
			'badge'    => __( 'Show unread message count in menu', 'formidable-pro' ),
			'promo'    => __( 'Sales and promotions', 'formidable-pro' ),
			'news'     => __( 'New features', 'formidable-pro' ),
			'feedback' => __( 'Requests for feedback', 'formidable-pro' ),
		);
	}

	public function store() {
		// Save the posted value in the database
		update_option( $this->option_name, $this, 'no' );

		delete_transient($this->option_name);
		set_transient($this->option_name, $this);
	}
}
