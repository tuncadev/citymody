<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

/**
 * @since 5.5
 */
trait FrmProFieldAutocompleteField {

	/**
	 * @return array<string,string>
	 */
	public function autocomplete_options() {
		$options     = FrmProFieldsHelper::get_autocomplete_options();
		$filter_keys = $this->get_filter_keys();
		if ( $filter_keys ) {
			$options = array_filter(
				$options,
				function( $key ) use ( $filter_keys ) {
					return in_array( $key, $filter_keys, true );
				},
				ARRAY_FILTER_USE_KEY
			);
		}
		return $options;
	}

	/**
	 * @return array An empty array will include every key.
	 */
	protected function get_filter_keys() {
		return array();
	}
}
