<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if($job_id){
	$jobs_id = $job_id;
} else {
	$jobs_id = get_the_ID();
}

$jobs_categories = get_the_terms($jobs_id, 'jobs-categories');
$enable_single_jobs_related = civi_get_option('enable_single_jobs_related', '1');

$categories = array();
if ($jobs_categories) :
    foreach ($jobs_categories as $cate) {
        $cate_id = $cate->term_id;
        $categories[] = $cate_id;
    }
endif;

$args = array(
    'posts_per_page' => 3,
    'post_type' => 'jobs',
    'post_status' => 'publish',
    'ignore_sticky_posts' => 1,
    'exclude' => $jobs_id,
    'orderby' => array(
        'menu_order' => 'ASC',
        'date' => 'DESC',
    ),
    'tax_query' => array(
        'relation' => 'AND',
        array(
            'taxonomy' => 'jobs-categories',
            'field' => 'id',
            'terms' => $categories
        ),
        'meta_query' => array(
            array(
                'key' => CIVI_METABOX_PREFIX . 'enable_jobs_package_expires',
                'value' => 0,
                'compare' => '=='
            )
        ),
    ),
);
$jobs = get_posts($args);
?>
<?php if ($enable_single_jobs_related && !empty($jobs)) : ?>
    <div class="jobs-related-details">
        <div class="header-related">
            <h4 class="title-jobs"><?php esc_html_e('Similar jobs', 'civi-framework') ?></h4>
            <a href="<?php echo get_post_type_archive_link('jobs') ?>"
               class="civi-button button-border-bottom"><?php esc_html_e('View all jobs', 'civi-framework') ?></a>
        </div>
        <div class="related-inner">
            <?php echo civi_get_jobs_by_category(3, 3, $categories); ?>
        </div>
    </div>
<?php endif; ?>
