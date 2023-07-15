var CIVI = CIVI || {};

(function ($) {
	"use strict";

	var $body = $("body");
	var ajax_url = theme_vars.ajax_url;

	CIVI.element = {
		init: function () {
			CIVI.element.rtl();
			CIVI.element.general();
			CIVI.element.retina_logo();
			CIVI.element.auto_close_loading_effect();
			CIVI.element.widget_categories();
			CIVI.element.swiper_carousel();
			CIVI.element.WidgetCiviCarouselHandler();
			CIVI.element.slick_carousel();
			CIVI.element.main_menu();
			CIVI.element.dropdown_select();
			CIVI.element.elementor_header();
			CIVI.element.sticky_header();
			CIVI.element.popup();
			CIVI.element.toggle_popup();
			CIVI.element.nav_tabs();
			CIVI.element.validate_form();
			CIVI.element.forget_password();
			CIVI.element.cookie_notices();
		},

		windowLoad: function () {
			this.page_loading_effect();
			this.handler_animation();
			this.handler_entrance_queue_animation();

			this.ajax_login_fb();
			this.ajax_login_google();
		},

		rtl: function () {
			if ($("body").attr("dir") == "rtl") {
				$(".elementor-section-stretched").each(function () {
					var val = $(this).css("left");
					$(this).css("left", "auto");
					$(this).css("right", val);
				});
			}
		},

		general: function () {
			$(".mobile-menu .account .user-show").on("click", function (e) {
				e.preventDefault();
				$(this).parent().toggleClass("active");
			});

			$(".block-search.search-icon").on("click", function (e) {
				e.preventDefault();
				$(".search-form-wrapper.canvas-search").addClass("on");
			});

			$(".canvas-search").on("click", ".btn-close,.bg-overlay", function (e) {
				e.preventDefault();
				$(this).parents(".canvas-search").removeClass("on");
				$("body").css("overflow", "auto");
			});

			$(".block-search.search-input").on(
				"keyup",
				".input-search",
				function (e) {
					e.preventDefault();
					if ($(this).val().length > 0) {
						$(this).closest(".search-input").addClass("has-clear");
					} else {
						$(this).closest(".search-input").removeClass("has-clear");
					}
				}
			);

			$(".block-search.search-input").on("click", ".icon-clear", function (e) {
				e.preventDefault();
				$(this).closest(".search-input").find(".input-search").val("");
				$(this).closest(".search-input").removeClass("has-clear");
			});

			$("body").on("click", ".civi-categories li", function (event) {
				event.returnValue = true;
			});

			$("body").on("click", ".civi-categories li > a", function (event) {
				event.returnValue = true;
			});
		},

		retina_logo: function () {
			if (
				window.matchMedia("only screen and (min--moz-device-pixel-ratio: 1.5)")
					.matches ||
				window.matchMedia("only screen and (-o-min-device-pixel-ratio: 3/2)")
					.matches ||
				window.matchMedia(
					"only screen and (-webkit-min-device-pixel-ratio: 1.5)"
				).matches ||
				window.matchMedia("only screen and (min-device-pixel-ratio: 1.5)")
					.matches
			) {
				$(".site-logo img").each(function () {
					$(this).addClass("logo-retina");
					$(this).attr("src", $(this).data("retina"));
				});
			}
		},

		page_loading_effect: function () {
			$(".page-loading-effect").addClass("visibility");
			$(".civi-jobs-item").removeClass("skeleton-loading");

			setTimeout(function () {
				$(".page-loading-effect").remove();
			}, 2000);
		},

		auto_close_loading_effect: function () {
			setTimeout(function () {
				$(".page-loading-effect").remove();
			}, 2000);
		},

		handler_animation: function () {
			var items = $(".modern-grid").children(".grid-item");

			items.waypoint(
				function () {
					// Fix for different ver of waypoints plugin.
					var _self = this.element ? this.element : this;
					var $self = $(_self);
					$self.addClass("animate");
				},
				{
					offset: "100%",
					triggerOnce: true,
				}
			);
		},

		handler_entrance_queue_animation: function () {
			var animateQueueDelay = 200,
				queueResetDelay;
			$(".civi-entrance-animation-queue").each(function () {
				var itemQueue = [],
					queueTimer,
					queueDelay = $(this).data("animation-delay")
						? $(this).data("animation-delay")
						: animateQueueDelay;

				$(this)
					.children(".item")
					.waypoint(
						function () {
							// Fix for different ver of waypoints plugin.
							var _self = this.element ? this.element : $(this);

							queueResetDelay = setTimeout(function () {
								queueDelay = animateQueueDelay;
							}, animateQueueDelay);

							itemQueue.push(_self);
							CIVI.element.process_item_queue(
								itemQueue,
								queueDelay,
								queueTimer
							);
							queueDelay += animateQueueDelay;
						},
						{
							offset: "100%",
							triggerOnce: true,
						}
					);
			});
		},

		process_item_queue: function (
			itemQueue,
			queueDelay,
			queueTimer,
			queueResetDelay
		) {
			clearTimeout(queueResetDelay);
			queueTimer = window.setInterval(function () {
				if (itemQueue !== undefined && itemQueue.length) {
					$(itemQueue.shift()).addClass("animate");
					CIVI.element.process_item_queue();
				} else {
					window.clearInterval(queueTimer);
				}
			}, queueDelay);
		},

		widget_categories: function () {
			$(".widget_categories>ul>li").each(function () {
				if ($(this).find(".children").length > 0) {
					$(this).append('<i class="far fa-plus"></i>');
					$(this).on("click", function () {
						$(this).toggleClass("active");
					});
					$(".widget_categories>ul>li a").on("click", function (e) {
						e.stopPropagation();
					});
				}
			});
		},

		swiper_carousel: function () {
			$(".civi-slider").each(function () {
				if ($(this).hasClass("civi-swiper-linked-yes")) {
					var mainSlider = $(this).children(".civi-main-swiper").CiviSwiper();
					var thumbsSlider = $(this)
						.children(".civi-thumbs-swiper")
						.CiviSwiper();

					mainSlider.controller.control = thumbsSlider;
					thumbsSlider.controller.control = mainSlider;
				} else {
					$(this).CiviSwiper();
				}
			});
		},

		WidgetCiviCarouselHandler: function () {
			$(".civi-carousel-activation").each(function () {
				var carousel_elem = $(this);

				if (carousel_elem.length > 0) {
					var settings = carousel_elem.data("settings");
					var arrows = settings["arrows"];
					var arrow_prev_txt = settings["arrow_prev_txt"];
					var arrow_next_txt = settings["arrow_next_txt"];
					var dots = settings["dots"];
					var autoplay = settings["autoplay"];
					var autoplay_speed = parseInt(settings["autoplay_speed"]) || 3000;
					var animation_speed = parseInt(settings["animation_speed"]) || 300;
					var pause_on_hover = settings["pause_on_hover"];
					var center_mode = settings["center_mode"];
					var center_padding = settings["center_padding"]
						? settings["center_padding"]
						: "50px";
					var display_columns = parseInt(settings["display_columns"]) || 1;
					var scroll_columns = parseInt(settings["scroll_columns"]) || 1;
					var tablet_width = parseInt(settings["tablet_width"]) || 800;
					var tablet_display_columns =
						parseInt(settings["tablet_display_columns"]) || 1;
					var tablet_scroll_columns =
						parseInt(settings["tablet_scroll_columns"]) || 1;
					var mobile_width = parseInt(settings["mobile_width"]) || 480;
					var mobile_display_columns =
						parseInt(settings["mobile_display_columns"]) || 1;
					var mobile_scroll_columns =
						parseInt(settings["mobile_scroll_columns"]) || 1;
					var carousel_style_ck = parseInt(settings["carousel_style_ck"]) || 1;

					if (carousel_style_ck == 4) {
						carousel_elem.slick({
							arrows: arrows,
							prevArrow:
								'<button class="civi-carosul-prev">' +
								arrow_prev_txt +
								"</button>",
							nextArrow:
								'<button class="civi-carosul-next">' +
								arrow_next_txt +
								"</button>",
							dots: dots,
							customPaging: function (slick, index) {
								var data_title = slick.$slides
									.eq(index)
									.find(".civi-data-title")
									.data("title");
								return "<h6>" + data_title + "</h6>";
							},
							infinite: true,
							autoplay: autoplay,
							autoplaySpeed: autoplay_speed,
							speed: animation_speed,
							fade: false,
							pauseOnHover: pause_on_hover,
							slidesToShow: display_columns,
							slidesToScroll: scroll_columns,
							centerMode: center_mode,
							centerPadding: center_padding,
							responsive: [
								{
									breakpoint: tablet_width,
									settings: {
										slidesToShow: tablet_display_columns,
										slidesToScroll: tablet_scroll_columns,
									},
								},
								{
									breakpoint: mobile_width,
									settings: {
										slidesToShow: mobile_display_columns,
										slidesToScroll: mobile_scroll_columns,
									},
								},
							],
						});
					} else {
						carousel_elem.slick({
							arrows: arrows,
							prevArrow:
								'<button class="civi-carosul-prev">' +
								arrow_prev_txt +
								"</button>",
							nextArrow:
								'<button class="civi-carosul-next">' +
								arrow_next_txt +
								"</button>",
							dots: dots,
							infinite: true,
							autoplay: autoplay,
							autoplaySpeed: autoplay_speed,
							speed: animation_speed,
							fade: false,
							pauseOnHover: pause_on_hover,
							slidesToShow: display_columns,
							slidesToScroll: scroll_columns,
							centerMode: center_mode,
							centerPadding: center_padding,
							responsive: [
								{
									breakpoint: tablet_width,
									settings: {
										slidesToShow: tablet_display_columns,
										slidesToScroll: tablet_scroll_columns,
									},
								},
								{
									breakpoint: mobile_width,
									settings: {
										slidesToShow: mobile_display_columns,
										slidesToScroll: mobile_scroll_columns,
									},
								},
							],
						});
					}
				}
			});
		},

		slick_carousel: function () {
			var rtl = false;
			if ($("body").hasClass("rtl")) {
				rtl = true;
			}
			$(".slick-carousel").each(function () {
				var slider = $(this);
				var defaults = {
					slidesToShow: 1,
					slidesToScroll: 1,
					arrows: true,
					prevArrow:
						'<div class="gl-prev slick-arrow"><i class="far fa-chevron-left large"></i></div>',
					nextArrow:
						'<div class="gl-next slick-arrow"><i class="far fa-chevron-right large"></i></div>',
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
						'<div class="gl-prev"><i class="far fa-chevron-left large"></i></div>';
					defaults["nextArrow"] =
						'<div class="gl-next"><i class="far fa-chevron-right large"></i></div>';
				}

				var config = $.extend({}, defaults, slider.data("slick"));
				// Initialize Slider
				slider.slick(config);
			});
		},

		main_menu: function () {
			$(
				".default-menu .menu-item-has-children>a,.site-menu .page_item_has_children>a"
			).append(
				'<span class="chevron"><i class="far fa-chevron-down"></i></span>'
			);

			$(
				".canvas-menu .menu-item-has-children>a,.canvas-menu .page_item_has_children>a"
			).on("click", function (e) {
				e.preventDefault();
				e.stopPropagation();
				var parent = $(this).parent();
				if (parent.hasClass("active")) {
					parent.removeClass("active");
					parent.find(">.sub-menu,>.children").slideUp(300);
				} else {
					if (
						$(this)
							.parents(".menu-item-has-children,.page_item_has_children")
							.hasClass("active") == false
					) {
						$(".canvas-menu li>.sub-menu,.canvas-menu li>.children").slideUp(
							300
						);
						$(".canvas-menu li").removeClass("active");
					}
					parent.find(">.sub-menu,>.children").slideDown(300);
					parent.addClass("active");
				}
			});

			// Open Canvas Menu
			$(".canvas-menu").on("click", ".icon-menu", function (e) {
				e.preventDefault();
				$(this).parents(".canvas-menu").toggleClass("active");
			});

			// Close Canvas Menu
			$(".canvas-menu").on("click", ".btn-close,.bg-overlay", function (e) {
				e.preventDefault();
				$(this).parents(".canvas-menu").removeClass("active");
				$("body").css("overflow", "auto");
			});

			// Check Sub Menu
			$(".site-menu .sub-menu").each(function () {
				var width = $(this).outerWidth();

				if (width > 0) {
					var offset = $(this).offset();
					var w_body = $("body").outerWidth();
					var left = offset.left;
					if (w_body < left + width) {
						$(this).css("left", "-100%");
					}
				}
			});
		},

		dropdown_select: function () {
			$(".dropdown-select").on("click", ".entry-show", function () {
				$(this).parent().toggleClass("active");
			});
			CIVI.element.click_outside(".dropdown-select");
		},

		click_outside: function (element) {
			$(document).on("click", function (event) {
				var $this = $(element);
				if ($this !== event.target && !$this.has(event.target).length) {
					$this.removeClass("active");
				}
			});
		},

		elementor_header: function () {
			if (theme_vars.sticky_header == 1) {
				$(
					".elementor-location-header .elementor-section-wrap>.elementor-element"
				).addClass("uxper-sticky");
			}

			if (theme_vars.float_header == 1) {
				$(
					".elementor-location-header .elementor-section-wrap>.elementor-element"
				).addClass("uxper-float");
			}
		},

		sticky_header: function () {
			var offset = "";
			if ($("header.site-header").length > 0) {
				offset = $("header.site-header").offset().top;
			}
			var has_wpadminbar = $("#wpadminbar").length;
			var wpadminbar = 0;
			var lastScroll = 0;
			if (has_wpadminbar > 0) {
				wpadminbar = $("#wpadminbar").height();
				$(".sticky-header").addClass("has-wpadminbar");
			}
			if ($(window).scrollTop() > offset - wpadminbar) {
				$(".sticky-header").addClass("on");
			}
			$(window).scroll(function () {
				if ($(window).scrollTop() > offset - wpadminbar) {
					$(".sticky-header").addClass("on");
				} else {
					$(".sticky-header").removeClass("on");
				}
			});
		},

		popup: function () {
			$(".civi-on-popup").on("click", function (event) {
				event.preventDefault();
				var id = $(this).attr("href");
				$(id).addClass("active");
				$("body").addClass("open-popup");
			});

			$(".civi-popup").on("click", ".btn-close,.bg-overlay", function () {
				$(this).parents(".civi-popup").removeClass("active");
				$("body").removeClass("open-popup");
				$("body").css("overflow", "auto");
			});
		},

		toggle_popup: function () {
			$(".popup").on("click", ".bg-overlay, .btn-close", function (e) {
				e.preventDefault();
				$("body").css("overflow", "auto");
				$("body").removeClass("open-popup");
				$(this).parents(".popup").removeClass("open");
				$(".site-header").removeClass("show-popup");
			});

			$(".btn-open-popup").on("click", function (e) {
				e.preventDefault();
				$("body").css("overflow", "hidden");
				$("body").addClass("open-popup");
				$(".popup").removeClass("open");
				$(this).parent().find(".popup").addClass("open");
				$(".site-header").addClass("show-popup");
			});

			$("#secondary .jobs-booking .btn-open-popup").on("click", function (e) {
				e.preventDefault();
				$("body").css("overflow", "auto");
			});

			$(".btn-open-claim").on("click", function (e) {
				e.preventDefault();
				$("body").css("overflow", "hidden");
				$("body").addClass("open-popup");
				$(".popup").removeClass("open");
				$(this).parents(".claim-badge").find(".popup").addClass("open");
				$(".site-header").addClass("show-popup");
			});

			$("body").on("click", ".logged-out a", function (e) {
				e.preventDefault();
				$("body").css("overflow", "hidden");
				var tab = $(this).attr("class");
				$(".tabs-form a").removeClass("active");
				if (tab.indexOf("btn-login") != -1) {
					$(".tabs-form a.btn-login").addClass("active");
				}
				if (tab.indexOf("btn-register") != -1) {
					$(".tabs-form a.btn-register").addClass("active");
				}
				$(".body-popup .form-account").removeClass("active");
				$(".canvas-menu").removeClass("active");
				var form_id = $(".tabs-form a.active").attr("data-form");
				$(".body-popup ." + form_id).addClass("active");
				$(".popup").removeClass("open");
				var id = $(this).attr("href");

				$(id).addClass("open");

				//enable social
				$.ajax({
					dataType: "json",
					url: ajax_url,
					data: {
						action: "get_script_social_login",
					},
					beforeSend: function () {},
					success: function (data) {
						if (data.success == true) {
							let fbScript = $(data.fb);
							let ggScript = $(data.google);
							let captcha = $(data.captcha);
							$("body").append(fbScript);
							$("body").append(ggScript);
							$(".popup-account .form-captcha").html(captcha);
						}
					},
				});
			});
		},

		nav_tabs: function () {
			$(".tabs-form a").on("click", function (e) {
				e.preventDefault();
				$(".tabs-form a").removeClass("active");
				$(this).addClass("active");
				$(".body-popup .form-account").each(function () {
					if (!$(this).hasClass("alway-show")) {
						$(this).removeClass("active");
					}
				});

				var id = $(this).attr("data-form");
				if (id == "ux-register") {
					$(this).closest(".inner-popup").find(".footer-popup").fadeOut(0);
				} else {
					$(this).closest(".inner-popup").find(".footer-popup").fadeIn(0);
				}
				$(".body-popup ." + id).addClass("active");
			});

			$(".tab-group > ul li a").on("click", function (e) {
				e.preventDefault();
				$(".tab-group > ul li").removeClass("active");
				$(this).parent().addClass("active");
				$(".tab-group .tab").removeClass("active");
				var id = $(this).attr("href");
				$(id).addClass("active");
			});

			$(".btn-reset-password").on("click", function (e) {
				e.preventDefault();
				$(".ux-login").removeClass("active");
				$(".civi-reset-password-wrap").addClass("active");
			});

			$(".back-to-login").on("click", function (e) {
				e.preventDefault();
				$(".civi-reset-password-wrap").removeClass("active");
				$(".ux-login").addClass("active");
			});
		},

		validate_form: function () {
			$(".ux-login").each(function () {
				var _this = $(this);
				_this.validate({
					rules: {
						email: {
							required: true,
						},
						password: {
							required: true,
							minlength: 5,
							maxlength: 30,
						},
					},
					submitHandler: function (form) {
						$.ajax({
							url: ajax_url,
							type: "POST",
							cache: false,
							dataType: "json",
							data: {
								email: _this.find('input[name="email"]').val(),
								password: _this.find('input[name="password"]').val(),
								captcha: _this.find(".civi-captcha").val(),
								num_captcha: _this.find(".civi-num-captcha").data("captcha"),
								action: "get_login_user",
							},
							beforeSend: function () {
								_this
									.find("p.msg")
									.removeClass("text-error text-success text-warning");
								_this.find("p.msg").text(theme_vars.send_user_info);
								_this.find("p.msg").show();
								$(".popup-account .loading-effect").fadeIn();
							},
							success: function (data) {
								_this.find("p.msg").text(data.messages);
								if (data.success != true) {
									_this.find("p.msg").addClass(data.class);
								} else {
									if (data.url_redirect) {
										window.location.href = data.url_redirect;
									} else {
										location.reload();
									}
								}
								$(".popup-account .loading-effect").fadeOut();
							},
						});
					},
				});
			});

			$(".ux-register").each(function () {
				var _this = $(this);
				var _next = $(this).next();
				_this.validate({
					rules: {
						reg_firstname: {
							required: true,
						},
						reg_lastname: {
							required: true,
						},
						reg_company_name: {
							required: true,
						},
						reg_email: {
							required: true,
							email: true,
						},
						reg_password: {
							required: true,
							minlength: 5,
							maxlength: 20,
						},
						accept_account: {
							required: true,
						},
					},
					submitHandler: function (form) {
						$.ajax({
							url: ajax_url,
							type: "POST",
							cache: false,
							dataType: "json",
							data: {
								account_type: _this
									.find('input[name="account_type"]:checked')
									.val(),
								firstname: _this.find('input[name="reg_firstname"]').val(),
								lastname: _this.find('input[name="reg_lastname"]').val(),
								companyname: _this.find('input[name="reg_company_name"]').val(),
								email: _this.find('input[name="reg_email"]').val(),
								password: _this.find('input[name="reg_password"]').val(),
								captcha: _this.find(".civi-captcha").val(),
								action: "get_register_user",
							},
							beforeSend: function () {
								_this
									.find("p.msg")
									.removeClass("text-error text-success text-warning");
								_this.find("p.msg").text(theme_vars.send_user_info);
								_this.find("p.msg").show();
								$(".popup-account .loading-effect").fadeIn();
							},
							success: function (data) {
								_this.find("p.msg").text(data.messages);
								if (data.cookie_name) {
									_this.find("p.msg").addClass(data.class);
									_this.removeClass("active");
									_next.addClass("active");
								} else {
									if (data.success != true) {
										_this.find("p.msg").addClass(data.class);
									} else {
										if (data.url_redirect) {
											window.location.href = data.url_redirect;
										} else {
											location.reload();
										}
									}
								}
								$(".popup-account .loading-effect").fadeOut();
							},
						});
					},
				});
			});

			$(".ux-verify").each(function () {
				var _this = $(this);
				var _prev = $(this).prev();
				_this.validate({
					rules: {
						verify_code: {
							required: true,
						},
					},
					submitHandler: function (form) {
						$.ajax({
							url: ajax_url,
							type: "POST",
							cache: false,
							dataType: "json",
							data: {
								verify_code: _this.find('input[name="verify_code"]').val(),
								account_type: _prev
									.find('input[name="account_type"]:checked')
									.val(),
								firstname: _prev.find('input[name="reg_firstname"]').val(),
								lastname: _prev.find('input[name="reg_lastname"]').val(),
								companyname: _prev.find('input[name="reg_company_name"]').val(),
								email: _prev.find('input[name="reg_email"]').val(),
								password: _prev.find('input[name="reg_password"]').val(),
								action: "verify_code",
							},
							beforeSend: function () {
								_this
									.find("p.msg")
									.removeClass("text-error text-success text-warning");
								_this.find("p.msg").text(theme_vars.send_user_info);
								_this.find("p.msg").show();
								$(".popup-account .loading-effect").fadeIn();
							},
							success: function (data) {
								_this.find("p.msg").text(data.messages);
								if (data.success != true) {
									_this.find("p.msg").addClass(data.class);
								} else {
									if (data.url_redirect) {
										window.location.href = data.url_redirect;
									} else {
										location.reload();
									}
								}
								$(".popup-account .loading-effect").fadeOut();
							},
						});
					},
				});
			});
			if (userLocale == "en") {
				console.log("this is en");
				jQuery.extend(jQuery.validator.messages, {
				
					required: "This field is required",
					remote: "Please fix this field",
					email: "A valid email address is required",
					url: "Please enter a valid URL",
					date: "Please enter a valid date",
					dateISO: "Please enter a valid date (ISO)",
					number: "Please enter a valid number.",
					digits: "Please enter only digits",
					creditcard: "Please enter a valid credit card number",
					equalTo: "Please enter the same value again",
					accept: "Please enter a value with a valid extension",
					maxlength: jQuery.validator.format(
						"Please enter no more than {0} characters"
					),
					minlength: jQuery.validator.format(
						"Please enter at least {0} characters"
					),
					rangelength: jQuery.validator.format(
						"Please enter a value between {0} and {1} characters long"
					),
					range: jQuery.validator.format(
						"Please enter a value between {0} and {1}"
					),
					max: jQuery.validator.format(
						"Please enter a value less than or equal to {0}"
					),
					min: jQuery.validator.format(
						"Please enter a value greater than or equal to {0}"
					),
				});
			} else {
				jQuery.extend(jQuery.validator.messages, {
					console.log("this is not en");
					required: "Bu alan gereklidir",
					remote: "Bu alanı düzeltin lütfen",
					email: "Geçerli bir e-posta adresi gerekli",
					url: "Lütfen geçerli bir adres girin",
					date: "Lütfen geçerli bir tarih giriniz",
					dateISO: "Lütfen geçerli bir tarih girin (ISO)",
					number: "Lütfen geçerli bir sayı girin.",
					digits: "lütfen sadece rakam giriniz",
					creditcard: "Geçerli bir kredi kartı numarası girin",
					equalTo: "Lütfen aynı değeri tekrar giriniz",
					accept: "Lütfen geçerli bir uzantıya sahip bir değer girin",
					maxlength: jQuery.validator.format(
						"Lütfen en fazla {0} karakter girin"
					),
					minlength: jQuery.validator.format(
						"Lütfen en az {0} karakter girin"
					),
					rangelength: jQuery.validator.format(
						"Lütfen {0} ile {1} karakter uzunluğunda bir değer girin"
					),
					range: jQuery.validator.format(
						"Lütfen {0} ile {1} arasında bir değer girin"
					),
					max: jQuery.validator.format(
						"Lütfen {0} değerinden küçük veya ona eşit bir değer girin"
					),
					min: jQuery.validator.format(
						"Lütfen {0} değerinden büyük veya ona eşit bir değer girin"
					),
				});
			}
			
		},

		forget_password: function ($this) {
			$(".forgot-password").on("click", function () {
				$(".civi-resset-password-wrap").slideToggle();
			});

			$("#civi_forgetpass").on("click", function (e) {
				e.preventDefault();
				var $form = $(this).parents("form");
				$("#ux-login p.error").hide();

				$.ajax({
					type: "post",
					url: ajax_url,
					dataType: "json",
					data: $form.serialize(),
					beforeSend: function () {
						$(".popup-account p.msg").removeClass(
							"text-error text-success text-warning"
						);
						$(".popup-account p.msg").text(theme_vars.forget_password);
						$(".civi-reset-password-wrap p.msg").show();
						$(".popup-account .loading-effect").fadeIn();
					},
					success: function (data) {
						$(".civi-reset-password-wrap p.msg").text(data.message);
						$(".civi-reset-password-wrap p.msg").addClass(data.class);
						$(".popup-account .loading-effect").fadeOut();
					},
				});
			});

			$(".generate-password").on("click", function (e) {
				e.preventDefault();
				var Password = {
					_pattern: /[a-zA-Z0-9_\-\+\.\}\{\?\!\@\#\$\%\&\*\~]/,

					_getRandomByte: function () {
						// http://caniuse.com/#feat=getrandomvalues
						if (window.crypto && window.crypto.getRandomValues) {
							var result = new Uint8Array(1);
							window.crypto.getRandomValues(result);
							return result[0];
						} else if (window.msCrypto && window.msCrypto.getRandomValues) {
							var result = new Uint8Array(1);
							window.msCrypto.getRandomValues(result);
							return result[0];
						} else {
							return Math.floor(Math.random() * 256);
						}
					},

					generate: function (length) {
						return Array.apply(null, { length: length })
							.map(function () {
								var result;
								while (true) {
									result = String.fromCharCode(this._getRandomByte());
									if (this._pattern.test(result)) {
										return result;
									}
								}
							}, this)
							.join("");
					},
				};
				$("#new-password").val(Password.generate(24));
				$("#new-password-error").fadeOut();
			});

			$(".control-password span").on("click", function () {
				var _this = $(this);
				if (_this.hasClass("active")) {
					_this.removeClass("active");
					$("#new-password").attr("type", "text");
				} else {
					_this.addClass("active");
					$("#new-password").attr("type", "password");
				}
			});

			$(".civi-new-password-wrap form").validate({
				rules: {
					new_password: {
						required: true,
						minlength: 8,
					},
				},
				submitHandler: function (form) {
					var new_password = $(form).find('input[name="new_password"]').val();
					var login = $(form).find('input[name="login"]').val();

					$.ajax({
						type: "POST",
						url: ajax_url,
						data: {
							new_password: new_password,
							login: login,
							action: "change_password_ajax",
						},
						beforeSend: function () {
							$(".popup-account p.msg").removeClass(
								"text-error text-success text-warning"
							);
							$(".popup-account p.msg").text(theme_vars.change_password);
							$(".civi-new-password-wrap p.msg").show();
							$(".popup-account .loading-effect").fadeIn();
						},
						success: function (data) {
							var data = $.parseJSON(data);
							$(".civi-new-password-wrap p.msg").text(data.message);
							$(".civi-new-password-wrap p.msg").addClass(data.class);
							$(".popup-account .loading-effect").fadeOut();

							var baseurl = window.location.origin + window.location.pathname;

							window.location.href = baseurl;
						},
					});
				},
			});
		},

		ajax_login_fb: function () {
			$(".facebook-login").on("click", function () {
				FB.login(
					function (response) {
						if (response.status === "connected") {
							FB.api(
								"/me",
								{ fields: "id,name,email,short_name" },
								function (response) {
									$.ajax({
										url: ajax_url,
										type: "POST",
										data: {
											id: response.id,
											email: response.email,
											name: response.name,
											action: "fb_ajax_login_or_register",
										},
										success: function (data) {
											var data = $.parseJSON(data);
											if (data.success == true) {
												location.reload();
											}
										},
									});
								}
							);
						}
					},
					{ scope: "public_profile,email" }
				);
			});
		},

		ajax_login_google: function (googleUser) {
			var google_id = theme_vars.google_id;
			$(".google-login").on("click", function (e) {
				e.preventDefault();

				gapi.load("auth2", function () {
					var scopes = [
						"https://www.googleapis.com/auth/userinfo.email",
						"https://www.googleapis.com/auth/userinfo.profile",
						"https://www.googleapis.com/auth/plus.login",
					];

					var auth2;

					// Use gapi.auth2.authorize instead of gapi.auth2.init.
					// This is because I only need the data from Google once.
					auth2 = gapi.auth2.init({
						client_id: google_id,
						cookie_policy: "single_host_origin",
						fetch_basic_profile: true,
						ux_mode: "popup",
						scope: scopes.join(" "),
						prompt: "select_account",
						plugin_name: "civi",
					});

					auth2
						.signIn()
						.then(() => {
							var profile = auth2.currentUser.get().getBasicProfile();
							$.ajax({
								url: ajax_url,
								type: "POST",
								data: {
									action: "google_ajax_login_or_register",
									id: profile.getId(),
									name: profile.getName(),
									avatar: profile.getImageUrl(),
									email: profile.getEmail(),
								},
								beforeSend: function () {
									$(".popup-account p.msg").removeClass(
										"text-error text-success text-warning"
									);
									$(".popup-account p.msg").text(theme_vars.forget_password);
									$(".popup-account p.msg").show();
									$(".popup-account .loading-effect").fadeIn();
								},
								success: function (data) {
									var data = $.parseJSON(data);
									$(".popup-account p.msg").text(data.messages);
									$(".popup-account p.msg").addClass(data.class);
									if (data.success == true) {
										// location.reload();
										location.href = 'https://www.citymody.com/candidate-profile/';
									}
									$(".popup-account .loading-effect").fadeOut();
								},
							});
						})
						.catch((error) => {
							console.error("Google Sign Up or Login Error: ", error);
						});
				});
			});
		},

		cookie_notices: function () {
			if (
				theme_vars.notice_cookie_enable == 1 &&
				theme_vars.notice_cookie_confirm != "yes" &&
				theme_vars.notice_cookie_messages != "" &&
				sessionStorage.getItem("hide-cookie-form") != "true" &&
				$("body.home").length > 0
			) {
				$.growl({
					location: "br",
					fixed: true,
					duration: 3600000,
					size: "large",
					title: "",
					message: theme_vars.notice_cookie_messages,
				});

				$("#civi-button-cookie-notice-not-ok").on("click", function () {
					$(this).closest("#growls-br").remove();
					sessionStorage.setItem("hide-cookie-form", "true");
				});

				$("#civi-button-cookie-notice-ok").on("click", function () {
					$(this).closest("#growls-br").remove();

					var _data = {
						action: "notice_cookie_confirm",
					};

					_data = $.param(_data);

					$.ajax({
						url: theme_vars.ajax_url,
						type: "POST",
						data: _data,
						dataType: "json",
						success: function (results) {},
						error: function (errorThrown) {
							console.log(errorThrown);
						},
					});
				});
			}
		},
	};

	CIVI.onReady = {
		init: function () {
			CIVI.element.init();
		},
	};

	CIVI.onLoad = {
		init: function () {
			CIVI.element.windowLoad();
		},
	};

	CIVI.onScroll = {
		init: function () {
			// Scroll Window
		},
	};

	CIVI.onResize = {
		init: function () {
			// Resize Window
		},
	};

	$(document).ready(CIVI.onReady.init);
	$(window).scroll(CIVI.onScroll.init);
	$(window).resize(CIVI.onResize.init);
	$(window).load(CIVI.onLoad.init);
})(jQuery);
