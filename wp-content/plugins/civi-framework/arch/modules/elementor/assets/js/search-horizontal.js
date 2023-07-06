(function ($) {
    "use strict";

    var HorizontalSearchHandler = function ($scope, $) {
        var search_form = $scope.find('.civi-search-horizontal');
        var filter_search = search_form.find('#search-horizontal_filter_search');
        var available = filter_search.data("key");

        filter_search.autocomplete({
            source: available,
            minLength: 0,
            autoFocus: true,
            focus: true,
        }).focus(function() {
            $(this).data("uiAutocomplete").search($(this).val());
        });

        search_form.find(".civi-clear-top-filter").on("click", function () {
            filter_search.val("");
            search_form.find(".civi-select2").val("");
			search_form.find(".civi-select2").select2("destroy");
			search_form.find(".civi-select2").select2();
        });
    };

    $(window).on("elementor/frontend/init", function () {
        elementorFrontend.hooks.addAction(
            "frontend/element_ready/civi-search-horizontal.default",
            HorizontalSearchHandler
        );
    });
})(jQuery);
