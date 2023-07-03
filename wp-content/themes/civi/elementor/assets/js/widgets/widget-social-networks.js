(function ($) {
	"use strict";

	var CiviSocialNetworksHandler = function ($scope, $) {
		var $element = $scope.find(".civi-social-networks"),
			$heading = $element.find(".heading"),
			$list_inner = $element.find(".list-inner");

		if (window.matchMedia("(max-width: 576px)").matches) {
			$heading.click(function () {
				$list_inner.slideToggle();
				$(this).find("i").toggleClass("far fa-chevron-up far fa-chevron-down");
			});
		}
	};

	$(window).on("elementor/frontend/init", function () {
		elementorFrontend.hooks.addAction(
			"frontend/element_ready/civi-social-networks.default",
			CiviSocialNetworksHandler
		);
	});
})(jQuery);
