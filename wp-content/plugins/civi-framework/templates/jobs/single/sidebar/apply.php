<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$jobs_id = get_the_ID();
$classes = array();
$enable_sticky_sidebar_type = civi_get_option('enable_sticky_sidebar_type', 1);
if ($enable_sticky_sidebar_type) {
    $classes[] = 'has-sticky';
}
?>

<div class="jobs-apply-sidebar block-archive-sidebar <?php echo implode(" ", $classes); ?>">
    <div class="info-apply">
        <h4><?php esc_html_e('Interested in this job?', 'civi-framework') ?></h4>
    </div>
    <?php civi_get_status_apply($jobs_id);?>
</div>	