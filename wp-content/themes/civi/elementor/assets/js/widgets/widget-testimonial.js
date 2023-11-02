(function ($) {
    "use strict";

    var Widget_Reload_Carousel = function ($scope, $) {
        var carousel_elem = $scope.find(".elementor-carousel").eq(0);
        if (carousel_elem.length > 0) {
            var settings = carousel_elem.data("slider_options");
            if (settings["isslick"] == "false") {
                alert(settings["isslick"]);
                carousel_elem.unslick();
            } else {
                carousel_elem.slick(settings);
            }
        }
    };

    $(window).on("elementor/frontend/init", function () {
        elementorFrontend.hooks.addAction(
            "frontend/element_ready/civi-testimonial.default",
            Widget_Reload_Carousel
        );
    });
})(jQuery);
