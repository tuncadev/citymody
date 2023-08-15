<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$company_id    = get_the_ID();
$company_location =  get_the_terms($company_id, 'company-location');
$company_logo   = get_post_meta($company_id, CIVI_METABOX_PREFIX . 'company_logo');
$company_website  = get_post_meta($company_id, CIVI_METABOX_PREFIX . 'company_website');
?>
<div class="block-archive-inner company-head-details">
	<div class="civi-company-header-top">
		<?php if (!empty($company_logo[0]['url'])) : ?>
		<div class="logo-comnpany">
			<img src="<?php echo $company_logo[0]['url'] ?>" alt="" />
		</div>
		<?php endif; ?>
		<div class="info">
			<div class="title-wapper">
				<?php if (!empty(get_the_title())) : ?>
				<h1><?php echo get_the_title(); ?></h1>
				<?php civi_company_green_tick($company_id); ?>
				<?php endif; ?>
			</div>
			<div class="company-info">
				<?php if (is_array($company_location)) { ?>
				<div class="company-warpper">
					<i class="fas fa-map-marker-alt"></i>
					<?php foreach ($company_location as $location) {
                            $cate_link = get_term_link($location, 'company-location'); ?>
					<div class="cate-warpper">
						<a href="<?php echo esc_url($cate_link); ?>" class="cate civi-link-bottom">
							<?php echo $location->name; ?>
						</a>
					</div>
					<?php } ?>
				</div>
				<?php } ?>
				<?php echo civi_get_total_rating('company',$company_id); ?>
			</div>
		</div>
	</div>
	<div class="civi-company-header-bottom">
		<?php civi_get_template('company/follow.php', array(
            'company_id' => $company_id,
        )); ?>
		<a href="<?php if (isset($company_website[0])) {
                        echo $company_website[0];
                    } ?>" class="civi-button button-outline btn-webs button-icon-right" target="_blank">
			<?php esc_html_e('Visit website', 'civi-framework') ?><i class="far fa-external-link-alt"></i>
		</a>
		<?php 
				/* Remove message 
				civi_get_template('company/messages.php', array(
            'company_id' => $company_id,
        )); 
				*/
				?>
	</div>
</div>