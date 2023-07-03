<?php

/**
 * The Template for displaying company archive
 */

defined('ABSPATH') || exit;

get_header('civi');
$company_map_postion = $map_event = '';
$content_company              = civi_get_option('archive_company_layout', 'layout-list');
$enable_company_show_map = civi_get_option('enable_company_show_map', 1);
$enable_company_show_map = !empty($_GET['has_map']) ? Civi_Helper::civi_clean(wp_unslash($_GET['has_map'])) : $enable_company_show_map;

$map_event = '';
if ($enable_company_show_map == 1) {
    $archive_company_filter = 'filter-canvas';
    $company_map_postion = civi_get_option('company_map_postion');
    $company_map_postion = !empty($_GET['map']) ? Civi_Helper::civi_clean(wp_unslash($_GET['map'])) : $company_map_postion;
    if ($company_map_postion == 'map-right') {
        $map_event = 'map-event';
    }
} else {
    $archive_company_filter = civi_get_option('company_filter_sidebar_option', 'filter-left');
};
$archive_company_filter = !empty($_GET['filter']) ? Civi_Helper::civi_clean(wp_unslash($_GET['filter'])) : $archive_company_filter;
$content_company = !empty($_GET['layout']) ? Civi_Helper::civi_clean(wp_unslash($_GET['layout'])) : $content_company;
$archive_classes = array('archive-layout', 'archive-company', $archive_company_filter,$map_event, $company_map_postion);
?>

<div class="<?php echo join(' ', $archive_classes); ?>">
    <?php civi_get_template('company/archive/layout/layout-default.php'); ?>
</div>
<?php
get_footer('civi');
