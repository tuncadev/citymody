<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
<textarea placeholder="<?php echo esc_attr('Write your message', 'civi-framework'); ?>" name="uxper_send_mess"></textarea>
<button id="btn-write-message">
    <?php esc_html_e('Send', 'civi-framework'); ?>
    <span class="btn-loading"><i class="fal fa-spinner fa-spin large"></i></span>
</button>