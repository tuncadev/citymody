<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$custom_field_jobs = civi_render_custom_field('jobs');
$civi_jobs_page_id = civi_get_option('civi_jobs_dashboard_page_id', 0);
wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'jobs-submit');
wp_enqueue_script('jquery-validate');
wp_localize_script(
    CIVI_PLUGIN_PREFIX . 'jobs-submit',
    'civi_submit_vars',
    array(
        'ajax_url' => CIVI_AJAX_URL,
        'not_found' => esc_html__("We didn't find any results, you can retry with other keyword.", 'civi-framework'),
        'not_jobs' => esc_html__('No jobs found', 'civi-framework'),
        'jobs_dashboard' => get_page_link($civi_jobs_page_id),
        'custom_field_jobs' => $custom_field_jobs,
    )
);
$form = 'submit-jobs';
$action = 'add_jobs';
$jobs_id = get_the_ID();

global $current_user, $hide_jobs_fields, $hide_jobs_group_fields;
$hide_jobs_fields = civi_get_option('hide_jobs_fields', array());
if (!is_array($hide_jobs_fields)) {
    $hide_jobs_fields = array();
}
$hide_jobs_group_fields = civi_get_option('hide_jobs_group_fields', array());
if (!is_array($hide_jobs_group_fields)) {
    $hide_jobs_group_fields = array();
}
$jobs_salary_active   = civi_get_option('enable_single_jobs_salary', '1');
if ($jobs_salary_active) {
    $layout = array('general', 'salary', 'apply', 'company', 'location', 'thumbnail', 'gallery', 'video');
} else {
    $layout = array('general', 'apply', 'company', 'location', 'thumbnail', 'gallery', 'video');
}

wp_get_current_user();
$user_id = $current_user->ID;
$paid_submission_type = civi_get_option('paid_submission_type', 'no');
$user_package_id = get_the_author_meta(CIVI_METABOX_PREFIX . 'package_id', $user_id);
$package_num_job = get_post_meta($user_package_id, CIVI_METABOX_PREFIX . 'package_number_job', true);
$package_unlimited_job = get_post_meta($user_package_id, CIVI_METABOX_PREFIX . 'package_unlimited_job', true);
$notice_text = $shortcode = '';

$civi_package = new Civi_Package();
$get_expired_date = $civi_package->get_expired_date($user_package_id, $user_id);
$current_date = date('Y-m-d');

$d1 = strtotime($get_expired_date);
$d2 = strtotime($current_date);

if ($get_expired_date === 'Never Expires' || $get_expired_date === 'Unlimited') {
    $d1 = 999999999999999999999999;
}

if ($paid_submission_type == 'no') {
    if (in_array('civi_user_candidate', (array)$current_user->roles)) {
        $notice_text = esc_html__("Sorry, you can't view this page as Candidate, register Employer account to get access.", 'civi-framework');
    }
} else {
    if (in_array('civi_user_candidate', (array)$current_user->roles)) {
        $notice_text = esc_html__("Sorry, you can't view this page as Candidate, register Employer account to get access.", 'civi-framework');
    } elseif ((in_array('civi_user_employer', (array)$current_user->roles) && $user_package_id == '') || $d1 < $d2) {
        $notice_text = esc_html__("You have not purchased the package. Please choose 1 of the packages now.", 'civi-framework');
        $shortcode = '1';
    } elseif (in_array('civi_user_employer', (array)$current_user->roles) && $package_num_job < 1 && $package_unlimited_job != '1') {
        $notice_text = esc_html__("The package you selected has reached its allowable limit. Please come back later!", 'civi-framework');
    }
}

$package_number_job = get_the_author_meta(CIVI_METABOX_PREFIX . 'package_number_job', $user_id);
$has_package = true;
if ($paid_submission_type == 'per_package') {
    $current_package_key = get_the_author_meta(CIVI_METABOX_PREFIX . 'package_key', $user_id);
    $jobs_package_key = get_post_meta($user_id, CIVI_METABOX_PREFIX . 'package_key', true);
    $civi_profile = new Civi_Profile();
    $check_package = $civi_profile->user_package_available($user_id);
    if (($check_package == -1) || ($check_package == 0)) {
        $has_package = false;
    }
}

?>

<?php if (!empty($notice_text)) { ?>
    <p class="notice"><i class="fal fa-exclamation-circle"></i><?php echo $notice_text; ?></p>
    <?php
    if ($shortcode == '1') {
        echo do_shortcode('[civi_package]');
    }
    ?>
<?php } else { ?>
    <div class="entry-my-page submit-jobs-dashboard">
        <form action="#" method="post" id="submit_jobs_form" class="form-dashboard" enctype="multipart/form-data" data-titleerror="<?php echo esc_html__('Please enter jobs name', 'civi-framework'); ?>" data-deserror="<?php echo esc_html__('Please enter jobs description', 'civi-framework'); ?>" data-caterror="<?php echo esc_html__('Please choose category', 'civi-framework'); ?>" data-typeerror="<?php echo esc_html__('Please choose type', 'civi-framework'); ?>" data-skillserror="<?php echo esc_html__('Please choose skills', 'civi-framework'); ?>">
            <div class="content-jobs">
                <div class="row">
                    <div class="col-lg-8 col-md-7">
                        <div class="submit-jobs-header civi-submit-header">
                            <div class="entry-title">
                                <h4><?php esc_html_e('Create a job post', 'civi-framework') ?></h4>
                            </div>
                            <div class="button-warpper">
                                <a href="<?php echo civi_get_permalink('jobs_dashboard'); ?>" class="civi-button button-link">
                                    <?php esc_html_e('Cancel', 'civi-framework') ?>
                                </a>
                                <?php if (($has_package && $package_number_job > 0) || $paid_submission_type !== 'per_package') { ?>
                                    <button type="submit" class="btn-submit-draft civi-button button-outline" name="submit_draft">
                                        <?php esc_html_e('Save as draft', 'civi-framework') ?>
                                        <span class="btn-loading"><i class="fal fa-spinner fa-spin large"></i></span>
                                    </button>
                                    <button type="submit" class="btn-submit-jobs civi-button" name="submit_jobs">
                                        <span><?php esc_html_e('Post job', 'civi-framework'); ?></span>
                                        <span class="btn-loading"><i class="fal fa-spinner fa-spin large"></i></span>
                                    </button>
                                <?php } else { ?>
                                    <a class="civi-button package-out-stock" href="<?php echo civi_get_permalink('package'); ?>"><?php esc_html_e('Upgrade now', 'civi-framework'); ?></a>
                                <?php } ?>
                            </div>
                        </div>
                        <?php foreach ($layout as $value) {
                            switch ($value) {
                                case 'general':
                                    $name = esc_html__('Basic info', 'civi-framework');
                                    break;
                                case 'salary':
                                    $name = esc_html__('Salary', 'civi-framework');
                                    break;
                                case 'apply':
                                    $name = esc_html__('Job apply type', 'civi-framework');
                                    break;
                                case 'company':
                                    $name = esc_html__('Company', 'civi-framework');
                                    break;
                                case 'location':
                                    $name = esc_html__('Location', 'civi-framework');
                                    break;
                                case 'thumbnail':
                                    $name = esc_html__('Cover Image', 'civi-framework');
                                    break;
                                case 'gallery':
                                    $name = esc_html__('Gallery', 'civi-framework');
                                    break;
                                case 'video':
                                    $name = esc_html__('Video', 'civi-framework');
                                    break;
                            }
                            if (!in_array($value, $hide_jobs_group_fields)) : ?>
                                <div class="block-from" id="<?php echo 'jobs-submit-' . esc_attr($value); ?>">
                                    <h6><?php echo $name ?></h6>
                                    <?php civi_get_template('jobs/submit/' . $value . '.php'); ?>
                                </div>
                        <?php endif;
                        } ?>

                        <?php $custom_field_jobs = civi_render_custom_field('jobs');
                        if (count($custom_field_jobs) > 0) : ?>
                            <div class="block-from" id="jobs-submit-additional">
                                <h6><?php echo esc_html__('Additional', 'civi-framework'); ?></h6>
                                <?php civi_get_template('jobs/submit/additional.php'); ?>
                            </div>
                        <?php endif; ?>

                        <?php wp_nonce_field('civi_submit_jobs_action', 'civi_submit_jobs_nonce_field'); ?>

                        <input type="hidden" name="jobs_form" value="<?php echo esc_attr($form); ?>" />
                        <input type="hidden" name="jobs_action" value="<?php echo esc_attr($action) ?>" />
                        <input type="hidden" name="jobs_id" value="<?php echo esc_attr($jobs_id); ?>" />
                    </div>
                    <div class="col-lg-4 col-md-5">
                        <div class="widget-area-init has-sticky">
                            <h3 class="title-jobs-about"><?php esc_html_e('About this job', 'civi-framework') ?></h3>
                            <div class="about-jobs-dashboard block-archive-sidebar">
                                <div class="img-company"><i class="far fa-camera"></i></div>
                                <h4 class="title-about" data-title="<?php esc_attr_e('Title of job', 'civi-framework') ?>"><?php esc_html_e('Title of job', 'civi-framework') ?></h4>
                                <div class="info-jobs-warpper">
                                    <?php esc_html_e('Company: ', 'civi-framework'); ?>
                                    <span class="name-company" data-name="<?php esc_attr_e('Company Name', 'civi-framework') ?>"><?php esc_html_e('Company Name', 'civi-framework'); ?></span>
																		<br />
                                    <?php esc_html_e('Category: ', 'civi-framework'); ?>
                                    <span class="cate-about" data-cate="<?php esc_attr_e('Category', 'civi-framework') ?>"><?php esc_html_e('Category', 'civi-framework'); ?></span>
                                    <div class="label-warpper">
                                        <span class="label-type-inner"></span>
                                        <span class="label-location-inner"></span>
                                    </div>
									<?php
										if( $jobs_salary_active ){
											echo '<div class="label label-price" data-text-min="' . esc_attr__('Minimum:', 'civi-framework') . '" data-text-max="' .esc_attr__('Maximum:', 'civi-framework') .'" data-text-agree="' . esc_attr_e('Negotiable Price', 'civi-framework') .'"></div>';
										}
									?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    </form>
<?php } ?>
