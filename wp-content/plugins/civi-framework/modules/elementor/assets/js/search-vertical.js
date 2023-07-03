(function ($) {
    "use strict";
    
    var VerticalSearchHandler = function ($scope, $) {
        var search_vertical = $scope.find('.civi-search-vertical');
        var search_form = search_vertical.find('.form-search-vertical');

        search_form.each(function() {
            var post_type = $(this).find('input[name="post_type"]').val();
            var filter_search = $(this).find('.search-vertical-' + post_type);
            var available = filter_search.data("key");

            filter_search.autocomplete({
                source: available,
                minLength: 0,
                autoFocus: true,
                focus: true,
            }).focus(function() {
                $(this).data("uiAutocomplete").search($(this).val());
            });
        });

        //tabs
        function tab_dashboard(obj) {
            search_vertical.find(".tab-dashboard ul li").removeClass("active");
            $(obj).addClass("active");
            var id = $(obj).find("a").attr("href");
            search_vertical.find(".tab-info").hide();
            $(id).show();
        }

        search_vertical.find(".tab-list li").click(function () {
            tab_dashboard(this);
            return false;
        });

        tab_dashboard($(".tab-list li:first-child"));
    };

    $(window).on("elementor/frontend/init", function () {
        elementorFrontend.hooks.addAction(
            "frontend/element_ready/civi-search-vertical.default",
            VerticalSearchHandler
        );
    });
})(jQuery);
