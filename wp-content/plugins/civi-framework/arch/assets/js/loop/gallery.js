(function ($) {
	"use strict";
	var ajax_url = civi_gallery_vars.ajax_url,
		gallery_title = civi_gallery_vars.gallery_title,
		gallery_type = civi_gallery_vars.gallery_type,
		gallery_max_images = civi_gallery_vars.gallery_max_images,
		gallery_file_size = civi_gallery_vars.gallery_file_size,
		gallery_url = civi_gallery_vars.gallery_url,
		gallery_upload_nonce = civi_gallery_vars.gallery_upload_nonce;

	jQuery(document).ready(function () {
		var civi_gallery_images = function () {
			$("#civi_gallery_thumbs").sortable();
			/* initialize uploader */
			var uploader = new plupload.Uploader({
				browse_button: "civi_select_gallery",
				file_data_name: "civi_gallery_upload_file",
				container: "civi_gallery_container",
				drop_element: "civi_gallery_container",
				multi_selection: true,
				url: gallery_url,
				filters: {
					mime_types: [
						{
							title: gallery_title,
							extensions: gallery_type,
						},
					],
					max_file_size: gallery_file_size,
					prevent_duplicates: true,
				},
			});
			uploader.init();

			uploader.bind("FilesAdded", function (up, files) {
				var civiThumb = "";
				var maxfiles = gallery_max_images;
				if (up.files.length > maxfiles) {
					up.splice(maxfiles);
					alert("no more than " + maxfiles + " file(s)");
					return;
				}
				plupload.each(files, function (file) {
					civiThumb += '<div id="holder-' + file.id + '"></div>';
				});
				document.getElementById("civi_gallery_thumbs").innerHTML += civiThumb;
				up.refresh();
				uploader.start();
			});

			uploader.bind("UploadProgress", function (up, file) {
				document.getElementById("holder-" + file.id).innerHTML =
					'<span><i class="fal fa-spinner fa-spin large"></i></span>';
			});

			uploader.bind("Error", function (up, err) {
				document.getElementById("civi_gallery_errors").innerHTML +=
					"Error: " + err.message + "<br/>";
			});

			uploader.bind("FileUploaded", function (up, file, ajax_response) {
				var response = $.parseJSON(ajax_response.response);
				if (response.success) {
					var $html =
						'<figure class="media-thumb media-thumb-wrap">' +
						'<img src="' +
						response.url +
						'"/>' +
						'<div class="media-item-actions">' +
						'<a class="icon icon-gallery-delete" data-attachment-id="' +
						response.attachment_id +
						'" href="#" ><i class="far fa-trash-alt large"></i></a>' +
						'<input type="hidden" class="civi_gallery_ids" name="civi_gallery_ids[]" value="' +
						response.attachment_id +
						'"/>' +
						'<span style="display: none;" class="icon icon-loader"><i class="fal fa-spinner fa-spin large"></i></span>' +
						"</div>" +
						"</figure>";

					document.getElementById("holder-" + file.id).innerHTML = $html;
					civi_gallery_remove("gallery");
					if ($(".form-dashboard").hasClass("candidate-profile-form")) {
						$("#candidate-profile-form").find(".point-mark").change();
					}
				}
			});
		};
		civi_gallery_images();

		var civi_gallery_remove = function ($type) {
			$("body").on("click", ".icon-gallery-delete", function (e) {
				e.preventDefault();
				var $this = $(this),
					icon_delete = $this,
					gallery = $this.closest(".media-thumb-wrap"),
					attachment_id = $this.data("attachment-id");

				icon_delete.html('<i class="fal fa-spinner fa-spin large"></i>');

				$.ajax({
					type: "post",
					url: ajax_url,
					dataType: "json",
					data: {
						action: "civi_gallery_remove_ajax",
						attachment_id: attachment_id,
						type: $type,
						removeNonce: gallery_upload_nonce,
					},
					success: function (response) {
						if (response.success) {
							gallery.remove();
							gallery.hide();

							$("#featured_image_url-error").show();
						}
						icon_delete.html('<i class="fal fa-spinner fa-spin large"></i>');
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
		civi_gallery_remove("gallery");
	});
})(jQuery);
