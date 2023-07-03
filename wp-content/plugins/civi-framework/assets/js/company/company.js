var COMPANY = COMPANY || {};
(function ($) {
    "use strict";

    COMPANY = {
        init: function () {
            this.tab_company();
            this.social_company();
            this.read_more();
            this.view_phone_number();
        },

        tab_company: function () {
            function tabcompany(obj) {
                $('.jobs-company-sidebar ul li.tab-item').removeClass('active');
                $(obj).addClass('active');
                var id = $(obj).find('a').attr('href');
                $('.tab-info-company').hide();
                $(id).show();
            }

            $('.tab-company li.tab-item').click(function () {
                tabcompany(this);
                return false;
            });
            tabcompany($('.jobs-company-sidebar ul li.tab-item:first-child'));
        },

        social_company: function () {
            $('body').on('click', '#company-submit-social .soical-remove-inner', function () {
                var wrap = $(this).closest('.clone-wrap');
                $(wrap).find('.field-wrap').slideToggle();
            });
        },

		read_more: function () {
            $('body').on('click', '#tab-sidebar-overview .content a', function (e) {
				e.preventDefault();
                $( this ).parent().addClass( 'is-active' );
				$( this ).remove();
            });
        },
		view_phone_number: function () {
			$( '.company-phone' ).each( function() {
				var phone = $( this ).find( 'a' ).attr( 'data-phone' );
				var text = $( this ).find( 'a' ).text();
				var el = $( this ).find( 'a' );
				var icon = $( this ).find( 'i' );
				var icon_view = 'fa-eye';
				var icon_close = 'fa-eye-slash';

				icon.on( 'click', function() {
					if( el.text() == text ) {
						el.text(phone);
					} else {
						el.text(text);
					}
					if( $( this ).hasClass( icon_view ) ){
						$( this ).removeClass(icon_view);
						$( this ).addClass(icon_close);
					} else {
						$( this ).removeClass(icon_close);
						$( this ).addClass(icon_view);
					}
				});
			});
        },

    };
    $(document).ready(function () {
        COMPANY.init();
    });
})(jQuery);
