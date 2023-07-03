(function ($) {
    "use strict";
    jQuery(document).ready(function () {
        $('.add-social').on('click', function (e) {
            e.preventDefault();
            $('.errors-log').text('');
            $('.add-social').addClass('disabled');
            var clone = $('.field-social-clone').html();
            $('.add-social-list').append(clone);
            $('.add-social-list .clone-wrap').each(function (index) {
                index += 1;
                $(this).find('.number-network').html(index);
            });
            $('.add-social-list .clone-wrap:last-child').find('.icon-delete').trigger('click');
        });
        $('.add-social-list .clone-wrap').each(function (index) {
            index += 1;
            $(this).find('.number-network').html(index);
        });
        $('.remove-social').on('click', function (e) {
            e.preventDefault();
            $(this).parents('.clone-wrap').remove();
        });
        $(".add-social-list").bind("DOMSubtreeModified", function () {
            $('.remove-social').on('click', function (e) {
                e.preventDefault();
                $(this).parents('.clone-wrap').remove();
            });
        });
    });
})(jQuery);
