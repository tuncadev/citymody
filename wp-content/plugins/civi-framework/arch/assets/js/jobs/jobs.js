var JOBS = JOBS || {};
(function ($) {
	"use strict";

	JOBS = {
		init: function () {
			this.toggle_insights();
            this.toggle_insights_sidebar();
			this.toggle_overview();
			this.toggle_review();
			this.apply_popup();
			this.civi_alert_message();
            this.civi_crop_image();
            this.popup_job_alerts();
		},

		toggle_insights: function () {
			var $show = $(".show-more-insights");
			var $hide = $(".hide-all-insights");
			var $warpper = $(".jobs-insights-details");
			$warpper.addClass("on");
			$show.click(function (e) {
				e.preventDefault();
				$warpper.removeClass("on");
				$hide.show();
				$(this).hide();
			});
			$hide.click(function (e) {
				e.preventDefault();
				$warpper.addClass("on");
				$show.show();
				$(this).hide();
			});
		},

        toggle_insights_sidebar: function () {
            var $show = $(".show-more-insights-sidebar");
            var $hide = $(".hide-all-insights-sidebar");
            var $warpper = $(".jobs-insights-sidebar");
            $warpper.addClass("on");
            $show.click(function (e) {
                e.preventDefault();
                $warpper.removeClass("on");
                $hide.show();
                $(this).hide();
            });
            $hide.click(function (e) {
                e.preventDefault();
                $warpper.addClass("on");
                $show.show();
                $(this).hide();
            });
        },

		toggle_overview: function () {
			var $show = $(".show-more-description");
			var $hide = $(".hide-all-description");
			var $warpper = $(".civi-description-details");
            var $height_des = $(".civi-description").height();
			$warpper.addClass("on");
			$show.click(function (e) {
				e.preventDefault();
				$warpper.removeClass("on");
				$hide.show();
				$(this).hide();
			});
			$hide.click(function (e) {
				e.preventDefault();
				$warpper.addClass("on");
				$show.show();
				$(this).hide();
			});
            if($height_des < 330){
                $warpper.find(".toggle-description").hide();
            }
		},

        toggle_review: function () {
            var $show = $(".show-more-review");
            var $hide = $(".hide-all-review");
            var $warpper = $(".civi-review-details");
            var $height_des = $(".civi-review").height();
            $warpper.addClass("on");
            $show.click(function (e) {
                e.preventDefault();
                $warpper.removeClass("on");
                $hide.show();
                $(this).hide();
            });
            $hide.click(function (e) {
                e.preventDefault();
                $warpper.addClass("on");
                $show.show();
                $(this).hide();
            });
            if($height_des < 120){
                $warpper.find(".toggle-review").hide();
            }
        },

		apply_popup: function () {
			var $form_popup = $('.form-popup-apply');
			var $btn_close = $form_popup.find(".btn-close");
			var $bg_overlay = $form_popup.find(".bg-overlay");
			var $btn_cancel = $form_popup.find(".button-cancel");

            $form_popup.each(function () {
                var $form_popup_id = $('#' + ($(this).attr('id')));
                var $btn_popup = $('.civi-button-apply.' + ($(this).attr('id')));
				function open_popup(e) {
					e.preventDefault();
                    $form_popup_id.css({ opacity: "1", visibility: "unset" });
				}

				function close_popup(e) {
					e.preventDefault();
                    $form_popup_id.css({ opacity: "0", visibility: "hidden" });
				}
                $btn_popup.on( 'click', open_popup);
                $bg_overlay.on( 'click', close_popup);
                $btn_close.on( 'click', close_popup);
                $btn_cancel.on( 'click', close_popup);
            });
		},

		civi_alert_message: function () {
			$("body").on("click", ".btn-add-to-message", function (e) {
				e.preventDefault();
				var $text = $(this).data("text");
				var $html = '<div class="civi_alert_message fadeInRight">' + $text + "</div>";
				$("body").find(".civi_alert_message").remove();
				$("body").append($html).fadeIn(500);
				setTimeout(function () {
					$("body").find(".civi_alert_message").removeClass("fadeInRight").addClass("fadeOutRight show");
				}, 2000);
			});
		},

        civi_crop_image: function () {
			var $crop_image = $('.civi_crop_image img'),
				$height_image = $crop_image.attr('height'),
				$width_image = $crop_image.attr('width');
            $crop_image.css({'height' : $height_image, 'width' : $width_image,'object-fit' : 'cover'});
        },

		popup_job_alerts: function () {
			$( '.alert-form' ).each( function() {
				var _this = $( this );
				if (sessionStorage.getItem("hide-alert-form") == 'true' ) {
					_this.fadeOut(0);
				} else {
					_this.fadeIn(0);
					var close = _this.find( '.close' );
					close.on( 'click', function(e) {
						e.preventDefault();
						sessionStorage.setItem("hide-alert-form", 'true');
						_this.fadeOut(0);
					});
				}
			});
        }
	};
	$(document).ready(function () {
		JOBS.init();
	});
})(jQuery);
