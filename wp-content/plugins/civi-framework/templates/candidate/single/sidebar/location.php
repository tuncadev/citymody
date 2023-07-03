<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$candidate_id    = get_the_ID();
$candidate_location = get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_location', true);

$map_type = civi_get_option('map_type', 'mapbox');
if (!empty($candidate_location['location'])) {
    list($lat, $lng) = !empty($candidate_location['location']) ? explode(',', $candidate_location['location']) : array('', '');
    $map_direction = "http://maps.google.com/?q=" . $candidate_location['location'];
} else {
    return;
}

$classes = array();
$enable_sticky_sidebar_type = civi_get_option('enable_sticky_candidate_sidebar_type', 1);
if ($enable_sticky_sidebar_type) {
    $classes[] = 'has-sticky';
}

civi_get_single_map_type($lng,$lat);

?>
<div class="block-archive-sidebar candidate-sidebar <?php echo implode(" ", $classes); ?>"  style="display: none;">
    <div class="civi-location">
        <h3><?php esc_html_e('Location', 'civi-framework') ?></h3>
        <a href="<?php echo esc_url($map_direction) ?>" class="civi-button button-border-bottom" target="_blank"><?php esc_html_e('Get direction', 'civi-framework') ?></a>
    </div>
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
