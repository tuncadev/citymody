<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

/**
 * @since 2.04
 */
class FrmProFieldValue extends FrmFieldValue {

	/**
	 * FrmProFieldValue constructor.
	 *
	 * @param stdClass $field
	 * @param stdClass $entry
	 */
	public function __construct( $field, $entry ) {
		parent::__construct( $field, $entry );
	}

	/**
	 * Initialize the saved_value property
	 *
	 * @since 2.04
	 *
	 * @param stdClass $entry
	 */
	protected function init_saved_value( $entry ) {

		if ( $entry->form_id != $this->field->form_id ) {
			//If parent entry ID and child repeating field

			$where = array(
				'form_id' => $this->field->form_id,
				'parent_item_id' => $entry->id,
			);
			$child_entry_ids = FrmDb::get_col( 'frm_items', $where );

			if ( is_array( $child_entry_ids ) ) {
				$this->saved_value = array();

				foreach ( $child_entry_ids as $child_entry_id ) {
					$child_entry = FrmEntry::getOne( $child_entry_id, true );
					$current_field_value = new FrmProFieldValue( $this->field, $child_entry );
					$this->saved_value[ $child_entry_id ] = $current_field_value->get_saved_value();
				}
			}
		} elseif ( isset( $this->field->field_options['post_field'] ) && $this->field->field_options['post_field'] ) {

			$this->saved_value = $this->get_post_value( $entry );
			$this->clean_saved_value();

		} else {

			parent::init_saved_value( $entry );

		}
	}

	/**
	 * Initialize a field's displayed value
	 *
	 * @since 2.04
	 *
	 * @param array $atts
	 */
	public function prepare_displayed_value( $atts = array() ) {
		// TODO: Pick up on making this work well
		// TODO: maybe move these various functions into FrmFieldType classes
		$this->displayed_value = $this->saved_value;

		unset( $atts['class'] ); // This shouldn't be used on values.
		$atts['entry'] = $this->entry;

		if ( $this->has_child_entries() ) {
			$this->prepare_displayed_value_for_field_with_child_entries( $atts );
		} else {

			// TODO: all display value generation should be handled in one classes or subclasses
			$this->generate_post_field_displayed_value();
			$this->generate_displayed_value_for_field_type( $atts );
			$this->filter_array_displayed_value();
			$new_atts = array(
				'plain_text'         => isset( $atts['plain_text'] ) && $atts['plain_text'],
				'show_image_options' => isset( $atts['show_image_options'] ) && $atts['show_image_options'],
			);
			$this->get_option_label_for_saved_value( $new_atts );
			$this->filter_displayed_value( $atts );
		}
	}

	/**
	 * Get the displayed value for different field types
	 *
	 * @since 5.5.2
	 *
	 * @param array $atts
	 *
	 * @return void
	 */
	protected function generate_displayed_value_for_field_type( $atts ) {
		$this->maybe_set_displayed_value_for_page_break();
		parent::generate_displayed_value_for_field_type( $atts );
	}

	/**
	 * Set a displayed value for break types so that we can set a display value for it in FrmProFieldBreak::get_display_value.
	 * Lite checks for a displayed_value before calling get_display_value, so set 1 to pass the check.
	 *
	 * @since 5.5.2
	 * @return void
	 */
	private function maybe_set_displayed_value_for_page_break() {
		if ( 'break' !== $this->get_field_type() || ! FrmAppHelper::is_empty_value( $this->displayed_value, '' ) ) {
			return;
		}

		$this->displayed_value = 1;
	}

	protected function prepare_displayed_value_for_field_with_child_entries( $atts = array() ) {
		if ( ! is_array( $this->saved_value ) ) {
			return;
		}

		$atts['entry'] = false;
		if ( is_object( $this->displayed_value ) ) {
			$atts['entry'] = $this->displayed_value;
		}

		$this->displayed_value = array();

		if ( isset( $atts['include_fields'] ) ) {
			$atts['include_fields'] = '';
		}

		if ( isset( $atts['fields'] ) ) {
			$atts['fields'] = '';
		}

		foreach ( $this->saved_value as $k => $child_id ) {
			if ( is_numeric( $child_id ) ) {
				$child_values = new FrmProEntryValues( $child_id, $atts );
				$this->displayed_value[ $child_id ] = $child_values->get_field_values();
			} elseif ( is_object( $child_id ) ) {
				if ( $atts['summary'] ) {
					$this->set_actual_other_values( $child_id );
				}

				$atts['entry'] = $child_id;
				$child_values = new FrmProEntryValues( 0, $atts );
				$this->displayed_value[] = $child_values->get_field_values();
				$atts['entry'] = false;
			}
		}
	}

	/**
	 * Get the value entered in an other field to include
	 * in the summary. This covers fields in child forms.
	 *
	 * @since 4.02.04
	 */
	private function set_actual_other_values( &$field ) {
		if ( ! isset( $field->metas ) || ! isset( $field->metas['other'] ) ) {
			return;
		}

		$values = array( 'item_meta' => $field->metas );
		$values = FrmProEntry::mod_other_vals( $values, 'front' );
		$field->metas = $values['item_meta'];
	}

	/**
	 * Get the saved value for a post field
	 *
	 * @since 2.04
	 *
	 * @param stdClass $entry
	 *
	 * @return mixed
	 */
	protected function get_post_value( $entry ) {
		$pass_args = array(
			'links' => false,
			'truncate' => false,
		);

		return FrmProEntryMetaHelper::get_post_or_meta_value( $entry, $this->field, $pass_args );
	}

	/* Check if an embedded form or repeating section has child entries
	 * @since 2.04
	 *
	 * @return bool
	 */
	public function has_child_entries() {
		$has_child_entries = false;

		if ( $this->field->type === 'form' || $this->is_repeater() ) {

			if ( FrmAppHelper::is_not_empty_value( $this->saved_value ) ) {
				$has_child_entries = true;
				$this->check_unsaved_children();
			}
		}

		return $has_child_entries;

	}

	/**
	 * @since 3.06.01
	 */
	public function is_repeater() {
		return FrmField::is_repeating_field( $this->field );
	}

	/**
	 * We've seen cases where the repeater saves the posted values without
	 * creating the child entries. This saves them as children.
	 *
	 * @since 4.04.04
	 */
	private function check_unsaved_children() {
		$saved_post = is_array( $this->saved_value ) && isset( $this->saved_value['row_ids'] );
		if ( ! $saved_post ) {
			return;
		}

		$values = array(
			'form_id'   => $this->field->form_id,
			'id'        => $this->entry_id,
			'item_meta' => array(
				$this->field->id => $this->saved_value,
			),
		);
		FrmProEntry::save_sub_entry( $this->field, 'create', $values, $values );

		$this->saved_value = $values['item_meta'][ $this->field->id ];
		FrmEntryMeta::update_entry_meta( $this->entry_id, $this->field->id, null, $this->saved_value );
	}

	/**
	 * Get a post field's displayed value
	 *
	 * @since 2.04
	 *
	 * @return mixed
	 */
	protected function generate_post_field_displayed_value() {
		if ( FrmField::is_option_true( $this->field, 'post_field' ) ) {
			$entry = $this->entry;
			if ( ! is_object( $this->entry ) || empty( $this->entry->metas ) ) {
				$entry = FrmEntry::getOne( $this->entry_id, true );
			}
			$this->displayed_value = FrmProEntryMetaHelper::get_post_or_meta_value( $entry, $this->field, array( 'truncate' => true ) );
			FrmProAppHelper::unserialize_or_decode( $this->displayed_value );
		}
	}

	/**
	 * Filter an array displayed value
	 *
	 * @since 2.04
	 */
	protected function filter_array_displayed_value() {
		if ( is_array( $this->displayed_value ) ) {
			$new_value = '';
			foreach ( $this->displayed_value as $val ) {
				if ( is_array( $val ) ) {
					// This will affect checkboxes inside of repeating sections
					$new_value .= implode( ', ', $val ) . "\n";
				}
			}

			if ( $new_value != '' ) {
				$this->displayed_value = $new_value;
			} else {
				$this->displayed_value = array_filter( $this->displayed_value, array( $this, 'remove_empty_values_but_leave_0' ) );
			}
		}
	}

	/**
	 * Filter function called by array_filter call in filter_array_displayed_value.
	 *
	 * @param mixed $value
	 * @return bool true if the value should be maintained in the array, false and it is filtered out.
	 */
	private function remove_empty_values_but_leave_0( $value ) {
		return ! empty( $value ) || '0' === $value;
	}

	/**
	 * Get the option label for a saved value
	 *
	 * @since 2.04
	 */
	protected function get_option_label_for_saved_value( $atts = array() ) {
		$this->displayed_value = FrmProEntriesController::get_option_label_for_saved_value( $this->displayed_value, $this->field, $atts );
	}
}
