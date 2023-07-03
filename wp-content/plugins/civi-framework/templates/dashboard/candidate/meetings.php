<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'meetings');
wp_localize_script(
    CIVI_PLUGIN_PREFIX . 'meetings',
    'civi_meetings_vars',
    array(
        'ajax_url' => CIVI_AJAX_URL,
        'not_meetings' => esc_html__('No meetings found', 'civi-framework'),
    )
);
global $current_user;
$user_id = $current_user->ID;
$action = 'edit-meetings';
$posts_per_page = 9;

$args_applicants = array(
    'post_type' => 'applicants',
    'ignore_sticky_posts' => 1,
    'posts_per_page' => -1,
    'post_status' => 'publish',
    'author' => $user_id,
);
$data_applicants = new WP_Query($args_applicants);
$applicants_id = array();
if ($data_applicants->have_posts()) {
    while ($data_applicants->have_posts()) : $data_applicants->the_post();
        $applicants_id[] = get_the_ID();
    endwhile;
}

$args = array(
    'post_type' => 'meetings',
    'post_status' => 'publish',
    'posts_per_page' => $posts_per_page,
    'offset' => (max(1, get_query_var('paged')) - 1) * $posts_per_page,
    'orderby' => 'date',
    'meta_query' => array(
        'relation' => 'AND',
        array(
            'key' => CIVI_METABOX_PREFIX . 'meeting_applicants_id',
            'value' => $applicants_id,
            'compare' => 'IN'
        ),
        array(
            'key' => CIVI_METABOX_PREFIX . 'meeting_status',
            'value' => 'completed',
            'compare' => '!='
        )
    ),
);
$data = new WP_Query($args);
?>

<div class="entry-my-page meetings-dashboard mettings-action-dashboard">
    <div class="entry-title">
        <h4><?php esc_html_e('Meetings', 'civi-framework') ?></h4>
    </div>
    <div class="meetings-dashboard-candidate">
        <?php if ($data->have_posts() && !empty($applicants_id)) { ?>
            <div class="row">
                <?php while ($data->have_posts()) : $data->the_post();
                    $meeting_id = get_the_ID();
                    $user_id = get_post_meta($meeting_id, CIVI_METABOX_PREFIX . 'meeting_user_employer_id', true);
                    $user_by = get_user_by('id', $user_id);
                    $meeting_date = get_post_meta($meeting_id, CIVI_METABOX_PREFIX . 'meeting_date', true);
                    $meeting_time = get_post_meta($meeting_id, CIVI_METABOX_PREFIX . 'meeting_time', true);
                    $meeting_time_duration = get_post_meta($meeting_id, CIVI_METABOX_PREFIX . 'meeting_time_duration', true);
                    $meeting_message = get_post_meta($meeting_id, CIVI_METABOX_PREFIX . 'meeting_message', true);
                    $zoom_link = get_user_meta($user_id, CIVI_METABOX_PREFIX . 'metting_zoom_link', true);
                    $zoom_pw = get_user_meta($user_id, CIVI_METABOX_PREFIX . 'metting_zoom_pw', true);
                    $current_date = date('Y-m-d');
                    $time_date_current = date('H:i');
                    $time_date_current = get_date_from_gmt($time_date_current, 'H:i');
                    $time_date_end = date('H:i', strtotime('+' . $meeting_time_duration . 'minutes', strtotime($meeting_time)));
                    if (!empty($user_id)) {
                        $meeting_with = $user_by->display_name;
                    }
                ?>
                    <div class="col-md-4 col-sm-6">
                        <div class="meetings-warpper">
                            <div class="meetings-top">
                                <?php if (strtotime($meeting_date) < strtotime($current_date)) : ?>
                                    <span class="label label-close"><?php esc_html_e('Expired', 'civi-framework') ?></span>
                                <?php endif; ?>
                                <?php if (strtotime($meeting_date) == strtotime($current_date) && $time_date_current >= $meeting_time && $time_date_current <= $time_date_end) : ?>
                                    <p class="calendar is-active">
                                        <i class="fal fa-calendar-alt"></i><?php esc_html_e('Today', 'civi-framework') ?>
                                        <span class="dot">.</span> <?php esc_html_e($meeting_time) ?>
                                    </p>
                                <?php else : ?>
                                    <p class="calendar">
                                        <i class="fal fa-calendar-alt"></i><?php esc_html_e($meeting_date) ?>
                                        <span class="dot">.</span> <?php esc_html_e($meeting_time) ?>
                                    </p>
                                <?php endif; ?>
                                <h6><?php echo get_the_title(); ?></h6>
                                <?php if (!empty($meeting_with)) : ?>
                                    <p class="meeting-width">
                                        <?php esc_html_e('Meeting with:', 'civi-framework'); ?>
                                        <span class="athour"><?php esc_html_e($meeting_with) ?></span>
                                    </p>
                                <?php endif; ?>
                                <p class="meeting_message"><?php esc_html_e($meeting_message) ?></p>
                            </div>
                            <div class="meetings-bottom">
                                <span class="social zoom">
                                    <i class="fas fa-video-plus"></i>
                                    <span><?php esc_html_e('Via Zoom', 'civi-framework'); ?></span>
                                </span>
                                <span class="social time">
                                    <i class="far fa-clock"></i>
                                    <span><?php esc_html_e($meeting_time_duration) ?> <?php esc_html_e('Minutes', 'civi-framework'); ?></span>
                                </span>
                                <div class="action action-setting">
                                    <a href="#" class="icon-setting"><i class="fal fa-ellipsis-v"></i></a>
                                    <ul class="action-dropdown">
                                        <li><a class="btn-edit btn-reschedule-meetings" data-id="<?php echo esc_attr($meeting_id); ?>" href="#"><?php esc_html_e('Edit', 'civi-framework') ?></a></li>
                                    </ul>
                                </div>
                            </div>
                            <?php if (strtotime($meeting_date) == strtotime($current_date) && $time_date_current >= $meeting_time && $time_date_current <= $time_date_end) : ?>
                                <div class="meeting-info-settings">
                                    <div class="meeting-zoom-link">
                                        <span><?php esc_html_e('Start meeting:', 'civi-framework'); ?></span>
                                        <a href="<?php echo esc_url($zoom_link) ?>"><?php esc_html_e('Here', 'civi-framework'); ?></a>
                                    </div>
                                    <div class="meeting-zoom-pw">
                                        <span><?php esc_html_e('Password:', 'civi-framework'); ?></span>
                                        <?php esc_html_e($zoom_pw) ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php } else { ?>
            <div class="item-not-found"><?php esc_html_e('No item found', 'civi-framework'); ?></div>
        <?php } ?>
        <div class="civi-loading-effect"><span class="civi-dual-ring"></span></div>
        <?php $max_num_upcoming = $data->max_num_pages;
        $total_post_upcoming = $data->found_posts;
        if ($total_post_upcoming > $posts_per_page && !empty($applicants_id)) { ?>
            <div class="pagination-dashboard">
                <?php civi_get_template('global/pagination.php', array('total_post' => $total_post_upcoming, 'max_num_pages' => $max_num_upcoming, 'layout' => 'number'));
                wp_reset_postdata(); ?>
            </div>
        <?php } ?>
        <input type="hidden" name="mettings_action" value="<?php echo esc_attr($action) ?>" />
    </div>
</div>