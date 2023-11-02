(function ($) {
	"use strict";

	var CircleProgressChartHandler = function ($scope, $) {
		var $element = $scope.find(".civi-circle-progress-chart");

		elementorFrontend.waypoint($element, function () {
			var countHtml = $element.find(".chart-number");

			var chart = $element.find(".chart").circleProgress({
				startAngle: (-Math.PI / 4) * 2,
				animation: { duration: 1700 },
			});

			if ($element.data("use-number") == "1") {
				chart.on("circle-animation-progress", function (event, progress) {
					countHtml.html(
						parseInt(countHtml.data("max") * progress) +
							"<span>" +
							countHtml.data("units") +
							"</span>"
					);
				});
			}
		});
	};

	$(window).on("elementor/frontend/init", function () {
		elementorFrontend.hooks.addAction(
			"frontend/element_ready/civi-circle-progress-chart.default",
			CircleProgressChartHandler
		);
	});
})(jQuery);
