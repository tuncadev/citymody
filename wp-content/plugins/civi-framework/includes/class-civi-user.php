<?php

if (!defined('ABSPATH')) {
    exit;
}
if (!class_exists('Civi_User')) {
    /**
     * Class Civi_User
     */
    class Civi_User
    {

        function __construct()
        {
            add_action('init', array($this, 'add_user_roles'));
            add_action('init', array($this, 'civi_deactive_user'));
            add_filter('pre_option_default_role', array($this, 'add_user_roles_default'));
            add_action('wp_footer', array($this, 'jobs_single_bottombar'));
            add_action('user_register', array($this, 'create_a_profile_post_for_new_candidate'), 10, 2);

            if (civi_get_option('enable_job_alerts') === '1') {
                add_action('wp_footer', array($this, 'job_alert_form'));
            }
        }

        public static function add_user_roles()
        {
            add_role(
                'civi_user_candidate',
                esc_html__('Candidate', 'civi-framework'),
                array(
                    'read' => true,
                    'edit_posts' => false,
                    'delete_posts' => false,
                    'upload_files' => true,
                )
            );
            add_role(
                'civi_user_employer',
                esc_html__('Employer', 'civi-framework'),
                array(
                    'read' => true,
                    'edit_posts' => false,
                    'delete_posts' => false,
                    'upload_files' => true,
                )
            );
        }

        public function civi_deactive_user()
        {
            if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'civi_deactive_user' && is_user_logged_in()) {
                if (!wp_verify_nonce($_GET['_wpnonce'], 'deactive_' . $_GET['user_id'])) exit();
                include("./wp-admin/includes/user.php");
                $current_user = wp_get_current_user();
                $password_string = '!@#$%*&abcdefghijklmnpqrstuwxyzABCDEFGHJKLMNPQRSTUWXYZ23456789';
                $password = substr(str_shuffle($password_string), 0, 12);
                wp_set_password($password, $current_user->ID);
                wp_logout();
            }
        }

        public static function add_user_roles_default()
        {
            return 'civi_user_candidate';
        }

        //Single Jobs
        public static function jobs_single_bottombar()
        {
            if (is_singular('jobs')) {
                $social_sharing = civi_get_option('social_sharing');
                $jobs_id = get_the_ID(); ?>
                <div class="civi-apply-bottombar">
                    <?php civi_get_status_apply($jobs_id); ?>
                    <?php if (!empty($social_sharing)) : ?>
                        <div class="toggle-social">
                            <a href="#" class="jobs-share btn-share">
                                <i class="fas fa-share-alt"></i>
                            </a>
                            <?php civi_get_template('global/social-share.php'); ?>
                        </div>
                    <?php endif; ?>
                    <?php civi_get_template('jobs/wishlist.php', array(
                        'jobs_id' => $jobs_id,
                    )); ?>
                </div>
            <?php
            }
        }

        public function create_a_profile_post_for_new_candidate($user_id, $userdata)
        {
            $user_roles = array();

            // Check if admin creates user or a new client is registering
            if (is_admin() && !wp_doing_ajax()) {
                $user_roles = get_userdata($user_id)->roles;
            } else {
                $user_roles[] = $userdata['account_type'];
            }

            $is_candidate = in_array('civi_user_candidate', $user_roles);

            if ($is_candidate == false) {
                return;
            }

            $new_profile_id = 0;

            $new_profile_id = $this->create_profile_for_new_candidate($user_id, $userdata);

            // Add a UserMeta link to the new candidate profile post-type
            if ($new_profile_id > 0) {
                update_user_meta($user_id, 'civi-cpt_id', $new_profile_id);
            }
        }

        private function create_profile_for_new_candidate($user_id, $userdata)
        {
            // Author MUST be the ID of Candidate user
            $new_profile['post_author'] = $user_id;
            $new_profile['post_type']   = 'candidate';
            $new_profile['post_title']  = sanitize_user($userdata['user_login'], true);
            $new_profile['post_status'] = 'publish';

            $new_profile_id = 0;

            if (!empty($new_profile['post_title'])) {
                $new_profile_id = wp_insert_post($new_profile, true);
            }

            if ($new_profile_id > 0) {

                // Add Metadata for Candidate
                $new_profile_first_name = empty($userdata['first_name']) ? '' : $userdata['first_name'];
                $new_profile_last_name  = empty($userdata['last_name']) ? '' : $userdata['last_name'];
                $new_profile_user_email = empty($userdata['user_email']) ? '' : $userdata['user_email'];

                update_post_meta($new_profile_id, CIVI_METABOX_PREFIX . 'candidate_user_id', $user_id);
                update_post_meta($new_profile_id, CIVI_METABOX_PREFIX . 'candidate_first_name', $new_profile_first_name);
                update_post_meta($new_profile_id, CIVI_METABOX_PREFIX . 'candidate_last_name', $new_profile_last_name);
                update_post_meta($new_profile_id, CIVI_METABOX_PREFIX . 'candidate_email', $new_profile_user_email);
            }

            return $new_profile_id;
        }

        public function job_alert_form()
        {
            $current_page_id = get_the_ID();
            $alerts_title = esc_html__('Job Alert', 'civi-framework');
            $alerts_desc = esc_html__('Subscribe to receive instant alerts of new relevant jobs directly to your email inbox.', 'civi-framework');
            $alerts_button_title = esc_html__('Subcrible', 'civi-framework');
            $civi_job_alerts_page_id  = civi_get_option('civi_job_alerts_page_id');
            if (($current_page_id == $civi_job_alerts_page_id) || isset($_COOKIE["cookie_job_alerts"]) || (get_post_type() != 'jobs')) {
                return;
            }
            ?>
            <div class="alert-form">
                <a href="#" class="close"><i class="far fa-times"></i></a>
                <div class="inner">
                    <div class="head">
                        <img src="https://www.citymody.com/wp-content/uploads/2023/07/bell-icon.png" style="margin-right: 30px;"/>
                        <span><?php echo $alerts_title; ?></span>
                    </div>
                    <div class="content">
                        <div class="desc"><?php echo $alerts_desc; ?></div>
                        <a href="<?php echo esc_url(get_page_link($civi_job_alerts_page_id)); ?>" class="civi-button"><?php echo $alerts_button_title; ?></a>
                    </div>
                </div>
            </div>
<?php
        }
    }
    new Civi_User();
}
