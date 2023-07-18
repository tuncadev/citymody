<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}


wp_enqueue_script('plupload');
wp_enqueue_script('jquery-validate');
wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'candidate-submit');
wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'candidate');
$custom_field_candidate = civi_render_custom_field('candidate');
wp_localize_script(
    CIVI_PLUGIN_PREFIX . 'candidate-submit',
    'civi_candidate_vars',
    array(
        'ajax_url' => CIVI_AJAX_URL,
        'site_url' => get_site_url(),
        'custom_field_candidate' => $custom_field_candidate,
    )
);

global $current_user, $hide_candidate_fields, $hide_candidate_group_fields, $candidate_data, $candidate_meta_data;
wp_get_current_user();
$candidate_id = civi_get_post_id_candidate();
if (!empty($candidate_id)) {
    $candidate_data = get_post($candidate_id);
    $candidate_meta_data = get_post_custom($candidate_data->ID);
}
$user_id = $current_user->ID;
$user_demo = get_the_author_meta(CIVI_METABOX_PREFIX . 'user_demo', $user_id);

$ajax_url = admin_url('admin-ajax.php');
$upload_nonce = wp_create_nonce('candidate_allow_upload');

$profile_strength_percent = isset($candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_profile_strength']) ? $candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_profile_strength'][0] : '';
if (empty($profile_strength_percent)) {
    $profile_strength_percent = 0;
}

$hide_candidate_fields = civi_get_option('hide_candidate_fields', array());
if (!is_array($hide_candidate_fields)) {
    $hide_candidate_fields = array();
}
$hide_candidate_group_fields = civi_get_option('hide_candidate_group_fields', array());
if (!is_array($hide_candidate_group_fields)) {
    $hide_candidate_group_fields = array();
}
$layout = array('info', 'education', 'experience', 'paymentinfo', 'projects', 'awards');
?>

<div id="candidate-profile" class="candidate-profile">

    <div class="entry-my-page candidate-profile-dashboard">

        <div class="entry-title">
            <h4><?php esc_html_e('Profile Settings', 'civi-framework') ?></h4>
        </div>

        <div class="tab-dashboard">
            <ul class="tab-list candidate-profile-tab">
                <?php foreach ($layout as $value) {
                    switch ($value) {
                        case 'info':
                            $name = esc_html__('Basic Info', 'civi-framework');
                            $class = '';
                            break;
                        case 'education':
                            $name = esc_html__('Education', 'civi-framework');
                            $class = 'repeater';
                            break;
                        case 'experience':
                            $name = esc_html__('Experience', 'civi-framework');
                            $class = 'repeater';
                            break;
												
												/* Bank Info */
												case 'paymentinfo':
													$name = esc_html__('Bank and Payment Information', 'civi-framework');
													$class = '';
													break;
												/* ************* */
                        case 'projects':
                            $name = esc_html__('Projects', 'civi-framework');
                            $class = 'repeater ';
                            break;
                        case 'awards':
                            $name = esc_html__('Awards', 'civi-framework');
                            $class = 'repeater';
                            break;
                    }
                    if (!in_array($value, $hide_candidate_group_fields)) : ?>
                        <li class="tab-item <?php esc_attr_e($class); ?>"><a href="#tab-<?php esc_attr_e($value) ?>"><?php esc_html_e($name) ?></a>
                        </li>
                <?php endif;
                } ?>

                <?php $custom_field_candidate = civi_render_custom_field('candidate');
                if (count($custom_field_candidate) > 0) :
                    $tabs_array = array();
                    foreach ($custom_field_candidate as $field) {
                        if ((!in_array($field['section'], $tabs_array)) && !empty($field['section'])) {
                            $tabs_array[] = $field['section'];
                        }
                    }
                    foreach ($tabs_array as $value) {
                        $tabs_id = str_replace(" ", "-", $value); ?>
                        <li class="tab-item"><a href="#tab-<?php echo $tabs_id ?>"><?php echo $value; ?></a></li>
                    <?php } ?>
                <?php endif; ?>
            </ul>

            <div class="tab-content row">
                <form action="#" method="post" enctype="multipart/form-data" id="candidate-profile-form" class="candidate-profile-form form-dashboard  col-lg-8 col-md-7">
                    <input type="hidden" name="candidate_profile_strength" value="<?php esc_attr_e($profile_strength_percent) ?>">
                    <?php foreach ($layout as $value) {
                        switch ($value) {
                            case 'info':
                                break;
                            case 'education':
                                break;
                            case 'experience':
                                break;
														case 'paymentinfo':
															break;
                            case 'projects':
                                break;
                            case 'awards':
                                break;
                        }
                        if (!in_array($value, $hide_candidate_group_fields)) : ?>
                            <?php civi_get_template('dashboard/candidate/profile/' . $value . '.php'); ?>
                    <?php endif;
                    } ?>

                    <?php $custom_field_candidate = civi_render_custom_field('candidate');
                    if (count($custom_field_candidate) > 0) :
                        foreach ($custom_field_candidate as $field) {
                            $tabs_id = str_replace(" ", "-", $field['section']); ?>
                            <div id="tab-<?php echo $tabs_id; ?>" class="tab-info block-from">
                                <h5><?php echo $field['section']; ?></h5>
                                <?php civi_custom_field_candidate($field['section'], true); ?>
                            </div>
                        <?php } ?>
                    <?php endif; ?>

                    <div class="button-warpper">
                        <a href="<?php echo civi_get_permalink('jobs_dashboard'); ?>" class="civi-button button-outline">
                            <?php esc_html_e('Cancel', 'civi-framework') ?>
                        </a>
                        <?php if ($user_demo == 'yes') : ?>
                            <button class="civi-button btn-add-to-message" data-text="<?php echo esc_attr('This is a "Demo" account, so you can not change it', 'civi-framework'); ?>">
                                <span><?php esc_html_e('Publish', 'civi-framework'); ?></span>
                            </button>
                        <?php else : ?>
                            <button type="submit" class="btn-update-profile civi-button" name="submit_jobs">
                                <span><?php esc_html_e('Publish', 'civi-framework'); ?></span>
                                <span class="btn-loading"><i class="fal fa-spinner fa-spin large"></i></span>
                            </button>
                        <?php endif; ?>
                    </div>
                    <input type="hidden" name="candidate_id" value="2476" />
                </form>

                <div class="candidate-profile-strength col-lg-4 col-md-5">
                    <div class="has-sticky">
                        <div class="profile-strength tip" style="--pct: <?php echo esc_attr($profile_strength_percent) ?>">
                            <h1><span><?php echo esc_attr($profile_strength_percent) ?></span><span>%</span></h1>
                            <div><?php esc_html_e('Profile Strength', 'civi-framework') ?></div>
                            <div class="tip-content post-bottom">
                                <ul class="profile-list-check">
                                    <?php foreach ($layout as $value) {
                                        switch ($value) {
                                            case 'info':
                                                $name = esc_html__('Basic Info', 'civi-framework');
                                                break;
                                            case 'education':
                                                $name = esc_html__('Education', 'civi-framework');
                                                break;
                                            case 'experience':
                                                $name = esc_html__('Experience', 'civi-framework');
                                                break;

																						case 'paymentinfo':
																							$name = esc_html__('Bank and Payment Information', 'civi-framework');
																							break;
                                            case 'projects':
                                                $name = esc_html__('Projects', 'civi-framework');
                                                break;
                                            case 'awards':
                                                $name = esc_html__('Awards', 'civi-framework');
                                                break;
                                        }
                                        if (!in_array($value, $hide_candidate_group_fields)) : ?>
                                            <li class="profile-check-item" id="<?php echo 'profile-check-' . $value ?>" data-has-check="<?php echo sprintf(__('%s has enough information', 'civi-framework'), $name); ?>" data-not-check="<?php echo sprintf(__('%s not enough information', 'civi-framework'), $name); ?>">
                                                <i class="fas fa-check"></i>
                                                <span><?php echo sprintf(__('%s not enough information', 'civi-framework'), $name); ?></span>
                                            </li>
                                    <?php endif;
                                    } ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>