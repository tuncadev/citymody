<?php

/**
 * Register Custom Post Type
 */
if (!function_exists('glf_register_post_type')) {
	function glf_register_post_type()
	{
		$cpt_args = apply_filters('glf_register_post_type', array());

		foreach ($cpt_args as $post_type => $args) {
			$post_type_name = !is_array($args)
				? $args
				: (isset($args['labels']) && isset($args['labels']['name'])
					? $args['labels']['name']
					: (isset($args['label']) ? $args['label'] : $post_type));

			$singular_name = $post_type_name;

			if (!is_array($args)) {
				$args = array();
				$args['labels'] = array();
			} else {
				if (!isset($args['labels'])) {
					$args['labels'] = array();
				}
				if (isset($args['label'])) {
					$args['labels']['name'] = $args['label'];
				}
				if (isset($args['singular_name'])) {
					$singular_name = $args['singular_name'];
				}
			}

			$defaults = array(
				'label'              => $post_type_name,
				'public'             => true,
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'query_var'          => true,
				'rewrite'            => array('slug' => $post_type),
				'capability_type'    => 'post',
				'has_archive'        => true,
				'hierarchical'       => false,
				'menu_position'      => null,
				'supports'           => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
				'labels'             => array(
					'name'                  => $post_type_name,
					'singular_name'         => $singular_name,
					'add_new_item'          => sprintf(__('Add New %s', 'civi-framework'), $singular_name),
					'edit_item'             => sprintf(__('Edit %s', 'civi-framework'), $singular_name),
					'new_item'              => sprintf(__('New %s', 'civi-framework'), $singular_name),
					'view_item'             => sprintf(__('View %s', 'civi-framework'), $singular_name),
					'search_items'          => sprintf(__('Search %s', 'civi-framework'), $post_type_name),
					'not_found'             => sprintf(__('No %s found.', 'civi-framework'), strtolower($post_type_name)),
					'not_found_in_trash'    => sprintf(__('No %s found in Trash.', 'civi-framework'), strtolower($post_type_name)),
					'all_items'             => sprintf(__('All %s', 'civi-framework'), $post_type_name),
					'archives'              => sprintf(__('%s Archives', 'civi-framework'), $post_type_name),
					'insert_into_item'      => sprintf(__('Insert into %s', 'civi-framework'), strtolower($singular_name)),
					'uploaded_to_this_item' => sprintf(__('Uploaded to this %s', 'civi-framework'), strtolower($singular_name)),
					'filter_items_list'     => sprintf(__('Filter %s list', 'civi-framework'), strtolower($post_type_name)),
					'items_list_navigation' => sprintf(__('%s list navigation', 'civi-framework'), $post_type_name),
					'items_list'            => sprintf(__('%s list', 'civi-framework'), $post_type_name),
				)
			);
			$args = wp_parse_args($args, $defaults);
			$args['labels'] = wp_parse_args($args['labels'], $defaults['labels']);

			register_post_type($post_type, $args);
		}
	}

	add_action('init', 'glf_register_post_type', 0);
}
