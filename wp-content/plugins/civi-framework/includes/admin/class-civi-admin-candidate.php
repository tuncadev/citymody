<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

/**
 *  Class Civi_Admin_Candidate
 */
class Civi_Admin_Candidate
{
	/**
	 *  Register custom columns
	 *
	 *  @param  $columns
	 *  @return  array
	 *
	 */
	public function register_custom_column_titles($columns)
	{
		unset($columns['tags']);

		$columns['thumb']       = esc_html__('Avatar', 'civi-framework');
		$columns['title']       = esc_html__('Candidate', 'civi-framework');
		$columns['cate']  = esc_html__('Categories', 'civi-framework');
		$columns['skills'] = esc_html__('Skills', 'civi-framework');
		/* Physical Attr */

		/* *** */
		$columns['author']      = esc_html__('Author', 'civi-framework');
		$new_columns    = array();
		$custom_order   = array('cb', 'thumb', 'title', 'cate', 'skills', 'author', 'date');

		foreach ($custom_order as $colname) {
			$new_columns[$colname] = $columns[$colname];
		}

		return $new_columns;
	}

	/**
	 *  Display custom column for candidates
	 *
	 *  @param  $column
	 *
	 */
	public function display_custom_column($column)
	{
		global $post;
		switch ($column) {
			case 'thumb':
				$author_id = get_post_field('post_author', $post->ID);
				$candidate_avatar = get_the_author_meta('author_avatar_image_url', $author_id);
				if (!empty($candidate_avatar)) {
					echo '<img src = " ' . $candidate_avatar . '" alt=""/>';
				} else {
					echo '&ndash;';
				}
				break;
			case 'cate':
				echo civi_admin_taxonomy_terms($post->ID, 'candidate_categories', 'candidate');
				break;
			case 'skills':
				echo civi_admin_taxonomy_terms($post->ID, 'candidate_skills', 'candidate');
				break;
			case 'author':
				echo '<a href="' . esc_url(add_query_arg('author', $post->post_author)) . '">' . get_the_author() . '</a>';
				break;
		}
	}

	/**
	 *  Sortable columns
	 *
	 *  @param  $columns
	 *  @return mixed
	 *
	 */
	public function sortable_columns($columns)
	{
		$columns['cate']  = 'cate';
		$columns['skills']  = 'skills';
		$columns['post_date']   = 'post_date';
		return $columns;
	}

	/**
	 *  Modify Candidate Slug
	 *
	 *  @param  $existing_slug
	 *  @return $string
	 *
	 */
	public function modify_candidate_slug($existing_slug)
	{
		$candidate_url_slug = civi_get_option('candidate_url_slug');
        $enable_slug_categories = civi_get_option('enable_slug_categories');
        if ($candidate_url_slug) {
            if($enable_slug_categories == 1){
                return $candidate_url_slug . '/%candidate_categories%';
            } else {
                return $candidate_url_slug;
            }
		}

		return $existing_slug;
	}

	public function modify_candidate_has_archive($existing_slug)
	{
		$candidate_url_slug = civi_get_option('candidate_url_slug');
		if ($candidate_url_slug) {
			return $candidate_url_slug;
		}
		return $existing_slug;
	}

	/**
	 * Modify candidate categories slug
	 * @param $existing_slug
	 * @return string
	 */
	public function modify_candidate_categories_url_slug($existing_slug)
	{
		$candidate_categories_url_slug = civi_get_option('candidate_categories_url_slug');
		if ($candidate_categories_url_slug) {
			return $candidate_categories_url_slug;
		}
		return $existing_slug;
	}

	/**
	 * Modify candidate ages slug
	 * @param $existing_slug
	 * @return string
	 */
	public function modify_candidate_ages_url_slug($existing_slug)
	{
		$candidate_ages_url_slug = civi_get_option('candidate_ages_url_slug');
		if ($candidate_ages_url_slug) {
			return $candidate_ages_url_slug;
		}
		return $existing_slug;
	}

	/**
	 * Modify candidate languages slug
	 * @param $existing_slug
	 * @return string
	 */
	public function modify_candidate_languages_url_slug($existing_slug)
	{
		$candidate_languages_url_slug = civi_get_option('candidate_languages_url_slug');
		if ($candidate_languages_url_slug) {
			return $candidate_languages_url_slug;
		}
		return $existing_slug;
	}

	/**
	 * Modify candidate qualification slug
	 * @param $existing_slug
	 * @return string
	 */
	public function modify_candidate_qualification_url_slug($existing_slug)
	{
		$candidate_qualification_url_slug = civi_get_option('candidate_qualification_url_slug');
		if ($candidate_qualification_url_slug) {
			return $candidate_qualification_url_slug;
		}
		return $existing_slug;
	}

	/**
	 * Modify candidate salary types slug
	 * @param $existing_slug
	 * @return string
	 */
	public function modify_candidate_salary_types_url_slug($existing_slug)
	{
		$candidate_salary_types_url_slug = civi_get_option('candidate_salary_types_url_slug');
		if ($candidate_salary_types_url_slug) {
			return $candidate_salary_types_url_slug;
		}
		return $existing_slug;
	}

	/**
	 * Modify candidate yoe slug
	 * @param $existing_slug
	 * @return string
	 */
	public function modify_candidate_yoe_url_slug($existing_slug)
	{
		$candidate_yoe_url_slug = civi_get_option('candidate_yoe_url_slug');
		if ($candidate_yoe_url_slug) {
			return $candidate_yoe_url_slug;
		}
		return $existing_slug;
	}

	/**
	 * Modify candidate education levels slug
	 * @param $existing_slug
	 * @return string
	 */
	public function modify_candidate_education_levels_url_slug($existing_slug)
	{
		$candidate_education_levels_url_slug = civi_get_option('candidate_education_levels_url_slug');
		if ($candidate_education_levels_url_slug) {
			return $candidate_education_levels_url_slug;
		}
		return $existing_slug;
	}

	/**
	 * Modify candidate skills slug
	 * @param $existing_slug
	 * @return string
	 */
	public function modify_candidate_skills_url_slug($existing_slug)
	{
		$candidate_skills_url_slug = civi_get_option('candidate_skills_url_slug');
		if ($candidate_skills_url_slug) {
			return $candidate_skills_url_slug;
		}
		return $existing_slug;
	}

	/**
	 * Modify candidate locations slug
	 * @param $existing_slug
	 * @return string
	 */
	public function modify_candidate_locations_url_slug($existing_slug)
	{
		$candidate_locations_url_slug = civi_get_option('candidate_locations_url_slug');
		if ($candidate_locations_url_slug) {
			return $candidate_locations_url_slug;
		}
		return $existing_slug;
	}

	/**
	 * filter_restrict_manage_company
	 */
	public function filter_restrict_manage_candidate()
	{
		global $typenow;
		$post_type = 'candidate';
		if ($typenow == $post_type) {
			$taxonomy_arr  = array('candidate_categories', 'candidate_skills');
			foreach ($taxonomy_arr as $taxonomy) {
				$selected      = isset($_GET[$taxonomy]) ? civi_clean(wp_unslash($_GET[$taxonomy])) : '';
				$info_taxonomy = get_taxonomy($taxonomy);
				wp_dropdown_categories(array(
					'show_option_all' => __("All {$info_taxonomy->label}"),
					'taxonomy'        => $taxonomy,
					'name'            => $taxonomy,
					'orderby'         => 'name',
					'selected'        => $selected,
					'hide_empty'      => false,
				));
			}
?>
            <?php
		};
	}

	/**
	 *  Show Candidate
	 *
	 */
	public function show_candidates()
	{
		if (!empty($_GET['show_listing']) && wp_verify_nonce($_REQUEST['_wpnonce'], 'show_listing') && current_user_can('publish_post', $_GET['show_listing'])) {
			$post_id = absint(civi_clean(wp_unslash($_GET['show_listing'])));
			$listing_data   = array(
				'ID'            => $post_id,
				'post_status'   => 'publish'
			);

			wp_update_post($listing_data);
			wp_redirect(remove_query_arg('show_listing', add_query_arg('show_listing', $post_id, admin_url('edit.php?post_type=candidate'))));
			exit;
		}
	}

	/**
	 * h_filter
	 * @param $query
	 */
	public function candidate_filter($query)
	{
		global $pagenow;
		$post_type = 'candidate';
		$q_vars    = &$query->query_vars;
		if ($pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type) {
			$taxonomy_arr  = array('candidate_categories', 'candidate_skills');
			foreach ($taxonomy_arr as $taxonomy) {
				if (isset($q_vars[$taxonomy]) && is_numeric($q_vars[$taxonomy]) && $q_vars[$taxonomy] != 0) {
					$term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
					$q_vars[$taxonomy] = $term->slug;
				}
			}
		}
	}
}
