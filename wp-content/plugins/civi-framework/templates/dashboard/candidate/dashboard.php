<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if (!is_user_logged_in()) {
    civi_get_template('global/access-denied.php', array('type' => 'not_login'));
    return;
}
global $current_user;
$user_id   = $current_user->ID;
$user_name = $current_user->display_name;

wp_enqueue_script('chart');
wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'chart');
$number_days = '7';
$labels = array();
for ($i = $number_days; $i >= 0; $i--) {
    $date = strtotime(date("Y-m-d", strtotime("-" . $i . " day")));
    $labels[] = date('M j, Y', $date);
}

$args = array(
    'post_type'           => 'applicants',
    'ignore_sticky_posts' => 1,
    'posts_per_page'      => 5,
    'post_status'         => 'publish',
    'orderby'             => 'date',
    'author'              => $user_id,
);
$data = new WP_Query($args);
?>
<div class="civi-dashboard area-main-control">
    <div class="entry-my-page">
        <h2 class="entry-title"><?php echo sprintf(__('Welcome back! %s', 'civi-framework'), $user_name); ?></h2>
        <div class="total-action">
            <ul class="action-wrapper row">
                <li class="col-xl-3 col-sm-6">
                    <div class="jobs civi-boxdb">
                        <div class="entry-detai ">
                            <h3 class="entry-title"><?php esc_html_e('Applied Jobs', 'civi-framework'); ?></h3>
                            <span class="entry-number"><?php echo civi_total_my_apply(); ?></span>
                        </div>
                        <div class="icon-total">
                            <img src="<?php echo esc_attr(CIVI_PLUGIN_URL . 'assets/images/icon-dashboard-04.svg'); ?>" alt="<?php esc_attr_e('jobs', 'civi-framework'); ?>">
                        </div>
                        <a href="<?php echo civi_get_permalink('my_jobs'); ?>" target="_blank" class="civi-link-boxdb"></a>
                    </div>
                </li>
				<?php if( civi_total_post('company', 'my_follow') > 0 ) { ?>
                <li class="col-xl-3 col-sm-6">
                    <div class="applications civi-boxdb">
                        <div class="entry-detai ">
                            <h3 class="entry-title"><?php esc_html_e('My Following', 'civi-framework'); ?></h3>
                            <span class="entry-number"><?php echo civi_total_post('company', 'my_follow') ?></span>
                        </div>
                        <div class="icon-total">
                            <img src="<?php echo esc_attr(CIVI_PLUGIN_URL . 'assets/images/icon-dashboard-06.svg'); ?>" alt="<?php esc_attr_e('applications', 'civi-framework'); ?>">
                        </div>
                        <a href="<?php echo civi_get_permalink('candidate_company'); ?>" target="_blank" class="civi-link-boxdb"></a>
                    </div>
                </li>
				<?php } ?>
				<?php if(civi_get_total_reviews() > 0 ) { ?>
                <li class="col-xl-3 col-sm-6">
                    <div class="interviews civi-boxdb">
                        <div class="entry-detai ">
                            <h3 class="entry-title"><?php esc_html_e('MY Reviews', 'civi-framework'); ?></h3>
                            <span class="entry-number"><?php echo civi_get_total_reviews(); ?></span>
                        </div>
                        <div class="icon-total">
                            <img src="<?php echo esc_attr(CIVI_PLUGIN_URL . 'assets/images/icon-dashboard-03.svg'); ?>" alt="<?php esc_attr_e('interviews', 'civi-framework'); ?>">
                        </div>
                        <a href="<?php echo civi_get_permalink('candidate_reviews'); ?>" target="_blank" class="civi-link-boxdb"></a>
                    </div>
                </li>
				<?php } ?>
                <?php if(civi_total_meeting('candidate') > 0 ) { ?>
                <li class="col-xl-3 col-sm-6">
                    <div class="candidates civi-boxdb">
                        <div class="entry-detail">
                            <h3 class="entry-title"><?php esc_html_e('Meetings', 'civi-framework'); ?></h3>
                            <span class="entry-number"><?php echo civi_total_meeting('candidate') ?></span>
                        </div>
                        <div class="icon-total">
                            <img src="<?php echo esc_attr(CIVI_PLUGIN_URL . 'assets/images/icon-dashboard-02.svg'); ?>" alt="<?php esc_attr_e('candidates', 'civi-framework'); ?>">
                        </div>
                        <a href="<?php echo civi_get_permalink('candidate_meetings'); ?>" target="_blank" class="civi-link-boxdb"></a>
                    </div>
                </li>
                <?php } ?>
            </ul>
        </div>
        <div class="notification-dashboard">
            <div class="row">
                <div class="col-md-7">
                    <div class="civi-chart-warpper civi-chart-candidate">
                        <div class="chart-header">
                            <h4 class="title-chart"><?php esc_html_e('Your Profile Views', 'civi-framework'); ?></h4>
                            <div class="form-chart">
                                <div class="select2-field">
									<select name="chart_candidate" class="civi-select2">
										<option value="7"><?php esc_html_e('7 days', 'civi-framework'); ?></option>
										<option value="15"><?php esc_html_e('15 days', 'civi-framework'); ?></option>
										<option value="30"><?php esc_html_e('30 days', 'civi-framework'); ?></option>
									</select>
								</div>
                            </div>
                        </div>
                        <canvas id="civi-dashboard_candidate" data-labels="<?php echo esc_attr(json_encode($labels)); ?>" data-values="<?php echo esc_attr(json_encode(civi_total_view_candidate($number_days))); ?>" data-label="<?php esc_attr_e('Your Profile Views', 'civi-framework'); ?>">
                        </canvas>
                        <div class="civi-loading-effect"><span class="civi-dual-ring"></span></div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="jobs-dashboard-wrap">
                        <h4 class="title-jobs"><?php esc_html_e('Jobs Applied Recently', 'civi-framework'); ?></h4>
                        <div class="jobs-innner">
                            <?php if ($data->have_posts()) { ?>
                                <div class="jobs-content">
                                    <?php while ($data->have_posts()) : $data->the_post();
                                        $applicants_id = get_the_ID();
                                        $jobs_id = get_post_meta($applicants_id, CIVI_METABOX_PREFIX . 'applicants_jobs_id');
                                        if (!empty($jobs_id)) {
                                            $jobs_id = intval($jobs_id[0]);
                                        }
                                        global $current_user;
                                        wp_get_current_user();
                                        $user_id = $current_user->ID;
                                        $jobs_type = wp_get_post_terms($jobs_id, 'jobs-type');
                                        $jobs_categories =  wp_get_post_terms($jobs_id, 'jobs-categories');
                                        $jobs_location =  wp_get_post_terms($jobs_id, 'jobs-location');
                                        $jobs_select_company    = get_post_meta($jobs_id, CIVI_METABOX_PREFIX . 'jobs_select_company');
                                        $company_id = isset($jobs_select_company[0]) ? $jobs_select_company[0] : '';
                                        $company_logo   = get_post_meta($company_id, CIVI_METABOX_PREFIX . 'company_logo');
                                        $public_date = get_the_date('Y-m-d');
                                    ?>
                                        <div class="company-header">
                                            <div class="img-comnpany">
                                                <?php if (!empty($company_logo[0]['url'])) : ?>
                                                    <img class="logo-comnpany" src="<?php echo $company_logo[0]['url'] ?>" alt="" />
                                                <?php else : ?>
                                                    <div class="logo-comnpany"><i class="far fa-camera"></i></div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="info-jobs">
                                                <h3 class="title-jobs-dashboard">
                                                    <a href="<?php echo get_permalink($jobs_id); ?>" target="_blank">
                                                        <?php echo get_the_title($applicants_id); ?>
                                                        <i class="fas fa-external-link-alt"></i>
                                                    </a>
                                                </h3>
                                                <div>
                                                    <?php if (is_array($jobs_categories)) {
                                                        foreach ($jobs_categories as $categories) { ?>
                                                            <?php esc_html_e($categories->name); ?>
                                                    <?php }
                                                    } ?>
                                                    <?php if (is_array($jobs_type)) {
                                                        foreach ($jobs_type as $type) { ?>
                                                            <?php esc_html_e('/ ' . $type->name); ?>
                                                    <?php }
                                                    } ?>
                                                    <?php if (is_array($jobs_location)) {
                                                        foreach ($jobs_location as $location) { ?>
                                                            <?php esc_html_e('/ ' . $location->name); ?>
                                                    <?php }
                                                    } ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            <?php } else { ?>
                                <div class="item-not-found"><?php esc_html_e('No item found', 'civi-framework'); ?></div>
                            <?php } ?>
                        </div>
                        <a href="<?php echo esc_url(civi_get_permalink('my_jobs')) ?>" class="civi-button button-block button-outline button-rounded"><?php esc_html_e('All Applied', 'civi-framework'); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
