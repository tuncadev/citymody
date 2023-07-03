(function($) {
  "use strict";
    $(function() {
    // Upload Image
    $('body').on('click', '.kaya_upload_image_button', function(e){
            e.preventDefault();
            var button = $(this),
                custom_uploader = wp.media({
            title: 'Insert image',
            library : {
                type : 'image'
            },
            button: {
                text: 'Upload Meta box Image'
            },
            multiple: false
            }).on('select', function() {
                var attachment = custom_uploader.state().get('selection').first().toJSON();
                $(button).removeClass('button').html('<img class="true_pre_image" src="' + attachment.url + '" style="width:100px; height:100px; display:block;" />').next().val(attachment.id).next().show();
            })
            .open();
         });
        /*
         * Remove image event
         */
        $('body').on('click', '.kaya_remove_upload_image_button', function(){
            $(this).hide().prev().val('').prev().addClass('button').html('Upload image');
            return false;
        });
    //    
    $('select#select_page_subheader').change(function(){
        $('.upload_page_title_bar_img').hide();
        $('.custom_page_title').hide();
        $('.page_title_description').hide();
        $('.main_slider_shortcode').hide();
        var subheader_type = $('select#select_page_subheader').find('option:selected').val();
        if( subheader_type == 'page_titlebar' ){
            $('.upload_page_title_bar_img').show();
            $('.custom_page_title').show();
            $('.page_title_description').show();
        }else if( subheader_type == 'slider' ){
            $('.main_slider_shortcode').show();
        
        }
    }).change();

});
})(jQuery);