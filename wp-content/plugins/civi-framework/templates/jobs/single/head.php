<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if($job_id){
	$jobs_id = $job_id;
} else {
	$jobs_id = get_the_ID();
}
$jobs_type = get_the_terms($jobs_id, 'jobs-type');
$jobs_categories =  get_the_terms($jobs_id, 'jobs-categories');
$jobs_location =  get_the_terms($jobs_id, 'jobs-location');
$jobs_featured    = get_post_meta($jobs_id, CIVI_METABOX_PREFIX . 'jobs_featured', true);
$jobs_select_company    = get_post_meta($jobs_id, CIVI_METABOX_PREFIX . 'jobs_select_company');
$company_id = isset($jobs_select_company[0]) ? $jobs_select_company[0] : '';
$company_logo   = get_post_meta($company_id, CIVI_METABOX_PREFIX . 'company_logo');
$mycompany = get_post($company_id);
$social_sharing = civi_get_option('social_sharing');
?>
<script>
function closeMe(m){
	location.reload();
}
</script>

<div class="bg-overlay-success" id="apply_success" style="display: none;">

	<div class="container" id="post_projects"> 
		<div class="row show">
			<div class="cntr-msg">
				<h2>
					<?php echo _e("Great! We have recieved your application", "civi-framework"); ?>
				</h2>
				<a class="close" href="#"  onClick="closeMe()">×</a>
				<span><strong>
					<?php echo _e("We will contact you if your application will be accepted", "civi-framework"); ?>
					</strong>
				</span>
				<p>
					<?php echo _e("This windows will close in:", "civi-framework"); ?>
					<br />
					<span id="seconds"></span>
				</p>
			</div>		
		</div>
	</div>
</div>

<div class="block-archive-inner jobs-head-details">
    <div class="civi-jobs-header-top">
        <div class="civi-header-left">
            <?php if (!empty($company_logo[0]['url'])) : ?>
                <img class="logo-comnpany" src="<?php echo $company_logo[0]['url'] ?>" alt="" />
            <?php endif; ?>
            <div class="info">
                <div class="title-wapper">
                    <?php if (!empty(get_the_title($jobs_id))) : ?>
                        <h1>
                            <?php echo get_the_title($jobs_id); ?>
                            <?php if ($jobs_featured == '1') : ?>
                                <span class="tooltip featured" data-title="<?php esc_attr_e('Featured', 'civi-framework') ?>">
                                    <img src="<?php echo esc_attr(CIVI_PLUGIN_URL . 'assets/images/icon-featured.svg'); ?>" alt="">
                                </span>
                            <?php endif; ?>
                        </h1>
                    <?php endif; ?>
                </div>
                <div>
                    <?php if (!empty($company_id)) : ?>
                        <?php esc_html_e('by', 'civi-framework') ?>
                        <a class="authour civi-link-bottom" href="<?php echo get_post_permalink($company_id) ?>"><?php echo get_the_title($company_id); ?></a>
                        <?php esc_html_e('in', 'civi-framework') ?>
                    <?php endif; ?>
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
                   <?php civi_total_view_jobs_details($jobs_id);?>
                    <?php ?>
                </div>
            </div>
        </div>
        <div class="civi-header-right">
            <?php if(!empty($social_sharing)) : ?>
                <div class="toggle-social">
                    <a href="#" class="jobs-share btn-share tooltip" data-title="<?php esc_attr_e('Share', 'civi-framework') ?>">
                        <i class="fas fa-share-alt"></i>
                    </a>
                    <?php civi_get_template('global/social-share.php'); ?>
                </div>
            <?php endif; ?>
            <?php civi_get_template('jobs/wishlist.php', array(
                'jobs_id' => $jobs_id,
            )); ?>
        </div>
    </div>
    <div class="civi-jobs-header-bottom">
		<div class="left">
			<?php if (is_array($jobs_location)) {
				foreach ($jobs_location as $location) {
					$location_link = get_term_link($location, 'jobs-location');
			?>
					<a class="label label-location" href="<?php echo esc_url($location_link); ?>">
						<i class="fas fa-map-marker-alt"></i><?php esc_html_e($location->name); ?>
					</a>
			<?php }
			} ?>
			<?php if (is_array($jobs_type)) {
				foreach ($jobs_type as $type) {
					$type_link = get_term_link($type, 'jobs-type'); ?>
					<a class="label label-type" href="<?php echo esc_url($type_link); ?>">
						<?php esc_html_e($type->name); ?>
					</a>
			<?php }
			} ?>
		</div>
		<div class="right">
			<?php
				if($job_id){
					civi_get_status_apply($jobs_id);
				}
			?>
		</div>
    </div>
</div>
