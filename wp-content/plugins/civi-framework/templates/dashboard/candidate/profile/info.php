<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly 
}
$hide_candidate_fields = civi_get_option('hide_candidate_fields', array());
if (!is_array($hide_candidate_fields)) {
    $hide_candidate_fields = array();
}
$layout_info = array('resume', 'social', 'location', 'gallery', 'video', 'audio');
?>

<div id="tab-info" class="tab-info">
    <?php civi_get_template('dashboard/candidate/profile/info/basic.php') ?>
    <?php foreach ($layout_info as $value) {
        switch ($value) {
            case 'resume':
                break;
            case 'social':
                break;
            case 'location':
                break;
            case 'gallery':
                break;
            case 'video':
                break;
			case 'audio':
                break;
        }
        if (!in_array('fields_candidate_' . $value, $hide_candidate_fields)) : ?>
            <?php civi_get_template('dashboard/candidate/profile/info/'. $value .'.php') ?>
        <?php endif;
    } ?>
    <?php civi_custom_field_candidate('info'); ?>
</div>