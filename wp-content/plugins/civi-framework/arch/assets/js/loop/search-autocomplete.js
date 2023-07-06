(function ($) {
    "use strict";
    $(document).ready(function () {
        var search_id =  '#' + $('.archive-search-control').attr('id');
        var available = $(search_id).data("key");
        $(search_id).autocomplete({
            source: available,
            minLength: 0,
            autoFocus: true,
            focus: true,
        }).focus(function () {
            $(this).data("uiAutocomplete").search($(this).val());
        });

        var search_ids =
            "#" + $(".form-search-canvas .jobs-search-canvas").attr("id");
        var availables = $(search_id).data("key");
        $(search_ids)
            .autocomplete({
                source: availables,
                minLength: 0,
                autoFocus: true,
                focus: true,
            })
            .focus(function () {
                $(this).data("uiAutocomplete").search($(this).val());
            });

    });
})(jQuery);
