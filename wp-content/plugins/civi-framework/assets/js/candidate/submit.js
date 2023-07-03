"use strict";

jQuery(document).ready(function ($) {
  /**
   *  Declarations go here
   */

  var submit_form = $("#candidate-profile-form");
  var profile_strength = $(".candidate-profile-strength");
  var profile_dashboard = $(".candidate-profile-dashboard");
  var ajax_url = civi_candidate_vars.ajax_url;
  var custom_field_candidate = civi_candidate_vars.custom_field_candidate;

  var tabTemplate = profile_dashboard.find(".tab-item.repeater");
  if (tabTemplate.length > 0) {
    $.each(tabTemplate, function () {
      var val = $(this).find("a").attr("href").replace("#tab-", "");
      var $index = 1,
        tabID = submit_form.find("#tab-" + val),
        btn_more = tabID.find(".btn-more.profile-fields"),
        item = tabID.find(".civi-candidate-warpper .row").length,
        template = $(tabID.find("template").html().trim());

      if (item == 0) {
        template.find(".group-title h6 span").text($index);
        template
          .find(".project-upload")
          .attr("id", "project-uploader_" + $index);
        template
          .find(".project-uploaded-list")
          .attr("id", "project-uploaded-list_" + $index);
        template
          .find(".errors-log")
          .attr("id", "civi_project_errors_log_" + $index);
        template
          .find(".uploaded-container")
          .attr("id", "uploaded-container_" + $index);
        template.find(".uploaded-main").attr("id", "uploader-main_" + $index);
        template.insertBefore(btn_more);
      }
    });
  }

  var $rowActive = submit_form.find(
    ".civi-candidate-warpper > .row:first-child"
  );
  $rowActive.find(".group-title i").removeClass("delete-group");
  $rowActive.find("input").addClass("point-mark");
  $rowActive.find("textarea").addClass("point-mark");
  $rowActive.find(".project-uploaded-list input").removeClass("point-mark");
  $rowActive.find("#project-uploaded-list_1").addClass("point-mark");

  var $profile = $("#candidate-profile");
  var $fieldPoint = submit_form.find(".point-mark");

  function tabMarkPoint() {
    var textTab = [
      "info",
      "awards",
      "projects",
      "skills",
      "experience",
      "education",
    ];
    $.each(textTab, function (index, val) {
      var tabID = submit_form.find("#tab-" + val),
        checkStrength = profile_strength.find("#profile-check-" + val),
        textHasCheck = checkStrength.data("has-check"),
        textNotCheck = checkStrength.data("not-check"),
        textCheck = checkStrength.find("span"),
        tabLength = tabID.find(".point-mark").length,
        tabLengthActive = tabID.find(".point-mark.point-active").length;

      if (tabLength == tabLengthActive) {
        checkStrength.addClass("check");
        textCheck.text(textHasCheck);
      } else {
        checkStrength.removeClass("check");
        textCheck.text(textNotCheck);
      }
    });
  }

  function markPoint() {
    $fieldPoint.each(function () {
      if ($(this).val() !== "") {
        $(this).addClass("point-active");
      } else {
        $(this).removeClass("point-active");
      }
    });

    var pointActive = submit_form.find(".point-mark.point-active").length - 1;
    var pointAll = submit_form.find(".point-mark").length - 1;

    var mediaGallery = submit_form.find(
      "#civi_gallery_thumbs .media-thumb-wrap"
    ).length;
    var uploadCv = submit_form.find("#civi_drop_cv").attr("data-attachment-id");
    var avatar = submit_form
      .find('input[name="author_avatar_image_url"]')
      .val();
    var coverImage = submit_form.find(
      'input[name="candidate_cover_image_url"]'
    ).length;
    var select2 = submit_form
      .find(".select2-selection__choice")
      .attr("data-select2-id");
    var projectUploaded = submit_form.find("#project-uploaded-list_1");

    if (mediaGallery > 0) {
      pointActive = pointActive + 1;
    } else {
      pointActive = pointActive;
    }

    if (avatar !== "") {
      pointActive = pointActive + 1;
    } else {
      pointActive = pointActive;
    }

    if (coverImage > 0) {
      pointActive = pointActive + 1;
    } else {
      pointActive = pointActive;
    }

    if (uploadCv !== "") {
      pointActive = pointActive + 1;
    } else {
      pointActive = pointActive;
    }

    projectUploaded.addClass("point-mark");
    if (projectUploaded.find(".media-thumb").length > 0) {
      projectUploaded.addClass("point-active");
      pointActive = pointActive + 1;
    } else {
      projectUploaded.removeClass("point-active");
      pointActive = pointActive;
    }

    pointAll = pointAll + 5;

    if (typeof select2 === "undefined") {
      pointActive = pointActive;
      $(".civi-select2").removeClass("point-active");
    } else {
      pointActive = pointActive + 1;
      $(".civi-select2").addClass("point-active");
    }

    if (pointAll > 0) {
      var percent = Math.round((pointActive / pointAll) * 100);
    } else {
      var percent = 0;
    }

    $profile.find(".profile-strength").css("--pct", percent);
    $profile.find(".profile-strength h1 span:first-child").text(percent);
    submit_form.attr("data-pointactive", pointActive);
    submit_form.attr("data-pointall", pointAll);
    submit_form.find('input[name="candidate_profile_strength"]').val(percent);

    $(".profile-strength.left-sidebar").css("--pct", percent);
    $(".profile-strength.left-sidebar")
      .find(".title")
      .find("span:nth-child(2)")
      .text(percent);

    tabMarkPoint();
  }

  markPoint();
  $fieldPoint.change(function () {
    markPoint();
  });

  if (typeof tinyMCE !== "undefined") {
    if ($("#wp-candidate_des-wrap").hasClass("tmce-active")) {
      tinyMCE.get("candidate_des").on("change", function () {
        var value = tinyMCE
          .get("candidate_des")
          .getContent({ format: "text" })
          .trim().length;
        $("#wp-candidate_des-wrap").addClass("point-mark");
        if (value > 0) {
          $("#wp-candidate_des-wrap").addClass("point-active");
        } else {
          $("#wp-candidate_des-wrap").removeClass("point-active");
        }
        markPoint();
      });
    }
  }

  submit_form.closest("#wrapper").css("overflow", "inherit");

  //  Edit Candidate Profile
  function ajax_submit() {
    var candidate_id = submit_form.find('input[name="candidate_id"]').val(),
      candidate_first_name = submit_form
        .find('input[name="candidate_first_name"]')
        .val(),
      candidate_last_name = submit_form
        .find('input[name="candidate_last_name"]')
        .val(),
      candidate_email = submit_form.find('input[name="candidate_email"]').val(),
      candidate_phone = submit_form.find('input[name="candidate_phone"]').val(),
      candidate_current_position = submit_form
        .find('input[name="candidate_current_position"]')
        .val(),
      candidate_categories = submit_form
        .find('select[name="candidate_categories"]')
        .val(),
      candidate_des = tinymce.get("candidate_des").getContent(),
      candidate_dob = submit_form.find('input[name="candidate_dob"]').val(),
      candidate_age = submit_form.find('select[name="candidate_age"]').val(),
      /* Physical */
      candidate_height = submit_form
        .find('select[name="candidate_height"]')
        .val(),
      candidate_weight = submit_form
        .find('select[name="candidate_weight"]')
        .val(),
      candidate_footsize = submit_form
        .find('select[name="candidate_footsize"]')
        .val(),
      candidate_haircolor = submit_form
        .find('select[name="candidate_haircolor"]')
        .val(),
      candidate_hairtype = submit_form
        .find('select[name="candidate_hairtype"]')
        .val(),
      candidate_eyecolor = submit_form
        .find('select[name="candidate_eyecolor"]')
        .val(),
      candidate_skincolor = submit_form
        .find('select[name="candidate_skincolor"]')
        .val(),
      candidate_chestsize = submit_form
        .find('select[name="candidate_chestsize"]')
        .val(),
      candidate_waistsize = submit_form
        .find('select[name="candidate_waistsize"]')
        .val(),
      candidate_hipsize = submit_form
        .find('select[name="candidate_hipsize"]')
        .val(),
      candidate_bodytype = submit_form
        .find('select[name="candidate_bodytype"]')
        .val(),
      /* ****** */
      /* Bank */
      candidate_hesapturu = submit_form
        .find('select[name="candidate_hesapturu"]')
        .val(),
      candidate_namelast = submit_form
        .find('input[name="candidate_namelast"]')
        .val(),
      candidate_kimlik = submit_form
        .find('input[name="candidate_kimlik"]')
        .val(),
      candidate_telefonu = submit_form
        .find('input[name="candidate_telefonu"]')
        .val(),
      candidate_iban = submit_form.find('input[name="candidate_iban"]').val(),
      candidate_adres = submit_form.find('input[name="candidate_adres"]').val(),
      /******* */
      candidate_gender = submit_form
        .find('select[name="candidate_gender"]')
        .val(),
      candidate_languages = submit_form
        .find('select[name="candidate_languages"]')
        .val(),
      candidate_qualification = submit_form
        .find('select[name="candidate_qualification"]')
        .val(),
      candidate_yoe = submit_form.find('select[name="candidate_yoe"]').val(),
      candidate_salary_type = submit_form
        .find('select[name="candidate_salary_type"]')
        .val(),
      candidate_offer_salary = submit_form
        .find('input[name="candidate_offer_salary"]')
        .val(),
      candidate_currency_type = submit_form
        .find('select[name="candidate_currency_type"]')
        .val(),
      candidate_education_title = submit_form
        .find('input[name="candidate_education_title[]"]')
        .map(function () {
          return $(this).val();
        })
        .get(),
      candidate_education_level = submit_form
        .find('input[name="candidate_education_level[]"]')
        .map(function () {
          return $(this).val();
        })
        .get(),
      candidate_education_from = submit_form
        .find('input[name="candidate_education_from[]"]')
        .map(function () {
          return $(this).val();
        })
        .get(),
      candidate_education_to = submit_form
        .find('input[name="candidate_education_to[]"]')
        .map(function () {
          return $(this).val();
        })
        .get(),
      candidate_education_description = submit_form
        .find('textarea[name="candidate_education_description[]"]')
        .map(function () {
          return $(this).val();
        })
        .get(),
      candidate_experience_job = submit_form
        .find('input[name="candidate_experience_job[]"]')
        .map(function () {
          return $(this).val();
        })
        .get(),
      candidate_experience_company = submit_form
        .find('input[name="candidate_experience_company[]"]')
        .map(function () {
          return $(this).val();
        })
        .get(),
      candidate_experience_from = submit_form
        .find('input[name="candidate_experience_from[]"]')
        .map(function () {
          return $(this).val();
        })
        .get(),
      candidate_experience_to = submit_form
        .find('input[name="candidate_experience_to[]"]')
        .map(function () {
          return $(this).val();
        })
        .get(),
      candidate_experience_description = submit_form
        .find('textarea[name="candidate_experience_description[]"]')
        .map(function () {
          return $(this).val();
        })
        .get(),
      candidate_skills = submit_form
        .find('select[name="candidate_skills"]')
        .val(),
      candidate_project_title = submit_form
        .find('input[name="candidate_project_title[]"]')
        .map(function () {
          return $(this).val();
        })
        .get(),
      candidate_project_link = submit_form
        .find('input[name="candidate_project_link[]"]')
        .map(function () {
          return $(this).val();
        })
        .get(),
      candidate_project_description = submit_form
        .find('textarea[name="candidate_project_description[]"]')
        .map(function () {
          return $(this).val();
        })
        .get(),
      candidate_project_image_id = submit_form
        .find('input[name="candidate_project_image_id[]"]')
        .map(function () {
          return $(this).val();
        })
        .get(),
      candidate_project_image_url = submit_form
        .find('input[name="candidate_project_image_url[]"]')
        .map(function () {
          return $(this).val();
        })
        .get(),
      candidate_award_title = submit_form
        .find('input[name="candidate_award_title[]"]')
        .map(function () {
          return $(this).val();
        })
        .get(),
      candidate_award_date = submit_form
        .find('input[name="candidate_award_date[]"]')
        .map(function () {
          return $(this).val();
        })
        .get(),
      candidate_award_description = submit_form
        .find('textarea[name="candidate_award_description[]"]')
        .map(function () {
          return $(this).val();
        })
        .get(),
      candidate_cover_image_id = submit_form
        .find('input[name="candidate_cover_image_id"]')
        .val(),
      candidate_cover_image_url = submit_form
        .find('input[name="candidate_cover_image_url"]')
        .val(),
      author_avatar_image_id = submit_form
        .find('input[name="author_avatar_image_id"]')
        .val(),
      author_avatar_image_url = submit_form
        .find('input[name="author_avatar_image_url"]')
        .val(),
      candidate_video_title = submit_form
        .find('input[name="candidate_video_title[]"]')
        .map(function () {
          return $(this).val();
        })
        .get(),
      candidate_video_url = submit_form
        .find('input[name="candidate_video_url[]"]')
        .map(function () {
          return $(this).val();
        })
        .get(),
      candidate_audio_title = submit_form
        .find('input[name="candidate_audio_title[]"]')
        .map(function () {
          return $(this).val();
        })
        .get(),
      candidate_audio_url = submit_form
        .find('input[name="candidate_audio_url[]"]')
        .map(function () {
          return $(this).val();
        })
        .get(),
      candidate_resume = submit_form
        .find("#civi_drop_cv")
        .attr("data-attachment-id"),
      candidate_twitter = submit_form
        .find('input[name="candidate_twitter"]')
        .val(),
      candidate_linkedin = submit_form
        .find('input[name="candidate_linkedin"]')
        .val(),
      candidate_facebook = submit_form
        .find('input[name="candidate_facebook"]')
        .val(),
      candidate_instagram = submit_form
        .find('input[name="candidate_instagram"]')
        .val(),
      candidate_social_name = submit_form
        .find('input[name="candidate_social_name[]"]')
        .map(function () {
          return $(this).val();
        })
        .get(),
      candidate_social_url = submit_form
        .find('input[name="candidate_social_url[]"]')
        .map(function () {
          return $(this).val();
        })
        .get(),
      candidate_location = submit_form
        .find('select[name="candidate_location"]')
        .val(),
      candidate_map_address = submit_form
        .find('input[name="civi_map_address"]')
        .val(),
      candidate_map_location = submit_form
        .find('input[name="civi_map_location"]')
        .val(),
      candidate_latitude = submit_form
        .find('input[name="civi_latitude"]')
        .val(),
      candidate_longtitude = submit_form
        .find('input[name="civi_longtitude"]')
        .val(),
      civi_gallery_ids = submit_form
        .find('input[name="civi_gallery_ids[]"]')
        .map(function () {
          return $(this).val();
        })
        .get(),
      candidate_profile_strength = submit_form
        .find('input[name="candidate_profile_strength"]')
        .val();

    var additional = {};
    submit_form.find(".block-from").each(function () {
      $.each(custom_field_candidate, function (index, value) {
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
        action: "candidate_submit_ajax",
        candidate_id: candidate_id,

        candidate_first_name: candidate_first_name,
        candidate_last_name: candidate_last_name,
        candidate_email: candidate_email,
        candidate_phone: candidate_phone,
        candidate_current_position: candidate_current_position,
        candidate_categories: candidate_categories,
        candidate_des: candidate_des,
        candidate_dob: candidate_dob,
        candidate_age: candidate_age,
        /* Physical attr */
        candidate_height: candidate_height,
        candidate_weight: candidate_weight,
        candidate_footsize: candidate_footsize,
        candidate_haircolor: candidate_haircolor,
        candidate_hairtype: candidate_hairtype,
        candidate_eyecolor: candidate_eyecolor,
        candidate_skincolor: candidate_skincolor,
        candidate_chestsize: candidate_chestsize,
        candidate_waistsize: candidate_waistsize,
        candidate_hipsize: candidate_hipsize,
        candidate_bodytype: candidate_bodytype,
        /* ***** */
        /* Bank */
        candidate_hesapturu: candidate_hesapturu,
        candidate_namelast: candidate_namelast,
        candidate_kimlik: candidate_kimlik,
        candidate_telefonu: candidate_telefonu,
        candidate_iban: candidate_iban,
        candidate_adres: candidate_adres,
        /**** */
        candidate_gender: candidate_gender,
        candidate_languages: candidate_languages,
        candidate_qualification: candidate_qualification,
        candidate_yoe: candidate_yoe,
        candidate_offer_salary: candidate_offer_salary,
        candidate_salary_type: candidate_salary_type,
        candidate_currency_type: candidate_currency_type,

        candidate_education_title: candidate_education_title,
        candidate_education_level: candidate_education_level,
        candidate_education_from: candidate_education_from,
        candidate_education_to: candidate_education_to,
        candidate_education_description: candidate_education_description,

        candidate_experience_job: candidate_experience_job,
        candidate_experience_company: candidate_experience_company,
        candidate_experience_from: candidate_experience_from,
        candidate_experience_to: candidate_experience_to,
        candidate_experience_description: candidate_experience_description,

        candidate_skills: candidate_skills,

        candidate_project_title: candidate_project_title,
        candidate_project_link: candidate_project_link,
        candidate_project_description: candidate_project_description,
        candidate_project_image_id: candidate_project_image_id,
        candidate_project_image_url: candidate_project_image_url,

        candidate_award_title: candidate_award_title,
        candidate_award_date: candidate_award_date,
        candidate_award_description: candidate_award_description,

        candidate_cover_image_id: candidate_cover_image_id,
        candidate_cover_image_url: candidate_cover_image_url,
        author_avatar_image_id: author_avatar_image_id,
        author_avatar_image_url: author_avatar_image_url,

        civi_gallery_ids: civi_gallery_ids,

        candidate_video_title: candidate_video_title,
        candidate_video_url: candidate_video_url,

        candidate_audio_title: candidate_audio_title,
        candidate_audio_url: candidate_audio_url,

        candidate_resume: candidate_resume,

        candidate_twitter: candidate_twitter,
        candidate_linkedin: candidate_linkedin,
        candidate_facebook: candidate_facebook,
        candidate_instagram: candidate_instagram,
        candidate_social_name: candidate_social_name,
        candidate_social_url: candidate_social_url,

        candidate_location: candidate_location,
        candidate_map_address: candidate_map_address,
        candidate_map_location: candidate_map_location,
        candidate_latitude: candidate_latitude,
        candidate_longtitude: candidate_longtitude,

        candidate_profile_strength: candidate_profile_strength,

        custom_field_candidate: additional,
      },
      beforeSend: function () {
        $(".btn-update-profile .btn-loading").fadeIn();
      },
      success: function (data) {
        $(".btn-update-profile .btn-loading").fadeOut();
        if (data.success === true) {
          window.location.reload();
        }
      },
    });
  }

  // Extend Date object with a function to add days
  //
  Date.prototype.addDays = function (days) {
    this.setDate(this.getDate() + parseInt(days));
    return this;
  };

  // Stored current tab in Local Storage
  //
  function setTabLocalStorage(value) {
    var current_page = $("#main div:first-child").attr("id");

    localStorage.setItem(
      "session_civi_tab_dashboard" + "_" + current_page,
      value
    );
  }

  // Retrive stored tab in Local Storage
  //
  function getTabLocalStorage() {
    var current_page = $("#main div:first-child").attr("id");

    return localStorage.getItem(
      "session_civi_tab_dashboard" + "_" + current_page
    );
  }

  // Candidate Profile Switch tab
  //
  function switchToTab(obj) {
    $(".tab-dashboard ul li").removeClass("active");
    $(obj).addClass("active");
    var id = $(obj).find("a").attr("href");
    $(".tab-info").hide();
    $(id).show();
  }

  // Onready check and load the stored tab
  //
  function showSavedTab() {
    var tabDefault = $("#candidate-profile .tab-list li:first-child");
    var idStored = getTabLocalStorage();

    if (idStored !== null) {
      tabDefault = $(".tab-list").find(`li a[href="${idStored}"]`).parent();
    }

    switchToTab(tabDefault);
  }

  // Oncheck check and load the stored tab
  //
  function removeAllChecked() {
    var checkedBoxes = $('input[name="candidate_cover_image_id"]:checked');
    checkedBoxes.prop("checked", false);
  }

  // Make Validator to check array Input fields
  // https://github.com/jquery-validation/jquery-validation/issues/1226
  //
  $.validator.prototype.checkForm = function () {
    this.prepareForm();
    for (
      var i = 0, elements = (this.currentElements = this.elements());
      elements[i];
      i++
    ) {
      if (
        this.findByName(elements[i].name).length != undefined &&
        this.findByName(elements[i].name).length > 1
      ) {
        for (
          var cnt = 0;
          cnt < this.findByName(elements[i].name).length;
          cnt++
        ) {
          this.check(this.findByName(elements[i].name)[cnt]);
        }
      } else {
        this.check(elements[i]);
      }
    }
    return this.valid();
  };

  // Set Attribute and Property for Element
  //
  function setAttrAndProp(input, attr = {}, prop = {}) {
    if (!(input instanceof jQuery)) {
      return false;
    }

    $.each(attr, function (attrName, attrVal) {
      input.attr(attrName, attrVal);
    });

    $.each(prop, function (propName, propVal) {
      input.attr(propName, propVal);
    });
  }

  // Find Input Date in the same group
  //
  function findRelatedInputDate(input, nameToFind) {
    if (!(input instanceof jQuery)) {
      return false;
    }
    var relatedInput = input
      .closest(".row")
      .find(`input[name="${nameToFind}"]`);
    return relatedInput;
  }

  function setRelatedInputDateTo(input) {
    if (!(input instanceof jQuery)) {
      return false;
    }

    var nameWithFrom = input.attr("name");
    var nameWithTo = nameWithFrom.replace("from", "to");

    var relatedInput = findRelatedInputDate(input, nameWithTo);

    if (relatedInput == false) {
      return false;
    }

    var fromDate = new Date(input.val());
    if (fromDate !== "") {
      var minDate = fromDate.addDays(1).toISOString().split("T")[0];
    }

    var attrs = {
      min: minDate,
    };

    var props = {
      required: true,
    };

    setAttrAndProp(relatedInput, attrs, props);
  }

  function setRelatedInputDateFrom(input) {
    if (!(input instanceof jQuery)) {
      return false;
    }

    var nameWithTo = input.attr("name");
    var nameWithFrom = nameWithTo.replace("to", "from");

    var relatedInput = findRelatedInputDate(input, nameWithFrom);

    if (relatedInput == false) {
      return false;
    }

    var toDate = new Date(input.val());
    var maxDate = toDate.addDays(-1).toISOString().split("T")[0];

    var attrs = {
      max: maxDate,
    };

    var props = {
      required: true,
    };

    setAttrAndProp(relatedInput, attrs, props);
  }

  // Validate Single Input
  //
  function validateSingleInput(input) {
    if (!(input instanceof jQuery)) {
      return false;
    }

    submit_form.validate().element(input);

    if (input.hasClass("error")) {
      input.focus();
      return false;
    }

    return true;
  }

  // Ajax Delete attachment
  function ajaxDeleteAttachment(clickedEl, $type, $none) {
    var $this = $(clickedEl),
      icon_delete = $this,
      thumbnail = $this.closest(".media-thumb-wrap"),
      candidate_id = $this.data("candidate-id"),
      attachment_id = $this.data("attachment-id");

    icon_delete.html('<i class="fal fa-spinner fa-spin large"></i>');

    $.ajax({
      type: "post",
      url: ajax_url,
      dataType: "json",
      data: {
        action: "remove_candidate_attachment_ajax",
        candidate_id: candidate_id,
        attachment_id: attachment_id,
        type: $type,
        removeNonce: $none,
      },
      success: function (response) {
        if (response.success) {
          thumbnail.remove();
        }
        icon_delete.html('<i class="fal fa-spinner fa-spin large"></i>');
      },
      error: function () {
        icon_delete.html('<i class="far fa-trash-alt large"></i>');
      },
    });
  }

  function formatInputDate() {
    $('input[type="date"]').datepicker({
      dateFormat: "dd/mm/yyyy",
    });
  }

  //  Event: Remove an add-more Group
  //
  submit_form.on("click", "i.delete-group", function () {
    var groupToRemove = $(this).closest(".group-title").closest(".row");
    var groupSiblings = groupToRemove.siblings(".row");
    var template = groupToRemove.siblings("template");

    groupToRemove.remove();

    $.each(groupSiblings, function renumberGroups(index) {
      $(this)
        .find(".group-title h6 span")
        .text(index + 1);
    });

    // Update total number of groups
    template.data("size", groupSiblings.size());
  });

  //  Event: Hide/Show A Group
  //
  submit_form.on("click", ".group-title", function () {
    if (!$(this).hasClass("up")) {
      $(this).addClass("up");
    } else {
      $(this).removeClass("up");
    }
  });

  // Validate Form and Submit
  //
  $.validator.setDefaults({ ignore: ":hidden:not(select)" });

  submit_form.validate({
    ignore: [],
    rules: {},
    messages: {},

    submitHandler: function (form) {
      ajax_submit();
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

  // Event: onblur DateInput "from", set DateInput "to" minDate
  //

  submit_form.on("blur", 'input[type="date"][name*="from"]', function () {
    var isValid = validateSingleInput($(this));

    if (!isValid) {
      return false;
    }

    setRelatedInputDateTo($(this));
  });

  // Event: onblur DateInput "from", set DateInput "to" minDate
  //
  submit_form.on("blur", 'input[type="date"][name*="to"]', function () {
    var isValid = validateSingleInput($(this));

    if (!isValid) {
      return false;
    }

    setRelatedInputDateFrom($(this));
  });

  // Run on ready
  showSavedTab();

  // Event: onclick tab Profile Form
  $("#candidate-profile .tab-list li").click(function () {
    setTabLocalStorage($(this).find("a").attr("href"));
    switchToTab(this);
  });

  $(".btn-more.profile-fields").on("click", function () {
    var template = $(this).siblings("template");
    var html = $(template.html().trim());
    var index = parseInt(template.data("size")) + 1;

    html.find(".group-title h6 span").text(index);
    html.find(".project-upload").attr("id", "project-uploader_" + index);
    html
      .find(".project-uploaded-list")
      .attr("id", "project-uploaded-list_" + index);
    html.find(".errors-log").attr("id", "civi_project_errors_log_" + index);
    html.find(".uploaded-container").attr("id", "uploaded-container_" + index);
    html.find(".uploaded-main").attr("id", "uploader-main_" + index);

    html.insertBefore($(this));
    tab_projects_each(index);

    template.data("size", index);
  });

  // Event: Oncheck Cover Image
  //
  $('input[name="candidate_cover_image_id"]').click(function () {
    var is_checked = false;
    if ($(this).is(":checked")) {
      is_checked = true;
    }

    removeAllChecked();

    $(this).prop("checked", is_checked);
  });

  // Event: OnClick Project Upload
  //
  function tab_projects_each($index) {
    $("#tab-projects .row").each(function ($index) {
      var $index = $index + 1;

      var upload_nonce = $("#tab-projects").data("nonce");
      var cv_title = $("#tab-projects").data("title");
      var cv_type = $("#tab-projects").data("type");
      var cv_size = $("#tab-projects").data("file-size");
      var uploader = "uploader_" + $index;

      uploader = new plupload.Uploader({
        browse_button: "uploader-main_" + $index,
        file_data_name: "candidate_upload_file",
        container: "uploaded-container_" + $index,
        drop_element: "uploaded-container_" + $index,
        max_file_count: 1,
        url:
          ajax_url +
          "?action=upload_candidate_attachment_ajax&nonce=" +
          upload_nonce,
        filters: {
          mime_types: [
            {
              title: cv_title,
              extensions: cv_type,
            },
          ],
          max_file_size: cv_size,
          prevent_duplicates: true,
        },
      });

      uploader.init();

      function configProjectUploader(uploader) {
        var options = {
          filters: {
            mime_types: [
              {
                title: cv_title,
                extensions: cv_type,
              },
            ],
            max_file_size: cv_size,
            prevent_duplicates: true,
          },
        };

        uploader.setOption(options);

        uploader.bind("FilesAdded", function (up, files) {
          var candidateThumb = "";
          plupload.each(files, function (file) {
            candidateThumb +=
              '<li class="card-preview-item" id="holder-' + file.id + '"></li>';
          });

          document.getElementById(
            "project-uploaded-list_" + $index
          ).innerHTML += candidateThumb;
          up.refresh();
          uploader.start();
        });

        uploader.bind("UploadProgress", function (up, file) {
          var project_btn = "project-uploader_" + $index;
          document.getElementById(project_btn).innerHTML =
            '<span><i class="fal fa-spinner fa-spin large"></i></span>';
        });

        uploader.bind("Error", function (up, err) {
          document.getElementById(
            "civi_project_errors_log_" + $index
          ).innerHTML += "Error: " + err.message + "<br/>";
        });

        uploader.bind("FileUploaded", function (up, file, ajax_response) {
          var response = $.parseJSON(ajax_response.response);
          if (response.success) {
            var $html = $($("#project-single-image").html().trim());
            var $project_uploaded = $("#project-uploaded-list_" + $index);
            var $project_btn = $("#project-uploader_" + $index);

            $html.find("img").attr("src", response.url);
            $html.find("a").attr("data-attachment-id", response.attachment_id);

            $project_uploaded
              .find("input.candidate_project_image_id")
              .val(response.attachment_id);
            $project_uploaded
              .find("input.candidate_project_image_url")
              .val(response.url);

            $("#holder-" + file.id).html($html);
            $("#candidate-profile-form").find(".point-mark").change();
            $project_btn.text("");
          }
        });
      }

      function triggerUploaderButton(uploader) {
        $(uploader.settings.browse_button).trigger("click");
      }

      var icon_delete =
        "#project-uploaded-list_" + $index + " .icon-project-delete";
      $("body").on("click", icon_delete, function (e) {
        e.preventDefault();
        var $this = $(this);
        var $none = $this.closest("#tab-projects").data("nonce");
        var $type = $this.closest("#tab-projects").data("type");
        var $project_uploaded = $this.closest(".project-uploaded-list");
        var $project_btn = $project_uploaded.siblings(".project-upload");
        var $text_uploaded = $this.closest("#tab-projects").data("uploaded");

        $project_uploaded
          .find('input[name="candidate_project_image_id[]"]')
          .val("");
        $project_uploaded
          .find('input[name="candidate_project_image_url[]"]')
          .val("");
        ajaxDeleteAttachment($(this), $type, $none);
        $("#candidate-profile-form").find(".point-mark").change();

        $project_btn.html($text_uploaded);
      });

      $(this)
        .find(".browse.project-upload")
        .on("click", function () {
          configProjectUploader(uploader);

          triggerUploaderButton(uploader);
        });
    });
  }

  tab_projects_each();
});
