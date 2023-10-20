jQuery(document).ready(function($) {


    /***** Uploading files *****/

    var file_frame;

    jQuery.fn.uploadMediaFile = function( button, preview_media ) {
        var button_id = button.attr('id');
        var field_id = button_id.replace( '_button', '' );
        var preview_id = button_id.replace( '_button', '_preview' );
        var thumb_id = button_id.replace( '_button', '_thumb' );
        var filename_id = button_id.replace( '_button', '_filename' );

        // If the media frame already exists, reopen it.
/*

        if ( file_frame ) {
            console.log('file frane open', file_frame);
            file_frame.open();
            return;
        }
*/


        // Create the media frame.
        file_frame = wp.media.frames.file_frame = wp.media({
            title: jQuery( this ).data( 'uploader_title' ),
            button: {
                text: jQuery( this ).data( 'uploader_button_text' ),
            },
            multiple: false
        });

        // When an file is selected, run a callback.
        file_frame.on( 'select', function() {
            attachment = file_frame.state().get('selection').first().toJSON();
            jQuery("#"+field_id).val(attachment.id);
            if( preview_media ) {
                if(attachment.sizes){
                    // image
                    jQuery("#"+thumb_id).attr('src',attachment.sizes.thumbnail.url);
                }else if(attachment.icon){
                    // other file type
                    jQuery("#"+thumb_id).attr('src',attachment.icon);
                }
                jQuery("#"+filename_id).html(attachment.filename);
                jQuery("#"+preview_id).show();
            }
        });

        // Finally, open the modal
        file_frame.open();
    };

    jQuery('.file_upload_button').click(function() {
        jQuery.fn.uploadMediaFile( jQuery(this), true );
    });

    jQuery('.file_remove_button').click(function() {
        jQuery(this).closest('td').find( '.file_data_field' ).val( '' );
        jQuery(this).closest('td').find( '.file_preview' ).hide();
        return false;
    });



});