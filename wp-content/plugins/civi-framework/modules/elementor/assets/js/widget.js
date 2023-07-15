var ISF = ISF || {};

(function ($) {
	"use strict";

	var ajax_url = civi_template_vars.ajax_url;

	var Widget_Civi_Nav_Menu = function () {
		$(
			".elementor-widget-civi-nav-menu ul.elementor-nav-menu>li.menu-item-has-children>a"
		).append('<span class="sub-arrow"><i class="fa"></i></span>');
	};

	/******************* Refresh after live preview *********************/

	var Widget_Reload_Carousel = function ($scope, $) {
		var carousel_elem = $scope.find(".elementor-carousel").eq(0);
		if (carousel_elem.length > 0) {
			var settings = carousel_elem.data("slider_options");
			if (settings["isslick"] == "false") {
				alert(settings["isslick"]);
				carousel_elem.unslick();
			} else {
				carousel_elem.slick(settings);
			}
		}
	};

	var Widget_Job_Alerts = function ($scope, $) {
		var form = $scope.find(".job-alerts-form");
		form.on( 'submit', function(e) {
			e.preventDefault();
			var name = $( this ).find('input[name="name"]').val();
			var email = $( this ).find('input[name="email"]').val();
			var skills = $( this ).find('select[name="skills"]').val();
			var location = $( this ).find('select[name="location"]').val();
			var category = $( this ).find('select[name="category"]').val();
			var experience = $( this ).find('select[name="experience"]').val();
			var types = $( this ).find('select[name="types"]').val();
			var frequency = $( this ).find('select[name="frequency"]').val();

			$.ajax({
				type: "post",
				url: ajax_url,
				dataType: "json",
				data: {
					name: name,
					email: email,
					skills: skills,
					location: location,
					category: category,
					experience: experience,
					types: types,
					frequency: frequency,
					action: "civi_job_alerts_action",
				},
				beforeSend: function () {
					form.find(".notice").text('').removeClass('warning').removeClass('success');
					form.find(".btn-loading").fadeIn();
				},
				success: function (data) {
					form.find(".btn-loading").fadeOut();
					form.find(".notice").removeClass('warning').removeClass('success').addClass(data.class);
					form.find(".notice").text('').text(data.message);
				},
				error: function () {
					form.find(".btn-loading").fadeOut();
				},
			});
		});
	};

    $(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/civi-companies.default', Widget_Reload_Carousel);
        elementorFrontend.hooks.addAction('frontend/element_ready/civi-jobs.default', Widget_Reload_Carousel);
        elementorFrontend.hooks.addAction('frontend/element_ready/civi-jobs-category.default', Widget_Reload_Carousel);
        elementorFrontend.hooks.addAction('frontend/element_ready/civi-companies-category.default', Widget_Reload_Carousel);
        elementorFrontend.hooks.addAction('frontend/element_ready/civi-jobs-location.default', Widget_Reload_Carousel);
        elementorFrontend.hooks.addAction('frontend/element_ready/civi-nav-menu.default', Widget_Civi_Nav_Menu);
        elementorFrontend.hooks.addAction('frontend/element_ready/civi-job-alerts.default', Widget_Job_Alerts);
    });
})(jQuery);
