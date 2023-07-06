<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $current_user;
$user_id = $current_user->ID;
$post_id = get_the_ID();
?>
<?php if (is_user_logged_in() && in_array('civi_user_candidate', (array)$current_user->roles)) { ?>
    <a href="#form-messages-popup" class="civi-button civi-send-mess" id="civi-add-messages"
       data-post-current="<?php echo intval($post_id) ?>" data-author-id="<?php echo intval($user_id) ?>">
        <?php esc_html_e('Send message', 'civi-framework') ?>
    </a>
<?php } else { ?>
    <div class="logged-out">
        <a href="#popup-form"
           class="civi-button btn-login civi-send-mess">
            <?php esc_html_e('Send message', 'civi-framework') ?>
        </a>
    </div>
<?php } ?>