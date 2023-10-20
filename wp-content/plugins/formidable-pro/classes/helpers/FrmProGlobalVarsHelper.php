<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

/**
 * @since 5.1
 */
class FrmProGlobalVarsHelper {

	/**
	 * @var FrmProGlobalVarsHelper $helper
	 */
	private static $helper;

	/**
	 * @var array<int> $include_ids
	 */
	private $include_ids;

	/**
	 * @var array<string> $include_keys
	 */
	private $include_keys;

	/**
	 * @var array<int> $exclude_ids
	 */
	private $exclude_ids;

	/**
	 * @var array<string> $exclude_keys
	 */
	private $exclude_keys;

	/**
	 * @var array<int> $included_embedded_form_ids
	 */
	private $included_embedded_form_ids;

	/**
	 * @var array<int> $excluded_embedded_form_ids
	 */
	private $excluded_embedded_form_ids;

	/**
	 * @var array
	 */
	private $embedded_form_field_id_by_form_id;

	/**
	 * @var array<int> $included_section_ids
	 */
	private $included_section_ids;

	/**
	 * @var array<int> $excluded_section_ids
	 */
	private $excluded_section_ids;

	/**
	 * @var array<int> $required_section_ids
	 */
	private $required_section_ids;

	/**
	 * @var array<int> $required_embed_form_ids
	 */
	private $required_embed_form_ids;

	/**
	 * @param bool $force
	 *
	 * @return FrmProGlobalVarsHelper
	 */
	public static function get_instance( $force = false ) {
		if ( ! isset( self::$helper ) || $force ) {
			self::$helper = new self();
		}
		return self::$helper;
	}

	/**
	 * @return void
	 */
	private function __construct() {
		$this->initialize_arrays();
	}

	/**
	 * @return void
	 */
	private function initialize_arrays() {
		$this->include_ids                       = array();
		$this->exclude_ids                       = array();
		$this->include_keys                      = array();
		$this->exclude_keys                      = array();
		$this->excluded_embedded_form_ids        = array();
		$this->included_embedded_form_ids        = array();
		$this->excluded_section_ids              = array();
		$this->included_section_ids              = array();
		$this->embedded_form_field_id_by_form_id = array();
		$this->required_section_ids              = array();
		$this->required_embed_form_ids           = array();
	}

	/**
	 * If fields are excluded in the form shortcode, set the list of all fields
	 * that should be included.
	 *
	 * @param array $atts
	 *
	 * @since 4.03.03
	 * @since 5.1 moved to FrmProGlobalVarsHelper from FrmProFormsController.
	 */
	public function set_included_fields( $atts ) {
		global $frm_vars;

		if ( isset( $atts['fields'] ) && ! is_array( $atts['fields'] ) ) {
			$frm_vars['show_fields'] = array_map(
				function( $field_id_or_key ) {
					return is_numeric( $field_id_or_key ) ? (int) $field_id_or_key : $field_id_or_key;
				},
				explode( ',', $atts['fields'] )
			);
		}

		if ( empty( $atts['exclude_fields'] ) ) {
			$this->adjust_included_fields( $atts );
			if ( ! empty( $frm_vars['show_fields'] ) ) {
				FrmProFormState::set_initial_value( 'include_fields', $frm_vars['show_fields'] );
			}
			return;
		}

		if ( ! is_array( $atts['exclude_fields'] ) ) {
			$atts['exclude_fields'] = explode( ',', $atts['exclude_fields'] );
		}

		$fields = FrmField::get_all_for_form( (int) $atts['id'], '', 'include' );
		list( $this->exclude_ids, $this->exclude_keys ) = FrmProAppHelper::pull_ids_and_keys( $atts['exclude_fields'] );

		$include_ids = array();
		foreach ( $fields as $field ) {
			if ( ! $this->exclude_field( $field ) ) {
				$include_ids[] = (int) $field->id;
			}
			unset( $field );
		}

		$frm_vars['show_fields'] = $include_ids;
		FrmProFormState::set_initial_value( 'include_fields', $frm_vars['show_fields'] );
	}

	/**
	 * @param stdClass|array $field
	 * @return bool
	 */
	public function field_is_visible( $field ) {
		global $frm_vars;
		if ( empty( $frm_vars['show_fields'] ) || ! is_array( $frm_vars['show_fields'] ) ) {
			return true;
		}

		list( $field_id, $field_key ) = $this->get_id_and_key( $field );
		return in_array( $field_id, $frm_vars['show_fields'], true ) || in_array( $field_key, $frm_vars['show_fields'], true );
	}

	/**
	 * Get field id and key from a field object or array.
	 *
	 * @param stdClass|array $field
	 * @return array
	 */
	private function get_id_and_key( $field ) {
		if ( is_object( $field ) ) {
			return array( (int) $field->id, $field->field_key );
		}
		return array( (int) $field['id'], $field['field_key'] );
	}

	/**
	 * @param array $atts
	 * @return void
	 */
	private function adjust_included_fields( $atts ) {
		if ( empty( $atts['fields'] ) ) {
			return;
		}

		global $frm_vars;

		$fields = FrmField::get_all_for_form( (int) $atts['id'], '', 'include' );
		list( $this->include_ids, $this->include_keys ) = FrmProAppHelper::pull_ids_and_keys( $frm_vars['show_fields'] );

		$hidden_sections_and_embedded_forms = array();

		foreach ( $fields as $field ) {
			if ( $this->include_field( $field ) ) {
				if ( ! $this->field_is_visible( $field ) ) {
					// Add fields that should be included but are missing from show_fields.
					$frm_vars['show_fields'][] = (int) $field->id;
				}
			} elseif ( in_array( $field->type, array( 'form', 'divider' ), true ) && ! $this->field_is_visible( $field ) ) {
				$hidden_sections_and_embedded_forms[] = $field;
			}
		}

		// Do a second pass on sections and embedded forms after including children fields.
		foreach ( $hidden_sections_and_embedded_forms as $field ) {
			if ( $this->field_is_required( $field ) ) {
				$frm_vars['show_fields'][] = (int) $field->id;
			}
		}
	}

	/**
	 * @param stdClass $field
	 * @return bool
	 */
	private function field_is_required( $field ) {
		if ( 'form' === $field->type ) {
			return ! empty( $field->field_options['form_select'] ) && in_array( (int) $field->field_options['form_select'], $this->required_embed_form_ids, true );
		}
		if ( 'divider' === $field->type ) {
			return in_array( (int) $field->id, $this->required_section_ids, true );
		}
		return false;
	}

	/**
	 * @param stdClass $field
	 * @return bool
	 */
	private function include_field( $field ) {
		if ( 'form' === $field->type && ! empty( $field->field_options['form_select'] ) ) {
			$this->embedded_form_field_id_by_form_id[ $field->field_options['form_select'] ] = (int) $field->id;
		}

		if ( ! empty( $field->field_options['in_section'] ) && in_array( (int) $field->field_options['in_section'], $this->included_section_ids, true ) ) {
			return true;
		}

		if ( array_key_exists( $field->form_id, $this->included_embedded_form_ids ) ) {
			return true;
		}

		if ( in_array( (int) $field->id, $this->include_ids, true ) || in_array( $field->field_key, $this->include_keys, true ) ) {
			if ( 'divider' === $field->type ) {
				$this->included_section_ids[] = (int) $field->id;
			} elseif ( 'form' === $field->type && ! empty( $field->field_options['form_select'] ) ) {
				$this->included_embedded_form_ids[ (int) $field->field_options['form_select'] ] = (int) $field->id;
			} elseif ( ! empty( $field->field_options['in_section'] ) ) {
				$this->required_section_ids[ $field->field_options['in_section'] ] = (int) $field->field_options['in_section'];
			} elseif ( ! empty( $this->embedded_form_field_id_by_form_id[ $field->form_id ] ) ) {
				$this->required_embed_form_ids[ $field->form_id ] = (int) $field->form_id;
			}
			return true;
		}

		return false;
	}

	/**
	 * @param stdClass $field
	 * @return bool
	 */
	private function exclude_field( $field ) {
		if ( ! empty( $field->field_options['in_section'] ) && in_array( (int) $field->field_options['in_section'], $this->excluded_section_ids, true ) ) {
			return true;
		}

		if ( array_key_exists( $field->form_id, $this->excluded_embedded_form_ids ) ) {
			return true;
		}

		if ( in_array( (int) $field->id, $this->exclude_ids, true ) || in_array( $field->field_key, $this->exclude_keys, true ) ) {
			if ( 'divider' === $field->type ) {
				$this->excluded_section_ids[] = (int) $field->id;
			} elseif ( 'form' === $field->type && ! empty( $field->field_options['form_select'] ) ) {
				$this->excluded_embedded_form_ids[ (int) $field->field_options['form_select'] ] = (int) $field->id;
			}
			return true;
		}

		return false;
	}
}
