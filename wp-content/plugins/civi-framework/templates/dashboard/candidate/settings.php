<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}




if (!is_user_logged_in()) {
    civi_get_template('global/access-denied.php', array('type' => 'not_login'));
    return;
}

global $current_user;
wp_get_current_user();

$user_id                 = $current_user->ID;
$user_login              = $current_user->user_login;
$user_demo = get_the_author_meta(CIVI_METABOX_PREFIX . 'user_demo', $user_id);

$ajax_url     = admin_url('admin-ajax.php');
$upload_nonce = wp_create_nonce('civi_thumbnail_allow_upload');

wp_enqueue_script('plupload');
wp_enqueue_script('jquery-validate');
wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'settings');
wp_localize_script(
    CIVI_PLUGIN_PREFIX . 'settings',
    'civi_settings_vars',
    array(
        'ajax_url' => CIVI_AJAX_URL,
        'site_url' => get_site_url(),
    )
);

?>

<div class="entry-my-page settings-dashboard">
    <div class="entry-title">
        <h4><?php esc_html_e('Settings', 'civi-framework') ?></h4>
    </div>
    <div class="form-dashboard">
        <form action="#" class="block-from form-password form-change-password">
            <h6><?php esc_html_e('Change password', 'civi-framework') ?></h6>
            <div class="row">
                <div class="form-group col-md-12">
                    <label for="oldpass"><?php esc_html_e('Current password', 'civi-framework') ?></label>
                    <input class="form-control" type="password" id="oldpass" name="oldpass" value="" placeholder="<?php esc_attr_e('Enter current password', 'civi-framework'); ?>">
                    <span toggle="#oldpass" class="fa fa-fw fa-eye field-icon civi-toggle-password"></span>
                </div>
                <div class="form-group col-md-12">
                    <label for="newpass"><?php esc_html_e('New password', 'civi-framework') ?></label>
                    <input class="form-control" type="password" id="newpass" name="nnewpass" value="" placeholder="<?php esc_attr_e('Enter new password', 'civi-framework'); ?>">
                    <span toggle="#newpass" class="fa fa-fw fa-eye field-icon civi-toggle-password"></span>
                </div>
                <div class="form-group col-md-12">
                    <label for="confirmpass"><?php esc_html_e('Confirm new password', 'civi-framework') ?></label>
                    <input class="form-control" type="password" id="confirmpass" name="confirmpass" value="" placeholder="<?php esc_attr_e('Enter confirm password', 'civi-framework'); ?>">
                    <span toggle="#confirmpass" class="fa fa-fw fa-eye field-icon civi-toggle-password"></span>
                </div>
            </div>
            <?php wp_nonce_field('civi_change_password_ajax_nonce', 'civi_security_change_password'); ?>
            <div class="message"></div>
            <?php if ($user_demo == 'yes') : ?>
                <button class="civi-button btn-add-to-message" data-text="<?php echo esc_attr('This is a "Demo" account, so you can not change it', 'civi-framework'); ?>">
                    <?php esc_html_e('Save changes', 'civi-framework'); ?>
                </button>
            <?php else : ?>
                <button class="civi-button button-password" id="civi_change_pass">
                    <span><?php esc_html_e('Save changes', 'civi-framework'); ?></span>
                    <span class="btn-loading"><i class="fal fa-spinner fa-spin large"></i></span>
                </button>
            <?php endif; ?>
        </form>
        <?php if ($user_demo == 'yes') : ?>
            <a class="btn-add-to-message delete-account" data-text="<?php echo esc_attr('This is a "Demo" account so you not cant deactive it', 'civi-framework'); ?>" href="#"><?php esc_html_e('Deactive account', 'civi-framework') ?></a></li>
        <?php else :
            $nonce_url = wp_nonce_url(get_site_url() . '?action=civi_deactive_user&user_id=' . $user_id, 'deactive_' . $user_id); ?>
            <a href="<?php echo $nonce_url ?>" class="delete-account"><?php esc_html_e('Deactive account', 'civi-framework') ?></a>
        <?php endif; ?>
    </div>
</div>