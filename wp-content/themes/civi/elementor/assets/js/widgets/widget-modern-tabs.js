(function ($) {
	"use strict";

	var CiviModernTabsHandler = function ($scope, $) {
		function activeTab(obj) {
			$(".civi-modern-tabs ul li").removeClass("active");
			$(obj).addClass("active");
			var id = $(obj).find("a").attr("href");
			$(".modern-tabs-item").hide();
			$(id).show();
		}
		$(".nav-modern-tabs li").click(function () {
			activeTab(this);
			return false;
		});
		activeTab($(".nav-modern-tabs li:first-child"));
        $('.content-modern-tabs .modern-tabs-item:first-child').show();
	};

	$(window).on("elementor/frontend/init", function () {
		elementorFrontend.hooks.addAction(
			"frontend/element_ready/civi-modern-tabs.default",
			CiviModernTabsHandler
		);
	});
})(jQuery);
