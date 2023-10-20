<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

class FrmProCopy {

	public static function table_name() {
		global $wpmuBaseTablePrefix, $wpdb;
		$prefix = $wpmuBaseTablePrefix ? $wpmuBaseTablePrefix : $wpdb->base_prefix;
		return $prefix . 'frmpro_copies';
	}

	public static function create( $values ) {
		global $wpdb, $blog_id;

		$new_values = array();
		self::prepare_values( $values, $new_values );

		$id = false;

		$exists = self::getAll( array( 'blog_id' => $blog_id, 'form_id' => $new_values['form_id'], 'type' => $new_values['type'] ), '', ' LIMIT 1' );
		if ( ! $exists ) {
			$query_results = $wpdb->insert( self::table_name(), $new_values );

			if ( $query_results ) {
				$id = $wpdb->insert_id;
			}
		}
		return $id;
	}

	public static function destroy( $id ) {
		global $wpdb;
		return $wpdb->delete( self::table_name(), array( 'id' => $id ) );
	}

	public static function getAll( $where = array(), $order_by = '', $limit = '' ) {
		if ( ! self::table_exists() ) {
			return array();
		}

		$method = ( $limit == ' LIMIT 1' ) ? 'row' : 'results';
		$results = FrmDb::get_var( self::table_name(), $where, '*', $args = array( 'order_by' => $order_by ), $limit, $method );

		return $results;
	}

	/**
	 * @since 2.02.10
	 * @param array $values
	 * @param array $new_values
	 */
	private static function prepare_values( $values, &$new_values ) {
		global $blog_id;
		$new_values['blog_id'] = $blog_id;
		$new_values['form_id'] = isset( $values['form_id'] ) ? (int) $values['form_id'] : null;
		$new_values['type']    = isset( $values['type'] ) ? $values['type'] : 'form'; // options here are: form, display
		if ( 'form' === $new_values['type'] ) {
			$form_copied            = FrmForm::getOne( $new_values['form_id'] );
			$new_values['copy_key'] = $form_copied->form_key;
		} elseif ( 'display' === $new_values['type'] && is_callable( 'FrmViewsCopy::prepare_values' ) ) {
			$new_values['copy_key'] = FrmViewsCopy::prepare_values( $new_values['form_id'] );
		}
		$new_values['created_at'] = current_time('mysql', 1);
	}

	/**
	 * @param bool $force
	 */
	public static function install( $force = false ) {
		$create_table = $force || ! self::table_exists();

		if ( $create_table ) {
			$force = true;
			self::create_table();
		}

		self::copy_forms($force);
	}

	private static function maybe_create_table() {
		if ( ! self::table_exists() ) {
			self::create_table();
		}
	}

	/**
	 * @return bool true if table exists
	 */
	private static function table_exists() {
		$db_version        = self::get_db_version_where_copies_table_is_expected_to_have_been_created(); // this is the version of the database we're moving to
		$active_db_version = get_site_option( 'frmpro_copies_db_version' );
		return $active_db_version >= $db_version;
	}

	private static function create_table() {
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$frmdb           = new FrmMigrate();
		$charset_collate = $frmdb->collation();

		/* Create/Upgrade Display Table */
		$sql = 'CREATE TABLE ' . self::table_name() . ' (
				id int(11) NOT NULL auto_increment,
				type varchar(255) default NULL,
				copy_key varchar(255) default NULL,
				form_id int(11) default NULL,
				blog_id int(11) default NULL,
				created_at datetime NOT NULL,
				PRIMARY KEY id (id),
				KEY form_id (form_id),
				KEY blog_id (blog_id)
		) ' . $charset_collate . ';';

		dbDelta( $sql );
		update_site_option( 'frmpro_copies_db_version', self::get_db_version_where_copies_table_is_expected_to_have_been_created() );
	}

	private static function get_db_version_where_copies_table_is_expected_to_have_been_created() {
		return 1.2;
	}

	/**
	 * Copy forms that are set to copy from one site to another
	 */
	public static function copy_forms( $force = false ) {
		self::maybe_force( $force );
		if ( ! $force ) {
			return;
		}

		$templates = self::get_templates_to_copy();

		foreach ( $templates as $template ) {
			if ( 'form' === $template->type ) {
				self::copy_form( $template );
			} elseif ( 'display' === $template->type && is_callable( 'FrmViewsCopy::copy_view' ) ) {
				FrmViewsCopy::copy_view( $template );
			}

			//TODO: replace any ids with field keys in the display before duplicated
			unset( $template );
		}

		update_option( 'frmpro_copies_checked', time() );
	}

	/**
	 * @since 2.02.10
	 * @param boolean $force
	 */
	private static function maybe_force( &$force ) {
		if ( ! $force ) { //don't check on every page load
			$last_checked = get_option('frmpro_copies_checked');

			if ( ! $last_checked || ( ( time() - $last_checked ) >= ( 60 * 60 ) ) ) {
				//check every hour
				$force = true;
			}
		}
	}

	/**
	 * Get all forms to be copied from global table
	 *
	 * @since 2.02.10
	 */
	private static function get_templates_to_copy() {
		if ( ! self::table_exists() ) {
			return array();
		}

		global $wpdb, $blog_id;

		$query = 'SELECT c.*, p.post_name FROM ' . self::table_name() . ' c ' .
				'LEFT JOIN ' . $wpdb->prefix . 'frm_forms f ON (c.copy_key = f.form_key) ' .
				'LEFT JOIN ' . $wpdb->posts . ' p ON (c.copy_key = p.post_name) ' .
				'WHERE blog_id != %d ' .
				'AND ((type = %s AND f.form_key is NULL) OR (type = %s AND p.post_name is NULL)) ' .
				'ORDER BY type DESC';
		$query = $wpdb->prepare( $query, $blog_id, 'form', 'display' );

		return FrmDb::check_cache( 'all_templates_' . $blog_id, 'frm_copy', $query, 'get_results' );
	}

	/**
	 * @since 2.02.10
	 *
	 * @param object $template
	 */
	private static function copy_form( $template ) {
		FrmForm::duplicate( $template->form_id, false, true, $template->blog_id );
	}
}
