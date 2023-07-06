<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $current_user;
wp_get_current_user();
wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'candidate-follow');

$key = false;
$user_id = $current_user->ID;
$follow_candidate = get_user_meta($user_id, CIVI_METABOX_PREFIX . 'follow_candidate', true);
$id = get_the_ID();

if (!empty($candidate_id)) {
    $id = $candidate_id;
}

if (!empty($follow_candidate)) {
    $key = array_search($id, $follow_candidate);
}

$css_class = '';
if ($key !== false) {
    $css_class = 'added';
}
?>
<?php if (is_user_logged_in() && in_array('civi_user_employer', (array)$current_user->roles)) { ?>
    <a href="#"
       class="civi-button button-outline-accent civi-add-to-follow-candidate add-follow-candidate <?php echo esc_attr($css_class); ?>"
       data-candidate-id="<?php echo intval($id) ?>">
        <span class="icon-plus">
            <i class="far fa-plus"></i>
        </span>
        <?php if ($key !== false) { ?>
            <?php esc_html_e('Following', 'civi-framework') ?>
        <?php } else { ?>
            <?php esc_html_e('Follow', 'civi-framework') ?>
        <?php } ?>
    </a>
<?php } else { ?>
    <div class="logged-out">
        <a href="#popup-form"
           class="civi-button button-outline-accent btn-login notice-employer add-follow-candidate <?php echo esc_attr($css_class); ?>"
           data-candidate-id="<?php echo intval($id) ?>" data-notice="<?php esc_attr_e('Please login role Employer to view', 'civi-framework') ?>">
            <span class="icon-plus">
                <i class="far fa-plus"></i>
            </span>
            <?php esc_html_e('Follow', 'civi-framework') ?>
        </a>
    </div>
<?php } ?>