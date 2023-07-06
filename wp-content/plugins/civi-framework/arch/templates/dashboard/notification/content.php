<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $current_user;
?>
<?php if (!empty($data_notification)) { ?>
    <div class="head-noti">
        <div class="head-left">
            <span class="noti-refresh">
                <i class="far fa-sync fa-spin"></i>
                <?php esc_html_e('Refresh', 'civi-framework'); ?>
            </span>
            <span class="noti-clear">
                <i class="far fa-trash-alt"></i>
                <?php esc_html_e('Clear All', 'civi-framework'); ?>
            </span>
        </div>
        <a href="#" class="close-noti">
            <i class="far fa-times"></i>
        </a>
    </div>
    <ul>
        <?php foreach ($data_notification as $data) {
            $post_id = $data->ID;
            $user_send_id = get_post_meta($post_id, CIVI_METABOX_PREFIX . 'user_send_noti', true);
            $user_avatar = get_the_author_meta('author_avatar_image_url', $user_send_id);
            $user_send = get_the_author_meta('display_name', $user_send_id);
            $time = human_time_diff(get_the_time('U', $post_id), current_time('timestamp'));
            $mess_noti = get_post_meta($post_id, CIVI_METABOX_PREFIX . 'mess_noti', true);

            $link = get_post_meta($post_id, CIVI_METABOX_PREFIX . 'link_post_noti', true);
            if (!empty($link)) {
                $link_noti = '<a href="' . $link . '">' . get_the_title($post_id) . '</a>';
            } else {
                $link_noti = '';
            }

            $page_link = get_post_meta($post_id, CIVI_METABOX_PREFIX . 'link_page_noti', true);
            if (!empty($page_link)) {
                $link_page = $page_link;
            } else {
                $link_page = '#';
            }

        ?>
            <li>
                <?php if (!empty($user_avatar)) : ?>
                    <img class="avatar" src="<?php echo esc_attr($user_avatar) ?>" alt="" />
                <?php else : ?>
                    <span class="avatar"><i class="far fa-camera"></i></span>
                <?php endif; ?>
                <span class="content-wrapper">
                    <span class="content">
                        <?php echo sprintf(
                            esc_html__('%1s %2s by %3s', 'civi-framework'),
                            $mess_noti,
                            $link_noti,
                            '<span class="athour">' . $user_send . '</span>'
                        ) ?>
                    </span>
                    <span class="date">
                        <?php echo sprintf(esc_html__('%s ago', 'civi-framework'), $time); ?>
                    </span>
                </span>
                <span class="action action-setting">
                    <a href="#" class="icon-setting"><i class="fal fa-ellipsis-v"></i></a>
                    <span class="action-dropdown">
                        <a class="btn-delete" data-noti-id="<?php echo esc_attr(wp_json_encode($post_id)); ?>" href="#"><?php esc_html_e('Delete', 'civi-framework') ?></a>
                    </span>
                </span>
                <a href="<?php echo $link_page ?>" class="link-page"></a>
            </li>
        <?php } ?>
    </ul>
<?php } else { ?>
    <span class="empty"><?php esc_html_e('You do not have any notifications.', 'civi-framework'); ?></span>
<?php } ?>