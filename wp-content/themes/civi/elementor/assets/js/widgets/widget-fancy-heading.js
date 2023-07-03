(function ($) {
	"use strict";
	var CiviFancyHeadingHandler = function ($scope) {
		var $element = $scope.find(".civi-fancy-heading");

		var options_default = {
			animationDelay: 4000,
			barAnimationDelay: 3000,
			typingSpeed: 200,
			typingDelay: 2000,
			typingLoop: false,
			typingCursor: false,
		};

		$element.each(function () {
			var $this = $(this);
			var options = $this.data("settings-options");
			var animationDelay = options.animationDelay;
			options = $.extend({}, options_default, options);
			options.barAnimationDelay = options.animationDelay;

			if (options.animationDelay < 3000) {
				options.barWaiting = options.animationDelay * (10 / 100);
			}
			if (options.animationDelay >= 3000) {
				options.barWaiting = options.animationDelay - 3000;
			}

			var duration = animationDelay;

			if ($this.hasClass("loading-bar")) {
				duration = options.barAnimationDelay;
				setTimeout(function () {
					$this.find(".civi-fancy-heading-animated").addClass("is-loading");
				}, options.barWaiting);
			}

			if ($this.hasClass("civi-fancy-heading-typing")) {
				var txt = $this.data("text");
				$this.find(".civi-fancy-heading-animated").typed({
					strings: txt,
					typeSpeed: options.typingSpeed,
					backSpeed: 0,
					startDelay: 300,
					backDelay: options.typingDelay,
					showCursor: options.typingCursor,
					loop: options.typingLoop,
				});
			} else {
				setTimeout(function () {
					hideWord($this.find(".civi-fancy-heading-show").eq(0), options);
				}, duration);
			}
		});

		function hideWord($word, options) {
			var nextWord = takeNext($word);
			if (
				$word
					.parents(".civi-fancy-heading")
					.hasClass("civi-fancy-heading-loading")
			) {
				$word.parent(".civi-fancy-heading-animated").removeClass("is-loading");
				switchWord($word, nextWord);
				setTimeout(function () {
					hideWord(nextWord, options);
				}, options.barAnimationDelay);
				setTimeout(function () {
					$word.parent(".civi-fancy-heading-animated").addClass("is-loading");
				}, options.barWaiting);
			} else {
				switchWord($word, nextWord);
				setTimeout(function () {
					hideWord(nextWord, options);
				}, options.animationDelay);
			}
		}

		function takeNext($word) {
			return !$word.is(":last-child")
				? $word.next()
				: $word.parent().children().eq(0);
		}

		function switchWord($oldWord, $newWord) {
			$oldWord
				.removeClass("civi-fancy-heading-show")
				.addClass("civi-fancy-heading-hidden");
			$newWord
				.removeClass("civi-fancy-heading-hidden")
				.addClass("civi-fancy-heading-show");
		}
	};

	$(window).on("elementor/frontend/init", function () {
		elementorFrontend.hooks.addAction(
			"frontend/element_ready/civi-fancy-heading.default",
			CiviFancyHeadingHandler
		);
	});
})(jQuery);
