(function ($) {
	"use strict";

	var my_apply = $(".civi-my-apply");

	var ajax_url = civi_my_apply_vars.ajax_url,
		not_jobs = civi_my_apply_vars.not_jobs;

	$(document).ready(function () {
		my_apply.find("select-pagination").change(function () {
			var number = "";
			my_apply.find(".select-pagination option:selected").each(function () {
				number += $(this).val() + " ";
			});
			$(this).attr("value");
		});

		my_apply.find("select.search-control").on("change", function () {
			my_apply.find(".civi-pagination").find('input[name="paged"]').val(1);
			ajax_load_apply();
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

		my_apply.find("input.search-control").keyup(
			delay(function () {
				my_apply.find(".civi-pagination").find('input[name="paged"]').val(1);
				ajax_load_apply();
			}, 1000)
		);

		$("body").on(
			"click",
			".civi-my-apply .jobs-control .btn-delete",
			function (e) {
				e.preventDefault();
				var delete_id = $(this).attr("jobs-id");
				ajax_load_apply(delete_id, "delete");
			}
		);

		$("body").on(
			"click",
			".civi-my-apply .civi-pagination a.page-numbers",
			function (e) {
				e.preventDefault();
				my_apply
					.find(".civi-pagination li .page-numbers")
					.removeClass("current");
				$(this).addClass("current");
				var paged = $(this).text();
				var current_page = 1;
				if (
					my_apply.find(".civi-pagination").find('input[name="paged"]').val()
				) {
					current_page = my_apply
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
				my_apply
					.find(".civi-pagination")
					.find('input[name="paged"]')
					.val(paged);

				ajax_load_apply();
			}
		);

		var paged = 1;
		my_apply.find(".select-pagination").attr("data-value", paged);

		function ajax_load_apply(item_id = "", action_click = "") {
			var paged = 1;
			var height = my_apply.find("#my-apply").height();
			var jobs_search = my_apply.find('input[name="jobs_search"]').val(),
				item_amount = my_apply.find('select[name="item_amount"]').val(),
				jobs_sort_by = my_apply.find('select[name="jobs_sort_by"]').val(),
				paged = my_apply.find('.civi-pagination input[name="paged"]').val();

			$.ajax({
				dataType: "json",
				url: ajax_url,
				data: {
					action: "civi_filter_my_apply",
					item_amount: item_amount,
					paged: paged,
					jobs_search: jobs_search,
					jobs_sort_by: jobs_sort_by,
					item_id: item_id,
					action_click: action_click,
				},
				beforeSend: function () {
					my_apply.find(".civi-loading-effect").addClass("loading").fadeIn();
					my_apply.find("#my-apply").height(height);
				},
				success: function (data) {
					if (data.success === true) {
						var $items_pagination = my_apply.find(".items-pagination"),
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

						my_apply.find(".pagination").html(data.pagination);
						my_apply.find("#my-apply tbody").fadeOut("fast", function () {
							my_apply.find("#my-apply tbody").html(data.jobs_html);
							my_apply.find("#my-apply tbody").fadeIn(300);
						});
						my_apply.find("#my-apply").css("height", "auto");
					} else {
						my_apply
							.find("#my-apply tbody")
							.html('<span class="not-jobs">' + not_jobs + "</span>");
					}
					$(".tab-apply-item span").html("(" + data.total_post + ")");
					my_apply
						.find(".civi-loading-effect")
						.removeClass("loading")
						.fadeOut();
				},
			});
		}
	});
})(jQuery);
