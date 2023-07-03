<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $hide_candidate_fields, $current_user, $candidate_data;
$user_id = $current_user->ID;

$candidate_location = get_post_meta($candidate_data->ID, CIVI_METABOX_PREFIX . 'candidate_location', true);
$candidate_map_address = isset($candidate_location['address']) ? $candidate_location['address'] : '';
$candidate_map_location = isset($candidate_location['location']) ? $candidate_location['location'] : '';

$map_type = civi_get_option('map_type', 'mapbox');
$map_default_position = civi_get_option('map_default_position', '');
$lat = civi_get_option('map_lat_default','59.325');
$lng = civi_get_option('map_lng_default','18.070');
if (!empty($candidate_location['location'])) {
    list($lat, $lng) = !empty($candidate_location['location']) ? explode(',', $candidate_location['location']) : array('', '');
} else {
    if ($map_default_position) {
        if ($map_default_position['location']) {
            list($lat, $lng) = !empty($map_default_position['location']) ? explode(',', $map_default_position['location']) : array('', '');
        }
    }
}

civi_get_map_type($lng,$lat,'#candidate-profile-form');

?>
<div class="candidate-submit-location block-from civi-map-form" id="submit_candidate_form">
    <h6><?php esc_html_e('Location', 'civi-framework') ?></h6>
    <div class="row">
        <div class="form-group col-lg-6">
            <label><?php esc_html_e('Location', 'civi-framework') ?></label>
            <div class="select2-field">
				<select name="candidate_location" class="civi-select2 point-mark" required>
					<?php civi_get_taxonomy_by_post_id($candidate_data->ID, 'candidate_locations', true); ?>
				</select>
			</div>
        </div>
        <div class="form-group col-lg-6"  style="display: none;">
            <label for="search-location"><?php esc_html_e('Maps location', 'civi-framework') ?></label>
            <input type="text" id="search-location" class="form-control" name="civi_map_address"
                   value="<?php echo esc_attr($candidate_map_address); ?>"
                   placeholder="<?php esc_attr_e('Full Address', 'civi-framework'); ?>" autocomplete="off">
            <input type="hidden" class="form-control candidate-map-location" name="civi_map_location"
                   value="<?php echo esc_attr($candidate_map_location); ?>"/>
            <div id="geocoder" class="geocoder"></div>
        </div>
		
        <div class="form-group col-md-12 candidate-fields-map" style="display: none;">
            <div class="candidate-fields candidate-map">
                <?php if ($map_type == 'google_map') { ?>
                    <div class="map_canvas maptype civi-map-warpper" id="map"></div>
                <?php } else if ($map_type == 'openstreetmap') { ?>
                    <div id="openstreetmap_location" class="civi-map-warpper"></div>
                <?php } else { ?>
                    <div id="mapbox_location" class="civi-map-warpper"></div>
                <?php } ?>
            </div>
        </div>
        <div class="form-group col-md-6" style="display: none;">
            <label for="candidate_longtitude"><?php esc_html_e('Longtitude', 'civi-framework'); ?></label>
            <input type="text" id="candidate_longtitude" name="civi_longtitude" value="<?php echo $lng ?>"
                   placeholder="<?php esc_attr_e('0.0000000', 'civi-framework') ?>">
        </div>
        <div class="form-group col-md-6" style="display: none;">
            <label for="candidate_latitude"><?php esc_html_e('Latitude', 'civi-framework'); ?></label>
            <input type="text" id="candidate_latitude" name="civi_latitude" value="<?php echo $lat ?>"
                   placeholder="<?php esc_attr_e('0.0000000', 'civi-framework') ?>">
        </div>
    </div>
</div>
