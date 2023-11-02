(function ($) {
	"use strict";

	var SwiperHandler = function ($scope, $) {
		var $slider = $scope.find(".civi-slider-widget");
		var $countdown = $scope.find(".countdown");
		var countSettings = $countdown.data();
		var daysText = countSettings.daysText;
		var hoursText = countSettings.hoursText;
		var minutesText = countSettings.minutesText;
		var secondsText = countSettings.secondsText;

		$slider.CiviSwiper();
		$countdown.countdown(countSettings.date, function (event) {
			$(this).html(
				event.strftime(
					"" +
						'<div class="countdown-content">' +
						'<div class="hour">' +
						'<span class="number">%H</span>' +
						'<span class="text">' +
						hoursText +
						"</span>" +
						"</div>" +
						'<div class="minute">' +
						'<span class="number">%M</span>' +
						'<span class="text">' +
						minutesText +
						"</span>" +
						"</div>" +
						'<div class="second">' +
						'<span class="number">%S</span>' +
						'<span class="text">' +
						secondsText +
						"</span>" +
						"</div>" +
						"</div>"
				)
			);
		});
	};

	var SwiperLinkedHandler = function ($scope, $) {
		var $element = $scope.find(".civi-slider-widget");

		$element.CiviSwiper();
	};

	$(window).on("elementor/frontend/init", function () {
		elementorFrontend.hooks.addAction(
			"frontend/element_ready/civi-product-carousel-countdown.default",
			SwiperHandler
		);
	});
})(jQuery);
