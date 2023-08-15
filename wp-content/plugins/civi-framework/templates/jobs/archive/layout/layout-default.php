<?php

/**
 * The Template for displaying jobs archive
 */

defined('ABSPATH') || exit;

wp_enqueue_script('plupload');
$candidate_resume = isset($candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_resume_id_list']) ? $candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_resume_id_list'][0] : '';
$filename = basename(get_attached_file($candidate_resume));
$ajax_url = admin_url('admin-ajax.php');
$cv_file = civi_get_option('civi-cv-type');
$cv_max_file_size = civi_get_option('cv_file_size', '1000kb');

$upload_nonce = wp_create_nonce('civi_thumbnail_allow_upload');
$url = CIVI_AJAX_URL . '?action=civi_thumbnail_upload_ajax&nonce=' . esc_attr($upload_nonce);
$text = '<i class="far fa-arrow-from-bottom large"></i> ' . esc_html__('Browse', 'civi-framework');

wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'upload-cv');

wp_localize_script(
	CIVI_PLUGIN_PREFIX . 'upload-cv',
	'civi_upload_cv_vars',
	array(
		'ajax_url' => $ajax_url,
		'title' => esc_html__('Valid file formats', 'civi-framework'),
		'cv_file' => $cv_file,
		'cv_max_file_size' => $cv_max_file_size,
		'upload_nonce' => $upload_nonce,
		'url' => $url,
		'text' => $text,
	)
);
wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'jobs-archive');

$archive_jobs_items_amount = civi_get_option('archive_jobs_items_amount', '12');
$content_jobs = civi_get_option('archive_jobs_layout', 'layout-list');
$content_jobs = !empty($_GET['layout']) ? Civi_Helper::civi_clean(wp_unslash($_GET['layout'])) : $content_jobs;
$hide_jobs_top_filter_fields = civi_get_option('hide_jobs_top_filter_fields');
$enable_jobs_filter_top = civi_get_option('enable_jobs_filter_top');
$enable_jobs_show_map = civi_get_option('enable_jobs_show_map');
$jobs_map_postion = civi_get_option('jobs_map_postion');

if ($enable_jobs_show_map == 1 || $content_jobs == 'layout-full') {
	$jobs_filter_sidebar_option = 'filter-canvas';
} else {
	$jobs_filter_sidebar_option = civi_get_option('jobs_filter_sidebar_option');
}

$jobs_filter_sidebar_option = !empty($_GET['filter']) ? Civi_Helper::civi_clean(wp_unslash($_GET['filter'])) : $jobs_filter_sidebar_option;
$jobs_map_postion = !empty($_GET['map']) ? Civi_Helper::civi_clean(wp_unslash($_GET['map'])) : $jobs_map_postion;
$enable_jobs_show_map = !empty($_GET['has_map']) ? Civi_Helper::civi_clean(wp_unslash($_GET['has_map'])) : $enable_jobs_show_map;

$key = isset($_GET['s']) ? civi_clean(wp_unslash($_GET['s'])) : '';

$archive_class = array();
$archive_class[] = 'content-jobs area-jobs area-archive';

$class_scrollbar = '';
if ($content_jobs == 'layout-list') {
	$class_inner[] = 'layout-list';
} else if ($content_jobs == 'layout-full') {
	$archive_class[] = 'column-1';
	$class_scrollbar = 'custom-scrollbar';
} else {
	$class_inner[] = 'layout-grid';
}

$tax_query = array();
$meta_query = array();
$args = array(
	'posts_per_page' => $archive_jobs_items_amount,
	'post_type' => 'jobs',
	'ignore_sticky_posts' => 1,
	'post_status' => 'publish',
	'tax_query' => $tax_query,
	's' => $key,
	'meta_key' => 'civi-jobs_featured',
	'orderby' => 'meta_value date',
	'order' => 'DESC',
);

$meta_query[] = array(
	'key' => CIVI_METABOX_PREFIX . 'enable_jobs_package_expires',
	'value' => 0,
	'compare' => '=='
);

$company_id = isset($_GET['company_id']) ? civi_clean(wp_unslash($_GET['company_id'])) : '';
if ($company_id) {
	$meta_query[] = array(
		'key' => CIVI_METABOX_PREFIX . 'jobs_select_company',
		'value' => $company_id,
		'compare' => '=='
	);
}

$args['meta_query'] = array(
	'relation' => 'AND',
	$meta_query
);

//Current term
$jobs_location = isset($_GET['jobs-location']) ? civi_clean(wp_unslash($_GET['jobs-location'])) : '';
if (!empty($jobs_location)) {
	$current_term = get_term_by('slug', $jobs_location, get_query_var('taxonomy'));
} else {
	$current_term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
}

$current_term_name = '';
if (!empty($current_term)) {
	$current_term_name = $current_term->name;
} elseif (empty($current_term) && isset($_GET['jobs-location']) && $_GET['jobs-location'] != '') {
	$current_term_name = civi_clean(wp_unslash($_GET['jobs-location']));
}
if (is_tax() && !empty($current_term)) {
	$taxonomy_title = $current_term->name;
	$taxonomy_name = get_query_var('taxonomy');
	if (!empty($taxonomy_name)) {
		$tax_query[] = array(
			'taxonomy' => $taxonomy_name,
			'field' => 'slug',
			'terms' => $current_term->slug
		);
	}
} elseif (empty($current_term) && isset($_GET['jobs-location']) && $_GET['jobs-location'] != '') {
	$taxonomy_name = get_query_var('taxonomy');
	$tax_query[] = array(
		'taxonomy' => $taxonomy_name,
		'field' => 'slug',
		'terms' => civi_clean(wp_unslash($_GET['jobs-location'])),
	);
}

$tax_count = count($tax_query);
if ($tax_count > 0) {
	$args['tax_query'] = array(
		'relation' => 'AND',
		$tax_query
	);
}

$data = new WP_Query($args);
$total_post = $data->found_posts;

$first_job_id = 0;

if ($enable_jobs_show_map == 1) {
	$class_inner[] = 'has-map';
} else if ($content_jobs == 'layout-full') {
	$class_inner[] = 'layout-full';
} else {
	$class_inner[] = 'no-map';
}

?>
<?php if ($enable_jobs_show_map == 1 && $jobs_map_postion == 'map-top') { ?>
	<div class="col-right">
		<?php
		/**
		 * @Hook: civi_archive_map_filter
		 *
		 * @hooked archive_map_filter
		 */
		do_action('civi_archive_map_filter');
		?>
	</div>
<?php } ?>

<?php if ($enable_jobs_filter_top == 1) { ?>
	<?php do_action('civi_archive_jobs_top_filter', $current_term, $total_post); ?>
<?php } ?>

<div class="inner-content container <?php echo join(' ', $class_inner); ?>">
	<div class="col-left <?php echo $class_scrollbar; ?>">

		<?php if ($jobs_filter_sidebar_option !== 'filter-right') {
			do_action('civi_archive_jobs_sidebar_filter', $current_term, $total_post);
		} ?>

		<?php
		/**
		 * @Hook: civi_output_content_wrapper_start
		 *
		 * @hooked output_content_wrapper_start
		 */
		do_action('civi_output_content_wrapper_start');
		?>

		<div class="filter-warpper">
			<div class="entry-left">
				<div class="btn-canvas-filter <?php if ($jobs_filter_sidebar_option !== 'filter-canvas' && $enable_jobs_show_map != 1) { ?>hidden-lg-up<?php } ?>">
					<a href="#"><i class="fal fa-filter"></i><?php esc_html_e('Filter', 'civi-framework'); ?></a>
				</div>
				<span class="result-count">
					<?php if (!empty($key)) { ?>
						<?php printf(esc_html__('%1$s jobs for "%2$s"', 'civi-framework'), '<span>' . $total_post . '</span>', $key); ?>
					<?php } elseif (is_tax()) { ?>
						<?php printf(esc_html__('%1$s jobs for "%2$s"', 'civi-framework'), '<span>' . $total_post . '</span>', $current_term_name); ?>
					<?php } else { ?>
						<?php printf(esc_html__('%1$s jobs', 'civi-framework'), '<span>' . $total_post . '</span>'); ?>
					<?php } ?>
				</span>
			</div>
			<div class="entry-right">
				<div class="entry-filter">
					<div class="civi-clear-filter hidden-lg-up">
						<i class="far fa-sync fa-spin"></i>
						<span><?php esc_html_e('Clear All', 'civi-framework'); ?></span>
					</div>
					<?php
					if ($content_jobs != 'layout-full') {
					?>
						<div class="jobs-layout switch-layout">
							<a class="<?php if ($content_jobs == 'layout-grid') : echo 'active';
										endif; ?>" href="#" data-layout="layout-grid"><i class="far far fa-th-large icon-large"></i></a>
							<a class="<?php if ($content_jobs == 'layout-list') : echo 'active';
										endif; ?>" href="#" data-layout="layout-list"><i class="far fa-list icon-large"></i></a>
						</div>
					<?php
					}
					?>
					<span class="text-sort-by" style="display:none;"><?php esc_html_e('Sort by', 'civi-framework'); ?></span>
					<select name="sort_by" class="sort-by filter-control civi-select2">
						<option value="newest"><?php esc_html_e('Newest', 'civi-framework'); ?></option>
						<option value="oldest"><?php esc_html_e('Oldest', 'civi-framework'); ?></option>
						<option value="featured"><?php esc_html_e('Featured', 'civi-framework'); ?></option>
					</select>
					<?php if ($enable_jobs_show_map == 1 && $jobs_map_postion == 'map-right') { ?>
						<div class="btn-control btn-switch btn-hide-map">
							<span class="text-switch"><?php esc_html_e('Map', 'civi-framework'); ?></span>
							<label class="switch">
								<input type="checkbox" value="hide_map">
								<span class="slider round"></span>
							</label>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
		<div class="entry-mobie">
			<span class="result-count">
				<?php if (!empty($key)) { ?>
					<?php printf(esc_html__('%1$s jobs for "%2$s"', 'civi-framework'), '<span>' . $total_post . '</span>', $key); ?>
				<?php } elseif (is_tax()) { ?>
					<?php printf(esc_html__('%1$s jobs for "%2$s"', 'civi-framework'), '<span>' . $total_post . '</span>', $current_term_name); ?>
				<?php } else { ?>
					<?php printf(esc_html__('%1$s jobs', 'civi-framework'), '<span>' . $total_post . '</span>'); ?>
				<?php } ?>
			</span>
			<div class="civi-clear-filter hidden-lg-up">
				<i class="far fa-sync fa-spin"></i>
				<span><?php esc_html_e('Clear All', 'civi-framework'); ?></span>
			</div>
		</div>

		<div class="<?php echo join(' ', $archive_class); ?>">
			<?php
			$i = 1;
			if ($data->have_posts()) { ?>
				<?php while ($data->have_posts()) : $data->the_post(); ?>
					<?php
					if ($i == 1) {
						$first_job_id = get_the_ID();
					}
					civi_get_template('content-jobs.php', array(
						'jobs_layout' => $content_jobs,
					));
					?>
				<?php $i++;
				endwhile; ?>
			<?php } else { ?>
				<div class="item-not-found"><?php esc_html_e('No item found', 'civi-framework'); ?></div>
			<?php } ?>
		</div>

		<?php
		$max_num_pages = $data->max_num_pages;
		civi_get_template('global/pagination.php', array('max_num_pages' => $max_num_pages, 'type' => 'ajax-call'));
		wp_reset_postdata();
		?>
		<?php
		/**
		 * @Hook: civi_output_content_wrapper_end
		 *
		 * @hooked output_content_wrapper_end
		 */
		do_action('civi_output_content_wrapper_end');
		?>

		<?php if ($jobs_filter_sidebar_option == 'filter-right' && $enable_jobs_show_map != 1) {
			do_action('civi_archive_jobs_sidebar_filter', $current_term, $total_post);
		} ?>

	</div>
	<?php
	if ($enable_jobs_show_map == 1 && $jobs_map_postion == 'map-right') {
		echo '<div class="col-right">';
		/**
		 * @Hook: civi_archive_map_filter
		 *
		 * @hooked archive_map_filter
		 */
		do_action('civi_archive_map_filter');
		echo '</div>';
	} elseif ($content_jobs == 'layout-full') {
		echo '<div class="col-right preview-job-wrapper">';
		$post_id = $first_job_id;
		$company_id = get_post_meta($post_id, CIVI_METABOX_PREFIX . 'jobs_select_company');
		$company_id = $company_id[0];
		$enable_social_twitter = civi_get_option('enable_social_twitter', '1');
		$enable_social_linkedin = civi_get_option('enable_social_linkedin', '1');
		$enable_social_facebook = civi_get_option('enable_social_facebook', '1');
		$enable_social_instagram = civi_get_option('enable_social_instagram', '1');
		if ($company_id !== '') {
			$company_logo   = get_post_meta($company_id, CIVI_METABOX_PREFIX . 'company_logo');
			$company_categories =  get_the_terms($company_id, 'company-categories');
			$company_founded =  get_post_meta($company_id, CIVI_METABOX_PREFIX . 'company_founded');
			$company_phone =  get_post_meta($company_id, CIVI_METABOX_PREFIX . 'company_phone');
			$company_email =  get_post_meta($company_id, CIVI_METABOX_PREFIX . 'company_email');
			$company_size =  get_the_terms($company_id,  'company-size');
			$company_website =  get_post_meta($company_id, CIVI_METABOX_PREFIX . 'company_website');
			$company_twitter   = get_post_meta($company_id, CIVI_METABOX_PREFIX . 'company_twitter');
			$company_facebook   = get_post_meta($company_id, CIVI_METABOX_PREFIX . 'company_facebook');
			$company_instagram   = get_post_meta($company_id, CIVI_METABOX_PREFIX . 'company_instagram');
			$company_linkedin   = get_post_meta($company_id, CIVI_METABOX_PREFIX . 'company_linkedin');
			$mycompany = get_post($company_id);
			$meta_query = civi_posts_company($company_id);
			$meta_query_post = civi_posts_company($company_id, 5);
			$company_location =  get_the_terms($company_id, 'company-location');
		}
	?>
		<div id="jobs-<?php echo $post_id; ?>">
			<div class="block-jobs-warrper">
				<div class="block-archive-top">
					<?php
					/**
					 * Hook: civi_preview_jobs_before_summary hook.
					 */
					do_action('civi_preview_jobs_before_summary', $post_id); ?>
					<div class="preview-tabs">
						<ul class="tab-nav">
							<li><a href="#job-detail" class="is-active"><?php esc_html_e('Job Detail', 'civi'); ?></a></li>
							<?php
							if ($company_id !== '') {
							?>
								<li><a href="#company-overview"><?php esc_html_e('Company Overview', 'civi'); ?></a></li>
							<?php
							}
							?>
						</ul>
						<div id="job-detail" class="tab-content is-active">
							<?php
							/**
							 * Hook: civi_preview_jobs_summary hook.
							 */
							do_action('civi_preview_jobs_summary', $post_id);
							?>
						</div>
						<?php
						if ($company_id !== '') {
						?>
							<div id="company-overview" class="tab-content">
								<div class="company-overview">
									<h4 class="title"><?php esc_html_e('Overview', 'civi-framework'); ?></h4>
									<?php if (!empty($mycompany->post_content)) : ?>
										<div class="content"><?php echo $mycompany->post_content; ?><a href="#"><?php esc_html_e('Read more', 'civi-framework'); ?></a></div>
									<?php endif; ?>
									<?php if (is_array($company_categories)) : ?>
										<div class="info">
											<p class="title-info"><?php esc_html_e('Categories', 'civi-framework'); ?></p>
											<div class="list-cate">
												<?php foreach ($company_categories as $categories) {
													$cate_link = get_term_link($categories, 'jobs-categories'); ?>
													<a href="<?php echo esc_url($cate_link); ?>" class="cate civi-link-bottom">
														<?php echo $categories->name; ?>
													</a>
												<?php } ?>
											</div>
										</div>
									<?php endif; ?>
									<?php if (is_array($company_size)) : ?>
										<div class="info">
											<p class="title-info"><?php esc_html_e('Company size', 'civi-framework'); ?></p>
											<div class="list-cate">
												<?php foreach ($company_size as $size) {
													echo $size->name;
												} ?>
											</div>
										</div>
									<?php endif; ?>
									<?php if (!empty($company_founded[0])) : ?>
										<div class="info">
											<p class="title-info"><?php esc_html_e('Founded in', 'civi-framework'); ?></p>
											<p class="details-info"><?php echo $company_founded[0]; ?></p>
										</div>
									<?php endif; ?>
									<?php if (is_array($company_location)) : ?>
										<div class="info">
											<p class="title-info"><?php esc_html_e('Location', 'civi-framework'); ?></p>
											<p class="details-info">
												<?php foreach ($company_location as $location) { ?>
													<span><?php echo $location->name; ?></span>
												<?php } ?>
											</p>
										</div>
									<?php endif; ?>
									<?php if (!empty($company_phone[0])) : ?>
										<div class="info">
											<p class="title-info"><?php esc_html_e('Phone', 'civi-framework'); ?></p>
											<p class="details-info company-phone"><a href="tel:<?php echo $company_phone[0]; ?>" data-phone="<?php echo $company_phone[0]; ?>"><?php echo substr($company_phone[0], 0, strlen($company_phone[0]) - 4); ?>****</a><i class="fal fa-eye"></i></p>
										</div>
									<?php endif; ?>
									<?php if (!empty($company_email[0])) : ?>
										<div class="info">
											<p class="title-info"><?php esc_html_e('Email', 'civi-framework'); ?></p>
											<p class="details-info email"><a href="mailto:<?php echo $company_email[0]; ?>"><?php echo $company_email[0]; ?></a></p>
										</div>
									<?php endif; ?>
									<ul class="list-social">
										<?php if (!empty($company_facebook[0]) && $enable_social_facebook == 1) : ?>
											<li><a href="<?php echo $company_facebook[0]; ?>"><i class="fab fa-facebook-f"></i></a></li>
										<?php endif; ?>
										<?php if (!empty($company_twitter[0]) && $enable_social_twitter == 1) : ?>
											<li><a href="<?php echo $company_twitter[0]; ?>"><i class="fab fa-twitter"></i></a></li>
										<?php endif; ?>
										<?php if (!empty($company_linkedin[0]) && $enable_social_linkedin == 1) : ?>
											<li><a href="<?php echo $company_linkedin[0]; ?>"><i class="fab fa-linkedin"></i></a></li>
										<?php endif; ?>
										<?php if (!empty($company_instagram[0]) && $enable_social_instagram == 1) : ?>
											<li><a href="<?php echo $company_instagram[0]; ?>"><i class="fab fa-instagram"></i></a></li>
										<?php endif; ?>
										<?php civi_get_social_network($company_id, 'company'); ?>
									</ul>
									<?php if (!empty($company_website[0])) :
										$remove_url = array("http://", "https://");
										$name_website = str_replace($remove_url, "", $company_website[0]);
									?>
										<a href="<?php echo $company_website[0]; ?>" class="civi-button button-outline button-block button-visit" target="_blank"><?php esc_html_e('Visit ', 'civi-framework'); ?><?php echo $name_website ?><i class="fas fa-external-link"></i></a>
									<?php endif; ?>
									<?php civi_get_template('company/messages.php', array(
										'company_id' => $company_id,
									)); ?>
								</div>
								<div class="company-jobs">
									<h4 class="title"><?php esc_html_e('Jobs Opening', 'civi-framework'); ?></h4>
									<ul class="list-jobs">
										<?php foreach ($meta_query_post->posts as $post) {
											$id_job = $post->ID;
										?>
											<li class="list-items">
												<h6 class="title"><a href="<?php echo get_post_permalink($id_job) ?>"><?php echo get_the_title($id_job); ?></a></h6>
												<div class="info-company">
													<?php $jobs_categories = get_the_terms($post->ID, 'jobs-categories'); ?>
													<?php if (is_array($jobs_categories)) { ?>
														<div class="categories-warpper">
															<?php foreach ($jobs_categories as $categories) {
																$cate_link = get_term_link($categories, 'jobs-categories'); ?>
																<div class="cate-warpper">
																	<a href="<?php echo esc_url($cate_link); ?>" class="cate civi-link-bottom">
																		<?php echo $categories->name; ?>
																	</a>
																</div>
															<?php } ?>
														</div>
													<?php } ?>
												</div>
											</li>
										<?php }; ?>
									</ul>
									<a href="<?php echo esc_url(get_post_type_archive_link('jobs')) . '/?company_id=' . $company_id ?>" class="civi-button button-outline button-block">
										<?php esc_html_e('View all jobs', 'civi-framework'); ?>
									</a>
								</div>
							</div>
						<?php
						}
						?>
					</div>
				</div>
				<?php
				/**
				 * Hook: civi_after_content_single_jobs_summary hook.
				 */
				do_action('civi_after_content_single_jobs_summary', $post_id);
				?>
				<?php
				/**
				 * Hook: civi_apply_single_jobs hook.
				 */
				do_action('civi_apply_single_jobs', $post_id);
				?>
			</div>
		</div>
	<?php
		echo '</div>';
	}
	?>
</div>
