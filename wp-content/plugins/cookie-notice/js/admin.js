( function( $ ) {

    // ready event
	$( function() {
		// initialize color picker
		$( '.cn_color' ).wpColorPicker();

		// purge cache
		$( '#cn_app_purge_cache a' ).on( 'click', function( e ) {
			e.preventDefault();

			var el = this;

			$( el ).parent().addClass( 'loading' ).append( '<span class="spinner is-active" style="float: none"></span>' );

			var ajaxArgs = {
				action: 'cn_purge_cache',
				nonce: cnArgs.nonce
			};

			// network area?
			if ( cnArgs.network )
				ajaxArgs.cn_network = 1;

			$.ajax( {
				url: cnArgs.ajaxURL,
				type: 'POST',
				dataType: 'json',
				data: ajaxArgs
			} ).always( function( result ) {
				$( el ).parent().find( '.spinner' ).remove();
			} );
		} );

		// global override
		$( 'input[name="cookie_notice_options[global_override]"]' ).on( 'change', function() {
			$( '.cookie-notice-settings form' ).toggleClass( 'cn-options-disabled' );
		} );

		// refuse option
		$( '#cn_refuse_opt' ).on( 'change', function() {
			if ( $( this ).is( ':checked' ) )
				$( '#cn_refuse_opt_container' ).slideDown( 'fast' );
			else
				$( '#cn_refuse_opt_container' ).slideUp( 'fast' );
		} );

		// revoke option
		$( '#cn_revoke_cookies' ).on( 'change', function() {
			if ( $( this ).is( ':checked' ) )
				$( '#cn_revoke_opt_container' ).slideDown( 'fast' );
			else
				$( '#cn_revoke_opt_container' ).slideUp( 'fast' );
		} );

		// privacy policy option
		$( '#cn_see_more' ).on( 'change', function() {
			if ( $( this ).is( ':checked' ) )
				$( '#cn_see_more_opt' ).slideDown( 'fast' );
			else
				$( '#cn_see_more_opt' ).slideUp( 'fast' );
		} );

		// on scroll option
		$( '#cn_on_scroll' ).on( 'change', function() {
			if ( $( this ).is( ':checked' ) )
				$( '#cn_on_scroll_offset' ).slideDown( 'fast' );
			else
				$( '#cn_on_scroll_offset' ).slideUp( 'fast' );
		} );
		
		// conditional display option
		$( '#cn_conditional_display_opt' ).on( 'change', function() {
			if ( $( this ).is( ':checked' ) )
				$( '#cn_conditional_display_opt_container' ).slideDown( 'fast' );
			else
				$( '#cn_conditional_display_opt_container' ).slideUp( 'fast' );
		} );

		// privacy policy link
		$( '#cn_see_more_link-custom, #cn_see_more_link-page' ).on( 'change', function() {
			if ( $( '#cn_see_more_link-custom:checked' ).val() === 'custom' ) {
				$( '#cn_see_more_opt_page' ).slideUp( 'fast', function() {
					$( '#cn_see_more_opt_link' ).slideDown( 'fast' );
				} );
			} else if ( $( '#cn_see_more_link-page:checked' ).val() === 'page' ) {
				$( '#cn_see_more_opt_link' ).slideUp( 'fast', function() {
					$( '#cn_see_more_opt_page' ).slideDown( 'fast' );
				} );
			}
		} );

		// script blocking
		$( '#cn_refuse_code_fields' ).find( 'a' ).on( 'click', function( e ) {
			e.preventDefault();

			$( '#cn_refuse_code_fields' ).find( 'a' ).removeClass( 'nav-tab-active' );
			$( '.refuse-code-tab' ).removeClass( 'active' );

			var id = $( this ).attr( 'id' ).replace( '-tab', '' );

			$( '#' + id ).addClass( 'active' );
			$( this ).addClass( 'nav-tab-active' );
		} );

		// add new group of rules
		$( document ).on( 'click', '.add-rule-group', function( e ) {
			e.preventDefault();

			var html = $( '#rules-group-template' ).html();
			var group = $( '#rules-groups' );
			var groups = group.find( '.rules-group' );
			var groupID = ( groups.length > 0 ? parseInt( groups.last().attr( 'id' ).split( '-' )[2] ) + 1 : 1 );

			html = html.replace( /__GROUP_ID__/g, groupID );
			html = html.replace( /__RULE_ID__/g, 1 );

			group.append( '<div class="rules-group" id="rules-group-' + groupID + '">' + html + '</div>' );
			group.find( '.rules-group' ).last().fadeIn( 'fast' );
		} );

		// remove single rule or group
		$( document ).on( 'click', '.remove-rule', function( e ) {
			e.preventDefault();

			var number = $( this ).closest( 'tbody' ).find( 'tr' ).length;

			if ( number === 1 ) {
				$( this ).closest( '.rules-group' ).fadeOut( 'fast', function() {
					$( this ).remove();
				} );
			} else {
				$( this ).closest( 'tr' ).fadeOut( 'fast', function() {
					$( this ).remove();
				} );
			}
		} );

		// handle changing values for specified type of rules
		$( document ).on( 'change', '.rule-type', function() {
			var el = $( this );
			var td = el.closest( 'tr' ).find( 'td.value' );
			var select = td.find( 'select' );
			var spinner = td.find( '.spinner' );

			select.hide();
			spinner.fadeIn( 'fast' ).css( 'visibility', 'visible' );

			$.post( ajaxurl, {
				action: 'cn-get-group-rules-values',
				cn_param: el.val(),
				cn_nonce: cnArgs.nonceConditional
			} ).done( function( data ) {
				spinner.hide().css( 'visibility', 'hidden' );

				try {
					var response = $.parseJSON( data );

					// replace old select options with new ones
					select.fadeIn( 'fast' ).find( 'option' ).remove().end().append( response.select );
				} catch( e ) {
					//
				}
			} ).fail(function() {
				//
			} );
		} );
    } );

	$( document ).on( 'click', 'input#reset_cookie_notice_options', function() {
		return confirm( cnArgs.resetToDefaults );
	} );

} )( jQuery );