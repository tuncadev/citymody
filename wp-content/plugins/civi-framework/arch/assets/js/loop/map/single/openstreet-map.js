jQuery(document).ready(function () {
    var api_key = civi_openstreet_map_single_vars.api_key,
        lng = parseFloat(civi_openstreet_map_single_vars.lng),
        lat = parseFloat(civi_openstreet_map_single_vars.lat),
        map_zoom = civi_openstreet_map_single_vars.map_zoom,
        map_style = civi_openstreet_map_single_vars.map_style,
        map_marker = civi_openstreet_map_single_vars.map_marker;

    var element = jQuery('#openstreetmap_map');
    if (element != null) {
        var stores = {
            "type": "FeatureCollection",
            "features": [{
                "type": "Feature",
                "geometry": {
                    "type": "Point",
                    "coordinates": [lat, lng],
                },
                "properties": {
                    "iconSize": [48, 48],
                    "icon": map_marker,
                }
            }]
        };

        stores.features.forEach(function (store, i) {
            store.properties.id = i;
        });

        var container = L.DomUtil.get('openstreetmap_map');
        if (container != null) {
            container._leaflet_id = null;
        }

        var osm_map = new L.map('openstreetmap_map');

        osm_map.on('load', onMapLoad);

        osm_map.setView([lat, lng], map_zoom);


        function onMapLoad() {

            var titleLayer_id = 'mapbox/' + map_style;

            L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=' + api_key, {
                attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                id: titleLayer_id,
                tileSize: 512,
                zoomOffset: -1,
                accessToken: api_key
            }).addTo(osm_map);

            addMarkers();
        };

        function flyToStore(currentFeature) {
            osm_map.flyTo(currentFeature.geometry.coordinates, osm_level);
        }

        /* This will let you use the .remove() function later on */
        if (!('remove' in Element.prototype)) {
            Element.prototype.remove = function () {
                if (this.parentNode) {
                    this.parentNode.removeChild(this);
                }
            };
        }

        function addMarkers() {

            /* For each feature in the GeoJSON object above: */
            stores.features.forEach(function (marker) {
                /* Create a div element for the marker. */
                var el = document.createElement('div');
                /* Assign a unique `id` to the marker. */
                el.id = "marker-" + marker.properties.id;
                /* Assign the `marker` class to each marker for styling. */
                el.className = 'marker';
                el.style.backgroundImage = 'url(' + map_marker + ')';
                el.style.width = marker.properties.iconSize[0] + 'px';
                el.style.height = marker.properties.iconSize[1] + 'px';
                /**
                 * Create a marker using the div element
                 * defined above and add it to the map.
                 **/

                var PlaceIcon = L.Icon.extend({
                    options: {
                        className: 'marker-' + marker.properties.id,
                        iconSize: [40, 40],
                        shadowSize: [50, 64],
                        iconAnchor: [20, 20],
                        shadowAnchor: [4, 62],
                        popupAnchor: [0, -12]
                    }
                });
                var icon = new PlaceIcon({
                    iconUrl: map_marker,
                });

                new L.marker([marker.geometry.coordinates[0], marker.geometry.coordinates[1]], {
                    icon: icon
                }).addTo(osm_map);

                el.addEventListener('click', function (e) {
                    /* Fly to the point */
                    flyToStore(marker);
                    /* Highlight listing in sidebar */
                    var activeItem = document.getElementsByClassName('active');
                    e.stopPropagation();
                    if (activeItem[0]) {
                        activeItem[0].classList.remove('active');
                    }
                });
            });
        }
    }
});
