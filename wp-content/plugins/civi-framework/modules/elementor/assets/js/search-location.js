(function ($) {
    "use strict";

    var HorizontalSearchHandler = function ($scope, $) {
        var map_api_key = civi_template_vars.map_api_key,
            map_type = civi_template_vars.map_type,
            search_form = $scope.find('.civi-form-location'),
            input = search_form.find('.input-search-location'),
			field_select = search_form.find(".civi-select2");

        $("body").on("mousedown", ".civi-form-location .icon-arrow i", function (e) {
            e.preventDefault();
            var select2_container = search_form.find( '.select2.select2-container' );
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
        var locationBtn = search_form.find('.icon-location svg');

        locationBtn.on('click', () => {
            // Check if geolocation is supported by the browser
            if ('geolocation' in navigator) {
            // Use the geolocation API to get the user's current position
            navigator.geolocation.getCurrentPosition((position) => {
                // Get the latitude and longitude from the position object
                var latitude = position.coords.latitude;
            var longitude = position.coords.longitude;

            if (map_type == "google_map") {
                var url = '';
            } else if (map_type == "openstreetmap") {
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


    };

    $(window).on("elementor/frontend/init", function () {
        elementorFrontend.hooks.addAction(
            "frontend/element_ready/civi-search-horizontal.default",
            HorizontalSearchHandler
        );
        elementorFrontend.hooks.addAction(
            "frontend/element_ready/civi-search-vertical.default",
            HorizontalSearchHandler
        );
    });
})(jQuery);
