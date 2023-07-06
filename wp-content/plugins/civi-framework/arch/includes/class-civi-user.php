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
                        <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21.7042 19.316C21.5254 17.691 24.5704 12.5448 21.9617 9.08601C21.6917 8.71101 22.0667 7.92351 22.3179 7.39226C22.9279 6.10726 22.7979 5.05726 21.5792 4.42976C20.3604 3.80226 19.4192 4.37976 18.7042 5.42976C18.3667 5.92976 17.8192 6.67976 17.4317 6.64101C13.1442 6.36726 10.5267 11.716 9.08041 12.4498C7.37041 13.3173 3.45541 13.896 2.79416 16.2735C1.93291 19.346 4.65666 22.4085 10.2367 25.446C15.8167 28.4835 19.8617 29.1035 21.9492 26.696C23.5679 24.8373 21.9154 21.2298 21.7042 19.316Z" fill="#191919" />
                            <path d="M18.3748 29.1702C18.01 29.1692 17.6456 29.1467 17.2835 29.1027C15.2048 28.8527 12.7435 27.9452 9.7585 26.3202C6.7735 24.6952 4.67225 23.1252 3.331 21.5089C1.821 19.6914 1.316 17.8377 1.831 16.0002C2.46225 13.7502 5.10725 12.8327 7.03975 12.1664C7.57981 11.994 8.10909 11.7895 8.62475 11.5539C8.87475 11.4289 9.466 10.7327 9.93975 10.1789C11.5223 8.32016 13.886 5.53766 17.2798 5.62516C17.5019 5.39094 17.7011 5.13603 17.8748 4.86391C18.9848 3.23891 20.4998 2.75391 22.0323 3.54141C22.8098 3.94141 24.481 5.15891 23.2173 7.82266C23.0819 8.08965 22.9686 8.36724 22.8785 8.65266C24.8473 11.4552 23.8135 14.9902 23.1285 17.3439C22.9223 18.0477 22.666 18.9227 22.6985 19.2064C22.7847 19.7692 22.9041 20.3264 23.056 20.8752C23.5823 22.9739 24.2373 25.5864 22.7048 27.3477C21.6535 28.5614 20.1998 29.1702 18.3748 29.1702ZM17.1248 7.62516C14.7385 7.62516 12.8573 9.83766 11.471 11.4689C10.7323 12.3439 10.1485 13.0252 9.536 13.3352C8.93867 13.6139 8.32408 13.8539 7.696 14.0539C6.0985 14.6052 4.10975 15.2927 3.761 16.5377C3.07725 18.9764 5.4185 21.6764 10.7198 24.5627C16.021 27.4489 19.5423 27.9377 21.1985 26.0389C22.046 25.0639 21.5323 23.0139 21.1185 21.3652C20.942 20.7278 20.8063 20.0798 20.7123 19.4252C20.6373 18.7427 20.8898 17.8764 21.2123 16.7814C21.8373 14.6427 22.6948 11.7139 21.1673 9.68766L21.156 9.67266C20.5385 8.81891 21.0685 7.70266 21.4185 6.96391C21.9885 5.76266 21.4898 5.50641 21.126 5.31891C20.8123 5.15766 20.2873 4.88766 19.536 5.98891C18.9673 6.82391 18.286 7.70766 17.3635 7.63391C17.2798 7.62516 17.1998 7.62516 17.1248 7.62516Z" fill="#191919" />
                            <path d="M21.7042 19.316C21.5254 17.691 24.5704 12.5448 21.9617 9.08601C21.6917 8.71101 22.0667 7.92351 22.3179 7.39226C22.9279 6.10726 22.7979 5.05726 21.5792 4.42976C20.3604 3.80226 19.4192 4.37976 18.7042 5.42976C18.3667 5.92976 17.8192 6.67976 17.4317 6.64101C13.1442 6.36726 10.5267 11.716 9.08041 12.4498C7.37041 13.3173 3.45541 13.896 2.79416 16.2735C1.93291 19.346 4.65666 22.4085 10.2367 25.446C15.8167 28.4835 19.8617 29.1035 21.9492 26.696C23.5679 24.8373 21.9154 21.2298 21.7042 19.316Z" fill="#FFD75E" />
                            <path d="M10.3053 18.8889C10.7028 18.9151 15.759 21.6839 16.1065 21.9589C16.454 22.2339 15.559 23.3851 14.019 23.4989C12.479 23.6126 10.2765 22.6939 9.83403 21.9351C9.39153 21.1764 9.90778 18.8626 10.3053 18.8889Z" fill="#ED0006" />
                            <path d="M15.8232 21.8089C15.5807 21.8227 13.9344 23.5302 12.1982 22.6277C9.79315 21.3777 10.7394 19.2852 10.5732 19.0027C10.4069 18.7202 5.79565 17.0727 5.26815 17.9502C4.7844 18.7564 7.4194 21.6139 11.2369 23.5902C15.1232 25.6014 18.6632 26.3127 19.1244 25.4927C19.6707 24.5102 16.0644 21.7952 15.8232 21.8089Z" fill="white" />
                            <path d="M2.48001 9.8367C2.99126 7.8892 5.09001 6.0042 6.98001 5.52795C7.45001 5.41045 7.11126 4.2867 6.58126 4.40295C4.50876 4.86545 2.28251 6.6017 1.26001 9.3567C1.00001 10.0442 2.38376 10.2055 2.48001 9.8367Z" fill="#191919" />
                            <path d="M5.84292 9.59303C6.68542 8.36053 7.80417 8.07803 8.86667 7.96803C9.33667 7.92053 9.33667 6.71803 8.84542 6.75678C7.54792 6.86053 6.02917 7.11428 4.79292 8.82053C4.37542 9.39178 5.60292 9.94553 5.84292 9.59303Z" fill="#191919" />
                            <path d="M27.699 14.6729C29.2715 16.0091 30.074 18.7841 29.679 20.7454C29.579 21.2316 30.7202 21.5954 30.8465 21.0529C31.3465 18.9279 30.7215 15.8654 28.6365 13.6979C28.1152 13.1566 27.4015 14.4204 27.699 14.6729Z" fill="#191919" />
                            <path d="M26.4837 18.0432C27.17 19.4107 26.8675 20.5532 26.4362 21.5607C26.2462 22.0057 27.3337 22.542 27.5425 22.0832C28.135 20.7795 28.5875 19.427 27.6475 17.417C27.3425 16.762 26.2887 17.6532 26.4837 18.0432Z" fill="#191919" />
                        </svg>
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
