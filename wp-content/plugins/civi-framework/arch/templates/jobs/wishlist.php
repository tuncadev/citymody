<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $current_user;
wp_get_current_user();
wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'wishlist');

$key = false;
$user_id = $current_user->ID;
$my_wishlist = get_user_meta($user_id, CIVI_METABOX_PREFIX . 'my_wishlist', true);
$id = get_the_ID();

if (!empty($jobs_id)) {
    $id = $jobs_id;
}

if (!empty($my_wishlist)) {
    $key = array_search($id, $my_wishlist);
}

$css_class = '';
if ($key !== false) {
    $css_class = 'added';
}
?>
<?php if (is_user_logged_in() && in_array('civi_user_candidate', (array)$current_user->roles)) { ?>
    <a href="#" class="civi-add-to-wishlist btn-add-to-wishlist tooltip <?php echo esc_attr($css_class); ?>"
       data-jobs-id="<?php echo intval($id) ?>" data-title="<?php esc_attr_e('Wishlist', 'civi-framework') ?>">
        <span class="icon-heart">
            <i class="fas fa-heart"></i>
        </span>
    </a>
<?php } else { ?>
    <div class="logged-out">
        <a href="#popup-form" class="btn-login btn-add-to-wishlist tooltip <?php echo esc_attr($css_class); ?>"
           data-jobs-id="<?php echo intval($id) ?>" data-title="<?php esc_attr_e('Wishlist', 'civi-framework') ?>">
            <span class="icon-heart">
                <i class="fas fa-heart"></i>
            </span>
        </a>
    </div>
<?php } ?>