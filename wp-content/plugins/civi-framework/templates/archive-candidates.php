<?php

/**
 * The Template for displaying candidate archive
 */

defined('ABSPATH') || exit;

get_header('civi');
$map_event = $candidate_map_postion = '';
$content_candidate = civi_get_option('archive_candidate_layout', 'layout-list');
$enable_candidate_show_map = civi_get_option('enable_candidate_show_map', 1);
$enable_candidate_show_map = !empty($_GET['has_map']) ? Civi_Helper::civi_clean(wp_unslash($_GET['has_map'])) : $enable_candidate_show_map;

if ($enable_candidate_show_map == 1) {
    $archive_candidate_filter = 'filter-canvas';
    $candidate_map_postion = civi_get_option('candidate_map_postion');
    $candidate_map_postion = !empty($_GET['map']) ? Civi_Helper::civi_clean(wp_unslash($_GET['map'])) : $candidate_map_postion;
    if ($candidate_map_postion == 'map-right') {
        $map_event = 'map-event';
    }
} else {
    $archive_candidate_filter = civi_get_option('candidate_filter_sidebar_option', 'filter-left');
};
$archive_candidate_filter = !empty($_GET['filter']) ? Civi_Helper::civi_clean(wp_unslash($_GET['filter'])) : $archive_candidate_filter;
$content_candidate = !empty($_GET['layout']) ? Civi_Helper::civi_clean(wp_unslash($_GET['layout'])) : $content_candidate;
$archive_classes = array('archive-layout', 'archive-candidates', $archive_candidate_filter,$map_event, $candidate_map_postion);

?>
<div class="<?php echo join(' ', $archive_classes); ?>">
    <?php civi_get_template('candidate/archive/layout/layout-default.php'); ?>
</div>
<?php
get_footer('civi');
