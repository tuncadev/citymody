<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
wp_enqueue_script(CIVI_PLUGIN_PREFIX . "messages-dashboard");

$data_list = civi_get_data_list_message(false);
$data_list_first = civi_get_data_list_message(true);
$message_id = array();
if ($data_list_first->have_posts()) {
    while ($data_list_first->have_posts()) : $data_list_first->the_post();
        $message_id[] = get_the_ID();
    endwhile;
}
$message_id = !empty($message_id) ? $message_id[0] : '';
?>
<div class="entry-my-page messages-dashboard">
    <div class="entry-title">
        <h4><?php esc_html_e('Messages', 'civi-framework') ?></h4>
    </div>
    <div class="table-dashboard-wapper uxper-messages">
        <?php if ($data_list->have_posts()) { ?>
            <div class="bg-overlay"></div>
            <div class="mess-list">
                <?php civi_get_template('dashboard/messages/tab.php'); ?>
            </div>
            <div class="mess-content">
                <?php civi_get_template('dashboard/messages/content.php', array(
                    'message_id' => $message_id,
                )); ?>
            </div>
        <?php } else { ?>
            <?php civi_get_template('dashboard/messages/empty.php'); ?>
        <?php } ?>
        <div class="civi-loading-effect"><span class="civi-dual-ring"></span></div>
    </div>
</div>