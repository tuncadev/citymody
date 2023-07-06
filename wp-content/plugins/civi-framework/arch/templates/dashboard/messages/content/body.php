<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $current_user;
$user_id = $current_user->ID;
$no_image_src = CIVI_PLUGIN_URL . 'assets/images/default-user-image.png';

$creator_user_id = get_post_meta($message_id, CIVI_METABOX_PREFIX . 'creator_message', true);
$recipient_user_id = get_post_meta($message_id, CIVI_METABOX_PREFIX . 'recipient_message', true);
$avatar = get_the_author_meta('author_avatar_image_url', $creator_user_id);
$display_name = get_the_author_meta('display_name', $creator_user_id);

if (intval($creator_user_id) == $user_id) {
    $author_id = get_post_field('post_author', $recipient_user_id);
} else {
    $author_id = $creator_user_id;
}
$name_author = get_the_author_meta('display_name', $author_id);

$args_write = array(
    'post_type' => 'messages',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'order' => 'ASC',
    'meta_query' => array(
        array(
            'key' => CIVI_METABOX_PREFIX . 'post_message_reply',
            'value' => $message_id,
            'compare' => '=='
        )
    ),
);
$data_write = new WP_Query($args_write);

if (intval($creator_user_id) === $user_id) {
    $card = 'card-send';
} else {
    $card = 'card-receive';
}
?>
<div class="card-mess <?php echo esc_attr($card); ?>">
    <div class="thumb">
        <?php if (!empty($avatar)) : ?>
            <img src="<?php echo esc_url($avatar); ?>" alt="">
        <?php else : ?>
            <img src="<?php echo esc_url($no_image_src); ?>" alt="">
        <?php endif; ?>
    </div>
    <div class="detail">
        <div class="name">
            <?php if (intval($creator_user_id) === $user_id) : ?>
                <span class="uname"><?php esc_html_e('You', 'civi-framework'); ?></span>
            <?php else : ?>
                <span class="uname"><?php esc_html_e($display_name); ?></span>
            <?php endif; ?>
            <span class="date"><?php echo date("F j, Y, g:i a"); ?></span>
        </div>
        <div class="desc">
            <?php echo get_the_excerpt($message_id); ?>
        </div>
    </div>
</div>
<?php if ($data_write->have_posts()) { ?>
    <?php while ($data_write->have_posts()) : $data_write->the_post();
        $message_id = get_the_ID();
        $creator_message = get_post_meta($message_id, CIVI_METABOX_PREFIX . 'creator_message_user', true);
        $avatar = get_the_author_meta('author_avatar_image_url', $creator_message);
        if (intval($creator_message) === $user_id) {
            $card = 'card-send';
        } else {
            $card = 'card-receive';
        }
    ?>
        <div class="card-mess <?php echo esc_attr($card); ?>">
            <div class="thumb">
                <div class="thumb">
                    <?php if (!empty($avatar)) : ?>
                        <img src="<?php echo esc_url($avatar); ?>" alt="">
                    <?php else : ?>
                        <img src="<?php echo esc_url($no_image_src); ?>" alt="">
                    <?php endif; ?>
                </div>
            </div>
            <div class="detail">
                <div class="name">
                    <?php if (intval($creator_message) === $user_id) : ?>
                        <span class="uname"><?php esc_html_e('You', 'civi-framework'); ?></span>
                    <?php else : ?>
                        <span class="uname"><?php esc_html_e($name_author); ?></span>
                    <?php endif; ?>
                    <span class="date"><?php echo date("F j, Y, g:i a"); ?></span>
                </div>
                <div class="desc">
                    <?php echo get_the_excerpt($message_id); ?>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
<?php } ?>