(function ($) {
	"use strict";

	var $body = $("body");

	var CiviGridDataHandler = function ($scope, $) {
		var $element = $scope.find(".civi-grid-wrapper");
		$element.CiviGridLayout();

		handlerOverlayHuge($element);

		handlerOverlayMovement($element);
	};

	function handlerOverlayMovement($element) {
		$element.on("mousemove", ".post-wrapper", function (e) {
			var offset = $(this).offset();
			var x = e.pageX - offset.left;
			var y = e.pageY - offset.top;

			var mover = $(this).find(".post-overlay");

			var moverW = mover.width();
			var moverH = mover.height();

			moverW /= 2;
			moverH /= 2;

			x -= moverW;
			y -= moverH;

			// Convert float number to int to fix box blur.
			var finalX = parseInt(x);
			var finalY = parseInt(y);

			mover.css(
				"transform",
				"translate3d(" + finalX + "px," + finalY + "px,0px)"
			);
		});
	}

	function handlerOverlayHuge($element) {
		$element.on("mouseenter", ".grid-item", function () {
			$element.addClass("on");
		});

		$element.on("mouseleave", ".grid-item", function () {
			$element.removeClass("on");
		});
	}

	$(window).on("elementor/frontend/init", function () {
		elementorFrontend.hooks.addAction(
			"frontend/element_ready/civi-blog.default",
			CiviGridDataHandler
		);
		elementorFrontend.hooks.addAction(
			"frontend/element_ready/civi-product.default",
			CiviGridDataHandler
		);
	});
})(jQuery);
