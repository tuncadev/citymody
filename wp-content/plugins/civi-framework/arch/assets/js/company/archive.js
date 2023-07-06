(function ($) {
    "use strict";

    var ajax_url = civi_template_vars.ajax_url,
        item_amount = civi_template_vars.item_amount,
        map_effects = civi_template_vars.map_effects,
        not_company = civi_company_archive_vars.not_company,
        map_api_key = civi_template_vars.map_api_key,
        range_min = civi_company_archive_vars.range_min,
        range_max = civi_company_archive_vars.range_max;

    var ajax_call = false;

    var menu_filter_wrap = $(".civi-menu-filter");
    var archive_company = $(".archive-company");
    var civi_map;

    //map
    var mapType = $(".maptype").data("maptype");
    var is_mobile = false;
    var has_map = "";

    if (mapType == "google_map") {
        var company_maps_filter = $("#company-map-filter");
    } else if (mapType == "openstreetmap") {
        var company_maps_filter = $("#maps");
    } else {
        var company_maps_filter = $("#map");
    }

    if (company_maps_filter.length) {
        has_map = "yes";
    }

    if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
        is_mobile = true;
    }

    var civi_hover_map_effects = function () {
        if (map_effects !== '' && has_map) {
            $(".map-event .area-company .civi-company-item").each(function () {
                var title = $(this).find(".add-follow-company").data("company-id");

                if (mapType == "google_map") {
                    $(this).on("mouseenter", function () {
                        if (map_effects == 'popup') {
                            $('div[title="marker' + title + '"]').trigger("click").css('z-index', '2');
                        } else if (map_effects == 'shine') {
                            $('div[title="marker' + title + '"]').trigger("click").addClass("mouseenter");
                        }
                    });

                    $(this).on("mouseleave", function () {
                        if (map_effects == 'popup') {
                            $('div[title="marker' + title + '"]').css('z-index', '0');
                            infowindow.open(null, null);
                        } else if (map_effects == 'shine') {
                            $('div[title="marker' + title + '"]').trigger("click").removeClass("mouseenter");
                        }
                    });
                } else if (mapType == "openstreetmap") {
                    $(this).on("mouseenter", function () {
                        if (map_effects == 'popup') {
                            $(".marker-" + title).trigger("click").css('z-index','2');
                        } else if (map_effects == 'shine'){
                            $(".marker-" + title).trigger("click").addClass("mouseenter");
                        }
                    });

                    $(this).on("mouseleave", function () {
                        if (map_effects == 'popup') {
                            $(".marker-" + title).css('z-index','0');
                            $(".leaflet-popup-close-button").trigger("click");
                        } else if (map_effects == 'shine'){
                            $(".marker-" + title).trigger("click").removeClass("mouseenter");
                        }
                    });
                } else {
                    $(this).on("mouseenter", function () {
                        if (map_effects == 'popup') {
                            $("#marker-" + title).trigger("click").css('z-index','2');
                        } else if (map_effects == 'shine'){
                            $("#marker-" + title).trigger("click").addClass("mouseenter");
                        }
                    });

                    $(this).on("mouseleave", function () {
                        if (map_effects == 'popup') {
                            $(".marker-" + title).css('z-index','0');
                            $(".mapboxgl-popup-close-button").trigger("click");
                        } else if (map_effects == 'shine'){
                            $("#marker-" + title).trigger("click").removeClass("mouseenter");
                        }
                    });
                }
            });
        }
    };

    COMPANY.elements = {
        init: function () {
            this.company_layout();
            this.slider_range();
            this.pagination();
            this.display_clear();
            this.search_cate_location();
            this.filter_clear_top();
            this.filter_clear();

            archive_company
                .find(".civi-menu-filter")
                .on("input", "input.input-control", function () {
                    $(".civi-pagination").find('input[name="paged"]').val(1);
                    $(".form-company-top-filter .btn-top-filter").removeData("clicked");
                    ajax_call = true;
                    COMPANY.elements.ajax_load(ajax_call);
                });

            archive_company.find("select.sort-by").on("change", function () {
                $(".civi-pagination").find('input[name="paged"]').val(1);
                $(".form-company-top-filter .btn-top-filter").removeData("clicked");
                ajax_call = true;
                COMPANY.elements.ajax_load(ajax_call);
            });

            archive_company
                .find(".form-company-top-filter .btn-top-filter")
                .on("click", function (e) {
                    e.preventDefault();
                    $(".civi-pagination").find('input[name="paged"]').val(1);
                    $(this).data("clicked", true);
                    ajax_call = true;
                    COMPANY.elements.ajax_load(ajax_call);
                });

            if (company_maps_filter.length > 0) {
                COMPANY.elements.ajax_load();
            }

            $('.btn-hide-map input[type="checkbox"]').on("change", function () {
                var elem = $(".archive-layout .inner-content");
                var ltf = $(".layout-top-filter .nav-bar");
                if ($(this).attr("checked")) {
                    $("input[value='hide_map']").prop("checked", false);
                } else {
                    $("input[value='hide_map']").prop("checked", true);
                }
                if (elem.hasClass("has-map")) {
                    elem.removeClass("has-map");
                    elem.addClass("no-map");
                    ltf.removeClass("has-map");
                    ltf.addClass("no-map");
                } else {
                    elem.removeClass("no-map");
                    elem.addClass("has-map");
                    ltf.removeClass("no-map");
                    ltf.addClass("has-map");
                }
                ajax_call = true;
                COMPANY.elements.ajax_load(ajax_call);
            });
        },

        slider_range: function () {
            var archive_company = $(".archive-company");
            var min = parseInt(range_min);
            var max = parseInt(range_max);
            var timers = {};

            function delayShowData(type, values) {
                clearTimeout(timers[type]);
                timers[type] = setTimeout(function () {
                    $(".civi-pagination").find('input[name="paged"]').val(1);
                    $(this).data("clicked", true);
                    ajax_call = true;
                    COMPANY.elements.ajax_load(ajax_call);
                }, 500);
            }

            $("#slider-range").slider({
                range: true,
                min: min,
                max: max,
                step: 1,
                values: [min, max],
                slide: function (event, ui) {
                    $("#amount").val(ui.values[0] + " - " + ui.values[1]);
                },
                change: function (event, ui) {
                    var values_start = ui.values[0];
                    var values_end = ui.values[1];
                    if (values_start !== min || values_end !== max) {
                        archive_company.addClass("filter-active");
                    } else {
                        archive_company.removeClass("filter-active");
                    }
                },
                stop: function () {
                    delayShowData();
                },
            });
            $("#amount").val(
                $("#slider-range").slider("values", 0) +
                " - " +
                $("#slider-range").slider("values", 1)
            );
        },

        pagination: function () {
            $("body").on(
                "click",
                ".civi-pagination.ajax-call a.page-numbers",
                function (e) {
                    e.preventDefault();
                    archive_company
                        .find(".civi-pagination .pagination")
                        .addClass("active");
                    archive_company
                        .find(".civi-pagination li .page-numbers")
                        .removeClass("current");
                    $(this).addClass("current");
                    var paged = $(this).text();
                    var current_page = 1;
                    if ($(".civi-pagination").find('input[name="paged"]').val()) {
                        current_page = $(".civi-pagination")
                            .find('input[name="paged"]')
                            .val();
                    }
                    if ($(this).hasClass("next")) {
                        paged = parseInt(current_page) + 1;
                    }
                    if ($(this).hasClass("prev")) {
                        paged = parseInt(current_page) - 1;
                    }
                    archive_company
                        .find(".civi-pagination")
                        .find('input[name="paged"]')
                        .val(paged);
                    ajax_call = true;
                    if ($(this).attr("data-type") == "number") {
                        COMPANY.elements.scroll_to(".area-company");
                        COMPANY.elements.ajax_load(ajax_call);
                    } else {
                        COMPANY.elements.ajax_load(ajax_call, "loadmore");
                    }
                }
            );
        },

        removeClassStartingWith: function (node, begin) {
            node.removeClass(function (index, className) {
                return (
                    className.match(new RegExp("\\b" + begin + "\\S+", "g")) || []
                ).join(" ");
            });
        },

        company_layout: function () {
            archive_company.find(".company-layout a").on("click", function (event) {
                event.preventDefault();
                var layout = $(this).attr("data-layout");
                var type_pagination = $(".civi-pagination").attr("data-type");
                if (type_pagination == "loadmore") {
                    $(".civi-pagination").find('input[name="paged"]').val(1);
                }
                $(this).closest(".company-layout").find(">a").removeClass("active");
                $(this).addClass("active");
                COMPANY.elements.removeClassStartingWith(
                    $(".archive-layout>.inner-content"),
                    "layout-"
                );
                $(this).closest(".inner-content").addClass(layout);

                $(".form-company-top-filter .btn-top-filter").removeData("clicked");

                $(".area-company .civi-company-item").each(function () {
                    COMPANY.elements.removeClassStartingWith($(this), "layout-");
                    $(this).addClass(layout);
                });

                ajax_call = true;
                COMPANY.elements.ajax_load(ajax_call);
            });
        },

        search_cate_location: function () {

			var form = $(".archive-layout .civi-form-location"),
				input = form.find(".archive-search-location"),
				field_select = form.find(".civi-select2");

			$("body").on("mousedown", ".civi-form-location .icon-arrow i", function (e) {
				e.preventDefault();
				var select2_container = form.find( '.select2.select2-container' );
				if(select2_container.hasClass('select2-container--open')){
					field_select.select2("close");
				} else {
					field_select.select2("open");
				}
				field_select.on('select2:select', function (e) {
					var data = e.params.data;
    				input.val(data.text);
				});
			});

            //Geo Location
            var locationBtn = form.find('.icon-location svg');

            locationBtn.on('click', () => {
                // Check if geolocation is supported by the browser
                if ('geolocation' in navigator) {
                // Use the geolocation API to get the user's current position
                navigator.geolocation.getCurrentPosition((position) => {
                    // Get the latitude and longitude from the position object
                    var latitude = position.coords.latitude;
                var longitude = position.coords.longitude;

                if (mapType == "google_map") {
                    var url = '';
                } else if (mapType == "openstreetmap") {
                    var url = 'https://nominatim.openstreetmap.org/reverse?lat=' + latitude + '&lon=' + longitude + '&format=jsonv2';
                } else {
                    var url = 'https://api.mapbox.com/geocoding/v5/mapbox.places/' + longitude + ',' + latitude + '.json?access_token=' + map_api_key + '';
                }

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: (result) => {
                    // Set the value of the location input field to the address
                    input.val(result.features[0].context[2].text);
            },
                error: (error) => {
                    console.log(`Error: ${error}`);
                }
            });
            });
            } else {
                // Geolocation is not supported by the browser
                console.log('Geolocation is not supported by your browser');
            }
        });

        },

        display_clear: function () {
            var archive_company = $(".archive-company");
            if ($(".civi-menu-filter ul.filter-control li.active").length > 0) {
                $(".civi-nav-filter").addClass("active");
                archive_company.find(".civi-clear-filter").show();
            } else {
                $(".civi-nav-filter").removeClass("active");
                archive_company.find(".civi-clear-filter").hide();
            }

            $('.civi-menu-filter input[type="checkbox"]:checked').each(function () {
                if ($(this).length > 0) {
                    $(".civi-nav-filter").addClass("active");
                    $(this).closest(".entry-filter").addClass("open");
                    archive_company.find(".civi-clear-filter").show();
                } else {
                    $(".civi-nav-filter").removeClass("active");
                    $(this).closest(".entry-filter").removeClass("open");
                    archive_company.find(".civi-clear-filter").hide();
                }
            });
        },

        filter_clear_top: function () {
            archive_company.find(".civi-clear-top-filter").on("click", function () {
                $('.form-company-top-filter input[name="company-search-location"]').val("");
                $(".form-company-top-filter .civi-select2").val("");
                $(".form-company-top-filter .civi-select2").select2("destroy");
                $(".form-company-top-filter .civi-select2").select2();
                ajax_call = true;
                COMPANY.elements.ajax_load(ajax_call);
            });
        },

        filter_clear: function () {
            archive_company.find(".civi-clear-filter").on("click", function () {
                $(".civi-menu-filter ul.filter-control li").removeClass("active");
                $('.civi-menu-filter input[type="checkbox"]').prop("checked", false);
                $('.civi-menu-filter select[name="company_filter_salary_min"]').prop(
                    "selectedIndex",
                    0
                );
                $('.civi-menu-filter select[name="company_filter_salary_max"]').prop(
                    "selectedIndex",
                    0
                );
                $('.civi-menu-filter select[name="company_filter_rate"]').prop(
                    "selectedIndex",
                    0
                );
                $("#slider-range").slider("values", 0, parseInt(range_min));
                $("#slider-range").slider("values", 1, parseInt(range_max));
                $("#amount").val(parseInt(range_min) + " - " + parseInt(range_max));
                ajax_call = true;
                COMPANY.elements.ajax_load(ajax_call);
            });
        },

        ajax_load: function (ajax_call, pagination) {
            var title,
                sort_by,
                range_min,
                founded,
                range_max,
                categories,
                location,
                current_term,
                type_term,
                size,
                rating,
                search_fields_sidebar,
                company_layout;
            var paged = 1;

            paged = archive_company
                .find(".civi-pagination")
                .find('input[name="paged"]')
                .val();
            title = archive_company.find('input[name="company_filter_search"]').val();
            current_term = $('input[name="current_term"]').val();
            type_term = $('input[name="type_term"]').val();

            company_layout = archive_company
                .find(".company-layout a.active")
                .attr("data-layout");

            sort_by = menu_filter_wrap
                .find(".sort-by.filter-control li.active a")
                .data("sort");

            var select_sort = $('.archive-layout select[name="sort_by"]').val();
            if (select_sort) {
                sort_by = select_sort;
            }

            search_fields_sidebar = $('input[name="search_fields_sidebar"]').val();
            var result_fields = $.parseJSON(search_fields_sidebar);

            if (result_fields.hasOwnProperty('company-founded')) {
                range_min = archive_company.find("#slider-range").slider("values", 0);
                range_max = archive_company.find("#slider-range").slider("values", 1);
            } else {
                founded = archive_company.find('select[name="company-founded"]').val();
            }

            if (result_fields.hasOwnProperty('company-location')) {
                location = $('input[name="company-location_id[]"]:checked').map(function () {
                    return $(this).val();
                }).get();
            } else {
                location = $('input[name="company-search-location"]').val();
            }

            if (result_fields.hasOwnProperty('company-categories')) {
                categories = $('input[name="company-categories_id[]"]:checked').map(function () {
                    return $(this).val();
                }).get();
            } else {
                categories = $('select[name="company-categories"]').val();
            }

            if (result_fields.hasOwnProperty('company-size')) {
                size = $('input[name="company-size_id[]"]:checked').map(function () {
                    return $(this).val();
                }).get();
            } else {
                size = $('select[name="company-size"]').val();
            }

            if (result_fields.hasOwnProperty('company-rating')) {
                rating = $('input[name="company_rating[]"]:checked').map(function () {
                    return $(this).val();
                }).get();
            } else {
                rating = $('select[name="company-rating"]').val();
            }


            //Map
            var map_html = $(".maptype").clone();
            if (mapType == "google_map") {
                var marker_cluster = null,
                    googlemap_default_zoom = civi_template_vars.googlemap_default_zoom,
                    not_found = civi_template_vars.not_found,
                    clusterIcon = civi_template_vars.clusterIcon,
                    google_map_style = civi_template_vars.google_map_style,
                    google_map_type = civi_template_vars.google_map_type,
                    pin_cluster_enable = civi_template_vars.pin_cluster_enable;

                var infowindow = new google.maps.InfoWindow({
                    maxWidth: 370,
                });

                var silver = [
                    {
                        featureType: "landscape",
                        elementType: "labels",
                        stylers: [
                            {
                                visibility: "off",
                            },
                        ],
                    },
                    {
                        featureType: "transit",
                        elementType: "labels",
                        stylers: [
                            {
                                visibility: "off",
                            },
                        ],
                    },
                    {
                        featureType: "poi",
                        elementType: "labels",
                        stylers: [
                            {
                                visibility: "off",
                            },
                        ],
                    },
                    {
                        featureType: "water",
                        elementType: "labels",
                        stylers: [
                            {
                                visibility: "off",
                            },
                        ],
                    },
                    {
                        featureType: "road",
                        elementType: "labels.icon",
                        stylers: [
                            {
                                visibility: "off",
                            },
                        ],
                    },
                    {
                        stylers: [
                            {
                                hue: "#00aaff",
                            },
                            {
                                saturation: -100,
                            },
                            {
                                gamma: 2.15,
                            },
                            {
                                lightness: 12,
                            },
                        ],
                    },
                    {
                        featureType: "road",
                        elementType: "labels.text.fill",
                        stylers: [
                            {
                                visibility: "on",
                            },
                            {
                                lightness: 24,
                            },
                        ],
                    },
                    {
                        featureType: "road",
                        elementType: "geometry",
                        stylers: [
                            {
                                lightness: 57,
                            },
                        ],
                    },
                ];

                if (has_map) {
                    var civi_search_map_option = {
                        scrollwheel: true,
                        scroll: {x: $(window).scrollLeft(), y: $(window).scrollTop()},
                        zoom: parseInt(googlemap_default_zoom),
                        mapTypeId: google_map_type,
                        draggable: true,
                        fullscreenControl: true,
                        styles: silver,
                        mapTypeControl: false,
                        zoomControlOptions: {
                            position: google.maps.ControlPosition.RIGHT_BOTTOM,
                        },
                        fullscreenControlOptions: {
                            position: google.maps.ControlPosition.RIGHT_BOTTOM,
                        },
                    };
                }

                var civi_add_markers = function (props, map) {
                    $.each(props, function (i, prop) {
                        var latlng = new google.maps.LatLng(prop.lat, prop.lng),
                            marker_url = prop.marker_icon,
                            marker_size = new google.maps.Size(60, 60);
                        var marker_icon = {
                            url: marker_url,
                            size: marker_size,
                            scaledSize: new google.maps.Size(40, 40),
                            origin: new google.maps.Point(-10, -10),
                            anchor: new google.maps.Point(7, 27),
                        };

                        var marker = new google.maps.Marker({
                            position: latlng,
                            url: ".company-" + prop.id,
                            map: map,
                            company: prop.company,
                            icon: marker_icon,
                            draggable: false,
                            title: "marker" + prop.id,
                            animation: google.maps.Animation.DROP,
                        });

                        var prop_title = prop.data ? prop.data.post_title : prop.title;

                        var contentString = document.createElement("div");
                        contentString.className = "civi-marker";
                        contentString.innerHTML =  prop.company;

						var click_marker = false;

                        marker.addListener("mouseover", function () {
                            click_marker = true;
                        });

                        marker.addListener("mouseout", function () {
                            click_marker = false;
                        });

                        google.maps.event.addListener(marker, "click", function () {
                            infowindow.close();
                            infowindow.setContent(contentString);
                            infowindow.open(map, marker);

                            var scale = Math.pow(2, map.getZoom()),
                                offsety = 30 / scale || 0,
                                projection = map.getProjection(),
                                markerPosition = marker.getPosition(),
                                markerScreenPosition =
                                    projection.fromLatLngToPoint(markerPosition),
                                pointHalfScreenAbove = new google.maps.Point(
                                    markerScreenPosition.x,
                                    markerScreenPosition.y - offsety
                                ),
                                aboveMarkerLatLng =
                                    projection.fromPointToLatLng(pointHalfScreenAbove);
                            map.panTo(aboveMarkerLatLng);

                            var elem = $(marker.url);
                            $(".area-company .civi-company-item").removeClass("highlight");
                            if (
                                elem.length > 0 &&
                                click_marker &&
                                $(".archive-company.map-event").length > 0
                            ) {
                                elem.addClass("highlight");
                                $("html, body").animate(
                                    {
                                        scrollTop: elem.offset().top - 50,
                                    },
                                    500
                                );
                            }
                        });

                        markers.push(marker);
                    });
                };

                var civi_my_location = function (map) {
                    var my_location = {};
                    var my_lat = "";
                    var my_lng = "";

                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            function (position) {
                                var pos = {
                                    lat: position.coords.latitude,
                                    lng: position.coords.longitude,
                                };

                                my_lat = position.coords.latitude;
                                my_lng = position.coords.longitude;

                                my_location = {
                                    lat: parseFloat(my_lat),
                                    lng: parseFloat(my_lng),
                                };
                            },
                            function () {
                                handleLocationError(true, infowindow, map.getCenter());
                            }
                        );
                    } else {
                        // Browser doesn't support Geolocation
                        handleLocationError(false, infowindow, map.getCenter());
                    }

                    function CenterControl(controlDiv, map) {
                        // Set CSS for the control border.
                        const controlUI = document.createElement("div");
                        controlUI.style.backgroundColor = "#fff";
                        controlUI.style.border = "2px solid #fff";
                        controlUI.style.borderRadius = "3px";
                        controlUI.style.boxShadow = "0 2px 6px rgba(0,0,0,.3)";
                        controlUI.style.cursor = "pointer";
                        controlUI.style.width = "40px";
                        controlUI.style.height = "40px";
                        controlUI.style.margin = "10px";
                        controlUI.style.textAlign = "center";
                        controlUI.title = "My location";
                        controlDiv.appendChild(controlUI);

                        // Set CSS for the control interior.
                        const controlText = document.createElement("div");
                        controlText.style.fontSize = "18px";
                        controlText.style.lineHeight = "37px";
                        controlText.style.paddingLeft = "5px";
                        controlText.style.paddingRight = "5px";
                        controlText.innerHTML = "<i class='fas fa-location'></i>";
                        controlUI.appendChild(controlText);

                        var marker_icon = {
                            url: default_icon,
                            scaledSize: new google.maps.Size(40, 40),
                            origin: new google.maps.Point(0, 0),
                            anchor: new google.maps.Point(7, 27),
                        };

                        // Setup the click event listeners: simply set the map to Chicago.
                        controlUI.addEventListener("click", () => {
                            var current_location = new google.maps.Marker({
                                position: my_location,
                                map,
                                icon: marker_icon,
                            });

                        infowindow.setPosition(my_location);
                        infowindow.setContent(
                            '<div class="default-result">Your location.</div>'
                        );
                        //infowindow.open(map);
                        map.panTo(my_location);
                    });
                    }

                    const centerControlDiv = document.createElement("div");
                    CenterControl(centerControlDiv, map);

                    centerControlDiv.index = 1;
                    map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(
                        centerControlDiv
                    );

                    function handleLocationError(browserHasGeolocation, infowindow, pos) {
                        infowindow.setPosition(pos);
                        infowindow.setContent(
                            browserHasGeolocation
                                ? "Error: The Geolocation service failed."
                                : "Error: Your browser doesn't support geolocation."
                        );
                        infowindow.open(map);
                    }
                };

                if (!is_mobile) {
                    civi_hover_map_effects();
                }
            } else if (mapType == "openstreetmap") {

                var civi_osm_add_markers = function (props, maps) {
                    $(".maptype").remove();
                    $(map_html).insertAfter("#pac-input");

                    var osm_api = $("#maps").data("key");
                    var osm_level = $("#maps").data("level");
                    var osm_style = $("#maps").data("style");

                    var features_info = [];
                    var lng_args = [];
                    var lat_args = [];

                    $.each(props, function (i, prop) {
                        features_info.push({
                            type: "Feature",
                            geometry: {
                                type: "Point",
                                coordinates: [prop.lat, prop.lng],
                            },
                            properties: {
                                iconSize: [40, 40],
                                id: prop.id,
                                icon: prop.marker_icon,
                                company: prop.company,
                            },
                        });

                        lng_args.push(prop.lng);
                        lat_args.push(prop.lat);
                    });

                    var stores = {
                        type: "FeatureCollection",
                        features: features_info,
                    };

                    var sum_lng = 0;
                    for (var i = 0; i < lng_args.length; i++) {
                        sum_lng += parseInt(lng_args[i], 10);
                    }

                    var avg_lng = 0;

                    if (sum_lng / lng_args.length) {
                        avg_lng = sum_lng / lng_args.length;
                    }

                    var sum_lat = 0;
                    for (var i = 0; i < lat_args.length; i++) {
                        sum_lat += parseInt(lat_args[i], 10);
                    }

                    var avg_lat = 0;

                    if (sum_lat / lat_args.length) {
                        avg_lat = sum_lat / lat_args.length;
                    }

                    var container = L.DomUtil.get("maps");
                    if (container != null) {
                        container._leaflet_id = null;
                    }

                    $(".leaflet-map-pane").remove();
                    $(".leaflet-control-container").remove();

                    var osm_map = new L.map("maps");

                    osm_map.on("load", onMapLoad);

                    osm_map.setView([avg_lat, avg_lng], osm_level);

                    function onMapLoad() {
                        var titleLayer_id = "mapbox/" + osm_style;

                        L.tileLayer(
                            "https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=" +
                            osm_api,
                            {
                                attribution:
                                    'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                                id: titleLayer_id,
                                tileSize: 512,
                                zoomOffset: -1,
                                accessToken: osm_api,
                            }
                        ).addTo(osm_map);

                        /**
                         * Add all the things to the page:
                         * - The location listings on the side of the page
                         * - The markers onto the map
                         */
                        addMarkers();
                    }

                    function flyToStore(currentFeature) {
                        osm_map.flyTo(currentFeature.geometry.coordinates, osm_level);
                    }

                    /* This will let you use the .remove() function later on */
                    if (!("remove" in Element.prototype)) {
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
                            var el = document.createElement("div");
                            /* Assign a unique `id` to the marker. */
                            el.id = "marker-" + marker.properties.id;
                            /* Assign the `marker` class to each marker for styling. */
                            el.className = "marker";
                            el.style.backgroundImage = "url(" + marker.properties.icon + ")";
                            el.style.width = marker.properties.iconSize[0] + "px";
                            el.style.height = marker.properties.iconSize[1] + "px";
                            /**
                             * Create a marker using the div element
                             * defined above and add it to the map.
                             **/
                            properties: {
                            }

                            var icon = L.divIcon({
                                className: "marker-" + marker.properties.id,
                                html:
                                '<div><img src="' +
                                marker.properties.icon +
                                '" alt=""></div>',
                                iconSize: [48, 48],
                            });

                            var markers = new L.marker(
                                [
                                    marker.geometry.coordinates[0],
                                    marker.geometry.coordinates[1],
                                ],
                                {icon: icon}
                            );

                            markers.addTo(osm_map);

                            if (map_effects == 'popup') {
                                markers.bindPopup(marker.properties.company);
                            } else{
                                markers.bindPopup();
                            }

                            el.addEventListener("click", function (e) {
                                /* Fly to the point */
                                flyToStore(marker);
                                /* Highlight listing in sidebar */
                                var activeItem = document.getElementsByClassName("active");
                                e.stopPropagation();
                                if (activeItem[0]) {
                                    activeItem[0].classList.remove("active");
                                }
                            });
                        });
                    }

                    if (!is_mobile) {
                        civi_hover_map_effects();
                    }

                };

                // End Openstreetmap
            } else {
                // Begin Mapbox

                var civi_mapbox_add_markers = function (props, map) {
                    var mapbox_api = $("#map").data("key");
                    var mapbox_level = $("#map").data("level");
                    var mapType = $("#map").data("type");
                    mapboxgl.accessToken = mapbox_api;
                    $(".mapboxgl-canary").remove();
                    $(".mapboxgl-canvas-container").remove();
                    $(".mapboxgl-control-container").remove();
                    var features_info = [];
                    var lng_args = [];
                    var lat_args = [];

                    $.each(props, function (i, prop) {
                        features_info.push({
                            type: "Feature",
                            geometry: {
                                type: "Point",
                                coordinates: [prop.lng, prop.lat],
                            },
                            properties: {
                                iconSize: [48, 48],
                                id: prop.id,
                                icon: prop.marker_icon,
                                company: prop.company,
                            },
                        });

                        lng_args.push(prop.lng);
                        lat_args.push(prop.lat);
                    });

                    var sum_lng = 0;
                    for (var i = 0; i < lng_args.length; i++) {
                        sum_lng += parseInt(lng_args[i], 10);
                    }

                    var avg_lng = 0;

                    if (sum_lng / lng_args.length) {
                        avg_lng = sum_lng / lng_args.length;
                    }

                    var sum_lat = 0;
                    for (var i = 0; i < lat_args.length; i++) {
                        sum_lat += parseInt(lat_args[i], 10);
                    }

                    var avg_lat = 0;

                    if (sum_lat / lat_args.length) {
                        avg_lat = sum_lat / lat_args.length;
                    }

                    var map = new mapboxgl.Map({
                        container: "map",
                        style: "mapbox://styles/mapbox/" + mapType,
                        zoom: mapbox_level,
                        center: [avg_lng, avg_lat],
                    });

                    map.addControl(new mapboxgl.NavigationControl());

                    var stores = {
                        type: "FeatureCollection",
                        features: features_info,
                    };

                    /**
                     * Wait until the map loads to make changes to the map.
                     */
                    map.on("load", function (e) {
                        /**
                         * This is where your '.addLayer()' used to be, instead
                         * add only the source without styling a layer
                         */
                        map.addLayer({
                            id: "locations",
                            type: "symbol",
                            /* Add a GeoJSON source containing company coordinates and information. */
                            source: {
                                type: "geojson",
                                data: stores,
                            },
                            layout: {
                                "icon-image": "",
                                "icon-allow-overlap": true,
                            },
                        });

                        /**
                         * Add all the things to the page:
                         * - The location listings on the side of the page
                         * - The markers onto the map
                         */
                        addMarkers();
                    });

                    function flyToStore(currentFeature) {
                        map.flyTo({
                            center: currentFeature.geometry.coordinates,
                            bearing: 0,
                            duration: 0,
                            speed: 0.2,
                            curve: 1,
                            easing: function (t) {
                                return t;
                            },
                        });
                    }

                    function createPopUp(currentFeature) {
                        var popUps = document.getElementsByClassName("mapboxgl-popup");
                        /** Check if there is already a popup on the map and if so, remove it */
                        if (popUps[0]) popUps[0].remove();

                        var popup = new mapboxgl.Popup({closeOnClick: false})
                            .setLngLat(currentFeature.geometry.coordinates)
                            .setHTML(currentFeature.properties.company)
                            .addTo(map);
                    }

                    /* This will let you use the .remove() function later on */
                    if (!("remove" in Element.prototype)) {
                        Element.prototype.remove = function () {
                            if (this.parentNode) {
                                this.parentNode.removeChild(this);
                            }
                        };
                    }

                    map.on("click", function (e) {
                        /* Determine if a feature in the "locations" layer exists at that point. */
                        var features = map.queryRenderedFeatures(e.point, {
                            layers: ["locations"],
                        });

                        /* If yes, then: */
                        if (features.length) {
                            var clickedPoint = features[0];

                            /* Fly to the point */
                            flyToStore(clickedPoint);

                            /* Close all other popups and display popup for clicked store */
                            createPopUp(clickedPoint);
                        }
                    });

                    function addMarkers() {
                        /* For each feature in the GeoJSON object above: */
                        stores.features.forEach(function (marker) {
                            /* Create a div element for the marker. */
                            var el = document.createElement("div");
                            /* Assign a unique `id` to the marker. */
                            el.id = "marker-" + marker.properties.id;
                            /* Assign the `marker` class to each marker for styling. */
                            el.className = "marker";
                            el.style.backgroundImage = "url(" + marker.properties.icon + ")";
                            el.style.width = marker.properties.iconSize[0] + "px";
                            el.style.height = marker.properties.iconSize[1] + "px";
                            /**
                             * Create a marker using the div element
                             * defined above and add it to the map.
                             **/
                            new mapboxgl.Marker(el, {offset: [0, -23]})
                                .setLngLat(marker.geometry.coordinates)
                                .addTo(map);

                            el.addEventListener("click", function (e) {
                                /* Fly to the point */
                                flyToStore(marker);
                                /* Close all other popups and display popup for clicked store */
                                if (map_effects == 'popup') {
                                    createPopUp(marker);
                                }
                                /* Highlight listing in sidebar */
                                var activeItem = document.getElementsByClassName("active");
                                e.stopPropagation();
                                if (activeItem[0]) {
                                    activeItem[0].classList.remove("active");
                                }
                            });
                        });
                    }
                };

                if (!is_mobile) {
                    civi_hover_map_effects();
                }
                // End Mapbox
            }


            COMPANY.elements.display_clear();

            var page_item = $(".area-company").attr("data-item-amount");

            if (page_item) {
                item_amount = page_item;
            }

            var type_pagination = $(".civi-pagination").attr("data-type");
            $(".area-company .civi-company-item").addClass("skeleton-loading");

            $.ajax({
                dataType: "json",
                url: ajax_url,
                data: {
                    action: "civi_company_archive_ajax",
                    paged: paged,
                    title: title,
                    item_amount: item_amount,
                    sort_by: sort_by,
                    current_term: current_term,
                    type_term: type_term,
                    size: size,
                    rating: rating,
                    range_min: range_min,
                    range_max: range_max,
                    founded: founded,
                    location: location,
                    categories: categories,
                    company_layout: company_layout,
                },
                beforeSend: function () {
                    archive_company
                        .find(".civi-filter-search-map .civi-loading-effect")
                        .fadeIn();
                    if (
                        archive_company
                            .find(".form-company-top-filter .btn-top-filter")
                            .data("clicked")
                    ) {
                        archive_company.find(".btn-top-filter .btn-loading").fadeIn();
                    }
                    if (type_pagination == "loadmore") {
                        archive_company.find(".btn-loading").fadeIn();
                    }
                },
                success: function (data) {
                    archive_company.find(".btn-top-filter .btn-loading").fadeOut();
                    archive_company.find(".civi-filter-search-map .civi-loading-effect").fadeOut();
                    $(".area-company .civi-company-item").removeClass("skeleton-loading");

                    if (data.success === true) {
                        if (ajax_call == true) {
                            if (
                                data.pagination_type == "number" ||
                                pagination !== "loadmore"
                            ) {
                                archive_company.find(".area-company").html(data.company_html);
                                archive_company
                                    .find(".civi-pagination .pagination")
                                    .html(data.pagination);
                                archive_company.find(".result-count").html(data.count_post);
                            } else {
                                archive_company.find(".area-company").append(data.company_html);
                                if (data.hidden_pagination) {
                                    archive_company.find(".civi-pagination .pagination").html("");
                                }
                                archive_company.find(".btn-loading").fadeOut();
                                archive_company
                                    .find(".civi-pagination .pagination")
                                    .removeClass("active");
                            }
                        }
                    } else {
                        if (ajax_call == true) {
                            if (
                                data.pagination_type == "number" ||
                                pagination !== "loadmore"
                            ) {
                                archive_company
                                    .find(".area-company")
                                    .html(
                                        '<div class="civi-ajax-result">' + not_company + "</div>"
                                    );
                                archive_company.find(".result-count").html(data.count_post);
                                archive_company.find(".civi-pagination .pagination").html("");
                            } else {
                                archive_company.find(".area-company").append(data.company_html);
                                if (data.hidden_pagination) {
                                    $(".civi-pagination .pagination").html("");
                                }
                                archive_company
                                    .find(".civi-pagination .pagination")
                                    .removeClass("active");
                            }
                        }
                    }
                    if (!is_mobile) {
                        civi_hover_map_effects();
                    }
                    if (has_map) {
                        if (mapType == "google_map") {
                            markers.forEach(function (marker) {
                                marker.setMap(null);
                            });

                            markers = [];
                            civi_add_markers(data.company, civi_map);
                            civi_my_location(civi_map);
                            civi_map.fitBounds(
                                markers.reduce(function (bounds, marker) {
                                    return bounds.extend(marker.getPosition());
                                }, new google.maps.LatLngBounds())
                            );
                        } else if (mapType == "openstreetmap") {
                            civi_osm_add_markers(data.company, maps);
                        } else {
                            civi_mapbox_add_markers(data.company, map);
                        }
                    }
                },
            });
        },
    };

    COMPANY.onReady = {
        init: function () {
            COMPANY.elements.init();
        },
    };

    COMPANY.onLoad = {
        init: function () {
        },
    };

    $(document).ready(function () {
        COMPANY.elements.init();
    });

    $(window).load(COMPANY.onLoad.init);
})(jQuery);
