<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

class FrmProAppHelper {

	public static function plugin_folder() {
		return basename( self::plugin_path() );
	}

	public static function plugin_path() {
		return dirname( dirname( dirname( __FILE__ ) ) );
	}

	public static function plugin_url() {
		return plugins_url( '', self::plugin_path() . '/formidable-pro.php' );
	}

	public static function relative_plugin_url() {
		return str_replace( array( 'https:', 'http:' ), '', self::plugin_url() );
	}

	/**
	 * @since 6.4.2
	 *
	 * @return string
	 */
	public static function get_svg_folder_path() {
		return self::plugin_path() . '/images/svg/';
	}

	/**
	 * Get the Pro settings
	 *
	 * @since 2.0
	 *
	 * @param None
	 * @return Object
	 */
	public static function get_settings() {
		global $frmpro_settings;
		if ( empty($frmpro_settings) ) {
			$frmpro_settings = new FrmProSettings();
		}
		return $frmpro_settings;
	}

	/**
	 * Only load the Pro updater once on a page
	 *
	 * @since 3.04.03
	 */
	public static function get_updater() {
		global $frmpro_updater;
		if ( empty( $frmpro_updater ) ) {
			$frmpro_updater = new FrmProEddController();
		}
		return $frmpro_updater;
	}

	/**
	 * @since 4.09
	 * @return bool
	 */
	public static function views_is_installed() {
		return class_exists( 'FrmViewsAppHelper' );
	}

	/**
	 * Try to show the SVG if possible. Otherwise, use the font icon.
	 *
	 * @since 4.0.02
	 * @param string $class
	 * @param array  $atts
	 */
	public static function icon_by_class( $class, $atts = array() ) {
		if ( is_callable( 'FrmAppHelper::icon_by_class' ) ) {
			return FrmAppHelper::icon_by_class( $class, $atts );
		}
	}

	/**
	 * Load an independent svg file from the external folder. Returns the svg html or the inner HTML tags of the svg.
	 *
	 * @since 6.4.2
	 * @param string $slug
	 * @param boolean $inner_html_only
	 * @param array $atts
	 * @return string|array|false
	 */
	private static function init_svg_by_slug( $slug, $inner_html_only = false, $atts = array() ) {
		$svg_file_path = self::get_svg_folder_path() . $slug . '.svg';

		if ( false === file_exists( $svg_file_path ) ) {
			return false;
		}

		$svg = file_get_contents( $svg_file_path );

		if ( false === $inner_html_only ) {
			return $svg;
		}

		$svg_atts = array_merge(
			array(
				'viewBox' => '',
			),
			$atts
		);

		$svg_element = array(
			'inner_html'  => '',
			'atts'        => $svg_atts,
			'atts_string' => '',
		);

		preg_match( '/viewBox\s*=\s*["\'](.*?)["\']/i', $svg, $matches );
		if ( $matches[1] ) {
			$svg_element['atts']['viewBox'] = $matches[1];
		}

		$html_params = $svg_element['atts'];
		unset( $html_params['echo'] );
		$svg_element['atts_string'] = FrmAppHelper::array_to_html_params( $html_params );

		$svg_element['inner_html'] = preg_replace( '/^<svg[^>]*>|<\/svg>$/i', '', $svg );

		return $svg_element;
	}

	/**
	 * Get svg icon by slug.
	 *
	 * @since 6.4.2
	 *
	 * @param string $slug
	 * @param string $classnames
	 * @param array  $atts
	 * @return string
	 */
	public static function get_svg_icon( $slug, $classnames, $atts = array() ) {
		$echo        = isset( $atts['echo'] ) ? $atts['echo'] : false;
		$inner_html  = ( isset( $classnames ) && '' !== $classnames ) || ! empty( $atts );
		$svg_context = self::init_svg_by_slug( $slug, $inner_html, $atts );

		if ( false === $svg_context ) {
			return self::echo_or_return( '', $echo );
		}

		if ( ! is_array( $svg_context ) && false === $inner_html ) {
			return self::echo_or_return( $svg_context, $echo );
		}

		return self::echo_or_return(
			'<svg ' . $svg_context['atts_string'] . ' class="' . esc_attr( $classnames ) . '">' . $svg_context['inner_html'] . '</svg>',
			$echo
		);
	}

	/**
	 * Echo the string or return it.
	 *
	 * @since 6.4.2
	 *
	 * @param string $string
	 * @param boolean $echo
	 */
	private static function echo_or_return( $string, $echo = true ) {
		if ( true === $echo ) {
			echo $string; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			return;
		}

		return $string;
	}

	/**
	 * Get the current date in the display format
	 * Used by [date] shortcode
	 *
	 * @since 2.0
	 * @return string
	 */
	public static function get_date( $format = '' ) {
		if ( empty($format) ) {
			$frmpro_settings = self::get_settings();
			$format = $frmpro_settings->date_format;
		}

		return date_i18n($format, strtotime(current_time('mysql')));
	}

	/**
	 * Get the current time
	 * Used by [time] shortcode
	 *
	 * @since 2.0
	 * @return string
	 */
	public static function get_time( $atts = array() ) {
		$defaults = array( 'format' => 'H:i:s', 'round' => 0 );
		$atts = array_merge( $defaults, (array) $atts );
		$current_time = strtotime( current_time( 'mysql' ) );
		if ( ! empty( $atts['round'] ) ) {
			$round_numerator = 60 * (float) $atts['round'];
			$current_time = round( $current_time / $round_numerator ) * $round_numerator;
		}
		return date_i18n( $atts['format'], $current_time );
	}

	/**
	 * Format the time field values
	 *
	 * @since 2.0.14
	 */
	public static function format_time( $time, $format = 'H:i' ) {
		if ( is_array( $time ) ) {
			$time = '';
		}

		if ( $time !== '' ) {
			if ( $format == 'h:i A' ) {
				// for reverse compatibility
				$format = 'g:i A';
			}
			$time = gmdate( $format, strtotime( $time ) );
		}
		return $time;
	}

	public static function format_time_by_reference( &$time ) {
		$time = self::format_time( $time, 'H:i' );
	}

	/**
	 * Get a value from the current user profile
	 *
	 * @since 2.0
	 *
	 * @param string $value
	 * @param bool   $return_array
	 * @return string|array
	 */
	public static function get_current_user_value( $value, $return_array = false ) {
		global $current_user;
		$new_value = isset( $current_user->{$value} ) ? $current_user->{$value} : '';
		if ( is_array( $new_value ) && ! $return_array ) {
			$new_value = implode( ', ', $new_value );
		}

		return $new_value;
	}

	/**
	 * Get the id of the current user
	 * Used by [user_id] shortcode
	 *
	 * @since 2.0
	 * @return string
	 */
	public static function get_user_id() {
		$user_ID = get_current_user_id();
		return $user_ID ? $user_ID : '';
	}

	/**
	 * Get a value from the currently viewed post
	 *
	 * @since 2.0
	 * @return string
	 */
	public static function get_current_post_value( $value ) {
		global $post;
		if ( ! $post ) {
			return;
		}

		if ( isset($post->{$value}) ) {
			$new_value = $post->{$value};
		} else {
			$new_value = get_post_meta($post->ID, $value, true);
		}

		return $new_value;
	}

	/**
	 * Get the email of the author of current post
	 * Used by [post_author_email] shortcode
	 *
	 * @since 2.0
	 * @return string
	 */
	public static function get_post_author_email() {
		return get_the_author_meta('user_email');
	}

	/**
	 * Check for either json or serilized data. This is temporary while transitioning
	 * all data to json.
	 *
	 * @since 4.02.03
	 */
	public static function unserialize_or_decode( &$value ) {
		if ( is_callable( 'FrmAppHelper::unserialize_or_decode' ) ) {
			FrmAppHelper::unserialize_or_decode( $value );
		} else {
			$value = maybe_unserialize( $value );
		}
	}

	/**
	 * @since 2.0.2
	 */
	public static function display_to_datepicker_format() {
		$formats = array(
			'm/d/Y' => 'mm/dd/yy',
			'n/j/Y' => 'm/d/yy',
			'Y/m/d' => 'yy/mm/dd',
			'd/m/Y' => 'dd/mm/yy',
			'd.m.Y' => 'dd.mm.yy',
			'j/m/y' => 'd/mm/y',
			'j/n/y' => 'd/m/y',
			'Y-m-d' => 'yy-mm-dd',
			'j-m-Y' => 'd-mm-yy',
		);
		$formats = apply_filters( 'frm_datepicker_formats', $formats );
		return $formats;
	}

	public static function maybe_convert_to_db_date( $date_str, $to_format = 'Y-m-d' ) {
		$date_str = trim($date_str);
		$in_db_format = preg_match('/^\d{4}-\d{2}-\d{2}/', $date_str);

		if ( ! $in_db_format ) {
			$date_str = self::convert_date($date_str, 'db', $to_format);
		}

		return $date_str;
	}

	public static function maybe_convert_from_db_date( $date_str, $from_format = 'Y-m-d' ) {
		$date_str = trim($date_str);
		$in_db_format = preg_match('/^\d{4}-\d{2}-\d{2}/', $date_str);

		if ( $in_db_format ) {
			$date_str = self::convert_date($date_str, $from_format, 'db');
		}

		return $date_str;
	}

	public static function convert_date( $date_str, $from_format, $to_format ) {
		if ( 'db' == $to_format ) {
			$frmpro_settings = self::get_settings();
			$to_format = $frmpro_settings->date_format;
		} else if ( 'db' == $from_format ) {
			$frmpro_settings = self::get_settings();
			$from_format = $frmpro_settings->date_format;
		}

		if ( $from_format == 'Y-m-d' && strpos( $date_str, '00:00:00' ) ) {
			$date_str = str_replace( ' 00:00:00', '', $date_str );
		}

		$date = date_create_from_format( $from_format, $date_str );
		if ( $date ) {
			$new_date = $date->format( $to_format );
		} else {
			$new_date = self::convert_date_fallback( $date_str, $from_format, $to_format );
		}
		return $new_date;
	}

	private static function convert_date_fallback( $date_str, $from_format, $to_format ) {
		$base_struc     = preg_split( '/[\/|.| |-]/', $from_format );
		$date_str_parts = preg_split( '/[\/|.| |-]/', $date_str );
		$date_elements = array();

		$p_keys = array_keys( $base_struc );
		foreach ( $p_keys as $p_key ) {
			if ( ! empty( $date_str_parts[ $p_key ] ) ) {
				$date_elements[ $base_struc[ $p_key ] ] = $date_str_parts[ $p_key ];
			} else {
				return false;
			}
		}

		if ( is_numeric( $date_elements['m'] ) ) {
			$day = ( isset( $date_elements['j'] ) ? $date_elements['j'] : $date_elements['d'] );
			$year = ( isset( $date_elements['Y'] ) ? $date_elements['Y'] : $date_elements['y'] );
			$dummy_ts = mktime( 0, 0, 0, $date_elements['m'], $day, $year );
		} else {
			$dummy_ts = strtotime( $date_str );
		}

		return gmdate( $to_format, $dummy_ts );
	}

	public static function get_edit_link( $id ) {
		$output = '';
		if ( current_user_can('administrator') ) {
			$output = '<a href="' . esc_url( FrmProEntry::admin_edit_link( $id ) ) . '">' . __( 'Edit', 'formidable-pro' ) . '</a>';
		}

		return $output;
	}

	public static function get_custom_post_types() {
		$custom_posts = get_post_types( array(), 'object');
		foreach ( array( 'revision', 'attachment', 'nav_menu_item' ) as $unset ) {
			// @phpstan-ignore-next-line
			unset( $custom_posts[ $unset ] );
		}

		// alphebetize
		ksort( $custom_posts );

		// keep post and page first
		$first_types = array( 'post' => $custom_posts['post'], 'page' => $custom_posts['page'] );
		$custom_posts = $first_types + $custom_posts;

		return $custom_posts;
	}

	public static function get_custom_taxonomy( $post_type, $field ) {
		$taxonomies = get_object_taxonomies($post_type);
		if ( ! $taxonomies ) {
			return false;
		} else {
			$field = (array) $field;
			if ( ! isset($field['taxonomy']) ) {
				self::unserialize_or_decode( $field['field_options'] );
				$field['taxonomy'] = FrmField::get_option( $field, 'taxonomy' );
			}

			if ( isset($field['taxonomy']) && in_array($field['taxonomy'], $taxonomies) ) {
				return $field['taxonomy'];
			} elseif ( $post_type == 'post' ) {
				return 'category';
			} else {
				return reset($taxonomies);
			}
		}
	}

	public static function sort_by_array( $array, $order_array ) {
		$array = (array) $array;
		$order_array = (array) $order_array;
		$ordered = array();
		foreach ( $order_array as $key ) {
			if ( array_key_exists( $key, $array ) ) {
				$ordered[ $key ] = $array[ $key ];
				unset( $array[ $key ] );
			}
		}
		return $ordered + $array;
	}

	/**
	 * @param array $entry_ids
	 * @param array $args
	 * @return array
	 */
	public static function filter_where( $entry_ids, $args ) {
		$defaults = array(
			'where_opt'   => false,
			'where_is'    => '=',
			'where_val'   => '',
			'form_id'     => false,
			'form_posts'  => array(),
			'after_where' => false,
			'display'     => false,
			'drafts'      => 0,
			'use_ids'     => false,
		);

		$args = wp_parse_args( $args, $defaults );

		if ( ! (int) $args['form_id'] || ! $args['where_opt'] || ! is_numeric( $args['where_opt'] ) ) {
			return $entry_ids;
		}

		$where_field = FrmField::getOne( $args['where_opt'] );
		if ( ! $where_field ) {
			return $entry_ids;
		}

		if ( false === $args['display'] ) {
			$args['display'] = self::get_blank_display_object();
		}

		self::prepare_where_args($args, $where_field, $entry_ids);

		$new_ids = array();
		self::filter_entry_ids( $args, $where_field, $entry_ids, $new_ids );

		unset( $args['temp_where_is'] );

		self::prepare_post_filter( $args, $where_field, $new_ids );

		if ( $args['after_where'] ) {
			//only use entries that are found with all wheres
			$entry_ids = array_intersect($new_ids, $entry_ids);
		} else {
			$entry_ids = $new_ids;
		}

		return $entry_ids;
	}

	/**
	 * Some code examples in the documentation include examples that access $args['display']->ID without checking if it is an object first.
	 * As there might not always be a display set, the false default value is not safe enough as an attempt to read property "ID" on bool flags a warning.
	 *
	 * @since 5.0.14
	 *
	 * @return stdClass
	 */
	private static function get_blank_display_object() {
		$display     = new stdClass();
		$display->ID = 0;
		return $display;
	}

	/**
	 * Called by the filter_where function
	 */
	private static function prepare_where_args( &$args, $where_field, $entry_ids ) {
		self::prepare_where_datetime( $args, $where_field );

		if ( $args['where_is'] == '=' && $args['where_val'] != '' && FrmField::is_field_with_multiple_values( $where_field ) ) {
			if ( $where_field->type != 'data' || $where_field->field_options['data_type'] != 'checkbox' || is_numeric($args['where_val']) ) {
				// leave $args['where_is'] the same if this is a data from entries checkbox with a numeric value
				$args['where_is'] = 'LIKE';
			}
		}

		$args['temp_where_is'] = str_replace( array( '!', 'not ' ), '', $args['where_is'] );

		//get values that aren't blank and then remove them from entry list
		if ( $args['where_val'] == '' && $args['temp_where_is'] == '=' ) {
			$args['temp_where_is'] = '!=';
		}

		if ( self::option_is_like( $args['where_is'] ) ) {
			 //add extra slashes to match values that are escaped in the database
			$args['where_val_esc'] = addslashes( $args['where_val'] );
		} else if ( ! strpos( $args['where_is'], 'in' ) && ! is_numeric( $args['where_val'] ) ) {
			$args['where_val_esc'] = $args['where_val'];
		}
		$filter_args = $args;
		$filter_args['entry_ids'] = $entry_ids;
		$args['where_val'] = apply_filters('frm_filter_where_val', $args['where_val'], $filter_args);

		self::prepare_dfe_text($args, $where_field);
	}

	/**
	 * @since 2.3
	 *
	 * @param array  $args
	 * @param object $where_field
	 * @return void
	 */
	private static function prepare_where_datetime( &$args, $where_field ) {
		$is_datetime = ( $args['where_val'] == 'NOW' || $where_field->type == 'date' || $where_field->type == 'time' );
		if ( ! $is_datetime || empty( $args['where_val'] ) ) {
			return;
		}

		$date_format = ( $where_field->type == 'time' ) ? 'H:i' : 'Y-m-d';

		if ( $args['where_val'] == 'NOW' ) {
			$args['where_val'] = self::get_date( $date_format );
		} elseif ( ! self::option_is_like( $args['where_is'] ) ) {
			$args['where_val'] = gmdate( $date_format, strtotime( $args['where_val'] ) );
		}
	}

	/**
	 * @since 2.3
	 *
	 * @param string $where_is
	 * @return bool
	 */
	private static function option_is_like( $where_is ) {
		return in_array( $where_is, array( 'LIKE', 'not LIKE' ), true );
	}

	/**
	 * Replace a text value where_val with the matching entry IDs for Dynamic Field filters
	 *
	 * @param array  $args
	 * @param object $where_field
	 * @return void
	 */
	private static function prepare_dfe_text( &$args, $where_field ) {
		if ( $where_field->type != 'data' ) {
			return;
		}

		// Only proceed if we have a non-category dynamic field with a non-numeric/non-array where_val
		$is_a_string_value = ( $args['where_val'] && ! is_numeric( $args['where_val'] ) && ! is_array( $args['where_val'] ) );
		$is_a_post_field = ( isset( $where_field->field_options['post_field'] ) && $where_field->field_options['post_field'] == 'post_category' );
		$continue = ( $is_a_string_value && ! $is_a_post_field );
		$continue = apply_filters( 'frm_search_for_dynamic_text', $continue, $where_field, $args );

		if ( ! $continue ) {
			return;
		}

		$linked_id = FrmProField::get_dynamic_field_entry_id( $where_field->field_options['form_select'], $args['where_val'], $args['temp_where_is'] );

		// If text doesn't return any entry IDs, get entry IDs from entry key
		// Note: Keep for reverse compatibility
		if ( ! $linked_id ) {
			$linked_field = FrmField::getOne( $where_field->field_options['form_select'] );
			if ( ! $linked_field ) {
				return;
			}

			$linked_id = FrmDb::get_col(
				'frm_items',
				array(
					'form_id' => $linked_field->form_id,
					'item_key ' . FrmDb::append_where_is( $args['temp_where_is'] ) => $args['where_val'],
				)
			);
		}

		if ( ! $linked_id ) {
			return;
		}

		//Change $args['where_val'] to linked entry IDs
		$args['where_val'] = (array) $linked_id;

		// Don't use old where_val_esc value for filtering
		unset( $args['where_val_esc'] );

		$args['where_val'] = apply_filters( 'frm_filter_dfe_where_val', $args['where_val'], $args );
	}

	/**
	 * @param array  $args
	 * @param object $where_field
	 * @param array  $entry_ids
	 * @param array  $new_ids
	 * @return void
	 */
	private static function filter_entry_ids( $args, $where_field, $entry_ids, &$new_ids ) {
		$where_statement = array( 'fi.id' => (int) $args['where_opt'] );

		$num_query = self::maybe_query_as_number( $where_field->type );
		$field_key = 'meta_value ' . $num_query . FrmDb::append_where_is( $args['temp_where_is'] );
		$nested_where = array( $field_key => $args['where_val'] );
		if ( isset( $args['where_val_esc'] ) && $args['where_val_esc'] != $args['where_val'] ) {
			$nested_where['or']               = 1;
			$nested_where[ ' ' . $field_key ] = $args['where_val_esc'];
		}
		$where_statement[] = $nested_where;

		$args['entry_ids'] = $entry_ids;
		$where_statement   = apply_filters( 'frm_where_filter', $where_statement, $args );

		$filter_args = array( 'is_draft' => $args['drafts'] );

		// If the field is from a repeating section (or embedded form?) get the parent ID.
		$filter_args['return_parent_id'] = is_array( $args['form_id'] ) ? ! in_array( $where_field->form_id, $args['form_id'], true ) : $where_field->form_id != $args['form_id'];

		// Add entry IDs to $where_statement.
		if ( $args['use_ids'] ) {
			if ( is_array( $where_statement ) ) {
				if ( $filter_args['return_parent_id'] ) {
					$where_statement['parent_item_id'] = $entry_ids;
				} else {
					$where_statement['item_id'] = $entry_ids;
				}
			} else {
				// If the filter changed the query to a string, allow it.
				$where_statement .= FrmDb::prepend_and_or_where( ' AND ', array( 'item_id' => $entry_ids ) );
			}
		}

		$new_ids = FrmEntryMeta::getEntryIds( $where_statement, '', '', true, $filter_args );

		if ( $args['where_is'] != $args['temp_where_is'] ) {
			$new_ids = array_diff( (array) $entry_ids, $new_ids );
		}
	}

	/**
	 * @since 3.0
	 */
	public static function maybe_query_as_number( $type ) {
		$number_fields = apply_filters( 'frm_number_fields', array( 'number', 'scale', 'star', 'quiz_score' ) );
		return in_array( $type, $number_fields, true ) ? ' +0 ' : '';
	}

	/**
	 * If there are posts linked to entries for this form.
	 *
	 * @param array  $args
	 * @param object $where_field
	 * @param array  $new_ids
	 * @return void
	 */
	private static function prepare_post_filter( $args, $where_field, &$new_ids ) {
		if ( empty( $args['form_posts'] ) ) {
			// There are not posts related to this view.
			return;
		}

		if ( ! isset( $where_field->field_options['post_field'] ) || ! in_array( $where_field->field_options['post_field'], array( 'post_category', 'post_custom', 'post_status', 'post_content', 'post_excerpt', 'post_title', 'post_name', 'post_date' ), true ) ) {
			// This is not a post field.
			return;
		}

		$post_ids = array();
		foreach ( $args['form_posts'] as $form_post ) {
			$post_ids[ $form_post->post_id ] = $form_post->id;
			if ( ! in_array($form_post->id, $new_ids) ) {
				$new_ids[] = $form_post->id;
			}
		}

		if ( empty( $post_ids ) ) {
			return;
		}

		global $wpdb;
		$filter_args = array();

		if ( $where_field->field_options['post_field'] == 'post_category' ) {
			//check categories

			$args['temp_where_is'] = FrmDb::append_where_is( str_replace( array( '!', 'not ' ), '', $args['where_is'] ) );

			$t_where = array(
				'or' => 1,
				't.term_id ' . $args['temp_where_is'] => $args['where_val'],
				't.slug ' . $args['temp_where_is'] => $args['where_val'],
				't.name ' . $args['temp_where_is'] => $args['where_val'],
			);
			unset($args['temp_where_is']);

			$query = array( 'tt.taxonomy' => $where_field->field_options['taxonomy'] );
			$query[] = $t_where;

			$add_posts = FrmDb::get_col(
				$wpdb->terms . ' AS t INNER JOIN ' . $wpdb->term_taxonomy . ' AS tt ON tt.term_id = t.term_id INNER JOIN ' . $wpdb->term_relationships . ' AS tr ON tr.term_taxonomy_id = tt.term_taxonomy_id',
				$query,
				'tr.object_id',
				$filter_args
			);
			$add_posts = array_intersect($add_posts, array_keys($post_ids));

			if ( in_array( $args['where_is'], array( '!=', 'not LIKE' ), true ) ) {
				$remove_posts = $add_posts;
				$add_posts = false;
			} else if ( empty($add_posts) ) {
				$new_ids = array();
				return;
			}
		} else {
			$query = array();
			if ( $where_field->field_options['post_field'] == 'post_custom' && $where_field->field_options['custom_field'] != '' ) {
				//check custom fields
				$get_field = 'post_id';
				$get_table = $wpdb->postmeta;
				$query['meta_key'] = $where_field->field_options['custom_field'];
				$query_key = 'meta_value';
			} else {
				//if field is post field
				$get_field = 'ID';
				$get_table = $wpdb->posts;
				$query_key = sanitize_title( $where_field->field_options['post_field'] );
			}

			$query_key .= self::maybe_query_as_number( $where_field->type );
			$query_key .= FrmDb::append_where_is( $args['where_is'] );
			$query[ $query_key ] = $args['where_val'];

			$add_posts = FrmDb::get_col( $get_table, $query, $get_field, $filter_args );
			$add_posts = array_intersect( $add_posts, array_keys( $post_ids ) );
		}

		if ( $add_posts && ! empty($add_posts) ) {
			$new_ids = array();
			foreach ( $add_posts as $add_post ) {
				if ( ! in_array( $post_ids[ $add_post ], $new_ids ) ) {
					$new_ids[] = $post_ids[ $add_post ];
				}
			}
		}

		if ( isset( $remove_posts ) ) {
			if ( ! empty($remove_posts) ) {
				foreach ( $remove_posts as $remove_post ) {
					$key = array_search( $post_ids[ $remove_post ], $new_ids );
					if ( $key && $new_ids[ $key ] == $post_ids[ $remove_post ] ) {
						unset( $new_ids[ $key ] );
					}

					unset($key);
				}
			}
		} else if ( ! $add_posts ) {
			$new_ids = array();
		}
	}

	/**
	 * Outputs the HTML selected attribute.
	 *
	 * @since 4.07
	 * @param array|string $selected
	 * @param string $current
	 * @param bool $echo
	 * @return string
	 */
	private static function strict_selected( $selected, $current, $echo = true ) {
		return selected( in_array( $current, (array) $selected, true ), true, $echo );
	}

	/**
	 * Outputs the HTML selected attribute. For string $selected values, it will select all roles that rank higher as well.
	 *
	 * @since 4.07
	 * @param array|string $selected
	 * @param string $current
	 * @param bool $echo
	 * @return string
	 */
	public static function selected( $selected, $current, $echo = true ) {
		if ( is_array( $selected ) ) {
			return self::strict_selected( $selected, $current, $echo );
		}

		$roles        = array( 'administrator', 'editor', 'author', 'contributor', 'subscriber' );
		$required_key = array_search( $selected, $roles, true );
		$key          = array_search( $current, $roles, true );

		if ( $required_key === false || $key === false ) {
			return self::strict_selected( $selected, $current, $echo );
		}

		return selected( $key <= $required_key, true, $echo );
	}

	/**
	 * @param string $path
	 * @return string mime type
	 */
	public static function get_mime_type( $path ) {
		$filetype = wp_check_filetype( $path );
		return $filetype['type'] ? $filetype['type'] : 'application/octet-stream';
	}

	/**
	 * @since 5.0.13
	 *
	 * @return string The base Google APIS url for the current version of jQuery UI.
	 */
	public static function jquery_ui_base_url() {
		$url = 'http' . ( is_ssl() ? 's' : '' ) . '://ajax.googleapis.com/ajax/libs/jqueryui/' . FrmAppHelper::script_version( 'jquery-ui-core', '1.13.2' );
		$url = apply_filters( 'frm_jquery_ui_base_url', $url );
		return $url;
	}

	/**
	 * @since 5.0.15
	 *
	 * @param array $values
	 * @return array<array>
	 */
	public static function pull_ids_and_keys( $values ) {
		$ids  = array();
		$keys = array();
		foreach ( $values as $field_id_or_key ) {
			if ( is_numeric( $field_id_or_key ) ) {
				$ids[] = (int) $field_id_or_key;
			} else {
				$keys[] = $field_id_or_key;
			}
		}
		return array( $ids, $keys );
	}

	/**
	 * Include svg images for pro.
	 *
	 * @since 5.3
	 */
	public static function include_svg() {
		include_once self::plugin_path() . '/images/icons.svg';
	}

	/**
	 * Get the list of capabilities for pro.
	 *
	 * @since 5.3.1
	 *
	 * @param array<string,string> $permissions
	 * @return array<string,string>
	 */
	public static function add_pro_capabilities( $permissions ) {
		$permissions['frm_edit_applications']     = __( 'Add/Edit Applications', 'formidable-pro' );
		$permissions['frm_application_dashboard'] = __( 'Access Application Dashboard', 'formidable-pro' );
		return $permissions;
	}

	/**
	 * Checks if WP Cron is disabled.
	 *
	 * @since 5.4.1
	 *
	 * @return bool
	 */
	public static function is_cron_disabled() {
		return defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON;
	}

	/**
	 * Handles missing mime_content_type function.
	 *
	 * @since 6.4.3
	 *
	 * @param string $file
	 *
	 * @return string|false
	 */
	public static function get_mime_content_type( $file ) {
		if ( function_exists( 'mime_content_type' ) ) {
			return mime_content_type( $file );
		}

		$filetype = wp_check_filetype( $file );
		return $filetype['type'];
	}

	/**
	 * Let WordPress process the uploads
	 *
	 * @codeCoverageIgnore
	 *
	 * @param int $field_id
	 */
	public static function upload_file( $field_id ) {
		_deprecated_function( __FUNCTION__, '2.02', 'FrmProFileField::upload_file' );
		return FrmProFileField::upload_file( $field_id );
	}

	/**
	 * @codeCoverageIgnore
	 */
	public static function upload_dir( $uploads ) {
		_deprecated_function( __FUNCTION__, '2.02', 'FrmProFileField::upload_dir' );
		return FrmProFileField::upload_dir( $uploads );
	}

	/**
	 * @codeCoverageIgnore
	 */
	public static function get_time_format_for_field( $field ) {
		_deprecated_function( __FUNCTION__, '3.02.01', 'FrmProFieldTime->get_time_format_for_field' );
		$time_format = FrmField::get_option( $field, 'clock' );
		return self::get_time_format_for_setting( $time_format );
	}

	public static function get_time_format_for_setting( $time_format ) {
		_deprecated_function( __FUNCTION__, '3.02.01', 'FrmProFieldTime->get_time_format_for_setting' );
		return ( $time_format == 12 ) ? 'g:i A' : 'H:i';
	}

	public static function get_rand( $length ) {
		$all_g = 'ABCDEFGHIJKLMNOPQRSTWXZ';
		$pass = '';
		for ( $i = 0; $i < $length; $i++ ) {
			$pass .= $all_g[ rand(0, strlen($all_g) - 1) ];
		}
		return $pass;
	}

	/**
	 * @deprecated 4.09
	 */
	public static function load_genesis() {
		return FrmProDisplaysController::deprecated_function( __METHOD__, 'FrmViewsAppHelper::load_genesis' );
	}

	/**
	 * @deprecated 4.09
	 */
	public static function gen_pagination_class( $class ) {
		return FrmProDisplaysController::deprecated_function( __METHOD__, 'FrmViewsAppHelper::gen_pagination_class', $class );
	}

	/**
	 * @deprecated 4.09
	 */
	public static function gen_prev_label() {
		return FrmProDisplaysController::deprecated_function( __METHOD__, 'FrmViewsAppHelper::gen_prev_label' );
	}

	/**
	 * @deprecated 4.09
	 */
	public static function gen_next_label() {
		return FrmProDisplaysController::deprecated_function( __METHOD__, 'FrmViewsAppHelper::gen_next_label' );
	}

	/**
	 * @deprecated 4.09
	 */
	public static function gen_prev_class( $class ) {
		return FrmProDisplaysController::deprecated_function( __METHOD__, 'FrmViewsAppHelper::gen_prev_class', $class );
	}

	/**
	 * @deprecated 4.09
	 */
	public static function gen_next_class( $class ) {
		return FrmProDisplaysController::deprecated_function( __METHOD__, 'FrmViewsAppHelper::gen_next_class', $class );
	}

	/**
	 * @deprecated 4.09
	 */
	public static function gen_dots_class( $class ) {
		return FrmProDisplaysController::deprecated_function( __METHOD__, 'FrmViewsAppHelper::gen_dots_class', $class );
	}

	/**
	 * @deprecated 4.09
	 */
	public static function reset_keys( $arr ) {
		return FrmProDisplaysController::deprecated_function( __METHOD__, 'FrmViewsAppHelper::reset_keys', $arr );
	}

	/**
	 * Get the server OS
	 *
	 * @since 6.4.2
	 *
	 * @return string
	 */
	public static function get_server_os() {

		if ( is_callable( 'FrmAppHelper::get_server_os' ) ) {
			return FrmAppHelper::get_server_os();
		}
		if ( function_exists( 'php_uname' ) ) {
			return php_uname( 's' );
		}

		return '';
	}
}
