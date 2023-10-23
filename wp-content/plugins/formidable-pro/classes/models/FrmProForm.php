<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

class FrmProForm {

	/**
	 * Modifies form options when updating or creating.
	 *
	 * @since 5.4 Added the third param.
	 *
	 * @param array $options Form options.
	 * @param array $values  Form data.
	 * @param bool  $update  Is form updating or creating. It's `true` if is updating.
	 * @return array
	 */
	public static function update_options( $options, $values, $update = false ) {
		self::fill_option_defaults( $options, $values );

		if ( isset( $values['id'] ) ) {
			self::setup_file_protection(
				array(
					'new'     => $options['protect_files'],
					'form_id' => $values['id'],
				)
			);
		}

		if ( isset( $options['draft_label'] ) ) {
			$options['draft_label'] = sanitize_text_field( $options['draft_label'] );
		}

		if ( isset( $options['edit_value'] ) ) {
			$options['edit_value'] = sanitize_text_field( $options['edit_value'] );
		}

		if ( is_callable( 'FrmAppHelper::maybe_filter_array' ) ) {
			$options = FrmAppHelper::maybe_filter_array( $options, array( 'edit_msg', 'draft_msg' ) );
		}

		$options['single_entry'] = ( isset( $values['options']['single_entry'] ) ) ? $values['options']['single_entry'] : 0;
		if ( $options['single_entry'] ) {
			$options['single_entry_type'] = ( isset( $values['options']['single_entry_type'] ) ) ? $values['options']['single_entry_type'] : 'cookie';
		}

		if ( is_multisite() ) {
			$options['copy'] = isset( $values['options']['copy'] ) ? $values['options']['copy'] : 0;
		}

		if ( $update ) {
			self::maybe_add_start_over_shortcode( $options );
		}

		return $options;
	}

	/**
	 * Maybe add start over button shortcode to the Submit button setting if it's missing.
	 *
	 * @since 5.4
	 *
	 * @param array $options Form options.
	 */
	private static function maybe_add_start_over_shortcode( &$options ) {
		if ( empty( $options['start_over'] ) || false !== strpos( $options['submit_html'], '[if start_over]' ) ) {
			return;
		}

		$start_over_shortcode   = FrmFormsHelper::get_start_over_shortcode();
		$options['submit_html'] = preg_replace( '~\<\/div\>(?!.*\<\/div\>)~', $start_over_shortcode . "\r\n</div>", $options['submit_html'] );
	}

	/**
	 * @since 2.02
	 */
	private static function fill_option_defaults( &$options, $values ) {
		$defaults = FrmProFormsHelper::get_default_opts();
		unset( $defaults['logged_in'], $defaults['editable'] );

		foreach ( $defaults as $opt => $default ) {
			$options[ $opt ] = ( isset( $values['options'][ $opt ] ) ) ? $values['options'][ $opt ] : $default;

			unset( $opt, $default );
		}
	}

	/**
	 * Turn on and off file protection for this form folder
	 *
	 * @param array $atts
	 * @since 2.02
	 */
	private static function setup_file_protection( $atts ) {
		if ( ! self::file_protection_setting_was_updated( $atts ) ) {
			return;
		}

		$form_id          = absint( $atts['form_id'] );
		$file_ids         = self::get_all_file_ids_for_form( $form_id );
		$file_folders     = self::get_all_file_folders_for_form( $file_ids );
		$htaccess_folders = self::get_file_folders_for_form_that_should_have_htaccess( $file_ids );
		$upload_dir       = trailingslashit( wp_upload_dir()['basedir'] );

		foreach ( $file_folders as $folder ) {
			if ( in_array( $folder, $htaccess_folders, true ) ) {
				$folder_name = str_replace( $upload_dir, '', $folder );
				self::maybe_update_htaccess_file( $folder_name, $atts['new'] );
			}
			$args = array(
				'form_id'   => $form_id,
				'file_ids'  => $file_ids,
				'dir'       => $folder,
				'protected' => $atts['new'],
			);
			FrmProFileField::maybe_set_chmod( $args );
		}
	}

	/**
	 * @param array
	 * @return bool
	 */
	private static function file_protection_setting_was_updated( $atts ) {
		$previous_val = FrmProFileField::get_option( $atts['form_id'], 'protect_files', 0 );
		return $previous_val != $atts['new'];
	}

	private static function get_file_folders_for_form_that_should_have_htaccess( $file_ids ) {
		return array_filter( self::get_all_file_folders_for_form( $file_ids ), self::class . '::file_folder_should_have_htaccess' );
	}

	/**
	 * @param string $file_folder
	 * @return bool
	 */
	private static function file_folder_should_have_htaccess( $file_folder ) {
		$formidable_uploads_dir = FrmProFileField::default_formidable_uploads_dir();
		$dir_length             = strlen( $formidable_uploads_dir );
		if ( strlen( $file_folder ) < $dir_length ) {
			return false;
		}
		return $formidable_uploads_dir === substr( $file_folder, 0, $dir_length );
	}

	/**
	 * @param array<int> $file_ids
	 * @return array<string>
	 */
	private static function get_all_file_folders_for_form( $file_ids ) {
		$file_folders = array();
		foreach ( $file_ids as $file_id ) {
			$path                 = get_attached_file( $file_id );
			$dir                  = dirname( $path );
			$file_folders[ $dir ] = $dir;
		}
		return array_values( $file_folders );
	}

	/**
	 * @param int $form_id
	 * @return array
	 */
	private static function get_all_file_ids_for_form( $form_id ) {
		$child_form_ids = self::get_child_form_ids( $form_id );
		$all_form_ids   = array_merge( array( $form_id ), $child_form_ids );
		$file_field_ids = FrmDb::get_col( 'frm_fields', array( 'form_id' => $all_form_ids, 'type' => 'file' ) );

		if ( ! $file_field_ids ) {
			return array();
		}

		$file_data = FrmDb::get_col( 'frm_item_metas', array( 'field_id' => $file_field_ids ), 'meta_value' );
		$file_ids  = array();
		foreach ( $file_data as $meta_value ) {
			FrmProAppHelper::unserialize_or_decode( $meta_value );
			$file_ids = array_merge( $file_ids, (array) $meta_value );
		}

		return $file_ids;
	}

	/**
	 * @param string $folder_name
	 * @param bool $deny
	 */
	private static function maybe_update_htaccess_file( $folder_name, $deny ) {
		if ( ! FrmProFileField::server_supports_htaccess() ) {
			return;
		}

		self::create_folder_if_it_does_not_already_exist( $folder_name );

		$content     = $deny ? "Deny from all\r\n" : "\r\n";
		$create_file = new FrmCreateFile(
			array(
				'folder_name'   => $folder_name,
				'file_name'     => '.htaccess',
				'error_message' => sprintf( __( 'Unable to write to %s to protect your uploads.', 'formidable-pro' ), $folder_name . '/.htaccess' ),
			)
		);
		$create_file->create_file( $content );
	}

	private static function create_folder_if_it_does_not_already_exist( $folder_name ) {
		new FrmCreateFile( array( 'folder_name' => $folder_name, 'file_name' => '' ) );
	}

	/**
	 * Generate the content for an htaccess file to block any direct file access to a protected form's folder
	 * This is only called on Apache servers as an extra layer of security to prevent file access
	 * Without this file, the chmod code will set prevent access to the files
	 *
	 * @since 2.02
	 */
	public static function get_htaccess_content( &$content ) {
		$url = home_url();
		$url = str_replace( array( 'http://', 'https://' ), '', $url );

		$content .= 'RewriteEngine on' . "\r\n";
		$content .= 'RewriteCond %{HTTP_REFERER} !^http(s)?://(www\.)?' . $url . '/.*$ [NC]' . "\r\n";
		$content .= 'RewriteRule \.*$ - [F]' . "\r\n";
	}

	public static function save_wppost_actions( $settings, $action ) {
		$form_id = $action['menu_order'];

		if ( isset($settings['post_custom_fields']) ) {
			foreach ( $settings['post_custom_fields'] as $cf_key => $n ) {
				if ( ! isset($n['custom_meta_name']) ) {
					continue;
				}

				if ( $n['meta_name'] == '' && $n['custom_meta_name'] != '' ) {
					$settings['post_custom_fields'][ $cf_key ]['meta_name'] = $n['custom_meta_name'];
				}

				unset( $settings['post_custom_fields'][ $cf_key ]['custom_meta_name'] );

				unset($cf_key, $n);
			}
		}

		self::create_post_category_field( $settings, $form_id );
		self::create_post_status_field( $settings, $form_id );
		return $settings;
	}

	private static function create_post_category_field( array &$settings, $form_id ) {
		if ( ! isset($settings['post_category']) || ! $settings['post_category'] ) {
			return;
		}

		foreach ( $settings['post_category'] as $k => $field_name ) {
			if ( $field_name['field_id'] != 'checkbox' ) {
				continue;
			}

			//create a new field
			$new_values = apply_filters('frm_before_field_created', FrmFieldsHelper::setup_new_vars('checkbox', $form_id));
			$new_values['field_options']['taxonomy'] = isset($field_name['meta_name']) ? $field_name['meta_name'] : 'category';
			$new_values['name'] = ucwords(str_replace('_', ' ', $new_values['field_options']['taxonomy']));
			$new_values['field_options']['post_field'] = 'post_category';
			$new_values['field_options']['exclude_cat'] = isset($field_name['exclude_cat']) ? $field_name['exclude_cat'] : 0;

			$settings['post_category'][ $k ]['field_id'] = FrmField::create( $new_values );

			unset($new_values, $k, $field_name);
		}
	}

	private static function create_post_status_field( array &$settings, $form_id ) {
		if ( ! isset($settings['post_status']) || 'dropdown' != $settings['post_status'] ) {
			return;
		}

		//create a new field
		$new_values = apply_filters('frm_before_field_created', FrmFieldsHelper::setup_new_vars('select', $form_id));
		$new_values['name'] = __( 'Status', 'formidable-pro' );
		$new_values['field_options']['post_field'] = 'post_status';
		$new_values['field_options']['separate_value'] = 1;
		$new_values['options'] = FrmProFieldsHelper::get_initial_post_status_options();
		$settings['post_status'] = FrmField::create( $new_values );
	}

	public static function update_form_field_options( $field_options, $field ) {
		$field_options['post_field']   = '';
		$field_options['custom_field'] = '';
		$field_options['taxonomy']     = 'category';
		$field_options['exclude_cat']  = 0;

		$action_name = apply_filters( 'frm_save_post_name', 'wppost', $field );
		$post_action = FrmFormAction::get_action_for_form( $field->form_id, $action_name, 1 );
		if ( ! $post_action ) {
			return $field_options;
		}

		$post_fields = array(
			'post_content', 'post_excerpt', 'post_title',
			'post_name', 'post_date', 'post_status', 'post_password',
		);

		$this_post_field = array_search($field->id, $post_action->post_content);
		if ( in_array($this_post_field, $post_fields) ) {
			$field_options['post_field'] = $this_post_field;
		}
		if ( $this_post_field == 'post_status' ) {
			$field_options['separate_value'] = 1;
		}
		unset($this_post_field);

		//Set post categories
		foreach ( (array) $post_action->post_content['post_category'] as $field_name ) {
			if ( ! isset($field_name['field_id']) || $field_name['field_id'] != $field->id ) {
				continue;
			}

			$field_options['post_field'] = 'post_category';
			$field_options['taxonomy'] = isset($field_name['meta_name']) ? $field_name['meta_name'] : 'category';
			$field_options['exclude_cat'] = isset($field_name['exclude_cat']) ? $field_name['exclude_cat'] : 0;
		}

		//Set post custom fields
		foreach ( (array) $post_action->post_content['post_custom_fields'] as $field_name ) {
			if ( ! isset($field_name['field_id']) || $field_name['field_id'] != $field->id ) {
				continue;
			}

			$field_options['post_field'] = 'post_custom';
			$field_options['custom_field'] = ( $field_name['meta_name'] == '' && isset($field_name['custom_meta_name']) && $field_name['custom_meta_name'] != '' ) ? $field_name['custom_meta_name'] : $field_name['meta_name'];
		}

		return $field_options;
	}

	public static function update( $id, $values ) {
		global $wpdb;

		$action = FrmAppHelper::get_param( 'frm_action', '', 'post', 'sanitize_text_field' );
		if ( ! isset( $values['options'] ) || $action !== 'update_settings' ) {
			return;
		}

		$logged_in = isset( $values['logged_in'] ) ? $values['logged_in'] : 0;
		$editable = isset( $values['editable'] ) ? $values['editable'] : 0;
		$updated = $wpdb->update(
			$wpdb->prefix . 'frm_forms',
			array(
				'logged_in' => $logged_in,
				'editable' => $editable,
			),
			array( 'id' => $id )
		);

		if ( $updated ) {
			FrmForm::clear_form_cache();
			unset( $updated );
		}
	}

	/**
	 * @param array $new_opts
	 * @param int   $form_id
	 * @return array
	 */
	public static function after_duplicate( $new_opts, $form_id = 0 ) {
		self::maybe_switch_submit_condition_field_ids( $new_opts );

		if ( isset( $new_opts['success_url'] ) ) {
			$new_opts['success_url'] = FrmFieldsHelper::switch_field_ids( $new_opts['success_url'] );
		}
		if ( isset( $new_opts['rootline_titles'] ) ) {
			$new_opts['rootline_titles'] = self::switch_rootline_field_id_keys( $new_opts['rootline_titles'] );
		}
		if ( $form_id ) {
			self::maybe_fix_conditional_logic_after_duplicate( $form_id );
		}
		return $new_opts;
	}

	private static function maybe_switch_submit_condition_field_ids( &$new_opts ) {
		if ( ! empty( $new_opts['submit_conditions']['hide_field'] ) ) {
			global $frm_duplicate_ids;
			foreach ( $new_opts['submit_conditions']['hide_field'] as $key => $val ) {
				if ( isset( $frm_duplicate_ids[ $val ] ) ) {
					$new_opts['submit_conditions']['hide_field'][ $key ] = $frm_duplicate_ids[ $val ];
				}
			}
		}
	}

	/**
	 * Update the conditional logic for fields that depend on other fields that were not already duplicated.
	 *
	 * @param int $form_id the new duplicated form id.
	 */
	private static function maybe_fix_conditional_logic_after_duplicate( $form_id ) {
		global $frm_duplicate_ids;
		global $frm_unprocessed_duplicate_field_keys;

		if ( ! $frm_unprocessed_duplicate_field_keys ) {
			return;
		}

		$where  = array(
			'fi.field_key' => $frm_unprocessed_duplicate_field_keys,
			'fi.form_id'   => $form_id,
		);
		$fields = FrmField::getAll( $where, 'field_order' );
		foreach ( $fields as $field ) {
			if ( empty( $field->field_options['hide_field'] ) ) {
				continue;
			}

			$updated = false;
			foreach ( $field->field_options['hide_field'] as $key => $field_id ) {
				if ( isset( $frm_duplicate_ids[ $field_id ] ) ) {
					$field->field_options['hide_field'][ $key ] = $frm_duplicate_ids[ $field_id ];
					$updated                                    = true;
				}
			}

			if ( $updated ) {
				FrmField::update( $field->id, array( 'field_options' => $field->field_options ) );
			}
		}

		$frm_unprocessed_duplicate_field_keys = array();
	}

	/**
	 * @param array $original_titles
	 * @return array updated titles, indexed by the new duplicated page break field ids.
	 */
	private static function switch_rootline_field_id_keys( $original_titles ) {
		global $frm_duplicate_ids;
		$duplicate_ids = (array) $frm_duplicate_ids;

		$updated_titles = array();
		foreach ( $original_titles as $key => $value ) {
			$use_key                    = isset( $duplicate_ids[ $key ] ) ? $duplicate_ids[ $key ] : $key;
			$updated_titles[ $use_key ] = $value;
		}
		return $updated_titles;
	}

	public static function has_fields_with_conditional_logic( $form ) {
		$has_no_logic = '"hide_field";a:0:{}';
		$sub_fields = FrmDb::get_var( 'frm_fields', array( 'field_options not like' => $has_no_logic, 'form_id' => $form->id ) );
		return ! empty( $sub_fields );
	}

	/**
	 * Check if the "Submit this form with AJAX" setting is toggled on.
	 *
	 * @param stdClass $form
	 * @return int
	 */
	public static function is_ajax_on( $form ) {
		$ajax = isset( $form->options['ajax_submit'] ) ? $form->options['ajax_submit'] : 0;
		return $ajax;
	}

	/**
	 * @since 3.04
	 *
	 * @param object $form
	 * @return bool
	 */
	public static function is_open( $form ) {
		$options = $form->options;

		if ( ! isset( $options['open_status'] ) || empty( $options['open_status'] ) ) {
			return true;
		}

		if ( $options['open_status'] === 'closed' ) {
			return false;
		}

		if ( strpos( $options['open_status'], 'schedule' ) !== false ) {
			$is_started = self::has_time_passed( $options['open_date'], true );
			$is_ended   = self::has_time_passed( $options['close_date'], false );
			$is_open    = $is_started && ! $is_ended;

			if ( ! $is_open ) {
				return false;
			}
		}

		if ( strpos( $options['open_status'], 'limit' ) !== false && ! empty( $options['max_entries'] ) ) {
			$count = FrmEntry::getRecordCount( $form->id );
			return ( (int) $count < (int) $options['max_entries'] );
		}

		return true;
	}


	/**
	 * @since 3.04
	 *
	 * @param string $time
	 * @param bool $if_blank - If no time is set, should it default to passed?
	 * @return bool
	 */
	private static function has_time_passed( $time, $if_blank ) {
		return empty( $time ) ? $if_blank : ( current_time( 'timestamp' ) > strtotime( $time ) );
	}

	public static function validate( $errors, $values ) {
		// add a user id field if the form requires one
		if ( isset( $values['logged_in'] ) || isset( $values['editable'] ) || ( isset( $values['single_entry'] ) && isset( $values['options']['single_entry_type'] ) && $values['options']['single_entry_type'] == 'user' ) || ( isset( $values['options']['save_draft'] ) && $values['options']['save_draft'] == 1 ) ) {
			$form_id = $values['id'];

			$user_field = FrmField::get_all_types_in_form($form_id, 'user_id', 1);
			if ( ! $user_field ) {
				$new_values = FrmFieldsHelper::setup_new_vars('user_id', $form_id);
				$new_values['name'] = __( 'User ID', 'formidable-pro' );
				FrmField::create($new_values);
			}
		}

		return $errors;
	}

	/**
	 * @param int $form_id
	 * @return array
	 */
	public static function get_child_form_ids( $form_id ) {
		$form_ids       = array();
		$child_form_ids = FrmDb::get_col( 'frm_forms', array( 'parent_form_id' => $form_id ) );
		if ( $child_form_ids ) {
			$form_ids = $child_form_ids;
		}
		return array_filter( $form_ids, 'is_numeric' );
	}

	/**
	 * @param int $id form id
	 * @param array $map associative array mapped like $previous_value => $new_value
	 */
	private static function maybe_fix_submit_button_conditions( $id, $map ) {
		$form = FrmForm::getOne( $id );

		if ( empty( $form->options['submit_conditions'] ) ) {
			return;
		}

		// refactor submit condition logic so it is easier to work with
		$submit_conditions = array();
		if ( isset( $form->options['submit_conditions']['hide_field'] ) ) {
			foreach ( $form->options['submit_conditions']['hide_field'] as $index => $field_id ) {
				if ( ! isset( $submit_conditions[ $field_id ] ) ) {
					$submit_conditions[ $field_id ] = array();
				}

				$submit_conditions[ $field_id ][] = array(
					'cond' => $form->options['submit_conditions']['hide_field_cond'][ $index ],
					'opt'  => $form->options['submit_conditions']['hide_opt'][ $index ],
				);
			}
		}

		$updated = false;
		foreach ( $map as $field_id => $values ) {
			if ( ! isset( $submit_conditions[ $field_id ] ) ) {
				continue;
			}

			foreach ( $submit_conditions[ $field_id ] as $index => $condition ) {
				if ( isset( $values[ $condition['opt'] ] ) ) {
					$form->options['submit_conditions']['hide_opt'][ $index ] = $values[ $condition['opt'] ];
					$updated = true;
				}
			}
		}

		if ( $updated ) {
			FrmForm::update( $id, array( 'options' => $form->options ) );
		}
	}

	/**
	 * @param int $id form id
	 * @param array $map associative array mapped like $field_id => array( $previous_value => $new_value )
	 */
	private static function maybe_fix_action_conditions( $id, $map ) {
		$actions = FrmFormAction::get_action_for_form( $id );

		foreach ( $actions as $action ) {
			if ( empty( $action->post_content['conditions'] ) ) {
				continue;
			}

			$updated = false;
			foreach ( $action->post_content['conditions'] as $index => $condition ) {
				if ( ! is_array( $condition ) ) {
					// skip data like [send_stop] => send or [any_all] => any
					continue;
				}

				if ( ! isset( $map[ $condition['hide_field'] ] ) ) {
					// condition is not for any updated fields
					continue;
				}

				if ( isset( $map[ $condition['hide_field'] ][ $condition['hide_opt'] ] ) ) {
					$action->post_content['conditions'][ $index ]['hide_opt'] = $map[ $condition['hide_field'] ][ $condition['hide_opt'] ];
					$updated = true;
				}
			}

			if ( $updated ) {
				global $wpdb;
				$wpdb->update( $wpdb->posts, array( 'post_content' => wp_json_encode( $action->post_content ) ), array( 'ID' => $action->ID ) );
			}
		}
	}

	/**
	 * Conditional Logic sometimes relies on specifies radio / checkbox answers that can change
	 * Try to change the Conditional Logic that might have broken
	 *
	 * @param int $id form id
	 * @param array $map associative array mapped like $field_id => array( $previous_value => $new_value )
	 */
	public static function maybe_fix_conditions( $id, $map ) {
		$field_ids = array_keys( $map );

		// filter the map to exclude anything that hasn't changed
		$map = array_reduce(
			$field_ids,
			function( $total, $field_id ) use ( $map ) {
				$current = $map[ $field_id ];
				$current = array_filter(
					$current,
					function( $value, $key ) {
						return $value !== $key;
					},
					ARRAY_FILTER_USE_BOTH
				);

				if ( $current ) {
					$total[ $field_id ] = $current;
				}

				return $total;
			},
			array()
		);

		if ( $map ) {
			self::maybe_fix_submit_button_conditions( $id, $map );
			self::maybe_fix_action_conditions( $id, $map );
		}
	}
}
