(function ($) {
	"use strict";

	$.fn.CiviGridQuery = function () {
		var $el, $grid;
		var isQuerying = false;
		var $queryInput;

		/**
		 * Use small loader under of the grid instead of whole grid for pagination infinite scroll + load more button.
		 */
		var infiniteLoader = false;

		function initFilterCount() {
			if (!$el.children(".civi-grid-filter").data("filter-counter")) {
				return;
			}

			$el.find(".btn-filter").each(function () {
				var count = $(this).data("filter-count");

				if ($(this).children(".filter-counter").length > 0) {
					$(this).children(".filter-counter").text(count);
				} else {
					$(this).append('<span class="filter-counter">' + count + "</span>");
				}
			});
		}

		function handlerFilter() {
			$el
				.children(".civi-grid-filter")
				.on("click", ".btn-filter", function (e) {
					if ($(this).hasClass("current")) {
						e.preventDefault();
						return;
					}

					if ("1" != $el.data("query-main")) {
						e.preventDefault();
					} else {
						return;
					}

					var $self = $(this);

					var filterValue = $self.attr("data-filter");

					if ("*" === filterValue) {
						setQueryVars("extra_tax_query", "");
					} else {
						setQueryVars("extra_tax_query", filterValue);
					}

					$el.trigger("CiviBeginQuery");

					$self.siblings().removeClass("current");
					$self.addClass("current");
				});
		}

		function handlerPagination() {
			// Do nothing if in elementor editor mode.
			if ($("body").hasClass("elementor-editor-active")) {
				return;
			}

			if ($el.data("pagination") === "load-more") {
				$el
					.children(".civi-grid-pagination")
					.find(".civi-load-more-button")
					.on("click", function (e) {
						e.preventDefault();

						if (!isQuerying) {
							$(this).hide();

							var paged = getQueryVars("paged");
							paged++;

							setQueryVars("paged", paged);
							infiniteLoader = true;
							handlerQuery();
						}
					});
			} else if ($el.data("pagination") === "load-more-alt") {
				var loadMoreBtn = $($el.data("pagination-custom-button-id"));

				loadMoreBtn.on("click", function (e) {
					e.preventDefault();
					if (!isQuerying) {
						$(this).hide();

						var paged = getQueryVars("paged");
						paged++;

						setQueryVars("paged", paged);
						handlerQuery();
					}
				});
			} else if ($el.data("pagination") === "infinite") {
				// Waiting for header offset top & grid layout rendered.
				var infiniteReady = setInterval(function () {
					if ($grid.hasClass("loaded")) {
						handlerScrollInfinite();
						clearInterval(infiniteReady);
					}
				}, 200);
			} else if ($el.data("pagination") === "navigation") {
				var prevLink = $el.find(".prev-link"),
					nextLink = $el.find(".next-link");

				$el.on("click", ".nav-link", function (e) {
					e.preventDefault();

					if (isQuerying) {
						return;
					}

					if ($(this).hasClass("disabled")) {
						return;
					}

					var action = $(this).data("action");

					var paged = getQueryVars("paged");
					var maxNumberPages = getQuery("max_num_pages");

					if (action === "prev") {
						if (paged > 1) {
							paged--;
						}
					} else {
						if (paged < maxNumberPages) {
							paged++;
						}
					}

					if (paged > 1) {
						prevLink.removeClass("disabled");
					} else {
						prevLink.addClass("disabled");
					}

					if (maxNumberPages > paged) {
						nextLink.removeClass("disabled");
					} else {
						nextLink.addClass("disabled");
					}

					setQueryVars("paged", paged);
					handlerQuery(true);
				});
			}
		}

		function handlerScrollInfinite() {
			var windowHeight = $(window).height();
			// 90% window height.
			var halfWH = (90 / 100) * windowHeight;
			halfWH = parseInt(halfWH);

			var elOffsetTop = $el.offset().top;
			var elHeight = $el.outerHeight(true);
			var offsetTop = elOffsetTop + elHeight;
			var finalOffset = offsetTop - halfWH;
			var oldST = 0;

			// On scroll.
			$(window).scroll(function () {
				var st = $(this).scrollTop();
				// Scroll down & in view.
				if (st > oldST && st >= finalOffset) {
					if (!isQuerying) {
						var paged = getQueryVars("paged");
						var maxNumberPages = getQuery("max_num_pages");

						if (paged < maxNumberPages) {
							paged++;

							setQueryVars("paged", paged);
							infiniteLoader = true;
							handlerQuery();
						}
					}
				}

				oldST = st;
			});

			$(window).on("resize", function () {
				// Fix layout not really completed.
				setTimeout(function () {
					windowHeight = $(window).height();
					// 90% window height.
					halfWH = (90 / 100) * windowHeight;
					halfWH = parseInt(halfWH);

					elOffsetTop = $el.offset().top;
					elHeight = $el.outerHeight(true);
					offsetTop = elOffsetTop + elHeight;
					finalOffset = offsetTop - halfWH;
				}, 100);
			});
		}

		/**
		 * Load post infinity from db.
		 */
		function handlerQuery(reset) {
			isQuerying = true;

			var loader;
			if (infiniteLoader === true) {
				loader = $el.find(".civi-infinite-loader");
			} else {
				loader = $grid.children(".civi-grid-loader");
			}

			loader.addClass("show");

			setTimeout(function () {
				var query = jQuery.parseJSON($queryInput.val());
				var _data = $.param(query);

				$.ajax({
					url: $civi.ajaxurl,
					type: "POST",
					data: _data,
					dataType: "json",
					success: function (results) {
						if (results.max_num_pages) {
							setQuery("max_num_pages", results.max_num_pages);
						}

						if (results.found_posts) {
							setQuery("found_posts", results.found_posts);
						}

						if (results.count) {
							setQuery("count", results.count);
						}

						var html = results.template;
						var $newItems = $(html);

						if (reset === true) {
							$grid.children(".grid-item").remove();
						}

						$el.trigger("CiviQueryEnd", [$el, $newItems]);

						updateResultCount();

						handlerQueryEnd();

						loader.removeClass("show");

						isQuerying = false;
						infiniteLoader = false;
					},
				});
			}, 500);
		}

		/**
		 * Remove pagination if has no posts anymore
		 */
		function handlerQueryEnd() {
			// Do not apply for 'navigation' type.
			if ($el.data("pagination") === "navigation") {
				return;
			}

			var foundPosts = getQuery("found_posts");
			var paged = getQueryVars("paged");
			var numberPages = getQueryVars("posts_per_page");

			if (foundPosts <= paged * numberPages) {
				if ($el.data("pagination") === "load-more-alt") {
					var loadMoreBtn = $($el.data("pagination-custom-button-id"));

					loadMoreBtn.hide();
				} else {
					$el.children(".civi-grid-pagination").hide();
				}

				$el.children(".civi-grid-messages").show(1);
				setTimeout(function () {
					$el.children(".civi-grid-messages").remove();
				}, 5000);
			} else {
				if ($el.data("pagination") === "load-more-alt") {
					var loadMoreBtn = $($el.data("pagination-custom-button-id"));

					loadMoreBtn.show();
				} else {
					$el.children(".civi-grid-pagination").show();
					$el
						.children(".civi-grid-pagination")
						.find(".civi-load-more-button")
						.show();
				}
			}
		}

		function updateResultCount() {
			var resultCount = $el.find(".result-count").first();
			if (resultCount.length > 0) {
				var count = getQuery("found_posts");
				resultCount.find(".count").text(count);
			}
		}

		function getQuery(name) {
			var query = jQuery.parseJSON($queryInput.val());

			return query[name];
		}

		function setQuery(name, newValue) {
			var query = jQuery.parseJSON($queryInput.val());

			query[name] = newValue;

			$queryInput.val(JSON.stringify(query));
		}

		function getQueryVars(name) {
			var queryVars = jQuery.parseJSON($queryInput.val());

			return queryVars.query_vars[name];
		}

		function setQueryVars(name, newValue) {
			var queryVars = jQuery.parseJSON($queryInput.val());

			queryVars.query_vars[name] = newValue;

			$queryInput.val(JSON.stringify(queryVars));
		}

		return this.each(function () {
			$el = $(this);
			$grid = $el.find(".civi-grid");
			$queryInput = $el.find(".civi-query-input").first();

			initFilterCount();
			handlerFilter();
			handlerPagination();

			$el.on("CiviBeginQuery", function () {
				// Reset to first page.
				setQueryVars("paged", 1);
				handlerQuery(true);
			});
		});
	};
})(jQuery);
