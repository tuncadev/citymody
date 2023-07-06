<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$mail_footer_user = civi_get_option('mail_footer_user');
?>
<?php if (!empty($mail_footer_user)) { ?>
    <div class="content"><?php echo wp_kses_post($mail_footer_user); ?></div>
<?php } ?>


