( function( $ ) {
	$( function() {
		var mediaUploader;

		/**
		 * Logic to add an attachment.
		 */
		$( this ).on( 'click', '.frm_email_add_attachment', function( event ) {
			event.preventDefault();

			/**
			 * Add attachment button.
			 */
			var button = $( this );

			/**
			 * Closest parent to the add button.
			 */
			var container = button.closest( '.frm_email_add_attachment_container' );

			if ( mediaUploader ) {
				mediaUploader.open();
				return;
			}

			mediaUploader = wp.media.frames.file_frame = wp.media({
				multiple: false
			});

			mediaUploader.on( 'select', function() {
				var icon,
					attachment = mediaUploader.state().get( 'selection' ).first().toJSON();

				/**
				 * Add file attachment ID to the hidden field.
				 */
				container.find( '.frm_email_attachment' ).val( attachment.id );

				/**
				 * Display the filename of the selected attachment.
				 */
				container.find( '.frm_email_attachment_name' ).text( attachment.filename );

				/**
				 * Add the image or file icon.
				 */
				icon = attachment.icon;
				if ( typeof attachment.sizes !== 'undefined' ) {
					icon = attachment.sizes.thumbnail.url;
				}
				container.find( '.frm_email_attachment_icon' ).html( '<img src="' + icon + '" class="frm_image_preview" />' );

				button.addClass( 'frm_hidden' );
				$( '.frm_email_remove_attachment' ).removeClass( 'frm_hidden' );
			});

			mediaUploader.open();
		});

		/**
		 * Logic to remove an attachment.
		 */
		$( this ).on( 'click', '.frm_email_remove_attachment', function( event ) {
			event.preventDefault();

			/**
			 * Remove attachment button.
			 */
			var button = $( this );

			/**
			 * Closest parent to the remove button.
			 */
			var container = button.closest( '.frm_email_add_attachment_container' );

			container.find( '.frm_email_attachment' ).val( '' );
			container.find( '.frm_email_attachment_name' ).text( '' );
			container.find( '.frm_email_attachment_icon' ).html( '' );
			button.addClass( 'frm_hidden' );
			$( '.frm_email_add_attachment' ).removeClass( 'frm_hidden' );
		});
	});
}( jQuery ) );
