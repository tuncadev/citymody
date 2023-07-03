<?php

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

if (!class_exists('GLF_Field_Map')) {
	class GLF_Field_Map extends GLF_Field
	{
		public function enqueue()
		{

			$map_type = civi_get_option('map_type', 'mapbox');
			if ($map_type == 'google_map' || $map_type == 'openstreetmap') {
				$api_key = civi_get_option('googlemap_api_key', 'AIzaSyBvPDNG6pePr9iFpeRKaOlaZF_l0oT3lWk');
				$google_map_url = apply_filters('glf_google_map_api_url', 'https://maps.googleapis.com/maps/api/js?key=' . $api_key);

				wp_enqueue_script('google-map', esc_url_raw($google_map_url), array(), '', true);
			}
			wp_enqueue_script(GLF_BASE_RESOURCE_PREFIX . 'map', GLF_BASE_URL . 'fields/map/assets/map.js', array(), '1.4.8', true);
			wp_enqueue_style(GLF_BASE_RESOURCE_PREFIX . 'map', GLF_BASE_URL . 'fields/map/assets/map.css', array(), '1.4.6');
			if ($map_type == 'mapbox') {
				wp_enqueue_script(GLF_BASE_RESOURCE_PREFIX . 'mapbox', GLF_BASE_URL . 'assets/libs/mapbox/mapbox-gl.js', array(), GLF_VER, true);
				wp_enqueue_script(GLF_BASE_RESOURCE_PREFIX . 'mapbox-geocoder', GLF_BASE_URL . 'assets/libs/mapbox/mapbox-gl-geocoder.min.js', array(), GLF_VER, true);
				wp_enqueue_script(GLF_BASE_RESOURCE_PREFIX . 'mapbox-promise', GLF_BASE_URL . 'assets/libs/mapbox/es6-promise.min.js', array(), GLF_VER, true);
				wp_enqueue_script(GLF_BASE_RESOURCE_PREFIX . 'mapbox-promise-auto', GLF_BASE_URL . 'assets/libs/mapbox/es6-promise.auto.min.js', array(), GLF_VER, true);
				wp_enqueue_style(GLF_BASE_RESOURCE_PREFIX . 'mapbox', GLF_BASE_URL . 'assets/libs/mapbox/mapbox-gl.css', array(), GLF_VER);
				wp_enqueue_style(GLF_BASE_RESOURCE_PREFIX . 'mapbox-geocoder', GLF_BASE_URL . 'assets/libs/mapbox/mapbox-gl-geocoder.css', array(), GLF_VER);
			}
			if ($map_type == 'openstreetmap') {
				wp_enqueue_script(GLF_BASE_RESOURCE_PREFIX . 'leaflet', GLF_BASE_URL . 'assets/libs/leaflet/leaflet.js', array(), GLF_VER, true);
				wp_enqueue_script(GLF_BASE_RESOURCE_PREFIX . 'leaflet-src', GLF_BASE_URL . 'assets/libs/leaflet/leaflet-src.js', array(), GLF_VER, true);
				wp_enqueue_script(GLF_BASE_RESOURCE_PREFIX . 'esri-leaflet', GLF_BASE_URL . 'assets/libs/leaflet/esri-leaflet.js', array(), GLF_VER, true);
				wp_enqueue_script(GLF_BASE_RESOURCE_PREFIX . 'esri-leaflet-geocoder', GLF_BASE_URL . 'assets/libs/leaflet/esri-leaflet-geocoder.js', array(), GLF_VER, true);
				wp_enqueue_style(GLF_BASE_RESOURCE_PREFIX . 'leaflet', GLF_BASE_URL . 'assets/libs/leaflet/leaflet.css', array(), GLF_VER);
				wp_enqueue_style(GLF_BASE_RESOURCE_PREFIX . 'esri-leaflet-geocoder', GLF_BASE_URL . 'assets/libs/leaflet/esri-leaflet-geocoder.css', array(), GLF_VER);
			}
		}

		function field_map()
		{
			return 'location,address';
		}

		function render_content($content_args = '')
		{
			$map_type = civi_get_option('map_type', 'google_map');
			$googlemap_type = civi_get_option('googlemap_type', 'roadmap');
			$mapbox_style = civi_get_option('mapbox_style', 'streets-v11');
			$openstreetmap_style = civi_get_option('openstreetmap_style', 'streets-v11');
			$map_zoom_level = civi_get_option('map_zoom_level', '12');
			$mapbox_api_key = civi_get_option('mapbox_api_key', 'pk.eyJ1Ijoic2F5aTc3NDciLCJhIjoiY2tpcXRmYW1tMWpjMjJzbGllbThieTFlaCJ9.eDj6zNLBZpG-veFqXiyVPw');
			$openstreetmap_api_key = civi_get_option('openstreetmap_api_key', 'pk.eyJ1Ijoic2F5aTc3NDciLCJhIjoiY2tpcXRmYW1tMWpjMjJzbGllbThieTFlaCJ9.eDj6zNLBZpG-veFqXiyVPw');
			$field_value = $this->get_value();
			if (!is_array($field_value)) {
				$field_value = array();
			}
			$value_default = array(
				'location' => isset($this->params['default']) ? $this->params['default'] : '-74.5, 40',
				'address'  => ''
			);
			$field_value = wp_parse_args($field_value, $value_default);
			$js_options = isset($this->params['js_options']) ? $this->params['js_options'] : array();
			if (isset($js_options['styles'])) {
				$js_options['styles'] = json_decode($js_options['styles']);
			}
			$placeholder = isset($this->params['placeholder']) ? $this->params['placeholder'] : esc_html__('Enter an address...', 'civi-framework');
?>
			<div class="glf-field-map-inner">
				<input data-field-control="" type="hidden" class="glf-map-location-field" name="<?php echo esc_attr($this->get_name()) ?>[location]" value="<?php echo esc_attr($field_value['location']); ?>" />
				<?php if ((!isset($this->params['show_address']) || $this->params['show_address']) && $map_type == 'google_map') : ?>
					<div class="glf-map-address">
						<div class="glf-map-address-text">
							<input data-field-control="" type="text" placeholder="<?php echo esc_attr($placeholder); ?>" name="<?php echo esc_attr($this->get_name()) ?>[address]" value="<?php echo esc_attr($field_value['address']); ?>" />
						</div>
						<button type="button" class="button"><?php echo esc_html__('Find Address', 'civi-framework'); ?></button>
						<div class="glf-map-suggest"></div>
					</div>
				<?php endif; ?>
				<?php if ($map_type == 'google_map') {  ?>
					<div class="glf-map-canvas glf-map-type" data-maptype="<?php echo $map_type; ?>" data-googlemaptype="<?php echo $googlemap_type; ?>" data-options="<?php echo esc_attr(wp_json_encode($js_options)); ?>" data-zoom="<?php echo $map_zoom_level; ?>" style="height: 300px; width: 100%"></div>
				<?php } else if ($map_type == 'openstreetmap') {  ?>
					<div class="glf-openstreetmap-canvas glf-map-type" data-maptype="<?php echo $map_type; ?>" data-zoom="<?php echo $map_zoom_level; ?>" data-style="<?php echo $openstreetmap_style; ?>" data-api="<?php echo $openstreetmap_api_key; ?>" data-options="<?php echo esc_attr(wp_json_encode($js_options)); ?>" style="height: 300px; width: 100%"></div>
				<?php } else { ?>
					<div class="glf-mapbox-wrapper">
						<div class="glf-mapbox-canvas glf-map-type" data-maptype="<?php echo $map_type; ?>" data-zoom="<?php echo $map_zoom_level; ?>" data-style="<?php echo $mapbox_style; ?>" data-api="<?php echo $mapbox_api_key; ?>" data-options="<?php echo esc_attr(wp_json_encode($js_options)); ?>" style="height: 300px; width: 100%"></div>
					</div>
				<?php } ?>

			</div>
<?php
		}

		/**
		 * Get default value
		 *
		 * @return array
		 */
		function get_default()
		{
			$default = array(
				'location' => isset($this->params['default']) ? $this->params['default'] : '-74.5, 40',
				'address'  => ''
			);

			$field_default = isset($this->params['default']) ? $this->params['default'] : array();
			$default = wp_parse_args($field_default, $default);

			return $this->is_clone() ? array($default) : $default;
		}
	}
}
