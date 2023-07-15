<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$logo_email = civi_get_option('logo_email');
$title_email = civi_get_option('title_email');
?>
<?php if (!empty($logo_email)) { ?>
    <img style="text-align: center; margin-bottom: 10px;margin-top: 20px;max-width: 80px;" alt="Logo"
         src="<?php echo $logo_email; ?>">
<?php } ?>
<?php if (!empty($title_email)) { ?>
    <h1><?php echo $title_email . ' ' . get_option('blogname'); ?></h1>
<?php } ?>


