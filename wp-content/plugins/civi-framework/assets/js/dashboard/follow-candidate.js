(function ($) {
    "use strict";

    var follow_candidate = $(".civi-follow-candidate");

    var ajax_url = civi_follow_candidate_vars.ajax_url,
        not_candidate = civi_follow_candidate_vars.not_candidate;

    $(document).ready(function () {
        follow_candidate.find("select-pagination").change(function () {
            var number = "";
            follow_candidate.find(".select-pagination option:selected").each(function () {
                number += $(this).val() + " ";
            });
            $(this).attr("value");
        });

        follow_candidate.find("select.search-control").on("change", function () {
            follow_candidate.find(".civi-pagination").find('input[name="paged"]').val(1);
            ajax_load_follow();
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

        follow_candidate.find("input.search-control").keyup(
            delay(function () {
                follow_candidate.find(".civi-pagination").find('input[name="paged"]').val(1);
                ajax_load_follow();
            }, 1000)
        );

        $("body").on("click",".civi-follow-candidate .action-setting .btn-delete",
            function (e) {
                e.preventDefault();
                var delete_id = $(this).attr("items-id");
                ajax_load_follow(delete_id, "delete");
            }
        );

        $("body").on(
            "click",
            ".civi-follow-candidate .civi-pagination a.page-numbers",
            function (e) {
                e.preventDefault();
                follow_candidate
                    .find(".civi-pagination li .page-numbers")
                    .removeClass("current");
                $(this).addClass("current");
                var paged = $(this).text();
                var current_page = 1;
                if (
                    follow_candidate.find(".civi-pagination").find('input[name="paged"]').val()
                ) {
                    current_page = follow_candidate
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
                follow_candidate
                    .find(".civi-pagination")
                    .find('input[name="paged"]')
                    .val(paged);

                ajax_load_follow();
            }
        );

        var paged = 1;
        follow_candidate.find(".select-pagination").attr("data-value", paged);

        function ajax_load_follow(item_id = "", action_click = "") {
            var paged = 1;
            var height = follow_candidate.find("#follow-candidate").height();
            var candidate_search = follow_candidate.find('input[name="candidate_search"]').val(),
                item_amount = follow_candidate.find('select[name="item_amount"]').val(),
                candidate_sort_by = follow_candidate
                    .find('select[name="candidate_sort_by"]')
                    .val();
            paged = follow_candidate.find('.civi-pagination input[name="paged"]').val();

            $.ajax({
                dataType: "json",
                url: ajax_url,
                data: {
                    action: "civi_filter_follow_candidate",
                    item_amount: item_amount,
                    paged: paged,
                    candidate_search: candidate_search,
                    candidate_sort_by: candidate_sort_by,
                    item_id: item_id,
                    action_click: action_click,
                },
                beforeSend: function () {
                    follow_candidate.find(".civi-loading-effect").addClass("loading").fadeIn();
                    follow_candidate.find("#follow-candidate").height(height);
                },
                success: function (data) {
                    if (data.success === true) {
                        var $items_pagination = follow_candidate.find(".items-pagination"),
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

                        follow_candidate.find(".pagination").html(data.pagination);
                        follow_candidate.find("#follow-candidate tbody").fadeOut("fast", function () {
                            follow_candidate.find("#follow-candidate tbody").html(data.candidate_html);
                            follow_candidate.find("#follow-candidate tbody").fadeIn(300);
                        });
                        follow_candidate.find("#follow-candidate").css("height", "auto");
                    } else {
                        follow_candidate
                            .find("#follow-candidate tbody")
                            .html('<span class="not-candidates">' + not_candidate + "</span>");
                    }
                    $(".tab-follow-item span").html("(" + data.total_post + ")");
                    follow_candidate
                        .find(".civi-loading-effect")
                        .removeClass("loading")
                        .fadeOut();
                },
            });
        }
    });
})(jQuery);
