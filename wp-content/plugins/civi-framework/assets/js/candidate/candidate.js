var CANDIDATE = CANDIDATE || {};
(function ($) {
	"use strict";

	CANDIDATE = {
		init: function () {
			this.tab_candidate();
			this.social_candidate();
			this.login_notice();
		},

		tab_candidate: function () {
			function tabcandidate(obj) {
				$(".jobs-candidate-sidebar ul li").removeClass("active");
				$(obj).addClass("active");
				var id = $(obj).find("a").attr("href");
				$(".tab-info-candidate").hide();
				$(id).show();
			}
			$(".tab-candidate li").click(function () {
				tabcandidate(this);
				return false;
			});
			tabcandidate($(".jobs-candidate-sidebar ul li:first-child"));
		},

		social_candidate: function () {
			$("body").on(
				"click",
				"#candidate-submit-social .soical-remove-inner",
				function () {
					var wrap = $(this).closest(".clone-wrap");
					$(wrap).find(".field-wrap").slideToggle();
				}
			);
		},

		login_notice: function () {
			var notice = $(".btn-login.notice-employer").data("notice");
			if ($(".btn-login").hasClass("notice-employer")) {
				$("#popup-form .notice").html(
					'<i class="fal fa-exclamation-circle"></i>' + notice
				);
			}
		},
	};
	$(document).ready(function () {
		CANDIDATE.init();
	});
})(jQuery);
