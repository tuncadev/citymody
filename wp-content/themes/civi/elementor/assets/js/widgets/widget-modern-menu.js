(function ($) {
	"use strict";

	var CiviModernMenuHandler = function ($scope, $) {
		$(
			".elementor-widget-civi-modern-menu ul.elementor-nav-menu>li.menu-item-has-children>a"
		).append('<span class="sub-arrow"><i class="fa"></i></span>');
	};

	$(window).on("elementor/frontend/init", function () {
		elementorFrontend.hooks.addAction(
			"frontend/element_ready/civi-modern-menu.default",
			CiviModernMenuHandler
		);
	});
})(jQuery);
