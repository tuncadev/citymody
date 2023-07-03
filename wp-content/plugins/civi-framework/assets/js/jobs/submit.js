(function ($) {
  "use strict";

  var submit_form = $("#submit_jobs_form"),
    jobs_title_error = submit_form.data("titleerror"),
    jobs_des_error = submit_form.data("deserror"),
    jobs_cat_error = submit_form.data("caterror"),
    jobs_type_error = submit_form.data("typeerror"),
    jobs_skills_error = submit_form.data("skillserror");

  var ajax_url = civi_submit_vars.ajax_url,
    jobs_dashboard = civi_submit_vars.jobs_dashboard,
    custom_field_jobs = civi_submit_vars.custom_field_jobs;

  $(document).ready(function () {
    $.validator.setDefaults({ ignore: ":hidden:not(select)" });

    submit_form.validate({
      ignore: [],
      rules: {
        jobs_title: {
          required: true,
        },
        jobs_categories: {
          required: true,
        },
        jobs_skills: {
          required: true,
        },
        jobs_des: {
          required: true,
        },
      },
      messages: {
        jobs_title: jobs_title_error,
        jobs_des: jobs_des_error,
        jobs_categories: jobs_cat_error,
        jobs_type: jobs_type_error,
        jobs_skills: jobs_skills_error,
      },
      submitHandler: function (form) {
        var submitButtonName = $(this.submitButton).attr("name");
        ajax_load(submitButtonName);
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

    function ajax_load(submit_button) {
      var jobs_form = submit_form.find('input[name="jobs_form"]').val(),
        jobs_action = submit_form.find('input[name="jobs_action"]').val(),
        jobs_id = submit_form.find('input[name="jobs_id"]').val(),
        jobs_title = submit_form.find('input[name="jobs_title"]').val(),
        jobs_categories = submit_form
          .find('select[name="jobs_categories"]')
          .val(),
        jobs_type = submit_form.find('select[name="jobs_type"]').val(),
        jobs_skills = submit_form.find('select[name="jobs_skills"]').val(),
        jobs_des = tinymce.get("jobs_des").getContent(),
        jobs_career = submit_form.find('select[name="jobs_career"]').val(),
        jobs_experience = submit_form
          .find('select[name="jobs_experience"]')
          .val(),
        jobs_qualification = submit_form
          .find('select[name="jobs_qualification"]')
          .val(),
        jobs_quantity = submit_form.find('select[name="jobs_quantity"]').val(),
        jobs_gender = submit_form.find('select[name="jobs_gender"]').val(),
        jobs_days_closing = submit_form
          .find('input[name="jobs_days_closing"]')
          .val(),
        jobs_salary_show = submit_form
          .find('select[name="jobs_salary_show"]')
          .val(),
        jobs_currency_type = submit_form
          .find('select[name="jobs_currency_type"]')
          .val(),
        jobs_salary_minimum = submit_form
          .find('input[name="jobs_salary_minimum"]')
          .val(),
        jobs_salary_maximum = submit_form
          .find('input[name="jobs_salary_maximum"]')
          .val(),
        jobs_salary_rate = submit_form
          .find('select[name="jobs_salary_rate"]')
          .val(),
        jobs_minimum_price = submit_form
          .find('input[name="jobs_minimum_price"]')
          .val(),
        jobs_maximum_price = submit_form
          .find('input[name="jobs_maximum_price"]')
          .val(),
        jobs_select_apply = submit_form
          .find('select[name="jobs_select_apply"]')
          .val(),
        jobs_apply_email = submit_form
          .find('input[name="jobs_apply_email"]')
          .val(),
        jobs_apply_external = submit_form
          .find('input[name="jobs_apply_external"]')
          .val(),
        jobs_apply_call_to = submit_form
          .find('input[name="jobs_apply_call_to"]')
          .val(),
        jobs_select_company = submit_form
          .find('select[name="jobs_select_company"]')
          .val(),
        jobs_location = submit_form.find('select[name="jobs_location"]').val(),
        jobs_thumbnail_url = submit_form
          .find('input[name="jobs_thumbnail_url"]')
          .val(),
        jobs_thumbnail_id = submit_form
          .find('input[name="jobs_thumbnail_id"]')
          .val(),
        civi_gallery_ids = submit_form
          .find('input[name="civi_gallery_ids[]"]')
          .map(function () {
            return $(this).val();
          })
          .get(),
        jobs_video_url = submit_form.find('input[name="jobs_video_url"]').val(),
        jobs_map_address = submit_form
          .find('input[name="civi_map_address"]')
          .val(),
        jobs_map_location = submit_form
          .find('input[name="civi_map_location"]')
          .val(),
        jobs_latitude = submit_form.find('input[name="civi_latitude"]').val(),
        jobs_longtitude = submit_form
          .find('input[name="civi_longtitude"]')
          .val();

      var additional = {};
      $("#jobs-submit-additional").each(function () {
        $.each(custom_field_jobs, function (index, value) {
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
        type: "POST",
        dataType: "json",
        url: ajax_url,
        data: {
          action: "jobs_submit_ajax",
          jobs_form: jobs_form,
          jobs_action: jobs_action,
          jobs_id: jobs_id,
          jobs_title: jobs_title,
          jobs_categories: jobs_categories,
          jobs_type: jobs_type,
          jobs_skills: jobs_skills,
          jobs_des: jobs_des,
          jobs_career: jobs_career,
          jobs_experience: jobs_experience,
          jobs_qualification: jobs_qualification,
          jobs_quantity: jobs_quantity,
          jobs_gender: jobs_gender,
          jobs_days_closing: jobs_days_closing,

          jobs_salary_show: jobs_salary_show,
          jobs_currency_type: jobs_currency_type,
          jobs_salary_minimum: jobs_salary_minimum,
          jobs_salary_maximum: jobs_salary_maximum,
          jobs_salary_rate: jobs_salary_rate,
          jobs_minimum_price: jobs_minimum_price,
          jobs_maximum_price: jobs_maximum_price,

          jobs_select_apply: jobs_select_apply,
          jobs_apply_email: jobs_apply_email,
          jobs_apply_external: jobs_apply_external,
          jobs_apply_call_to: jobs_apply_call_to,

          jobs_select_company: jobs_select_company,
          jobs_location: jobs_location,
          jobs_thumbnail_url: jobs_thumbnail_url,
          jobs_thumbnail_id: jobs_thumbnail_id,
          civi_gallery_ids: civi_gallery_ids,
          jobs_video_url: jobs_video_url,
          custom_field_jobs: additional,

          jobs_map_address: jobs_map_address,
          jobs_map_location: jobs_map_location,
          jobs_latitude: jobs_latitude,
          jobs_longtitude: jobs_longtitude,

          submit_button: submit_button,
        },
        beforeSend: function () {
          if (submit_button == "submit_jobs") {
            $(".btn-submit-jobs .btn-loading").fadeIn();
          } else {
            $(".btn-submit-draft .btn-loading").fadeIn();
          }
        },
        success: function (data) {
          if (submit_button == "submit_jobs") {
            $(".btn-submit-jobs .btn-loading").fadeOut();
            if (data.success === true) {
              window.location.href = jobs_dashboard;
            }
          } else {
            $(".btn-submit-draft .btn-loading").fadeOut();
          }
        },
      });
    }
  });
})(jQuery);
