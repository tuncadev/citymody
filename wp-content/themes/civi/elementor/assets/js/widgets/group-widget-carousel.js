(function ($) {
	"use strict";

	var SwiperHandler = function ($scope, $) {
		var $element = $scope.find(".civi-slider-widget");

		$element.CiviSwiper();
	};

	var SwiperLinkedHandler = function ($scope, $) {
		var $element = $scope.find(".civi-slider-widget");

		if ($scope.hasClass("civi-swiper-linked-yes")) {
			var thumbsSlider = $element.filter(".civi-thumbs-swiper").CiviSwiper();
			var mainSlider = $element.filter(".civi-main-swiper").CiviSwiper({
				thumbs: {
					swiper: thumbsSlider,
				},
			});
		} else {
			$element.CiviSwiper();
		}
	};

	$(window).on("elementor/frontend/init", function () {
		elementorFrontend.hooks.addAction(
			"frontend/element_ready/civi-image-carousel.default",
			SwiperHandler
		);
		elementorFrontend.hooks.addAction(
			"frontend/element_ready/civi-modern-carousel.default",
			SwiperHandler
		);
		elementorFrontend.hooks.addAction(
			"frontend/element_ready/civi-modern-slider.default",
			SwiperHandler
		);
		elementorFrontend.hooks.addAction(
			"frontend/element_ready/civi-team-member-carousel.default",
			SwiperHandler
		);
		elementorFrontend.hooks.addAction(
			"frontend/element_ready/civi-testimonial.default",
			SwiperLinkedHandler
		);
	});
})(jQuery);
