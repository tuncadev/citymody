(function ($) {
	"use strict";

	var submit_form = $("#submit_company_form"),
		company_title_error = submit_form.data("titleerror"),
		company_des_error = submit_form.data("deserror"),
		company_cat_error = submit_form.data("caterror"),
		company_size_error = submit_form.data("sizeerror"),
		company_email_error = submit_form.data("emailerror");

	var ajax_url = civi_submit_vars.ajax_url,
		company_dashboard = civi_submit_vars.company_dashboard,
        custom_field_company = civi_submit_vars.custom_field_company;

	$(document).ready(function () {
		$.validator.setDefaults({ ignore: ":hidden:not(select)" });

		submit_form.validate({
			ignore: [],
			rules: {
				company_title: {
					required: true,
				},
				company_categories: {
					required: true,
				},
				company_size: {
					required: true,
				},
				company_email: {
					required: true,
				},
				company_des: {
					required: true,
				},
			},
			messages: {
				company_title: company_title_error,
				company_des: company_des_error,
				company_categories: company_cat_error,
				company_size: company_size_error,
				company_email: company_email_error,
			},
			submitHandler: function (form) {
				ajax_load();
			},
			errorPlacement: function (error, element) {
				error.insertAfter(element);
			},
			invalidHandler: function () {
				if ($(".error:visible").length > 0) {
					$("html, body").animate(
						{
							scrollTop: $(".error:visible").offset().top - 100,
						},
						500
					);
				}
			},
		});

		function ajax_load() {
			var company_form = submit_form.find('input[name="company_form"]').val(),
				company_action = submit_form.find('input[name="company_action"]').val(),
				company_id = submit_form.find('input[name="company_id"]').val(),
				company_title = submit_form.find('input[name="company_title"]').val(),
				company_categories = submit_form
					.find('select[name="company_categories"]')
					.val(),
				company_url = submit_form.find('input[name="company_url"]').val(),
				company_des = tinymce.get("company_des").getContent(),
				company_website = submit_form
					.find('input[name="company_website"]')
					.val(),
				company_phone = submit_form.find('input[name="company_phone"]').val(),
				company_email = submit_form.find('input[name="company_email"]').val(),
				company_founded = submit_form
					.find('select[name="company_founded"]')
					.val(),
				company_size = submit_form.find('select[name="company_size"]').val(),
				company_twitter = submit_form
					.find('input[name="company_twitter"]')
					.val(),
				company_linkedin = submit_form
					.find('input[name="company_linkedin"]')
					.val(),
				company_facebook = submit_form
					.find('input[name="company_facebook"]')
					.val(),
				company_instagram = submit_form
					.find('input[name="company_instagram"]')
					.val(),
				company_social_name = submit_form
					.find('input[name="company_social_name[]"]')
					.map(function () {
						return $(this).val();
					})
					.get(),
				company_social_url = submit_form
					.find('input[name="company_social_url[]"]')
					.map(function () {
						return $(this).val();
					})
					.get(),
				company_location = submit_form
					.find('select[name="company_location"]')
					.val(),
				company_map_address = submit_form
					.find('input[name="civi_map_address"]')
					.val(),
				company_map_location = submit_form
					.find('input[name="civi_map_location"]')
					.val(),
                company_latitude = submit_form.find('input[name="civi_latitude"]').val(),
                company_longtitude = submit_form
                    .find('input[name="civi_longtitude"]')
                    .val(),

				company_avatar_url = submit_form
					.find('input[name="company_avatar_url"]')
					.val(),
				company_avatar_id = submit_form
					.find('input[name="company_avatar_id"]')
					.val(),
				company_thumbnail_url = submit_form
					.find('input[name="company_thumbnail_url"]')
					.val(),
				company_thumbnail_id = submit_form
					.find('input[name="company_thumbnail_id"]')
					.val(),
				civi_gallery_ids = submit_form
					.find('input[name="civi_gallery_ids[]"]')
					.map(function () {
						return $(this).val();
					})
					.get(),
				company_video_url = submit_form
					.find('input[name="company_video_url"]')
					.val();

            var additional = {};
            $("#company-submit-additional").each(function () {
                $.each(custom_field_company, function (index, value) {
                    var val = $(".form-control[name=" + value.id + "]").val();
                    if (value.type == "radio") {
                        val = $("input[name=" + value.id + "]:checked").val();
                    }
                    if (value.type == "checkbox_list") {
                        var arr_checkbox = [];
                        $('input[name="' + value.id + '[]"]:checked').each(function () {
                            arr_checkbox.push($(this).val());
                        });
                        val = arr_checkbox;
                    }
                    additional[value.id] = val;
                });
            });

			$.ajax({
				dataType: "json",
				url: ajax_url,
				data: {
					action: "company_submit_ajax",
					company_form: company_form,
					company_action: company_action,
					company_id: company_id,
					company_title: company_title,
					company_categories: company_categories,
					company_url: company_url,
					company_des: company_des,
					company_website: company_website,
					company_founded: company_founded,
					company_phone: company_phone,
					company_email: company_email,
					company_size: company_size,

					company_twitter: company_twitter,
					company_linkedin: company_linkedin,
					company_facebook: company_facebook,
					company_instagram: company_instagram,
					company_social_name: company_social_name,
					company_social_url: company_social_url,

					company_location: company_location,
					company_map_address: company_map_address,
					company_map_location: company_map_location,
                    company_latitude: company_latitude,
                    company_longtitude: company_longtitude,

					company_avatar_url: company_avatar_url,
					company_avatar_id: company_avatar_id,
					company_thumbnail_url: company_thumbnail_url,
					company_thumbnail_id: company_thumbnail_id,
					civi_gallery_ids: civi_gallery_ids,
					company_video_url: company_video_url,

                    custom_field_company: additional,
				},
				beforeSend: function () {
					$(".btn-submit-company .btn-loading").fadeIn();
				},
				success: function (data) {
					$(".btn-submit-company .btn-loading").fadeOut();
					if (data.success === true) {
						window.location.href = company_dashboard;
					}
				},
			});
		}
	});
})(jQuery);
