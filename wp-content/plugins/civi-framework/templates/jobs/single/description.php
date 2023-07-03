<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if($job_id){
	$jobs_id = $job_id;
} else {
	$jobs_id = get_the_ID();
}
$content = get_post_field('post_content', $jobs_id);
if (isset($content) && !empty($content)) : ?>
    <div class="block-archive-inner jobs-description-details civi-description-details">
        <h4 class="title-jobs"><?php esc_html_e('Description', 'civi-framework') ?></h4>
        <div class="civi-description">
            <?php echo $content; ?>
        </div>
        <div class="toggle-description">
            <a href="#" class="show-more-description"><?php esc_html_e('Show more', 'civi-framework'); ?><i class="fas fa-angle-down"></i></a>
            <a href="#" class="hide-all-description"><?php esc_html_e('Hide less', 'civi-framework'); ?><i class="fas fa-angle-up"></i></a>
        </div>
    </div>
<?php endif; ?>
