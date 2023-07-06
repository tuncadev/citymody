<?php
if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('Civi_Package')) {
    /**
     * Class Civi_Package
     */
    class Civi_Package
    {
        /**
         * get_time_unit
         * @param $time_unit
         * @return null|string
         */
        public static function get_time_unit($time_unit)
        {
            if ($time_unit == 'Day') {
                return esc_html__('day', 'civi-framework');
            } else if ($time_unit == 'Days') {
                return esc_html__('days', 'civi-framework');
            } else if ($time_unit == 'Week') {
                return esc_html__('week', 'civi-framework');
            } else if ($time_unit == 'Weeks') {
                return esc_html__('weeks', 'civi-framework');
            } else if ($time_unit == 'Month') {
                return esc_html__('month', 'civi-framework');
            } else if ($time_unit == 'Months') {
                return esc_html__('months', 'civi-framework');
            } else if ($time_unit == 'Year') {
                return esc_html__('year', 'civi-framework');
            } else if ($time_unit == 'Years') {
                return esc_html__('years', 'civi-framework');
            }
            return null;
        }

        /**
         * Insert agent package
         * @param $user_id
         * @param $package_id
         */
        public function insert_user_package($user_id, $package_id)
        {
            $args = array(
                'post_type' => 'user_package',
                'posts_per_page' => -1,
                'meta_query' => array(
                    array(
                        'key' => CIVI_METABOX_PREFIX . 'package_user_id',
                        'value' => $user_id,
                        'compare' => '='
                    )
                ),
            );
            $user_package = new WP_Query($args);
            wp_reset_postdata();
            $existed_post = $user_package->found_posts;

            if ($existed_post < 1) {
                $args = array(
                    'post_title' => '#' . $user_id,
                    'post_type' => 'user_package',
                    'post_status' => 'publish'
                );
                $post_id = wp_insert_post($args);
                update_post_meta($post_id, CIVI_METABOX_PREFIX . 'package_user_id', $user_id);
            }
            $package_number_job = get_post_meta($package_id, CIVI_METABOX_PREFIX . 'package_number_job', true);
            $package_number_featured = get_post_meta($package_id, CIVI_METABOX_PREFIX . 'package_number_featured', true);
            $package_unlimited_job = get_post_meta($package_id, CIVI_METABOX_PREFIX . 'package_unlimited_job', true);

            if ($package_unlimited_job == 1) {
                $package_number_job = 999999999999999999;
            }
            update_user_meta($user_id, CIVI_METABOX_PREFIX . 'package_number_job', $package_number_job);
            update_user_meta($user_id, CIVI_METABOX_PREFIX . 'package_number_featured', $package_number_featured);
            $time = time();
            $date = date('Y-m-d H:i:s', $time);
            update_user_meta($user_id, CIVI_METABOX_PREFIX . 'package_activate_date', $date);
            update_user_meta($user_id, CIVI_METABOX_PREFIX . 'package_id', $package_id);
            $package_key = uniqid();
            update_user_meta($user_id, CIVI_METABOX_PREFIX . 'package_key', $package_key);


            $email_type = 'mail_activated_package';

            $message = civi_get_option($email_type, '');
            $subject = civi_get_option('subject_' . $email_type, '');

            if (function_exists('icl_translate')) {
                $message = icl_translate('civi-framework', 'civi_email_' . $message, $message);
                $subject = icl_translate('civi-framework', 'civi_email_subject_' . $subject, $subject);
            }

            $message = wpautop($message);

            $user = get_user_by('id', $user_id);
            $user_email = $user->user_email;

            $args['website_url'] = get_option('siteurl');
            $args['website_name'] = get_option('blogname');
            $args['your_name'] = $user->user_login;

            foreach ($args as $key => $val) {
                //$subject = str_replace('%' . $key, $val, $subject);
                //$message = str_replace('%' . $key, $val, $message);
            }

            $headers = apply_filters("civi_contact_mail_header", array('Content-Type: text/html; charset=UTF-8'));
            wp_mail(
                $user_email,
                $subject,
                $message,
                $headers
            );
        }

        public function get_expired_date($package_id, $package_user_id)
        {
            $package_unlimited_time = get_post_meta($package_id, CIVI_METABOX_PREFIX . 'package_unlimited_time', true);
            if ($package_unlimited_time == 1) {
                $expired_date = esc_html__('Never Expires');
            } else {
                $expired_date = $this->get_expired_time($package_id, $package_user_id);
                $expired_date = date_i18n('Y-m-d', $expired_date);
            }
            return $expired_date;
        }

        public function get_expired_time($package_id, $package_user_id)
        {
            $expired_time = '';
            $package_time_unit = get_post_meta($package_id, CIVI_METABOX_PREFIX . 'package_time_unit', true);
            $package_period = get_post_meta($package_id, CIVI_METABOX_PREFIX . 'package_period', true);
            $package_activate_date = strtotime(get_user_meta($package_user_id, CIVI_METABOX_PREFIX . 'package_activate_date', true));
            $seconds = 0;
            switch ($package_time_unit) {
                case 'Day':
                    $seconds = 60 * 60 * 24;
                    break;
                case 'Week':
                    $seconds = 60 * 60 * 24 * 7;
                    break;
                case 'Month':
                    $seconds = 60 * 60 * 24 * 30;
                    break;
                case 'Year':
                    $seconds = 60 * 60 * 24 * 365;
                    break;
            }
            if (is_numeric($package_activate_date) && is_numeric($seconds) && is_numeric($package_period)) {
                $expired_time = $package_activate_date + ($seconds * $package_period);
            }
            return $expired_time;
        }
    }
}
