<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if (!is_user_logged_in()) {
    civi_get_template('global/access-denied.php', array('type' => 'not_login'));
    return;
}
global $current_user;
$user_id = $current_user->ID;
$user_name = $current_user->display_name;

wp_enqueue_script('chart');
wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'chart');
$number_days = '7';
$labels = array();
for ($i = $number_days; $i >= 0; $i--) {
    $date = strtotime(date("Y-m-d", strtotime("-" . $i . " day")));
    $labels[] = date('M j, Y', $date);
}

$args_jobs = array(
    'post_type' => 'jobs',
    'post_status' => 'publish',
    'ignore_sticky_posts' => 1,
    'posts_per_page' => -1,
    'author' => $user_id,
    'orderby' => 'date',
);
$data_jobs = new WP_Query($args_jobs);
$jobs_employer_id = array();
if ($data_jobs->have_posts()) {
    while ($data_jobs->have_posts()) : $data_jobs->the_post();
        $jobs_employer_id[] = get_the_ID();
    endwhile;
}
?>
<div class="civi-dashboard area-main-control">
    <div class="entry-my-page">
        <h2 class="entry-title"><?php echo sprintf(__('Welcome back! %s', 'civi-framework'), $user_name); ?></h2>
        <div class="total-action">
            <ul class="action-wrapper row">
                <li class="col-xl-3 col-sm-6">
                    <div class="jobs civi-boxdb">
                        <div class="entry-detai ">
                            <h3 class="entry-title"><?php esc_html_e('Posted Jobs', 'civi-framework'); ?></h3>
                            <span class="entry-number"><?php echo civi_total_actived_jobs(); ?></span>
                        </div>
                        <div class="icon-total">
                            <img src="<?php echo esc_attr(CIVI_PLUGIN_URL . 'assets/images/icon-dashboard-01.svg'); ?>"
                                 alt="<?php esc_attr_e('jobs', 'civi-framework'); ?>">
                        </div>
                        <a href="<?php echo civi_get_permalink('jobs_dashboard'); ?>" target="_blank" class="civi-link-boxdb"></a>
                    </div>
                </li>
                <li class="col-xl-3 col-sm-6">
                    <div class="applications civi-boxdb">
                        <div class="entry-detai ">
                            <h3 class="entry-title"><?php esc_html_e('Applicants', 'civi-framework'); ?></h3>
                            <span class="entry-number"><?php echo civi_total_applications_jobs(); ?></span>
                        </div>
                        <div class="icon-total">
                            <img src="<?php echo esc_attr(CIVI_PLUGIN_URL . 'assets/images/icon-dashboard-07.svg'); ?>"
                                 alt="<?php esc_attr_e('applications', 'civi-framework'); ?>">
                        </div>
                        <a href="<?php echo civi_get_permalink('applicants'); ?>" target="_blank" class="civi-link-boxdb"></a>
                    </div>
                </li>
								<?php if(civi_total_meeting('candidate') > 0 ) { ?>
                <li class="col-xl-3 col-sm-6">
                    <div class="interviews civi-boxdb">
                        <div class="entry-detai ">
                            <h3 class="entry-title"><?php esc_html_e('MEETINGS', 'civi-framework'); ?></h3>
                            <span class="entry-number"><?php echo civi_total_meeting('employer'); ?></span>
                        </div>
                        <div class="icon-total">
                            <img src="<?php echo esc_attr(CIVI_PLUGIN_URL . 'assets/images/icon-dashboard-06.svg'); ?>"
                                 alt="<?php esc_attr_e('meetings', 'civi-framework'); ?>">
                        </div>
                        <a href="<?php echo civi_get_permalink('meetings'); ?>" target="_blank" class="civi-link-boxdb"></a>
                    </div>
                </li>
								<?php } ?>
                <li class="col-xl-3 col-sm-6">
                    <div class="candidates civi-boxdb">
                        <div class="entry-detail">
                            <h3 class="entry-title"><?php esc_html_e('My Follow', 'civi-framework'); ?></h3>
                            <span class="entry-number"><?php echo civi_total_post('candidate','follow_candidate'); ?></span>
                        </div>
                        <div class="icon-total">
                            <img src="<?php echo esc_attr(CIVI_PLUGIN_URL . 'assets/images/icon-dashboard-05.svg'); ?>"
                                 alt="<?php esc_attr_e('candidates', 'civi-framework'); ?>">
                        </div>
                        <a href="<?php echo civi_get_permalink('candidates'); ?>" target="_blank" class="civi-link-boxdb"></a>
                    </div>
                </li>
            </ul>
        </div>
        <div class="notification-dashboard">
            <div class="row">
                <div class="col-md-7">
                    <div class="civi-chart-warpper civi-chart-employer">
                        <div class="chart-header">
                            <h4 class="title-chart"><?php esc_html_e('Page views', 'civi-framework'); ?></h4>
                            <div class="form-chart">
                                <div class="select2-field">
									<select name="chart_employer" class="civi-select2">
										<option value="7"><?php esc_html_e('7 days', 'civi-framework'); ?></option>
										<option value="15"><?php esc_html_e('15 days', 'civi-framework'); ?></option>
										<option value="30"><?php esc_html_e('30 days', 'civi-framework'); ?></option>
									</select>
								</div>
                            </div>
                        </div>
                        <canvas id="civi-dashboard_employer" data-labels="<?php echo esc_attr(json_encode($labels)); ?>"
                                data-values="<?php echo esc_attr(json_encode(civi_total_view_jobs($number_days))); ?>"
                                data-label="<?php esc_attr_e('Page View', 'civi-framework'); ?>">
                        </canvas>
                        <div class="civi-loading-effect"><span class="civi-dual-ring"></span></div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="applicants-wrap">
                        <h4 class="title-applicants"><?php esc_html_e('New applicants', 'civi-framework'); ?></h4>
                        <div class="applicants-innner">
                            <div class="applicants-heading">
                                <?php
                                $args_title_one = array(
                                    'post_type' => 'applicants',
                                    'ignore_sticky_posts' => 1,
                                    'posts_per_page' => 1,
                                    'orderby' => 'date',
                                    'meta_query' => array(
                                        'relation' => 'AND',
                                        array(
                                            'key' => CIVI_METABOX_PREFIX . 'applicants_jobs_id',
                                            'value' => $jobs_employer_id,
                                            'compare' => 'IN'
                                        )
                                    ),
                                );
                                $data_title_one = new WP_Query($args_title_one);
                                $id_jobs_one = array();
                                if ($data_title_one->have_posts()) {
                                    while ($data_title_one->have_posts()) : $data_title_one->the_post();
                                        $id = get_the_ID();
                                        $id_jobs_one = get_post_meta($id, CIVI_METABOX_PREFIX . 'applicants_jobs_id');
                                        if (!empty($id_jobs_one)) {
                                            $id_jobs_one = $id_jobs_one[0];
                                        }
                                    endwhile;
                                } ?>
                                <?php if (!empty($id_jobs_one)) {
                                    $id_jobs_one = intval($id_jobs_one);
                                } ?>
                                <?php if (!empty(($id_jobs_one) && !empty($jobs_employer_id))) : ?>
                                    <h3><?php esc_html_e(get_the_title($id_jobs_one)); ?></h3>
                                    <span><?php echo civi_total_applications_jobs_id($id_jobs_one) ?></span>
                                <?php endif; ?>
                            </div>
                            <?php $args_applicants_one = array(
                                'post_type' => 'applicants',
                                'ignore_sticky_posts' => 1,
                                'posts_per_page' => 2,
                                'orderby' => 'date',
                                'meta_query' => array(
                                    'relation' => 'AND',
                                    array(
                                        'key' => CIVI_METABOX_PREFIX . 'applicants_jobs_id',
                                        'value' => $id_jobs_one,
                                        'compare' => '='
                                    )
                                ),
                            );
                            $data_applicants_one = new WP_Query($args_applicants_one);
                            if ($data_applicants_one->have_posts() && !empty($jobs_employer_id)) {
                                while ($data_applicants_one->have_posts()) : $data_applicants_one->the_post();
                                    $id = get_the_ID();
                                    $jobs_id = get_post_meta($id, CIVI_METABOX_PREFIX . 'applicants_jobs_id');
                                    $public_date = get_the_date('Y-m-d');
                                    $author_id = get_post_field( 'post_author', $id );
                                    $candidate_avatar = get_the_author_meta( 'author_avatar_image_url', $author_id );
                                    ?>
                                    <div class="applicants-content">
                                        <?php if (!empty($candidate_avatar)) : ?>
                                            <div class="image-applicants"><img src="<?php echo esc_url($candidate_avatar) ?>" alt="" /></div>
                                        <?php else : ?>
                                            <div class="image-applicants"><i class="far fa-camera"></i></div>
                                        <?php endif; ?>
                                        <?php if (!empty(get_the_title())) { ?>
                                            <div class="content">
                                                <?php if (!empty(get_the_author())) : ?>
                                                    <h6><?php esc_html_e(get_the_author()); ?></h6>
                                                <?php endif; ?>
                                                <p><?php esc_html_e('Applied date :', 'civi-framework') ?><?php echo $public_date ?></p>
                                            </div>
                                        <?php } ?>
                                    </div>
                                <?php endwhile; ?>
                            <?php } else { ?>
                                <div class="item-not-found"><?php esc_html_e('No item found', 'civi-framework'); ?></div>
                            <?php } ?>
                        </div>
                        <div class="applicants-innner">
                            <div class="applicants-heading">
                                <?php
                                $args_title_two = array(
                                    'post_type' => 'applicants',
                                    'ignore_sticky_posts' => 1,
                                    'posts_per_page' => 1,
                                    'orderby' => 'date',
                                    'meta_query' => array(
                                        'relation' => 'AND',
                                        array(
                                            'key' => CIVI_METABOX_PREFIX . 'applicants_jobs_id',
                                            'value' => $jobs_employer_id,
                                            'compare' => 'IN'
                                        ),
                                        array(
                                            'key' => CIVI_METABOX_PREFIX . 'applicants_jobs_id',
                                            'value' => $id_jobs_one,
                                            'compare' => '!='
                                        )
                                    ),
                                );
                                $data_title_two = new WP_Query($args_title_two);
                                $id_jobs_two = array();
                                if ($data_title_two->have_posts()) {
                                    while ($data_title_two->have_posts()) : $data_title_two->the_post();
                                        $id = get_the_ID();
                                        $id_jobs_two = get_post_meta($id, CIVI_METABOX_PREFIX . 'applicants_jobs_id');
                                        if (!empty($id_jobs_two)) {
                                            $id_jobs_two = $id_jobs_two[0];
                                        }
                                    endwhile;
                                } ?>

                                <?php if (!empty($id_jobs_two)) {
                                    $id_jobs_two = intval($id_jobs_two);
                                } ?>
                                <?php if (!empty(($id_jobs_two) && !empty($jobs_employer_id))) : ?>
                                    <h3><?php esc_html_e(get_the_title($id_jobs_two)); ?></h3>
                                    <span><?php echo civi_total_applications_jobs_id($id_jobs_two) ?></span>
                                <?php endif; ?>
                            </div>
                            <?php $args_applicants_two = array(
                                'post_type' => 'applicants',
                                'ignore_sticky_posts' => 1,
                                'posts_per_page' => 2,
                                'orderby' => 'date',
                                'meta_query' => array(
                                    'relation' => 'AND',
                                    array(
                                        'key' => CIVI_METABOX_PREFIX . 'applicants_jobs_id',
                                        'value' => $id_jobs_two,
                                        'compare' => '='
                                    ),
                                    array(
                                        'key' => CIVI_METABOX_PREFIX . 'applicants_jobs_id',
                                        'value' => $id_jobs_one,
                                        'compare' => '!='
                                    )
                                ),
                            );
                            $data_applicants_two = new WP_Query($args_applicants_two);
                            if ($data_applicants_two->have_posts() && !empty($jobs_employer_id) && !empty($id_jobs_two)) {
                                while ($data_applicants_two->have_posts()) : $data_applicants_two->the_post();
                                    $id = get_the_ID();
                                    $jobs_id = get_post_meta($id, CIVI_METABOX_PREFIX . 'applicants_jobs_id');
                                    $public_date = get_the_date('Y-m-d');
                                    $author_id = get_post_field( 'post_author', $id );
                                    $candidate_avatar = get_the_author_meta( 'author_avatar_image_url', $author_id );
                                    ?>
                                    <div class="applicants-content">
                                        <?php if (!empty($candidate_avatar)) : ?>
                                            <div class="image-applicants"><img src="<?php echo esc_url($candidate_avatar) ?>" alt="" /></div>
                                        <?php else : ?>
                                            <div class="image-applicants"><i class="far fa-camera"></i></div>
                                        <?php endif; ?>
                                        <?php if (!empty(get_the_title())) { ?>
                                            <div class="content">
                                                <?php if (!empty(get_the_author())) : ?>
                                                    <h6><?php esc_html_e(get_the_author()); ?></h6>
                                                <?php endif; ?>
                                                <p><?php esc_html_e('Applied date :', 'civi-framework') ?><?php echo $public_date ?></p>
                                            </div>
                                        <?php } ?>
                                    </div>
                                <?php endwhile; ?>
                            <?php } ?>
                        </div>
                        <a href="<?php echo esc_url(civi_get_permalink('applicants')) ?>"
                           class="civi-button button-outline button-rounded"><?php esc_html_e('All applicants', 'civi-framework'); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
