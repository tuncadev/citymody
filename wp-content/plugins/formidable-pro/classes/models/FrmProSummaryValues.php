<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

/**
 * @since 4.03
 */
class FrmProSummaryValues {

	protected $form_id = '';

	protected $fields = array();

	/**
	 * @var FrmProFieldSummaryValue[]
	 */
	protected $field_values = array();

	protected $excluded_ids = array();

	protected $excluded_types = array();

	/**
	 * @var stdClass
	 */
	private $break_before_summary = null;

	/**
	 * @var stdClass
	 */
	private $current_section = null;

	/**
	 * @var stdClass
	 */
	private $current_embedded_form = null;

	private $current_page_is_hidden = false;

	private $child_form_args = array();

	/**
	 * @var array
	 */
	protected $atts = array();

	/**
	 * @param int   $form_id
	 * @param array $atts
	 * @param array $child_form_args
	 */
	public function __construct( $form_id, $atts, $child_form_args = array() ) {
		if ( ! $form_id ) {
			return;
		}

		$this->form_id = $form_id;
		$this->atts    = $atts;

		$this->init_child_form_args( $child_form_args );
		$this->init_excluded_ids( $atts );
		$this->init_excluded_types( $atts );
		$this->init_fields();

		if ( $this->is_main_form() ) {
			$this->get_page_break_before_summary();
			if ( isset( $this->break_before_summary ) ) {
				$this->init_field_values();
			}
		} else {
			$this->init_field_values();
		}
	}

	protected function init_child_form_args( $args ) {
		$this->child_form_args = $args;
	}

	protected function init_excluded_ids( $atts ) {
		if ( isset( $atts['excluded_ids'] ) ) {
			$this->excluded_ids = $atts['excluded_ids'];
		}
	}

	protected function init_excluded_types( $atts ) {
		if ( isset( $atts['excluded_types'] ) ) {
			$this->excluded_types = $atts['excluded_types'];
		}
	}

	protected function is_main_form() {
		return empty( $this->child_form_args );
	}

	protected function init_fields() {
		if ( $this->is_main_form() ) {
			$this->fields = FrmField::get_all_for_form( $this->form_id, '', 'include', 'include' );
		} else {
			$this->fields = FrmField::get_all_for_form( $this->form_id, '', 'exclude', 'exclude' );
		}

		/**
		 * Allows changing fields in summary values.
		 *
		 * @since 5.0.15
		 *
		 * @param array $fields Fields array.
		 * @param array $args   Includes `form_id`.
		 */
		$this->fields = apply_filters( 'frm_pro_fields_in_summary_values', $this->fields, array( 'form_id' => $this->form_id ) );
	}

	protected function init_field_values() {
		foreach ( $this->fields as $field ) {
			if ( $this->is_field_included( $field ) ) {
				$this->add_field_values( $field );
			}
		}
	}

	/**
	 * Add a field's value to the field_values property
	 *
	 * @param stdClass $field
	 */
	protected function add_field_values( $field ) {
		$this->field_values[ $field->id ] = new FrmProFieldSummaryValue( $field, $this->atts, $this->child_form_args );
	}

	public function get_fields() {
		return $this->fields;
	}

	public function get_fake_entry() {
		$entry = $this->base_entry();

		foreach ( $this->field_values as $field_id => $field_value ) {
			$value = $field_value->get_posted_value();
			if ( $field_value->is_repeater() || 'form' === $field_value->get_field_type() ) {
				$entry->metas[ $field_id ] = $this->fake_repeater_entry( $value, $field_value );
			} elseif ( 'date' === $field_value->get_field_type() ) {
				$entry->metas[ $field_id ] = FrmProAppHelper::maybe_convert_to_db_date( $value );
			} else {
				$entry->metas[ $field_id ] = $value;
			}
		}

		return $entry;
	}

	private function fake_repeater_entry( $value, $field_value ) {
		if ( empty( $value ) || ! is_array( $value ) || ! isset( $value['row_ids'] ) ) {
			return $value;
		}

		$children = array();
		$form_id  = FrmField::get_option( $field_value->get_field(), 'form_select' );

		foreach ( $value['row_ids'] as $v ) {
			$child_entry = $this->base_entry( $form_id );
			foreach ( $value[ $v ] as $child_field => $child_value ) {
				if ( $child_field === 0 ) {
					continue;
				}

				$child_field = FrmField::getOne( $child_field );
				if ( ! $child_field ) {
					continue;
				}

				$child_field->temp_id = $child_field->id . '-' . $field_value->get_field_id() . '-' . $v;
				if ( $this->is_excluded_type( $child_field ) ) {
					continue;
				}

				$child_entry->metas[ $child_field->id ] = $child_value;
				unset( $child_field, $child_value );
			}
			$children[ $v ] = $child_entry;
		}

		return $children;
	}

	/**
	 * @since 4.03
	 *
	 * @param int $form_id
	 *
	 * @return stdClass
	 */
	private function base_entry( $form_id = 0 ) {
		$entry          = new stdClass();
		$entry->post_id = 0;
		$entry->id      = 0;
		$entry->ip      = '';
		$entry->form_id = $form_id;
		$entry->metas   = array();
		return $entry;
	}

	public function get_field_values() {
		return $this->field_values;
	}

	public function get_break_before_summary() {
		return $this->break_before_summary;
	}

	private function get_page_break_before_summary() {
		foreach ( $this->fields as $field ) {
			if ( $field->type !== 'end_divider' ) {
				$this->set_current_container( $field );
			}

			/**
			 * Check that a page break exists before the summary & is 'valid'.
			 * Validity is that the page break and the summary are both direct
			 * children of the main form, or the page break and the parent of
			 * the summary are siblings in the main form.
			 */
			if ( 'break' === $field->type ) {
				if ( ! isset( $this->current_section ) && ! isset( $this->current_embedded_form ) ) {
					// break is directly in the main form
					$this->break_before_summary = $field;
				}
			} elseif ( 'summary' === $field->type ) {
				// reset because of subsequent operations
				$this->current_section       = null;
				$this->current_embedded_form = null;

				break;
			} elseif ( $field->type === 'end_divider' ) {
				$this->set_current_container( $field );
			}
		}
	}

	private function maybe_hide_current_page( $field ) {
		if ( 'break' === $field->type ) {
			$this->current_page_is_hidden = $this->is_field_hidden( $field );
		}
	}

	private function is_excluded_type( $field ) {
		$is_excluded = false;
		foreach ( $this->excluded_types as $type ) {
			if ( 0 === strpos( $type, '[' ) ) {
				// it's a field option
				$opt = $this->get_option( $type );
				if ( ! FrmField::is_option_empty( $field, $opt ) ) {
					if ( 'admin_only' === $opt ) {
						$is_excluded = ! FrmProFieldsHelper::is_field_visible_to_user( $field );
					} elseif ( 'hide_field' === $opt ) {
						// 'hide_field' is the option for conditional logic
						$is_excluded = $this->is_field_hidden( $field );
					} else {
						$is_excluded = true;
					}
					break;
				}
			} elseif ( $type === $field->type ) {
				$is_excluded = true;
				break;
			}
		}

		return $is_excluded;
	}

	private function is_field_hidden( $field ) {
		return FrmProFieldsHelper::is_field_hidden( $field, wp_unslash( $_POST ) );
	}

	private function get_option( $type ) {
		preg_match( '/\[(.*?)\]/', $type, $matches, PREG_OFFSET_CAPTURE );
		return $matches[1][0];
	}

	private function field_val_is_blank( $field ) {
		if ( in_array( $field->type, $this->no_blank_check_fields(), true ) ) {
			return false;
		}

		$value = '';
		$args  = array();
		if ( ! $this->is_main_form() ) {
			$args = array(
				'parent_field_id' => $this->child_form_args['parent'],
				'key_pointer'     => $this->child_form_args['row_id'],
			);
		}

		FrmEntriesHelper::get_posted_value( $field, $value, $args );

		return FrmAppHelper::is_empty_value( $value );
	}

	/*
	 * Fields that blankness shouldn't be checked for.
	 */
	private function no_blank_check_fields() {
		return array( 'divider', 'end_divider', 'captcha', 'break', 'form', 'html' );
	}

	/**
	 * Set/clear the current section or embedded form
	 *
	 * @param stdClass $field
	 */
	private function set_current_container( $field ) {
		if ( $field->type === 'divider' ) {
			$this->current_section = $field;
		} elseif ( $field->type === 'end_divider' ) {
			$this->current_section = null;
		}

		if ( $field->type === 'form' ) {
			$this->current_embedded_form = $field;
		} elseif ( is_object( $this->current_embedded_form ) && $field->form_id != $this->current_embedded_form->field_options['form_select'] ) {
			$this->current_embedded_form = null;
		}
	}

	/**
	 * Check if a field should be included in the summary.
	 *
	 * @param stdClass $field
	 *
	 * @return bool
	 */
	protected function is_field_included( $field ) {
		$this->maybe_hide_current_page( $field );

		if ( $this->current_page_is_hidden ) {
			return false;
		}

		if ( $field->type !== 'end_divider' ) {
			$this->set_current_container( $field );
		}

		$is_included = true;

		if ( ! empty( $this->excluded_ids ) ) {
			$is_included = ! $this->is_self_or_parent_excluded_id( $field );
			if ( ! $is_included ) {
				/**
				 * Return now before another 'if' test turns it back to true;
				 * but before returning, maybe set current container.
				 */
				$this->maybe_set_current_container( $field );
				return $is_included;
			}
		}

		if ( ! empty( $this->excluded_types ) ) {
			$is_included = ! $this->is_self_or_parent_excluded_type( $field );
			if ( ! $is_included ) {
				$this->maybe_set_current_container( $field );
				return $is_included;
			}
		}

		if ( $this->field_val_is_blank( $field ) ) {
			$is_included = false;
		}

		$this->maybe_set_current_container( $field );

		return $is_included;
	}

	private function maybe_set_current_container( $field ) {
		if ( $field->type === 'end_divider' ) {
			$this->set_current_container( $field );
		}
	}

	/**
	 * Checks if the ID of a field or its parent (embedded form or section) is excluded.
	 *
	 * @param stdClass $field
	 *
	 * @return bool
	 */
	private function is_self_or_parent_excluded_id( $field ) {
		if ( in_array( $field->id, $this->excluded_ids ) ) {
			$is_excluded = true;
		} elseif ( is_object( $this->current_section ) && in_array( $this->current_section->id, $this->excluded_ids ) ) {
			$is_excluded = true;
		} elseif ( is_object( $this->current_embedded_form ) && in_array( $this->current_embedded_form->id, $this->excluded_ids ) ) {
			$is_excluded = true;
		} else {
			$is_excluded = false;
		}

		return $is_excluded;
	}

	/**
	 * Checks if the type (or field option) of a field or its parent (embedded form or section) is excluded.
	 *
	 * @param stdClass $field
	 *
	 * @return bool
	 */
	private function is_self_or_parent_excluded_type( $field ) {
		if ( $this->is_excluded_type( $field ) ) {
			$is_excluded = true;
		} elseif ( is_object( $this->current_section ) && $this->is_excluded_type( $this->current_section ) ) {
			$is_excluded = true;
		} elseif ( is_object( $this->current_embedded_form ) && $this->is_excluded_type( $this->current_embedded_form ) ) {
			$is_excluded = true;
		} else {
			$is_excluded = false;
		}

		return $is_excluded;
	}
}
