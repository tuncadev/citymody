<?php

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

global $post;
$jobs_id = get_the_ID();
$jobs_select_company    = get_post_meta($jobs_id, CIVI_METABOX_PREFIX . 'jobs_select_company');
$enable_social_twitter = civi_get_option('enable_social_twitter', '1');
$enable_social_linkedin = civi_get_option('enable_social_linkedin', '1');
$enable_social_facebook = civi_get_option('enable_social_facebook', '1');
$enable_social_instagram = civi_get_option('enable_social_instagram', '1');
$company_id = isset($jobs_select_company[0]) ? $jobs_select_company[0] : '';
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

$classes = array();
$enable_sticky_sidebar_type = civi_get_option('enable_sticky_sidebar_type', 1);
if ($enable_sticky_sidebar_type) {
	$classes[] = 'has-sticky';
};

?>
<?php if ($company_id !== '') :?>
	<div class="jobs-company-sidebar block-archive-sidebar <?php echo implode(" ", $classes); ?>">
		<div class="company-header">
			<?php if (!empty($company_logo[0]['url'])) : ?>
				<img src="<?php echo $company_logo[0]['url'] ?>" alt="" />
			<?php endif; ?>
			<?php if (get_the_title($company_id)) : ?>
				<div class="name">
					<h2> <a href="<?php echo get_post_permalink($company_id) ?>"><?php echo get_the_title($company_id); ?></a></h2>
					<?php civi_company_green_tick($company_id); ?>
					<p><a href="<?php echo get_post_permalink($company_id) ?>"><?php esc_html_e('View company profile', 'civi-framework') ?></a></p>
				</div>
			<?php endif; ?>
		</div>
		<ul class="tab-company">
			<li class="tab-item"><a href="#tab-sidebar-overview"><?php esc_html_e('Overview', 'civi-framework'); ?></a></li>
			<li class="tab-item">
				<a href="#tab-sidebar-jobs"><?php esc_html_e('Jobs', 'civi-framework'); ?>
					<span><?php echo $meta_query->post_count ?></span>
				</a>
			</li>
		</ul>
		<div class="tab-content">
			<div class="tab-info-company" id="tab-sidebar-overview">
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
				<?php if (!empty($company_website[0])) :
					$remove_url = array("http://", "https://");
					$name_website = str_replace($remove_url, "", $company_website[0]);
				?>
					<a href="<?php echo $company_website[0]; ?>" class="civi-button button-outline button-block button-visit" target="_blank"><?php esc_html_e('Visit ', 'civi-framework'); ?><?php echo $name_website ?><i class="fas fa-external-link"></i></a>
				<?php endif; ?>
                 <?php civi_get_template('company/messages.php', array(
                      'company_id' => $company_id,
                  )); ?>
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
			</div>
			<div class="tab-info-company" id="tab-sidebar-jobs">
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
	</div>
<?php endif; ?>
