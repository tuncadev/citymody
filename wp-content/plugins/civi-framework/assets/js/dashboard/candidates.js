(function ($) {
	"use strict";

	var candidates_dashboard = $(".candidates-dashboard");

	var ajax_url = civi_candidates_dashboard_vars.ajax_url,
		not_candidates = civi_candidates_dashboard_vars.not_candidates;

	$(document).ready(function () {
		candidates_dashboard
			.find(".select-pagination")
			.change(function () {
				var number = "";
				$(".select-pagination option:selected").each(function () {
					number += $(this).val() + " ";
				});
				$(this).attr("value");
			})
			.trigger("change");

		candidates_dashboard
			.find("select.search-control")
			.on("change", function () {
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

		candidates_dashboard.find("input.search-control").keyup(
			delay(function () {
				$(".civi-pagination").find('input[name="paged"]').val(1);
				ajax_load();
			}, 1000)
		);

		$("body").on(
			"click",
			".candidates-dashboard .list-action .btn-delete",
			function (e) {
				e.preventDefault();
				var items_id = $(this).attr("items-id");
				var author_id = $(this).attr("athour-id");
				var follow_company = $(this).attr("follow_company");
				ajax_load(items_id, author_id, follow_company, "delete");
			}
		);

		$("body").on(
			"click",
			".candidates-dashboard .civi-pagination a.page-numbers",
			function (e) {
				e.preventDefault();
				$(".civi-pagination li .page-numbers").removeClass("current");
				$(this).addClass("current");
				var paged = $(this).text();
				var current_page = 1;
				if ($(".civi-pagination").find('input[name="paged"]').val()) {
					current_page = $(".civi-pagination")
						.find('input[name="paged"]')
						.val();
				}
				if ($(this).hasClass("next")) {
					paged = parseInt(current_page) + 1;
				}
				if ($(this).hasClass("prev")) {
					paged = parseInt(current_page) - 1;
				}
				$(".civi-pagination").find('input[name="paged"]').val(paged);

				ajax_load();
			}
		);

		var paged = 1;
		$(".select-pagination").attr("data-value", paged);

		function ajax_load(
			item_id = "",
			author_id = "",
			follow_company = "",
			action_click = ""
		) {
			var paged = 1;
			var height = candidates_dashboard.find("#candidates-dashboard").height();
			var candidates_search = candidates_dashboard
					.find('input[name="candidates_search"]')
					.val(),
				item_amount = candidates_dashboard
					.find('select[name="item_amount"]')
					.val(),
				candidates_id = candidates_dashboard
					.find('input[name="candidates_id"]')
					.val(),
				candidates_sort_by = candidates_dashboard
					.find('select[name="candidates_sort_by"]')
					.val();
			paged = candidates_dashboard
				.find(".civi-pagination")
				.find('input[name="paged"]')
				.val();

			$.ajax({
				dataType: "json",
				url: ajax_url,
				data: {
					action: "civi_filter_candidates_dashboard",
					item_amount: item_amount,
					paged: paged,
					candidates_sort_by: candidates_sort_by,
					candidates_search: candidates_search,
					candidates_id: candidates_id,
					item_id: item_id,
					follow_company: follow_company,
					author_id: author_id,
					action_click: action_click,
				},
				beforeSend: function () {
					candidates_dashboard
						.find(".civi-loading-effect")
						.addClass("loading")
						.fadeIn();
					candidates_dashboard.find("#candidates-dashboard").height(height);
				},
				success: function (data) {
					if (data.success === true) {
						var $items_pagination =
								candidates_dashboard.find(".items-pagination"),
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

						candidates_dashboard.find(".pagination").html(data.pagination);
						candidates_dashboard
							.find("#candidates-db tbody")
							.fadeOut("fast", function () {
								candidates_dashboard
									.find("#candidates-db tbody")
									.html(data.candidates_html);
								candidates_dashboard.find("#candidates-db tbody").fadeIn(300);
							});
						candidates_dashboard
							.find("#candidates-dashboard")
							.css("height", "auto");
					} else {
						candidates_dashboard
							.find("#candidates-db tbody")
							.html(
								'<span class="not-candidates">' + not_candidates + "</span>"
							);
					}
					candidates_dashboard
						.find(".civi-loading-effect")
						.removeClass("loading")
						.fadeOut();
				},
			});
		}
	});
})(jQuery);
