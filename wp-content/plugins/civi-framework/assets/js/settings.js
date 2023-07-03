(function ($) {
	"use strict";

	var ajax_url = civi_settings_vars.ajax_url,
		civi_site_url = civi_settings_vars.site_url;

	$(document).ready(function () {
		$(".form-settings").validate({
			ignore: ":hidden", // any children of hidden desc are ignored
			errorElement: "span", // wrap error elements in span not label
			rules: {
				user_firstname: {
					required: true,
				},
				user_lastname: {
					required: true,
				},
				user_email: {
					required: true,
				},
				user_mobile_number: {
					required: true,
				},
			},
			messages: {
				user_firstname: "",
				user_lastname: "",
				user_email: "",
				user_mobile_number: "",
			},
		});

		$("#civi_update_profile").on("click", function () {
			var $this = $(this);
			var $form = $this.parents("form");
			var $alert_title = $this.text();
			if ($form.valid()) {
				$.ajax({
					type: "POST",
					url: ajax_url,
					dataType: "json",
					data: {
						action: "civi_update_profile_ajax",
						user_firstname: $("#user_firstname").val(),
						user_lastname: $("#user_lastname").val(),
						user_des: $("#user_des").val(),
						user_email: $("#user_email").val(),
						author_mobile_number: $("#author_mobile_number").val(),
						user_image_url: $("#author_avatar_image_url").val(),
						user_image_id: $("#author_avatar_image_id").val(),
						civi_security_update_profile: $(
							"#civi_security_update_profile"
						).val(),
					},
					beforeSend: function () {
						$this.find(".btn-loading").fadeIn();
					},
					success: function (response) {
						$this.find(".btn-loading").fadeOut();
						if (response.success) {
							location.reload();
						}
					},
					error: function () {
						$this.find(".btn-loading").fadeOut();
					},
				});
			}
		});

        $(".block-search.search-input").on("click", ".icon-clear", function (e) {
            e.preventDefault();
            $(this).closest(".search-input").find(".input-search").val("");
            $(this).closest(".search-input").removeClass("has-clear");
        });

		$("#civi_change_pass").on("click", function (e) {
			e.preventDefault();
			var securitypassword, oldpass, newpass, confirmpass;

			var $this = $(this);
			var $form = $this.parents("form");
			var $alert_title = $this.text();

			oldpass = $("#oldpass").val();
			newpass = $("#newpass").val();
			confirmpass = $("#confirmpass").val();
			securitypassword = $("#civi_security_change_password").val();

			$.ajax({
				type: "POST",
				dataType: "json",
				url: ajax_url,
				data: {
					action: "civi_change_password_ajax",
					oldpass: oldpass,
					newpass: newpass,
					confirmpass: confirmpass,
					civi_security_change_password: securitypassword,
				},
				beforeSend: function () {
					$this.find(".btn-loading").fadeIn();
				},
				success: function (response) {
					if (response.success) {
						window.location.href = civi_site_url;
					}
					$form.find(".message").html(response.message);
					$this.find(".btn-loading").fadeOut();
				},
				error: function () {
					$this.find(".btn-loading").fadeOut();
				},
			});
		});
	});
})(jQuery);
