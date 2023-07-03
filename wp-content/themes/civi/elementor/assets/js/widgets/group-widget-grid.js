(function ($) {
	"use strict";

	var CiviGridHandler = function ($scope, $) {
		var $element = $scope.find(".civi-grid-wrapper");

		$element.CiviGridLayout();
	};

	$(window).on("elementor/frontend/init", function () {
		elementorFrontend.hooks.addAction(
			"frontend/element_ready/civi-image-gallery.default",
			CiviGridHandler
		);
		elementorFrontend.hooks.addAction(
			"frontend/element_ready/civi-testimonial-grid.default",
			CiviGridHandler
		);
	});
})(jQuery);
