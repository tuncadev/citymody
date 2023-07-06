<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

global $hide_company_fields, $current_user;
$user_id = $current_user->ID;

$map_type = civi_get_option('map_type', 'mapbox');
$map_default_position = civi_get_option('map_default_position', '');
$lat = civi_get_option('map_lat_default','59.325');
$lng = civi_get_option('map_lng_default','18.070');
if (!empty($company_location['location'])) {
	list($lat, $lng) = !empty($company_location['location']) ? explode(',', $company_location['location']) : array('', '');
} else {
	if ($map_default_position) {
		if ($map_default_position['location']) {
			list($lat, $lng) = !empty($map_default_position['location']) ? explode(',', $map_default_position['location']) : array('', '');
		}
	}
}

civi_get_map_type($lng,$lat,'#submit_company_form');

?>
<div class="row">
	<?php if (!in_array('fields_company_location', $hide_company_fields)) : ?>
		<div class="form-group col-lg-6">
			<label><?php esc_html_e('Location', 'civi-framework') ?></label>
			<div class="select2-field">
				<select name="company_location" class="civi-select2">
					<?php civi_get_taxonomy('company-location', false, true); ?>
				</select>
			</div>
		</div>
	<?php endif; ?>

	<?php if (!in_array('fields_company_map', $hide_company_fields)) : ?>
		<div class="form-group col-lg-6">
			<label for="search-location"><?php esc_html_e('Maps location', 'civi-framework') ?></label>
			<div class="input-area">
				<input type="text" id="search-location" class="form-control" name="civi_map_address" placeholder="<?php esc_attr_e('Full Address', 'civi-framework'); ?>" autocomplete="off">
			</div>
			<input type="hidden" class="form-control company-map-location" name="civi_map_location" />
			<div id="geocoder" class="geocoder"></div>
		</div>
        <div class="form-group col-md-12 company-fields-map">
            <div class="company-fields company-map">
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
			<label for="company_longtitude"><?php esc_html_e('Longtitude', 'civi-framework'); ?></label>
			<input type="text" id="company_longtitude" name="civi_longtitude" value="<?php echo $lng ?>" placeholder="<?php esc_attr_e('0.0000000', 'civi-framework') ?>">
		</div>
		<div class="form-group col-md-6">
			<label for="company_latitude"><?php esc_html_e('Latitude', 'civi-framework'); ?></label>
			<input type="text" id="company_latitude" name="civi_latitude" value="<?php echo $lat ?>" placeholder="<?php esc_attr_e('0.0000000', 'civi-framework') ?>">
		</div>
	<?php endif; ?>
</div>
