<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

/**
 * @since 3.0
 */
class FrmProFieldData extends FrmFieldType {

	/**
	 * @var string
	 * @since 3.0
	 */
	protected $type = 'data';

	protected function input_html() {
		return $this->multiple_input_html();
	}

	protected function include_form_builder_file() {
		return FrmProAppHelper::plugin_path() . '/classes/views/frmpro-fields/back-end/field-' . $this->type . '.php';
	}

	protected function field_settings_for_type() {
		$settings = array();

		if ( $this->field && isset( $this->field->field_options['data_type'] ) ) {
			$settings['default_value'] = true;
			$settings['read_only'] = true;
			$settings['unique'] = true;

			switch ( $this->field->field_options['data_type'] ) {
				case 'data':
					$settings['required'] = false;
					$settings['read_only'] = false;
					$settings['unique'] = false;
					break;
				case 'select':
					$settings['size'] = true;
					$settings['clear_on_focus'] = true;
			}
		}

		FrmProFieldsHelper::fill_default_field_display( $settings );
		return $settings;
	}

	protected function extra_field_opts() {
		return array(
			'data_type' => 'select',
			'restrict' => 0,
			'option_order' => 'ascending',
		);
	}

	/**
	 * @since 4.0
	 * @param array $args - Includes 'field', 'display', and 'values'
	 */
	public function show_primary_options( $args ) {
		$field = $args['field'];
		$field_types = array(
			'select'    => __( 'Dropdown', 'formidable-pro' ),
			'radio'     => __( 'Radio Buttons', 'formidable-pro' ),
			'checkbox'  => __( 'Checkboxes', 'formidable-pro' ),
			'data'      => __( 'List', 'formidable-pro' ),
		);

		include( FrmProAppHelper::plugin_path() . '/classes/views/frmpro-fields/back-end/dynamic-field.php' );

		parent::show_primary_options( $args );
	}

	/**
	 * @since 4.0
	 * @param array $args - Includes 'field', 'display', and 'values'
	 */
	public function show_extra_field_choices( $args ) {
		$field     = $args['field'];
		$data_type = FrmField::get_option( $field, 'data_type' );
		$form_list = FrmForm::get_published_forms();

		if ( empty( $form_list ) ) {
			return;
		}

		$selected_field   = '';
		$selected_form_id = '';
		$current_field_id = $field['id'];
		if ( isset( $field['form_select'] ) && is_numeric( $field['form_select'] ) ) {
			$selected_field = FrmField::getOne( $field['form_select'] );
			if ( $selected_field ) {
				$selected_form_id = FrmProFieldsHelper::get_parent_form_id( $selected_field );
				$fields           = FrmProFieldsController::get_field_selection_fields( $selected_form_id );
			} else {
				$selected_field = '';
			}
		} elseif ( isset( $field['form_select'] ) ) {
			$selected_field = $field['form_select'];
		}

		include( FrmProAppHelper::plugin_path() . '/classes/views/frmpro-fields/options-form-before.php' );

		if ( $data_type === 'select' ) {
			include( FrmProAppHelper::plugin_path() . '/classes/views/frmpro-fields/back-end/multi-select.php' );

			$this->auto_width_setting( $args );
		}
	}

	public function prepare_front_field( $values, $atts ) {
		$data_type = FrmField::get_option( $this->field, 'data_type' );
		$form_select = FrmField::get_option( $this->field, 'form_select' );
		if ( in_array( $data_type, array( 'select', 'radio', 'checkbox' ) ) && is_numeric( $form_select ) ) {
			$entry_id = isset( $atts['entry_id'] ) ? $atts['entry_id'] : 0;
			FrmProDynamicFieldsController::add_options_for_dynamic_field( $this->field, $values, array( 'entry_id' => $entry_id ) );
		}
		return $values;
	}

	/**
	 * Remove the frm_opt_container class for dropdowns
	 */
	protected function after_replace_html_shortcodes( $args, $html ) {
		$data_type = FrmField::get_option( $this->field, 'data_type' );
		if ( 'select' === $data_type ) {
			$html = str_replace( '"frm_opt_container', '"frm_data_container', $html );
		}
		return $html;
	}

	/**
	 * @since 3.0
	 */
	protected function prepare_display_value( $value, $atts ) {
		if ( ! isset( $this->field->field_options['form_select'] ) || $this->field->field_options['form_select'] == 'taxonomy' ) {
			return $value;
		}

		$atts['show'] = isset( $atts['show'] ) ? $atts['show'] : false;

		if ( ! empty( $value ) && ! is_array( $value ) && strpos( $value, $atts['sep'] ) !== false ) {
			$value = explode( $atts['sep'], $value );
		}

		if ( $atts['show'] == 'id' ) {
			// keep the values the same since we already have the ids
			return (array) $value;
		}

		$show_opts = array( 'key', 'created-at', 'created_at', 'updated-at', 'updated_at, updated-by, updated_by', 'post_id' );
		if ( in_array( $atts['show'], $show_opts ) ) {
			$value = $this->get_show_value( $value, $atts );
		} else {
			$value = $this->get_data_value( $value, $atts );
		}

		return $value;
	}

	/**
	 * @since 3.0
	 */
	private function get_show_value( $linked_ids, $atts ) {
		$nice_show = str_replace( '-', '_', $atts['show'] );

		$value = array();
		foreach ( (array) $linked_ids as $linked_id ) {
			$linked_entry = FrmEntry::getOne( $linked_id );

			if ( isset( $linked_entry->{$atts['show']} ) ) {
				$value[] = $linked_entry->{$atts['show']};
			} else if ( isset( $linked_entry->{$nice_show} ) ) {
				$value[] = $linked_entry->{$nice_show};
			} else {
				$value[] = $linked_entry->item_key;
			}
		}
		return $value;
	}

	/**
	 * @since 3.0
	 */
	private function get_data_value( $linked_ids, $atts ) {
		$value = array();

		if ( ! empty( $linked_ids ) ) {
			if ( is_array( $linked_ids ) ) {
				foreach ( $linked_ids as $linked_id ) {
					$new_val = $this->get_single_data_value( $linked_id, $atts );
					if ( $new_val !== false ) {
						$value[] = $new_val;
					}

					unset( $new_val, $linked_id );
				}
				$value = array_filter(
					$value,
					function( $item ) {
						if ( null === $item ) {
							return false;
						}

						return strlen( $item );
					}
				);
			} else {
				$value = $this->get_single_data_value( $linked_ids, $atts );
			}
		}
		return $value;
	}

	private function get_single_data_value( $linked_id, $atts ) {
		$atts['includes_list_data'] = true;
		$value = FrmProFieldsHelper::get_data_value( $linked_id, $this->field, $atts );

		if ( $linked_id === $value && ! FrmProField::is_list_field( $this->field ) ) {
			$value = false;
		} elseif ( is_array( $value ) ) {
			$value = implode( $atts['sep'], $value );
		}

		return $value;
	}

	public function get_container_class() {
		$class = parent::get_container_class();
		return $class . ' frm_dynamic_' . $this->field['data_type'] . '_container';
	}

	protected function include_front_form_file() {
		return FrmProAppHelper::plugin_path() . '/classes/views/frmpro-fields/data-options.php';
	}

	/**
	 * Override parent to check if options are empty.
	 * TODO: Why is this needed?
	 */
	protected function maybe_include_hidden_values( $args ) {
		$hidden = '';
		$field = isset( $args['field'] ) ? $args['field'] : $this->field;
		$is_read_only = empty( $field['options'] ) || ( FrmField::is_read_only( $this->field ) && ! FrmAppHelper::is_admin() );
		if ( $is_read_only && $this->show_readonly_hidden() ) {
			$hidden = $this->show_hidden_values( $args );
		}
		return $hidden;
	}


	protected function show_readonly_hidden() {
		return in_array( FrmField::get_option( $this->field, 'data_type' ), array( 'select', 'radio', 'checkbox' ) );
	}

	protected function is_readonly_array() {
		return FrmField::get_option( $this->field, 'data_type' ) == 'checkbox';
	}

	/**
	 * Get the entry IDs for a value imported in a Dynamic field
	 *
	 * @since 3.0
	 *
	 * @param array|string|int $value
	 * @param array            $atts
	 *
	 * @return array|string|int
	 */
	protected function prepare_import_value( $value, $atts ) {
		if ( ! $this->field || FrmProField::is_list_field( $this->field ) ) {
			return $value;
		}

		$value = FrmProXMLHelper::convert_imported_value_to_array( $value );
		$this->switch_imported_entry_ids( $atts['ids'], $value );

		if ( count( $value ) <= 1 ) {
			$value = reset( $value );

			$target_field_id = $this->field->field_options['form_select'];
			$target_field    = FrmField::getOne( $target_field_id );
			if ( FrmField::get_option( $target_field, 'post_field' ) ) {
				$value = $this->get_post_field_import_value( $value, $target_field );
			} else {
				$object  = FrmFieldFactory::get_field_object( $target_field );
				$options = $object->get_options( array() );

				if ( is_array( $options ) ) {
					$key = array_search( $value, $options );

					if ( false !== $key ) {
						$where   = array(
							'meta_value' => $key,
							'field_id'   => $target_field_id,
						);
						$item_id = FrmDb::get_var( 'frm_item_metas', $where, 'item_id' );
						if ( $item_id ) {
							$value = $item_id;
						}
					}
				}
			}
		} else {
			$value = array_map( 'trim', $value );
		}

		return $value;
	}

	/**
	 * Gets post field import value.
	 *
	 * @since 5.0.02
	 *
	 * @param string|int $value The value before processing.
	 * @param object     $target_field The target field object.
	 * @return int|string
	 */
	protected function get_post_field_import_value( $value, $target_field ) {
		$post_field = FrmField::get_option( $target_field, 'post_field' );

		if ( 'post_custom' === $post_field ) {
			$meta_key = FrmField::get_option( $target_field, 'custom_field' );

			if ( ! $meta_key ) {
				return $value;
			}

			if ( '_thumbnail_id' === $meta_key && ! is_numeric( $value ) ) {
				$value = $this->get_attachment_id_from_url( $value, $target_field );
			}

			$item_id = $this->get_item_id_from_post_custom_field( $meta_key, $value );
		} else {
			$item_id = $this->get_item_id_from_post_field( $post_field, $value );
		}

		if ( $item_id ) {
			return $item_id;
		}

		return $value;
	}

	/**
	 * Gets attachment ID from URL.
	 *
	 * @since 5.0.02
	 *
	 * @param string   $value Attachment URL.
	 * @param stdClass $field The file upload field.
	 * @return string
	 */
	protected function get_attachment_id_from_url( $value, $field ) {
		add_filter( 'frm_should_import_files', 'FrmProFileImport::allow_file_import' );
		$value = FrmProFileImport::import_attachment( $value, $field );
		remove_filter( 'frm_should_import_files', 'FrmProFileImport::allow_file_import' );
		return $value;
	}

	/**
	 * Gets item ID from the post custom field.
	 *
	 * @since 5.0.02
	 *
	 * @param string $meta_key The meta key.
	 * @param string $value    The meta value.
	 * @return int Return `0` if post not found.
	 */
	protected function get_item_id_from_post_custom_field( $meta_key, $value ) {
		global $wpdb;

		$sql = "SELECT items.id FROM {$wpdb->posts} AS posts
INNER JOIN {$wpdb->prefix}frm_items as items ON posts.ID = items.post_id
INNER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id
WHERE postmeta.meta_key = %s and postmeta.meta_value = %s";

		$item_id = $wpdb->get_var( $wpdb->prepare( $sql, $meta_key, $value ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		return intval( $item_id );
	}

	/**
	 * Gets item ID from the post field.
	 *
	 * @since 5.0.02
	 *
	 * @param string $post_field The post field name.
	 * @param string $value      The post field value.
	 * @return int Return `0` if post not found.
	 */
	protected function get_item_id_from_post_field( $post_field, $value ) {
		global $wpdb;

		$post_field = esc_sql( $post_field );
		$sql        = "SELECT items.id FROM {$wpdb->posts} AS posts
INNER JOIN {$wpdb->prefix}frm_items AS items ON posts.ID = items.post_id WHERE posts.{$post_field} = %s";

		$item_id = $wpdb->get_var( $wpdb->prepare( $sql, $value ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		return intval( $item_id );
	}

	/**
	 * Switch the old entry IDs imported to new entry IDs for a Dynamic field
	 *
	 * @since 3.0
	 *
	 * @param array $imported_values
	 */
	private function switch_imported_entry_ids( $ids, &$imported_values ) {
		if ( ! is_array( $imported_values ) ) {
			return;
		}

		foreach ( $imported_values as $key => $imported_value ) {

			// This entry was just imported, so we have the id
			if ( is_numeric( $imported_value ) && isset( $ids[ $imported_value ] ) ) {
				$imported_values[ $key ] = $ids[ $imported_value ];
				continue;
			}

			// Look for the entry ID based on the imported value
			// TODO: this may not be needed for XML imports. It appears to always be the entry ID that's exported
			$where  = array( 'field_id' => $this->field->field_options['form_select'], 'meta_value' => $imported_value );
			$new_id = FrmDb::get_var( 'frm_item_metas', $where, 'item_id' );

			if ( $new_id && is_numeric( $new_id ) ) {
				$imported_values[ $key ] = $new_id;
			}
		}
	}

	/**
	 * @since 6.3.2
	 *
	 * @param array $args Contains 'value' that is submitted.
	 *
	 * @return array The field validation errors.
	 */
	public function validate( $args ) {
		$errors = array();
		if ( $this->field->required && $args['value'] === '0' ) {
			$errors[ 'field' . $args['id'] ] = FrmFieldsHelper::get_error_msg( $this->field, 'blank' );
		}

		return $errors;
	}
}
