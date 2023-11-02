(function ($) {
	"use strict";

	var CiviGalleryJustifiedContentHandler = function ($scope, $) {
		var $el = $scope.find(".civi-grid");

		var jRowHeight = $el.data("justified-height")
				? $el.data("justified-height")
				: 440,
			jMaxRowHeight = $el.data("justified-max-height")
				? $el.data("justified-max-height")
				: 0,
			jLastRow = $el.data("justified-last-row")
				? $el.data("justified-last-row")
				: "justify",
			$justifiedOptions = {
				rowHeight: jRowHeight,
				margins: 10,
				border: 0,
				lastRow: "nojustify",
			};

		$el.imagesLoaded(function () {
			$el.justifiedGallery($justifiedOptions);

			handlerEntranceAnimation($el);
		});
	};

	function handlerEntranceAnimation($grid) {
		var items = $grid.children(".grid-item");

		items.elementorWaypoint(
			function () {
				// Fix for different ver of waypoints plugin.
				var _self = this.element ? this.element : this;
				var $self = $(_self);
				$self.addClass("animate");
			},
			{
				offset: "90%",
				triggerOnce: true,
			}
		);
	}

	$(window).on("elementor/frontend/init", function () {
		elementorFrontend.hooks.addAction(
			"frontend/element_ready/civi-gallery-justified-content.default",
			CiviGalleryJustifiedContentHandler
		);
	});
})(jQuery);
