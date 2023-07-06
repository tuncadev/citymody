<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

global $wpdb;

$id = get_the_ID();
if (!empty($company_id)) {
	$id = $company_id;
}
$company_meta_data = get_post_custom($id);
$company_location = get_the_terms($company_id, 'company-location');
$company_categories =  get_the_terms($company_id, 'company-categories');
$company_size =  get_the_terms($company_id,  'company-size');
$company_logo   = get_post_meta($company_id, CIVI_METABOX_PREFIX . 'company_logo');
$company_item_class[] = 'civi-company-item';
if (!empty($layout)) {
	$company_item_class[] = $layout;
}
$company_item_class[] = 'company-' . $id;
$meta_query = civi_posts_company($id);
$enable_company_des = civi_get_option('enable_company_show_des');
?>
<div class="<?php echo join(' ', $company_item_class); ?>">
	<div class="company-header">
		<div class="company-header-right">
			<a class="company-img" href="<?php echo get_the_permalink($company_id); ?>">
				<?php if (!empty($company_logo[0]['url'])) : ?>
					<img class="logo-comnpany" src="<?php echo $company_logo[0]['url'] ?>" alt="" />
				<?php else : ?>
					<div class="logo-comnpany"><i class="far fa-camera"></i></div>
				<?php endif; ?>
			</a>
			<div class="company-info">
				<?php if (!empty(get_the_title($company_id))) : ?>
					<h2 class="company-title">
						<a href="<?php echo get_the_permalink($company_id); ?>"><?php echo get_the_title($company_id); ?></a>
					</h2>
					<?php civi_company_green_tick($company_id); ?>
				<?php endif; ?>
				<div class="company-inner">
					<?php if (is_array($company_location)) { ?>
						<div class="company-location">
							<?php foreach ($company_location as $location) {
								$location_link = get_term_link($location, 'company-size'); ?>
								<a href="<?php echo esc_url($location_link); ?>" class="cate civi-link-bottom">
									<i class="fas fa-map-marker-alt"></i><?php esc_html_e($location->name); ?>
								</a>
							<?php } ?>
						</div>
					<?php } ?>
					<?php if (is_array($company_size)) { ?>
						<div class="company-size">
							<?php foreach ($company_size as $size) {
								$size_link = get_term_link($size, 'company-size'); ?>
								<a href="<?php echo esc_url($size_link); ?>" class="cate civi-link-bottom">
									<i class="fas fa-users"></i><?php esc_html_e($size->name); ?>
								</a>
							<?php } ?>
						</div>
					<?php } ?>
					<?php echo civi_get_total_rating('company', $company_id); ?>
				</div>
			</div>
		</div>
		<div class="company-header-left">
			<div class="company-status-inner">
				<?php civi_get_template('company/follow.php', array(
					'company_id' => $company_id,
				)); ?>
			</div>
		</div>
	</div>
	<?php if (!empty(get_the_content($company_id)) && $enable_company_des) : ?>
		<div class="des-company">
			<?php echo wp_trim_words(get_the_content($company_id), 25); ?>
		</div>
	<?php endif; ?>
	<div class="company-bottom">
		<?php if (is_array($company_categories)) { ?>
			<div class="company-cate">
				<?php foreach ($company_categories as $categories) {
					$cate_link = get_term_link($categories, 'company-categories'); ?>
					<a href="<?php echo esc_url($cate_link); ?>" class="label label-categories">
						<i class="far fa-folder"></i><?php esc_html_e($categories->name); ?>
					</a>
				<?php } ?>
			</div>
		<?php } ?>
		<?php if ($meta_query->post_count > 0) : ?>
			<div class="company-available">
				<span><?php echo $meta_query->post_count; ?></span> <?php esc_html_e('jobs available', 'civi-framework') ?>
			</div>
		<?php endif; ?>
	</div>
	<a class="civi-link-item" href="<?php echo get_post_permalink($company_id) ?>"></a>
</div>