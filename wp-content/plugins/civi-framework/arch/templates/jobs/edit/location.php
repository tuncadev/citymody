<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $hide_jobs_fields, $current_user, $jobs_data;
$user_id = $current_user->ID;

$jobs_location = get_post_meta($jobs_data->ID, CIVI_METABOX_PREFIX . 'jobs_location', true);
$jobs_map_address = isset($jobs_location['address']) ? $jobs_location['address'] : '';
$jobs_map_location = isset($jobs_location['location']) ? $jobs_location['location'] : '';

$map_type = civi_get_option('map_type', 'mapbox');
$map_default_position = civi_get_option('map_default_position', '');
$lat = civi_get_option('map_lat_default','59.325');
$lng = civi_get_option('map_lng_default','18.070');
if (!empty($jobs_location['location'])) {
    list($lat, $lng) = !empty($jobs_location['location']) ? explode(',', $jobs_location['location']) : array('', '');
} else {
    if ($map_default_position) {
        if ($map_default_position['location']) {
            list($lat, $lng) = !empty($map_default_position['location']) ? explode(',', $map_default_position['location']) : array('', '');
        }
    }
}
civi_get_map_type($lng,$lat,'#submit_jobs_form');

?>
<div class="row">
    <?php if (!in_array('location', $hide_jobs_fields)) : ?>
        <div class="form-group col-lg-6">
            <label><?php esc_html_e('Location', 'civi-framework') ?></label>
            <div class="select2-field">
				<select name="jobs_location" class="civi-select2">
					<?php civi_get_taxonomy_by_post_id($jobs_data->ID, 'jobs-location', true); ?>
				</select>
			</div>
        </div>
    <?php endif; ?>
    <?php if (!in_array('address', $hide_jobs_fields)) : ?>
        <div class="form-group col-lg-6">
            <label for="search-location"><?php esc_html_e('Maps location', 'civi-framework') ?></label>
            <input type="text" id="search-location" class="form-control" name="civi_map_address"
                   value="<?php echo esc_attr($jobs_map_address); ?>"
                   placeholder="<?php esc_attr_e('Full Address', 'civi-framework'); ?>" autocomplete="off">
            <input type="hidden" class="form-control jobs-map-location" name="civi_map_location"
                   value="<?php echo esc_attr($jobs_map_location); ?>"/>
            <div id="geocoder" class="geocoder"></div>
        </div>
        <div class="form-group col-md-12 jobs-fields-map">
            <div class="jobs-fields jobs-map">
                <?php if ($map_type == 'google_map') { ?>
                    <div class="map_canvas maptype civi-map-warpper" id="map"></div>
                <?php } else if ($map_type == 'openstreetmap') { ?>
                    <div id="openstreetmap_location" class="civi-map-warpper"></div>
                <?php } else { ?>
                    <div id="mapbox_location" class="civi-map-warpper"></div>
                <?php } ?>
            </div>
        </div>
        <div class="form-group col-md-6">
            <label for="jobs_longtitude"><?php esc_html_e('Longtitude', 'civi-framework'); ?></label>
            <input type="text" id="jobs_longtitude" name="civi_longtitude" value="<?php echo $lng ?>"
                   placeholder="<?php esc_attr_e('0.0000000', 'civi-framework') ?>">
        </div>
        <div class="form-group col-md-6">
            <label for="jobs_latitude"><?php esc_html_e('Latitude', 'civi-framework'); ?></label>
            <input type="text" id="jobs_latitude" name="civi_latitude" value="<?php echo $lat ?>"
                   placeholder="<?php esc_attr_e('0.0000000', 'civi-framework') ?>">
        </div>
    <?php endif; ?>
</div>
