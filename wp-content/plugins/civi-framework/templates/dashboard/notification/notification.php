<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $current_user;
if (in_array('civi_user_candidate', (array)$current_user->roles)
    || in_array('civi_user_employer', (array)$current_user->roles)) {
    wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'notification');
}
$data_notification = civi_get_data_notification();
?>
<div class="civi-notification">

    <?php civi_get_template('dashboard/notification/count.php', array(
        'data_notification' => $data_notification,
    )); ?>

    <?php if (in_array('civi_user_candidate', (array)$current_user->roles)
    || in_array('civi_user_employer', (array)$current_user->roles)) { ?>
        <div class="content-noti custom-scrollbar">
            <?php civi_get_template('dashboard/notification/content.php', array(
                'data_notification' => $data_notification,
            )); ?>
        </div>
    <?php } ?>

</div>
