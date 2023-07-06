jQuery(document).ready(function () {

    var api_key = civi_openstreet_map_submit_vars.api_key,
        lng = civi_openstreet_map_submit_vars.lng,
        lat = civi_openstreet_map_submit_vars.lat,
        map_zoom = civi_openstreet_map_submit_vars.map_zoom,
        map_style = civi_openstreet_map_submit_vars.map_style,
        map_marker = civi_openstreet_map_submit_vars.map_marker,
        form_submit = civi_openstreet_map_submit_vars.form_submit;


    var lat = parseFloat(lat),
        lng = parseFloat(lng),
        location = jQuery(form_submit).find('input[name="civi_map_location"]'),
        address = jQuery(form_submit).find('input[name="civi_map_address"]'),
        longtitude = jQuery(form_submit).find('input[name="civi_longtitude"]'),
        latitude = jQuery(form_submit).find('input[name="civi_latitude"]');

    var map_location = new L.map('openstreetmap_location');

    var searchControl = L.esri.Geocoding.geosearch().addTo(map_location);

    var results = L.layerGroup().addTo(map_location);

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

    var container = L.DomUtil.get('openstreetmap_map');
    if (container != null) {
        container._leaflet_id = null;
    }

    stores.features.forEach(function (store, i) {
        store.properties.id = i;
    });

    function onMapLoad() {
        var titleLayer_id = 'mapbox/' + map_style;
        L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=' + api_key, {
            attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            id: titleLayer_id,
            tileSize: 512,
            zoomOffset: -1,
            accessToken: api_key
        }).addTo(map_location);

        addMarkers();
    };

    map_location.on('load', onMapLoad);

    map_location.setView([lat, lng], map_zoom);

    /* This will let you use the .remove() function later on */
    if (!('remove' in Element.prototype)) {
        Element.prototype.remove = function () {
            if (this.parentNode) {
                this.parentNode.removeChild(this);
            }
        };
    }

    searchControl.on('results', function (data) {
        results.clearLayers();
        for (var i = data.results.length - 1; i >= 0; i--) {
            address.val(data.results[i].text);
            loadMap(data.results[i].latlng['lng'],data.results[i].latlng['lat']);
        }
    });

    map_location.on('click', function (e) {
        loadMap(e.latlng.lng,e.latlng.lat);
    });

    //function
    function loadMap(lng,lat) {
        location.val(lat + ',' + lng);
        if (location.val() !== '') {
            longtitude.val(lng);
            latitude.val(lat);
        }
    };

    function flyToStore(currentFeature) {
        map_location.flyTo(currentFeature.geometry.coordinates, osm_level);
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
            }).addTo(map_location);

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
});