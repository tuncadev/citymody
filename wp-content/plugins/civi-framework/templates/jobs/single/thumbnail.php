<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if($job_id){
	$jobs_id = $job_id;
} else {
	$jobs_id = get_the_ID();
}
$custom_jobs_image_size = civi_get_option('single_jobs_image_size');
$jobs_select_company    = get_post_meta($jobs_id, CIVI_METABOX_PREFIX . 'jobs_select_company');
$company_id = isset($jobs_select_company[0]) ? $jobs_select_company[0] : '';
if (preg_match('/\d+x\d+/', $custom_jobs_image_size)) {
    $attach_id = get_post_thumbnail_id($jobs_id);
    $image_sizes = explode('x', $custom_jobs_image_size);
    $width         = $image_sizes[0];
    $height         = $image_sizes[1];
    $image_src      = civi_image_resize_id($attach_id, $width, $height, true);
}

if (has_post_thumbnail($jobs_id)) : ?>
    <div class="jobs-thumbnail-details">
        <?php if ($width !== '' & $height !== '') { ?>
            <img width="<?php echo esc_attr($width) ?>" height="<?php echo esc_attr($height) ?>" src="<?php echo esc_url($image_src) ?>" alt="<?php echo get_the_title($jobs_id); ?>" />
        <?php } else { ?>
            <?php echo the_post_thumbnail(); ?>
        <?php } ?>
    </div>
<?php endif; ?>
