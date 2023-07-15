jQuery(document).ready(function ($) {
	var ajax_url = civi_candidate_review_vars.ajax_url;
	var candidate_review = $(".candidate-review-details");

	candidate_review.find("input:file").change(function () {
		$(".fileList span").remove();
		for (var i = 0; i < this.files.length; i++) {
			var fileName = this.files[i].name;
			$(".fileList").append("<span>" + fileName + "</span>");
		}
	});

	candidate_review.find(".entry-nav .reply").on("click", function (e) {
		e.preventDefault();
		$(".author-review").removeClass("active");
		$(".author-review .form-reply").html("");
		var $this = $(this);
		var form_reply = $(".duplicate-form-reply").html();
		var comment_id = $this
			.parents(".author-review")
			.find(".form-reply")
			.data("id");
		$(".add-new-review").hide();
		$this.parents(".author-review").addClass("active");
		$this.parents(".author-review").find(".form-reply").html(form_reply);
		$this
			.parents(".author-review")
			.find('.form-reply input[name="comment_id"]')
			.val(comment_id);
	});

	$("body").on(
		"click",
		".form-reply .civi-submit-candidate-reply",
		function (e) {
			e.preventDefault();
			var $this = $(this);
			var $form = $this.parents("form");
			var message = $form.find("textarea").val();
			if (message == "") {
				$form.find("#message-error").fadeIn();
			} else {
				$form.find("#message-error").fadeOut();

				$.ajax({
					type: "POST",
					url: ajax_url,
					data: $form.serialize(),
					dataType: "json",
					beforeSend: function () {
						$this.attr("disabled", true);
						$this.children("i").remove();
						$this.append(
							'<i class="fa-left fal fa-spinner fa-spin large"></i>'
						);
					},
					success: function () {
						window.location.reload();
					},
					complete: function () {
						$this.children("i").removeClass("fal fa-spinner fa-spin large");
						$this.children("i").addClass("fa fa-check");
					},
				});
			}
		}
	);

	$("body").on("click", ".cancel-reply", function (e) {
		e.preventDefault();
		candidate_review.find(".author-review").removeClass("active");
		candidate_review.find(".author-review .form-reply").html("");
		candidate_review.find(".add-new-review").show();
	});

	$.validator.setDefaults({
		debug: true,
		success: "valid",
	});
	if (userLocale == "en") {	var nrequired = "This field is required" } else { var nrequired = "This field is required" }
	$(".reviewForm").validate({
		rules: {
			message: {
				required: true,
			},
		},
		messages: {
			message: {
				required: nrequired,
			},
		},
		errorPlacement: function (error, element) {
			if (element.is(":radio")) {
				error.appendTo(element.parents("fieldset"));
			} else {
				// This is the default behavior
				error.insertAfter(element);
			}
		},
		submitHandler: function (form) {
			var $this = $(".reviewForm").find(".civi-submit-candidate-rating");
			var $form = $(".reviewForm");

			var formdata = false;
			if (window.FormData) {
				formdata = new FormData($form[0]);
			}

			$.ajax({
				type: "POST",
				url: ajax_url,
				data: formdata ? formdata : $form.serialize(),
				enctype: "multipart/form-data",
				dataType: "json",
				processData: false,
				contentType: false,
				beforeSend: function () {
					$this.children("i").remove();
					$this.append('<i class="fa-left fal fa-spinner fa-spin large"></i>');
				},
				success: function (data) {
					window.location.reload();
				},
				complete: function () {
					$this.children("i").removeClass("fal fa-spinner fa-spin large");
					$this.children("i").addClass("fa fa-check");
				},
			});
		},
	});
});
