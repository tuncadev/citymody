<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

class FrmProFieldSummaryValue extends FrmProFieldValue {

	/**
	 * @var stdClass
	 */
	protected $field = null;

	/**
	 * @var array
	 */
	protected $atts = array();

	/**
	 * @var mixed
	 */
	protected $posted_value = '';

	/**
	 * @var array
	 */
	private $child_form_args = array();

	/**
	 * Class constructor.
	 *
	 * @param stdClass $field
	 * @param array    $atts
	 */
	public function __construct( $field, $atts, $child_form_args = array() ) {
		if ( ! is_object( $field ) ) {
			return;
		}

		$this->field = $field;
		$this->atts  = $atts;
		$this->init_child_form_args( $child_form_args );
		$this->init_posted_value();
	}

	/**
	 * TODO: Remove this after 4.04 since it was added to parent.
	 */
	public function get_field_attr( $option ) {
		return $this->field->{$option};
	}

	public function get_posted_value() {
		return $this->posted_value;
	}

	/**
	 * TODO: Remove this after 4.04 since it was added to parent.
	 */
	public function get_field() {
		return $this->field;
	}

	protected function init_child_form_args( $args ) {
		$this->child_form_args = $args;
	}

	protected function init_posted_value() {
		if ( 'html' === $this->field->type ) {
			$this->posted_value = $this->field->description;
			return;
		}

		if ( empty( $this->child_form_args ) ) {
			FrmEntriesHelper::get_posted_value( $this->field, $this->posted_value, array() );
		} else {
			$args = array(
				'parent_field_id' => $this->child_form_args['parent'],
				'key_pointer'     => $this->child_form_args['row_id'],
			);
			FrmEntriesHelper::get_posted_value( $this->field, $this->posted_value, $args );
		}

		$this->maybe_add_other();
	}

	/**
	 * Get the value entered in an other field to include
	 * in the summary. This covers non-repeating fields.
	 *
	 * @since 4.02.04
	 */
	private function maybe_add_other() {
		if ( empty( $this->posted_value ) || ! isset( $_POST['item_meta'] ) || ! isset( $_POST['item_meta']['other'] ) ) {
			return;
		}

		$other = $_POST['item_meta']['other'];
		FrmAppHelper::sanitize_value( 'sanitize_text_field', $other );
		$values = array(
			'item_meta' => array(
				$this->field->id => $this->posted_value,
				'other'          => $other,
			),
		);

		$values = FrmProEntry::mod_other_vals( $values, 'front' );
		$this->posted_value = $values['item_meta'][ $this->field->id ];
	}

	public function prepare_displayed_value( $atts = array() ) {
		$this->displayed_value = $this->posted_value;
		$this->get_option_label_for_saved_value();

		if ( $this->has_child_entries() ) {
			$this->prepare_displayed_value_for_field_with_child_entries( $atts );
		} else {
			$this->generate_displayed_value_for_field_type( $atts );
		}
	}

	protected function prepare_displayed_value_for_field_with_child_entries( $atts = array() ) {
		$this->displayed_value = array();

		foreach ( $this->posted_value['row_ids'] as $row_id ) {
			$args = array(
				'parent' => $this->field->id,
				'row_id' => $row_id,
			);
			$child_values = new FrmProSummaryValues( $this->posted_value['form'], $atts, $args );
			$this->displayed_value[ $row_id ] = $child_values->get_field_values();
		}
	}

	protected function generate_displayed_value_for_field_type( $atts ) {
		if ( ! FrmAppHelper::is_empty_value( $this->displayed_value, '' ) ) {
			$field_obj = FrmFieldFactory::get_field_object( $this->field );

			$atts = $this->prepare_display_atts();

			$this->displayed_value = $field_obj->get_display_value( $this->displayed_value, $atts );
		}
	}

	protected function prepare_display_atts() {
		$atts = array();
		// May use switch later if there are more to check
		if ( 'file' === $this->field->type ) {
			$atts['show_image'] = true;
		}

		return $atts;
	}
}
