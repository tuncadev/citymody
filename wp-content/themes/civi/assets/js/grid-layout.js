(function ($) {
	"use strict";

	$.fn.CiviGridLayout = function () {
		var $el, $grid, resizeTimer;

		/**
		 * Calculate size for grid items
		 */
		function calculateMasonrySize($isotopeOptions) {
			var windowWidth = window.innerWidth,
				$gridWidth = $grid[0].getBoundingClientRect().width,
				$column = 1,
				$gutter = 0,
				$zigzagHeight = 0,
				settings = $el.data("grid"),
				lgGutter = settings.gutter ? settings.gutter : 0,
				mdGutter = settings.gutterTablet ? settings.gutterTablet : lgGutter,
				smGutter = settings.gutterMobile ? settings.gutterMobile : mdGutter,
				lgColumns = settings.columns ? settings.columns : 1,
				mdColumns = settings.columnsTablet ? settings.columnsTablet : lgColumns,
				smColumns = settings.columnsMobile ? settings.columnsMobile : mdColumns,
				lgZigzagHeight = settings.zigzagHeight ? settings.zigzagHeight : 0,
				mdZigzagHeight = settings.zigzagHeightTablet
					? settings.zigzagHeightTablet
					: lgZigzagHeight,
				smZigzagHeight = settings.zigzagHeightMobile
					? settings.zigzagHeightMobile
					: mdZigzagHeight,
				zigzagReversed =
					settings.zigzagReversed && settings.zigzagReversed === 1
						? true
						: false;

			var tabletBreakPoint = 1025;
			var mobileBreakPoint = 768;

			if (typeof elementorFrontendConfig !== "undefined") {
				tabletBreakPoint = elementorFrontendConfig.breakpoints.lg;
				mobileBreakPoint = elementorFrontendConfig.breakpoints.md;
			}

			if (windowWidth >= tabletBreakPoint) {
				$column = lgColumns;
				$gutter = lgGutter;
				$zigzagHeight = lgZigzagHeight;
			} else if (windowWidth >= mobileBreakPoint) {
				$column = mdColumns;
				$gutter = mdGutter;
				$zigzagHeight = mdZigzagHeight;
			} else {
				$column = smColumns;
				$gutter = smGutter;
				$zigzagHeight = smZigzagHeight;
			}

			var totalGutterPerRow = ($column - 1) * $gutter;

			var columnWidth = ($gridWidth - totalGutterPerRow) / $column;

			columnWidth = Math.floor(columnWidth);

			var columnWidth2 = columnWidth;
			if ($column > 1) {
				columnWidth2 = columnWidth * 2 + $gutter;
			}

			$grid.children(".grid-sizer").css({
				width: columnWidth + "px",
			});

			var columnHeight = 0,
				columnHeight2 = 0, // 200%.
				columnHeight7 = 0, // 70%.
				columnHeight13 = 0, // 130%.
				ratioW = 1,
				ratioH = 1;

			if (settings.ratio) {
				ratioH = settings.ratio;
			}

			$grid.children(".grid-item").each(function (index) {
				var gridItem = $(this);

				// Zigzag.
				if (
					$zigzagHeight > 0 && // Has zigzag.
					$column > 1 && // More than 1 column.
					index + 1 <= $column // On top items.
				) {
					if (zigzagReversed === false) {
						// Is odd item.
						if (index % 2 === 0) {
							gridItem.css({
								marginTop: $zigzagHeight + "px",
							});
						} else {
							gridItem.css({
								marginTop: "0px",
							});
						}
					} else {
						if (index % 2 !== 0) {
							gridItem.css({
								marginTop: $zigzagHeight + "px",
							});
						} else {
							gridItem.css({
								marginTop: "0px",
							});
						}
					}
				} else {
					gridItem.css({
						marginTop: "0px",
					});
				}

				if (gridItem.data("width") === 2) {
					gridItem.css({
						width: columnWidth2 + "px",
					});
				} else {
					gridItem.css({
						width: columnWidth + "px",
					});
				}

				if ("grid" === settings.type) {
					gridItem.css({
						marginBottom: $gutter + "px",
					});
				}
			});

			if ($isotopeOptions) {
				$isotopeOptions.packery.gutter = $gutter;
				$isotopeOptions.fitRows.gutter = $gutter;
				$grid.isotope($isotopeOptions);
			}

			$grid.isotope("layout");
		}

		function handlerEntranceAnimation() {
			// Used find() for flex layout.
			var items = $grid.find(".grid-item");

			items.elementorWaypoint(
				function () {
					// Fix for different ver of waypoints plugin.
					var _self = this.element ? this.element : this;
					var $self = $(_self);
					$self.addClass("animate");
				},
				{
					offset: "90%",
					triggerOnce: true,
				}
			);
		}

		return this.each(function () {
			$el = $(this);
			$grid = $el.find(".civi-grid");

			var settings = $el.data("grid");
			var gridData;

			if (
				$grid.length > 0 &&
				settings &&
				typeof settings.type !== "undefined"
			) {
				var $isotopeOptions = {
					itemSelector: ".grid-item",
					percentPosition: true,
					transitionDuration: 0,
					packery: {
						columnWidth: ".grid-sizer",
					},
					fitRows: {
						gutter: 10,
					},
				};

				if ("masonry" === settings.type) {
					$isotopeOptions.layoutMode = "packery";
				} else {
					$isotopeOptions.layoutMode = "fitRows";
				}

				gridData = $grid.imagesLoaded(function () {
					calculateMasonrySize($isotopeOptions);
					if ("grid" === settings.type) {
						$grid.children(".grid-item").matchHeight();
						$grid.isotope("layout");
					}
					$grid.addClass("loaded");
				});

				gridData.one("arrangeComplete", function () {
					handlerEntranceAnimation();
				});

				$(window).resize(function () {
					calculateMasonrySize($isotopeOptions);

					// Sometimes layout can be overlap. then re-cal layout one time.
					clearTimeout(resizeTimer);
					resizeTimer = setTimeout(function () {
						// Run code here, resizing has "stopped"
						calculateMasonrySize($isotopeOptions);
					}, 500); // DO NOT decrease the time. Sometime, It'll make layout overlay on resize.
				});
			} else {
				handlerEntranceAnimation();
			}

			$el.on("CiviQueryEnd", function (event, el, $items) {
				if (
					$grid.length > 0 &&
					settings &&
					typeof settings.type !== "undefined"
				) {
					$grid
						.isotope()
						.append($items)
						.isotope("reloadItems", $items)
						.imagesLoaded()
						.always(function () {
							$items.addClass("animate");
							calculateMasonrySize($isotopeOptions);
							if ("grid" === settings.type) {
								$grid.children(".grid-item").matchHeight();
								$grid.isotope("layout");
							}
						});
				} else {
					$grid
						.append($items)
						.imagesLoaded()
						.always(function () {
							$items.addClass("animate");
						});
				}
			});
		});
	};
})(jQuery);
