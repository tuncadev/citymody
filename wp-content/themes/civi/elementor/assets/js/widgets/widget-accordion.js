(function ($) {
	"use strict";

	var CiviAccordionHandler = function ($scope, $) {
		var $element = $scope.find(".civi-accordion");
		var settings = $scope.data("settings");

		// Do it only on front-end.
		if (settings && "1" === settings.active_first_item) {
			$element
				.children(".accordion-section:first-child")
				.children(".accordion-content")
				.css("display", "block");
		}

		$element.on("click", ".accordion-header", function (e) {
			e = e || window.event;
			e.preventDefault();
			e.stopPropagation();

			var section = $(this).parent(".accordion-section");
			if (section.hasClass("active")) {
				section.removeClass("active");
				section.children(".accordion-content").slideUp(300);
			} else {
				var parent = $(this).parents(".civi-accordion").first();
				if (!parent.data("multi-open")) {
					parent
						.children(".active")
						.removeClass("active")
						.children(".accordion-content")
						.slideUp(300);
				}
				section.addClass("active");
				section.children(".accordion-content").slideDown(300);
			}
		});
	};

	$(window).on("elementor/frontend/init", function () {
		elementorFrontend.hooks.addAction(
			"frontend/element_ready/civi-accordion.default",
			CiviAccordionHandler
		);
	});
})(jQuery);
