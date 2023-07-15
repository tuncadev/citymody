<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

if (!class_exists('Civi_Admin')) {
	/**
	 * Class Civi_Admin
	 */
	class Civi_Admin
	{

		/**
		 * Remove admin bar
		 * @return bool
		 */
		function remove_admin_bar()
		{
			if (!current_user_can('administrator') && !is_admin()) {
				show_admin_bar(false);
			}
		}

		/**
		 * Check if it is a jobs edit page.
		 * @return bool
		 */
		public function is_civi_admin()
		{
			if (is_admin()) {
				global $pagenow;
				if (in_array($pagenow, array('edit.php', 'post.php', 'post-new.php', 'edit-tags.php'))) {
					global $post_type;
					if ('jobs' == $post_type) {
						return true;
					}
				}
			}
			return false;
		}

		/**
		 * Register admin_menu
		 */
		public function admin_menu()
		{
			$enable_claim_listing = civi_get_option('enable_claim_listing', '1');
			if ($enable_claim_listing) :
				add_menu_page(
					esc_html__('Claim Listing', 'civi-framework'),
					esc_html__('Claim Listing', 'civi-framework'),
					'manage_options',
					'claim_listing',
					array($this, 'menu_claim_listing_callback'),
					'dashicons-list-view',
					12
				);
			endif;
		}

		public function menu_claim_listing_callback()
		{
			$claim_email = $claim_name = $claim_username = $claim_status = '';

			$meta_query = array(
				'relative' => 'AND',
				array(
					'key' => 'civi-claim_request',
					'value' => 1,
					'compare' => '=',
				),
			);
			if (isset($_GET['claim_name']) && $_GET['claim_name'] != '') {
				$claim_name = $_GET['claim_name'];
				$meta_query[] = array(
					'key' => 'civi-cd_your_name',
					'value' => $_GET['claim_name'],
					'compare' => 'LIKE',
				);
			}
			if (isset($_GET['claim_email']) && $_GET['claim_email'] != '') {
				$claim_email = $_GET['claim_email'];
				$meta_query[] = array(
					'key' => 'civi-cd_your_email',
					'value' => $_GET['claim_email'],
					'compare' => 'LIKE',
				);
			}
			if (isset($_GET['claim_username']) && $_GET['claim_username'] != '') {
				$claim_username = $_GET['claim_username'];
				$meta_query[] = array(
					'key' => 'civi-cd_your_username',
					'value' => $_GET['claim_username'],
					'compare' => 'LIKE',
				);
			}
			if (isset($_GET['claim_status']) && $_GET['claim_status'] != '') {
				$claim_status = $_GET['claim_status'];
				$meta_query[] = array(
					'key' => 'civi-cd_status',
					'value' => $_GET['claim_status'],
					'compare' => '=',
				);
			}
			$paged = isset($_REQUEST['paged']) ? max(1, (int)$_REQUEST['paged']) : 1;
			$args = array(
				'post_type' => 'jobs',
				'posts_per_page' => 20,
				'paged' => $paged,
				'post_status' => 'publish',
				'meta_query' => $meta_query,
			);
			// The Query
			$the_query = new WP_Query($args);
			$count = $the_query->found_posts;
?>
			<div class="civi-wrap wrap about-wrap claim-wrap">
				<div class="entry-search">
					<div class="claim-action">
						<a href="#" class="button button-delete"><?php esc_html_e('Delete', 'civi-framework'); ?></a>
					</div>
					<form action="" method="GET" class="claimFilter">
						<div class="field-group">
							<input type="text" name="claim_name" value="<?php echo $claim_name; ?>" placeholder="<?php esc_html_e('Name', 'civi-framework'); ?>">
							<input type="email" name="claim_email" value="<?php echo $claim_email; ?>" placeholder="<?php esc_html_e('Email', 'civi-framework'); ?>">
							<input type="text" name="claim_username" value="<?php echo $claim_username; ?>" placeholder="<?php esc_html_e('Username', 'civi-framework'); ?>">
							<select name="claim_status" id="claim_status">
								<option value=""><?php esc_html_e('All Status', 'civi-framework'); ?></option>
								<option value="pending" <?php if ($claim_status == 'pending') {
															echo 'selected';
														} ?>><?php esc_html_e('Pending', 'civi-framework'); ?></option>
								<option value="accept" <?php if ($claim_status == 'accept') {
															echo 'selected';
														} ?>><?php esc_html_e('Accept', 'civi-framework'); ?></option>
								<option value="refuse" <?php if ($claim_status == 'refuse') {
															echo 'selected';
														} ?>><?php esc_html_e('Refuse', 'civi-framework'); ?></option>
							</select>
							<input type="hidden" name="page" value="claim_listing">
							<input type="submit" name="submit" value="<?php esc_html_e('Filter', 'civi-framework'); ?>">
						</div>
					</form>
					<div class="total"><?php printf(_n('%s item', '%s items', $count, 'civi-framework'), '<span class="count">' . esc_html($count) . '</span>'); ?></div>
				</div>

				<div class="wrap-content">
					<form action="" method="POST">
						<table class="table-changelogs">
							<thead>
								<tr>
									<th><input type="checkbox" id="checkall" name="claim_item"></th>
									<th><?php esc_html_e('Name', 'civi-framework'); ?></th>
									<th><?php esc_html_e('Email', 'civi-framework'); ?></th>
									<th><?php esc_html_e('Username', 'civi-framework'); ?></th>
									<th><?php esc_html_e('Listing Url', 'civi-framework'); ?></th>
									<th><?php esc_html_e('Messager', 'civi-framework'); ?></th>
									<th><?php esc_html_e('Status', 'civi-framework'); ?></th>
									<th><?php esc_html_e('Action', 'civi-framework'); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php
								// The Loop
								if ($the_query->have_posts()) {

									$i = 0;
									while ($the_query->have_posts()) {
										$the_query->the_post();
										$i++;
										$id = get_the_ID();
										$cd_your_name = get_post_meta($id, CIVI_METABOX_PREFIX . 'cd_your_name', true);
										$cd_your_email = get_post_meta($id, CIVI_METABOX_PREFIX . 'cd_your_email', true);
										$cd_your_listing = get_post_meta($id, CIVI_METABOX_PREFIX . 'cd_your_listing', true);
										$cd_your_username = get_post_meta($id, CIVI_METABOX_PREFIX . 'cd_your_username', true);
										$cd_messager = get_post_meta($id, CIVI_METABOX_PREFIX . 'cd_messager', true);
										$cd_status = get_post_meta($id, CIVI_METABOX_PREFIX . 'cd_status', true);
										$verified_listing = get_post_meta($id, CIVI_METABOX_PREFIX . 'verified_listing', true);
										echo '<tr>';
										echo '<td><input type="checkbox" name="claim_item"></td>';
										echo '<td>' . $cd_your_name . '</td>';
										echo '<td>' . $cd_your_email . '</td>';
										echo '<td>' . $cd_your_username . '</td>';
										echo '<td><a href="' . $cd_your_listing . '" target="_Blank">' . $cd_your_listing . '</a></td>';
										echo '<td>' . $cd_messager . '</td>';
										if ($cd_status == 'pending') {
											$value = 'pending';
										} else if ($verified_listing == 1) {
											$value = 'accept';
										} else {
											$value = 'refuse';
										}
										$data = 'data-status="' . $value . '"';
										echo '<td class="status"' . $data . '>' . $value . '</td>';
										echo '<td>
                                                <input type="submit" data-status="accept" data-jobs_id="' . $id . '" class="button button-primary" value="' . esc_attr('Accept', 'civi-framework') . '">
                                                <input type="submit" data-status="refuse" data-jobs_id="' . $id . '" class="button button-secondary" value="' . esc_attr('Refuse', 'civi-framework') . '">
                                                <input type="submit" data-status="delete" data-jobs_id="' . $id . '" class="button button-delete" value="' . esc_attr('Delete', 'civi-framework') . '"></td>';
										echo '</tr>';
									}
								} else {
									echo '<tr class="align-center">';
									echo '<td colspan="7">' . esc_attr('No result', 'civi-framework') . '</td>';
									echo '</tr>';
								}
								/* Restore original Post Data */
								wp_reset_postdata();
								?>
							</tbody>
						</table>
						<div class="pagination">
							<?php
							$big = 999999999; // need an unlikely integer

							echo paginate_links(array(
								'base' => admin_url('admin.php?page=claim_listing&paged=%#%'),
								'format' => '?paged=%#%',
								'current' => max(1, $paged),
								'total' => $the_query->max_num_pages,
								'prev_text' => '<i class="far fa-angle-left"></i>',
								'next_text' => '<i class="far fa-angle-right"></i>',
							));
							?>
						</div>
						<div class="civi-loading-effect"><span class="civi-dual-ring small"></span></div>
					</form>
				</div><!-- end .wrap-content -->
			</div>
		<?php
		}

		/**
		 * Register post_type
		 * @param $post_types
		 * @return mixed
		 */
		public function register_post_type($post_types)
		{
			$post_types['jobs'] = array(
				'labels' => array(
					'name' => esc_html__('Jobs', 'civi-framework'),
					'singular_name' => esc_html__('Jobs', 'civi-framework'),
					'all_items' => esc_html__('Jobs', 'civi-framework'),
				),
				'supports' => array('title', 'editor', 'excerpt', 'author', 'thumbnail', 'revisions', 'page-attributes', 'comments'),
				'menu_icon' => 'dashicons-hammer',
				'can_export' => true,
				'show_in_rest' => true,
				'capability_type' => 'jobs',
				'map_meta_cap' => true,
				'rewrite' => array(
					'slug' => apply_filters('civi_jobs_slug', 'jobs'),
					'with_front' => false,
				),
				'show_ui'	=> true,
				'menu_position' => 3,
				'has_archive' => apply_filters('civi_jobs_has_archive', 'jobs'),
				'show_in_menu' => true,
			);

			$post_types['applicants'] = array(
				'labels' => array(
					'name' => esc_html__('Applicants', 'civi-framework'),
					'singular_name' => esc_html__('Applicants', 'civi-framework'),
					'all_items' => esc_html__('Applicants', 'civi-framework'),
				),
				'menu_icon' => 'dashicons-universal-access-alt',
				'capabilities' => $this->get_applicants_capabilities(),
				'map_meta_cap' => true,
				'supports' => array('title'),
				'rewrite' => array(
					'slug' => apply_filters('civi_applicants_slug', 'applicants'),
				),
				'show_in_admin_bar' => true,
				'menu_position' => 4,
			);

			if (civi_get_option('enable_job_alerts') === '1') {
				$post_types['job_alerts'] = array(
					'labels' => array(
						'name' => esc_html__('Job Alerts', 'civi-framework'),
						'singular_name' => esc_html__('Job Alerts', 'civi-framework'),
						'all_items' => esc_html__('Job Alerts', 'civi-framework'),
					),
					'supports' => array('title'),
					'menu_icon'         => 'dashicons-email-alt',
					'has_archive'       => false,
					'publicly_queryable' => false,
					'show_in_rest'		=> false,
					'show_ui'	=> true,
					'show_in_menu' => true,
					'menu_position' => 5,
				);
			}

			$post_types['company'] = array(
				'labels' => array(
					'name' => esc_html__('Companies', 'civi-framework'),
					'singular_name' => esc_html__('Companies', 'civi-framework'),
					'all_items' => esc_html__('Companies', 'civi-framework'),
				),
				'supports' => array('title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments'),
				'menu_icon' => 'dashicons-admin-multisite',
				'can_export' => true,
				'show_in_rest' => true,
				'capability_type' => 'company',
				'map_meta_cap' => true,
				'rewrite' => array(
					'slug' => apply_filters('civi_company_slug', 'company'),
					'with_front' => false,
				),
				'has_archive' => apply_filters('civi_company_has_archive', 'company'),
				'show_in_admin_bar' => true,
				'menu_position' => 6,
			);

			$post_types['package'] = array(
				'labels' => array(
					'name' => esc_html__('Package', 'civi-framework'),
					'singular_name' => esc_html__('Package', 'civi-framework'),
					'all_items' => esc_html__('Package', 'civi-framework'),
				),
				'supports' => array('title', 'thumbnail'),
				'menu_icon' => 'dashicons-archive',
				'can_export' => true,
				'show_in_rest' => true,
				'capability_type' => 'package',
				'map_meta_cap' => true,
				'rewrite' => array(
					'slug' => apply_filters('civi_package_slug', 'package'),
				),
				'show_ui'	=> true,
				'show_in_menu' => true,
				'menu_position' => 7,
			);

			$post_types['user_package'] = array(
				'labels' => array(
					'name' => esc_html__('User Packages', 'civi-framework'),
					'singular_name' => esc_html__('User Packages', 'civi-framework'),
					'all_items' => esc_html__('User Packages', 'civi-framework'),
				),
				'supports' => array('title', 'excerpt'),
				'menu_icon' => 'dashicons-money',
				'can_export' => true,
				'capabilities' => $this->get_user_package_capabilities(),
				'map_meta_cap' => true,
				'rewrite' => array(
					'slug' => apply_filters('civi_user_package_slug', 'user_package'),
				),
				'show_ui'	=> true,
				'show_in_menu' => true,
				'menu_position' => 8,
			);

			$post_types['invoice'] = array(
				'labels' => array(
					'name' => esc_html__('Invoices', 'civi-framework'),
					'singular_name' => esc_html__('Invoices', 'civi-framework'),
					'all_items' => esc_html__('Invoices', 'civi-framework'),
				),
				'supports' => array('title', 'excerpt'),
				'menu_icon' => 'dashicons-list-view',
				'capabilities' => $this->get_invoice_capabilities(),
				'map_meta_cap' => true,
				'rewrite' => array(
					'slug' => apply_filters('civi_invoice_slug', 'invoice'),
				),
				'show_ui'	=> true,
				'show_in_menu' => true,
				'menu_position' => 9,
			);

			$post_types['candidate'] = array(
				'labels' => array(
					'name' => esc_html__('Candidates', 'civi-framework'),
					'singular_name' => esc_html__('Candidates', 'civi-framework'),
					'all_items' => esc_html__('Candidates', 'civi-framework'),
				),
				'supports' => array('title', 'editor', 'excerpt', 'author', 'thumbnail', 'page-attributes', 'comments'),
				'menu_icon' => 'dashicons-buddicons-buddypress-logo',
				'can_export' => true,
				'show_in_rest' => true,
				'capabilities' => $this->get_candidate_capabilities(),
				'map_meta_cap' => true,
				'rewrite' => array(
					'slug' => apply_filters('civi_candidate_slug', 'candidate'),
					'with_front' => false,
				),
				'has_archive' => apply_filters('civi_candidate_has_archive', 'candidate'),
				'show_ui'	=> true,
				'show_in_menu' => true,
				'menu_position' => 10,
			);

			$post_types['messages'] = array(
				'labels' => array(
					'name' => esc_html__('Messages', 'civi-framework'),
					'singular_name' => esc_html__('Messages', 'civi-framework'),
					'all_items' => esc_html__('Messages', 'civi-framework'),
				),
				'supports' => array('title', 'excerpt'),
				'menu_icon'         => 'dashicons-format-chat',
				'has_archive'       => false,
				'publicly_queryable' => false,
				'show_in_rest'		=> false,
				'show_ui'	=> true,
				'show_in_menu' => true,
				'menu_position' => 11,
			);

			$post_types['notification'] = array(
				'labels' => array(
					'name' => esc_html__('Notification', 'civi-framework'),
					'singular_name' => esc_html__('Notification', 'civi-framework'),
					'all_items' => esc_html__('Notification', 'civi-framework'),
				),
				'supports' => array('title', 'excerpt'),
				'menu_icon'         => 'dashicons-bell',
				'has_archive'       => false,
				'publicly_queryable' => false,
				'show_in_rest'		=> false,
				'show_ui'	=> true,
				'show_in_menu' => true,
				'menu_position' => 12,
			);

			$post_types['meetings'] = array(
				'labels' => array(
					'name' => esc_html__('Meetings', 'civi-framework'),
					'singular_name' => esc_html__('Meetings', 'civi-framework'),
					'all_items' => esc_html__('Meetings', 'civi-framework'),
				),
				'supports' => array('title'),
				'menu_icon' => 'dashicons-calendar-alt',
				'capabilities' => $this->get_meetings_capabilities(),
				'map_meta_cap' => true,
				'rewrite' => array(
					'slug' => apply_filters('civi_meetings_slug', 'meetings'),
				),
				'show_ui'	=> true,
				'show_in_menu' => true,
				'menu_position' => 13,
			);

			return $post_types;
		}

		/**
		 * Register jobs post status
		 */
		public function register_post_status()
		{
			register_post_status('expired', array(
				'label' => _x('Expired', 'post status', 'civi-framework'),
				'public' => true,
				'protected' => true,
				'exclude_from_search' => true,
				'show_in_admin_all_list' => true,
				'show_in_admin_status_list' => true,
				'label_count' => _n_noop('Expired <span class="count">(%s)</span>', 'Expired <span class="count">(%s)</span>', 'civi-framework'),
			));

			register_post_status('pause', array(
				'label' => _x('Pause', 'post status', 'civi-framework'),
				'public' => true,
				'protected' => true,
				'exclude_from_search' => true,
				'show_in_admin_all_list' => true,
				'show_in_admin_status_list' => true,
				'label_count' => _n_noop('Pause <span class="count">(%s)</span>', 'Pause <span class="count">(%s)</span>', 'civi-framework'),
			));

			register_post_status('canceled', array(
				'label' => _x('Canceled', 'post status', 'civi-framework'),
				'public' => true,
				'protected' => true,
				'exclude_from_search' => true,
				'show_in_admin_all_list' => true,
				'show_in_admin_status_list' => true,
				'label_count' => _n_noop('Canceled <span class="count">(%s)</span>', 'Canceled <span class="count">(%s)</span>', 'civi-framework'),
			));
		}

		/**
		 * Get invoice capabilities
		 * @return mixed
		 */
		private function get_invoice_capabilities()
		{
			$caps = array(
				'create_posts' => 'do_not_allow',
				'edit_post' => 'edit_invoices',
				'delete_posts' => 'delete_invoices'
			);
			return apply_filters('get_invoice_capabilities', $caps);
		}

		/**
		 * Get applicants capabilities
		 * @return mixed
		 */
		private function get_applicants_capabilities()
		{
			$caps = array(
				'create_posts' => 'do_not_allow',
				'edit_post' => 'edit_applicants',
				'delete_posts' => true,
			);
			return apply_filters('get_applicants_capabilities', $caps);
		}

		/**
		 * Get meetings capabilities
		 * @return mixed
		 */
		private function get_meetings_capabilities()
		{
			$caps = array(
				'create_posts' => 'do_not_allow',
				'edit_post' => 'edit_meetings',
				'delete_posts' => true,
			);
			return apply_filters('get_meetings_capabilities', $caps);
		}

		/**
		 * Get user_package capabilities
		 * @return mixed
		 */
		private function get_user_package_capabilities()
		{
			$caps = array(
				'create_posts' => 'do_not_allow',
				'edit_post' => 'edit_user_packages',
				'delete_posts' => 'do_not_allow'
			);
			return apply_filters('get_user_package_capabilities', $caps);
		}

		/**
		 * Get candidate capabilities
		 * @return mixed
		 */
		private function get_candidate_capabilities()
		{
			$caps = array(
				'create_posts' => 'do_not_allow',
				'edit_post' => 'edit_candidate',
				'delete_post' => 'delete_candidate',
			);
			return apply_filters('get_candidate_capabilities', $caps);
		}

		/**
		 * Register taxonomy
		 * @param $taxonomies
		 * @return mixed
		 */
		public function register_taxonomy($taxonomies)
		{
			// Candidate taxonomy
			$taxonomies['candidate_categories'] = array(
				'post_type' => 'candidate',
				'hierarchical' => true,
				'show_in_rest' => true,
				'label' => esc_html__('Categories', 'civi-framework'),
				'singular_name' => esc_html__('Candidate Category', 'civi-framework'),
				'rewrite' => array(
					'slug' => apply_filters('civi_candidate_categories_slug', 'candidate_categories'),
				),
			);
			$taxonomies['candidate_ages'] = array(
				'post_type' => 'candidate',
				'hierarchical' => true,
				'show_in_rest' => true,
				'label' => esc_html__('Ages', 'civi-framework'),
				'singular_name' => esc_html__('Candidate Age', 'civi-framework'),
				'rewrite' => array(
					'slug' => apply_filters('civi_candidate_ages_slug', 'candidate_ages'),
				),
			);

			$taxonomies['candidate_languages'] = array(
				'post_type' => 'candidate',
				'hierarchical' => true,
				'show_in_rest' => true,
				'label' => esc_html__('Languages', 'civi-framework'),
				'singular_name' => esc_html__('Candidate Language', 'civi-framework'),
				'rewrite' => array(
					'slug' => apply_filters('civi_candidate_languages_slug', 'candidate_languages'),
				),
			);
			$taxonomies['candidate_qualification'] = array(
				'post_type' => 'candidate',
				'hierarchical' => true,
				'show_in_rest' => true,
				'label' => esc_html__('Qualification', 'civi-framework'),
				'singular_name' => esc_html__('Candidate Qualification', 'civi-framework'),
				'rewrite' => array(
					'slug' => apply_filters('civi_candidate_qualification_slug', 'candidate_qualification'),
				),
			);

			$taxonomies['candidate_yoe'] = array(
				'post_type' => 'candidate',
				'hierarchical' => true,
				'show_in_rest' => true,
				'label' => esc_html__('Years of Experience', 'civi-framework'),
				'singular_name' => esc_html__('Candidate Years of Experience', 'civi-framework'),
				'rewrite' => array(
					'slug' => apply_filters('civi_candidate_yoe_slug', 'candidate_yoe'),
				),
			);

			$taxonomies['candidate_education_levels'] = array(
				'post_type' => 'candidate',
				'hierarchical' => true,
				'show_in_rest' => true,
				'label' => esc_html__('Levels of Education', 'civi-framework'),
				'singular_name' => esc_html__('Candidate Level of Education', 'civi-framework'),
				'rewrite' => array(
					'slug' => apply_filters('civi_candidate_education_levels_slug', 'candidate_education_levels'),
				),
			);

			$taxonomies['candidate_skills'] = array(
				'post_type' => 'candidate',
				'hierarchical' => true,
				'show_in_rest' => true,
				'label' => esc_html__('Skills', 'civi-framework'),
				'singular_name' => esc_html__('Candidate Skill', 'civi-framework'),
				'rewrite' => array(
					'slug' => apply_filters('civi_candidate_skills_slug', 'candidate_skills'),
				),
			);

			/* Bank */
			$taxonomies['candidate_paymentinfo'] = array(
				'post_type' => 'candidate',
				'hierarchical' => true,
				'show_in_rest' => true,
				'label' => esc_html__('Bank and Payment Informations', 'civi-framework'),
				'singular_name' => esc_html__('Bank and Payment Information', 'civi-framework'),
				'rewrite' => array(
					'slug' => apply_filters('civi_candidate_paymentinfo_slug', 'candidate_paymentinfo'),
				),
			);
			/* ******************* */

			$taxonomies['candidate_locations'] = array(
				'post_type' => 'candidate',
				'hierarchical' => true,
				'show_in_rest' => true,
				'label' => esc_html__('Locations', 'civi-framework'),
				'singular_name' => esc_html__('Candidate location', 'civi-framework'),
				'rewrite' => array(
					'slug' => apply_filters('civi_candidate_locations_slug', 'candidate_locations'),
				),
			);

			//Jobs
			$taxonomies['jobs-categories'] = array(
				'post_type' => 'jobs',
				'hierarchical' => true,
				'show_in_rest' => true,
				'label' => esc_html__('Categories', 'civi-framework'),
				'singular_name' => esc_html__('Jobs Categories', 'civi-framework'),
				'rewrite' => array(
					'slug' => apply_filters('civi_jobs_categories_slug', 'jobs-categories'),
				),
			);
			$taxonomies['jobs-skills'] = array(
				'post_type' => 'jobs',
				'hierarchical' => true,
				'show_in_rest' => true,
				'meta_box_cb' => array($this, 'taxonomy_select_meta_box'),
				'label' => esc_html__('Skills', 'civi-framework'),
				'singular_name' => esc_html__('Jobs Skills', 'civi-framework'),
				'rewrite' => array(
					'slug' => apply_filters('civi_jobs_skills_slug', 'jobs-skills'),
				),
			);
			$taxonomies['jobs-type'] = array(
				'post_type' => 'jobs',
				'hierarchical' => true,
				'show_in_rest' => true,
				'meta_box_cb' => array($this, 'taxonomy_select_meta_box'),
				'label' => esc_html__('Type', 'civi-framework'),
				'singular_name' => esc_html__('Jobs Type', 'civi-framework'),
				'rewrite' => array(
					'slug' => apply_filters('civi_jobs_type_slug', 'jobs-type'),
				),
			);
			$taxonomies['jobs-location'] = array(
				'post_type' => 'jobs',
				'hierarchical' => true,
				'show_in_rest' => true,
				'meta_box_cb' => array($this, 'taxonomy_select_meta_box'),
				'label' => esc_html__('Location', 'civi-framework'),
				'singular_name' => esc_html__('Jobs Location', 'civi-framework'),
				'rewrite' => array(
					'slug' => apply_filters('civi_jobs_location_slug', 'jobs-location'),
				),
			);
			$taxonomies['jobs-career'] = array(
				'post_type' => 'jobs',
				'hierarchical' => true,
				'show_in_rest' => true,
				'meta_box_cb' => array($this, 'taxonomy_select_meta_box'),
				'label' => esc_html__('Career', 'civi-framework'),
				'singular_name' => esc_html__('Jobs Career', 'civi-framework'),
				'rewrite' => array(
					'slug' => apply_filters('civi_jobs_career_slug', 'jobs-career'),
				),
			);
			$taxonomies['jobs-experience'] = array(
				'post_type' => 'jobs',
				'hierarchical' => true,
				'show_in_rest' => true,
				'meta_box_cb' => array($this, 'taxonomy_select_meta_box'),
				'label' => esc_html__('Experience', 'jobs-framework'),
				'singular_name' => esc_html__('Jobs Experience', 'civi-framework'),
				'rewrite' => array(
					'slug' => apply_filters('civi_jobs_experience_slug', 'jobs-experience'),
				),
			);
			$taxonomies['jobs-qualification'] = array(
				'post_type' => 'jobs',
				'hierarchical' => true,
				'show_in_rest' => true,
				'meta_box_cb' => array($this, 'taxonomy_select_meta_box'),
				'label' => esc_html__('Qualification', 'civi-framework'),
				'singular_name' => esc_html__('Jobs Qualification', 'civi-framework'),
				'rewrite' => array(
					'slug' => apply_filters('civi_jobs_qualification_slug', 'jobs-qualification'),
				),
			);

			//Company
			$taxonomies['company-categories'] = array(
				'post_type' => 'company',
				'hierarchical' => true,
				'show_in_rest' => true,
				'label' => esc_html__('Categories', 'civi-framework'),
				'singular_name' => esc_html__('Company Categories', 'civi-framework'),
				'rewrite' => array(
					'slug' => apply_filters('civi_company_categories_slug', 'company-categories'),
				),
			);
			$taxonomies['company-location'] = array(
				'post_type' => 'company',
				'hierarchical' => true,
				'show_in_rest' => true,
				'meta_box_cb' => array($this, 'taxonomy_select_meta_box'),
				'label' => esc_html__('Location', 'civi-framework'),
				'singular_name' => esc_html__('Company Location', 'civi-framework'),
				'rewrite' => array(
					'slug' => apply_filters('civi_company_location_slug', 'company-location'),
				),
			);
			$taxonomies['company-size'] = array(
				'post_type' => 'company',
				'hierarchical' => true,
				'show_in_rest' => true,
				'meta_box_cb' => array($this, 'taxonomy_select_meta_box'),
				'label' => esc_html__('Size', 'civi-framework'),
				'singular_name' => esc_html__('Company Size', 'civi-framework'),
				'rewrite' => array(
					'slug' => apply_filters('civi_company_size_slug', 'company-size'),
				),
			);
			return $taxonomies;
		}

		/**
		 * taxonomy_select_meta_box
		 */
		public function taxonomy_select_meta_box($post, $box)
		{
			$defaults = array('taxonomy' => 'category');

			if (!isset($box['args']) || !is_array($box['args']))
				$args = array();
			else
				$args = $box['args'];

			extract(wp_parse_args($args, $defaults), EXTR_SKIP);
			$tax = get_taxonomy($taxonomy);
			$selected = wp_get_object_terms($post->ID, $taxonomy, array('fields' => 'ids'));
			$hierarchical = $tax->hierarchical;
		?>
			<div id="taxonomy-<?php echo esc_attr($taxonomy); ?>" class="selectdiv civi-jobs-select-meta-box-wrap">
				<?php if (current_user_can($tax->cap->edit_terms)) : ?>
					<?php
					$class = 'widefat';
					if ($taxonomy == 'jobs-state') {
						$class .= ' civi-jobs-state-ajax';
					} elseif ($taxonomy == 'jobs-city') {
						$class .= ' civi-jobs-city-ajax';
					} elseif (($taxonomy == 'jobs-neighborhood')) {
						$class .= ' civi-jobs-neighborhood-ajax';
					}
					if ($hierarchical) {
						wp_dropdown_categories(array(
							'taxonomy' => $taxonomy,
							'class' => $class,
							'hide_empty' => false,
							'name' => "tax_input[$taxonomy][]",
							'selected' => count($selected) >= 1 ? $selected[0] : '',
							'orderby' => 'name',
							'hierarchical' => false,
							'show_option_all' => esc_html__('None', 'civi-framework')
						));
					} else {
					?>
						<select name="<?php echo "tax_input[$taxonomy][]"; ?>" class="<?php echo esc_attr($class); ?>" data-selected="<?php echo civi_get_taxonomy_slug_by_post_id($post->ID, $taxonomy); ?>">
							<option value=""><?php esc_html_e('None', 'civi-framework'); ?></option>
							<?php
							$terms = get_categories(
								array(
									'taxonomy' => $taxonomy,
									'orderby' => 'name',
									'order' => 'ASC',
									'hide_empty' => false,
									'parent' => 0
								)
							);
							foreach ($terms as $term) : ?>
								<option value="<?php echo esc_attr($term->slug); ?>" <?php echo selected($term->term_id, count($selected) >= 1 ? $selected[0] : ''); ?>><?php esc_html_e($term->name); ?></option>
							<?php endforeach; ?>
						</select>
					<?php
					}
					?>
				<?php endif; ?>
			</div>
<?php
		}


		/**
		 * Register meta term
		 * @param $configs
		 * @return mixed
		 */
		public function register_term_meta($configs)
		{
			$configs['jobs-experience-settings'] = apply_filters('civi_register_term_meta_jobs_experience', array(
				'name' => esc_html__('', 'civi-framework'),
				'layout' => 'horizontal',
				'taxonomy' => array('jobs-experience'),
				'fields' => array(
					array(
						'id' => 'jobs_experience_order',
						'title' => esc_html__('Number Order by', 'civi-framework'),
						'type' => 'text',
						'col' => '5',
						'pattern' => '[0-9]*',
						'default' => '',
					),
				)
			));

			$configs['company-size-settings'] = apply_filters('civi_register_term_meta_company_size', array(
				'name' => esc_html__('', 'civi-framework'),
				'layout' => 'horizontal',
				'taxonomy' => array('company-size'),
				'fields' => array(
					array(
						'id' => 'company_size_order',
						'title' => esc_html__('Number Order by', 'civi-framework'),
						'type' => 'text',
						'col' => '5',
						'pattern' => '[0-9]*',
						'default' => '',
					),
				)
			));

			return apply_filters('civi_register_term_meta', $configs);
		}

		/**
		 * Register meta boxes
		 * @param $configs
		 * @return mixed
		 */
		public function register_meta_boxes($configs)
		{
			$meta_prefix = CIVI_METABOX_PREFIX;
			$dec_point = civi_get_option('decimal_separator', '.');
			$format_number = '^[0-9]+([' . $dec_point . '][0-9]+)?$';
			$cf7_field = get_option('field-name');
			$cf7_list = get_posts(array(
				'post_type' => 'wpcf7_contact_form',
				'numberposts' => -1
			));
			$cf7_forms = array('' => 'None');
			$cf7_default = '';
			if (!empty($cf7_list[0]->ID)) {
				$cf7_default = $cf7_list[0]->ID;
			}
			foreach ($cf7_list as $cf7) {
				$cf7_forms[$cf7->ID] = $cf7->post_title . " (" . $cf7->ID . ")";
			}

			//Custom field jobs
			$render_custom_field_jobs = civi_render_custom_field('jobs');
			$custom_field_jobs = array();
			if (count($render_custom_field_jobs) > 0) {
				$custom_field_jobs = array(
					array(
						'id' => "{$meta_prefix}custom_field_jobs_tab",
						'title' => esc_html__('Additional Fields', 'civi-framework'),
						'icon' => 'dashicons dashicons-welcome-add-page',
						'fields' => $render_custom_field_jobs
					),
				);
			}

			//Custom field company
			$render_custom_field_company = civi_render_custom_field('company');


			//Custom field candidate
			$render_custom_field_candidate = civi_render_custom_field('candidate');
			$custom_field_candidate = array();
			if (count($render_custom_field_candidate) > 0) {
				$custom_field_candidate = array(
					array(
						'id' => "{$meta_prefix}custom_field_candidate_tab",
						'title' => esc_html__('Additional Fields', 'civi-framework'),
						'icon' => 'dashicons dashicons-welcome-add-page',
						'fields' => $render_custom_field_candidate
					),
				);
			}

			$configs['jobs_meta_boxes'] = apply_filters('civi_register_meta_boxes_jobs', array(
				'name' => esc_html__('Jobs Information', 'civi-framework'),
				'post_type' => array('jobs'),
				'section' => array_merge(
					apply_filters('civi_register_meta_boxes_jobs_top', array()),
					apply_filters(
						'civi_register_meta_boxes_jobs_main',
						array_merge(
							array(
								array(
									'id' => "{$meta_prefix}details_tab",
									'title' => esc_html__('Basic Infomation', 'civi-framework'),
									'icon' => 'dashicons-admin-home',
									'fields' => array(
										array(
											'type' => 'row',
											'col' => '12',
											'fields' => array(
												array(
													'id' => "{$meta_prefix}enable_jobs_package_expires",
													'type' => 'button_set',
													'title' => esc_html__('Enable package expires', 'civi-framework'),
													'desc' => esc_html__('Turn on when you want package to expire', 'civi-framework'),
													'options' => array(
														'1' => esc_html__('On', 'civi-framework'),
														'0' => esc_html__('Off', 'civi-framework'),
													),
													'col' => '4',
													'default' => '0'
												),

												array(
													'id' => "{$meta_prefix}enable_jobs_expires",
													'type' => 'button_set',
													'title' => esc_html__('Enable jobs expires', 'civi-framework'),
													'desc' => esc_html__('Turn on when you want jobs to expire', 'civi-framework'),
													'options' => array(
														'1' => esc_html__('On', 'civi-framework'),
														'0' => esc_html__('Off', 'civi-framework'),
													),
													'col' => '4',
													'default' => '0'
												),
												array(
													'id' => "{$meta_prefix}jobs_days_closing",
													'title' => esc_html__('Number of days to apply', 'civi-framework'),
													'desc' => esc_html__('Enter the number of days to apply for jobs', 'civi-framework'),
													'default' => '',
													'type' => 'text',
													'col' => '4',
													'required' => array(
														array("{$meta_prefix}enable_jobs_expires", '=', '0'),
													),
												),
												array(
													'id' => "{$meta_prefix}jobs_featured",
													'title' => esc_html__('Mark this jobs as featured ?', 'civi-framework'),
													'type' => 'button_set',
													'col' => '4',
													'options' => array(
														'1' => esc_html__('Yes', 'civi-framework'),
														'0' => esc_html__('No', 'civi-framework'),
													),
													'default' => '0',
												),
												array(
													'id' => "{$meta_prefix}jobs_quantity",
													'title' => esc_html__('Quantity to be recruited ', 'civi-framework'),
													'type' => 'select',
													'desc' => esc_html__('Select quantity', 'civi-framework'),
													'options' => array(
														'' => 'None',
														'1' => '1',
														'2' => '2',
														'3' => '3',
														'4' => '4',
														'5' => '5',
														'6' => '6',
														'7' => '7',
														'8' => '8',
														'9' => '9',
														'10' => '10+',
													),
													'col' => '4',
													'default' => 'quantity1',
												),
												array(
													'id' => "{$meta_prefix}jobs_gender",
													'title' => esc_html__('Gender', 'civi-framework'),
													'desc' => esc_html__('Select Gender', 'civi-framework'),
													'type' => 'select',
													'options' => array(
														'' => 'None',
														'female' => esc_html__('Female', 'civi-framework'),
														'male' => esc_html__('Male', 'civi-framework'),
													),
													'col' => '4',
													'default' => '',
												),
											)
										),
									)
								),
							),
							array(
								array(
									'id' => "{$meta_prefix}details_salary",
									'title' => esc_html__('Salary', 'civi-framework'),
									'icon' => 'dashicons dashicons-money-alt',
									'fields' => array(
										array(
											'type' => 'row',
											'col' => '12',
											'fields' => array(
												array(
													'id' => "{$meta_prefix}jobs_salary_show",
													'title' => esc_html__('Show pay by', 'civi-framework'),
													'type' => 'select',
													'options' => array(
														'range' => 'Range',
														'starting_amount' => 'Starting amount',
														'maximum_amount' => 'Maximum amount',
														'agree' => 'Negotiable Price',
													),
													'col' => '4',
													'default' => 'range',
												),
												array(
													'id' => "{$meta_prefix}jobs_salary_rate",
													'title' => esc_html__('Rate', 'civi-framework'),
													'type' => 'select',
													'options' => array(
														'' => esc_html('None', 'civi-framework'),
														'hours' => esc_html('Per Hours', 'civi-framework'),
														'days' => esc_html('Per Days', 'civi-framework'),
														'week' => esc_html('Per Week', 'civi-framework'),
														'month' => esc_html('Per Month', 'civi-framework'),
														'year' => esc_html('Per Year', 'civi-framework'),
													),
													'col' => '4',
													'default' => 'hours',
													'required' => array(
														array("{$meta_prefix}jobs_salary_show", '!=', 'agree')
													),
												),
												array(
													'id' => "{$meta_prefix}jobs_currency_type",
													'title' => esc_html__('Currency Type', 'civi-framework'),
													'type' => 'select',
													'options' => civi_get_select_currency_type(),
													'col' => '4',
												),
												array(
													'id' => "{$meta_prefix}jobs_salary_minimum",
													'title' => esc_html__('Minimum', 'civi-framework'),
													'desc' => esc_html__('Example Value: 450', 'civi-framework'),
													'type' => 'text',
													'pattern' => "{$format_number}",
													'default' => '450',
													'col' => '4',
													'required' => array(
														array("{$meta_prefix}jobs_salary_show", '=', 'range')
													),
												),
												array(
													'id' => "{$meta_prefix}jobs_salary_maximum",
													'title' => esc_html__('Maximum', 'civi-framework'),
													'desc' => esc_html__('Example Value: 900', 'civi-framework'),
													'type' => 'text',
													'pattern' => "{$format_number}",
													'default' => '900',
													'col' => '4',
													'required' => array(
														array("{$meta_prefix}jobs_salary_show", '=', 'range')
													),
												),
												array(
													'id' => "{$meta_prefix}jobs_maximum_price",
													'title' => esc_html__('Maximum Price', 'civi-framework'),
													'desc' => esc_html__('Example Value: 1000', 'civi-framework'),
													'type' => 'text',
													'pattern' => "{$format_number}",
													'default' => '',
													'col' => '4',
													'required' => array(
														array("{$meta_prefix}jobs_salary_show", '=', 'maximum_amount')
													),
												),
												array(
													'id' => "{$meta_prefix}jobs_minimum_price",
													'title' => esc_html__('Minimum Price', 'civi-framework'),
													'desc' => esc_html__('Example Value: 100', 'civi-framework'),
													'type' => 'text',
													'pattern' => "{$format_number}",
													'default' => '',
													'col' => '4',
													'required' => array(
														array("{$meta_prefix}jobs_salary_show", '=', 'starting_amount')
													),
												),
												array(
													'id' => "{$meta_prefix}jobs_salary_convert_min",
													'title' => esc_html__('Convert Min', 'civi-framework'),
													'type' => 'text',
													'col' => '4',
												),
												array(
													'id' => "{$meta_prefix}jobs_salary_convert_max",
													'title' => esc_html__('Convert Max', 'civi-framework'),
													'type' => 'text',
													'col' => '4',
												),
												array(
													'id' => "{$meta_prefix}jobs_price_convert_min",
													'title' => esc_html__('Convert Min', 'civi-framework'),
													'type' => 'text',
													'col' => '4',
												),
												array(
													'id' => "{$meta_prefix}jobs_price_convert_max",
													'title' => esc_html__('Convert Max', 'civi-framework'),
													'type' => 'text',
													'col' => '4',
												),
											)
										),
									)
								)
							),
							array(
								array(
									'id' => "{$meta_prefix}jobs_apply",
									'title' => esc_html__('Apply', 'civi-framework'),
									'icon' => 'dashicons-email',
									'fields' => array(
										array(
											'type' => 'row',
											'col' => '12',
											'fields' => array(
												array(
													'id' => "{$meta_prefix}jobs_select_apply",
													'title' => esc_html__('Select Type', 'civi-framework'),
													'type' => 'select',
													'col' => 6,
													'options' => apply_filters(
														'civi_fields_select_apply_jobs',
														array(
															'email' => esc_html__('By email', 'civi-framework'),
															'external' => esc_html__('External Apply', 'civi-framework'),
															'internal' => esc_html__('Internal Apply', 'civi-framework'),
															'call-to' => esc_html__('Call To Apply', 'civi-framework'),
														)
													)
												),
												array(
													'id' => "{$meta_prefix}jobs_apply_email",
													'title' => esc_html__('Job apply email', 'civi-framework'),
													'type' => 'text',
													'col' => 6,
													'required' => array(
														array("{$meta_prefix}jobs_select_apply", '=', 'email'),
													),
												),
												array(
													'id' => "{$meta_prefix}jobs_apply_external",
													'title' => esc_html__('Job apply external', 'civi-framework'),
													'type' => 'text',
													'col' => 6,
													'required' => array(
														array("{$meta_prefix}jobs_select_apply", '=', 'external'),
													),
												),
												array(
													'id' => "{$meta_prefix}jobs_apply_call_to",
													'title' => esc_html__('Job Call To Apply', 'civi-framework'),
													'type' => 'text',
													'col' => 6,
													'required' => array(
														array("{$meta_prefix}jobs_select_apply", '=', 'call-to'),
													),
												),
											)
										)
									)
								)
							),
							array(
								array(
									'id' => "{$meta_prefix}jobs_company",
									'title' => esc_html__('Company', 'civi-framework'),
									'icon' => 'dashicons dashicons-building',
									'fields' => array(
										array(
											'id' => "{$meta_prefix}jobs_select_company",
											'title' => esc_html__('Select company', 'civi-framework'),
											'type' => 'select',
											'options' => civi_select_post_company(),
										),
									)
								)
							),
							array(
								array(
									'id' => "{$meta_prefix}location_tab",
									'title' => esc_html__('Location', 'civi-framework'),
									'icon' => 'dashicons-location-alt',
									'fields' => array(
										array(
											'type' => 'row',
											'col' => '12',
											'fields' => array(
												array(
													'id' => "{$meta_prefix}jobs_address",
													'title' => esc_html__('Maps location', 'civi-framework'),
													'desc' => esc_html__('Address Details', 'civi-framework'),
													'type' => 'text',
													'col' => 4
												),
												array(
													'id' => "{$meta_prefix}jobs_latitude",
													'title' => esc_html__('Latitude', 'civi-framework'),
													'desc' => esc_html__('Latitude Details', 'civi-framework'),
													'type' => 'text',
													'col' => 4
												),
												array(
													'id' => "{$meta_prefix}jobs_longtitude",
													'title' => esc_html__('Longtitude', 'civi-framework'),
													'desc' => esc_html__('Longtitude Details', 'civi-framework'),
													'type' => 'text',
													'col' => 4
												),
												array(
													'id' => "{$meta_prefix}jobs_location",
													'title' => esc_html__('Jobs Location at Google Map', 'civi-framework'),
													'desc' => esc_html__('Drag the google map marker to point your jobs location. You can also use the address field above to search for your jobs', 'civi-framework'),
													'type' => 'map',
													'address_field' => "{$meta_prefix}jobs_address",
												),
											)
										)
									)
								),
								array(
									'id' => "{$meta_prefix}gallery_tab",
									'title' => esc_html__('Gallery Images', 'civi-framework'),
									'icon' => 'dashicons-format-gallery',
									'fields' => array(
										array(
											'id' => "{$meta_prefix}jobs_images",
											'title' => esc_html__('Gallery Images', 'civi-framework'),
											'type' => 'gallery',
										),
									)
								),
								array(
									'id' => "{$meta_prefix}video_tab",
									'title' => esc_html__('Video', 'civi-framework'),
									'icon' => 'dashicons-video-alt3',
									'fields' => array(
										array(
											'id' => "{$meta_prefix}jobs_video_url",
											'title' => esc_html__('Video URL', 'civi-framework'),
											'desc' => esc_html__('Input only URL. YouTube, Vimeo, SWF File and MOV File', 'civi-framework'),
											'type' => 'text',
											'col' => 12,
										),
										array(
											'id' => "{$meta_prefix}jobs_video_image",
											'title' => esc_html__('Video Image', 'civi-framework'),
											'type' => 'gallery',
											'col' => 12,
										),
									)
								),
							),
							$custom_field_jobs
						)
					),
					apply_filters('civi_register_meta_boxes_jobs_bottom', array())
				),
			));

			$configs['company_meta_boxes'] = apply_filters('civi_register_meta_boxes_company', array(
				'name' => esc_html__('Company Information', 'civi-framework'),
				'post_type' => array('company'),
				'section' => array_merge(
					apply_filters('civi_register_meta_boxes_company_top', array()),
					apply_filters(
						'civi_register_meta_boxes_company_main',
						array_merge(
							array(
								array(
									'id' => "{$meta_prefix}details_company_tab",
									'title' => esc_html__('Basic Infomation', 'civi-framework'),
									'icon' => 'dashicons-admin-home',
									'fields' => array(
										array(
											'type' => 'row',
											'col' => '12',
											'fields' => array(
												array(
													'id' => "{$meta_prefix}company_green_tick",
													'type' => 'button_set',
													'title' => esc_html__('Enable Green Tick', 'civi-framework'),
													'subtitle' => esc_html__('Enable/Disable Green Tick', 'civi-framework'),
													'options' => array(
														'1' => esc_html__('On', 'civi-framework'),
														'0' => esc_html__('Off', 'civi-framework'),
													),
													'default' => '0',
												),
												array(
													'id' => "{$meta_prefix}company_website",
													'title' => esc_html__(' Website ', 'civi-framework'),
													'type' => 'text',
													'col' => '6',
												),
												array(
													'id' => "{$meta_prefix}company_phone",
													'title' => esc_html__('Phone number', 'civi-framework'),
													'type' => 'text',
													'col' => '6',
												),
												array(
													'id' => "{$meta_prefix}company_email",
													'title' => esc_html__('Email', 'civi-framework'),
													'type' => 'text',
													'col' => '6',
												),
												array(
													'id' => "{$meta_prefix}company_founded",
													'title' => esc_html__('Founded In', 'civi-framework'),
													'type' => 'select',
													'options' => civi_get_company_founded(false),
													'col' => '6',
												),
											)
										),
									)
								),
								array(
									'id' => "{$meta_prefix}details_company_social",
									'title' => esc_html__('Social Network', 'civi-framework'),
									'icon' => 'dashicons dashicons-networking',
									'fields' => array(
										array(
											'type' => 'row',
											'fields' => array(
												array(
													'id' => "{$meta_prefix}company_twitter",
													'title' => esc_html__('Twitter', 'civi-framework'),
													'type' => 'text',
													'col' => '6',
												),
												array(
													'id' => "{$meta_prefix}company_linkedin",
													'title' => esc_html__('Linkedin', 'civi-framework'),
													'type' => 'text',
													'col' => '6',
												),

												array(
													'id' => "{$meta_prefix}company_facebook",
													'title' => esc_html__('Facebook', 'civi-framework'),
													'type' => 'text',
													'col' => '6',
												),
												array(
													'id' => "{$meta_prefix}company_instagram",
													'title' => esc_html__('Instagram', 'civi-framework'),
													'type' => 'text',
													'col' => '6',
												),
											)
										),
										array(
											'type' => 'divide'
										),
										array(
											'id' => "{$meta_prefix}company_social_tabs",
											'type' => 'panel',
											'title' => esc_html__('Social Network', 'civi-framework'),
											'sort' => true,
											'panel_title' => 'label',
											'fields' => array(
												array(
													'type' => 'row',
													'col' => '12',
													'fields' => array(
														array(
															'id' => "{$meta_prefix}company_social_name",
															'type' => 'select',
															'options' => civi_get_repeater_social(''),
															'col' => '6',
															'title' => esc_html__('Name', 'civi-framework'),
														),
														array(
															'id' => "{$meta_prefix}company_social_url",
															'type' => 'text',
															'col' => '6',
															'title' => esc_html__('Url', 'civi-framework'),
														),
													)
												)
											)
										),
									)
								),
								array(
									'id' => "{$meta_prefix}company_logo_tab",
									'title' => esc_html__('Logo', 'civi-framework'),
									'icon' => 'dashicons dashicons-format-image',
									'fields' => array(
										array(
											'type' => 'row',
											'col' => '12',
											'fields' => array(
												array(
													'id' => "{$meta_prefix}company_logo",
													'title' => esc_html__('Logo', 'civi-framework'),
													'type' => 'image',
												),
											)
										),
									)
								),
								array(
									'id' => "{$meta_prefix}location_tab",
									'title' => esc_html__('Location', 'civi-framework'),
									'icon' => 'dashicons-location-alt',
									'fields' => array(
										array(
											'type' => 'row',
											'col' => '12',
											'fields' => array(
												array(
													'id' => "{$meta_prefix}company_address",
													'title' => esc_html__('Maps location', 'civi-framework'),
													'desc' => esc_html__('Full Address', 'civi-framework'),
													'type' => 'text',
													'col' => 4
												),
												array(
													'id' => "{$meta_prefix}company_latitude",
													'title' => esc_html__('Latitude', 'civi-framework'),
													'desc' => esc_html__('Latitude Details', 'civi-framework'),
													'type' => 'text',
													'col' => 4
												),
												array(
													'id' => "{$meta_prefix}company_longtitude",
													'title' => esc_html__('Longtitude', 'civi-framework'),
													'desc' => esc_html__('Longtitude Details', 'civi-framework'),
													'type' => 'text',
													'col' => 4
												),
												array(
													'id' => "{$meta_prefix}company_location",
													'title' => esc_html__('Company Location at Google Map', 'civi-framework'),
													'desc' => esc_html__('Drag the google map marker to point your company location. You can also use the address field above to search for your company', 'civi-framework'),
													'type' => 'map',
													'address_field' => "{$meta_prefix}company_address",
												),
											)
										)
									)
								),
								array(
									'id' => "{$meta_prefix}gallery_company_tab",
									'title' => esc_html__('Gallery Images', 'civi-framework'),
									'icon' => 'dashicons-format-gallery',
									'fields' => array(
										array(
											'id' => "{$meta_prefix}company_images",
											'title' => esc_html__('Civi Gallery Images', 'civi-framework'),
											'type' => 'gallery',
										),
									)
								),
								array(
									'id' => "{$meta_prefix}video_company_tab",
									'title' => esc_html__('Video', 'civi-framework'),
									'icon' => 'dashicons-video-alt3',
									'fields' => array(
										array(
											'id' => "{$meta_prefix}company_video_url",
											'title' => esc_html__('Video URL', 'civi-framework'),
											'desc' => esc_html__('Input only URL. YouTube, Vimeo, SWF File and MOV File', 'civi-framework'),
											'type' => 'text',
											'col' => 12,
										),
										array(
											'id' => "{$meta_prefix}company_video_image",
											'title' => esc_html__('Video Image', 'civi-framework'),
											'type' => 'gallery',
											'col' => 12,
										),
									)
								),
								array(
									'id' => "{$meta_prefix}custom_field_company_tab",
									'title' => esc_html__('Additional Fields', 'civi-framework'),
									'icon' => 'dashicons dashicons-welcome-add-page',
									'fields' => $render_custom_field_company,
								)
							)
						)
					),
					apply_filters('civi_register_meta_boxes_company_bottom', array())
				),
			));

			$configs['candidate_meta_boxes'] = apply_filters('jobi_register_meta_boxes_candidate', array(
				'name' => esc_html__('Candidate Information', 'civi-framework'),
				'post_type' => array('candidate'),
				'section' => array_merge(
					apply_filters('jobi_register_meta_boxes_candidate_top', array()),
					apply_filters(
						'jobi_register_meta_boxes_candidate_main',
						array_merge(
							array( /* Basic Info */
								array( 
									'id' => "{$meta_prefix}details_tab",
									'title' => esc_html__('Basic Infomation', 'civi-framework'),
									'icon' => 'dashicons-admin-home',
									'fields' => array(
										array(
											'type' => 'row',
											'col' => '12',
											'fields' => array(
												array(
													'id' => "{$meta_prefix}candidate_first_name",
													'title' => esc_html__('First Name', 'civi-framework'),
													'type' => 'text',
													'default' => '',
													'col' => '6',
												),
												array(
													'id' => "{$meta_prefix}candidate_last_name",
													'title' => esc_html__('Last Name', 'civi-framework'),
													'type' => 'text',
													'default' => '',
													'col' => '6',
												),
												array(
													'id' => "{$meta_prefix}candidate_email",
													'title' => esc_html__('Email Address', 'civi-framework'),
													'type' => 'text',
													'default' => '',
													'col' => '6',
												),
												array(
													'id' => "{$meta_prefix}candidate_phone",
													'title' => esc_html__('Phone Number', 'civi-framework'),
													'type' => 'text',
													'default' => '',
													'col' => '6',
												),
												array(
													'id' => "{$meta_prefix}candidate_current_position",
													'title' => esc_html__('Current Position', 'civi-framework'),
													'type' => 'text',
													'default' => '',
													'col' => '6',
												),
												array(
													'id' => "{$meta_prefix}candidate_dob",
													'title' => esc_html__('Date Of Birth', 'civi-framework'),
													'type' => 'text',
													'default' => '',
													'pattern' => '(?:19|20)[0-9]{2}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1[0-9]|2[0-9])|(?:(?!02)(?:0[1-9]|1[0-2])-(?:30))|(?:(?:0[13578]|1[02])-31))',
													'placeholder' => 'yyyy-mm-dd',
													'maxlength' => '10',
													'col' => '6',
												),
												array(
													'id' => "{$meta_prefix}candidate_gender",
													'title' => esc_html__('Gender', 'civi-framework'),
													'type' => 'select',
													'options' => array(
														'' => esc_html__('None', 'civi-framework'),
														'female' => esc_html__('Female', 'civi-framework'),
														'male' => esc_html__('Male', 'civi-framework'),
													),
													'default' => '',
													'col' => '6',
												),

											)
										),

										
										array(
											'type' => 'divide'
										),

										array(
											'type' => 'row',
											'fields' => array(
												array(
													'id' => "{$meta_prefix}candidate_offer_salary",
													'title' => esc_html__('Offered Salary', 'civi-framework'),
													'type' => 'text',
													'pattern' => '^[0-9]+([.][0-9]+)?$',
													'default' => '',
													'col' => '4',
												),
												array(
													'id' => "{$meta_prefix}candidate_salary_type",
													'title' => esc_html__('Salary Type', 'civi-framework'),
													'type' => 'select',
													'options' => array(
														'' => esc_html__('None', 'civi-framework'),
														'hr' => esc_html__('Hourly', 'civi-framework'),
														'day' => esc_html__('Daily', 'civi-framework'),
														'month' => esc_html__('Monthly', 'civi-framework'),
														'year' => esc_html__('Yearly', 'civi-framework'),
													),
													'col' => '4',
													'default' => 'hr',
												),
												array(
													'id' => "{$meta_prefix}candidate_currency_type",
													'title' => esc_html__('Currency Type', 'civi-framework'),
													'type' => 'select',
													'options' => civi_get_select_currency_type(),
													'col' => '4',
												),
												array(
													'id' => "{$meta_prefix}candidate_featured",
													'title' => esc_html__('Mark this candidate as featured ?', 'civi-framework'),
													'type' => 'button_set',
													'options' => array(
														'1' => esc_html__('Yes', 'civi-framework'),
														'0' => esc_html__('No', 'civi-framework'),
													),
													'default' => '0',
												),
											)
										)
									)
								)
							),

							array( /* Social Network */
								array(
									'id' => "{$meta_prefix}details_candidate_social",
									'title' => esc_html__('Social Network', 'civi-framework'),
									'icon' => 'dashicons dashicons-networking',
									'fields' => array(
										array(
											'type' => 'row',
											'fields' => array(
												array(
													'id' => "{$meta_prefix}candidate_twitter",
													'title' => esc_html__('Twitter', 'civi-framework'),
													'type' => 'text',
													'col' => '6',
												),
												array(
													'id' => "{$meta_prefix}candidate_linkedin",
													'title' => esc_html__('Linkedin', 'civi-framework'),
													'type' => 'text',
													'col' => '6',
												),

												array(
													'id' => "{$meta_prefix}candidate_facebook",
													'title' => esc_html__('Facebook', 'civi-framework'),
													'type' => 'text',
													'col' => '6',
												),
												array(
													'id' => "{$meta_prefix}candidate_instagram",
													'title' => esc_html__('Instagram', 'civi-framework'),
													'type' => 'text',
													'col' => '6',
												),
											)
										),
										array(
											'type' => 'divide'
										),
										array(
											'id' => "{$meta_prefix}candidate_social_tabs",
											'type' => 'panel',
											'title' => esc_html__('Social Network', 'civi-framework'),
											'sort' => true,
											'panel_title' => 'label',
											'fields' => array(
												array(
													'type' => 'row',
													'col' => '12',
													'fields' => array(
														array(
															'id' => "{$meta_prefix}candidate_social_name",
															'type' => 'select',
															'options' => civi_get_repeater_social(''),
															'col' => '6',
															'title' => esc_html__('Name', 'civi-framework'),
														),
														array(
															'id' => "{$meta_prefix}candidate_social_url",
															'type' => 'text',
															'col' => '6',
															'title' => esc_html__('Url', 'civi-framework'),
														),
													)
												)
											)
										),
									)
								)
							),

							array( 
								array( /* Resume */	
									'id' => "{$meta_prefix}candidate_resume_tab",
									'title' => esc_html__('My Resume', 'civi-framework'),
									'icon' => 'dashicons-format-gallery',
									'fields' => array(
										array(
											'id' => "{$meta_prefix}candidate_resume_id_list",
											'title' => esc_html__('Candidate Resume', 'civi-framework'),
											'type' => 'file',
										),
									)
								),
								array( /* Location */
									'id' => "{$meta_prefix}candidate_location_tab",
									'title' => esc_html__('Location', 'civi-framework'),
									'icon' => 'dashicons-location-alt',
									'fields' => array(
										array(
											'type' => 'row',
											'col' => '12',
											'fields' => array(
												array(
													'id' => "{$meta_prefix}candidate_address",
													'title' => esc_html__('Maps location', 'civi-framework'),
													'desc' => esc_html__('Address Details', 'civi-framework'),
													'type' => 'text',
													'col' => 4
												),
												array(
													'id' => "{$meta_prefix}candidate_latitude",
													'title' => esc_html__('Latitude', 'civi-framework'),
													'desc' => esc_html__('Latitude Details', 'civi-framework'),
													'type' => 'text',
													'col' => 4
												),
												array(
													'id' => "{$meta_prefix}candidate_longtitude",
													'title' => esc_html__('Longtitude', 'civi-framework'),
													'desc' => esc_html__('Longtitude Details', 'civi-framework'),
													'type' => 'text',
													'col' => 4
												),
												array(
													'id' => "{$meta_prefix}candidate_location",
													'title' => esc_html__('Location at Google Map', 'civi-framework'),
													'desc' => esc_html__('Drag the google map marker to point your candidate location. You can also use the address field above to search for your candidate', 'civi-framework'),
													'type' => 'map',
													'address_field' => "{$meta_prefix}candidate_address",
												),
											)
										)

									)
								),
								array( /* Education */
									'id' => "{$meta_prefix}candidate_education_tabs",
									'title' => esc_html__('Education', 'civi-framework'),
									'icon' => 'dashicons-editor-ul',
									'fields' => array(
										array(
											'id' => "{$meta_prefix}candidate_education_list",
											'type' => 'panel',
											'title' => esc_html__('Education', 'civi-framework'),
											'sort' => true,
											'panel_title' => 'label',
											'fields' => array(
												array(
													'type' => 'row',
													'col' => '12',
													'fields' => array(
														array(
															'id' => "{$meta_prefix}candidate_education_title",
															'title' => esc_html__('Title', 'civi-framework'),
															'type' => 'text',
															'default' => '',
															'col' => '6'
														),
														array(
															'id' => "{$meta_prefix}candidate_education_level",
															'title' => esc_html__('Level of Education', 'civi-framework'),
															'type' => 'text',
															'default' => '',
															'col' => '6'
														),
														array(
															'id' => "{$meta_prefix}candidate_education_from",
															'title' => esc_html__('From', 'civi-framework'),
															'type' => 'text',
															'default' => '',
															'col' => '6'
														),
														array(
															'id' => "{$meta_prefix}candidate_education_to",
															'title' => esc_html__('To', 'civi-framework'),
															'type' => 'text',
															'default' => '',
															'col' => '6'
														),
														array(
															'id' => "{$meta_prefix}candidate_education_description",
															'title' => esc_html__('Description', 'civi-framework'),
															'type' => 'textarea',
															'default' => '',
															'col' => '12'
														),
													)
												)
											)
										)
									)
								),
								array( /* Experiencies */
									'id' => "{$meta_prefix}candidate_experience_tab",
									'title' => esc_html__('Experiencies', 'civi-framework'),
									'icon' => 'dashicons-location-alt',
									'fields' => array(
										array(
											'id' => "{$meta_prefix}candidate_experience_list",
											'type' => 'panel',
											'title' => esc_html__('Work Experience', 'civi-framework'),
											'sort' => true,
											'panel_title' => 'label',
											'fields' => array(
												array(
													'type' => 'row',
													'col' => '12',
													'fields' => array(
														array(
															'id' => "{$meta_prefix}candidate_experience_job",
															'title' => esc_html__('Job Title', 'civi-framework'),
															'type' => 'text',
															'default' => '',
															'col' => '6'
														),
														array(
															'id' => "{$meta_prefix}candidate_experience_company",
															'title' => esc_html__('Company', 'civi-framework'),
															'type' => 'text',
															'default' => '',
															'col' => '6'
														),
														array(
															'id' => "{$meta_prefix}candidate_experience_from",
															'title' => esc_html__('From', 'civi-framework'),
															'type' => 'text',
															'default' => '',
															'col' => '6'
														),
														array(
															'id' => "{$meta_prefix}candidate_experience_to",
															'title' => esc_html__('To', 'civi-framework'),
															'type' => 'text',
															'default' => '',
															'col' => '6'
														),
														array(
															'id' => "{$meta_prefix}candidate_experience_description",
															'title' => esc_html__('Description', 'civi-framework'),
															'type' => 'textarea',
															'default' => '',
															'col' => '12'
														),
													)
												)
											)
										)
									)
								),
								array( /* Projects */
									'id' => "{$meta_prefix}candidate_project_tab",
									'title' => esc_html__('Projects', 'civi-framework'),
									'icon' => 'dashicons-format-gallery',
									'fields' => array(
										array(
											'id' => "{$meta_prefix}candidate_project_list",
											'type' => 'panel',
											'title' => esc_html__('Projects', 'civi-framework'),
											'sort' => true,
											'panel_title' => 'label',
											'fields' => array(
												array(
													'type' => 'row',
													'col' => '12',
													'fields' => array(
														array(
															'id' => "{$meta_prefix}candidate_project_image_id",
															'title' => esc_html__('A screenshot of Project', 'civi-framework'),
															'type' => 'image',
															'default' => '',
															'col' => '12'
														),
														array(
															'id' => "{$meta_prefix}candidate_project_title",
															'title' => esc_html__('Project Title', 'civi-framework'),
															'type' => 'text',
															'default' => '',
															'col' => '6'
														),
														array(
															'id' => "{$meta_prefix}candidate_project_link",
															'title' => esc_html__('Link', 'civi-framework'),
															'type' => 'text',
															'default' => '',
															'col' => '6'
														),
														array(
															'id' => "{$meta_prefix}candidate_project_description",
															'title' => esc_html__('Description', 'civi-framework'),
															'type' => 'textarea',
															'default' => '',
															'col' => '12'
														),
													)
												)
											)
										)
									)
								),
								array( /* Awards */
									'id' => "{$meta_prefix}candidate_award_tab",
									'title' => esc_html__('Awards', 'civi-framework'),
									'icon' => 'dashicons-video-alt3',
									'fields' => array(
										array(
											'id' => "{$meta_prefix}candidate_award_list",
											'type' => 'panel',
											'title' => esc_html__('Awards', 'civi-framework'),
											'sort' => true,
											'panel_title' => 'label',
											'fields' => array(
												array(
													'type' => 'row',
													'col' => '12',
													'fields' => array(
														array(
															'id' => "{$meta_prefix}candidate_award_title",
															'title' => esc_html__('Eye Color', 'civi-framework'),
															'type' => 'text',
															'default' => '',
															'col' => '6'
														),
														array(
															'id' => "{$meta_prefix}candidate_award_date",
															'title' => esc_html__('Date', 'civi-framework'),
															'type' => 'text',
															'default' => '',
															'col' => '6'
														),
														array(
															'id' => "{$meta_prefix}candidate_award_description",
															'title' => esc_html__('Description', 'civi-framework'),
															'type' => 'textarea',
															'default' => '',
															'col' => '12'
														),
													)
												)
											)
										)
									)
								),								
								/* ***** */
								array( /* Photo Gallery */
									'id' => "{$meta_prefix}candidate_gallery_tab",
									'title' => esc_html__('Photo Gallery', 'civi-framework'),
									'icon' => 'dashicons-format-gallery',
									'fields' => array(
										array(
											'id' => "{$meta_prefix}candidate_galleries",
											'title' => esc_html__('Gallery', 'civi-framework'),
											'type' => 'gallery',
											'default' => '',
											'col' => '12'
										),
									)
								),								
								array( /* Video */
									'id' => "{$meta_prefix}candidate_video_tab",
									'title' => esc_html__('Videos', 'civi-framework'),
									'icon' => 'dashicons-video-alt3',
									'fields' => array(
										array(
											'id' => "{$meta_prefix}candidate_video_list",
											'type' => 'panel',
											'title' => esc_html__('Videos', 'civi-framework'),
											'sort' => true,
											'panel_title' => 'label',
											'fields' => array(
												array(
													'type' => 'row',
													'col' => '12',
													'fields' => array(
														array(
															'id' => "{$meta_prefix}candidate_video_title",
															'title' => esc_html__('Video Title', 'civi-framework'),
															'type' => 'text',
															'default' => '',
															'col' => '12'
														),
														array(
															'id' => "{$meta_prefix}candidate_video_url",
															'title' => esc_html__('Video URL', 'civi-framework'),
															'type' => 'text',
															'default' => '',
															'col' => '12'
														),
													)
												)
											)
										)
									)
								),							
								array(	/* Audio */
									'id' => "{$meta_prefix}candidate_audio_tab",
									'title' => esc_html__('Audio', 'civi-framework'),
									'icon' => 'dashicons-format-audio',
									'fields' => array(
										array(
											'id' => "{$meta_prefix}candidate_audio_list",
											'type' => 'panel',
											'title' => esc_html__('Audio', 'civi-framework'),
											'sort' => true,
											'panel_title' => 'label',
											'fields' => array(
												array(
													'type' => 'row',
													'col' => '12',
													'fields' => array(
														array(
															'id' => "{$meta_prefix}candidate_audio_title",
															'title' => esc_html__('Audio Title', 'civi-framework'),
															'type' => 'text',
															'default' => '',
															'col' => '12'
														),
														array(
															'id' => "{$meta_prefix}candidate_audio_url",
															'title' => esc_html__('Audio URL', 'civi-framework'),
															'type' => 'text',
															'default' => '',
															'col' => '12'
														),
													)
												)
											)
										)
									)
								),
								/* ***** */
							),
							$custom_field_candidate
						)
					),
					apply_filters('jobi_register_meta_boxes_candidate_bottom', array())
				),
			));

			$configs['package_meta_boxes'] = array(
				'name' => esc_html__('Package Settings', 'civi-framework'),
				'post_type' => array('package'),
				'fields' => array(
					array(
						'type' => 'row',
						'col' => '4',
						'fields' => array(
							array(
								'id' => "{$meta_prefix}package_free",
								'title' => esc_html__('Free package', 'civi-framework'),
								'type' => 'button_set',
								'options' => array(
									'1' => esc_html__('Yes', 'civi-framework'),
									'0' => esc_html__('No', 'civi-framework'),
								),
								'default' => '0',
							),
							array(
								'id' => "{$meta_prefix}package_price",
								'title' => esc_html__('Package Price', 'civi-framework'),
								'type' => 'text',
								'required' => array("{$meta_prefix}package_free", '=', '0'),
							),
						)
					),
					array(
						'type' => 'divide'
					),
					array(
						'type' => 'row',
						'col' => '4',
						'fields' => array(
							array(
								'id' => "{$meta_prefix}package_unlimited_job",
								'title' => esc_html__('Unlimited Job', 'civi-framework'),
								'type' => 'button_set',
								'options' => array(
									'1' => esc_html__('Yes', 'civi-framework'),
									'0' => esc_html__('No', 'civi-framework'),
								),
								'default' => '0',
							),
							array(
								'id' => "{$meta_prefix}package_number_job",
								'title' => esc_html__('Number Listings', 'civi-framework'),
								'type' => 'text',
								'default' => '',
								'pattern' => '[0-9]*',
								'required' => array("{$meta_prefix}package_unlimited_job", '=', '0'),
							),
						)
					),
					array(
						'type' => 'divide'
					),
					array(
						'type' => 'row',
						'col' => '4',
						'fields' => array(
							array(
								'id' => "{$meta_prefix}package_unlimited_job_featured",
								'title' => esc_html__('Unlimited Job Featured', 'civi-framework'),
								'type' => 'button_set',
								'options' => array(
									'1' => esc_html__('Yes', 'civi-framework'),
									'0' => esc_html__('No', 'civi-framework'),
								),
								'default' => '0',
							),
							array(
								'id' => "{$meta_prefix}package_number_featured",
								'title' => esc_html__('Number Featured Listings', 'civi-framework'),
								'type' => 'text',
								'default' => '',
								'pattern' => '[0-9]*',
								'required' => array("{$meta_prefix}package_unlimited_job_featured", '=', '0'),
							),
						)
					),
					array(
						'type' => 'divide'
					),
					array(
						'type' => 'row',
						'col' => '4',
						'fields' => array(
							array(
								'id' => "{$meta_prefix}package_unlimited_time",
								'title' => esc_html__('Unlimited time', 'civi-framework'),
								'type' => 'button_set',
								'options' => array(
									'1' => esc_html__('Yes', 'civi-framework'),
									'0' => esc_html__('No', 'civi-framework'),
								),
								'default' => '0',
							),
							array(
								'id' => "{$meta_prefix}package_time_unit",
								'title' => esc_html__('Time Unit', 'civi-framework'),
								'type' => 'button_set',
								'options' => array(
									'Day' => esc_html__('Day', 'civi-framework'),
									'Week' => esc_html__('Week', 'civi-framework'),
									'Month' => esc_html__('Month', 'civi-framework'),
									'Year' => esc_html__('Year', 'civi-framework'),
								),
								'default' => 'Day',
								'required' => array("{$meta_prefix}package_unlimited_time", '=', '0'),
							),
							array(
								'id' => "{$meta_prefix}package_period",
								'title' => esc_html__('Number Jobs', 'civi-framework'),
								'type' => 'text',
								'default' => '1',
								'pattern' => '[0-9]*',
								'required' => array("{$meta_prefix}package_unlimited_time", '=', '0'),
							),
						)
					),
					array(
						'type' => 'divide'
					),
					array(
						'id' => "{$meta_prefix}package_additional_details",
						'type' => 'repeater',
						'title' => esc_html__('Additional details:', 'civi-framework'),
						'col' => '6',
						'sort' => true,
						'fields' => array(
							array(
								'id' => "{$meta_prefix}package_details_text",
								'type' => 'text',
								'default' => esc_html__('Limited support', 'civi-framework'),
							),
						)
					),

					array(
						'type' => 'divide'
					),
					array(
						'type' => 'row',
						'col' => '4',
						'fields' => array(
							array(
								'id' => "{$meta_prefix}package_order_display",
								'title' => esc_html__('Order Number Display Via Frontend', 'civi-framework'),
								'type' => 'text',
								'default' => '1',
								'pattern' => '[0-9]*',
							),
							array(
								'id' => "{$meta_prefix}package_featured",
								'title' => esc_html__('Is Featured?', 'civi-framework'),
								'type' => 'button_set',
								'options' => array(
									'1' => esc_html__('Yes', 'civi-framework'),
									'0' => esc_html__('No', 'civi-framework'),
								),
								'default' => '0',
							),
							array(
								'id' => "{$meta_prefix}package_visible",
								'title' => esc_html__('Is Visible?', 'civi-framework'),
								'type' => 'button_set',
								'options' => array(
									'1' => esc_html__('Yes', 'civi-framework'),
									'0' => esc_html__('No', 'civi-framework'),
								),
								'default' => '1',
							),
						)
					),
				),
			);

			$date_applicants = get_the_date('Y-m-d');
			$configs['applicants_meta_boxes'] = array(
				'name' => esc_html__('Applicants Settings', 'civi-framework'),
				'post_type' => array('applicants'),
				'fields' => array(
					array(
						'type' => 'row',
						'col' => '6',
						'fields' => array(
							array(
								'id' => "{$meta_prefix}applicants_status",
								'title' => esc_html__('Status', 'civi-framework'),
								'type' => 'button_set',
								'options' => array(
									'pending' => esc_html__('Pending', 'civi-framework'),
									'approved' => esc_html__('Approved', 'civi-framework'),
									'rejected' => esc_html__('Rejected', 'civi-framework'),
								),
								'default' => 'pending',
							),
							array(
								'id' => "{$meta_prefix}applicants_type",
								'title' => esc_html__('Type Apply', 'civi-framework'),
								'type' => 'button_set',
								'options' => array(
									'email' => esc_html__('Email', 'civi-framework'),
									'internal' => esc_html__('Internal', 'civi-framework'),
								),
								'default' => 'email',
							),
						)
					),
					array(
						'type' => 'row',
						'col' => '6',
						'fields' => array(
							array(
								'id' => "{$meta_prefix}applicants_author",
								'title' => esc_html__('Name Apply', 'civi-framework'),
								'type' => 'text',
								'default' => '',
								'required' => array("{$meta_prefix}applicants_type", '=', 'email'),
							),
							array(
								'id' => "{$meta_prefix}applicants_phone",
								'title' => esc_html__('Phone', 'civi-framework'),
								'type' => 'text',
								'default' => '',
								'required' => array("{$meta_prefix}applicants_type", '=', 'email'),
							),
							array(
								'id' => "{$meta_prefix}applicants_email",
								'title' => esc_html__('Email Address', 'civi-framework'),
								'type' => 'text',
								'default' => '',
								'required' => array("{$meta_prefix}applicants_type", '=', 'email'),
							),
							array(
								'id' => "{$meta_prefix}applicants_cv",
								'title' => esc_html__('Cv Url', 'civi-framework'),
								'type' => 'text',
								'default' => '',
							),
						)
					),
					array(
						'id' => "{$meta_prefix}applicants_message",
						'title' => esc_html__('Message', 'civi-framework'),
						'type' => 'textarea',
						'default' => '',
					),
					array(
						'type' => 'row',
						'col' => '12',
						'fields' => array(
							array(
								'id' => "{$meta_prefix}applicants_jobs_id",
								'title' => esc_html__('Jobs ID', 'civi-framework'),
								'type' => 'text',
								'col' => '6',
								'default' => '',
							),
							array(
								'id' => "{$meta_prefix}applicants_date",
								'title' => esc_html__('Post Date', 'civi-framework'),
								'type' => 'text',
								'col' => '6',
								'default' => $date_applicants,
							),
						)
					),
				),
			);

			$configs['meetings_meta_boxes'] = array(
				'name' => esc_html__('Meetings Settings', 'civi-framework'),
				'post_type' => array('meetings'),
				'fields' => array(
					array(
						'id' => "{$meta_prefix}meeting_status",
						'title' => esc_html__('Status', 'civi-framework'),
						'type' => 'button_set',
						'options' => array(
							'upcoming' => esc_html__('Upcoming', 'civi-framework'),
							'completed' => esc_html__('Completed', 'civi-framework'),
						),
						'default' => 'upcoming',
					),
					array(
						'type' => 'row',
						'col' => '6',
						'fields' => array(
							array(
								'id' => "{$meta_prefix}meeting_with",
								'title' => esc_html__('Meeting With', 'civi-framework'),
								'type' => 'text',
								'default' => '',
							),
							array(
								'id' => "{$meta_prefix}meeting_date",
								'title' => esc_html__('Date', 'civi-framework'),
								'type' => 'text',
								'default' => '',
							),
							array(
								'id' => "{$meta_prefix}meeting_time",
								'title' => esc_html__('Time', 'civi-framework'),
								'type' => 'text',
								'default' => '',
							),
							array(
								'id' => "{$meta_prefix}meeting_time_duration",
								'title' => esc_html__('Time Duration', 'civi-framework'),
								'type' => 'text',
								'default' => '',
							),
						)
					),
					array(
						'id' => "{$meta_prefix}meeting_message",
						'title' => esc_html__('Message', 'civi-framework'),
						'type' => 'textarea',
						'default' => '',
					),
				),
			);

			if (post_type_exists('job_alerts')) {
				$configs['job_alerts_meta_boxes'] = array(
					'name' => esc_html__('Job Alerts Infomation', 'civi-framework'),
					'post_type' => array('job_alerts'),
					'fields' => array(
						array(
							'type' => 'row',
							'fields' => array(
								array(
									'id' => "{$meta_prefix}job_alerts_email",
									'title' => esc_html__('Email', 'civi-framework'),
									'type' => 'text',
									'default' => '',
									'col' => '12',
								),
								array(
									'id' => "{$meta_prefix}job_alerts_location",
									'title' => esc_html__('Location', 'civi-framework'),
									'type' => 'select',
									'options' => civi_get_taxonomy('jobs-location', false, true, true),
									'default' => '',
									'col' => '3',
								),
								array(
									'id' => "{$meta_prefix}job_alerts_categories",
									'title' => esc_html__('Categories', 'civi-framework'),
									'type' => 'select',
									'options' => civi_get_taxonomy('jobs-categories', false, true, true),
									'default' => '',
									'col' => '3',
								),
								array(
									'id' => "{$meta_prefix}job_alerts_experience",
									'title' => esc_html('Experience', 'civi-framework'),
									'type' => 'select',
									'options' => civi_get_taxonomy('jobs-experience', false, true, true),
									'default' => '',
									'col' => '3',
								),
								array(
									'id' => "{$meta_prefix}job_alerts_frequency",
									'title' => esc_html__('Frequency', 'civi-framework'),
									'type' => 'select',
									'options' => array(
										''	=> esc_html('Select an option', 'civi-framework'),
										'daily'	=> esc_html('Daily', 'civi-framework'),
										'weekly'	=> esc_html('Weekly', 'civi-framework'),
										'monthly'	=> esc_html('Monthly', 'civi-framework'),
									),
									'default' => '',
									'col' => '3',
								),
								array(
									'id' => "{$meta_prefix}job_alerts_skill",
									'type' => 'checkbox_list',
									'title' => esc_html__('Skills', 'civi-framework'),
									'options' => civi_get_taxonomy('jobs-skills', false, true, true),
									'value_inline' => true,
									'default' => array(),
									'col' => '12',
								),
								array(
									'id' => "{$meta_prefix}job_alerts_type",
									'type' => 'checkbox_list',
									'title' => esc_html__('Type', 'civi-framework'),
									'options' => civi_get_taxonomy('jobs-type', false, true, true),
									'value_inline' => true,
									'default' => array(),
									'col' => '12',
								),
							)
						),
					),
				);
			}

			// Page
			$configs['civi_page_options'] = array(
				'name' => esc_html__('Page Options', 'civi'),
				'post_type' => array('page'),
				'section' => array_merge(
					apply_filters('civi_register_meta_boxes_page_top', array()),
					apply_filters(
						'civi_register_meta_boxes_page_main',
						array_merge(
							array(
								array(
									'id' => "{$meta_prefix}page_Layout",
									'title' => esc_html__('Page Layout', 'civi-framework'),
									'icon' => 'dashicons-admin-home',
									'fields' => array(
										array(
											'type' => 'row',
											'col' => '12',
											'fields' => array(
												array(
													'id' => "{$meta_prefix}page_body_bg",
													'title' => esc_html__('Body Background', 'civi-framework'),
													'type' => 'color',
													'col' => '12',
													'default' => '',
												),
												array(
													'id' => "{$meta_prefix}page_pt_deskop",
													'title' => esc_html__('Padding Top (Deskop)', 'civi-framework'),
													'type' => 'text',
													'default' => '',
													'col' => '6',
													'pattern' => '[0-9]*',
												),
												array(
													'id' => "{$meta_prefix}page_pb_deskop",
													'title' => esc_html__('Padding Bottom (Deskop)', 'civi-framework'),
													'type' => 'text',
													'default' => '',
													'col' => '6',
													'pattern' => '[0-9]*',
												),
												array(
													'id' => "{$meta_prefix}page_pt_mobie",
													'title' => esc_html__('Padding Top (Mobie)', 'civi-framework'),
													'type' => 'text',
													'default' => '',
													'col' => '6',
													'pattern' => '[0-9]*',
												),
												array(
													'id' => "{$meta_prefix}page_pb_mobie",
													'title' => esc_html__('Padding Bottom (Mobie)', 'civi-framework'),
													'type' => 'text',
													'default' => '',
													'col' => '6',
													'pattern' => '[0-9]*',
												),
											)
										),

									)
								)
							),
							array(
								array(
									'id' => "{$meta_prefix}page_header",
									'title' => esc_html__('Page Header', 'civi-framework'),
									'icon' => 'dashicons-before dashicons-smiley',
									'fields' => array(
										array(
											'type' => 'row',
											'col' => '12',
											'fields' => array(
												array(
													'id' => "{$meta_prefix}header_style",
													'title' => esc_html__('Header Style', 'civi-framework'),
													'type' => 'button_set',
													'options' => array(
														'dark' => esc_html__('Dark', 'civi-framework'),
														'light' => esc_html__('Light', 'civi-framework'),
													),
													'col' => '4',
													'default' => 'dark',
												),
												array(
													'id' => "{$meta_prefix}header_show",
													'title' => esc_html__('Show Header', 'civi-framework'),
													'type' => 'button_set',
													'options' => array(
														'1' => esc_html__('Yes', 'civi-framework'),
														'0' => esc_html__('No', 'civi-framework'),
													),
													'col' => '4',
													'default' => '1',
												),
												array(
													'id' => "{$meta_prefix}show_top_bar",
													'title' => esc_html__('Show Top Bar', 'civi-framework'),
													'type' => 'button_set',
													'options' => array(
														'1' => esc_html__('Yes', 'civi-framework'),
														'0' => esc_html__('No', 'civi-framework'),
													),
													'col' => '4',
													'default' => '0',
													'required' => array("{$meta_prefix}header_show", '=', '1'),
												),
												array(
													'id' => "{$meta_prefix}show_header_float",
													'title' => esc_html__('Header Float', 'civi-framework'),
													'type' => 'select',
													'options' => array(
														'' => esc_html__('Default', 'civi-framework'),
														'1' => esc_html__('Yes', 'civi-framework'),
														'0' => esc_html__('No', 'civi-framework'),
													),
													'col' => '4',
													'default' => '',
													'required' => array("{$meta_prefix}header_show", '=', '1'),
												),
												array(
													'id' => "{$meta_prefix}show_header_sticky",
													'title' => esc_html__('Header Sticky', 'civi-framework'),
													'type' => 'select',
													'options' => array(
														'' => esc_html__('Default', 'civi-framework'),
														'1' => esc_html__('Yes', 'civi-framework'),
														'0' => esc_html__('No', 'civi-framework'),
													),
													'col' => '4',
													'default' => '',
													'required' => array("{$meta_prefix}header_show", '=', '1'),
												),
												array(
													'id' => "{$meta_prefix}show_header_rtl",
													'title' => esc_html__('Header Rtl', 'civi-framework'),
													'type' => 'select',
													'options' => array(
														'' => esc_html__('Default', 'civi-framework'),
														'1' => esc_html__('Yes', 'civi-framework'),
														'0' => esc_html__('No', 'civi-framework'),
													),
													'col' => '4',
													'default' => '',
													'required' => array("{$meta_prefix}header_show", '=', '1'),
												),
											)
										),

									)
								)
							),
							array(
								array(
									'id' => "{$meta_prefix}page_footer",
									'title' => esc_html__('Page Footer', 'civi-framework'),
									'icon' => 'dashicons-excerpt-view',
									'fields' => array(
										array(
											'type' => 'row',
											'col' => '12',
											'fields' => array(
												array(
													'id' => "{$meta_prefix}footer_show",
													'title' => esc_html__('Show Footer', 'civi-framework'),
													'type' => 'button_set',
													'options' => array(
														'1' => esc_html__('Yes', 'civi-framework'),
														'0' => esc_html__('No', 'civi-framework'),
													),
													'col' => '4',
													'default' => '1',
												),
												array(
													'id' => "{$meta_prefix}footer_type",
													'title' => esc_html__('Footer Type', 'civi-framework'),
													'type' => 'select',
													'default' => '',
													'options' => civi_get_footer_elementor(),
													'col' => '4',
													'required' => array("{$meta_prefix}footer_show", '=', '1'),
												)
											)
										),

									)
								)
							),
							array(
								array(
									'id' => "{$meta_prefix}page_title",
									'title' => esc_html__('Page Title', 'civi-framework'),
									'icon' => 'dashicons-analytics',
									'fields' => array(
										array(
											'type' => 'row',
											'col' => '12',
											'fields' => array(
												array(
													'id' => "{$meta_prefix}page_title_show",
													'title' => esc_html__('Show Page Title', 'civi-framework'),
													'type' => 'button_set',
													'options' => array(
														'1' => esc_html__('Yes', 'civi-framework'),
														'0' => esc_html__('No', 'civi-framework'),
													),
													'col' => '4',
													'default' => '0',
												),
												array(
													'id' => "{$meta_prefix}page_title_color",
													'title' => esc_html__('Text Color', 'civi-framework'),
													'type' => 'color',
													'col' => '4',
													'default' => '',
													'required' => array("{$meta_prefix}page_title_show", '=', '1'),
												),
												array(
													'id' => "{$meta_prefix}page_title_bg",
													'title' => esc_html__('Background Color', 'civi-framework'),
													'type' => 'color',
													'col' => '4',
													'default' => '',
													'required' => array("{$meta_prefix}page_title_show", '=', '1'),
												),
												array(
													'id' => "{$meta_prefix}page_title_image",
													'title' => esc_html__('Background Image', 'civi-framework'),
													'type' => 'image',
													'default' => '',
													'required' => array("{$meta_prefix}page_title_show", '=', '1'),
												)
											)
										),

									)
								)
							)
						)
					),
					apply_filters('civi_register_meta_boxes_page_bottom', array())
				),
			);

			return apply_filters('civi_register_meta_boxes', $configs);
		}

		/**
		 * Register options config
		 * @param $configs
		 * @return mixed
		 */
		public function register_options_config($configs)
		{
			$configs[CIVI_OPTIONS_NAME] = array(
				'layout' => 'horizontal',
				'page_title' => esc_html__('Theme Options', 'civi-framework'),
				'menu_title' => esc_html__('Theme Options', 'civi-framework'),
				'option_name' => CIVI_OPTIONS_NAME,
				'permission' => 'edit_theme_options',
				'section' => array_merge(
					apply_filters('civi_register_options_config_top', array()),
					array(
						$this->general_option(),
						$this->jobs_option(),
						$this->company_option(),
						$this->candidate_option(),
						$this->social_network(),
						$this->login_option(),
						$this->google_map_option(),
						$this->price_format_option(),
						$this->payment_option(),
						$this->user_option(),
						$this->url_slugs_option(),
						$this->setup_page(),
						$this->email_management_option(),
						$this->custom_field_jobs_option(),
						$this->custom_field_company_option(),
						$this->custom_field_candidate_option(),
					),
					apply_filters('civi_register_options_config_bottom', array())
				)
			);
			return apply_filters('civi_register_options_config', $configs);
		}

		/**
		 * @return mixed|void
		 */
		private function general_option()
		{
			return apply_filters('civi_register_option_general', array(
				'id' => 'civi_general_option',
				'title' => esc_html__('General Option', 'civi-framework'),
				'icon' => 'dashicons-admin-multisite',
				'fields' => array_merge(
					apply_filters('civi_register_option_general_top', array()),
					array(
						array(
							'id' => 'enable_cookie',
							'type' => 'button_set',
							'title' => esc_html__('Enable Cookie Notice', 'civi-framework'),
							'subtitle' => esc_html__('Enable/Disable Popup Cookie Notice', 'civi-framework'),
							'desc' => '',
							'options' => array(
								'1' => esc_html__('On', 'civi-framework'),
								'0' => esc_html__('Off', 'civi-framework'),
							),
							'default' => '0',
						),
						array(
							'id' => 'enable_search_box_dropdown',
							'type' => 'button_set',
							'title' => esc_html__('Enable Search Box', 'civi-framework'),
							'subtitle' => esc_html__('Enable/Disable Search Box for Dropdown', 'civi-framework'),
							'desc' => '',
							'options' => array(
								'1' => esc_html__('On', 'civi-framework'),
								'0' => esc_html__('Off', 'civi-framework'),
							),
							'default' => '0',
						),
						array(
							'id' => 'civi-cv-type',
							'title' => esc_html__('Cv Types', 'civi-framework'),
							'type' => 'text',
							'default' => 'doc,docx,pdf',
							'subtitle' => 'Add "," to separate file formats',
						),
						array(
							'id' => 'civi_image_type',
							'title' => esc_html__('Types Upload Image', 'civi-framework'),
							'type' => 'text',
							'default' => 'jpg,jpeg,png,gif,webp',
							'subtitle' => 'Add "," to separate file formats',
						),
						array(
							'id' => 'civi_max_gallery_images',
							'type' => 'text',
							'title' => esc_html__('Maximum Images', 'civi-framework'),
							'subtitle' => esc_html__('Maximum images allowed for single jobs.', 'civi-framework'),
							'default' => '5',
						),
						array(
							'id' => 'civi_image_max_file_size',
							'type' => 'text',
							'title' => esc_html__('Maximum File Size', 'civi-framework'),
							'subtitle' => esc_html__('Maximum upload image size. For example 10kb, 500kb, 1mb, 10mb, 100mb', 'civi-framework'),
							'default' => '1000kb',
						),
						array(
							'id' => 'enable_rtl_mode',
							'type' => 'button_set',
							'title' => esc_html__('Enable RTL Mode', 'civi-framework'),
							'subtitle' => esc_html__('Enable/Disable RTL mode', 'civi-framework'),
							'desc' => '',
							'options' => array(
								'1' => esc_html__('On', 'civi-framework'),
								'0' => esc_html__('Off', 'civi-framework'),
							),
							'default' => '0'
						),
						array(
							'id' => 'header_script',
							'type' => 'ace_editor',
							'title' => esc_html__('Header Script', 'civi-framework'),
							'subtitle' => esc_html__('Add custom scripts inside HEAD tag. You need to have a SCRIPT tag around scripts.', 'civi-framework'),
							'default' => ''
						),
						array(
							'id' => 'footer_script',
							'type' => 'ace_editor',
							'title' => esc_html__('Footer Script', 'civi-framework'),
							'subtitle' => esc_html__('Add custom scripts you might want to be loaded in the footer of your website. You need to have a SCRIPT tag around scripts.', 'civi-framework'),
							'default' => ''
						),
					),
					apply_filters('civi_register_option_general_bottom', array())
				)
			));
		}

		/**
		 * @return mixed|void
		 */
		private function setup_page()
		{
			return apply_filters('civi_register_setup_page', array(
				'id' => 'civi_setup_page',
				'title' => esc_html__('Setup Page', 'civi-framework'),
				'icon' => 'dashicons-admin-page',
				'fields' => array_merge(
					apply_filters('civi_register_setup_page_employer_top', array()),
					array(
						array(
							'id' => 'civi_package_page_id',
							'title' => esc_html__('Package Page', 'civi-framework'),
							'type' => 'select',
							'data' => 'page',
							'data_args' => array(
								'numberposts' => -1,
							)
						),
						array(
							'id' => 'civi_payment_page_id',
							'title' => esc_html__('Payment Page', 'civi-framework'),
							'type' => 'select',
							'data' => 'page',
							'data_args' => array(
								'numberposts' => -1,
							)
						),
						array(
							'id' => 'civi_payment_completed_page_id',
							'title' => esc_html__('Payment Completed Page', 'civi-framework'),
							'type' => 'select',
							'data' => 'page',
							'data_args' => array(
								'numberposts' => -1,
							)
						),
						array(
							'id' => 'terms_condition',
							'title' => esc_html__('Terms & Conditions', 'civi-framework'),
							'type' => 'select',
							'data' => 'page',
							'data_args' => array(
								'numberposts' => -1,
							)
						),
						array(
							'id' => 'privacy_policy',
							'title' => esc_html__('Privacy Policy', 'civi-framework'),
							'type' => 'select',
							'data' => 'page',
							'data_args' => array(
								'numberposts' => -1,
							)
						),
						array(
							'id' => 'civi_update_profile_page_id',
							'title' => esc_html__('Update Profile', 'civi-framework'),
							'type' => 'select',
							'data' => 'page',
							'data_args' => array(
								'numberposts' => -1,
							)
						),
						array(
							'id' => 'civi_add_jobs_page_id',
							'title' => esc_html__('Post Jobs (Login)', 'civi-framework'),
							'type' => 'select',
							'data' => 'page',
							'data_args' => array(
								'numberposts' => -1,
							)
						),
						array(
							'id' => 'civi_add_jobs_not_page_id',
							'title' => esc_html__('Post Jobs (Not Login)', 'civi-framework'),
							'type' => 'select',
							'data' => 'page',
							'data_args' => array(
								'numberposts' => -1,
							)
						),
						apply_filters('civi_register_setup_page_employer_option_main', array(
							'id' => 'civi_register_setup_page_employer_option_main',
							'type' => 'group',
							'title' => esc_html__('Employer Setting', 'civi-framework'),
							'fields' => array(
								array(
									'id' => 'civi_dashboard_page_id',
									'title' => esc_html__('Dashboard Page', 'civi-framework'),
									'type' => 'select',
									'data' => 'page',
									'data_args' => array(
										'numberposts' => -1,
									)
								),
								array(
									'id' => 'civi_jobs_dashboard_page_id',
									'title' => esc_html__('Jobs Page', 'civi-framework'),
									'type' => 'select',
									'data' => 'page',
									'data_args' => array(
										'numberposts' => -1,
									)
								),
								array(
									'id' => 'civi_jobs_submit_page_id',
									'title' => esc_html__('Jobs Submit Page', 'civi-framework'),
									'type' => 'select',
									'data' => 'page',
									'data_args' => array(
										'numberposts' => -1,
									)
								),
								array(
									'id' => 'civi_applicants_page_id',
									'title' => esc_html__('Applicants Page', 'civi-framework'),
									'type' => 'select',
									'data' => 'page',
									'data_args' => array(
										'numberposts' => -1,
									)
								),
								array(
									'id' => 'civi_candidates_page_id',
									'title' => esc_html__('Candidates Page', 'civi-framework'),
									'type' => 'select',
									'data' => 'page',
									'data_args' => array(
										'numberposts' => -1,
									)
								),
								array(
									'id' => 'civi_user_package_page_id',
									'title' => esc_html__('User Package Page', 'civi-framework'),
									'type' => 'select',
									'data' => 'page',
									'data_args' => array(
										'numberposts' => -1,
									)
								),
								array(
									'id' => 'civi_company_page_id',
									'title' => esc_html__('Company Page', 'civi-framework'),
									'type' => 'select',
									'data' => 'page',
									'data_args' => array(
										'numberposts' => -1,
									)
								),
								array(
									'id' => 'civi_submit_company_page_id',
									'title' => esc_html__('Submit Company Page', 'civi-framework'),
									'type' => 'select',
									'data' => 'page',
									'data_args' => array(
										'numberposts' => -1,
									)
								),
								array(
									'id' => 'civi_messages_page_id',
									'title' => esc_html__('Messages Page', 'civi-framework'),
									'type' => 'select',
									'data' => 'page',
									'data_args' => array(
										'numberposts' => -1,
									)
								),
								array(
									'id' => 'civi_meetings_page_id',
									'title' => esc_html__('Meetings Page', 'civi-framework'),
									'type' => 'select',
									'data' => 'page',
									'data_args' => array(
										'numberposts' => -1,
									)
								),
								array(
									'id' => 'civi_settings_page_id',
									'title' => esc_html__('Settings Page', 'civi-framework'),
									'type' => 'select',
									'data' => 'page',
									'data_args' => array(
										'numberposts' => -1,
									)
								),
								/* Membership */
								array(
									'id' => 'civi_membership_page_id',
									'title' => esc_html__('Membership Page', 'civi-framework'),
									'type' => 'select',
									'data' => 'page',
									'data_args' => array(
										'numberposts' => -1,
									)
								),
								/* End Membership */
							),
						))
					),
					apply_filters('civi_register_setup_page_employer_bottom', array()),
					apply_filters('civi_register_setup_page_candidate_top', array()),
					array(
						apply_filters('civi_register_setup_page_candidate_option_main', array(
							'id' => 'civi_register_setup_page_candidate_option_main',
							'type' => 'group',
							'title' => esc_html__('Candidate Setting', 'civi-framework'),
							'fields' => array(
								array(
									'id' => 'civi_candidate_dashboard_page_id',
									'title' => esc_html__('Dashboard Page', 'civi-framework'),
									'type' => 'select',
									'data' => 'page',
									'data_args' => array(
										'numberposts' => -1,
									)
								),
								array(
									'id' => 'civi_candidate_profile_page_id',
									'title' => esc_html__('Profile Page', 'civi-framework'),
									'type' => 'select',
									'data' => 'page',
									'data_args' => array(
										'numberposts' => -1,
									)
								),
								array(
									'id' => 'civi_my_jobs_page_id',
									'title' => esc_html__('My Jobs Page', 'civi-framework'),
									'type' => 'select',
									'data' => 'page',
									'data_args' => array(
										'numberposts' => -1,
									)
								),
								array(
									'id' => 'civi_candidate_reviews_page_id',
									'title' => esc_html__('My Reviews Page', 'civi-framework'),
									'type' => 'select',
									'data' => 'page',
									'data_args' => array(
										'numberposts' => -1,
									)
								),
								array(
									'id' => 'civi_candidate_company_page_id',
									'title' => esc_html__('My Following', 'civi-framework'),
									'type' => 'select',
									'data' => 'page',
									'data_args' => array(
										'numberposts' => -1,
									)
								),
								array(
									'id' => 'civi_candidate_messages_page_id',
									'title' => esc_html__('Messages Page', 'civi-framework'),
									'type' => 'select',
									'data' => 'page',
									'data_args' => array(
										'numberposts' => -1,
									)
								),
								array(
									'id' => 'civi_candidate_meetings_page_id',
									'title' => esc_html__('Meetings Page', 'civi-framework'),
									'type' => 'select',
									'data' => 'page',
									'data_args' => array(
										'numberposts' => -1,
									)
								),
								array(
									'id' => 'civi_candidate_settings_page_id',
									'title' => esc_html__('Settings Page', 'civi-framework'),
									'type' => 'select',
									'data' => 'page',
									'data_args' => array(
										'numberposts' => -1,
									)
								),
								/* Membership */
								array(
									'id' => 'civi_candidate_membership_page_id',
									'title' => esc_html__('Membership Page', 'civi-framework'),
									'type' => 'select',
									'data' => 'page',
									'data_args' => array(
										'numberposts' => -1,
									)
								),
								/* ******** */
							),
						)),
					),
					apply_filters('civi_register_setup_page_candidate_bottom', array())
				)
			));
		}

		/**
		 * @return mixed|void
		 */
		private function url_slugs_option()
		{

			return
				apply_filters(
					'civi_register_option_url_slugs',
					array(
						'id' => 'civi_url_slugs_option',
						'title' => esc_html__('URL Slug', 'civi-framework'),
						'icon' => 'dashicons-admin-links',
						'fields' => array(
							array(
								'id' => 'enable_slug_categories',
								'type' => 'button_set',
								'title' => esc_html__('Slug Categories', 'civi-framework'),
								'subtitle' => esc_html__('Show/Hidden Slug Categories', 'civi-framework'),
								'desc' => '',
								'options' => array(
									'1' => esc_html__('On', 'civi-framework'),
									'0' => esc_html__('Off', 'civi-framework'),
								),
								'default' => '1',
							),

							//Jobs
							apply_filters('civi_register_option_url_jobs_slugs_top', array()),
							apply_filters('civi_register_option_url_jobs_slugs_center', array(
								'id' => 'civi_main_group',
								'type' => 'group',
								'title' => esc_html__('Jobs', 'civi-framework'),
								'fields' => array(
									array(
										'id' => 'jobs_url_slug',
										'title' => esc_html__('Jobs Slug', 'civi-framework'),
										'type' => 'text',
										'default' => 'jobs',
									),
									array(
										'id' => 'jobs_type_url_slug',
										'title' => esc_html__('Type Slug', 'civi-framework'),
										'type' => 'text',
										'default' => 'jobs-type',
									),
									array(
										'id' => 'jobs_categories_url_slug',
										'title' => esc_html__('Categories Slug', 'civi-framework'),
										'type' => 'text',
										'default' => 'jobs-categories',
									),
									array(
										'id' => 'jobs_skills_url_slug',
										'title' => esc_html__('Skills Slug', 'civi-framework'),
										'type' => 'text',
										'default' => 'jobs-skills',
									),
									array(
										'id' => 'jobs_location_url_slug',
										'title' => esc_html__('Location Slug', 'civi-framework'),
										'type' => 'text',
										'default' => 'jobs-location',
									),
									array(
										'id' => 'jobs_career_url_slug',
										'title' => esc_html__('Career Slug', 'civi-framework'),
										'type' => 'text',
										'default' => 'jobs-career',
									),
									array(
										'id' => 'jobs_experience_url_slug',
										'title' => esc_html__('Experience Slug', 'civi-framework'),
										'type' => 'text',
										'default' => 'jobs-experience',
									),
									array(
										'id' => 'jobs_qualification_url_slug',
										'title' => esc_html__('Qualification Slug', 'civi-framework'),
										'type' => 'text',
										'default' => 'jobs-qualification',
									),
								),
							)),
							apply_filters('civi_register_option_url_jobs_slugs_bottom', array()),

							//Company
							apply_filters('civi_register_option_url_company_slugs_top', array()),
							apply_filters('civi_register_option_url_company_slugs_center', array(
								'id' => 'civi_main_group',
								'type' => 'group',
								'title' => esc_html__('Company', 'civi-framework'),
								'fields' => array(
									array(
										'id' => 'company_url_slug',
										'title' => esc_html__('Company Slug', 'civi-framework'),
										'type' => 'text',
										'default' => 'companies',
									),
									array(
										'id' => 'company_categories_url_slug',
										'title' => esc_html__('Categories Slug', 'civi-framework'),
										'type' => 'text',
										'default' => 'company-categories',
									),
									array(
										'id' => 'company_location_url_slug',
										'title' => esc_html__('Location Slug', 'civi-framework'),
										'type' => 'text',
										'default' => 'company-location',
									),
									array(
										'id' => 'company_size_url_slug',
										'title' => esc_html__('Size Slug', 'civi-framework'),
										'type' => 'text',
										'default' => 'company-size',
									),
								),
							)),
							apply_filters('civi_register_option_url_company_slugs_bottom', array()),

							//Candidate
							apply_filters('civi_register_option_url_candidate_slugs_top', array()),
							apply_filters('civi_register_option_url_candidate_slugs_center', array(
								'id' => 'civi_main_group',
								'type' => 'group',
								'title' => esc_html__('Candidate', 'civi-framework'),
								'fields' => array(
									array(
										'id' => 'candidate_url_slug',
										'title' => esc_html__('Candidate Slug', 'civi-framework'),
										'type' => 'text',
										'default' => 'candidates',
									),
									array(
										'id' => 'candidate_categories_url_slug',
										'title' => esc_html__('Categories Slug', 'civi-framework'),
										'type' => 'text',
										'default' => 'candidate_categories',
									),
									array(
										'id' => 'candidate_ages_url_slug',
										'title' => esc_html__('Ages Slug', 'civi-framework'),
										'type' => 'text',
										'default' => 'candidate-ages',
									),
									array(
										'id' => 'candidate_languages_url_slug',
										'title' => esc_html__('Languages Slug', 'civi-framework'),
										'type' => 'text',
										'default' => 'candidate-languages',
									),
									array(
										'id' => 'candidate_qualification_url_slug',
										'title' => esc_html__('Qualification Slug', 'civi-framework'),
										'type' => 'text',
										'default' => 'candidate-qualification',
									),
									array(
										'id' => 'candidate_salary_types_url_slug',
										'title' => esc_html__('Salary Types Slug', 'civi-framework'),
										'type' => 'text',
										'default' => 'candidate-salary-types',
									),
									array(
										'id' => 'candidate_yoe_url_slug',
										'title' => esc_html__('Yoe Slug', 'civi-framework'),
										'type' => 'text',
										'default' => 'candidate-yoe',
									),
									array(
										'id' => 'candidate_education_levels_url_slug',
										'title' => esc_html__('Education Levels Slug', 'civi-framework'),
										'type' => 'text',
										'default' => 'candidate-education-levels',
									),
									array(
										'id' => 'candidate_skills_url_slug',
										'title' => esc_html__('Skills Slug', 'civi-framework'),
										'type' => 'text',
										'default' => 'candidate-skills',
									),
									/* ************* */
									/* bank */ 
									array(
										'id' => 'candidate_paymentinfo_url_slug',
										'title' => esc_html__('Payment Info Slug', 'civi-framework'),
										'type' => 'text',
										'default' => 'candidate-paymentinfo',
									),
									/**** */
									array(
										'id' => 'candidate_locations_url_slug',
										'title' => esc_html__('Locations Slug', 'civi-framework'),
										'type' => 'text',
										'default' => 'candidate-locations',
									),
								),
							)),
							apply_filters('civi_register_option_url_candidate_slugs_bottom', array()),

							//Other
							apply_filters('civi_register_option_url_other_slugs_top', array()),
							apply_filters('civi_register_option_url_other_slugs_center', array(
								'id' => 'civi_main_group',
								'type' => 'group',
								'title' => esc_html__('Other', 'civi-framework'),
								'fields' => array(
									array(
										'id' => 'package_url_slug',
										'title' => esc_html__('Package Slug', 'civi-framework'),
										'type' => 'text',
										'default' => 'package',
									),
									array(
										'id' => 'invoice_url_slug',
										'title' => esc_html__('Invoice Slug', 'civi-framework'),
										'type' => 'text',
										'default' => 'invoice',
									)
								),
							)),
							apply_filters('civi_register_option_url_other_slugs_bottom', array()),
						),
					)
				);
		}

		/**
		 * @return mixed|void
		 */
		private function custom_field_jobs_option()
		{
			return apply_filters('civi_register_option_custom_field_jobs', array(
				'id' => 'civi_custom_field_jobs_option',
				'title' => esc_html__('Custom Field (Jobs)', 'civi-framework'),
				'icon' => 'dashicons dashicons-admin-customizer',
				'fields' => array_merge(
					apply_filters('civi_register_option_custom_field_jobs_top', array()),
					apply_filters('civi_register_option_custom_field_jobs_main', array(
						array(
							'id' => "custom_field_jobs",
							'type' => 'panel',
							'title' => esc_html__('Additional Field', 'civi-framework'),
							'sort' => true,
							'panel_title' => 'label',
							'fields' => array(
								array(
									'title' => esc_html__('Label', 'civi-framework'),
									'id' => "label",
									'type' => 'text',
									'default' => '',
								),
								array(
									'title' => esc_html__('ID', 'civi-framework'),
									'id' => "id",
									'type' => 'text',
									'placeholder' => esc_html__('Enter field ID', 'civi-framework'),
									'desc' => esc_html__('ID values cannot be changed after being set!', 'civi-framework'),
									'default' => '',
								),
								array(
									'title' => esc_html__('Field Type', 'civi-framework'),
									'id' => "field_type",
									'type' => 'select',
									'default' => 'text',
									'options' => array(
										'text' => esc_html__('Text', 'civi-framework'),
										'url' => esc_html__('Video Url', 'civi-framework'),
										'textarea' => esc_html__('Textarea', 'civi-framework'),
										'select' => esc_html__('Select', 'civi-framework'),
										'checkbox_list' => esc_html__('Checkbox', 'civi-framework'),
									)
								),
								array(
									'title' => esc_html__('Options Value', 'civi-framework'),
									'subtitle' => esc_html__('Input each per line', 'civi-framework'),
									'id' => "select_choices",
									'type' => 'textarea',
									'default' => '',
									'required' => array(
										"custom_field_jobs_field_type",
										'in',
										array('checkbox_list', 'radio', 'select')
									),
								),
							)
						)
					)),
					apply_filters('civi_register_option_custom_field_jobs_bottom', array())
				)
			));
		}

		private function custom_field_company_option()
		{
			return apply_filters('civi_register_option_custom_field_company', array(
				'id' => 'civi_custom_field_company_option',
				'title' => esc_html__('Custom Field (Company)', 'civi-framework'),
				'icon' => 'dashicons dashicons-admin-customizer',
				'fields' => array_merge(
					apply_filters('civi_register_option_custom_field_company_top', array()),
					apply_filters('civi_register_option_custom_field_company_main', array(
						array(
							'id' => "custom_field_company",
							'type' => 'panel',
							'title' => esc_html__('Additional Field', 'civi-framework'),
							'sort' => true,
							'panel_title' => 'label',
							'fields' => array(
								array(
									'title' => esc_html__('Label', 'civi-framework'),
									'id' => "label",
									'type' => 'text',
									'default' => '',
								),
								array(
									'title' => esc_html__('ID', 'civi-framework'),
									'id' => "id",
									'type' => 'text',
									'placeholder' => esc_html__('Enter field ID', 'civi-framework'),
									'desc' => esc_html__('ID values cannot be changed after being set!', 'civi-framework'),
									'default' => '',
								),
								array(
									'title' => esc_html__('Field Type', 'civi-framework'),
									'id' => "field_type",
									'type' => 'select',
									'default' => 'text',
									'options' => array(
										'text' => esc_html__('Text', 'civi-framework'),
										'url' => esc_html__('Video Url', 'civi-framework'),
										'textarea' => esc_html__('Textarea', 'civi-framework'),
										'select' => esc_html__('Select', 'civi-framework'),
										'checkbox_list' => esc_html__('Checkbox', 'civi-framework'),
									)
								),
								array(
									'title' => esc_html__('Options Value', 'civi-framework'),
									'subtitle' => esc_html__('Input each per line', 'civi-framework'),
									'id' => "select_choices",
									'type' => 'textarea',
									'default' => '',
									'required' => array(
										"custom_field_company_field_type",
										'in',
										array('checkbox_list', 'radio', 'select')
									),
								),
							)
						)
					)),
					apply_filters('civi_register_option_custom_field_company_bottom', array())
				)
			));
		}

		private function custom_field_candidate_option()
		{
			return apply_filters('civi_register_option_custom_field_candidate', array(
				'id' => 'civi_custom_field_candidate_option',
				'title' => esc_html__('Custom Field (Candidate)', 'civi-framework'),
				'icon' => 'dashicons dashicons-admin-customizer',
				'fields' => array_merge(
					apply_filters('civi_register_option_custom_field_candidate_top', array()),
					apply_filters('civi_register_option_custom_field_candidate_main', array(
						array(
							'id' => "custom_field_candidate",
							'type' => 'panel',
							'title' => esc_html__('Additional Field', 'civi-framework'),
							'sort' => true,
							'panel_title' => 'label',
							'fields' => array(
								array(
									'title' => esc_html__('Tabs', 'civi-framework'),
									'id' => "tabs",
									'type' => 'select',
									'default' => 'text',
									'options' => array(
										'info' => esc_html__('Info', 'civi-framework'),
										'education' => esc_html__('Education', 'civi-framework'),
										'experience' => esc_html__('Experience', 'civi-framework'),
										'skills' => esc_html__('Skills', 'civi-framework'),
										'paymentinfo' => esc_html__('Payment Information', 'civi-framework'),
										'projects' => esc_html__('Projects', 'civi-framework'),
										'awards' => esc_html__('Awards', 'civi-framework'),
										'new' => esc_html__('New Tabs', 'civi-framework'),
									),
									'default' => 'new',
								),
								array(
									'title' => esc_html__('Name Tabs', 'civi-framework'),
									'id' => "section",
									'type' => 'text',
									'default' => '',
									'required' => array(
										array("tabs", '=', 'new')
									),
								),
								array(
									'title' => esc_html__('Label', 'civi-framework'),
									'id' => "label",
									'type' => 'text',
									'default' => '',
								),
								array(
									'title' => esc_html__('ID', 'civi-framework'),
									'id' => "id",
									'type' => 'text',
									'placeholder' => esc_html__('Enter field ID', 'civi-framework'),
									'desc' => esc_html__('ID values cannot be changed after being set!', 'civi-framework'),
									'default' => '',
								),
								array(
									'title' => esc_html__('Field Type', 'civi-framework'),
									'id' => "field_type",
									'type' => 'select',
									'default' => 'text',
									'options' => array(
										'text' => esc_html__('Text', 'civi-framework'),
										'url' => esc_html__('Video Url', 'civi-framework'),
										'textarea' => esc_html__('Textarea', 'civi-framework'),
										'select' => esc_html__('Select', 'civi-framework'),
										'checkbox_list' => esc_html__('Checkbox', 'civi-framework'),
									)
								),
								array(
									'title' => esc_html__('Options Value', 'civi-framework'),
									'subtitle' => esc_html__('Input each per line', 'civi-framework'),
									'id' => "select_choices",
									'type' => 'textarea',
									'default' => '',
									'required' => array(
										"custom_field_candidate_field_type",
										'in',
										array('checkbox_list', 'radio', 'select')
									),
								),
							)
						)
					)),
					apply_filters('civi_register_option_custom_field_candidate_bottom', array())
				)
			));
		}

		function additional_details_field($meta_prefix)
		{
			if (!class_exists('Civi_Framework')) {
				return array(
					'id' => "{$meta_prefix}additional_features",
					'title' => esc_html__('Additional details:', 'civi-framework'),
					'type' => 'custom',
					'default' => array(),
					'template' => CIVI_PLUGIN_DIR . '/includes/admin/templates/additional-details-field.php',
				);
			}
			return array(
				'id' => "{$meta_prefix}additional_features",
				'type' => 'repeater',
				'title' => esc_html__('Additional details:', 'civi-framework'),
				'col' => '6',
				'sort' => true,
				'fields' => array(
					array(
						'id' => "{$meta_prefix}additional_feature_title",
						'title' => esc_html__('Title:', 'civi-framework'),
						'desc' => esc_html__('Enter additional title', 'civi-framework'),
						'type' => 'text',
						'default' => '',
						'col' => '5',
					),
					array(
						'id' => "{$meta_prefix}additional_feature_value",
						'title' => esc_html__('Value', 'civi-framework'),
						'desc' => esc_html__('Enter additional value', 'civi-framework'),
						'type' => 'text',
						'default' => '',
						'col' => '7',
					),
				)
			);
		}

		/**
		 * @return mixed|void
		 */
		private function price_format_option()
		{
			return apply_filters('civi_register_option_price_format', array(
				'id' => 'civi_price_format_option',
				'title' => esc_html__('Price Format', 'civi-framework'),
				'icon' => 'dashicons-money',
				'fields' => array_merge(
					apply_filters('civi_register_option_price_format_top', array()),
					apply_filters('civi_register_option_price_format_main', array(
						array(
							'id' => 'currency_position',
							'title' => esc_html__('Currency Sign Position', 'civi-framework'),
							'type' => 'select',
							'options' => array(
								'before' => esc_html__('Before ($59)', 'civi-framework'),
								'after' => esc_html__('After (59$)', 'civi-framework'),
							),
							'default' => 'before',
						),
						array(
							'id' => 'thousand_separator',
							'title' => esc_html__('Thousand Separator', 'civi-framework'),
							'type' => 'text',
							'default' => ',',
						),
						array(
							'id' => 'decimal_separator',
							'title' => esc_html__('Decimal Separator', 'civi-framework'),
							'type' => 'text',
							'default' => '.',
						),
						array(
							'id' => 'currency_type_default',
							'title' => esc_html__('Currency Type (Default)', 'civi-framework'),
							'type' => 'text',
							'default' => 'USD',
						),
						array(
							'id' => 'currency_sign_default',
							'title' => esc_html__('Currency Sign (Default)', 'civi-framework'),
							'type' => 'text',
							'default' => '$',
						),
						array(
							'id' => "currency_fields",
							'type' => 'panel',
							'title' => esc_html__('Currency Field', 'civi-framework'),
							'sort' => true,
							'panel_title' => 'label',
							'fields' => array(
								array(
									'id' => 'currency_type',
									'title' => esc_html__('Currency Type', 'civi-framework'),
									'type' => 'text',
									'default' => 'VND',
								),
								array(
									'id' => 'currency_sign',
									'title' => esc_html__('Currency Sign', 'civi-framework'),
									'type' => 'text',
									'default' => 'đ',
								),
								array(
									'id' => 'currency_conversion',
									'title' => esc_html__('Currency Conversion', 'civi-framework'),
									'type' => 'text',
									'desc' => esc_html__('Convert currency values ​​based on default currency', 'civi-framework'),
									'default' => '',
								),
							)
						)
					)),
					apply_filters('civi_register_option_price_format_bottom', array())
				)
			));
		}

		/**
		 * @return mixed|void
		 */
		private function google_map_option()
		{
			$allowed_html = array(
				'i' => array(
					'class' => array()
				),
				'span' => array(
					'class' => array()
				),
				'a' => array(
					'href' => array(),
					'title' => array(),
					'target' => array()
				)
			);
			return apply_filters('civi_register_option_google_map', array(
				'id' => 'civi_google_map_option',
				'title' => esc_html__('Maps Config', 'civi-framework'),
				'icon' => 'dashicons-admin-site',
				'fields' => array_merge(
					apply_filters('civi_register_option_google_map_top', array()),
					apply_filters('civi_register_option_google_map_main', array(
						array(
							'id' => 'map_effects',
							'title' => esc_html__('Maps Effects', 'civi-framework'),
							'type' => 'select',
							'options' => array(
								'' => esc_html__('None', 'civi-framework'),
								'shine' => esc_html__('Shine', 'civi-framework'),
								'popup' => esc_html__('Popup', 'civi-framework'),
							),
							'default' => 'shine',
						),
						array(
							'id' => 'map_type',
							'title' => esc_html__('Maps Type', 'civi-framework'),
							'type' => 'select',
							'options' => array(
								'google_map' => esc_html__('Google Map', 'civi-framework'),
								'mapbox' => esc_html__('Mapbox', 'civi-framework'),
								'openstreetmap' => esc_html__('OpenStreetMap', 'civi-framework'),
							),
							'default' => 'mapbox',
						),
						array(
							'id' => 'map_ssl',
							'title' => esc_html__('Maps SSL', 'civi-framework'),
							'subtitle' => esc_html__('Use maps with ssl', 'civi-framework'),
							'type' => 'button_set',
							'options' => array(
								'1' => esc_html__('Yes', 'civi-framework'),
								'0' => esc_html__('No', 'civi-framework'),
							),
							'default' => '0',
						),
						array(
							'id' => 'googlemap_type',
							'title' => esc_html__('Google Maps Type', 'civi-framework'),
							'type' => 'select',
							'options' => array(
								'roadmap' => esc_html__('Roadmap', 'civi-framework'),
								'satellite' => esc_html__('Satellite', 'civi-framework'),
								'hybrid' => esc_html__('Hybrid', 'civi-framework'),
								'terrain' => esc_html__('Terrain', 'civi-framework'),
							),
							'default' => 'roadmap',
							'required' => array("map_type", '=', 'google_map'),
						),
						array(
							'id' => 'googlemap_api_key',
							'type' => 'text',
							'title' => esc_html__('Google Maps API KEY', 'civi-framework'),
							'subtitle' => esc_html__('Enter your google maps api key', 'civi-framework'),
							'default' => 'AIzaSyBvPDNG6pePr9iFpeRKaOlaZF_l0oT3lWk',
							'required' => array("map_type", '=', 'google_map'),
						),
						array(
							'id' => 'mapbox_api_key',
							'type' => 'text',
							'title' => esc_html__('Mapbox API KEY', 'civi-framework'),
							'subtitle' => esc_html__('Enter your mapbox api key', 'civi-framework'),
							'default' => 'pk.eyJ1Ijoic2F5aTc3NDciLCJhIjoiY2tpcXRmYW1tMWpjMjJzbGllbThieTFlaCJ9.eDj6zNLBZpG-veFqXiyVPw',
							'required' => array("map_type", '=', 'mapbox'),
						),
						array(
							'id' => 'openstreetmap_api_key',
							'type' => 'text',
							'title' => esc_html__('OpenStreetMap API KEY', 'civi-framework'),
							'subtitle' => esc_html__('Enter your OpenStreetMap api key', 'civi-framework'),
							'default' => 'pk.eyJ1Ijoic2F5aTc3NDciLCJhIjoiY2tpcXRmYW1tMWpjMjJzbGllbThieTFlaCJ9.eDj6zNLBZpG-veFqXiyVPw',
							'required' => array("map_type", '=', 'openstreetmap'),
						),
						array(
							'id' => 'map_pin_cluster',
							'title' => esc_html__('Pin Cluster', 'civi-framework'),
							'subtitle' => esc_html__('Use pin cluster on map', 'civi-framework'),
							'type' => 'button_set',
							'options' => array(
								'1' => esc_html__('Yes', 'civi-framework'),
								'0' => esc_html__('No', 'civi-framework'),
							),
							'default' => '0',
							'required' => array("map_type", '=', 'google_map'),
						),
						array(
							'id' => 'googlemap_style',
							'type' => 'ace_editor',
							'title' => esc_html__('Style for Google Map', 'civi-framework'),
							'subtitle' => sprintf(
								__('Use %s https://snazzymaps.com/ %s to create styles', 'civi-framework'),
								'<a href="https://snazzymaps.com/" target="_blank">',
								'</a>'
							),
							'default' => '',
							'required' => array("map_type", '=', 'google_map'),
						),
						array(
							'id' => 'mapbox_style',
							'title' => esc_html__('Style for Mapbox', 'civi-framework'),
							'type' => 'select',
							'options' => array(
								'streets-v11' => esc_html__('Streets', 'civi-framework'),
								'light-v10' => esc_html__('Light', 'civi-framework'),
								'dark-v10' => esc_html__('Dark', 'civi-framework'),
								'outdoors-v11' => esc_html__('Outdoors', 'civi-framework'),
								'satellite-v9' => esc_html__('Satellite', 'civi-framework'),
							),
							'required' => array("map_type", '=', 'mapbox'),
						),
						array(
							'id' => 'openstreetmap_style',
							'title' => esc_html__('Style for OpenStreetMap', 'civi-framework'),
							'type' => 'select',
							'options' => array(
								'streets-v11' => esc_html__('Streets', 'civi-framework'),
								'light-v10' => esc_html__('Light', 'civi-framework'),
								'dark-v10' => esc_html__('Dark', 'civi-framework'),
								'outdoors-v11' => esc_html__('Outdoors', 'civi-framework'),
								'satellite-v9' => esc_html__('Satellite', 'civi-framework'),
							),
							'required' => array("map_type", '=', 'openstreetmap'),
						),
                        array(
                            'id' => 'map_zoom_level',
                            'type' => 'text',
                            'title' => esc_html__('Default Map Zoom', 'civi-framework'),
                            'default' => '3'
                        ),
                        array(
                            'id' => 'map_lat_default',
                            'type' => 'text',
                            'title' => esc_html__('Default Map Latitude', 'civi-framework'),
                            'default' => '59.325'
                        ),
                        array(
                            'id' => 'map_lng_default',
                            'type' => 'text',
                            'title' => esc_html__('Default Map Longitude ', 'civi-framework'),
                            'default' => '18.070'
                        ),
					)),
					apply_filters('civi_register_option_google_map_bottom', array())
				)
			));
		}

		/**
		 * @return mixed|void
		 */
		private function payment_option()
		{
			return apply_filters('civi_register_option_payment', array(
				'id' => 'civi_payment_option',
				'title' => esc_html__('Payment', 'civi-framework'),
				'icon' => 'dashicons-cart',
				'fields' => array_merge(
					apply_filters('civi_register_option_payment_top', array()),
					apply_filters('civi_register_option_payment_main', array(
						array(
							'id' => 'paid_submission_type',
							'type' => 'select',
							'title' => esc_html__('Paid Submission Type', 'civi-framework'),
							'subtitle' => '',
							'options' => array(
								'no' => esc_html__('Free Submit', 'civi-framework'),
								'per_package' => esc_html__('Pay Per Package', 'civi-framework')
							),
							'default' => 'no',
						),
						array(
							'id' => 'civi_paypal',
							'type' => 'info',
							'style' => 'info',
							'title' => esc_html__('Paypal Setting', 'civi-framework'),
							'required' => array('paid_submission_type', '!=', 'no'),
						),
						array(
							'id' => 'enable_paypal',
							'title' => esc_html__('Enable Paypal', 'civi-framework'),
							'type' => 'button_set',
							'options' => array(
								'1' => esc_html__('Enabled', 'civi-framework'),
								'0' => esc_html__('Disabled', 'civi-framework'),
							),
							'default' => '0',
							'required' => array('paid_submission_type', '!=', 'no'),
						),
						array(
							'id' => 'paypal_api',
							'type' => 'select',
							'required' => array(
								array('enable_paypal', '=', '1'),
								array('paid_submission_type', '!=', 'no')
							),
							'title' => esc_html__('Paypal Api', 'civi-framework'),
							'subtitle' => esc_html__('Sandbox = test API. LIVE = real payments API', 'civi-framework'),
							'desc' => esc_html__('Update PayPal settings according to API type selection', 'civi-framework'),
							'options' => array(
								'sandbox' => esc_html__('Sandbox', 'civi-framework'),
								'live' => esc_html__('Live', 'civi-framework')
							),
							'default' => 'sandbox',
						),
						array(
							'id' => 'paypal_client_id',
							'type' => 'text',
							'required' => array(
								array('enable_paypal', '=', '1'),
								array('paid_submission_type', '!=', 'no')
							),
							'title' => esc_html__('Paypal Client ID', 'civi-framework'),
							'subtitle' => '',
							'default' => '',
						),
						array(
							'id' => 'paypal_client_secret_key',
							'type' => 'text',
							'required' => array(
								array('enable_paypal', '=', '1'),
								array('paid_submission_type', '!=', 'no')
							),
							'title' => esc_html__('Paypal Client Secret Key', 'civi-framework'),
							'subtitle' => '',
							'default' => '',
						),
						array(
							'id' => 'civi_stripe',
							'type' => 'info',
							'style' => 'info',
							'title' => esc_html__('Stripe Setting', 'civi-framework'),
							'required' => array('paid_submission_type', '!=', 'no'),
						),
						array(
							'id' => 'enable_stripe',
							'title' => esc_html__('Enable Stripe', 'civi-framework'),
							'type' => 'button_set',
							'options' => array(
								'1' => esc_html__('Enabled', 'civi-framework'),
								'0' => esc_html__('Disabled', 'civi-framework'),
							),
							'default' => '0',
							'required' => array('paid_submission_type', '!=', 'no'),
						),
						array(
							'id' => 'stripe_secret_key',
							'type' => 'text',
							'required' => array(
								array('enable_stripe', '=', '1'),
								array('paid_submission_type', '!=', 'no')
							),
							'title' => esc_html__('Stripe Secret Key', 'civi-framework'),
							'subtitle' => esc_html__('Info is taken from your account at https://dashboard.stripe.com/login', 'civi-framework'),
							'default' => '',
						),
						array(
							'id' => 'stripe_publishable_key',
							'type' => 'text',
							'required' => array(
								array('enable_stripe', '=', '1'),
								array('paid_submission_type', '!=', 'no')
							),
							'title' => esc_html__('Stripe Publishable Key', 'civi-framework'),
							'subtitle' => esc_html__('Info is taken from your account at https://dashboard.stripe.com/login', 'civi-framework'),
							'default' => '',
						),
						array(
							'id' => 'civi_wire_transfer',
							'type' => 'info',
							'style' => 'info',
							'title' => esc_html__('Wire Transfer Setting', 'civi-framework'),
							'required' => array('paid_submission_type', '!=', 'no'),
						),
						array(
							'id' => 'enable_wire_transfer',
							'title' => esc_html__('Enable Wire Transfer', 'civi-framework'),
							'type' => 'button_set',
							'options' => array(
								'1' => esc_html__('Enabled', 'civi-framework'),
								'0' => esc_html__('Disabled', 'civi-framework'),
							),
							'default' => '0',
							'required' => array('paid_submission_type', '!=', 'no'),
						),
						array(
							'id' => 'wire_transfer_card_number',
							'type' => 'text',
							'required' => array(
								array('enable_wire_transfer', '=', '1'),
								array('paid_submission_type', '!=', 'no')
							),
							'title' => esc_html__('Card Number', 'civi-framework'),
							'subtitle' => '',
							'default' => '',
						),
						array(
							'id' => 'wire_transfer_card_name',
							'type' => 'text',
							'required' => array(
								array('enable_wire_transfer', '=', '1'),
								array('paid_submission_type', '!=', 'no')
							),
							'title' => esc_html__('Card Name', 'civi-framework'),
							'subtitle' => '',
							'default' => '',
						),
						array(
							'id' => 'wire_transfer_bank_name',
							'type' => 'text',
							'required' => array(
								array('enable_wire_transfer', '=', '1'),
								array('paid_submission_type', '!=', 'no')
							),
							'title' => esc_html__('Bank Name', 'civi-framework'),
							'subtitle' => '',
							'default' => '',
						),
						array(
							'id' => 'civi_woocheckout',
							'type' => 'info',
							'style' => 'info',
							'title' => esc_html__('Woocommerce Setting', 'civi-framework'),
							'required' => array('paid_submission_type', '!=', 'no'),
						),
						array(
							'id' => 'enable_woocheckout',
							'title' => esc_html__('Enable Woocommerce Checkout', 'civi-framework'),
							'type' => 'button_set',
							'subtitle' => esc_html__('Works when you activate plugin woocomerce and checkout page', 'civi-framework'),
							'options' => array(
								'1' => esc_html__('Enabled', 'civi-framework'),
								'0' => esc_html__('Disabled', 'civi-framework'),
							),
							'default' => '0',
							'required' => array('paid_submission_type', '!=', 'no'),
						),
					)),
					apply_filters('civi_register_option_payment_bottom', array())
				)
			));
		}

		/**
		 * @return mixed|void
		 */
		private function login_option()
		{
			return apply_filters('civi_register_option_login', array(
				'id' => 'civi_login_option',
				'title' => esc_html__('Login Option', 'civi-framework'),
				'icon' => 'dashicons-admin-users',
				'fields' => array_merge(
					apply_filters('civi_register_option_login_top', array()),
					array(
						array(
							'id' => 'enable_redirect_after_login',
							'type' => 'button_set',
							'title' => esc_html__('Enable Redirect After Login', 'civi-framework'),
							'subtitle' => esc_html__('Enable/Disable Redirect After Login', 'civi-framework'),
							'desc' => '',
							'options' => array(
								'1' => esc_html__('On', 'civi-framework'),
								'0' => esc_html__('Off', 'civi-framework'),
							),
							'default' => '0'
						),
						array(
							'id' => 'redirect_for_admin',
							'title' => esc_html__('Redirect For Admin', 'civi-framework'),
							'subtitle' => esc_html__('Select redirect page after admin login.', 'civi-framework'),
							'type' => 'select',
							'data' => 'page',
							'data_args' => array(
								'numberposts' => -1,
							),
							'required' => array('enable_redirect_after_login', '!=', '0'),
						),
						array(
							'id' => 'redirect_for_candidate',
							'title' => esc_html__('Redirect For Candidate', 'civi-framework'),
							'subtitle' => esc_html__('Select redirect page after candidate login.', 'civi-framework'),
							'type' => 'select',
							'data' => 'page',
							'data_args' => array(
								'numberposts' => -1,
							),
							'required' => array('enable_redirect_after_login', '!=', '0'),
						),
						array(
							'id' => 'redirect_for_employer',
							'title' => esc_html__('Redirect For Employer', 'civi-framework'),
							'subtitle' => esc_html__('Select redirect page after employer login.', 'civi-framework'),
							'type' => 'select',
							'data' => 'page',
							'data_args' => array(
								'numberposts' => -1,
							),
							'required' => array('enable_redirect_after_login', '!=', '0'),
						),
						array(
							'id' => 'enable_verify_user',
							'type' => 'button_set',
							'title' => esc_html__('Enable Verify User', 'civi-framework'),
							'subtitle' => esc_html__('Enable/Disable Verify User After Register', 'civi-framework'),
							'desc' => '',
							'options' => array(
								'1' => esc_html__('On', 'civi-framework'),
								'0' => esc_html__('Off', 'civi-framework'),
							),
							'default' => '0'
						),
						array(
							'id' => "verify_user_time",
							'title' => esc_html__('Verification Expiration Time', 'civi-framework'),
							'subtitle' => esc_html__('Enter the expiration time of the verification code (second)', 'civi-framework'),
							'default' => '900',
							'type' => 'text',
							'required' => array("enable_verify_user", '!=', '0'),
						),
						array(
							'id' => 'enable_social_login',
							'type' => 'button_set',
							'title' => esc_html__('Enable Social Login', 'civi-framework'),
							'subtitle' => esc_html__('Enable/Disable Social Login', 'civi-framework'),
							'desc' => '',
							'options' => array(
								'1' => esc_html__('On', 'civi-framework'),
								'0' => esc_html__('Off', 'civi-framework'),
							),
							'default' => '1'
						),
						array(
							'id' => 'enable_user_role',
							'type' => 'button_set',
							'title' => esc_html__('Enable User Role', 'civi-framework'),
							'subtitle' => esc_html__('Enable/Disable User Role In Form Register', 'civi-framework'),
							'desc' => '',
							'options' => array(
								'1' => esc_html__('On', 'civi-framework'),
								'0' => esc_html__('Off', 'civi-framework'),
							),
							'default' => '1'
						),
						array(
							'id' => 'enable_captcha',
							'type' => 'button_set',
							'title' => esc_html__('Enable Captcha', 'civi-framework'),
							'subtitle' => esc_html__('Enable/Disable Captcha', 'civi-framework'),
							'desc' => '',
							'options' => array(
								'1' => esc_html__('On', 'civi-framework'),
								'0' => esc_html__('Off', 'civi-framework'),
							),
							'default' => '0'
						),
						array(
							'id' => 'google_login_api',
							'type' => 'text',
							'title' => esc_html__('Google Login API', 'civi-framework'),
							'subtitle' => esc_html__('Enter your google login api key'),
							'default' => '912412937100-rbi096jb7j0c9e8ee2ge8mm1hjda24fb.apps.googleusercontent.com'
							
						),
						array(
							'id' => 'facebook_app_id',
							'type' => 'text',
							'title' => esc_html__('Facebook Login API', 'civi-framework'),
							'subtitle' => esc_html__('Enter your facebook login api key'),
							'default' => '1270446883532471'
						),
						array(
							'id' => 'linkedin_client_id',
							'type' => 'text',
							'title' => esc_html__('Linkedin Client ID', 'civi-framework'),
							'subtitle' => esc_html__('Enter your linkedin client id'),
							'default' => '77ckh5i6e10d4w'
						),
						array(
							'id' => 'linkedin_client_secret',
							'type' => 'text',
							'title' => esc_html__('Linkedin Client Secret', 'civi-framework'),
							'subtitle' => esc_html__('Enter your linkedin client secret'),
							'default' => 'DgvFxN7r057LNeMS'
						),
					),
					apply_filters('civi_register_option_login_bottom', array())
				)
			));
		}

		/**
		 * Social network
		 * @return mixed
		 */

		private function social_network()
		{
			return apply_filters('civi_register_social_option', array(
				'id' => 'civi_social_option',
				'title' => esc_html__('Social Network', 'civi-framework'),
				'icon' => 'dashicons dashicons-networking',
				'fields' => array_merge(
					apply_filters('civi_register_social_option_top', array()),
					array(
						array(
							'id' => "enable_social_twitter",
							'type' => 'button_set',
							'title' => esc_html__('Enable Twitter', 'civi-framework'),
							'subtitle' => esc_html__('Enable/Disable Twitter', 'civi-framework'),
							'options' => array(
								'1' => esc_html__('On', 'civi-framework'),
								'0' => esc_html__('Off', 'civi-framework'),
							),
							'default' => '1',
						),
						array(
							'id' => "enable_social_linkedin",
							'type' => 'button_set',
							'title' => esc_html__('Enable Linkedin', 'civi-framework'),
							'subtitle' => esc_html__('Enable/Disable Linkedin', 'civi-framework'),
							'options' => array(
								'1' => esc_html__('On', 'civi-framework'),
								'0' => esc_html__('Off', 'civi-framework'),
							),
							'default' => '1',
						),
						array(
							'id' => "enable_social_facebook",
							'type' => 'button_set',
							'title' => esc_html__('Enable Facebook', 'civi-framework'),
							'subtitle' => esc_html__('Enable/Disable Facebook', 'civi-framework'),
							'options' => array(
								'1' => esc_html__('On', 'civi-framework'),
								'0' => esc_html__('Off', 'civi-framework'),
							),
							'default' => '1',
						),
						array(
							'id' => "enable_social_instagram",
							'type' => 'button_set',
							'title' => esc_html__('Enable Instagram', 'civi-framework'),
							'subtitle' => esc_html__('Enable/Disable Instagram', 'civi-framework'),
							'options' => array(
								'1' => esc_html__('On', 'civi-framework'),
								'0' => esc_html__('Off', 'civi-framework'),
							),
							'default' => '1',
						),
						array(
							'id' => "civi_social_fields",
							'type' => 'panel',
							'title' => esc_html__('Social Field', 'civi-framework'),
							'sort' => true,
							'panel_title' => 'label',
							'fields' => array(
								array(
									'id' => 'social_name',
									'title' => esc_html__('Social Name', 'civi-framework'),
									'type' => 'text',
								),
								array(
									'id' => 'social_icon',
									'title' => esc_html__('Social Icon', 'civi-framework'),
									'type' => 'text',
									'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'civi-framework'),
								),
							)
						)
					),
					apply_filters('civi_register_social_option_bottom', array())
				),
			));
		}

		/**
		 * @return mixed|void
		 */
		private function user_option()
		{
			return apply_filters('civi_register_user_option', array(
				'id' => 'civi_user_option',
				'title' => esc_html__('User Navigation', 'civi-framework'),
				'icon' => 'dashicons-groups',
				'fields' => array_merge(
					apply_filters('civi_register_user_employer_option_top', array()),
					array(
						apply_filters(
							'civi_register_user_employer_option_main',
                            array(
                                'id' => 'show_employer_jobs_post_your',
                                'type' => 'button_set',
                                'title' => esc_html__('Show "Post Your"', 'civi-framework'),
                                'subtitle' => esc_html__('Show/Hide "Post your first job!"', 'civi-framework'),
                                'desc' => '',
                                'options' => array(
                                    '1' => esc_html__('On', 'civi-framework'),
                                    '0' => esc_html__('Off', 'civi-framework'),
                                ),
                                'default' => '1'
                            ),
							array(
								'id' => 'civi_user_option_employer',
								'type' => 'group',
								'title' => esc_html__('Employer Setting', 'civi-framework'),
								'fields' => array(
									array(
										'id' => 'type_icon_employer',
										'type' => 'select',
										'title' => esc_html__('Icon Type', 'civi-framework'),
										'default' => 'svg',
										'options' => array(
											'image' => esc_html__('Image', 'civi-framework'),
											'svg' => esc_html__('Svg', 'civi-framework')
										),
									),
									array(
										'id' => 'show_employer_dashboard',
										'type' => 'button_set',
										'title' => esc_html__('Show "Dashboard"', 'civi-framework'),
										'subtitle' => esc_html__('Show/Hide "Dashboard" on navigation', 'civi-framework'),
										'desc' => '',
										'options' => array(
											'1' => esc_html__('On', 'civi-framework'),
											'0' => esc_html__('Off', 'civi-framework'),
										),
										'default' => '1'
									),
									array(
										'id' => 'image_employer_dashboard',
										'type' => 'image',
										'url' => true,
										'title' => esc_html__('Icon Dashboard', 'civi-framework'),
										'subtitle' => esc_html__('Choose icon for dashboard', 'civi-framework'),
										'required' => array('show_employer_dashboard', '!=', '0'),
									),
									array(
										'id' => 'show_employer_jobs_dashboard',
										'type' => 'button_set',
										'title' => esc_html__('Show "Jobs"', 'civi-framework'),
										'subtitle' => esc_html__('Show/Hide "Jobs" on navigation', 'civi-framework'),
										'desc' => '',
										'options' => array(
											'1' => esc_html__('On', 'civi-framework'),
											'0' => esc_html__('Off', 'civi-framework'),
										),
										'default' => '1'
									),
									array(
										'id' => 'image_employer_jobs_dashboard',
										'type' => 'image',
										'url' => true,
										'title' => esc_html__('Icon Jobs', 'civi-framework'),
										'subtitle' => esc_html__('Choose icon for jobs', 'civi-framework'),
										'required' => array('show_employer_jobs', '!=', '0'),
									),
									array(
										'id' => 'show_employer_applicants',
										'type' => 'button_set',
										'title' => esc_html__('Show "Applicants"', 'civi-framework'),
										'subtitle' => esc_html__('Show/Hide "Applicants" on navigation', 'civi-framework'),
										'desc' => '',
										'options' => array(
											'1' => esc_html__('On', 'civi-framework'),
											'0' => esc_html__('Off', 'civi-framework'),
										),
										'default' => '1'
									),
									array(
										'id' => 'image_employer_applicants',
										'type' => 'image',
										'url' => true,
										'title' => esc_html__('Icon Aplicants', 'civi-framework'),
										'subtitle' => esc_html__('Choose icon for applicants', 'civi-framework'),
										'required' => array('show_employer_applicants', '!=', '0'),
									),
									array(
										'id' => 'show_employer_candidates',
										'type' => 'button_set',
										'title' => esc_html__('Show "Candidates"', 'civi-framework'),
										'subtitle' => esc_html__('Show/Hide "Candidates" on navigation', 'civi-framework'),
										'desc' => '',
										'options' => array(
											'1' => esc_html__('On', 'civi-framework'),
											'0' => esc_html__('Off', 'civi-framework'),
										),
										'default' => '1'
									),
									array(
										'id' => 'image_employer_candidates',
										'type' => 'image',
										'url' => true,
										'title' => esc_html__('Icon Candidates', 'civi-framework'),
										'subtitle' => esc_html__('Choose icon for Candidates', 'civi-framework'),
										'required' => array('show_employer_candidates', '!=', '0'),
									),
									array(
										'id' => 'show_employer_user_package',
										'type' => 'button_set',
										'title' => esc_html__('Show "User Package"', 'civi-framework'),
										'subtitle' => esc_html__('Show/Hide "User Package" on navigation', 'civi-framework'),
										'desc' => '',
										'options' => array(
											'1' => esc_html__('On', 'civi-framework'),
											'0' => esc_html__('Off', 'civi-framework'),
										),
										'default' => '1'
									),
									array(
										'id' => 'image_employer_user_package',
										'type' => 'image',
										'url' => true,
										'title' => esc_html__('Icon User Package', 'civi-framework'),
										'subtitle' => esc_html__('Choose icon for User Package', 'civi-framework'),
										'required' => array('show_employer_user_package', '!=', '0'),
									),
									array(
										'id' => 'show_employer_messages',
										'type' => 'button_set',
										'title' => esc_html__('Show "Messages"', 'civi-framework'),
										'subtitle' => esc_html__('Show/Hide "Messages" on navigation', 'civi-framework'),
										'desc' => '',
										'options' => array(
											'1' => esc_html__('On', 'civi-framework'),
											'0' => esc_html__('Off', 'civi-framework'),
										),
										'default' => '1'
									),
									array(
										'id' => 'image_employer_messages',
										'type' => 'image',
										'url' => true,
										'title' => esc_html__('Icon Messages', 'civi-framework'),
										'subtitle' => esc_html__('Choose icon for messages', 'civi-framework'),
										'required' => array('show_employer_messages', '!=', '0'),
									),
									array(
										'id' => 'show_employer_meetings',
										'type' => 'button_set',
										'title' => esc_html__('Show "Meetings"', 'civi-framework'),
										'subtitle' => esc_html__('Show/Hide "Meetings" on navigation', 'civi-framework'),
										'desc' => '',
										'options' => array(
											'1' => esc_html__('On', 'civi-framework'),
											'0' => esc_html__('Off', 'civi-framework'),
										),
										'default' => '1'
									),
									array(
										'id' => 'image_employer_meetings',
										'type' => 'image',
										'url' => true,
										'title' => esc_html__('Icon Meetings', 'civi-framework'),
										'subtitle' => esc_html__('Choose icon for meetings', 'civi-framework'),
										'required' => array('show_employer_meetings', '!=', '0'),
									),
									array(
										'id' => 'show_employer_company',
										'type' => 'button_set',
										'title' => esc_html__('Show "Company"', 'civi-framework'),
										'subtitle' => esc_html__('Show/Hide "Company" on navigation', 'civi-framework'),
										'desc' => '',
										'options' => array(
											'1' => esc_html__('On', 'civi-framework'),
											'0' => esc_html__('Off', 'civi-framework'),
										),
										'default' => '1'
									),
									array(
										'id' => 'image_employer_company',
										'type' => 'image',
										'url' => true,
										'title' => esc_html__('Icon Company', 'civi-framework'),
										'subtitle' => esc_html__('Choose icon for company', 'civi-framework'),
										'required' => array('show_employer_company', '!=', '0'),
									),
									array(
										'id' => 'show_employer_teams',
										'type' => 'button_set',
										'title' => esc_html__('Show "Teams"', 'civi-framework'),
										'subtitle' => esc_html__('Show/Hide "Teams" on navigation', 'civi-framework'),
										'desc' => '',
										'options' => array(
											'1' => esc_html__('On', 'civi-framework'),
											'0' => esc_html__('Off', 'civi-framework'),
										),
										'default' => '1'
									),
									array(
										'id' => 'image_employer_teams',
										'type' => 'image',
										'url' => true,
										'title' => esc_html__('Icon Teams', 'civi-framework'),
										'subtitle' => esc_html__('Choose icon for teams', 'civi-framework'),
										'required' => array('show_employer_teams', '!=', '0'),
									),
									array(
										'id' => 'show_employer_settings',
										'type' => 'button_set',
										'title' => esc_html__('Show "Settings"', 'civi-framework'),
										'subtitle' => esc_html__('Show/Hide "Settings" on navigation', 'civi-framework'),
										'desc' => '',
										'options' => array(
											'1' => esc_html__('On', 'civi-framework'),
											'0' => esc_html__('Off', 'civi-framework'),
										),
										'default' => '1'
									),
									array(
										'id' => 'image_employer_settings',
										'type' => 'image',
										'url' => true,
										'title' => esc_html__('Icon Settings', 'civi-framework'),
										'subtitle' => esc_html__('Choose icon for settings', 'civi-framework'),
										'required' => array('show_employer_settings', '!=', '0'),
									),
									array(
										'id' => 'show_employer_logout',
										'type' => 'button_set',
										'title' => esc_html__('Show "Logout"', 'civi-framework'),
										'subtitle' => esc_html__('Show/Hide "Logout" on navigation', 'civi-framework'),
										'desc' => '',
										'options' => array(
											'1' => esc_html__('On', 'civi-framework'),
											'0' => esc_html__('Off', 'civi-framework'),
										),
										'default' => '1'
									),
									array(
										'id' => 'image_employer_logout',
										'type' => 'image',
										'url' => true,
										'title' => esc_html__('Icon Logout', 'civi-framework'),
										'subtitle' => esc_html__('Choose icon for logout', 'civi-framework'),
										'required' => array('show_employer_logout', '!=', '0'),
									)
								),
							),
						),
					),
					apply_filters('civi_register_user_candidate_option_top', array()),
					array(
						apply_filters(
							'civi_register_user_candidate_option_main',
							array(
								'id' => 'civi_user_option_candidate',
								'type' => 'group',
								'title' => esc_html__('Candidate Setting', 'civi-framework'),
								'fields' => array(
									array(
										'id' => 'type_icon_candidate',
										'type' => 'select',
										'title' => esc_html__('Icon Type', 'civi-framework'),
										'default' => 'svg',
										'options' => array(
											'image' => esc_html__('Image', 'civi-framework'),
											'svg' => esc_html__('Svg', 'civi-framework')
										),
									),
									array(
										'id' => 'show_candidate_dashboard',
										'type' => 'button_set',
										'title' => esc_html__('Show "Dashboard"', 'civi-framework'),
										'subtitle' => esc_html__('Show/Hide "Dashboard" on navigation', 'civi-framework'),
										'desc' => '',
										'options' => array(
											'1' => esc_html__('On', 'civi-framework'),
											'0' => esc_html__('Off', 'civi-framework'),
										),
										'default' => '1'
									),
									array(
										'id' => 'image_candidate_dashboard',
										'type' => 'image',
										'url' => true,
										'title' => esc_html__('Icon Dashboard', 'civi-framework'),
										'subtitle' => esc_html__('Choose icon for dashboard', 'civi-framework'),
										'required' => array('show_candidate_dashboard', '!=', '0'),
									),
									array(
										'id' => 'show_candidate_profile',
										'type' => 'button_set',
										'title' => esc_html__('Show "Profile"', 'civi-framework'),
										'subtitle' => esc_html__('Show/Hide "Profile" on navigation', 'civi-framework'),
										'desc' => '',
										'options' => array(
											'1' => esc_html__('On', 'civi-framework'),
											'0' => esc_html__('Off', 'civi-framework'),
										),
										'default' => '1'
									),
									array(
										'id' => 'image_candidate_profile',
										'type' => 'image',
										'url' => true,
										'title' => esc_html__('Icon Profile', 'civi-framework'),
										'subtitle' => esc_html__('Choose icon for profile', 'civi-framework'),
										'required' => array('show_candidate_profile', '!=', '0'),
									),
									array(
										'id' => 'show_my_jobs',
										'type' => 'button_set',
										'title' => esc_html__('Show "My Jobs"', 'civi-framework'),
										'subtitle' => esc_html__('Show/Hide "My Jobs" on navigation', 'civi-framework'),
										'desc' => '',
										'options' => array(
											'1' => esc_html__('On', 'civi-framework'),
											'0' => esc_html__('Off', 'civi-framework'),
										),
										'default' => '1'
									),
									array(
										'id' => 'image_my_jobs',
										'type' => 'image',
										'url' => true,
										'title' => esc_html__('Icon My Jobs', 'civi-framework'),
										'subtitle' => esc_html__('Choose icon for My Jobs', 'civi-framework'),
										'required' => array('show_my_jobs', '!=', '0'),
									),
									array(
										'id' => 'show_candidate_reviews',
										'type' => 'button_set',
										'title' => esc_html__('Show "My Reviews"', 'civi-framework'),
										'subtitle' => esc_html__('Show/Hide "My Reviews" on navigation', 'civi-framework'),
										'desc' => '',
										'options' => array(
											'1' => esc_html__('On', 'civi-framework'),
											'0' => esc_html__('Off', 'civi-framework'),
										),
										'default' => '1'
									),
									array(
										'id' => 'image_candidate_reviews',
										'type' => 'image',
										'url' => true,
										'title' => esc_html__('Icon My Reviews', 'civi-framework'),
										'subtitle' => esc_html__('Choose icon for My Reviews', 'civi-framework'),
										'required' => array('show_candidate_reviews', '!=', '0'),
									),
									array(
										'id' => 'show_candidate_company',
										'type' => 'button_set',
										'title' => esc_html__('Show "My Following"', 'civi-framework'),
										'subtitle' => esc_html__('Show/Hide "My Following" on navigation', 'civi-framework'),
										'desc' => '',
										'options' => array(
											'1' => esc_html__('On', 'civi-framework'),
											'0' => esc_html__('Off', 'civi-framework'),
										),
										'default' => '1'
									),
									array(
										'id' => 'image_candidate_company',
										'type' => 'image',
										'url' => true,
										'title' => esc_html__('Icon My Following', 'civi-framework'),
										'subtitle' => esc_html__('Choose icon for My Following', 'civi-framework'),
										'required' => array('show_candidate_company', '!=', '0'),
									),
									array(
										'id' => 'show_candidate_messages',
										'type' => 'button_set',
										'title' => esc_html__('Show "Messages"', 'civi-framework'),
										'subtitle' => esc_html__('Show/Hide "Messages" on navigation', 'civi-framework'),
										'desc' => '',
										'options' => array(
											'1' => esc_html__('On', 'civi-framework'),
											'0' => esc_html__('Off', 'civi-framework'),
										),
										'default' => '1'
									),
									array(
										'id' => 'image_candidate_messages',
										'type' => 'image',
										'url' => true,
										'title' => esc_html__('Icon Messages', 'civi-framework'),
										'subtitle' => esc_html__('Choose icon for messages', 'civi-framework'),
										'required' => array('show_candidate_messages', '!=', '0'),
									),
									array(
										'id' => 'show_candidate_meetings',
										'type' => 'button_set',
										'title' => esc_html__('Show "Meetings"', 'civi-framework'),
										'subtitle' => esc_html__('Show/Hide "Meetings" on navigation', 'civi-framework'),
										'desc' => '',
										'options' => array(
											'1' => esc_html__('On', 'civi-framework'),
											'0' => esc_html__('Off', 'civi-framework'),
										),
										'default' => '1'
									),
									array(
										'id' => 'image_candidate_meetings',
										'type' => 'image',
										'url' => true,
										'title' => esc_html__('Icon Meetings', 'civi-framework'),
										'subtitle' => esc_html__('Choose icon for meetings', 'civi-framework'),
										'required' => array('show_candidate_meetings', '!=', '0'),
									),
									array(
										'id' => 'show_candidate_settings',
										'type' => 'button_set',
										'title' => esc_html__('Show "Settings"', 'civi-framework'),
										'subtitle' => esc_html__('Show/Hide "Settings" on navigation', 'civi-framework'),
										'desc' => '',
										'options' => array(
											'1' => esc_html__('On', 'civi-framework'),
											'0' => esc_html__('Off', 'civi-framework'),
										),
										'default' => '1'
									),
									array(
										'id' => 'image_candidate_settings',
										'type' => 'image',
										'url' => true,
										'title' => esc_html__('Icon Settings', 'civi-framework'),
										'subtitle' => esc_html__('Choose icon for settings', 'civi-framework'),
										'required' => array('show_candidate_settings', '!=', '0'),
									),
									/* Membership */
									array(
										'id' => 'show_candidate_membership',
										'type' => 'button_set',
										'title' => esc_html__('Show "Membership"', 'civi-framework'),
										'subtitle' => esc_html__('Show/Hide "Membership" on navigation', 'civi-framework'),
										'desc' => '',
										'options' => array(
											'1' => esc_html__('On', 'civi-framework'),
											'0' => esc_html__('Off', 'civi-framework'),
										),
										'default' => '1'
									),
									array(
										'id' => 'image_candidate_membership',
										'type' => 'image',
										'url' => true,
										'title' => esc_html__('Icon Membership', 'civi-framework'),
										'subtitle' => esc_html__('Choose icon for Membership', 'civi-framework'),
										'required' => array('show_candidate_membership', '!=', '0'),
									),
									/* **************************** */
									array(
										'id' => 'show_candidate_logout',
										'type' => 'button_set',
										'title' => esc_html__('Show "Logout"', 'civi-framework'),
										'subtitle' => esc_html__('Show/Hide "Logout" on navigation', 'civi-framework'),
										'desc' => '',
										'options' => array(
											'1' => esc_html__('On', 'civi-framework'),
											'0' => esc_html__('Off', 'civi-framework'),
										),
										'default' => '1'
									),
									array(
										'id' => 'image_candidate_logout',
										'type' => 'image',
										'url' => true,
										'title' => esc_html__('Icon Logout', 'civi-framework'),
										'subtitle' => esc_html__('Choose icon for logout', 'civi-framework'),
										'required' => array('show_candidate_logout', '!=', '0'),
									)
								),
							),
						),
					),
					apply_filters('civi_register_user_option_bottom', array())
				)
			));
		}

		/**
		 * Jobs page option
		 * @return mixed
		 */
		private function jobs_option()
		{
			return
				apply_filters('civi_register_option_listing_setting_page', array(
					'id' => 'civi_listing_setting_page_option',
					'title' => esc_html__('Jobs Setting', 'civi-framework'),
					'icon' => 'dashicons-list-view',
					'fields' => array(
						//General Jobs
						apply_filters('civi_register_option_genera_jobs_page_top', array()),
						apply_filters('civi_register_option_genera_jobs_page_main', array(
							'id' => 'civi_main_group',
							'type' => 'group',
							'title' => esc_html__('General Jobs', 'civi-framework'),
							'fields' => array(
								array(
									'id' => 'enable_apply_login',
									'type' => 'button_set',
									'title' => esc_html__('Enable Apply Job Login', 'civi-framework'),
									'subtitle' => esc_html__('Only works in apply (gmail,phone,external)', 'civi-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'civi-framework'),
										'0' => esc_html__('Off', 'civi-framework'),
									),
									'default' => '0'
								),
								array(
									'id' => "enable_job_alerts",
									'type' => 'button_set',
									'title' => esc_html__('Enable Job Alerts', 'civi-framework'),
									'subtitle' => esc_html__('Enable/Disable Job Alerts', 'civi-framework'),
									'options' => array(
										'1' => esc_html__('On', 'civi-framework'),
										'0' => esc_html__('Off', 'civi-framework'),
									),
									'default' => '0',
								),
								array(
									'id' => 'civi_job_alerts_page_id',
									'title' => esc_html__('Job Alerts', 'civi-framework'),
									'type' => 'select',
									'data' => 'page',
									'data_args' => array(
										'numberposts' => -1,
									),
									'subtitle' => esc_html__('Select page for job alerts', 'civi-framework'),
									'required' => array("enable_job_alerts", '=', '1'),
								),
								array(
									'id' => "enable_status_urgent",
									'type' => 'button_set',
									'title' => esc_html__('Enable Status Urgent', 'civi-framework'),
									'subtitle' => esc_html__('Enable/Disable Status Urgent', 'civi-framework'),
									'options' => array(
										'1' => esc_html__('On', 'civi-framework'),
										'0' => esc_html__('Off', 'civi-framework'),
									),
									'default' => '1',
								),
								array(
									'id' => "number_status_urgent",
									'title' => esc_html__('Number Status Urgent', 'civi-framework'),
									'subtitle' => esc_html__('Enter number of days status urgent', 'civi-framework'),
									'default' => '3',
									'type' => 'text',
									'required' => array("enable_status_urgent", '=', '1'),
								),
								array(
									'id' => "jobs_number_days",
									'title' => esc_html__('Number of days to apply', 'civi-framework'),
									'subtitle' => esc_html__('Enter number of days to apply', 'civi-framework'),
									'default' => '30',
									'type' => 'text',
								),
							),
						)),
						apply_filters('civi_register_option_genera_jobs_page_bottom', array()),

						//Archive Jobs
						apply_filters('civi_register_option_archive_jobs_page_top', array()),
						apply_filters('civi_register_option_archive_jobs_page_main', array(
							'id' => 'civi_main_group',
							'type' => 'group',
							'title' => esc_html__('Archive Jobs', 'civi-framework'),
							'fields' => array(
								array(
									'id' => 'archive_jobs_layout',
									'type' => 'select',
									'title' => esc_html__('Jobs Layout', 'civi-framework'),
									'default' => 'layout-list',
									'options' => array(
										'layout-list' => esc_html__('Layout List', 'civi-framework'),
										'layout-grid' => esc_html__('Layout Grid', 'civi-framework'),
										'layout-full' => esc_html__('Layout Full', 'civi-framework')
									)
								),
								array(
									'id' => 'archive_jobs_items_amount',
									'type' => 'text',
									'title' => esc_html__('Items Amount', 'civi-framework'),
									'default' => 12,
									'pattern' => '[0-9]*',
								),
								array(
									'id' => 'jobs_pagination_type',
									'type' => 'select',
									'title' => esc_html__('Type Pagination', 'civi-framework'),
									'default' => 'number',
									'options' => array(
										'number' => esc_html__('Number', 'civi-framework'),
										'loadmore' => esc_html__('Load More', 'civi-framework'),
//                                        'loadpage' => esc_html__('Load Page', 'civi-framework')
									)
								),
								array(
									'id' => "jobs_filter_sidebar_option",
									'title' => esc_html__('Postion Filter ', 'civi-framework'),
									'type' => 'select',
									'options' => array(
										'filter-left' => 'Filter Left',
										'filter-right' => 'Filter Right',
										'filter-canvas' => 'Filter Canvas',
									),
									'default' => 'left',
									'required' => array(
										array("enable_jobs_show_map", '=', '0'),
										array("archive_jobs_layout", '!=', 'layout-full'),
									),
								),

								array(
									'id' => 'enable_jobs_filter_top',
									'type' => 'button_set',
									'title' => esc_html__('Show Top Filter', 'civi-framework'),
									'subtitle' => esc_html__('Show/Hidden Top Filter', 'civi-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'civi-framework'),
										'0' => esc_html__('Off', 'civi-framework'),
									),
									'default' => '1',
								),
								array(
									'id' => 'enable_jobs_show_map',
									'type' => 'button_set',
									'title' => esc_html__('Show Maps', 'civi-framework'),
									'subtitle' => esc_html__('Show/Hidden Maps', 'civi-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'civi-framework'),
										'0' => esc_html__('Off', 'civi-framework'),
									),
									'default' => '0',
									'required' => array(
										array("archive_jobs_layout", '!=', 'layout-full'),
									),
								),
								array(
									'id' => "jobs_map_postion",
									'title' => esc_html__('Postion Maps ', 'civi-framework'),
									'type' => 'select',
									'options' => array(
										'map-right' => 'Map Right',
										'map-top' => 'Map Top',
									),
									'default' => 'right',
									'required' => array(
										array("enable_jobs_show_map", '=', '1'),
										array("archive_jobs_layout", '!=', 'layout-full'),
									),
								),
								array(
									'id' => 'enable_jobs_show_des',
									'type' => 'button_set',
									'title' => esc_html__('Show Description', 'civi-framework'),
									'subtitle' => esc_html__('Show/Hidden Description', 'civi-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'civi-framework'),
										'0' => esc_html__('Off', 'civi-framework'),
									),
									'default' => '0',
									'required' => array(
										array("archive_jobs_layout", '!=', 'layout-full'),
									),
								),
							),
						)),
						apply_filters('civi_register_option_archive_jobs_page_bottom', array()),

						//Single Jobs
						apply_filters('civi_register_option_single_jobs_page_top', array()),
						apply_filters('civi_register_option_single_jobs_page_main', array(
							'id' => 'jobs_page_main_group',
							'type' => 'group',
							'title' => esc_html__('Single Jobs', 'civi-framework'),
							'fields' => array(
								array(
									'id' => "enable_job_login_to_view",
									'type' => 'button_set',
									'title' => esc_html__('Enable Job Login To View', 'civi-framework'),
									'subtitle' => esc_html__('Enable/Disable Job Login To View', 'civi-framework'),
									'options' => array(
										'1' => esc_html__('On', 'civi-framework'),
										'0' => esc_html__('Off', 'civi-framework'),
									),
									'default' => '0',
								),
								array(
									'id' => 'enable_sticky_sidebar_type',
									'type' => 'button_set',
									'title' => esc_html__('Enable Sticky Sidebar', 'civi-framework'),
									'subtitle' => esc_html__('Enable/Disable sticky sidebar when scroll', 'civi-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'civi-framework'),
										'0' => esc_html__('Off', 'civi-framework'),
									),
									'default' => '1',
								),
								array(
									'id' => 'enable_single_jobs_salary',
									'type' => 'button_set',
									'title' => esc_html__('Enable Jobs Salary', 'civi-framework'),
									'subtitle' => esc_html__('Enable/Disable Jobs Salary', 'civi-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'civi-framework'),
										'0' => esc_html__('Off', 'civi-framework'),
									),
									'default' => '1',
								),
								array(
									'id' => 'enable_single_jobs_related',
									'type' => 'button_set',
									'title' => esc_html__('Enable Jobs Related', 'civi-framework'),
									'subtitle' => esc_html__('Enable/Disable Jobs Related', 'civi-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'civi-framework'),
										'0' => esc_html__('Off', 'civi-framework'),
									),
									'default' => '1',
								),

								array(
									'id' => 'enable_single_jobs_apply',
									'type' => 'button_set',
									'title' => esc_html__('Enable Jobs Apply', 'civi-framework'),
									'subtitle' => esc_html__('Enable/Disable Apply', 'civi-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'civi-framework'),
										'0' => esc_html__('Off', 'civi-framework'),
									),
									'default' => '1',
								),

								array(
									'id' => 'enable_toggle_show_more_apply',
									'type' => 'button_set',
									'title' => esc_html__('Enable Show More Insights', 'civi-framework'),
									'subtitle' => esc_html__('Enable/Disable Show More Insights(Below the mobile device)', 'civi-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'civi-framework'),
										'0' => esc_html__('Off', 'civi-framework'),
									),
									'default' => '1',
								),

								array(
									'id' => "enable_date_posted",
									'type' => 'button_set',
									'title' => esc_html__('Enable Date Posted', 'civi-framework'),
									'subtitle' => esc_html__('Enable/Disable Date Posted', 'civi-framework'),
									'options' => array(
										'1' => esc_html__('On', 'civi-framework'),
										'0' => esc_html__('Off', 'civi-framework'),
									),
									'default' => '1',
								),
								array(
									'id' => "enable_closing_date",
									'type' => 'button_set',
									'title' => esc_html__('Enable Closing Date', 'civi-framework'),
									'subtitle' => esc_html__('Enable/Disable Closing Date', 'civi-framework'),
									'options' => array(
										'1' => esc_html__('On', 'civi-framework'),
										'0' => esc_html__('Off', 'civi-framework'),
									),
									'default' => '1',
								),

								array(
									'id' => 'single_jobs_image_size',
									'type' => 'text',
									'title' => esc_html__('Image Size', 'civi-framework'),
									'subtitle' => esc_html__('Enter image size. Alternatively enter size in pixels (Example : 770x250 (Not Include Unit, Space))', 'civi-framework'),
									'default' => '770x250',
								),

								array(
									'id' => 'jobs_details_order',
									'type' => 'sortable',
									'title' => esc_html__('Jobs Content Order', 'civi-framework'),
									'desc' => esc_html__('Drag and drop layout manager, to quickly organize your jobs content details.', 'civi-framework'),
									'options' => array(
										'enable_sp_head' => esc_html__('Head', 'civi-framework'),
										'enable_sp_insights' => esc_html__('Insights', 'civi-framework'),
										'enable_sp_description' => esc_html__('Description', 'civi-framework'),
										'enable_sp_skills' => esc_html__('Skills', 'civi-framework'),
										'enable_sp_gallery' => esc_html__('Gallery', 'civi-framework'),
										'enable_sp_video' => esc_html__('Video', 'civi-framework'),
										'enable_sp_map' => esc_html__('Map', 'civi-framework'),
									),
									'default' => array('enable_sp_skills', 'enable_sp_head', 'enable_sp_description', 'enable_sp_video', 'enable_sp_map', 'enable_sp_insights')
								),
								array(
									'id' => 'jobs_details_sidebar_order',
									'type' => 'sortable',
									'title' => esc_html__('Jobs Sidebar Order', 'civi-framework'),
									'desc' => esc_html__('Drag and drop layout manager, to quickly organize your jobs sidebar order.', 'civi-framework'),
									'options' => array(
										'enable_sidebar_sp_apply' => esc_html__('Apply', 'civi-framework'),
										'enable_sidebar_sp_insights' => esc_html__('Insights', 'civi-framework'),
										'enable_sidebar_sp_company' => esc_html__('Company', 'civi-framework'),
									),
									'default' => array('enable_sidebar_sp_apply', 'enable_sidebar_sp_insights', 'enable_sidebar_sp_company')
								),

								array(
									'id' => 'social_sharing',
									'type' => 'checkbox_list',
									'title' => esc_html__('Show Social Sharing', 'civi-framework'),
									'subtitle' => esc_html__('Choose which fields you want to show on social sharing?', 'civi-framework'),
									'options' => array(
										'facebook' => esc_html__('Facebook', 'civi-framework'),
										'twitter' => esc_html__('Twitter', 'civi-framework'),
										'linkedin' => esc_html__('Linkedin', 'civi-framework'),
										'tumblr' => esc_html__('Tumblr', 'civi-framework'),
										'pinterest' => esc_html__('Pinterest', 'civi-framework'),
										'whatapp' => esc_html__('Whatapp', 'civi-framework'),
									),
									'value_inline' => false,
									'default' => array('facebook', 'twitter', 'linkedin', 'tumblr', 'pinterest', 'whatapp')
								),
							),
						)),
						apply_filters('civi_register_option_single_jobs_page_bottom', array()),

						//Jobs Submit
						apply_filters('civi_jobs_option_jobs_submit_top', array()),
						apply_filters('civi_jobs_option_jobs_submit_main', array(
							'id' => 'jobs_submit_group',
							'title' => esc_html__('Jobs Submit', 'civi-framework'),
							'type' => 'group',
							'fields' => array(
								array(
									'id' => 'auto_publish',
									'title' => esc_html__('Automatically publish the submitted jobs?', 'civi-framework'),
									'type' => 'button_set',
									'options' => array(
										'1' => esc_html__('Yes', 'civi-framework'),
										'0' => esc_html__('No', 'civi-framework'),
									),
									'default' => '1',
								),

								array(
									'id' => 'auto_publish_edited',
									'title' => esc_html__('Automatically publish the edited jobs?', 'civi-framework'),
									'type' => 'button_set',
									'options' => array(
										'1' => esc_html__('Yes', 'civi-framework'),
										'0' => esc_html__('No', 'civi-framework'),
									),
									'default' => '1',
								),
								array(
									'id' => 'section_jobs_hide_group_fields',
									'title' => esc_html__('Hide Submit Group Form Fields', 'civi-framework'),
									'type' => 'group',

									'fields' => array(
										array(
											'id' => 'hide_jobs_group_fields',
											'type' => 'checkbox_list',
											'title' => esc_html__('Hide Submit Form Groups', 'civi-framework'),
											'subtitle' => esc_html__('Choose which fields you want to hide on New Property page?', 'civi-framework'),
											'options' => array(
												'general' => esc_html__('General', 'civi-framework'),
												'salary' => esc_html__('Salary', 'civi-framework'),
												'apply' => esc_html__('Apply', 'civi-framework'),
												'company' => esc_html__('Company', 'civi-framework'),
												'location' => esc_html__('Location', 'civi-framework'),
												'thumbnail' => esc_html__('Cover Image', 'civi-framework'),
												'gallery' => esc_html__('Gallery', 'civi-framework'),
												'video' => esc_html__('Video', 'civi-framework'),
											),
											'value_inline' => false,
											'default' => array()
										),
										array(
											'id' => 'hide_jobs_fields',
											'type' => 'checkbox_list',
											'title' => esc_html__('Hide Submit Form Fields', 'civi-framework'),
											'subtitle' => esc_html__('Choose which fields you want to hide on New Property page?', 'civi-framework'),
											'options' => array(
												'fields_jobs_name' => esc_html__('Name', 'civi-framework'),
												'fields_jobs_category' => esc_html__('Category', 'civi-framework'),
												'fields_jobs_type' => esc_html__('Type', 'civi-framework'),
												'fields_jobs_skills' => esc_html__('Skills', 'civi-framework'),
												'fields_jobs_des' => esc_html__('Description', 'civi-framework'),
												'fields_jobs_career' => esc_html__('Career', 'civi-framework'),
												'fields_jobs_experience' => esc_html__('Experience', 'civi-framework'),
												'fields_jobs_qualification' => esc_html__('Qualification', 'civi-framework'),
												'fields_jobs_quantity' => esc_html__('Quantity', 'civi-framework'),
												'fields_jobs_gender' => esc_html__('Gender', 'civi-framework'),
												'fields_closing_days' => esc_html__('Closing', 'civi-framework'),
												'fields_jobs_location' => esc_html__('Location', 'civi-framework'),
												'fields_map' => esc_html__('Maps', 'civi-framework'),
											),
											'value_inline' => false,
											'default' => array()
										),
									)
								),
							)
						)),
						apply_filters('civi_jobs_option_jobs_submit_bottom', array()),

						//Jobs Search
						apply_filters('civi_register_option_search_page_top', array()),
						apply_filters('civi_register_option_search_page_main', array(
							'id' => 'jobs_search_group',
							'title' => esc_html__('Search Jobs', 'civi-framework'),
							'type' => 'group',
							'fields' => array(
								array(
									'id' => 'enable_jobs_search_bg',
									'type' => 'button_set',
									'title' => esc_html__('Enable Background', 'civi-framework'),
									'subtitle' => esc_html__('Show/Hidden Background', 'civi-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'civi-framework'),
										'0' => esc_html__('Off', 'civi-framework'),
									),
									'default' => '0',
								),
								array(
									'id' => "jobs_search_color",
									'title' => esc_html__('Color', 'civi-framework'),
									'type' => 'color',
									'col' => '12',
									'default' => '',
									'required' => array(
										array("enable_jobs_search_bg", '=', '1'),
									),
								),
								array(
									'id' => "jobs_search_image",
									'title' => esc_html__('Image', 'civi-framework'),
									'type' => 'image',
									'default' => '',
									'col' => '12',
									'required' => array(
										array("enable_jobs_search_bg", '=', '1'),
									),
								),
								array(
									'id' => 'jobs_search_fields',
									'title' => esc_html__('Search Fields', 'civi-framework'),
									'type' => 'sorter',
									'default' => array(
										'top' => array(
											'jobs-location' => esc_html__('Location', 'civi-framework'),
											'jobs-categories' => esc_html__('Categories', 'civi-framework'),
										),
										'sidebar' => array(
											'jobs-type' => esc_html__('Type', 'civi-framework'),
											'jobs-salary' => esc_html__('Salary', 'civi-framework'),
											'jobs-career' => esc_html__('Career', 'civi-framework'),
											'jobs-experience' => esc_html__('Experience', 'civi-framework'),
										),
										'disable' => array(
											'jobs-skills' => esc_html__('Skills', 'civi-framework'),
										)
									),
								),
								array(
									'id' => 'jobs_search_fields_jobs-categories',
									'title' => esc_html__('Icon Categories', 'civi-framework'),
									'type' => 'text',
									'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'civi-framework'),
								),
								array(
									'id' => 'jobs_search_fields_jobs-type',
									'title' => esc_html__('Icon Type', 'civi-framework'),
									'type' => 'text',
									'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'civi-framework'),
								),
								array(
									'id' => 'jobs_search_fields_jobs-career',
									'title' => esc_html__('Icon Career', 'civi-framework'),
									'type' => 'text',
									'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'civi-framework'),
								),
								array(
									'id' => 'jobs_search_fields_jobs-experience',
									'title' => esc_html__('Icon Experience', 'civi-framework'),
									'type' => 'text',
									'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'civi-framework'),
								),
							)
						)),
						apply_filters('civi_register_option_search_page_bottom', array()),
					),
				));
		}


		/**
		 * Company page option
		 * @return mixed
		 */
		private function company_option()
		{
			return
				apply_filters('civi_register_company_option_listing_setting_page', array(
					'id' => 'civi_listing_company_setting_page_option',
					'title' => esc_html__('Company Setting', 'civi-framework'),
					'icon' => 'dashicons-awards',
					'fields' => array(
						//Archive Company
						apply_filters('civi_register_option_archive_company_page_top', array()),
						apply_filters('civi_register_option_archive_company_page_main', array(
							'id' => 'civi_main_group',
							'type' => 'group',
							'title' => esc_html__('Archive Company', 'civi-framework'),
							'fields' => array(
								array(
									'id' => 'archive_company_layout',
									'type' => 'select',
									'title' => esc_html__('Company Layout', 'civi-framework'),
									'default' => 'layout-list',
									'options' => array(
										'layout-list' => esc_html__('Layout List', 'civi-framework'),
										'layout-grid' => esc_html__('Layout Grid', 'civi-framework'),
									)
								),
								array(
									'id' => 'archive_company_thumbnail_size',
									'type' => 'text',
									'title' => esc_html__('Thumbnail Size', 'civi-framework'),
									'subtitle' => esc_html__('Enter image size. Alternatively enter size in pixels (Example : 330x180 (Not Include Unit, Space))', 'civi-framework'),
									'default' => '576x327',
								),
								array(
									'id' => 'archive_company_items_amount',
									'type' => 'text',
									'title' => esc_html__('Items Amount', 'civi-framework'),
									'default' => 12,
									'pattern' => '[0-9]*',
								),
								array(
									'id' => 'company_pagination_type',
									'type' => 'select',
									'title' => esc_html__('Type Pagination', 'civi-framework'),
									'default' => 'number',
									'options' => array(
										'number' => esc_html__('Number', 'civi-framework'),
										'loadmore' => esc_html__('Load More', 'civi-framework')
									)
								),
								array(
									'id' => "company_filter_sidebar_option",
									'title' => esc_html__('Postion Filter ', 'civi-framework'),
									'type' => 'select',
									'options' => array(
										'filter-left' => 'Filter Left',
										'filter-right' => 'Filter Right',
									),
									'default' => 'left',
								),
								array(
									'id' => 'enable_company_filter_top',
									'type' => 'button_set',
									'title' => esc_html__('Show Top Filter', 'civi-framework'),
									'subtitle' => esc_html__('Show/Hidden Top Filter', 'civi-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'civi-framework'),
										'0' => esc_html__('Off', 'civi-framework'),
									),
									'default' => '1',
								),
								array(
									'id' => 'enable_company_show_map',
									'type' => 'button_set',
									'title' => esc_html__('Show Maps', 'civi-framework'),
									'subtitle' => esc_html__('Show/Hidden Maps', 'civi-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'civi-framework'),
										'0' => esc_html__('Off', 'civi-framework'),
									),
									'default' => '0',
								),
								array(
									'id' => "company_map_postion",
									'title' => esc_html__('Postion Maps ', 'civi-framework'),
									'type' => 'select',
									'options' => array(
										'map-right' => 'Map Right',
										'map-top' => 'Map Top',
									),
									'default' => 'right',
									'required' => array(
										array("enable_company_show_map", '=', '1'),
									),
								),
								array(
									'id' => 'enable_company_show_des',
									'type' => 'button_set',
									'title' => esc_html__('Show Description', 'civi-framework'),
									'subtitle' => esc_html__('Show/Hidden Description', 'civi-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'civi-framework'),
										'0' => esc_html__('Off', 'civi-framework'),
									),
									'default' => '1',
								),
							),
						)),
						apply_filters('civi_register_option_archive_company_page_bottom', array()),

						//Single Company
						apply_filters('civi_register_option_single_company_page_top', array()),
						apply_filters('civi_register_option_single_company_page_main', array(
							'id' => 'company_page_main_group',
							'type' => 'group',
							'title' => esc_html__('Single Company', 'civi-framework'),
							'fields' => array(
								array(
									'id' => "enable_company_login_to_view",
									'type' => 'button_set',
									'title' => esc_html__('Enable Company Login To View', 'civi-framework'),
									'subtitle' => esc_html__('Enable/Disable Company Login To View', 'civi-framework'),
									'options' => array(
										'1' => esc_html__('On', 'civi-framework'),
										'0' => esc_html__('Off', 'civi-framework'),
									),
									'default' => '0',
								),
								array(
									'id' => 'enable_sticky_company_sidebar_type',
									'type' => 'button_set',
									'title' => esc_html__('Enable Sticky Sidebar', 'civi-framework'),
									'subtitle' => esc_html__('Enable/Disable sticky sidebar when scroll', 'civi-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'civi-framework'),
										'0' => esc_html__('Off', 'civi-framework'),
									),
									'default' => '1',
								),
								array(
									'id' => 'enable_single_company_related',
									'type' => 'button_set',
									'title' => esc_html__('Enable Company Related', 'civi-framework'),
									'subtitle' => esc_html__('Enable/Disable Company Related', 'civi-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'civi-framework'),
										'0' => esc_html__('Off', 'civi-framework'),
									),
									'default' => '1',
								),

								array(
									'id' => 'enable_single_company_review',
									'type' => 'button_set',
									'title' => esc_html__('Enable Company Review', 'civi-framework'),
									'subtitle' => esc_html__('Enable/Disable Company Review', 'civi-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'civi-framework'),
										'0' => esc_html__('Off', 'civi-framework'),
									),
									'default' => '1',
								),

								array(
									'id' => "single_company_style",
									'title' => esc_html__('Style Thumbnail Images', 'civi-framework'),
									'type' => 'select',
									'options' => array(
										'cover-img' => 'Cover Image',
										'large-cover-img' => 'Large Cover Image',
									),
								),

								array(
									'id' => 'single_company_image_size',
									'type' => 'text',
									'title' => esc_html__('Image Size', 'civi-framework'),
									'subtitle' => esc_html__('Enter image size. Alternatively enter size in pixels (Example : 330x180 (Not Include Unit, Space))', 'civi-framework'),
									'default' => '',
								),

								array(
									'id' => 'company_details_order',
									'type' => 'sortable',
									'title' => esc_html__('Company Content Order', 'civi-framework'),
									'desc' => esc_html__('Drag and drop layout manager, to quickly organize your company content details.', 'civi-framework'),
									'options' => array(
										'enable_sp_head' => esc_html__('Head', 'civi-framework'),
										'enable_sp_overview' => esc_html__('Overview', 'civi-framework'),
										'enable_sp_gallery' => esc_html__('Gallery', 'civi-framework'),
										'enable_sp_video' => esc_html__('Video', 'civi-framework'),
									),
									'default' => array('enable_sp_company_head', 'enable_sp_company_overview', 'enable_sp_company_gallery', 'enable_sp_video')
								),
								array(
									'id' => 'company_details_sidebar_order',
									'type' => 'sortable',
									'title' => esc_html__('Company Sidebar Order', 'civi-framework'),
									'desc' => esc_html__('Drag and drop layout manager, to quickly organize your company sidebar order.', 'civi-framework'),
									'options' => array(
										'enable_sidebar_sp_info' => esc_html__('Information', 'civi-framework'),
										'enable_sidebar_sp_location' => esc_html__('Location', 'civi-framework'),
									),
									'default' => array('enable_sidebar_sp_info', 'enable_sidebar_sp_location'),
								),
							),
						)),
						apply_filters('civi_register_option_single_company_page_bottom', array()),

						//Company Submit
						apply_filters('civi_option_company_submit_top', array()),
						apply_filters('civi_option_company_submit_main', array(
							'id' => 'company_submit_group',
							'title' => esc_html__('Company Submit', 'civi-framework'),
							'type' => 'group',
							'fields' => array(
								array(
									'id' => 'company_auto_publish',
									'title' => esc_html__('Automatically publish the submitted Company?', 'civi-framework'),
									'type' => 'button_set',
									'options' => array(
										'1' => esc_html__('Yes', 'civi-framework'),
										'0' => esc_html__('No', 'civi-framework'),
									),
									'default' => '1',
								),

								array(
									'id' => 'company_auto_publish_edited',
									'title' => esc_html__('Automatically publish the edited Company?', 'civi-framework'),
									'type' => 'button_set',
									'options' => array(
										'1' => esc_html__('Yes', 'civi-framework'),
										'0' => esc_html__('No', 'civi-framework'),
									),
									'default' => '1',
								),
								array(
									'id' => 'value_founded_min',
									'type' => 'text',
									'title' => esc_html__('Founded Date Min', 'civi-framework'),
									'subtitle' => esc_html__('Enter values founded date min', 'civi-framework'),
									'default' => '2010',
								),
								array(
									'id' => 'value_founded_max',
									'type' => 'text',
									'title' => esc_html__('Founded Date Max', 'civi-framework'),
									'subtitle' => esc_html__('Enter values founded date max', 'civi-framework'),
									'default' => '2023',
								),
								array(
									'id' => 'section_company_hide_group_fields',
									'title' => esc_html__('Hide Submit Group Form Fields', 'civi-framework'),
									'type' => 'group',

									'fields' => array(
										array(
											'id' => 'hide_company_group_fields',
											'type' => 'checkbox_list',
											'title' => esc_html__('Hide Submit Form Groups', 'civi-framework'),
											'subtitle' => esc_html__('Choose which fields you want to hide on New Property page?', 'civi-framework'),
											'options' => array(
												'general' => esc_html__('General', 'civi-framework'),
												'media' => esc_html__('Media', 'civi-framework'),
												'social' => esc_html__('Social network', 'civi-framework'),
												'location' => esc_html__('Location', 'civi-framework'),
												'gallery' => esc_html__('Gallery', 'civi-framework'),
												'video' => esc_html__('Video', 'civi-framework'),
											),
											'value_inline' => false,
											'default' => array()
										),
										array(
											'id' => 'hide_company_fields',
											'type' => 'checkbox_list',
											'title' => esc_html__('Hide Submit Form Fields', 'civi-framework'),
											'subtitle' => esc_html__('Choose which fields you want to hide on New Property page?', 'civi-framework'),
											'options' => array(
												'fields_company_name' => esc_html__('Name', 'civi-framework'),
												'fields_company_category' => esc_html__('Category', 'civi-framework'),
												'fields_company_url' => esc_html__('Url', 'civi-framework'),
												'fields_company_about' => esc_html__('About', 'civi-framework'),
												'fields_company_website' => esc_html__('Website', 'civi-framework'),
												'fields_company_phone' => esc_html__('Phone', 'civi-framework'),
												'fields_company_email' => esc_html__('Email', 'civi-framework'),
												'fields_company_founded' => esc_html__('Founded', 'civi-framework'),
												'fields_company_size' => esc_html__('Size', 'civi-framework'),
												'fields_closing_logo' => esc_html__('Logo', 'civi-framework'),
												'fields_company_thumbnail' => esc_html__('Thumbnail', 'civi-framework'),
												'fields_company_location' => esc_html__('Location', 'civi-framework'),
												'fields_company_map' => esc_html__('Maps', 'civi-framework'),
											),
											'value_inline' => false,
											'default' => array()
										),
									)
								),
							)
						)),
						apply_filters('civi_option_company_submit_bottom', array()),

						//Company Search
						apply_filters('civi_register_option_search_company_page_top', array()),
						apply_filters('civi_register_option_search_company_page_main', array(
							'id' => 'company_search_group',
							'title' => esc_html__('Search Company', 'civi-framework'),
							'type' => 'group',
							'fields' => array(
								array(
									'id' => 'enable_company_search_bg',
									'type' => 'button_set',
									'title' => esc_html__('Enable Background', 'civi-framework'),
									'subtitle' => esc_html__('Show/Hidden Background', 'civi-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'civi-framework'),
										'0' => esc_html__('Off', 'civi-framework'),
									),
									'default' => '0',
								),
								array(
									'id' => "company_search_color",
									'title' => esc_html__('Color', 'civi-framework'),
									'type' => 'color',
									'col' => '12',
									'default' => '',
									'required' => array(
										array("enable_company_search_bg", '=', '1'),
									),
								),
								array(
									'id' => "company_search_image",
									'title' => esc_html__('Image', 'civi-framework'),
									'type' => 'image',
									'default' => '',
									'col' => '12',
									'required' => array(
										array("enable_company_search_bg", '=', '1'),
									),
								),
								array(
									'id' => 'company_search_fields',
									'title' => esc_html__('Search Fields', 'civi-framework'),
									'type' => 'sorter',
									'default' => array(
										'top' => array(
											'company-location' => esc_html__('Location', 'civi-framework'),
											'company-categories' => esc_html__('Categories', 'civi-framework'),
										),
										'sidebar' => array(
											'company-rating' => esc_html__('Rating', 'civi-framework'),
											'company-founded' => esc_html__('Founded', 'civi-framework'),
											'company-size' => esc_html__('Size', 'civi-framework'),
										),
										'disable' => array()
									),
								),
								array(
									'id' => 'company_search_fields_company-categories',
									'title' => esc_html__('Icon Categories', 'civi-framework'),
									'type' => 'text',
									'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'civi-framework'),
								),
								array(
									'id' => 'company_search_fields_company-rating',
									'title' => esc_html__('Icon Rating', 'civi-framework'),
									'type' => 'text',
									'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'civi-framework'),
								),
								array(
									'id' => 'company_search_fields_company-founded',
									'title' => esc_html__('Icon Founded', 'civi-framework'),
									'type' => 'text',
									'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'civi-framework'),
								),
								array(
									'id' => 'company_search_fields_company-size',
									'title' => esc_html__('Icon Size', 'civi-framework'),
									'type' => 'text',
									'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'civi-framework'),
								),
							)
						)),
						apply_filters('civi_register_option_search_company_page_bottom', array())
					),
				));
		}

		/**
		 * Candidate page option
		 * @return mixed
		 */
		private function candidate_option()
		{
			return
				apply_filters('civi_register_candidate_option_listing_setting_page', array(
					'id' => 'civi_listing_candidate_setting_page_option',
					'title' => esc_html__('Candidate Setting', 'civi-framework'),
					'icon' => 'dashicons-businessperson',
					'fields' => array(
						//Archive Candidate
						apply_filters('civi_register_option_archive_candidate_page_top', array()),
						apply_filters('civi_register_option_archive_candidate_page_main', array(
							'id' => 'civi_main_group',
							'type' => 'group',
							'title' => esc_html__('Archive Candidate', 'civi-framework'),
							'fields' => array(
								array(
									'id' => 'archive_candidate_layout',
									'type' => 'select',
									'title' => esc_html__('Candidate Layout', 'civi-framework'),
									'default' => 'layout-list',
									'options' => array(
										'layout-list' => esc_html__('Layout List', 'civi-framework'),
										'layout-grid' => esc_html__('Layout Grid', 'civi-framework')
									)
								),
								array(
									'id' => 'archive_candidate_items_amount',
									'type' => 'text',
									'title' => esc_html__('Items Amount', 'civi-framework'),
									'default' => 12,
									'pattern' => '[0-9]*',
								),
								array(
									'id' => 'candidate_pagination_type',
									'type' => 'select',
									'title' => esc_html__('Type Pagination', 'civi-framework'),
									'default' => 'number',
									'options' => array(
										'number' => esc_html__('Number', 'civi-framework'),
										'loadmore' => esc_html__('Load More', 'civi-framework')
									)
								),
								array(
									'id' => "candidate_filter_sidebar_option",
									'title' => esc_html__('Postion Filter ', 'civi-framework'),
									'type' => 'select',
									'options' => array(
										'filter-left' => 'Filter Left',
										'filter-right' => 'Filter Right',
									),
									'default' => 'left',
								),
								array(
									'id' => 'enable_candidate_filter_top',
									'type' => 'button_set',
									'title' => esc_html__('Show Top Filter', 'civi-framework'),
									'subtitle' => esc_html__('Show/Hidden Top Filter', 'civi-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'civi-framework'),
										'0' => esc_html__('Off', 'civi-framework'),
									),
									'default' => '1',
								),
								array(
									'id' => 'enable_candidate_show_map',
									'type' => 'button_set',
									'title' => esc_html__('Show Maps', 'civi-framework'),
									'subtitle' => esc_html__('Show/Hidden Maps', 'civi-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'civi-framework'),
										'0' => esc_html__('Off', 'civi-framework'),
									),
									'default' => '0',
								),
								array(
									'id' => "candidate_map_postion",
									'title' => esc_html__('Postion Maps ', 'civi-framework'),
									'type' => 'select',
									'options' => array(
										'map-right' => 'Map Right',
										'map-top' => 'Map Top',
									),
									'default' => 'right',
									'required' => array(
										array("enable_candidate_show_map", '=', '1'),
									),
								),
								array(
									'id' => 'enable_candidate_show_des',
									'type' => 'button_set',
									'title' => esc_html__('Show Description', 'civi-framework'),
									'subtitle' => esc_html__('Show/Hidden Description', 'civi-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'civi-framework'),
										'0' => esc_html__('Off', 'civi-framework'),
									),
									'default' => '1',
								),
							),
						)),
						apply_filters('civi_register_option_archive_candidate_page_bottom', array()),

						//Single Candidate
						apply_filters('civi_register_option_single_candidate_page_top', array()),
						apply_filters('civi_register_option_single_candidate_page_main', array(
							'id' => 'candidate_page_main_group',
							'type' => 'group',
							'title' => esc_html__('Single Candidate', 'civi-framework'),
							'fields' => array(
								array(
									'id' => "enable_candidate_login_to_view",
									'type' => 'button_set',
									'title' => esc_html__('Enable Candidate Login To View', 'civi-framework'),
									'subtitle' => esc_html__('Enable/Disable Candidate Login To View', 'civi-framework'),
									'options' => array(
										'1' => esc_html__('On', 'civi-framework'),
										'0' => esc_html__('Off', 'civi-framework'),
									),
									'default' => '0',
								),
								array(
									'id' => 'enable_sticky_candidate_sidebar_type',
									'type' => 'button_set',
									'title' => esc_html__('Enable Sticky Sidebar', 'civi-framework'),
									'subtitle' => esc_html__('Enable/Disable sticky sidebar when scroll', 'civi-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'civi-framework'),
										'0' => esc_html__('Off', 'civi-framework'),
									),
									'default' => '1',
								),
								array(
									'id' => 'enable_single_candidate_related',
									'type' => 'button_set',
									'title' => esc_html__('Enable Candidate Related', 'civi-framework'),
									'subtitle' => esc_html__('Enable/Disable Candidate Related', 'civi-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'civi-framework'),
										'0' => esc_html__('Off', 'civi-framework'),
									),
									'default' => '1',
								),

								array(
									'id' => 'enable_single_candidate_review',
									'type' => 'button_set',
									'title' => esc_html__('Enable Candidate Review', 'civi-framework'),
									'subtitle' => esc_html__('Enable/Disable Candidate Review', 'civi-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'civi-framework'),
										'0' => esc_html__('Off', 'civi-framework'),
									),
									'default' => '1',
								),

								array(
									'id' => "single_candidate_style",
									'title' => esc_html__('Style Thumbnail Images', 'civi-framework'),
									'type' => 'select',
									'options' => array(
										'cover-img' => 'Cover Image',
										'large-cover-img' => 'Large Cover Image',
									),
								),

								array(
									'id' => 'single_candidate_image_size',
									'type' => 'text',
									'title' => esc_html__('Image Size', 'civi-framework'),
									'subtitle' => esc_html__('Enter image size. Alternatively enter size in pixels (Example : 330x180 (Not Include Unit, Space))', 'civi-framework'),
									'default' => '',
								),

								array(
									'id' => 'candidate_details_order',
									'type' => 'sortable',
									'title' => esc_html__('Candidate Content Order', 'civi-framework'),
									'desc' => esc_html__('Drag and drop layout manager, to quickly organize your candidate content details.', 'civi-framework'),
									'options' => array(
										'enable_sp_head' => esc_html__('Head', 'civi-framework'),
										'enable_sp_photos' => esc_html__('Photos', 'civi-framework'),
										'enable_sp_about_me' => esc_html__('About Me', 'civi-framework'),
										'enable_sp_video' => esc_html__('Video', 'civi-framework'),
										'enable_sp_audio' => esc_html__('Audio', 'civi-framework'),
										'enable_sp_skills' => esc_html__('Skills', 'civi-framework'),
										'enable_sp_paymentinfo' => esc_html__('Payment Information', 'civi-framework'),
										'enable_sp_experience' => esc_html__('Experience', 'civi-framework'),
										'enable_sp_education' => esc_html__('Education', 'civi-framework'),
										'enable_sp_projects' => esc_html__('Projects', 'civi-framework'),
										'enable_sp_awards' => esc_html__('Awards', 'civi-framework'),
									),
									'default' => array('enable_sp_head', 'enable_sp_photos', 'enable_sp_about_me', 'enable_sp_skills',  'enable_sp_experience', 'enable_sp_education', 'enable_sp_projects', 'enable_sp_awards')
								),
								array(
									'id' => 'candidate_details_sidebar_order',
									'type' => 'sortable',
									'title' => esc_html__('Candidate Sidebar Order', 'civi-framework'),
									'desc' => esc_html__('Drag and drop layout manager, to quickly organize your candidate sidebar order.', 'civi-framework'),
									'options' => array(
										'enable_sidebar_sp_info' => esc_html__('Information', 'civi-framework'),
										'enable_sidebar_sp_location' => esc_html__('Location', 'civi-framework'),
									),
									'default' => array('enable_sidebar_sp_info', 'enable_sidebar_sp_location'),
								),
							),
						)),
						apply_filters('civi_register_option_single_candidate_page_bottom', array()),

						//Candidate Profile
						apply_filters('civi_option_candidate_profile_top', array()),
						apply_filters('civi_option_candidate_profile_main', array(
							'id' => 'candidate_profile_group',
							'title' => esc_html__('Candidate Profile', 'civi-framework'),
							'type' => 'group',
							'fields' => array(
								array(
									'id' => 'section_candidate_hide_group_fields',
									'title' => esc_html__('Hide Submit Group Form Fields', 'civi-framework'),
									'type' => 'group',
									'fields' => array(
										array(
											'id' => 'hide_candidate_group_fields',
											'type' => 'checkbox_list',
											'title' => esc_html__('Hide Submit Form Groups', 'civi-framework'),
											'subtitle' => esc_html__('Choose which fields you want to hide on Candidate Profile', 'civi-framework'),
											'options' => array(
												'info' => esc_html__('Basic Info', 'civi-framework'),
												'education' => esc_html__('Education', 'civi-framework'),
												'experience' => esc_html__('Experience', 'civi-framework'),
												'skills' => esc_html__('Skills', 'civi-framework'),
												'paymentinfo' => esc_html__('Payment Information', 'civi-framework'),
												'projects' => esc_html__('Projects', 'civi-framework'),
												'awards' => esc_html__('Awards', 'civi-framework'),
											),			
											'value_inline' => false,
											'default' => array()
										),
										array(
											'id' => 'hide_candidate_fields',
											'type' => 'checkbox_list',
											'title' => esc_html__('Hide Submit Form Fields', 'civi-framework'),
											'subtitle' => esc_html__('Choose which fields you want to hide on New Property page?', 'civi-framework'),
											'options' => array(
												'fields_candidate_avatar' => esc_html__('Avatar', 'civi-framework'),
												'fields_candidate_thumbnail' => esc_html__('Thumbnail', 'civi-framework'),
												'fields_candidate_first_name' => esc_html__('First name', 'civi-framework'),
												'fields_candidate_last_name' => esc_html__('Last name', 'civi-framework'),
												'fields_candidate_email_address' => esc_html__('Email address', 'civi-framework'),
												'fields_candidate_phone_number' => esc_html__('Phone number', 'civi-framework'),
												'fields_candidate_current_position' => esc_html__('Current Position', 'civi-framework'),
												'fields_candidate_categories' => esc_html__('Categories', 'civi-framework'),
												'fields_candidate_description' => esc_html__('Description', 'civi-framework'),
												'fields_candidate_date_of_birth' => esc_html__('Date of Birth', 'civi-framework'),
												'fields_candidate_age' => esc_html__('Age', 'civi-framework'),
												'fields_candidate_gender' => esc_html__('Gender', 'civi-framework'),
												'fields_closing_languages' => esc_html__('Languages', 'civi-framework'),
												'fields_candidate_qualification' => esc_html__('Qualification', 'civi-framework'),
												'fields_candidate_experience' => esc_html__('Years of Experience', 'civi-framework'),
												'fields_candidate_salary' => esc_html__('Salary', 'civi-framework'),
												'fields_candidate_resume' => esc_html__('Resume', 'civi-framework'),
												'fields_candidate_social' => esc_html__('Social Network', 'civi-framework'),
												'fields_candidate_my_profile' => esc_html__('My Profile', 'civi-framework'),
												'fields_candidate_location' => esc_html__('Locations', 'civi-framework'),
												'fields_candidate_gallery' => esc_html__('Gallery', 'civi-framework'),
												'fields_candidate_video' => esc_html__('Video', 'civi-framework'),
											),
											'value_inline' => false,
											'default' => array()
										),
									)
								),
							)
						)),
						apply_filters('civi_option_candidate_submit_bottom', array()),

						//Candidate Search
						apply_filters('civi_register_option_search_candidate_page_top', array()),
						apply_filters('civi_register_option_search_candidate_page_main', array(
							'id' => 'candidate_search_group',
							'title' => esc_html__('Search Candidate', 'civi-framework'),
							'type' => 'group',
							'fields' => array(
								array(
									'id' => 'enable_candidate_search_bg',
									'type' => 'button_set',
									'title' => esc_html__('Enable Background', 'civi-framework'),
									'subtitle' => esc_html__('Show/Hidden Background', 'civi-framework'),
									'desc' => '',
									'options' => array(
										'1' => esc_html__('On', 'civi-framework'),
										'0' => esc_html__('Off', 'civi-framework'),
									),
									'default' => '0',
								),
								array(
									'id' => "candidate_search_color",
									'title' => esc_html__('Color', 'civi-framework'),
									'type' => 'color',
									'col' => '12',
									'default' => '',
									'required' => array(
										array("enable_candidate_search_bg", '=', '1'),
									),
								),
								array(
									'id' => "candidate_search_image",
									'title' => esc_html__('Image', 'civi-framework'),
									'type' => 'image',
									'default' => '',
									'col' => '12',
									'required' => array(
										array("enable_candidate_search_bg", '=', '1'),
									),
								),
								array(
									'id' => 'candidate_search_fields',
									'title' => esc_html__('Search Fields', 'civi-framework'),
									'type' => 'sorter',
									'default' => array(
										'top' => array(
											'candidate_locations' => esc_html__('Location', 'civi-framework'),
											'candidate_categories' => esc_html__('Categories', 'civi-framework'),
										),
										'sidebar' => array(
											'candidate_rating' => esc_html__('Rating', 'civi-framework'),
											'candidate_yoe' => esc_html__('Experience', 'civi-framework'),
											'candidate_qualification' => esc_html__('Qualification', 'civi-framework'),
											'candidate_gender' => esc_html__('Gender', 'civi-framework'),
										),
										'disable' => array(
											'candidate_ages' => esc_html__('Ages', 'civi-framework'),
											'candidate_skills' => esc_html__('Skills', 'civi-framework'),
											'candidate_languages' => esc_html__('Native Language', 'civi-framework'),
										)
									),
								),
								array(
									'id' => 'candidate_search_fields_candidate_categories',
									'title' => esc_html__('Icon Categories', 'civi-framework'),
									'type' => 'text',
									'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'civi-framework'),
								),
								array(
									'id' => 'candidate_search_fields_candidate_rating',
									'title' => esc_html__('Icon Rating', 'civi-framework'),
									'type' => 'text',
									'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'civi-framework'),
								),
								array(
									'id' => 'candidate_search_fields_candidate_yoe',
									'title' => esc_html__('Icon Experience', 'civi-framework'),
									'type' => 'text',
									'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'civi-framework'),
								),
								array(
									'id' => 'candidate_search_fields_candidate_qualification',
									'title' => esc_html__('Icon Qualification', 'civi-framework'),
									'type' => 'text',
									'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'civi-framework'),
								),
								array(
									'id' => 'candidate_search_fields_candidate_ages',
									'title' => esc_html__('Icon Ages', 'civi-framework'),
									'type' => 'text',
									'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'civi-framework'),
								),
								array(
									'id' => 'candidate_search_fields_candidate_gender',
									'title' => esc_html__('Icon Gender', 'civi-framework'),
									'type' => 'text',
									'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'civi-framework'),
								),
								array(
									'id' => 'candidate_search_fields_candidate_skills',
									'title' => esc_html__('Icon Skills', 'civi-framework'),
									'type' => 'text',
									'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'civi-framework'),
								),
								array(
									'id' => 'candidate_search_fields_candidate_languages',
									'title' => esc_html__('Icon Languages', 'civi-framework'),
									'type' => 'text',
									'desc' => esc_html__('Please enter the html code of the "fontawesome" icon to display it', 'civi-framework'),
								),
							)
						)),
						apply_filters('civi_register_option_search_candidate_page_bottom', array())
					),
				));
		}


		/**
		 * @return mixed|void
		 */
		private function email_management_option()
		{
			return apply_filters('civi_register_option_email_management', array(
				'id' => 'civi_email_management_option',
				'title' => esc_html__('Email Template', 'civi-framework'),
				'icon' => 'dashicons-email-alt',
				'fields' => array_merge(
					apply_filters('civi_register_option_email_management_top', array()),
					array(
						//Header
						array(
							'id' => 'email-header',
							'title' => esc_html__('Header Email', 'civi-framework'),
							'type' => 'group',
							'toggle_default' => false,
							'fields' => array(
								array(
									'id' => 'logo_email',
									'type' => 'text',
									'title' => esc_html__('Logo Email', 'civi-framework'),
									'default' => '',
									'subtitle' => esc_html__('Choose link logo for email', 'civi-framework'),
								),
								array(
									'id' => 'title_email',
									'type' => 'text',
									'title' => esc_html__('Title', 'civi-framework'),
									'default' => esc_html__('Welcome to %website_url!', 'civi-framework'),
								),
							)
						),
						//Content
						array(
							'id' => 'email-content',
							'title' => esc_html__('Content Email', 'civi-framework'),
							'type' => 'group',
							'toggle_default' => false,
							'fields' => array(
								array(
									'id' => 'email-new-user',
									'title' => esc_html__('New Registed User', 'civi-framework'),
									'type' => 'group',
									'toggle_default' => false,
									'fields' => array(
										array(
											'id' => 'civi_user_mail_register_user',
											'type' => 'info',
											'style' => 'info',
											'title' => esc_html__('User Email', 'civi-framework'),
										),
										array(
											'id' => 'subject_mail_register_user',
											'type' => 'text',
											'title' => esc_html__('Subject', 'civi-framework'),
											'default' => esc_html__('You have succesfully registered on %website_url', 'civi-framework'),
										),
										array(
											'id' => 'mail_register_user',
											'type' => 'editor',
											'args' => array(
												'media_buttons' => true,
												'quicktags' => true,
											),
											'title' => esc_html__('Content', 'civi-framework'),
											'default' => esc_html__(
												'Welcome to CityMody Family !												
													You can now apply to the projects that suit your abilities.
													If you have any questions, you can always contact us.',
												'civi-framework'
											),
										),
										array(
											'id' => 'civi_admin_mail_register_user',
											'type' => 'info',
											'style' => 'info',
											'title' => esc_html__('Admin Email', 'civi-framework'),
										),
										array(
											'id' => 'subject_admin_mail_register_user',
											'type' => 'text',
											'title' => esc_html__('Subject', 'civi-framework'),
											'default' => esc_html__('New User Registration', 'civi-framework'),
										),
										array(
											'id' => 'admin_mail_register_user',
											'type' => 'editor',
											'args' => array(
												'media_buttons' => true,
												'quicktags' => true,
											),
											'title' => esc_html__('Content', 'civi-framework'),
											'default' => esc_html__(
												'New CityMody user registration on %website_url.
													Username: %user_login_register,
                          E-mail: %user_email_register',
												'civi-framework'
											),
										)
									)
								),

								array(
									'id' => 'mail-verify-user',
									'title' => esc_html__('Verify User', 'civi-framework'),
									'type' => 'group',
									'toggle_default' => false,
									'fields' => array(
										array(
											'id' => 'civi_user_mail_verify_user',
											'type' => 'info',
											'style' => 'info',
											'title' => esc_html__('User Email', 'civi-framework'),
										),
										array(
											'id' => 'subject_mail_verify_user',
											'type' => 'text',
											'title' => esc_html__('Subject', 'civi-framework'),
											'default' => esc_html__('Account Verification', 'civi-framework'),
										),
										array(
											'id' => 'mail_verify_user',
											'type' => 'editor',
											'args' => array(
												'media_buttons' => true,
												'quicktags' => true,
											),
											'title' => esc_html__('Content', 'civi-framework'),
											'default' => esc_html__(
												"To verify your email address, please use the following One Time Password (OTP):
													%code_verify_user
												If you have any problems, please contact us.
                                        		Thank you!",
												'civi-framework'
											),
										)
									)
								),

								array(
									'id' => 'email-activated-package',
									'title' => esc_html__('Activated Package', 'civi-framework'),
									'type' => 'group',
									'toggle_default' => false,
									'fields' => array(
										array(
											'id' => 'civi_user_mail_activated_package',
											'type' => 'info',
											'style' => 'info',
											'title' => esc_html__('User Email', 'civi-framework'),
										),
										array(
											'id' => 'subject_mail_activated_package',
											'type' => 'text',
											'title' => esc_html__('Subject', 'civi-framework'),
											'default' => esc_html__('Your purchase was activated', 'civi-framework'),
										),
										array(
											'id' => 'mail_activated_package',
											'type' => 'editor',
											'args' => array(
												'media_buttons' => true,
												'quicktags' => true,
											),
											'title' => esc_html__('Content', 'civi-framework'),
											'default' => esc_html__(
												"Hi thcivi,
                                        Welcome to %website_url and thank you for purchasing a plan with us. We are excited you have chosen %website_name . %website_name is a great jobs to advertise and search properties.
                                        You plan on  %website_url activated! You can now list your properties according to you plan.",
												'civi-framework'
											),
										)
									)
								),

								array(
									'id' => 'new-jobs-apply',
									'title' => esc_html__('Apply Jobs', 'civi-framework'),
									'type' => 'group',
									'toggle_default' => false,
									'fields' => array(
										array(
											'id' => 'civi_info_mail_candidate_apply',
											'type' => 'info',
											'style' => 'info',
											'title' => esc_html__('User Email', 'civi-framework'),
										),
										array(
											'id' => 'subject_mail_candidate_apply',
											'type' => 'text',
											'title' => esc_html__('Subject', 'civi-framework'),
											'default' => esc_html__('You have successfully applied for a project on %website_url', 'civi-framework'),
										),
										array(
											'id' => 'mail_candidate_apply',
											'type' => 'editor',
											'args' => array(
												'media_buttons' => true,
												'quicktags' => true,
											),
											'title' => esc_html__('Content', 'civi-framework'),
											'default' => esc_html__(
												'Congratulations %user_apply!
													We Received Your Project Application.
															Jobs Title: %jobs_apply
                              Jobs Url: %jobs_url
												We will contact you if approved by the project owner.',
												'civi-framework'
											),
										),
										array(
											'id' => 'civi_info_mail_employer_apply',
											'type' => 'info',
											'style' => 'info',
											'title' => esc_html__('Admin Email', 'civi-framework'),
										),
										array(
											'id' => 'subject_mail_employer_apply',
											'type' => 'text',
											'title' => esc_html__('Subject', 'civi-framework'),
											'default' => esc_html__('There is 1 candidate applied for your job', 'civi-framework'),
										),
										array(
											'id' => 'mail_employer_apply',
											'type' => 'editor',
											'args' => array(
												'media_buttons' => true,
												'quicktags' => true,
											),
											'title' => esc_html__('Content', 'civi-framework'),
											'default' => esc_html__(
												'Hi,
                                        Your jobs on %website_url has been applied.
                                        Jobs Title: %jobs_apply
                                        Jobs Url: %jobs_url
                                        User Apply: %user_apply
                                        User Info: %user_url',
												'civi-framework'
											),
										),
										array(
											'id' => 'civi_info_mail_candidate_apply_nlogin',
											'type' => 'info',
											'style' => 'info',
											'title' => esc_html__('Candidate Email (Not Login)', 'civi-framework'),
										),
										array(
											'id' => 'subject_mail_candidate_apply_nlogin',
											'type' => 'text',
											'title' => esc_html__('Subject', 'civi-framework'),
											'default' => esc_html__('There is 1 candidate applied for your job', 'civi-framework'),
										),
										array(
											'id' => 'mail_candidate_apply_nlogin',
											'type' => 'editor',
											'args' => array(
												'media_buttons' => true,
												'quicktags' => true,
											),
											'title' => esc_html__('Content', 'civi-framework'),
											'default' => esc_html__(
												'Hi,
                                        Your jobs on %website_url has been applied.
                                        Jobs Title: %jobs_apply
                                        Jobs Url: %jobs_url
                                        CV Url: %cv_url
                                        Message: %message',
												'civi-framework'
											),
										),
									)
								),

								array(
									'id' => 'email-activated-listing',
									'title' => esc_html__('Activated Jobs', 'civi-framework'),
									'type' => 'group',
									'toggle_default' => false,
									'fields' => array(
										array(
											'id' => 'civi_user_mail_activated_listing',
											'type' => 'info',
											'style' => 'info',
											'title' => esc_html__('User Email', 'civi-framework'),
										),
										array(
											'id' => 'subject_mail_activated_listing',
											'type' => 'text',
											'title' => esc_html__('Subject', 'civi-framework'),
											'default' => esc_html__('Your purchase was activated', 'civi-framework'),
										),
										array(
											'id' => 'mail_activated_listing',
											'type' => 'editor',
											'args' => array(
												'media_buttons' => true,
												'quicktags' => true,
											),
											'title' => esc_html__('Content', 'civi-framework'),
											'default' => esc_html__('Hi thcivi,Your purchase on %website_url is activated! You should go and check it out.', 'civi-framework'),
										)
									)
								),

								array(
									'id' => 'email-approved-listing',
									'title' => esc_html__('Approved Jobs', 'civi-framework'),
									'type' => 'group',
									'toggle_default' => false,
									'fields' => array(
										array(
											'id' => 'civi_user_mail_approved_listing',
											'type' => 'info',
											'style' => 'info',
											'title' => esc_html__('User Email', 'civi-framework'),
										),
										array(
											'id' => 'subject_mail_approved_listing',
											'type' => 'text',
											'title' => esc_html__('Subject', 'civi-framework'),
											'default' => esc_html__('Your listing approved', 'civi-framework'),
										),
										array(
											'id' => 'mail_approved_listing',
											'type' => 'editor',
											'args' => array(
												'media_buttons' => true,
												'quicktags' => true,
											),
											'title' => esc_html__('Content', 'civi-framework'),
											'default' => esc_html__(
												"Hi thcivi,
                                        Your jobs on %website_url has been approved.

                                        Jobs Title:%listing_title
                                        Jobs Url: %listing_url",
												'civi-framework'
											),
										)
									)
								),
								array(
									'id' => 'email-expired-listing',
									'title' => esc_html__('Expired Jobs', 'civi-framework'),
									'type' => 'group',
									'toggle_default' => false,
									'fields' => array(
										array(
											'id' => 'civi_user_mail_expired_listing',
											'type' => 'info',
											'style' => 'info',
											'title' => esc_html__('User Email', 'civi-framework'),
										),
										array(
											'id' => 'subject_mail_expired_listing',
											'type' => 'text',
											'title' => esc_html__('Subject', 'civi-framework'),
											'default' => esc_html__('Your listing expired', 'civi-framework'),
										),
										array(
											'id' => 'mail_expired_listing',
											'type' => 'editor',
											'args' => array(
												'media_buttons' => true,
												'quicktags' => true,
											),
											'title' => esc_html__('Content', 'civi-framework'),
											'default' => esc_html__(
												"Hi,
                                        Your jobs on %website_url has been expired.

                                        Jobs Title:%listing_title
                                        Jobs Url: %listing_url",
												'civi-framework'
											),
										)
									)
								),
								array(
									'id' => 'email-new-wire-transfer',
									'title' => esc_html__('New Wire Transfer', 'civi-framework'),
									'type' => 'group',
									'toggle_default' => false,
									'fields' => array(
										array(
											'id' => 'civi_user_mail_new_wire_transfer',
											'type' => 'info',
											'style' => 'info',
											'title' => esc_html__('User Email', 'civi-framework'),
										),
										array(
											'id' => 'subject_mail_new_wire_transfer',
											'type' => 'text',
											'title' => esc_html__('Subject', 'civi-framework'),
											'default' => esc_html__('You ordcivid a new Wire Transfer', 'civi-framework'),
										),
										array(
											'id' => 'mail_new_wire_transfer',
											'type' => 'editor',
											'args' => array(
												'media_buttons' => true,
												'quicktags' => true,
											),
											'title' => esc_html__('Content', 'civi-framework'),
											'default' => esc_html__(
												'We received your Wire Transfer payment request on  %website_url !
                                        Please follow the instructions below in order to start submitting properties as soon as possible.
                                        The invoice number is: %invoice_no, Amount: %total_price.',
												'civi-framework'
											),
										),
										array(
											'id' => 'civi_admin_mail_new_wire_transfer',
											'type' => 'info',
											'style' => 'info',
											'title' => esc_html__('Admin Email', 'civi-framework'),
										),
										array(
											'id' => 'subject_admin_mail_new_wire_transfer',
											'type' => 'text',
											'title' => esc_html__('Subject', 'civi-framework'),
											'default' => esc_html__('Somebody ordcivid a new Wire Transfer', 'civi-framework'),
										),
										array(
											'id' => 'admin_mail_new_wire_transfer',
											'type' => 'editor',
											'args' => array(
												'media_buttons' => true,
												'quicktags' => true,
											),
											'title' => esc_html__('Content', 'civi-framework'),
											'default' => esc_html__(
												'We received your Wire Transfer payment request on  %website_url !
                                        Please follow the instructions below in order to start submitting properties as soon as possible.
                                        The invoice number is: %invoice_no, Amount: %total_price.',
												'civi-framework'
											),
										)
									)
								),
							)
						),
						//Footer
						array(
							'id' => 'email-footer',
							'title' => esc_html__('Footer Email', 'civi-framework'),
							'type' => 'group',
							'toggle_default' => false,
							'fields' => array(
								array(
									'id' => 'mail_footer_user',
									'type' => 'editor',
									'args' => array(
										'media_buttons' => true,
										'quicktags' => true,
									),
									'title' => esc_html__('Content', 'civi-framework'),
									'default' => esc_html__(
										'Do you need help? Contact us
                                        T. (00) 658 54332
                                        E. hello@uxper.co
                                        © 2023 Uxper. All Right Reserved.',
										'civi-framework'
									),
								),
							)
						),
					),
					apply_filters('civi_register_option_email_management_bottom', array())
				)
			));
		}
	}
}
