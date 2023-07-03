jQuery(document).ready(function () {

    var api_key = civi_google_map_submit_vars.api_key,
        lng = civi_google_map_submit_vars.lng,
        lat = civi_google_map_submit_vars.lat,
        map_style = civi_google_map_submit_vars.map_style,
        map_type = civi_google_map_submit_vars.map_type,
        form_submit = civi_google_map_submit_vars.form_submit;

    var styles, google_map_style;
    var bounds = new google.maps.LatLngBounds();
    var silver = [{
        elementType: 'geometry',
        stylers: [{
            color: '#f5f5f5'
        }]
    },
        {
            elementType: 'labels.icon',
            stylers: [{
                visibility: 'off'
            }]
        },
        {
            elementType: 'labels.text.fill',
            stylers: [{
                color: '#616161'
            }]
        },
        {
            elementType: 'labels.text.stroke',
            stylers: [{
                color: '#f5f5f5'
            }]
        },
        {
            featureType: 'administrative.land_parcel',
            elementType: 'labels.text.fill',
            stylers: [{
                color: '#bdbdbd'
            }]
        },
        {
            featureType: 'poi',
            elementType: 'geometry',
            stylers: [{
                color: '#eeeeee'
            }]
        },
        {
            featureType: 'poi',
            elementType: 'labels.text.fill',
            stylers: [{
                color: '#757575'
            }]
        },
        {
            featureType: 'poi.park',
            elementType: 'geometry',
            stylers: [{
                color: '#e5e5e5'
            }]
        },
        {
            featureType: 'poi.park',
            elementType: 'labels.text.fill',
            stylers: [{
                color: '#9e9e9e'
            }]
        },
        {
            featureType: 'road',
            elementType: 'geometry',
            stylers: [{
                color: '#ffffff'
            }]
        },
        {
            featureType: 'road.arterial',
            elementType: 'labels.text.fill',
            stylers: [{
                color: '#757575'
            }]
        },
        {
            featureType: 'road.highway',
            elementType: 'geometry',
            stylers: [{
                color: '#dadada'
            }]
        },
        {
            featureType: 'road.highway',
            elementType: 'labels.text.fill',
            stylers: [{
                color: '#616161'
            }]
        },
        {
            featureType: 'road.local',
            elementType: 'labels.text.fill',
            stylers: [{
                color: '#9e9e9e'
            }]
        },
        {
            featureType: 'transit.line',
            elementType: 'geometry',
            stylers: [{
                color: '#e5e5e5'
            }]
        },
        {
            featureType: 'transit.station',
            elementType: 'geometry',
            stylers: [{
                color: '#eeeeee'
            }]
        },
        {
            featureType: 'water',
            elementType: 'geometry',
            stylers: [{
                color: '#9dcaef'
            }]
        },
        {
            featureType: 'water',
            elementType: 'labels.text.fill',
            stylers: [{
                color: '#9e9e9e'
            }]
        }
    ];

    styles = silver;

    if (map_style) {
        styles = JSON.parse(map_style);
    }
    var lat = parseFloat(lat),
        lng = parseFloat(lng);

    var marker;
    var w = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
    var isDraggable = w > 1024;
    var mapOptions = {
            zoom: 14,
            center: {
                lat: lat,
                lng: lng
            },
            mapTypeId: map_type,
            draggable: isDraggable,
            styles: styles,
            mapTypeControl: false,
            streetViewControl: false,
            rotateControl: false,
            zoomControl: true,
            fullscreenControl: true,
        }
    ;
    var map = new google.maps.Map(document.getElementById('map'), mapOptions);

    marker = new google.maps.Marker({
        map: map,
        draggable: true,
        position: {
            lat: lat,
            lng: lng
        },
    });

    var geocoder = new google.maps.Geocoder;

    var infowindow = new google.maps.InfoWindow({
        maxWidth: 370,
    });

    initAutocomplete();
    controlMarker();

    function controlMarker() {
        // This event listener will call addMarker() when the map is clicked.
        map.addListener('click', function(event) {
            if( jQuery('body .lock-marker').length == 0 ) {
                marker.setPosition(event.latLng);
                geocodeLatLng(geocoder, map, infowindow, event.latLng);
                jQuery('input[name="civi_map_location"]').val(event.latLng.lat() + ',' + event.latLng.lng());
            }
        });

        google.maps.event.addListener(marker, 'dragend', function(event) {
            geocodeLatLng(geocoder, map, infowindow, event.latLng);
            jQuery('input[name="civi_map_location"]').val(event.latLng.lat() + ',' + event.latLng.lng());
        });

        google.maps.event.addListener(marker, 'click', function(event) {
            infowindow.open(map,marker);
            jQuery('input[name="civi_map_location"]').val(event.latLng.lat() + ',' + event.latLng.lng());
        });
    }

    function geocodeLatLng(geocoder, map, infowindow, latlng) {
        var findResult = function (results) {
            var result = _.find(results, function (obj) {
                return obj.types[0] == 'locality' && obj.types[1] == "political";
            });
            if (!result) {
                var result = _.find(results, function (obj) {
                    return obj.types[0] == 'administrative_area_level_1' && obj.types[1] == "political";
                });
            }
            return result ? result.short_name : null;
        };

        geocoder.geocode({
            'location': latlng
        }, function (results, status) {
            if (status === 'OK') {
                if (results[0]) {
                    marker.setPosition(latlng);
                    var scale = Math.pow(2, map.getZoom()),
                        offsety = ((50 / scale) || 0),
                        projection = map.getProjection(),
                        markerPosition = marker.getPosition(),
                        markerScreenPosition = projection.fromLatLngToPoint(markerPosition),
                        pointHalfScreenAbove = new google.maps.Point(markerScreenPosition.x, markerScreenPosition.y - offsety),
                        aboveMarkerLatLng = projection.fromPointToLatLng(pointHalfScreenAbove);
                    map.panTo(aboveMarkerLatLng);

                    infowindow.close();
                    infowindow.setContent(results[0].formatted_address);
                    infowindow.open(map, marker);

                    document.getElementById('search-location').value = results[0].formatted_address;
                } else {
                    window.alert('No results found');
                }
            } else {
                window.alert('Geocoder failed due to: ' + status);
            }
        });
    }

    function setMapOnAll(map) {
        marker.setMap(map);
    }

    function clearMarkers() {
        setMapOnAll(null);
    }

    function showMarkers() {
        setMapOnAll(map);
    }

    function initAutocomplete() {

        // Create the search box and link it to the UI element.
        var input = document.getElementById('search-location');
        var autocomplete = new google.maps.places.Autocomplete(input);

        document.getElementById('geocoder').style.display = 'none';

        autocomplete.bindTo('bounds', map);

        autocomplete.setFields(['address_components', 'geometry', 'icon', 'name']);

        var findResult = function(results){
            var result =  _.find(results, function(obj){
                return obj.types[0] == 'locality' && obj.types[1] == "political";
            });
            if( !result ) {
                var result =  _.find(results, function(obj){
                    return obj.types[0] == 'administrative_area_level_1' && obj.types[1] == "political";
                });
            }
            return result ? result.short_name : null;
        };

        autocomplete.addListener('place_changed', function() {
            infowindow.close();
            marker.setVisible(false);
            var place = autocomplete.getPlace();
            if (!place.geometry) {
                // User entered the name of a Place that was not suggested and
                // pressed the Enter key, or the Place Details request failed.
                window.alert("No details available for input: '" + place.name + "'");
                return;
            }

            // If the place has a geometry, then present it on a map.
            if (place.geometry.viewport) {
                map.fitBounds(place.geometry.viewport);
            } else {
                map.setCenter(place.geometry.location);
                map.setZoom(12);  // Why 17? Because it looks good.
            }
            marker.setPosition(place.geometry.location);
            marker.setVisible(true);

            var address = '';
            if (place.address_components) {
                address = [
                    (place.address_components[0] && place.address_components[0].short_name || ''),
                    (place.address_components[1] && place.address_components[1].short_name || ''),
                    (place.address_components[2] && place.address_components[2].short_name || '')
                ].join(' ');
            }

            jQuery('input[name="civi_map_location"]').val(place.geometry.location.lat() + ',' + place.geometry.location.lng());
            jQuery('input[name="civi_longtitude"]').val(place.geometry.location.lng());
            jQuery('input[name="civi_latitude"]').val(place.geometry.location.lat());


            infowindow.setContent(address);
            infowindow.open(map, marker);
        });
    }
});
