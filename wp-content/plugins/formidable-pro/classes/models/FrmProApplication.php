<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

/**
 * @since 5.3
 */
class FrmProApplication {

	/**
	 * @var WP_Term $term
	 */
	private $term;

	/**
	 * @var string $updated_at
	 */
	private $updated_at;

	/**
	 * @var string|null $created_at
	 */
	private $created_at;

	/**
	 * Used to limit timestamp updates from happening more than once per application during heavy updates
	 *
	 * @var array<int> $updated_application_ids
	 */
	private static $updated_application_ids = array();

	/**
	 * @param WP_Term $term
	 * @param string  $updated_at
	 * @return void
	 */
	public function __construct( $term, $updated_at ) {
		$this->term       = $term;
		$this->updated_at = $updated_at;
	}

	/**
	 * @param string $name
	 * @return array|WP_Error
	 */
	public static function create( $name ) {
		$term = wp_insert_term( $name, 'frm_application' );

		if ( is_array( $term ) ) {
			$application_id = $term['term_id'];
			$timestamp      = time();
			add_term_meta( $application_id, '_frm_created_at', $timestamp );
			add_term_meta( $application_id, '_frm_updated_at', $timestamp );
		}

		return $term;
	}

	/**
	 * Set the updated timestamp for an application.
	 *
	 * @param int $application_id
	 * @return void
	 */
	public static function update_timestamp( $application_id ) {
		if ( in_array( $application_id, self::$updated_application_ids, true ) ) {
			// Prevent setting multiple times in a single request, for performance.
			return;
		}

		update_term_meta( $application_id, '_frm_updated_at', time() );
		self::$updated_application_ids[] = $application_id;
	}

	/**
	 * @param int $application_id
	 * @param int $form_id
	 * @return void
	 */
	public static function add_form_to_application( $application_id, $form_id ) {
		add_term_meta( $application_id, '_frm_form_id', $form_id );
		self::update_form_count( $application_id );
	}

	/**
	 * @param int $application_id
	 * @param int $form_id
	 * @return void
	 */
	public static function remove_form_from_application( $application_id, $form_id ) {
		delete_term_meta( $application_id, '_frm_form_id', $form_id );
		self::update_form_count( $application_id );
	}

	/**
	 * @param int    $application_id
	 * @param int    $post_id
	 * @param string $type {
	 *     Type of item being added. Possible values include '', 'view', 'page', or 'form'.
	 *     If type is known, only update count for the specific type being added.
	 * }
	 * @return void
	 */
	public static function add_post_to_application( $application_id, $post_id, $type = '' ) {
		wp_set_post_terms( $post_id, array( $application_id ), 'frm_application', true );
		self::maybe_update_post_count( $application_id, $type );
	}

	/**
	 * @param int    $application_id
	 * @param string $type Type of item being added. Possible values include '', 'view', 'page', or 'form'.
	 * @return void
	 */
	private static function maybe_update_post_count( $application_id, $type ) {
		if ( in_array( $type, array( '', 'view' ), true ) ) {
			self::update_view_count( $application_id );
		}

		if ( in_array( $type, array( '', 'page' ), true ) ) {
			self::update_page_count( $application_id );
		}
	}

	/**
	 * @param int    $application_id
	 * @param int    $post_id
	 * @param string $type
	 * @return void
	 */
	public static function remove_post_from_application( $application_id, $post_id, $type = '' ) {
		wp_remove_object_terms( $post_id, array( $application_id ), 'frm_application' );
		self::maybe_update_post_count( $application_id, $type );
	}

	/**
	 * @param int $application_id
	 * @return void
	 */
	public static function update_form_count( $application_id ) {
		$count = count( self::get_forms_for_application( $application_id, true ) );
		update_term_meta( $application_id, '_frm_form_count', $count );
		self::update_timestamp( $application_id );
	}

	/**
	 * @param int $application_id
	 * @return void
	 */
	public static function update_view_count( $application_id ) {
		update_term_meta( $application_id, '_frm_view_count', self::count_views( $application_id ) );
		self::update_timestamp( $application_id );
	}

	/**
	 * Get number of views for application.
	 *
	 * @param int $application_id
	 * @return int
	 */
	private static function count_views( $application_id ) {
		return self::count_post_type( $application_id, 'frm_display' );
	}

	/**
	 * @param int $application_id
	 * @return void
	 */
	public static function update_page_count( $application_id ) {
		update_term_meta( $application_id, '_frm_page_count', self::count_pages( $application_id ) );
		self::update_timestamp( $application_id );
	}

	/**
	 * Get number of pages for application.
	 *
	 * @param int $application_id
	 * @return int
	 */
	private static function count_pages( $application_id ) {
		return self::count_post_type( $application_id, 'page' );
	}

	/**
	 * Get number of post items for application for a post specific type.
	 *
	 * @param int    $application_id
	 * @param string $post_type
	 * @return int
	 */
	private static function count_post_type( $application_id, $post_type ) {
		$ids = self::get_posts_for_application( $application_id, array( $post_type ), array( 'fields' => 'ids' ) );
		return count( $ids );
	}

	/**
	 * @return array
	 */
	public function as_js_object() {
		return array(
			'termId'    => $this->term->term_id,
			'slug'      => $this->term->slug,
			'name'      => $this->term->name,
			'editUrl'   => FrmProApplicationsHelper::get_edit_url( $this->term->term_id ),
			'createdAt' => $this->get_created_at(),
			'updatedAt' => $this->updated_at,
			'formCount' => (string) $this->get_count( 'form' ),
			'viewCount' => (string) $this->get_count( 'view' ),
			'pageCount' => (string) $this->get_count( 'page' ),
		);
	}

	/**
	 * @return int
	 */
	private function get_created_at() {
		return get_term_meta( $this->term->term_id, '_frm_created_at', true );
	}

	/**
	 * @param string $type supports 'view', 'page', and 'form'.
	 * @return int
	 */
	private function get_count( $type ) {
		$count = get_term_meta( $this->term->term_id, '_frm_' . $type . '_count', true );
		return is_numeric( $count ) ? absint( $count ) : 0;
	}

	/**
	 * @param int  $application_id
	 * @param bool $get_ids_only
	 * @return array<stdClass>|array<int>
	 */
	public static function get_forms_for_application( $application_id, $get_ids_only = false ) {
		$form_ids = get_term_meta( $application_id, '_frm_form_id' );
		if ( ! $form_ids ) {
			return array();
		}

		$where = array(
			'id'     => $form_ids,
			'status' => 'published',
		);

		if ( $get_ids_only ) {
			return array_map( 'intval', FrmDb::get_col( 'frm_forms', $where ) );
		}

		return FrmForm::getAll( $where );
	}

	/**
	 * @param int   $application_id
	 * @param array $post_type
	 * @param array $args {
	 *     @type string $fields use 'ids' to get ids instead of WP_Post objects.
	 * }
	 * @return array<WP_Post>|array<int>
	 */
	public static function get_posts_for_application( $application_id, $post_type = array( 'page', 'frm_display' ), $args = array() ) {
		if ( ! FrmProApplicationsHelper::views_is_active_and_supports_applications() ) {
			// Remove views from application if views is not active or up to date.
			$views_key = array_search( 'frm_display', $post_type, true );
			if ( false !== $views_key ) {
				unset( $post_type[ $views_key ] );
			}
		}

		$post_args = array(
			'post_type'   => $post_type,
			'post_status' => array( 'publish', 'private', 'draft' ),
			'numberposts' => -1,
			'tax_query'   => array(
				array(
					'taxonomy'         => 'frm_application',
					'field'            => 'term_id',
					'terms'            => $application_id,
					'include_children' => false,
				),
			),
			// Fixes a Polylang conflict. While Polylang was active, no views would appear in an Application.
			'lang'        => '',
		);

		if ( ! empty( $args['fields'] ) ) {
			$post_args['fields'] = $args['fields'];
		}

		return get_posts( $post_args );
	}

	/**
	 * Check if name is taken
	 *
	 * @param string $name
	 * @param int    $application_id
	 * @return bool
	 */
	public static function name_is_taken( $name, $application_id ) {
		$term = get_term_by( 'name', $name, 'frm_application' );
		if ( ! ( $term instanceof WP_Term ) ) {
			return false;
		}
		return $term->term_id !== $application_id;
	}
}
