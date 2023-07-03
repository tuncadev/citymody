<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
<div class="mess-content__head">
    <?php civi_get_template('dashboard/messages/content/head.php', array(
        'message_id' => $message_id,
    )); ?>
</div>
<div class="mess-content__body custom-scrollbar">
    <?php civi_get_template('dashboard/messages/content/body.php', array(
        'message_id' => $message_id,
    )); ?>
</div>
<div class="mess-content__action">
    <?php civi_get_template('dashboard/messages/content/action.php'); ?>
</div>
