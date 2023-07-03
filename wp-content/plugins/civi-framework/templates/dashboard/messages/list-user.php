<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $current_user;
$user_id = $current_user->ID;
$no_image_src = CIVI_PLUGIN_URL . 'assets/images/default-user-image.png';
?>
<ul>
    <?php while ($data_list->have_posts()) : $data_list->the_post();
        $message_id = get_the_ID();
        $creator_message = get_post_meta($message_id, CIVI_METABOX_PREFIX . 'creator_message', true);
        $status = get_post_status($message_id);

        if (intval($creator_message) == $user_id) {
            $recipient = get_post_meta($message_id, CIVI_METABOX_PREFIX . 'recipient_message', true);
            $author_id = get_post_field('post_author', $recipient);
        } else {
            $author_id = $creator_message;
        }

        $class_status = '';
        if ($status == 'pending') {
            $class_status = 'unread';
        }

        $name = get_the_author_meta('display_name', $author_id);
        $avatar = get_the_author_meta('author_avatar_image_url', $author_id);
        $time = human_time_diff(get_the_time('U', $message_id), current_time('timestamp'));
    ?>
        <li class="list-user <?php echo esc_attr($class_status) ?>" data-mess-id="<?php echo esc_attr($message_id) ?>">
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
                    <span class="date"><?php echo sprintf(esc_html__('%s ago', 'civi-framework'), $time); ?></span>
                </div>
                <div class="desc">
                    <?php echo wp_trim_words(get_the_excerpt($message_id), 12); ?>
                </div>
            </div>
        </li>
    <?php endwhile; ?>
</ul>