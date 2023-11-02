jQuery(document).ready(function($) {

    /***** Colour picker *****/
    $('.colorpicker').each( function() {
        $(this).hide();
        $(this).farbtastic( $(this).closest('.color-picker').find('.color') );
    });

    $('.color').click(function() {
        $(this).closest('.color-picker').find('.colorpicker').fadeIn();
    });

    $(document).mousedown(function() {
        $('.colorpicker').each(function() {
            var display = $(this).css('display');
            if ( display === 'block' )
                $(this).fadeOut();
        });
    });


});