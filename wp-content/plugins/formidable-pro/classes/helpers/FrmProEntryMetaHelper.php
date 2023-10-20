<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

class FrmProEntryMetaHelper {

	public static function get_sub_meta_values( $entries, $field, $atts = array() ) {
		$values = array();
		foreach ( $entries as $entry ) {
			$sub_val = self::get_post_or_meta_value($entry, $field, $atts);
			$include_blank = ( isset( $atts['include_blank'] ) && $atts['include_blank'] );
			if ( $sub_val != '' || $include_blank ) {
				$values[ $entry->id ] = $sub_val;
			}
		}

		return $values;
	}

	public static function get_post_or_meta_value( $entry, $field, $atts = array() ) {
		$defaults = array(
			'links'    => true,
			'show'     => '',
			'truncate' => true,
			'sep'      => ', ',
		);
		$atts = wp_parse_args( (array) $atts, $defaults);

		FrmEntry::maybe_get_entry( $entry );

		if ( empty($entry) || empty($field) ) {
			return '';
		}

		if ( $entry->post_id ) {
			if ( ! isset($field->field_options['custom_field']) ) {
				$field->field_options['custom_field'] = '';
			}

			if ( ! isset($field->field_options['post_field']) ) {
				$field->field_options['post_field'] = '';
			}

			$links = $atts['links'];

			if ( $field->type == 'tag' || $field->field_options['post_field'] ) {
				$post_args = array(
					'type' => $field->type, 'form_id' => $field->form_id,
					'field' => $field, 'links' => $links,
					'exclude_cat' => $field->field_options['exclude_cat'],
				);

				foreach ( array( 'show', 'truncate', 'sep' ) as $p ) {
					$post_args[ $p ] = $atts[ $p ];
					unset($p);
				}

				$value = self::get_post_value($entry->post_id, $field->field_options['post_field'], $field->field_options['custom_field'], $post_args);
				unset($post_args);
			} else {
				$value = FrmEntryMeta::get_meta_value( $entry, $field->id );
			}
		} else {
			$value = FrmEntryMeta::get_meta_value( $entry, $field->id );

			self::convert_non_post_taxonomy_ids_to_names( $field, $atts, $value );
		}

		return $value;
	}

	/**
	 * Convert taxonomy IDs to taxonomy names if field is a category field and no post is connected to entry
	 *
	 * @since 2.02.05
	 *
	 * @param object $field
	 * @param array $atts
	 * @param string|array $value
	 */
	private static function convert_non_post_taxonomy_ids_to_names( $field, $atts, &$value ) {
		if ( isset( $field->field_options['post_field'] ) && $field->field_options['post_field'] == 'post_category' && ! empty( $value ) && $atts['truncate'] ) {
			FrmProAppHelper::unserialize_or_decode( $value );

			$new_value = array();
			foreach ( (array) $value as $tax_id ) {
				if ( is_numeric( $tax_id ) ) {
					$new_value[] = FrmProPost::get_taxonomy_term_name_from_id( $tax_id, $field->field_options['taxonomy'] );
				} else {
					$new_value[] = $tax_id;
				}
			}

			$value = $new_value;
		}
	}

	public static function get_post_value( $post_id, $post_field, $custom_field, $atts ) {
		if ( ! $post_id ) {
			return '';
		}
		$post = get_post($post_id);
		if ( ! $post ) {
			return '';
		}

		$defaults = array(
			'sep' => ', ', 'truncate' => true, 'form_id' => false,
			'field' => array(), 'links' => false, 'show' => '',
		);

		$atts = wp_parse_args( $atts, $defaults );

		$value = '';
		if ( $atts['type'] == 'tag' ) {
			if ( isset( $atts['field']->field_options ) ) {
				$field_options = $atts['field']->field_options;
				FrmProAppHelper::unserialize_or_decode( $field_options );
				$tax = isset($field_options['taxonomy']) ? $field_options['taxonomy'] : 'frm_tag';
				$tags = get_the_terms( $post_id, $tax );

				if ( $tags ) {
					$names = array();
					foreach ( $tags as $tag ) {
						self::get_term_with_link( $tag, $tax, $names, $atts );
					}
					$value = implode($atts['sep'], $names);
				}
			}
		} else {
			if ( $post_field == 'post_custom' ) { //get custom post field value
				$value = self::get_post_meta_value( $post_id, $custom_field );
			} else if ( $post_field == 'post_category' ) {
				if ( $atts['form_id'] ) {
					$post_type = FrmProFormsHelper::post_type($atts['form_id']);
					$taxonomy = FrmProAppHelper::get_custom_taxonomy($post_type, $atts['field']);
				} else {
					$taxonomy = 'category';
				}

				$categories = get_the_terms( $post_id, $taxonomy );

				$names = array();
				$cat_ids = array();
				if ( $categories ) {
					foreach ( $categories as $cat ) {
						if ( isset($atts['exclude_cat']) && in_array($cat->term_id, (array) $atts['exclude_cat']) ) {
							continue;
						}

						self::get_term_with_link( $cat, $taxonomy, $names, $atts );

						$cat_ids[] = $cat->term_id;
					}
				}

				if ( $atts['show'] == 'id' ) {
					$value = implode($atts['sep'], $cat_ids);
				} else if ( $atts['truncate'] ) {
					$value = implode($atts['sep'], $names);
				} else {
					$value = $cat_ids;
				}
			} else {
				$post = (array) $post;
				$value = $post[ $post_field ];
			}
		}
		return $value;
	}

	private static function get_post_meta_value( $post_id, $key ) {
		if ( FrmProPost::is_acf_field( $post_id, $key ) ) {
			$value = get_field( substr( $key, 1 ), $post_id );
		} else {
			$value = get_post_meta( $post_id, $key, true );
		}
		return $value;
	}

	private static function get_term_with_link( $tag, $tax, &$names, $atts ) {
		$tag_name = $tag->name;
		if ( $atts['links'] ) {
			$tag_name = '<a href="' . esc_url( get_term_link( $tag, $tax ) ) . '" title="' . esc_attr( sprintf( __( 'View all posts filed under %s', 'formidable-pro' ), $tag_name ) ) . '">' . $tag_name . '</a>';
		}
		$names[] = $tag_name;
	}

	public static function set_post_fields( $field, $value, $errors ) {
		// save file ids for later use
		if ( 'file' == $field->type ) {
			global $frm_vars;
			if ( ! isset($frm_vars['media_id']) ) {
				$frm_vars['media_id'] = array();
			}

			$frm_vars['media_id'][ $field->id ] = $value;
		}

		if ( empty( $value ) || ! FrmField::is_option_true( $field, 'unique' ) ) {
			return $errors;
		}

		$post_form_action = FrmFormAction::get_action_for_form( $field->form_id, 'wppost', 1 );
		if ( ! $post_form_action ) {
			return $errors;
		}

		// check if this is a regular post field
		$post_field = array_search($field->id, $post_form_action->post_content);
		$custom_field = '';

		if ( ! $post_field ) {
			// check if this is a custom field
			foreach ( $post_form_action->post_content['post_custom_fields'] as $custom_field ) {
				if ( isset($custom_field['field_id']) && ! empty($custom_field['field_id']) && isset($custom_field['meta_name']) && ! empty($custom_field['meta_name']) && $field->id == $custom_field['field_id'] ) {
					$post_field = 'post_custom';
					$custom_field = $custom_field['meta_name'];
				}
			}

			if ( ! $post_field ) {
				return $errors;
			}
		}

		// check for unique values in post fields
		$entry_id = ( $_POST && isset( $_POST['id'] ) ) ? $_POST['id'] : false;
		$post_id = false;
		if ( $entry_id ) {
			global $wpdb;
			$post_id = FrmDb::get_var( $wpdb->prefix . 'frm_items', array( 'id' => $entry_id ), 'post_id' );
		}

		if ( self::post_value_exists($post_field, $value, $post_id, $custom_field) ) {
			$errors[ 'field' . $field->id ] = FrmFieldsHelper::get_error_msg( $field, 'unique_msg' );
		}

		return $errors;
	}

	public static function meta_through_join( $hide_field, $selected_field, $observed_field_val, $this_field, &$metas ) {
		if ( is_array($observed_field_val) ) {
			$observed_field_val = array_filter($observed_field_val);
		}

		if ( empty($observed_field_val) || ( ! is_numeric($observed_field_val) && ! is_array($observed_field_val) ) ) {
			return;
		}

		$observed_info = FrmField::getOne($hide_field);

		if ( ! $selected_field || ! $observed_info ) {
			return;
		}

		$form_id = FrmProFieldsHelper::get_parent_form_id($selected_field);
		$join_fields = FrmField::get_all_types_in_form($form_id, 'data');
		if ( empty($join_fields) ) {
			return;
		}

		foreach ( $join_fields as $jf ) {
			if ( isset($jf->field_options['form_select']) && isset($observed_info->field_options['form_select']) && $jf->field_options['form_select'] == $observed_info->field_options['form_select'] ) {
				$join_field = $jf->id;
			}
		}

		if ( ! isset($join_field) ) {
			return;
		}

		$observed_field_val = array_filter( (array) $observed_field_val);
		$query = array( 'field_id' => (int) $join_field );
		$sub_query = array( 'it.meta_value' => $observed_field_val );
		foreach ( $observed_field_val as $obs_val ) {
			$sub_query['or'] = 1;
			$sub_query['it.meta_value LIKE'] = ':"' . $obs_val . '"';
		}
		$query[] = $sub_query;

		if ( $this_field && isset($this_field->field_options['restrict']) && $this_field->field_options['restrict'] ) {
			$query['e.user_id'] = self::get_entry_id_for_dynamic_opts( array( 'field' => $this_field ) );
		}

		// the ids of all the entries that have been selected in the linked form
		$entry_ids = FrmEntryMeta::getEntryIds( $query );

		if ( ! empty($entry_ids) ) {
			if ( $form_id != $selected_field->form_id ) {
				// this is a child field so we need to get the child entries
				global $wpdb;
				$entry_ids = FrmDb::get_col( $wpdb->prefix . 'frm_items', array( 'parent_item_id' => $entry_ids ) );
			}

			if ( ! empty( $entry_ids ) ) {
				$metas = FrmEntryMeta::getAll( array( 'item_id' => $entry_ids, 'field_id' => $selected_field->id ), ' ORDER BY meta_value' );
			}
		}
	}

	private static function get_entry_id_for_dynamic_opts( $atts ) {
		$user_id = get_current_user_id();
		$entry_id = 0;
		if ( FrmAppHelper::is_admin() ) {
			$entry_id = FrmAppHelper::get_param( 'id', 0, 'get', 'absint' );
		} elseif ( FrmAppHelper::doing_ajax() ) {
			$entry_id = FrmAppHelper::get_param( 'editing_entry', 0, 'get', 'absint' );
		}
		$atts['entry_id'] = $entry_id;
		return self::user_for_dynamic_opts( $user_id, $atts );
	}

	public static function user_for_dynamic_opts( $user_id, $atts ) {
		$entry_user = (array) $user_id;
		if ( $atts['entry_id'] ) {
			$entry_owner = FrmDb::get_var( 'frm_items', array( 'id' => $atts['entry_id'] ), 'user_id' );
			if ( $entry_owner ) {
				$entry_user[] = $entry_owner;
			}
		}

		/**
		 * Set the user id(s) for the limited dynamic field options
		 *
		 * @since 2.2.8
		 * @return array|int
		 */
		return apply_filters( 'frm_dynamic_field_user', $entry_user, $atts );
	}

	public static function &value_exists( $field_id, $value, $entry_id = false ) {
		if ( is_object($field_id) ) {
			$field_id = $field_id->id;
		}
		// Makes sure this works when $value is an array
		$value = maybe_serialize( $value );

		$query = array( 'meta_value' => $value, 'field_id' => $field_id );
		if ( $entry_id ) {
			$query['item_id !'] = $entry_id;
		}

		$value = FrmDb::get_var( 'frm_item_metas', $query );

		return $value;
	}

	public static function post_value_exists( $post_field, $value, $post_id, $custom_field = '' ) {
		global $wpdb;
		$query = array( 'post_status' => array( 'publish', 'draft', 'pending', 'future' ) );
		if ( $post_field == 'post_custom' ) {
			$table = $wpdb->postmeta . ' pm LEFT JOIN ' . $wpdb->posts . ' p ON (p.ID=pm.post_id)';
			$db_field = 'post_id';
			$query['meta_value'] = $value;
			$query['meta_key'] = $custom_field;
			if ( $post_id && is_numeric($post_id) ) {
				$query['post_id !'] = $post_id;
			}
		} else {
			$table = $wpdb->posts;
			$db_field = 'ID';
			$query[ $post_field ] = $value;
			if ( $post_id && is_numeric($post_id) ) {
				$query['ID !'] = $post_id;
			}
		}

		return FrmDb::get_var( $table, $query, $db_field );
	}

	public static function &get_max( $field ) {
		if ( ! is_object( $field ) ) {
			$field = FrmField::getOne( $field );
		}

		if ( ! $field ) {
			return '';
		}

		$max = FrmDb::get_var( 'frm_item_metas', array( 'field_id' => $field->id ), 'meta_value', array( 'order_by' => 'item_id DESC' ) );
		$max = self::get_increment_from_value( $max, $field );

		if ( self::field_supports_post_autoid( $field ) ) {
			$post_max = self::get_post_max_value( $field );
			if ( $post_max ) {
				$post_max = self::get_increment_from_value( $post_max, $field );
				if ( (float) $post_max > (float) $max ) {
					$max = $post_max;
				}
			}
		}

		return $max;
	}

	/**
	 * Fields with associated Post actions will have a post_field field option set specifiying how the field is mapped to the post data.
	 * [autoid] should work for several keys, as well as for custom post meta.
	 *
	 * @param object $field
	 * @return bool
	 */
	private static function field_supports_post_autoid( $field ) {
		return isset( $field->field_options['post_field'] ) && in_array( $field->field_options['post_field'], array( 'post_custom', 'post_title', 'post_content', 'post_name', 'post_excerpt' ), true );
	}

	/**
	 * @param object $field
	 * @return string the most recently submitted meta value to increment from.
	 */
	private static function get_post_max_value( $field ) {
		global $wpdb;
		$post_field = $field->field_options['post_field'];
		if ( 'post_custom' === $post_field ) {
			return FrmDb::get_var( $wpdb->postmeta, array( 'meta_key' => $field->field_options['custom_field'] ), 'meta_value', array( 'order_by' => 'post_ID DESC' ) );
		}
		return FrmDb::get_var(
			$wpdb->posts . ' AS t INNER JOIN ' . $wpdb->prefix . 'frm_items AS i ON i.post_id = t.ID',
			array( 'i.form_id' => $field->form_id ),
			't.' . $post_field,
			array( 'order_by' => 't.ID DESC' )
		);
	}

	/**
	 * If an auto_id field includes a prefix or suffix, strip them from the last value
	 *
	 * @param string $max
	 * @param stdClass $field
	 * @return string
	 */
	private static function get_increment_from_value( $max, $field ) {
		$default_value = $field->default_value;
		if ( strpos( $default_value, '[auto_id') !== false ) {
			list( $prefix, $shortcode ) = explode( '[auto_id', $default_value );
			list( $shortcode, $suffix ) = explode( ']', $shortcode, 2 );

			if ( $prefix !== '' ) {
				list ( $max, $prefix ) = self::maybe_remove_date_or_time_from_from_autoid( $max, $prefix, true );
				FrmProFieldsHelper::replace_non_standard_formidable_shortcodes( array(), $prefix );
			}

			if ( $suffix !== '' ) {
				list ( $max, $suffix ) = self::maybe_remove_date_or_time_from_from_autoid( $max, $suffix, false );
				FrmProFieldsHelper::replace_non_standard_formidable_shortcodes( array(), $suffix );
			}

			$max = str_replace( $prefix, '', $max );
			$max = str_replace( $suffix, '', $max );
		}

		$max = filter_var( $max, FILTER_SANITIZE_NUMBER_INT );

		return $max;
	}

	/**
	 * @since 5.0.06
	 *
	 * @param string $max
	 * @param string $pattern either a prefix or a suffix.
	 * @param bool   $is_prefix
	 * @return array
	 */
	private static function maybe_remove_date_or_time_from_from_autoid( $max, $pattern, $is_prefix ) {
		list( $max, $pattern ) = self::replace_datetime_from_autoid( 'date', $max, $pattern, $is_prefix );
		list( $max, $pattern ) = self::replace_datetime_from_autoid( 'time', $max, $pattern, $is_prefix );
		return array( $max, $pattern );
	}

	/**
	 * @since 5.0.06
	 *
	 * @param string $shortcode either 'date' or 'time'.
	 * @param string $max
	 * @param string $pattern either a prefix or a suffix.
	 * @param bool   $is_prefix
	 * @return array
	 */
	private static function replace_datetime_from_autoid( $shortcode, $max, $pattern, $is_prefix ) {
		$check = '[' . $shortcode;
		$start = strpos( $pattern, $check );
		if ( false !== $start ) {
			$start = strpos( $pattern, 'format=', $start );
		}
		if ( false !== $start ) {
			$start        += strlen( 'format=' );
			$end           = strpos( $pattern, ']', $start );
			$format        = substr( $pattern, $start, $end - $start );
			$format        = trim( $format, '"\'' );
			$reverse_regex = ! $is_prefix;
			$regex         = self::build_regex_from_datetime_format( $format, $reverse_regex );

			if ( $reverse_regex ) {
				$max = strrev( preg_replace( $regex, '', strrev( $max ), 1 ) );
			} else {
				$max = preg_replace( $regex, '', $max, 1 );
			}

			$replace_regex = '/\[' . $shortcode . '\s+format=("|\'){0,1}' . $format . '("|\'){0,1}\]/';
			$pattern       = preg_replace( $replace_regex, '', $pattern, 1 );
		}
		return array( $max, $pattern );
	}

	/**
	 * @since 5.0.06
	 *
	 * @param string $format
	 * @param bool   $reverse
	 * @return string
	 */
	private static function build_regex_from_datetime_format( $format, $reverse = false ) {
		$regex      = array();
		$characters = str_split( $format );

		foreach ( $characters as $character ) {
			switch ( $character ) {
				case 'Y':
					$regex[] = '\d{4}';
					break;

				case 'm':
				case 'd':
				case 'H':
				case 'h':
				case 'i':
				case 'y':
				case 's':
					$regex[] = '\d{2}';
					break;

				case 'G':
					$regex[] = '\d{1,2}';
					break;

				case 'w':
					$regex[] = '\d{1}';
					break;

				case '_':
				case '-':
				case ':':
					$regex[] = $character;
					break;
			}
		}

		if ( $reverse ) {
			$regex = array_reverse( $regex );
		}

		return '/' . implode( '', $regex ) . '/';
	}

	/**
	 * @codeCoverageIgnore
	 * @deprecated 2.04
	 */
	public static function email_value( $value ) {
		_deprecated_function( __FUNCTION__, '2.04', 'custom code' );

		return $value;
	}
}
