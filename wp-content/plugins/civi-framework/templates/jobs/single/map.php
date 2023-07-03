<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$jobs_id    = get_the_ID();
$jobs_location = get_post_meta($jobs_id, CIVI_METABOX_PREFIX . 'jobs_location', true);
$jobs_address = get_post_meta($jobs_id, CIVI_METABOX_PREFIX . 'jobs_address');
$jobs_address = isset($jobs_address) ? $jobs_address[0] : '';
$map_type = civi_get_option('map_type', 'mapbox');
if (!empty($jobs_location['location']) && !empty($jobs_address)) {
    list($lat, $lng) = !empty($jobs_location['location']) ? explode(',', $jobs_location['location']) : array('', '');
} else {
    return;
}
civi_get_single_map_type($lng,$lat);
?>
<div class="block-archive-inner jobs-maps-details">
    <h4 class="title-jobs"><?php esc_html_e('Maps', 'civi-framework') ?></h4>
    <div class="entry-detail">
        <?php if ($map_type == 'google_map') { ?>
            <div id="google_map" class="civi-map-warpper"></div>
        <?php } else if ($map_type == 'openstreetmap') { ?>
            <div id="openstreetmap_map" class="civi-map-warpper"></div>
        <?php } else { ?>
            <div id="mapbox_map" class="civi-map-warpper"></div>
        <?php } ?>
    </div>
</div>


