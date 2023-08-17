(function ($) {
	"use strict";
	var ajax_url = civi_avatar_vars.ajax_url,
		avatar_title = civi_avatar_vars.avatar_title,
		avatar_type = civi_avatar_vars.avatar_type,
		avatar_file_size = civi_avatar_vars.avatar_file_size,
		avatar_text = civi_avatar_vars.avatar_text,
		avatar_url = civi_avatar_vars.avatar_url,
		avatar_upload_nonce = civi_avatar_vars.avatar_upload_nonce;

	jQuery(document).ready(function () {
		var civi_avatar = function () {
			var uploader_avatar = new plupload.Uploader({
				browse_button: "civi_select_avatar",
				file_data_name: "civi_avatar_upload_file",
				drop_element: "civi_avatar_view",
				container: "civi_avatar_container",
				url: avatar_url,
				filters: {
					mime_types: [
						{
							title: avatar_title,
							extensions: avatar_type,
						},
					],
					max_file_size: avatar_file_size,
					prevent_duplicates: true,
				},
			});
			uploader_avatar.init();

			uploader_avatar.bind("UploadProgress", function (up, file) {
				$("#civi_add_avatar .la-upload").hide();
				document.getElementById("civi_select_avatar").innerHTML =
					'<span><i class="fal fa-spinner fa-spin large"></i></span>';
			});

			uploader_avatar.bind("FilesAdded", function (up, files) {
				up.refresh();
				uploader_avatar.start();
			});
			uploader_avatar.bind("Error", function (up, err) {
				document.getElementById("civi_avatar_errors").innerHTML +=
					"Error #" + err.code + ": " + err.message + "<br/>";
			});

			var $image_id = $("#civi_avatar_view").data("image-id");
			var $image_url = $("#civi_avatar_view").data("image-url");
			if ($image_id && $image_url) {
				var $html =
					'<figure class="media-thumb media-thumb-wrap">' +
					'<img src="' +
					$image_url +
					'">' +
					'<div class="media-item-actions">' +
					'<a class="icon icon-avatar-delete" data-attachment-id="' +
					$image_id +
					'" href="#" ><i class="far fa-trash-alt large"></i></a>' +
					'<span style="display: none;" class="icon icon-loader"><i class="fal fa-spinner fa-spin large"></i></span>' +
					"</div>" +
					"</figure>";
				$("#civi_avatar_view").html($html);
				$("#civi_add_avatar").hide();
			}
			uploader_avatar.bind("FileUploaded", function (up, file, ajax_response) {
				document.getElementById("civi_drop_avatar").style.display = "none";
				var response = $.parseJSON(ajax_response.response);
				if (response.success) {
					$("input.avatar_url").val(response.full_image);
					$("input.avatar_id").val(response.attachment_id);
					var $html =
						'<figure class="media-thumb media-thumb-wrap">' +
						'<img src="' +
						response.full_image +
						'">' +
						'<div class="media-item-actions">' +
						'<a class="icon icon-avatar-delete" data-attachment-id="' +
						response.attachment_id +
						'" href="#" ><i class="far fa-trash-alt large"></i></a>' +
						'<span style="display: none;" class="icon icon-loader"><i class="fal fa-spinner fa-spin large"></i></span>' +
						"</div>" +
						"</figure>";
					$("#civi_avatar_view").html($html);
					civi_avatar_delete();
					$("#civi_add_avatar .la-upload").hide();
					$("#avatar_url-error").hide();
					if ($(".form-dashboard").hasClass("candidate-profile-form")) {
						$("#candidate-profile-form").find(".point-mark").change();
					}
				}
			});
		};
		civi_avatar();

		var civi_avatar_delete = function ($type) {
			$("body").on("click", ".icon-avatar-delete", function (e) {
				$("#no-selfie").addClass( "avatar_bg-show" );
				e.preventDefault();
				var $this = $(this),
					icon_delete = $this,
					avatar = $this.closest("#civi_avatar_view").find(".media-thumb-wrap"),
					attachment_id = $this.data("attachment-id"),
					$drop = $("#civi_drop_avatar");

				icon_delete.html('<i class="fal fa-spinner fa-spin large"></i>');

				$.ajax({
					type: "post",
					url: ajax_url,
					dataType: "json",
					data: {
						action: "civi_avatar_remove_ajax",
						attachment_id: attachment_id,
						type: $type,
						removeNonce: avatar_upload_nonce,
					},
					success: function (response) {
						if (response.success) {
							avatar.remove();
							avatar.hide();

							$("#avatar_url-error").show();
							$("#civi_add_avatar").show();
						}
						icon_delete.html('<i class="fal fa-spinner fa-spin large"></i>');
						$drop.css("display", "block");
						$("#civi_add_avatar .la-upload").show();
						$("#civi_select_avatar").html(avatar_text);
						$("input.avatar_url").val("");
						$("input.avatar_id").val("");
						if ($(".form-dashboard").hasClass("candidate-profile-form")) {
							$("#candidate-profile-form").find(".point-mark").change();
						}
					},
					error: function () {
						icon_delete.html('<i class="far fa-trash-alt large"></i>');
					},
				});
			});
		};
		civi_avatar_delete();
	});
})(jQuery);
