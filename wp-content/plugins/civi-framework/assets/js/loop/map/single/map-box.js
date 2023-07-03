jQuery(document).ready(function () {

    var api_key = civi_map_box_single_vars.api_key,
        lng = civi_map_box_single_vars.lng,
        lat = civi_map_box_single_vars.lat,
        map_zoom = civi_map_box_single_vars.map_zoom,
        map_style = civi_map_box_single_vars.map_style,
        map_marker = civi_map_box_single_vars.map_marker;

    var element = jQuery('#mapbox_map');
    if (element != null) {

        if (lat !== '' && lng !== '') {
            mapboxgl.accessToken = api_key;

            var mapbox_maps = new mapboxgl.Map({
                container: 'mapbox_map',
                style: 'mapbox://styles/mapbox/' + map_style,
                zoom: map_zoom,
                center: [lng, lat],
            });

            mapbox_maps.addControl(new mapboxgl.NavigationControl());

            var stores = {
                "type": "FeatureCollection",
                "features": [{
                    "type": "Feature",
                    "geometry": {
                        "type": "Point",
                        "coordinates": [lng, lat],
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

            /**
             * Wait until the map loads to make changes to the map.
             */
            mapbox_maps.on('load', function (e) {
                /**
                 * This is where your '.addLayer()' used to be, instead
                 * add only the source without styling a layer
                 */
                mapbox_maps.addLayer({
                    "id": "locations",
                    "type": "symbol",
                    /* Add a GeoJSON source containing jobs coordinates and information. */
                    "source": {
                        "type": "geojson",
                        "data": stores
                    },
                    "layout": {
                        "icon-image": "",
                        "icon-allow-overlap": true,
                    }
                });

                /**
                 * Add all the things to the page:
                 * - The location listings on the side of the page
                 * - The markers onto the map
                 */
                addMarkers();

            });

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
                    el.style.backgroundImage = 'url(' + marker.properties.icon + ')';
                    el.style.width = marker.properties.iconSize[0] + 'px';
                    el.style.height = marker.properties.iconSize[1] + 'px';
                    /**
                     * Create a marker using the div element
                     * defined above and add it to the map.
                     **/
                    new mapboxgl.Marker(el, {
                        offset: [0, -50 / 2]
                    })
                        .setLngLat(marker.geometry.coordinates)
                        .addTo(mapbox_maps);

                    el.addEventListener('click', function (e) {
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
    }
});