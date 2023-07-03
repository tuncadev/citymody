<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $current_user;
$user_id = $current_user->ID;
$user_demo = get_the_author_meta(CIVI_METABOX_PREFIX . 'user_demo', $user_id);
$no_image_src = CIVI_PLUGIN_URL . 'assets/images/default-user-image.png';

$creator_message = get_post_meta($message_id, CIVI_METABOX_PREFIX . 'creator_message', true);
$recipient_message = get_post_meta($message_id, CIVI_METABOX_PREFIX . 'recipient_message', true);

if (intval($creator_message) == $user_id) {
    $author_id = get_post_field('post_author', $recipient_message);
} else {
    $author_id = $creator_message;
}

$name = get_the_author_meta('display_name', $author_id);
$title   = get_the_title($message_id);
$avatar = get_the_author_meta('author_avatar_image_url', $author_id);
$phone = get_the_author_meta(CIVI_METABOX_PREFIX . 'author_mobile_number', $author_id);
?>
<div class="left">
    <div class="thumb">
        <?php if (!empty($avatar)) : ?>
            <img src="<?php echo esc_url($avatar); ?>" alt="">
        <?php else : ?>
            <img src="<?php echo esc_url($no_image_src); ?>" alt="">
        <?php endif; ?>
    </div>
    <div class="detail">
        <div class="name">
            <span class="uname"><?php esc_html_e($name) ?></span>
        </div>
        <?php if (!empty($title)) : ?>
            <div class="info"><?php esc_html_e($title) ?></div>
        <?php endif; ?>
    </div>
</div>
<div class="right">
    <?php if (!empty($phone)) : ?>
        <a href="tel:<?php esc_attr_e($phone); ?>" class="action phone tooltip" data-title="<?php esc_attr_e('Phone', 'civi-framework') ?>">
            <i class="fas fa-phone-alt"></i>
        </a>
    <?php endif; ?>
    <div class="action action-setting">
        <a href="#" class="icon-setting"><i class="fal fa-ellipsis-v"></i></a>
        <ul class="action-dropdown">
            <?php if ($user_demo == 'yes') : ?>
                <li><a class="btn-add-to-message" data-text="<?php echo esc_attr('This is a "Demo" account so you not cant delete it', 'civi-framework'); ?>" href="#"><?php esc_html_e('Delete', 'civi-framework') ?></a></li>
            <?php else : ?>
                <li><a class="btn-delete" data-mess-id="<?php esc_attr_e($message_id); ?>" href="#"><?php esc_html_e('Delete', 'civi-framework') ?></a></li>
            <?php endif; ?>
        </ul>
    </div>
</div>