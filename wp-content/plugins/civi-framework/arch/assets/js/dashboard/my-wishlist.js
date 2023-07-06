(function ($) {
	"use strict";

	var my_wishlist = $(".civi-my-wishlist");

	var ajax_url = civi_my_wishlist_vars.ajax_url,
		not_jobs = civi_my_wishlist_vars.not_jobs;

	$(document).ready(function () {
		my_wishlist.find("select-pagination").change(function () {
			var number = "";
			my_wishlist.find(".select-pagination option:selected").each(function () {
				number += $(this).val() + " ";
			});
			$(this).attr("value");
		});

		my_wishlist.find("select.search-control").on("change", function () {
			my_wishlist.find(".civi-pagination").find('input[name="paged"]').val(1);
			ajax_load_wishlist();
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

		my_wishlist.find("input.jobs-search-control").keyup(
			delay(function () {
				my_wishlist.find(".civi-pagination").find('input[name="paged"]').val(1);
				ajax_load_wishlist();
			}, 1000)
		);

		$("body").on(
			"click",
			".civi-my-wishlist .jobs-control .btn-delete",
			function (e) {
				e.preventDefault();
				var delete_id = $(this).attr("jobs-id");
				ajax_load_wishlist(delete_id, "delete");
			}
		);

		$("body").on(
			"click",
			".civi-my-wishlist .civi-pagination a.page-numbers",
			function (e) {
				e.preventDefault();
				my_wishlist
					.find(".civi-pagination li .page-numbers")
					.removeClass("current");
				$(this).addClass("current");
				var paged = $(this).text();
				var current_page = 1;
				if (
					my_wishlist.find(".civi-pagination").find('input[name="paged"]').val()
				) {
					current_page = my_wishlist
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
				my_wishlist
					.find(".civi-pagination")
					.find('input[name="paged"]')
					.val(paged);

				ajax_load_wishlist();
			}
		);

		var paged = 1;
		my_wishlist.find(".select-pagination").attr("data-value", paged);

		function ajax_load_wishlist(item_id = "", action_click = "") {
			var paged = 1;
			var height = my_wishlist.find("#my-wishlist").height();
			var jobs_search = my_wishlist.find('input[name="jobs_search"]').val(),
				item_amount = my_wishlist.find('select[name="item_amount"]').val(),
				jobs_sort_by = my_wishlist.find('select[name="jobs_sort_by"]').val();
			paged = my_wishlist.find('.civi-pagination input[name="paged"]').val();

			$.ajax({
				dataType: "json",
				url: ajax_url,
				data: {
					action: "civi_filter_my_wishlist",
					item_amount: item_amount,
					paged: paged,
					jobs_search: jobs_search,
					jobs_sort_by: jobs_sort_by,
					item_id: item_id,
					action_click: action_click,
				},
				beforeSend: function () {
					my_wishlist.find(".civi-loading-effect").addClass("loading").fadeIn();
					my_wishlist.find("#my-wishlist").height(height);
				},
				success: function (data) {
					if (data.success === true) {
						var $items_pagination = my_wishlist.find(".items-pagination"),
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

						my_wishlist.find(".pagination").html(data.pagination);
						my_wishlist.find("#my-wishlist tbody").fadeOut("fast", function () {
							my_wishlist.find("#my-wishlist tbody").html(data.jobs_html);
							my_wishlist.find("#my-wishlist tbody").fadeIn(300);
						});
						my_wishlist.find("#my-wishlist").css("height", "auto");
					} else {
						my_wishlist
							.find("#my-wishlist tbody")
							.html('<span class="not-jobs">' + not_jobs + "</span>");
					}
					$(".tab-wishlist-item span").html("(" + data.total_post + ")");
					my_wishlist
						.find(".civi-loading-effect")
						.removeClass("loading")
						.fadeOut();
				},
			});
		}
	});
})(jQuery);
