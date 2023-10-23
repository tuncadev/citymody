<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

/**
 * @since 5.3
 */
class FrmProApplicationRelationHelper {

	/**
	 * @var array<int,string> $form_names_by_id
	 */
	private $form_names_by_id;

	/**
	 * @var array<int,string> $post_titles_by_id
	 */
	private $post_titles_by_id;

	/**
	 * @var array<int,string> $post_types_by_id
	 */
	private $post_types_by_id;

	/**
	 * @var array<int,int> $parent_post_ids_by_child_post_id
	 */
	private $parent_post_ids_by_child_post_id;

	/**
	 * @var array<int,int> $parent_post_ids_by_child_form_id
	 */
	private $parent_post_ids_by_child_form_id;

	/**
	 * @var array<int,array> $memoized_parent_of_data_by_post_id
	 */
	private $memoized_parent_of_data_by_post_id;

	/**
	 * @var array $view_ids_by_parent_form_id
	 */
	private $view_ids_by_parent_form_id;

	/**
	 * @var array $embedded_form_ids_by_parent_form_id
	 */
	private $embedded_form_ids_by_parent_form_id;

	/**
	 * @var array<int,int> $parent_form_ids_by_embedded_form_id
	 */
	private $parent_form_ids_by_embedded_form_id;

	/**
	 * @var array
	 */
	private $repeater_form_ids_by_parent_form_id;

	/**
	 * @var array<int,int> $parent_form_id_by_repeater_form_id
	 */
	private $parent_form_id_by_repeater_form_id;

	/**
	 * @param array<stdClass> $forms
	 * @param array<WP_Post>  $posts
	 * @return void
	 */
	public function __construct( $forms, $posts ) {
		$this->parent_post_ids_by_child_form_id    = array();
		$this->form_names_by_id                    = array();
		$this->view_ids_by_parent_form_id          = array();
		$this->embedded_form_ids_by_parent_form_id = array();
		$this->parent_form_id_by_repeater_form_id  = array();
		$this->repeater_form_ids_by_parent_form_id = array();
		$this->parent_form_ids_by_embedded_form_id = array();

		if ( $forms ) {
			$form_ids = array();
			foreach ( $forms as $form ) {
				$form_ids[]                                             = $form->id;
				$this->form_names_by_id[ $form->id ]                    = $form->name;
				$this->parent_post_ids_by_child_form_id[ $form->id ]    = array();
				$this->view_ids_by_parent_form_id[ $form->id ]          = array();
				$this->embedded_form_ids_by_parent_form_id[ $form->id ] = array();
				$this->parent_form_ids_by_embedded_form_id[ $form->id ] = array();
				$this->parent_form_id_by_repeater_form_id[ $form->id ]  = $form->parent_form_id;

				if ( ! empty( $form->parent_form_id ) ) {
					if ( ! array_key_exists( $form->parent_form_id, $this->repeater_form_ids_by_parent_form_id ) ) {
						$this->repeater_form_ids_by_parent_form_id[ $form->parent_form_id ] = array();
					}
					$this->repeater_form_ids_by_parent_form_id[ $form->parent_form_id ][] = $form->id;
				}
			}

			$this->set_embedded_form_info( $form_ids );
			unset( $form_ids );
		}

		$this->parent_post_ids_by_child_post_id   = array();
		$this->memoized_parent_of_data_by_post_id = array();
		$this->post_titles_by_id                  = array();
		$this->post_types_by_id                   = array();

		foreach ( $posts as $post ) {
			$this->post_titles_by_id[ $post->ID ]                = $post->post_title;
			$this->post_types_by_id[ $post->ID ]                 = $post->post_type;
			$this->parent_post_ids_by_child_post_id[ $post->ID ] = array();

			if ( 'frm_display' === $post->post_type ) {
				$form_id = get_post_meta( $post->ID, 'frm_form_id', true );
				if ( ! is_numeric( $form_id ) || ! array_key_exists( $form_id, $this->view_ids_by_parent_form_id ) ) {
					continue;
				}
				$this->view_ids_by_parent_form_id[ $form_id ][] = $post->ID;
			}
		}

		// Walk through posts and set children parents for embedded in column data.
		array_walk(
			$posts,
			function( $current ) {
				$parent_of_data = $this->get_parent_of_data( $current );
				$this->memoized_parent_of_data_by_post_id[ $current->ID ] = $parent_of_data;

				foreach ( $parent_of_data as $data ) {
					if ( ! empty( $data['view_id'] ) ) {
						$this->parent_post_ids_by_child_post_id[ $data['view_id'] ][] = $current->ID;
					} elseif ( ! empty( $data['form_id'] ) ) {
						$this->parent_post_ids_by_child_form_id[ $data['form_id'] ][] = $current->ID;
					}
				}
			}
		);
	}

	/**
	 * @param array<int> $form_ids
	 * @return void
	 */
	private function set_embedded_form_info( $form_ids ) {
		$where      = array(
			'form_id' => $form_ids,
			'type'    => 'form',
		);
		$field_data = FrmDb::get_results( 'frm_fields', $where, 'form_id, field_options' );
		foreach ( $field_data as $field ) {
			$field_options = $field->field_options;
			FrmProAppHelper::unserialize_or_decode( $field_options );

			if ( ! is_array( $field_options ) || ! array_key_exists( 'form_select', $field_options ) ) {
				continue;
			}

			$embedded_form_id = $field_options['form_select'];
			if ( ! array_key_exists( $embedded_form_id, $this->form_names_by_id ) ) {
				continue;
			}

			$this->parent_form_ids_by_embedded_form_id[ $embedded_form_id ][] = $field->form_id;
			$this->embedded_form_ids_by_parent_form_id[ $field->form_id ][]   = $embedded_form_id;
		}
	}

	/**
	 * @param WP_Post|stdClass $item
	 * @return array
	 */
	public function get_parent_of_data( $item ) {
		$is_post = $item instanceof WP_Post;

		if ( ! $is_post ) {
			return $this->get_parent_of_data_for_form( $item );
		}

		if ( array_key_exists( $item->ID, $this->memoized_parent_of_data_by_post_id ) ) {
			return $this->memoized_parent_of_data_by_post_id[ $item->ID ];
		}

		if ( 'frm_display' === $item->post_type ) {
			return $this->get_parent_of_data_for_view( $item );
		}

		return $this->get_parent_of_data_for_page( $item );
	}

	/**
	 * Get shortcode matches for all content in View (including detail page, before/after content and no entries message).
	 *
	 * @param WP_Post $view
	 * @return array
	 */
	private function get_parent_of_data_for_view( $view ) {
		if ( ! isset( $view->frm_empty_msg ) && is_callable( 'FrmViewsDisplaysHelper::setup_edit_vars' ) ) {
			$view = FrmViewsDisplaysHelper::setup_edit_vars( $view, false );
		}

		$keys = array( 'before_content', 'after_content', 'dyncontent', 'empty_msg' );
		$html = $view->post_content;
		foreach ( $keys as $key ) {
			$frm_key = 'frm_' . $key;
			if ( ! empty( $view->$frm_key ) ) {
				$html .= $view->$frm_key;
			}
		}

		return $this->get_shortcode_matches_from_string( $html );
	}

	/**
	 * @param string $string
	 * @param array  $tags
	 * @return array
	 */
	private function get_shortcode_matches_from_string( $string, $tags = array( 'formidable', 'display-frm-data' ) ) {
		return FrmProApplicationsHelper::get_shortcode_matches_from_string( $string, array( $this, 'handle_shortcode' ), $tags );
	}

	/**
	 * @param string $shortcode_found
	 * @param int    $id_found
	 * @return array|false
	 */
	public function handle_shortcode( $shortcode_found, $id_found ) {
		switch ( $shortcode_found ) {
			case 'formidable':
				return $this->handle_form_shortcode( $id_found );

			case 'display-frm-data':
				return $this->handle_view_shortcode( $id_found );
		}
		return false;
	}

	/**
	 * @param int|string $form_id Form id or key.
	 * @return array|false
	 */
	private function handle_form_shortcode( $form_id ) {
		if ( ! is_numeric( $form_id ) ) {
			$form_id = FrmForm::get_id_by_key( $form_id );
		}
		if ( ! array_key_exists( $form_id, $this->form_names_by_id ) ) {
			return false;
		}
		return $this->map_form_id_to_info_array( $form_id );
	}

	/**
	 * @param int|string $view_id View id or key.
	 * @return array|false
	 */
	private function handle_view_shortcode( $view_id ) {
		if ( ! is_numeric( $view_id ) ) {
			if ( ! is_callable( 'FrmViewsDisplay::get_id_by_key' ) ) {
				return false;
			}

			$view_id = FrmViewsDisplay::get_id_by_key( $view_id );
		}

		if ( ! array_key_exists( $view_id, $this->post_titles_by_id ) ) {
			return false;
		}

		return $this->map_view_id_to_info_array( $view_id );
	}

	/**
	 * @param WP_Post $page
	 * @return array
	 */
	private function get_parent_of_data_for_page( $page ) {
		return self::get_shortcode_matches_from_string( $page->post_content );
	}

	/**
	 * @param stdClass $form
	 * @return array
	 */
	private function get_parent_of_data_for_form( $form ) {
		$parent_of_data = array_merge(
			// A form could be a parent of an embedded form. A form would not be a parent of a view.
			$this->map_form_ids_to_info_arrays( $this->embedded_form_ids_by_parent_form_id[ $form->id ] ),
			// A form is a parent of whatever views are included with it.
			$this->map_view_ids_to_info_arrays( $this->view_ids_by_parent_form_id[ $form->id ] )
		);

		if ( array_key_exists( $form->id, $this->repeater_form_ids_by_parent_form_id ) ) {
			$parent_of_data = array_merge(
				$parent_of_data,
				$this->map_form_ids_to_info_arrays( $this->repeater_form_ids_by_parent_form_id[ $form->id ] )
			);
		}

		return $parent_of_data;
	}

	/**
	 * @param int $post_id
	 * @return array
	 */
	private function map_post_id_to_info_array( $post_id ) {
		$post_type = $this->post_types_by_id[ $post_id ];
		if ( 'frm_display' === $post_type ) {
			return $this->map_view_id_to_info_array( $post_id );
		}

		return array(
			'type'    => __( 'PAGE', 'formidable-pro' ),
			'view_id' => $post_id,
			'label'   => $this->post_titles_by_id[ $post_id ],
		);
	}

	/**
	 * @param int $view_id
	 * @return array
	 */
	private function map_view_id_to_info_array( $view_id ) {
		return array(
			'type'    => __( 'VIEW', 'formidable-pro' ),
			'view_id' => $view_id,
			'label'   => $this->post_titles_by_id[ $view_id ],
		);
	}

	/**
	 * @param int $form_id
	 * @return array
	 */
	private function map_form_id_to_info_array( $form_id ) {
		return array(
			'type'    => __( 'FORM', 'formidable-pro' ),
			'form_id' => $form_id,
			'label'   => $this->form_names_by_id[ $form_id ],
		);
	}

	/**
	 * @param WP_Post|stdClass $item
	 * @return array
	 */
	public function get_embedded_in_data( $item ) {
		if ( $item instanceof WP_Post ) {
			if ( 'frm_display' === $item->post_type ) {
				return $this->get_embedded_in_data_for_view( $item );
			}
			// Pages are not embedded in anything, return nothing.
			return array();
		}
		return $this->get_embedded_in_data_for_form( $item );
	}

	/**
	 * @param WP_Post $item
	 * @return array
	 */
	private function get_embedded_in_data_for_view( $item ) {
		if ( ! array_key_exists( $item->ID, $this->parent_post_ids_by_child_post_id ) ) {
			return array();
		}
		return $this->map_post_ids_to_info_arrays( $this->parent_post_ids_by_child_post_id[ $item->ID ] );
	}

	/**
	 * @param stdClass $form
	 * @return array<array>
	 */
	private function get_embedded_in_data_for_form( $form ) {
		$embedded_form_parent_info = $this->map_form_ids_to_info_arrays( $this->parent_form_ids_by_embedded_form_id[ $form->id ] );
		$view_parent_info          = $this->map_post_ids_to_info_arrays( $this->parent_post_ids_by_child_form_id[ $form->id ] );
		$info                      = array_merge( $embedded_form_parent_info, $view_parent_info );

		if ( ! empty( $this->parent_form_id_by_repeater_form_id[ $form->id ] ) ) {
			$parent_form_id = $this->parent_form_id_by_repeater_form_id[ $form->id ];

			if ( array_key_exists( $parent_form_id, $this->form_names_by_id ) ) {
				$info[] = $this->map_form_id_to_info_array( $parent_form_id );
			}
		}

		return $info;
	}

	/**
	 * @param array<int> $post_ids
	 * @return array<array>
	 */
	private function map_post_ids_to_info_arrays( $post_ids ) {
		return array_map( array( $this, 'map_post_id_to_info_array' ), $post_ids );
	}

	/**
	 * @param array<int> $view_ids
	 * @return array<array>
	 */
	private function map_view_ids_to_info_arrays( $view_ids ) {
		return array_map( array( $this, 'map_view_id_to_info_array' ), $view_ids );
	}

	/**
	 * @param array<int> $form_ids
	 * @return array<array>
	 */
	private function map_form_ids_to_info_arrays( $form_ids ) {
		return array_map( array( $this, 'map_form_id_to_info_array' ), $form_ids );
	}

	/**
	 * Return if specific form id was found in another form's embedded field form select field option data.
	 *
	 * @param int $form_id
	 * @return bool
	 */
	public function form_is_an_embed_field_form( $form_id ) {
		return ! empty( $this->parent_form_ids_by_embedded_form_id[ $form_id ] );
	}
}
