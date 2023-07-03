(function ($) {
	"use strict";
	var ajax_url = civi_upload_cv_vars.ajax_url,
		title = civi_upload_cv_vars.title,
		cv_file = civi_upload_cv_vars.cv_file,
		cv_max_file_size = civi_upload_cv_vars.cv_max_file_size,
		text = civi_upload_cv_vars.text,
		url = civi_upload_cv_vars.url,
		upload_nonce = civi_upload_cv_vars.upload_nonce;

	$(document).ready(function () {
		var featured_image = function () {
			var uploader_featured_image = new plupload.Uploader({
				browse_button: "civi_select_cv",
				file_data_name: "civi_thumbnail_upload_file",
				drop_element: "civi_select_cv",
				container: "civi_cv_plupload_container",
				url: url,
				filters: {
					mime_types: [
						{
							title: title,
							extensions: cv_file,
						},
					],
					max_file_size: cv_max_file_size,
					prevent_duplicates: true,
				},
			});
			uploader_featured_image.init();

			uploader_featured_image.bind("UploadProgress", function (up, file) {
				$("#civi_select_cv i").removeClass("far fa-arrow-from-bottom large");
				$("#civi_select_cv i").addClass("fal fa-spinner fa-spin large");
			});

			uploader_featured_image.bind("FilesAdded", function (up, files) {
				var maxfiles = 1;
				up.refresh();
				uploader_featured_image.start();
			});
			uploader_featured_image.bind("Error", function (up, err) {
				document.getElementById("cv_errors_log").innerHTML +=
					"Error #" + err.code + ": " + err.message + "<br/>";
			});

			uploader_featured_image.bind(
				"FileUploaded",
				function (up, file, ajax_response) {
					var response = $.parseJSON(ajax_response.response);
					if (response.success) {
						$(".cv_url").val(response.url);
						$("#civi_drop_cv").attr(
							"data-attachment-id",
							response.attachment_id
						);
						$("#civi_drop_cv").append(
							'<a class="icon cv-icon-delete" data-jobs-id="0"  data-attachment-id="' +
								response.attachment_id +
								'" href="#" ><i class="far fa-trash-alt large"></i></a>'
						);
						var $html =
							'<i class="far fa-arrow-from-bottom large"></i><span>' +
							response.title +
							"</span>";
						$("#civi_select_cv i").addClass("far fa-arrow-from-bottom large");
						$("#civi_select_cv").html($html);
						$("#cv_url-error").hide();
						$("#candidate-profile-form").find(".point-mark").change();
					}
				}
			);
		};
		featured_image();
		var civi_jobs_thumb_event = function ($type) {
			$("body").on("click", ".cv-icon-delete", function (e) {
				e.preventDefault();
				var $this = $(this),
					icon_delete = $this,
					thumbnail = $this.closest(".media-thumb-wrap"),
					jobs_id = $this.data("jobs-id"),
					attachment_id = $this.data("attachment-id");
				icon_delete.html('<i class="fal fa-spinner fa-spin large"></i>');

				$.ajax({
					type: "post",
					url: ajax_url,
					dataType: "json",
					data: {
						action: "civi_thumbnail_remove_ajax",
						jobs_id: jobs_id,
						attachment_id: attachment_id,
						type: $type,
						removeNonce: upload_nonce,
					},
					beforeSend: function () {
						icon_delete.html('<i class="fal fa-spinner fa-spin large"></i>');
					},
					success: function (response) {
						if (response.success) {
							$("#cv_url-error").show();
							$(".civi_cv_file").show();
						}
						$("#civi_select_cv").html(text);
						$("#civi_drop_cv").attr("data-attachment-id", "");
						$("#candidate-profile-form").find(".point-mark").change();
						icon_delete.remove();
					},
					error: function () {
						icon_delete.html('<i class="far fa-trash-alt large"></i>');
					},
				});
			});
		};
		civi_jobs_thumb_event("thumb");
	});
})(jQuery);
