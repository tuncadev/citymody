<?php
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('Civi_Meetings')) {
    /**
     * Class Civi_Meetings
     */
    class Civi_Meetings
    {
        /**
         * Meetings Settings
         */
        public function civi_meetings_settings()
        {
            $link = isset($_REQUEST['link']) ? civi_clean(wp_unslash($_REQUEST['link'])) : '';
            $password = isset($_REQUEST['password']) ? civi_clean(wp_unslash($_REQUEST['password'])) : '';

            global $current_user;
            $user_id = $current_user->ID;

            if (!empty($link)) (update_user_meta($user_id, CIVI_METABOX_PREFIX . 'metting_zoom_link', $link)
            );

            if (!empty($password)) (update_user_meta($user_id, CIVI_METABOX_PREFIX . 'metting_zoom_pw', $password)
            );

            if ($link !== '' || $password !== '') {
                echo json_encode(array('success' => true));
            } else {
                echo json_encode(array('success' => false));
            }

            wp_die();
        }

        /**
         * Meetings Reschedule
         */
        public function civi_meetings_reschedule_ajax()
        {
            $applicants_id = isset($_REQUEST['applicants_id']) ? civi_clean(wp_unslash($_REQUEST['applicants_id'])) : '';
            $date = isset($_REQUEST['date']) ? civi_clean(wp_unslash($_REQUEST['date'])) : '';
            $message = isset($_REQUEST['message']) ? civi_clean(wp_unslash($_REQUEST['message'])) : '';
            $time = isset($_REQUEST['time']) ? civi_clean(wp_unslash($_REQUEST['time'])) : '';
            $timeduration = isset($_REQUEST['timeduration']) ? civi_clean(wp_unslash($_REQUEST['timeduration'])) : '';
            $action_metting = isset($_REQUEST['action_metting']) ? civi_clean(wp_unslash($_REQUEST['action_metting'])) : '';

            if ($date == '' || $message == '' || $time == '' || $timeduration == '') {
                echo json_encode(array('added' => false, 'success' => false, 'message' => esc_html__('Please fill all form fields', 'civi-framework')));
                wp_die();
            }

            // Check Time
            $date_meeting = $date;
            $time_from_start = $time;
            $time_form_duration = $timeduration;
            $current_date = date('Y-m-d');
            $time_from_end = date('H:i', strtotime('+' . $time_form_duration . 'minutes', strtotime($time_from_start)));
            $time_date_current = date('H:i');
            $time_date_current = get_date_from_gmt($time_date_current, 'H:i');

            if ((strtotime($date_meeting) < strtotime($current_date)) || (strtotime($date_meeting) == strtotime($current_date) && strtotime($time_from_end) < strtotime($time_date_current))){
                echo json_encode(array('added' => false, 'success' => false, 'message' => esc_html__('Your time is smaller than the current time', 'civi-framework')));
                wp_die();
            }

            $args_meeting_time = array(
                'post_type' => 'meetings',
                'ignore_sticky_posts' => 1,
                'posts_per_page' => -1,
                'post_status' => 'publish',
                'meta_query' => array(
                    array(
                        'key' => CIVI_METABOX_PREFIX . 'meeting_date',
                        'value' => $date_meeting,
                        'compare' => '=',
                    )
                )
            );

            $check_time = array();
            $data_meeting_time = new WP_Query($args_meeting_time);
            if ($data_meeting_time->have_posts()) {
                while ($data_meeting_time->have_posts()) : $data_meeting_time->the_post();
                    $id_time = get_the_ID();
                    $time_duration = get_post_meta($id_time, CIVI_METABOX_PREFIX . 'meeting_time_duration', true);
                    $time_date_start = get_post_meta($id_time, CIVI_METABOX_PREFIX . 'meeting_time', true);
                    $time_date_end = date('H:i', strtotime('+' . $time_duration . 'minutes', strtotime($time_date_start)));

                    if (($time_from_start >= $time_date_start && $time_from_start <= $time_date_end)
                        || $time_from_end >= $time_date_start && $time_from_end <= $time_date_end) {
                        $check_time[] = '1';
                    } else {
                        $check_time[] = '0';
                    }
                endwhile;
            }

            if(is_array($check_time) && in_array('1',$check_time)){
                echo json_encode(array('added' => false, 'success' => false, 'message' => esc_html__('Same time and date in another call you made', 'civi-framework')));
                wp_die();
            }

            // New meetings
            $new_meetings = array(
                'post_type' => 'meetings',
                'post_status' => 'publish',
            );
            $meetings_title = get_the_title($applicants_id);
            if (isset($meetings_title)) {
                $new_meetings['post_title'] = $meetings_title;
            }
            if (!empty($new_meetings['post_title'])) {
                if ($action_metting == 'submit-meetings') {
                    $meetings_id = wp_insert_post($new_meetings, true);
                    global $current_user;
                    $user_id = $current_user->ID;
                    $my_meetings = get_post($applicants_id);
                    $user_ID = $my_meetings->post_author;
                    $user_by = get_user_by('id', $user_ID);
                    $meeting_width = $user_by->display_name;
                    if (isset($applicants_id)) {
                        update_post_meta($meetings_id, CIVI_METABOX_PREFIX . 'meeting_applicants_id', $applicants_id);
                    }
                    if (isset($user_id)) {
                        update_post_meta($meetings_id, CIVI_METABOX_PREFIX . 'meeting_user_employer_id', $user_id);
                    }
                    if (isset($meeting_width)) {
                        update_post_meta($meetings_id, CIVI_METABOX_PREFIX . 'meeting_with', $meeting_width);
                    }
                    if (isset($user_ID)) {
                        update_post_meta($meetings_id, CIVI_METABOX_PREFIX . 'meeting_candidate_user_id', $user_ID);
                    }
                    update_post_meta($meetings_id, CIVI_METABOX_PREFIX . 'meeting_status', 'upcoming');

                    //Notification
                    $user_receive_mee = get_post_field('post_author', $applicants_id);
                    $jobs_id = get_post_meta($applicants_id, CIVI_METABOX_PREFIX . 'applicants_jobs_id', true);
                    update_post_meta($meetings_id, CIVI_METABOX_PREFIX . 'mee_jobs_id',$jobs_id);
                    update_post_meta($meetings_id, CIVI_METABOX_PREFIX . 'user_receive_mee',$user_receive_mee);
                    civi_get_data_ajax_notification($meetings_id,'add-meeting');

                } elseif ($action_metting == 'edit-meetings') {
                    $meetings_id = absint(wp_unslash($applicants_id));
                    $new_meetings['ID'] = intval($meetings_id);
                    $meetings_id = wp_update_post($new_meetings);
                }
            }

            if (isset($date)) {
                update_post_meta($meetings_id, CIVI_METABOX_PREFIX . 'meeting_date', $date);
            }

            if (isset($message)) {
                update_post_meta($meetings_id, CIVI_METABOX_PREFIX . 'meeting_message', $message);
            }

            if (isset($time)) {
                update_post_meta($meetings_id, CIVI_METABOX_PREFIX . 'meeting_time', $time);
            }

            if (isset($timeduration)) {
                update_post_meta($meetings_id, CIVI_METABOX_PREFIX . 'meeting_time_duration', $timeduration);
            }

            echo json_encode(array('added' => true, 'success' => true, 'message' => esc_html__('You have meetings successfully', 'civi-framework')));

            wp_die();
        }

        /**
         * Meetings Upcoming Dashboard
         */
        public function civi_meetings_upcoming_dashboard()
        {
            $item_amount = isset($_REQUEST['item_amount']) ? civi_clean(wp_unslash($_REQUEST['item_amount'])) : '9';
            $paged = isset($_REQUEST['paged']) ? civi_clean(wp_unslash($_REQUEST['paged'])) : '1';
            $item_id = isset($_REQUEST['item_id']) ? civi_clean(wp_unslash($_REQUEST['item_id'])) : '';
            $action_click = isset($_REQUEST['action_click']) ? civi_clean(wp_unslash($_REQUEST['action_click'])) : '';
            $page = isset($_REQUEST['paged']) ? civi_clean(wp_unslash($_REQUEST['paged'])) : '';

            global $current_user;
            $user_id = $current_user->ID;
            $meta_query = array();
            $tax_query = array();

            $args = array(
                'post_type' => 'meetings',
                'post_status' => 'publish',
                'paged' => $paged,
                'author' => $user_id,
                'orderby' => 'date',
            );


            $meta_query[] = array(
                'relation' => 'AND',
                array(
                    'key' => CIVI_METABOX_PREFIX . 'meeting_status',
                    'value' => 'completed',
                    'compare' => '!='
                )
            );

            if (!empty($item_id)) {
                if ($action_click == 'delete') {
                    wp_delete_post($item_id, true);
                }
                if ($action_click == 'completed') {
                    update_post_meta($item_id, CIVI_METABOX_PREFIX . 'meeting_status', 'completed');
                }
            }

            if (!empty($item_amount)) {
                $args['posts_per_page'] = $item_amount;
            }

            $args['meta_query'] = array(
                'relation' => 'AND',
                $meta_query
            );

            $tax_count = count($tax_query);
            if ($tax_count > 0) {
                $args['tax_query'] = array(
                    'relation' => 'AND',
                    $tax_query
                );
            }

            $data = new WP_Query($args);
            $total_post = $data->found_posts;
            $max_num_pages = $data->max_num_pages;
            $pagination = paginate_links(apply_filters('civi_pagination_args', array(
                'total' => $max_num_pages,
                'current' => $paged,
                'mid_size' => 1,
                'type' => 'array',
                'prev_text' => __('<i class="fal fa-chevron-left"></i>', 'civi-framework'),
                'next_text' => __('<i class="fal fa-chevron-right"></i>', 'civi-framework'),
            )));

            ob_start();

            if ($total_post >= 0) {
                 while ($data->have_posts()) : $data->the_post();
                    global $current_user;
                    $user_id = $current_user->ID;
                    $user_demo = get_the_author_meta(CIVI_METABOX_PREFIX . 'user_demo', $user_id);
                    $meeting_id = get_the_ID();
                    $meeting_date = get_post_meta($meeting_id, CIVI_METABOX_PREFIX . 'meeting_date', true);
                    $meeting_time = get_post_meta($meeting_id, CIVI_METABOX_PREFIX . 'meeting_time', true);
                    $meeting_time_duration = get_post_meta($meeting_id, CIVI_METABOX_PREFIX . 'meeting_time_duration', true);
                    $meeting_message = get_post_meta($meeting_id, CIVI_METABOX_PREFIX . 'meeting_message', true);
                    $meeting_with = get_post_meta($meeting_id, CIVI_METABOX_PREFIX . 'meeting_with', true);
                    $zoom_link = get_user_meta($user_id, CIVI_METABOX_PREFIX . 'metting_zoom_link', true);
                    $zoom_pw = get_user_meta($user_id, CIVI_METABOX_PREFIX . 'metting_zoom_pw', true);
                    $current_date = date('Y-m-d');
                    $time_date_current = date('H:i');
                    $time_date_current = get_date_from_gmt($time_date_current, 'H:i');
                    $time_date_end = date('H:i', strtotime('+' . $meeting_time_duration . 'minutes', strtotime($meeting_time)));
                    ?>
                    <div class="col-md-4 col-sm-6">
                        <div class="meetings-warpper">
                            <div class="meetings-top">
                                <?php if ((strtotime($meeting_date) < strtotime($current_date))
                                    || (strtotime($meeting_date) == strtotime($current_date) && strtotime($time_date_end) < strtotime($time_date_current))) : ?>
                                    <span class="label label-close"><?php esc_html_e('Expired', 'civi-framework') ?></span>
                                <?php endif; ?>

                                <?php if (strtotime($meeting_date) == strtotime($current_date)) : ?>
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
                                <p class="meeting-width"><?php esc_html_e('Meeting with:', 'civi-framework'); ?>
                                    <span class="athour"><?php esc_html_e($meeting_with) ?></span>
                                </p>
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
                                        <li><a class="btn-completed" meeting-id="<?php echo esc_attr($meeting_id); ?>" href="#"><?php esc_html_e('Completed', 'civi-framework') ?></a>
                                        </li>
                                        <?php if($user_demo == 'yes'): ?>
                                            <li><a class="btn-add-to-message" data-text="<?php echo esc_attr('This is a "Demo" account so you not cant delete it', 'civi-framework'); ?>" href="#"><?php esc_html_e('Delete', 'civi-framework') ?></a></li>
                                        <?php else: ?>
                                            <li><a class="btn-delete" meeting-id="<?php echo esc_attr($meeting_id); ?>" href="#"><?php esc_html_e('Delete', 'civi-framework') ?></a></li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                            <?php if (strtotime($meeting_date) == strtotime($current_date) && $time_date_current >=  $meeting_time && $time_date_current <= $time_date_end) : ?>
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
                <?php endwhile;
            }
            wp_reset_postdata();

            $meetings_html = ob_get_clean();

            if ($total_post > 0) {
                echo json_encode(array('success' => true, 'pagination' => $pagination, 'meetings_html' => $meetings_html, 'total_post' => $total_post, 'page' => $page));
            } else {
                echo json_encode(array('success' => false, 'total_post' => $total_post));
            }
            wp_die();
        }


        /**
         * Meetings Completed Dashboard
         */
        public function civi_meetings_completed_dashboard()
        {
            $item_amount = isset($_REQUEST['item_amount']) ? civi_clean(wp_unslash($_REQUEST['item_amount'])) : '9';
            $paged = isset($_REQUEST['paged']) ? civi_clean(wp_unslash($_REQUEST['paged'])) : '1';
            $item_id = isset($_REQUEST['item_id']) ? civi_clean(wp_unslash($_REQUEST['item_id'])) : '';
            $action_click = isset($_REQUEST['action_click']) ? civi_clean(wp_unslash($_REQUEST['action_click'])) : '';
            $page = isset($_REQUEST['paged']) ? civi_clean(wp_unslash($_REQUEST['paged'])) : '';

            global $current_user;
            $user_id = $current_user->ID;
            $user_demo = get_the_author_meta(CIVI_METABOX_PREFIX . 'user_demo', $user_id);
            $meta_query = array();
            $tax_query = array();

            $args = array(
                'post_type' => 'meetings',
                'post_status' => 'publish',
                'paged' => $paged,
                'author' => $user_id,
                'orderby' => 'date',
            );

            $meta_query[] = array(
                'relation' => 'AND',
                array(
                    'key' => CIVI_METABOX_PREFIX . 'meeting_status',
                    'value' => 'completed',
                    'compare' => '='
                )
            );

            if (!empty($item_id)) {
                if ($action_click == 'delete') {
                    wp_delete_post($item_id, true);
                }
                if ($action_click == 'upcoming') {
                    update_post_meta($item_id, CIVI_METABOX_PREFIX . 'meeting_status', 'upcoming');
                }
            }

            if (!empty($item_amount)) {
                $args['posts_per_page'] = $item_amount;
            }

            $args['meta_query'] = array(
                'relation' => 'AND',
                $meta_query
            );

            $tax_count = count($tax_query);
            if ($tax_count > 0) {
                $args['tax_query'] = array(
                    'relation' => 'AND',
                    $tax_query
                );
            }

            $data = new WP_Query($args);
            $total_post = $data->found_posts;
            $max_num_pages = $data->max_num_pages;
            $pagination = paginate_links(apply_filters('civi_pagination_args', array(
                'total' => $max_num_pages,
                'current' => $paged,
                'mid_size' => 1,
                'type' => 'array',
                'prev_text' => __('<i class="fal fa-chevron-left"></i>', 'civi-framework'),
                'next_text' => __('<i class="fal fa-chevron-right"></i>', 'civi-framework'),
            )));

            ob_start();

            if ($total_post >= 0) {
                while ($data->have_posts()) : $data->the_post();
                    $meeting_id = get_the_ID();
                    $meeting_date = get_post_meta($meeting_id, CIVI_METABOX_PREFIX . 'meeting_date', true);
                    $meeting_time = get_post_meta($meeting_id, CIVI_METABOX_PREFIX . 'meeting_time', true);
                    $meeting_time_duration = get_post_meta($meeting_id, CIVI_METABOX_PREFIX . 'meeting_time_duration', true);
                    $meeting_message = get_post_meta($meeting_id, CIVI_METABOX_PREFIX . 'meeting_message', true);
                    $meeting_with = get_post_meta($meeting_id, CIVI_METABOX_PREFIX . 'meeting_with', true);
                ?>
                    <div class="col-md-4 col-sm-6">
                        <div class="meetings-warpper">
                            <div class="meetings-top">
                                <p class="calendar"><i class="fal fa-calendar-alt"></i><?php esc_html_e($meeting_date) ?> <span class="dot">.</span> <?php esc_html_e($meeting_time) ?></p>
                                <h6><?php echo get_the_title(); ?></h6>
                                <p class="meeting-width"><?php esc_html_e('Meeting with:', 'civi-framework'); ?> <span class="athour"><?php esc_html_e($meeting_with) ?></span></p>
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
                                        <li><a class="btn-upcoming" meeting-id="<?php echo esc_attr($meeting_id); ?>" href="#"><?php esc_html_e('Upcoming', 'civi-framework') ?></a></li>
                                        <?php if($user_demo == 'yes'): ?>
                                            <li><a class="btn-add-to-message" data-text="<?php echo esc_attr('This is a "Demo" account so you not cant delete it', 'civi-framework'); ?>" href="#"><?php esc_html_e('Delete', 'civi-framework') ?></a></li>
                                        <?php else: ?>
                                            <li><a class="btn-delete" meeting-id="<?php echo esc_attr($meeting_id); ?>" href="#"><?php esc_html_e('Delete', 'civi-framework') ?></a></li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile;
            }
            wp_reset_postdata();

            $meetings_html = ob_get_clean();

            if ($total_post > 0) {
                echo json_encode(array('success' => true, 'pagination' => $pagination, 'meetings_html' => $meetings_html, 'total_post' => $total_post, 'page' => $page));
            } else {
                echo json_encode(array('success' => false, 'total_post' => $total_post));
            }
            wp_die();
        }


        /**
         * Meetings Candidate Dashboard
         */
        public function civi_meetings_candidate_dashboard()
        {
            $item_amount = isset($_REQUEST['item_amount']) ? civi_clean(wp_unslash($_REQUEST['item_amount'])) : '3';
            $paged = isset($_REQUEST['paged']) ? civi_clean(wp_unslash($_REQUEST['paged'])) : '1';
            $item_id = isset($_REQUEST['item_id']) ? civi_clean(wp_unslash($_REQUEST['item_id'])) : '';
            $page = isset($_REQUEST['paged']) ? civi_clean(wp_unslash($_REQUEST['paged'])) : '';

            global $current_user;
            $user_id = $current_user->ID;
            $meta_query = array();
            $tax_query = array();

            $args = array(
                'post_type' => 'meetings',
                'post_status' => 'publish',
                'paged' => $paged,
                'orderby' => 'date',
            );

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

            $meta_query[] = array(
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
            );

            if (!empty($item_amount)) {
                $args['posts_per_page'] = $item_amount;
            }

            $args['meta_query'] = array(
                'relation' => 'AND',
                $meta_query
            );

            $tax_count = count($tax_query);
            if ($tax_count > 0) {
                $args['tax_query'] = array(
                    'relation' => 'AND',
                    $tax_query
                );
            }

            $data = new WP_Query($args);
            $total_post = $data->found_posts;
            $max_num_pages = $data->max_num_pages;
            $pagination = paginate_links(apply_filters('civi_pagination_args', array(
                'total' => $max_num_pages,
                'current' => $paged,
                'mid_size' => 1,
                'type' => 'array',
                'prev_text' => __('<i class="fal fa-chevron-left"></i>', 'civi-framework'),
                'next_text' => __('<i class="fal fa-chevron-right"></i>', 'civi-framework'),
            )));

            ob_start();

            if ($total_post > 0) {
                while ($data->have_posts()) : $data->the_post();
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
<?php endwhile;
            }
            wp_reset_postdata();

            $meetings_html = ob_get_clean();

            if ($total_post > 0) {
                echo json_encode(array('success' => true, 'pagination' => $pagination, 'meetings_html' => $meetings_html, 'total_post' => $total_post, 'page' => $page));
            } else {
                echo json_encode(array('success' => false, 'total_post' => $total_post));
            }
            wp_die();
        }
    }
}
