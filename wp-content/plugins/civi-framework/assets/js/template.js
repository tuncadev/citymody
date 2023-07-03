var GLF = GLF || {};

(function ($) {
	"use strict";

	GLF.element = {
		init: function () {
			GLF.element.click_outside();
			GLF.element.payment_method();
			GLF.element.select2();
			GLF.element.sticky_element();
			GLF.element.light_gallery();
			GLF.element.click_to_demo();
			GLF.element.toggle_panel();
			GLF.element.toggle_social();
			GLF.element.toggle_content();
			GLF.element.nav_scroll();
			GLF.element.filter_toggle();
			GLF.element.slick_carousel();
			GLF.element.click_target_blank();

			GLF.element.click_outside(".input-field", ".focus-result");
			GLF.element.click_outside(".location-field", ".focus-result");
			GLF.element.click_outside(".type-field", ".focus-result");

			$(".toggle-select").on("click", ".toggle-show", function () {
				$(this).closest(".toggle-select").find(".toggle-list").slideToggle();
			});
			GLF.element.click_outside(".toggle-select", ".toggle-list", "slide");
		},

		light_gallery: function () {
			jQuery.event.special.touchstart = {
				setup: function (_, ns, handle) {
					this.addEventListener("touchstart", handle, {
						passive: !ns.includes("noPreventDefault"),
					});
				},
			};
		},

		scroll_to: function (element) {
			var offset = $(element).offset().top;
			$("html, body").animate(
				{
					scrollTop: offset - 100,
				},
				500
			);
		},

		click_to_demo: function () {
			$(".menu a").on("click", function (e) {
				var id = $(this).attr("href");
				if (id == "#demo") {
					e.preventDefault();
					scroll_to(id);
				}
			});
		},

		click_outside: function (element, child, type) {
			$(document).on("click", function (event) {
				var $this = $(element);
				if ($this !== event.target && !$this.has(event.target).length) {
					if (type) {
						if (child) {
							$this.find(child).slideUp();
						} else {
							$this.slideUp();
						}
					} else {
						if (child) {
							$this.find(child).hide();
						} else {
							$this.hide();
						}
					}
				}
			});
		},

		payment_method: function () {
			$(".civi-payment-method-wrap .radio").on("click", function () {
				$(".civi-payment-method-wrap .radio").removeClass("active");
				$(this).addClass("active");
			});
		},

		select2: function () {
			if( theme_vars.enable_search_box_dropdown == 1 ){
				$(".civi-select2").select2();
			} else {
				$(".civi-select2").select2({
					minimumResultsForSearch: -1
				});
			}
		},

		sticky_element: function () {
			var offset = "";
			if ($(".uxper-sticky").length > 0) {
				offset = $(".uxper-sticky").offset().top;
			}
			var has_wpadminbar = $("#wpadminbar").length;
			var height_sticky = $(".uxper-sticky").height();
			var wpadminbar = 0;
			var lastScroll = 0;
			if (has_wpadminbar > 0) {
				wpadminbar = $("#wpadminbar").height();
				$(".uxper-sticky").addClass("has-wpadminbar");
			}

			var lastScrollTop = 0;
			$(window).scroll(function (event) {
				var st = $(this).scrollTop();
				if (st < lastScrollTop) {
					$(".uxper-sticky").addClass("on");
				} else {
					$(".uxper-sticky").removeClass("on");
				}

				if (st < height_sticky + wpadminbar) {
					$(".uxper-sticky").removeClass("on");
				}
				lastScrollTop = st;
			});

			$(".block-archive-sidebar").each(function () {
				var _this = $(this);
				if (_this.hasClass("has-sticky")) {
					_this.removeClass("has-sticky");
					_this.parents(".widget-area-init").addClass("has-sticky");
				}
			});
		},

		toggle_panel: function () {
			$(".block-panel").on("click", ".block-tab", function () {
				var parent = $(this).closest(".block-panel");
				if (parent.hasClass("active")) {
					parent.removeClass("active");
					parent.find(".block-content").slideUp(300);
				} else {
					$(".entry-property-element .block-panel").removeClass("active");
					$(".entry-property-element .block-panel .block-content").slideUp(300);
					parent.addClass("active");
					parent.find(".block-content").slideDown(300);
				}
			});
		},

		toggle_social: function () {
			$(".toggle-social").on("click", ".btn-share", function (e) {
				e.preventDefault();
				$(this).parent().toggleClass("active");
				$(this).parent().find(".social-share").slideToggle(300);
			});
		},

		toggle_content: function () {
			var h_desc = $(
				".single-jobs .jobs-content .inner-content .entry-visibility"
			).height();
			if (h_desc > 130) {
				$(".single-jobs .jobs-content").addClass("on");
			}

			$(".show-more").on("click", function (e) {
				e.preventDefault();
				$(this).parents(".jobs-area").addClass("active");
			});

			$(".hide-all").on("click", function (e) {
				e.preventDefault();
				$(this).parents(".jobs-area").removeClass("active");
			});

			$(".open-toggle").on("click", function (e) {
				e.preventDefault();
				$(this).parent().toggleClass("active");
			});

			$(document).on("click", function (event) {
				var $this = $(".form-toggle");
				if ($this !== event.target && !$this.has(event.target).length) {
					$this.removeClass("active");
				}
			});

			$("body").on("click", ".area-booking .minus", function (e) {
				var input = $(this)
					.parents(".product-quantity")
					.find(".input-text.qty");
				var name = $(this)
					.parents(".product-quantity")
					.find(".input-text.qty")
					.attr("name");
				var val = parseInt(input.val()) - 1;
				if (input.val() > 0) input.attr("value", val);
				$(this)
					.parents(".area-booking")
					.find(".open-toggle")
					.addClass("active");
				if (val > 0) {
					$(this)
						.parents(".area-booking")
						.find("." + name + " span")
						.text(parseInt(val));
				} else {
					$(this)
						.parents(".area-booking")
						.find("." + name + " span")
						.text(0);
				}
			});
		},

		nav_scroll: function () {
			$('.nav-scroll a[href^="#"]').on("click", function (event) {
				event.preventDefault();
				var target = $(this.getAttribute("href"));
				var has_wpadminbar = 0;
				if ($("#wpadminbar").height()) {
					has_wpadminbar = $("#wpadminbar").height();
				}
				if (target.length) {
					if ($(window).width() > 767) {
						var top = target.offset().top - 15 - has_wpadminbar;
					} else {
						var top = target.offset().top - 15 - has_wpadminbar;
					}
					$("html, body").stop().animate(
						{
							scrollTop: top,
						},
						500
					);
				}

				$(".nav-scroll li").removeClass("active");
				$(this).parent().addClass("active");
			});

			$(window).scroll(function () {
				var scrollDistance = $(window).scrollTop();

				// Assign active class to nav links while scolling
				$(".group-field").each(function (i) {
					if ($(this).offset().top <= scrollDistance + 50) {
						var href = $(this).attr("id"),
							id = "#" + href;
						$(".nav-scroll a").parent().removeClass("active");
						$(".nav-scroll a").each(function () {
							var attr = $(this).attr("href");
							// For some browsers, `attr` is undefined; for others, `attr` is false. Check for both.
							if (attr == id) {
								// Element has this attribute
								$(this).parent().addClass("active");
							}
						});
					}
				});
			});
		},

		filter_toggle: function () {
			$(".btn-canvas-filter").on("click", function (event) {
				event.preventDefault();
				$("body").css("overflow", "hidden");
				$("body").addClass("open-popup");
				$(this).toggleClass("active");
				$(".archive-filter").toggleClass("open-canvas");
			});

			$(".archive-filter").on(
				"click",
				".btn-close,.bg-overlay,.show-result .civi-button",
				function (e) {
					e.preventDefault();
					$("body").css("overflow", "inherit");
					$("body").removeClass("open-popup");
					$(this).parents(".archive-filter").removeClass("open-canvas");
					$(".btn-canvas-filter").removeClass("active");
				}
			);
		},

		slick_carousel: function () {
			var rtl = false;
			if ($("body").hasClass("rtl")) {
				rtl = true;
			}
			$(".civi-slick-carousel").each(function () {
				var slider = $(this);
				var defaults = {
					slidesToShow: 1,
					slidesToScroll: 1,
					arrows: true,
					prevArrow:
						'<div class="gl-prev slick-arrow"><i class="far fa-angle-left large"></i></div>',
					nextArrow:
						'<div class="gl-next slick-arrow"><i class="far fa-angle-right large"></i></div>',
					dots: false,
					fade: false,
					infinite: false,
					centerMode: false,
					adaptiveHeight: true,
					pauseOnFocus: true,
					pauseOnHover: true,
					swipe: true,
					draggable: true,
					rtl: rtl,
					autoplay: false,
					autoplaySpeed: 250,
					speed: 250,
				};

				if (slider.hasClass("slick-nav")) {
					defaults["prevArrow"] =
						'<div class="gl-prev"><i class="far fa-angle-left large"></i></div>';
					defaults["nextArrow"] =
						'<div class="gl-next"><i class="far fa-angle-right large"></i></div>';
				}

				var config = $.extend({}, defaults, slider.data("slick"));
				// Initialize Slider
				slider.slick(config);
			});
		},

		click_target_blank: function () {
			var $layout = $(".post-type-archive .archive-layout");
			$layout.find(".civi-link-item").prop("target", "_blank");
		},
	};

	GLF.onReady = {
		init: function () {
			GLF.element.init();
		},
	};

	GLF.onLoad = {
		init: function () {},
	};

	GLF.onResize = {
		init: function () {
			// Resize Window
		},
	};

	$(document).ready(GLF.onReady.init);
	$(window).resize(GLF.onResize.init);
	$(window).load(GLF.onLoad.init);
})(jQuery);
