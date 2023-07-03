/**
 * map field script
 *
 */

var GLF_MapClass = function($container) {
    this.$container = $container;
};

(function($) {
    "use strict";

    /**
     * Define class field prototype
     */
    GLF_MapClass.prototype = {
        init: function() {
            this.$location = this.$container.find('.glf-map-location-field');
            this.$address = this.$container.find('.glf-map-address input');
            this.$findAddress = this.$container.find('.glf-map-address button');
            this.$maptype = this.$container.find('.glf-map-type').data( 'maptype' );
            this.$mapstyle = this.$container.find('.glf-map-type').data( 'style' );
            this.$mapapi = this.$container.find('.glf-map-type').data( 'api' );
            this.$mapzoom = this.$container.find('.glf-map-type').data( 'zoom' );
            
            if( this.$maptype == 'google_map' ){
                this.$canvas = this.$container.find('.glf-map-canvas');
                this.$googlemap_type = this.$container.find('.glf-map-type').data('googlemaptype');
                this.geocoder = new google.maps.Geocoder();
            } else if( this.$maptype == 'openstreetmap' ){
                this.$canvas = this.$container.find('.glf-openstreetmap-canvas');
                this.geocoder = new google.maps.Geocoder();
            } else {
                this.$mapbox = this.$container.find('.glf-mapbox-canvas');
            }
    
            this.bindMap();   
            this.mapListener();
            this.findAddress();
            this.autoComplete();
        },

        /**
         * Bind map on canvas
         */
        bindMap: function () {
            
            var field = this;
            
            if( this.$maptype == 'google_map' ){
                
                var locationValue = this.$location.val(),
                js_options = this.$canvas.data('options');

                locationValue = locationValue ? locationValue.split(',') : [-33.868419, 151.193245];
                var latLng = new google.maps.LatLng(locationValue[0], locationValue[1]);

                var config_default = {
                    center: latLng,
                    zoom: this.$mapzoom,
                    scrollwheel: false,
                    streetViewControl: 0,
                    mapTypeId: this.$googlemap_type,
                    mapTypeControlOptions: {
                        style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
                        position: google.maps.ControlPosition.LEFT_BOTTOM
                    }
                };
                if (js_options) {
                    config_default = $.extend(config_default, js_options);
                }
    
                this.map = new google.maps.Map(this.$canvas[0], config_default);
                this.marker = new google.maps.Marker({
                    position: latLng,
                    map: this.map,
                    draggable: true
                });
                
            } else if( this.$maptype == 'openstreetmap' ){
                
                var locationValue = field.$location.val(),
                js_options = field.$canvas.data('options');

                locationValue = locationValue ? locationValue.split(',') : [-33.868419, 151.193245];
                var latLng = new google.maps.LatLng(locationValue[0], locationValue[1]);
                
                field.mymap = L.map(field.$canvas[0], { zoomControl: false }).setView([locationValue[0], locationValue[1]], field.$mapzoom);
                
                var titleLayer_id = 'mapbox/' + field.$mapstyle;
    
                L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=' + field.$mapapi, {
                    attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                    id: titleLayer_id,
                    tileSize: 512,
                    zoomOffset: -1,
                    accessToken: field.$mapapi
                }).addTo(field.mymap);

                field.osm_marker = new L.marker([locationValue[0], locationValue[1]], {draggable:'true'});

                field.mymap.addLayer(field.osm_marker);

                var searchControl = L.esri.Geocoding.geosearch().addTo(field.mymap);

                var results = L.layerGroup().addTo(field.mymap);
                
                searchControl.on('results', function (data) {
                    results.clearLayers();
                    for (var i = data.results.length - 1; i >= 0; i--) {
                        results.addLayer(field.osm_marker.setLatLng(new L.LatLng(data.results[i].latlng['lat'], data.results[i].latlng['lng']),{draggable:'true'}));
                        this.$location.val(data.results[i].latlng['lat'] + ',' + data.results[i].latlng['lng']);
                    }
                });
                
            } else {
                var locationValue = this.$location.val(),
                js_options = this.$mapbox.data('options');

                locationValue = locationValue ? locationValue.split(',') : [-74.5, 40];
                
                var config_default = {
                    center: [locationValue[1], locationValue[0]],
                    zoom: 16,
                    scrollwheel: false,
                    streetViewControl: 0,
                };
                if (js_options) {
                    config_default = $.extend(config_default, js_options);
                }
    
                
                mapboxgl.accessToken = this.$container.find('.glf-map-type').data('api');
                this.mapbox = new mapboxgl.Map({
                container: this.$mapbox[0], // container id
                style: 'mapbox://styles/mapbox/streets-v11', // style URL
                center: [locationValue[1], locationValue[0]], // starting position [lng, lat]
                zoom: 6 // starting zoom
                });
                this.mapbox_marker = new mapboxgl.Marker({
                    draggable: true
                })
                .setLngLat([locationValue[1], locationValue[0]])
                .addTo(this.mapbox);

                var mapgeocoder = new MapboxGeocoder({
                    accessToken: mapboxgl.accessToken,
                    mapboxgl: mapboxgl,
                });

                mapgeocoder.on('result', function(e) {
                    $( '.mapboxgl-marker' ).remove();
                    field.$location.val(e.result.center[1] + ',' + e.result.center[0]);
                    field.mapbox_marker = new mapboxgl.Marker({ draggable: true, })
                    .setLngLat(e.result.center)
                    .addTo(field.mapbox)

                    function onDragEnd() {
                        var lngLat = field.mapbox_marker.getLngLat();
                        field.mapbox_marker.setLngLat([lngLat.lng, lngLat.lat]);
                        field.$location.val(lngLat.lat + ',' + lngLat.lng);
                        
                        field.changeField();
                    }
                     
                    field.mapbox_marker.on('dragend', onDragEnd);
                });
                 
                this.mapbox.addControl(mapgeocoder);
                
            }
        },

        /**
         * Map listener
         */
        mapListener: function () {
            
            var field = this;
            
            if( this.$maptype == 'google_map' ){
                
                // Event Click
                google.maps.event.addListener(field.map, 'click', function (event) {
                    field.marker.setPosition(event.latLng);
                    field.$location.val(event.latLng.lat() + ',' + event.latLng.lng());
    
                    field.changeField();
                });
    
                // Event Drag
                google.maps.event.addListener(field.marker, 'drag', function (event) {
                    field.$location.val(event.latLng.lat() + ',' + event.latLng.lng());
    
                    field.changeField();
                });
            } else if( this.$maptype == 'openstreetmap' ){
                
                function onDragEnd() {
                    var lngLat = field.osm_marker.getLatLng();
                    field.$location.val(lngLat.lat + ',' + lngLat.lng);
                    field.osm_marker.setLatLng(new L.LatLng(lngLat.lat, lngLat.lng),{draggable:'true'});
                    field.mymap.panTo(new L.LatLng(lngLat.lat, lngLat.lng));
                    
                    field.changeField();
                }
                 
                field.osm_marker.on('dragend', onDragEnd);
                
                field.mymap.on('click', function(e){
                    var lngLat = field.osm_marker.getLatLng();
                    field.$location.val(e.latlng.lat + ',' + e.latlng.lng);
                    field.osm_marker.setLatLng(new L.LatLng(e.latlng.lat, e.latlng.lng),{draggable:'true'}).addTo(field.mymap);
                    field.mymap.panTo(new L.LatLng(e.latlng.lat, e.latlng.lng));
                    
                    field.changeField();
                });
                
            } else {
            
                function onDragEnd() {
                    var lngLat = field.mapbox_marker.getLngLat();
                    field.mapbox_marker.setLngLat([lngLat.lng, lngLat.lat]);
                    field.$location.val(lngLat.lat + ',' + lngLat.lng);
                    
                    field.changeField();
                }
                 
                field.mapbox_marker.on('dragend', onDragEnd);
                
                field.mapbox.on('click', function(e) {
                    var lngLat = field.mapbox_marker.getLngLat();
                    field.mapbox_marker.setLngLat(e.lngLat).addTo(field.mapbox);
                    field.$location.val(lngLat.lat + ',' + lngLat.lng);
                    field.changeField();
                });
                
            }
            
        },

        findAddress: function () {
            var field = this;

            if( this.$maptype == 'google_map' ){
                
                field.$findAddress.on('click', function (e) {
                    var address = field.$address.val();
                    field.geocoder.geocode({'address': address}, function (results, status) {
                        if (status === google.maps.GeocoderStatus.OK) {
                            
                            field.map.setCenter(results[0].geometry.location);
                            field.marker.setPosition(results[0].geometry.location);
                            field.$location.val(results[0].geometry.location.lat() + ',' + results[0].geometry.location.lng());
    
                            field.changeField();
                        }
                    });
                });
            
            } else if( this.$maptype == 'openstreetmap' ){
                
                field.$findAddress.on('click', function (e) {
                    var address = field.$address.val();
                    field.geocoder.geocode({'address': address}, function (results, status) {
                        if (status === google.maps.GeocoderStatus.OK) {
                            
                            var lngLat = field.osm_marker.getLatLng();
                            field.$location.val(results[0].geometry.location.lat() + ',' + results[0].geometry.location.lng());
                            
                            field.osm_marker.setLatLng([results[0].geometry.location.lat(), results[0].geometry.location.lng()]).addTo(field.mymap);
                            
                            field.mymap.flyTo([results[0].geometry.location.lat(), results[0].geometry.location.lng()], this.$mapzoom, {
                                animate: false,
                                noMoveStart : true,
                            });
    
                            field.changeField();
                        }
                    });
                });
                
            } else {
                
                field.$findAddress.on('click', function (e) {
                    var address = field.$address.val();
                    field.geocoder.geocode({'address': address}, function (results, status) {
                        if (status === google.maps.GeocoderStatus.OK) {
                            
                            var lngLat = field.mapbox_marker.getLngLat();
                            field.$location.val(results[0].geometry.location.lat() + ',' + results[0].geometry.location.lng());
                            
                            field.mapbox_marker.setLngLat([results[0].geometry.location.lng(), results[0].geometry.location.lat()]).addTo(field.mapbox);
                            
                            field.mapbox.flyTo({
                                center: [
                                results[0].geometry.location.lng(),
                                results[0].geometry.location.lat()
                                ],
                                essential: true
                            });
    
                            field.changeField();
                        }
                    });
                });
                
            }
            
            field.$address.on('keydown', function(event) {
                if (event.which === 13) {
                    event.preventDefault();
                    field.$findAddress.trigger('click');
                }
            });
        },

        autoComplete: function () {
            var field = this;

            if( this.$maptype == 'google_map' ){
                
                field.$address.autocomplete({
                    source: function (request, response) {
                        field.geocoder.geocode({
                            'address': request.term
                        }, function (results) {
                            response($.map(results, function (item) {
                                return {
                                    label: item.formatted_address,
                                    value: item.formatted_address,
                                    latitude: item.geometry.location.lat(),
                                    longitude: item.geometry.location.lng()
                                };
                            }));
                        });
                    },
    
                    select: function (event, ui) {
                        var latLng = new google.maps.LatLng(ui.item.latitude, ui.item.longitude);
                        
                        field.map.setCenter(latLng);
                        field.marker.setPosition(latLng);
                        field.$location.val(ui.item.latitude + ',' + ui.item.longitude);
                        
                        field.changeField();
                    }
                });
            
            } else if( this.$maptype == 'openstreetmap' ){
                
                field.$address.autocomplete({
                    source: function (request, response) {
                        field.geocoder.geocode({
                            'address': request.term
                        }, function (results) {
                            response($.map(results, function (item) {
                                return {
                                    label: item.formatted_address,
                                    value: item.formatted_address,
                                    latitude: item.geometry.location.lat(),
                                    longitude: item.geometry.location.lng()
                                };
                            }));
                        });
                    },
    
                    select: function (event, ui) {
                        var latLng = new google.maps.LatLng(ui.item.latitude, ui.item.longitude);
                        
                        field.$location.val(ui.item.latitude + ',' + ui.item.longitude);
                        
                        field.osm_marker.setLatLng([ui.item.latitude, ui.item.longitude]).addTo(field.mymap);
                            
                        field.mymap.flyTo([ui.item.latitude, ui.item.longitude], this.$mapzoom, {
                            animate: false,
                            noMoveStart : true,
                        });
    
                        field.changeField();
                    }
                });
                
            } else {
                
                field.$address.autocomplete({
                    source: function (request, response) {
                        field.geocoder.geocode({
                            'address': request.term
                        }, function (results) {
                            response($.map(results, function (item) {
                                return {
                                    label: item.formatted_address,
                                    value: item.formatted_address,
                                    latitude: item.geometry.location.lat(),
                                    longitude: item.geometry.location.lng()
                                };
                            }));
                        });
                    },
    
                    select: function (event, ui) {
                        var latLng = new google.maps.LatLng(ui.item.latitude, ui.item.longitude);
                        
                        field.$location.val(ui.item.latitude + ',' + ui.item.longitude);
                        
                        field.mapbox_marker.setLngLat([ui.item.longitude, ui.item.latitude]).addTo(field.mapbox);
                            
                        field.mapbox.flyTo({
                            center: [
                            ui.item.longitude,
                            ui.item.latitude
                            ],
                            essential: true
                        });
    
                        field.changeField();
                    }
                });
                
            }
            
            
        },

        changeField: function() {
            var $field = this.$container.closest('.glf-field'),
                value = GLFFieldsConfig.fields.getValue($field);
            GLFFieldsConfig.required.checkRequired($field, value);
        }
    };

    /**
     * Define object field
     */
    var GLF_MapObject = {
        init: function() {
            /**
             * Init Fields after make clone template
             */
            var $configWrapper = $('.glf-meta-config-wrapper');
            $configWrapper = $configWrapper.length ? $configWrapper : $('body');

            $configWrapper.on('glf_make_template_done', function() {
                $('.glf-field-map-inner').each(function () {
                    var field = new GLF_MapClass($(this));
                    field.init();
                });
            });

            /**
             * Init Clone Field after field cloned
             */
            $('.glf-field.glf-field-map').on('glf_add_clone_field', function(event){
                var $items = $(event.target).find('.glf-field-map-inner');
                if ($items.length) {
                    var field = new GLF_MapClass($items);
                    field.init();
                }
            });
        }
    };

    /**
     * Init Field when document ready
     */
    $(document).ready(function() {
        GLF_MapObject.init();
        GLFFieldsConfig.fieldInstance.push(GLF_MapObject);
        
        if( this.$maptype == 'google_map' ){
            $('.glf-tab a').on('glf-tab-clicked', function(event) {
                var sectionId = $(this).attr('href');
                $('.glf-map-canvas', sectionId).each(function() {
                    if ((typeof (google) != "undefined") && (typeof (google.maps) != "undefined") && (typeof (google.maps.event) != "undefined")) {
                        google.maps.event.trigger(map, 'resize');
                    }
                });
            });
        }
    });
})(jQuery);