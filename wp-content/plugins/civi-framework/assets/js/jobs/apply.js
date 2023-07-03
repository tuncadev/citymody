(function ($) {
  "use strict";
  $(document).ready(function () {
    var ajax_url = civi_template_vars.ajax_url,
      apply_saved = civi_template_vars.apply_saved,
      $form_popup = $(".form-popup-apply");

    $form_popup.each(function () {
      var $btn_submit = $("#" + $(".btn-submit-apply-jobs").attr("id"));
      var $btn_popup = $(".civi-button-apply." + $(this).attr("id"));
      var apply_form = $("#" + $(this).attr("id"));
      $btn_submit.on("click", function (e) {
        e.preventDefault();
        var $this = $(this),
          emaill = apply_form.find('input[name="apply_emaill"]').val(),
          message = apply_form.find('textarea[name="apply_message"]').val(),
          phone = apply_form.find('input[name="apply_phone"]').val(),
          candidate_id = $btn_popup.data("candidate_id"),
          jobs_id = $btn_popup.data("jobs_id"),
          type_apply = apply_form.find('input[name="type_apply"]').val();

        $.ajax({
          type: "POST",
          url: ajax_url,
          dataType: "json",
          data: {
            action: "jobs_add_to_apply",
            jobs_id: jobs_id,
            candidate_id: candidate_id,
            emaill: emaill,
            phone: phone,
            message: message,
            type_apply: type_apply,
          },
          beforeSend: function () {
            $this.find(".btn-loading").fadeIn();
          },
          success: function (data) {
            if (data.success == true) {
              apply_form.find(".message_error").addClass("true");
              apply_form.find(".message_error").text(data.message);
              $(".civi-button-apply[data-jobs_id =" + jobs_id + "]").html(
                apply_saved
              );
              $("#apply_success").css("display", "block");
				runCountDown();
				
            } else {
              $(".message_error").text(data.message);
            }
            $this.find(".btn-loading").fadeOut();
          },
        });
      });
    });
  });
})(jQuery);
