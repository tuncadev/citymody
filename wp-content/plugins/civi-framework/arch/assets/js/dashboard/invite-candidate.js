(function ($) {
    "use strict";

    var invite_candidate = $(".civi-invite-candidate");

    var ajax_url = civi_invite_candidate_vars.ajax_url,
        not_candidate = civi_invite_candidate_vars.not_candidate;

    $(document).ready(function () {
        invite_candidate.find("select-pagination").change(function () {
            var number = "";
            invite_candidate.find(".select-pagination option:selected").each(function () {
                number += $(this).val() + " ";
            });
            $(this).attr("value");
        });

        invite_candidate.find("select.search-control").on("change", function () {
            invite_candidate.find(".civi-pagination").find('input[name="paged"]').val(1);
            ajax_load_invite();
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

        invite_candidate.find("input.search-control").keyup(
            delay(function () {
                invite_candidate.find(".civi-pagination").find('input[name="paged"]').val(1);
                ajax_load_invite();
            }, 1000)
        );

        $("body").on("click",".civi-invite-candidate .action-setting .btn-delete",
            function (e) {
                e.preventDefault();
                var delete_id = $(this).attr("items-id");
                ajax_load_invite(delete_id, "delete");
            }
        );

        $("body").on(
            "click",
            ".civi-invite-candidate .civi-pagination a.page-numbers",
            function (e) {
                e.preventDefault();
                invite_candidate
                    .find(".civi-pagination li .page-numbers")
                    .removeClass("current");
                $(this).addClass("current");
                var paged = $(this).text();
                var current_page = 1;
                if (
                    invite_candidate.find(".civi-pagination").find('input[name="paged"]').val()
                ) {
                    current_page = invite_candidate
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
                invite_candidate
                    .find(".civi-pagination")
                    .find('input[name="paged"]')
                    .val(paged);

                ajax_load_invite();
            }
        );

        var paged = 1;
        invite_candidate.find(".select-pagination").attr("data-value", paged);

        function ajax_load_invite(item_id = "", action_click = "") {
            var paged = 1;
            var height = invite_candidate.find("#invite-candidate").height();
            var candidate_search = invite_candidate.find('input[name="candidate_search"]').val(),
                list_jobs = $('input[name="list_jobs"]').val(),
                item_amount = invite_candidate.find('select[name="item_amount"]').val(),
                candidate_sort_by = invite_candidate
                    .find('select[name="candidate_sort_by"]')
                    .val();
            paged = invite_candidate.find('.civi-pagination input[name="paged"]').val();

            $.ajax({
                dataType: "json",
                url: ajax_url,
                data: {
                    action: "civi_filter_invite_candidate",
                    item_amount: item_amount,
                    paged: paged,
                    list_jobs : list_jobs,
                    candidate_search: candidate_search,
                    candidate_sort_by: candidate_sort_by,
                    item_id: item_id,
                    action_click: action_click,
                },
                beforeSend: function () {
                    invite_candidate.find(".civi-loading-effect").addClass("loading").fadeIn();
                    invite_candidate.find("#invite-candidate").height(height);
                },
                success: function (data) {
                    if (data.success === true) {
                        var $items_pagination = invite_candidate.find(".items-pagination"),
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

                        invite_candidate.find(".pagination").html(data.pagination);
                        invite_candidate.find("#invite-candidate tbody").fadeOut("fast", function () {
                            invite_candidate.find("#invite-candidate tbody").html(data.candidate_html);
                            invite_candidate.find("#invite-candidate tbody").fadeIn(300);
                        });
                        invite_candidate.find("#invite-candidate").css("height", "auto");
                    } else {
                        invite_candidate
                            .find("#invite-candidate tbody")
                            .html('<span class="not-candidates">' + not_candidate + "</span>");
                    }
                    $(".tab-invite-item span").html("(" + data.total_post + ")");
                    invite_candidate
                        .find(".civi-loading-effect")
                        .removeClass("loading")
                        .fadeOut();
                },
            });
        }
    });
})(jQuery);
