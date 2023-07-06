(function ($) {
	"use strict";

	var my_follow = $(".civi-my-follow");

	var ajax_url = civi_my_follow_vars.ajax_url,
		not_company = civi_my_follow_vars.not_company;

	$(document).ready(function () {
		my_follow.find("select-pagination").change(function () {
			var number = "";
			my_follow.find(".select-pagination option:selected").each(function () {
				number += $(this).val() + " ";
			});
			$(this).attr("value");
		});

		my_follow.find("select.search-control").on("change", function () {
			my_follow.find(".civi-pagination").find('input[name="paged"]').val(1);
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

		my_follow.find("input.search-control").keyup(
			delay(function () {
				my_follow.find(".civi-pagination").find('input[name="paged"]').val(1);
				ajax_load_follow();
			}, 1000)
		);

		$("body").on(
			"click",
			".civi-my-follow .company-control .btn-delete",
			function (e) {
				e.preventDefault();
				var delete_id = $(this).attr("company-id");
				ajax_load_follow(delete_id, "delete");
			}
		);

		$("body").on(
			"click",
			".civi-my-follow .civi-pagination a.page-numbers",
			function (e) {
				e.preventDefault();
				my_follow
					.find(".civi-pagination li .page-numbers")
					.removeClass("current");
				$(this).addClass("current");
				var paged = $(this).text();
				var current_page = 1;
				if (
					my_follow.find(".civi-pagination").find('input[name="paged"]').val()
				) {
					current_page = my_follow
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
				my_follow
					.find(".civi-pagination")
					.find('input[name="paged"]')
					.val(paged);

				ajax_load_follow();
			}
		);

		var paged = 1;
		my_follow.find(".select-pagination").attr("data-value", paged);

		function ajax_load_follow(item_id = "", action_click = "") {
			var paged = 1;
			var height = my_follow.find("#my-follow").height();
			var company_search = my_follow.find('input[name="company_search"]').val(),
				item_amount = my_follow.find('select[name="item_amount"]').val(),
				company_sort_by = my_follow
					.find('select[name="company_sort_by"]')
					.val();
			paged = my_follow.find('.civi-pagination input[name="paged"]').val();

			$.ajax({
				dataType: "json",
				url: ajax_url,
				data: {
					action: "civi_filter_my_follow",
					item_amount: item_amount,
					paged: paged,
					company_search: company_search,
					company_sort_by: company_sort_by,
					item_id: item_id,
					action_click: action_click,
				},
				beforeSend: function () {
					my_follow.find(".civi-loading-effect").addClass("loading").fadeIn();
					my_follow.find("#my-follow").height(height);
				},
				success: function (data) {
					if (data.success === true) {
						var $items_pagination = my_follow.find(".items-pagination"),
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

						my_follow.find(".pagination").html(data.pagination);
						my_follow.find("#my-follow tbody").fadeOut("fast", function () {
							my_follow.find("#my-follow tbody").html(data.company_html);
							my_follow.find("#my-follow tbody").fadeIn(300);
						});
						my_follow.find("#my-follow").css("height", "auto");
					} else {
						my_follow
							.find("#my-follow tbody")
							.html('<span class="not-company">' + not_company + "</span>");
					}
					$(".tab-follow-item span").html("(" + data.total_post + ")");
					my_follow
						.find(".civi-loading-effect")
						.removeClass("loading")
						.fadeOut();
				},
			});
		}
	});
})(jQuery);
