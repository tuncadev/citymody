(function ($) {
	"use strict";

	var jobs_dashboard = $(".jobs-dashboard");

	var ajax_url = civi_jobs_dashboard_vars.ajax_url,
		not_jobs = civi_jobs_dashboard_vars.not_jobs;

	$(document).ready(function () {
		jobs_dashboard
			.find(".select-pagination")
			.change(function () {
				var number = "";
				$(".select-pagination option:selected").each(function () {
					number += $(this).val() + " ";
				});
				$(this).attr("value");
			})
			.trigger("change");

		jobs_dashboard.find("select.search-control").on("change", function () {
			$(".civi-pagination").find('input[name="paged"]').val(1);
			ajax_load();
		});

		jobs_dashboard.find("input.search-control").on("input", function () {
			$(".civi-pagination").find('input[name="paged"]').val(1);
			ajax_load();
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

		jobs_dashboard.find("input.jobs-search-control").keyup(
			delay(function () {
				$(".civi-pagination").find('input[name="paged"]').val(1);
				ajax_load();
			}, 1000)
		);

		$("body").on("click", ".jobs-control .btn-mark-featured", function (e) {
			e.preventDefault();
			var item_id = $(this).attr("jobs-id");
			ajax_load(item_id, "mark-featured");
		});

		$("body").on("click", ".jobs-control .btn-show", function (e) {
			e.preventDefault();
			var item_id = $(this).attr("jobs-id");
			ajax_load(item_id, "show");
		});

		$("body").on("click", ".jobs-control .btn-mark-filled", function (e) {
			e.preventDefault();
			var item_id = $(this).attr("jobs-id");
			ajax_load(item_id, "mark-filled");
		});

		$("body").on("click", ".jobs-control .btn-pause", function (e) {
			e.preventDefault();
			var item_id = $(this).attr("jobs-id");
			ajax_load(item_id, "pause");
		});

		$("body").on("click", ".civi-pagination a.page-numbers", function (e) {
			e.preventDefault();
			$(".civi-pagination li .page-numbers").removeClass("current");
			$(this).addClass("current");
			var paged = $(this).text();
			var current_page = 1;
			if (
				jobs_dashboard
					.find(".civi-pagination")
					.find('input[name="paged"]')
					.val()
			) {
				current_page = $(".civi-pagination").find('input[name="paged"]').val();
			}
			if ($(this).hasClass("next")) {
				paged = parseInt(current_page) + 1;
			}
			if ($(this).hasClass("prev")) {
				paged = parseInt(current_page) - 1;
			}
			jobs_dashboard
				.find(".civi-pagination")
				.find('input[name="paged"]')
				.val(paged);

			ajax_load();
		});

		var paged = 1;
		jobs_dashboard.find(".select-pagination").attr("data-value", paged);

		function ajax_load(item_id = "", action_click = "") {
			var paged = 1;
			var height = jobs_dashboard.find("#jobs-dashboard").height();
			var jobs_search = jobs_dashboard.find('input[name="jobs_search"]').val(),
				jobs_status = jobs_dashboard.find('select[name="jobs_status"]').val(),
				item_amount = jobs_dashboard.find('select[name="item_amount"]').val(),
				jobs_sort_by = jobs_dashboard.find('select[name="jobs_sort_by"]').val();
			paged = $(".civi-pagination").find('input[name="paged"]').val();

			$.ajax({
				dataType: "json",
				url: ajax_url,
				data: {
					action: "civi_filter_jobs_dashboard",
					item_amount: item_amount,
					paged: paged,
					jobs_search: jobs_search,
					jobs_status: jobs_status,
					jobs_sort_by: jobs_sort_by,
					item_id: item_id,
					action_click: action_click,
				},
				beforeSend: function () {
					jobs_dashboard
						.find(".civi-loading-effect")
						.addClass("loading")
						.fadeIn();
					jobs_dashboard.find("#jobs-dashboard").height(height);
				},
				success: function (data) {
					if (data.success === true) {
						var $items_pagination = jobs_dashboard.find(".items-pagination"),
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
						$(".num-first").text(value_first);
						$(".num-last").text(value_last);

						if (max_number > select_item) {
							$items_pagination.closest(".pagination-dashboard").show();
							$items_pagination.find(".num-total").html(data.total_post);
						} else {
							$items_pagination.closest(".pagination-dashboard").hide();
						}

						jobs_dashboard.find(".pagination").html(data.pagination);
						jobs_dashboard.find("#my-jobs tbody").fadeOut("fast", function () {
							jobs_dashboard.find("#my-jobs tbody").html(data.jobs_html);
							jobs_dashboard.find("#my-jobs tbody").fadeIn(300);
						});
						jobs_dashboard.find("#jobs-dashboard").css("height", "auto");
					} else {
						jobs_dashboard
							.find("#my-jobs tbody")
							.html('<span class="not-jobs">' + not_jobs + "</span>");
					}
					jobs_dashboard
						.find(".civi-loading-effect")
						.removeClass("loading")
						.fadeOut();
				},
			});
		}
	});
})(jQuery);
