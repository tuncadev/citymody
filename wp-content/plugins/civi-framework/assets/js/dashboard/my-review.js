(function ($) {
  "use strict";

  var my_review = $(".civi-my-review");

  var ajax_url = civi_my_review_vars.ajax_url,
    not_company = civi_my_review_vars.not_company;

  $(document).ready(function () {
    my_review.find("select-pagination").change(function () {
      var number = "";
      my_review.find(".select-pagination option:selected").each(function () {
        number += $(this).val() + " ";
      });
      $(this).attr("value");
    });

    my_review.find("select.search-control").on("change", function () {
      my_review.find(".civi-pagination").find('input[name="paged"]').val(1);
      ajax_load_review();
    });

    function delay(callback, ms) {
      var timer = 0;
      return function () {
        var context = this,
          args = arguments;
        clearTimeout(timer);
        timer = setTimeout(function () {
          callback.apply(context, args);
        }, ms || 0);
      };
    }

    my_review.find("input.search-control").keyup(
      delay(function () {
        my_review.find(".civi-pagination").find('input[name="paged"]').val(1);
        ajax_load_review();
      }, 1000)
    );

    $("body").on(
      "click",
      ".civi-my-review .company-control .btn-delete",
      function (e) {
        e.preventDefault();
        var delete_id = $(this).attr("comment-id");
        ajax_load_review(delete_id, "delete");
      }
    );

    $("body").on(
      "click",
      ".civi-my-review .civi-pagination a.page-numbers",
      function (e) {
        e.preventDefault();
        my_review
          .find(".civi-pagination li .page-numbers")
          .removeClass("current");
        $(this).addClass("current");
        var paged = $(this).text();
        var current_page = 1;
        if (
          my_review.find(".civi-pagination").find('input[name="paged"]').val()
        ) {
          current_page = my_review
            .find(".civi-pagination")
            .find('input[name="paged"]')
            .val();
        }
        if ($(this).hasClass("next")) {
          paged = parseInt(current_page) + 1;
        }
        if ($(this).hasClass("prev")) {
          paged = parseInt(current_page) - 1;
        }
        my_review
          .find(".civi-pagination")
          .find('input[name="paged"]')
          .val(paged);

        ajax_load_review();
      }
    );

    var paged = 1;
    my_review.find(".select-pagination").attr("data-value", paged);

    function ajax_load_review(item_id = "", action_click = "") {
      var paged = 1;
      var height = my_review.find("#my-review").height();
      var company_search = my_review.find('input[name="company_search"]').val(),
        item_amount = my_review.find('select[name="item_amount"]').val(),
        company_sort_by = my_review
          .find('select[name="company_sort_by"]')
          .val();
      paged = my_review.find('.civi-pagination input[name="paged"]').val();

      $.ajax({
        dataType: "json",
        url: ajax_url,
        data: {
          action: "civi_filter_my_review",
          item_amount: item_amount,
          paged: paged,
          company_search: company_search,
          company_sort_by: company_sort_by,
          item_id: item_id,
          action_click: action_click,
        },
        beforeSend: function () {
          my_review.find(".civi-loading-effect").addClass("loading").fadeIn();
          my_review.find("#my-review").height(height);
        },
        success: function (data) {
          if (data.success === true) {
            var $items_pagination = my_review.find(".items-pagination"),
              select_item = $items_pagination
                .find('select[name="item_amount"] option:selected')
                .val(),
              max_number = data.total_post,
              value_first = select_item * paged + 1 - select_item,
              value_last = select_item * paged;
            if (max_number < value_first) {
              value_first = select_item * (paged - 1) + 1;
            }
            if (max_number < value_last) {
              value_last = max_number;
            }
            $items_pagination.find(".num-first").text(value_first);
            $items_pagination.find(".num-last").text(value_last);

            if (max_number > select_item) {
              $items_pagination.closest(".pagination-dashboard").show();
              $items_pagination.find(".num-total").html(data.total_post);
            } else {
              $items_pagination.closest(".pagination-dashboard").hide();
            }

            my_review.find(".pagination").html(data.pagination);
            my_review.find("#my-review tbody").fadeOut("fast", function () {
              my_review.find("#my-review tbody").html(data.company_html);
              my_review.find("#my-review tbody").fadeIn(300);
            });
            my_review.find("#my-review").css("height", "auto");
          } else {
            my_review
              .find("#my-review tbody")
              .html('<span class="not-company">' + not_company + "</span>");
          }
          $(".tab-review-item span").html("(" + data.total_post + ")");
          my_review
            .find(".civi-loading-effect")
            .removeClass("loading")
            .fadeOut();
        },
      });
    }
  });
})(jQuery);
