/**
 * File Custom.js.
 *
 * Theme custom jquery functionality
 */

( function( $ ) {
	"use strict";
	
	/**
	* Jquery Smart Menu 
	*/
	$('nav #main-menu, #header-navigation ul#main-nav').smartmenus({
		subMenusSubOffsetX: 1,
		subMenusSubOffsetY: -8
	});

	var $mainMenuState = $('#main-menu-state');

	if ($mainMenuState.length) {
		// animate mobile menu
		$mainMenuState.change(function(e) {
			var $menu = $('nav #main-menu, nav #main-nav, .shortlist-align');

			if (this.checked) {
				$menu.hide().slideDown(250).removeClass('add-mobile-menu').addClass('hide-mobile-menu');
			} else {
				$menu.show().slideUp(250).addClass('add-mobile-menu').removeClass('hide-mobile-menu');
			}
		});
		// hide mobile menu beforeunload
		$(window).bind('beforeunload unload', function() {
			if ($mainMenuState[0].checked) {
			$mainMenuState[0].click();
		}
		});
	}

	/**
     * Scroll Top Animation
     */
	$(window).scroll(function() {
		if ($(this).scrollTop() > 100) {
			$('.scrolltop').fadeIn();
		} else {
			$('.scrolltop').fadeOut();
		}
	});
	$('.scrolltop').on('click', function() {
		$("html, body").animate({
		scrollTop: 0
		}, 600);
		return false;
	});
    // Page content based on footer position fixed / not
   function kaya_footer_position(){
   		var header_height = $('#kaya-header-content-wrapper').outerHeight();
   		var menu_height = $('.kaya-page-titlebar-wrapper').outerHeight();
   		var footer_height = $('#kaya-footer-content-wrapper').outerHeight();
	   	if ($('body').height() < $(window).height()){
	   		$('#kaya-mid-content-wrapper').css('height', Math.ceil($(document).height() - (parseInt(header_height) + parseInt(menu_height) + parseInt(footer_height))));
	        $("#kaya-footer-content-wrapper").addClass("footer_bottom_position_fix");
	    }else{
	    	//lert('test2');
	    	$('#kaya-mid-content-wrapper').removeAttr('style');
	        $("#kaya-footer-content-wrapper").removeClass("footer_bottom_position_fix");
	    }
   }
   $(window).load(function(){
   		kaya_footer_position();
   });
   $(window).resize(function(){
   		kaya_footer_position();
   });

   	// Single page Tabs Content 
    $('.single_tabs_content_wrapper, .user_tabs_section_wrapper').each(function() {
        $(".single-page-meta-content-wrapper, .author-page-meta-content-wrapper").hide(); //Hide all content
        $("ul.tabs_content_wrapper li:first").addClass("tab-active").show();
        $(".single-page-meta-content-wrapper:first, .author-page-meta-content-wrapper:first").stop(true, true).fadeIn(0);
        $("ul.tabs_content_wrapper li").click(function() {
        	var $container = $( '.cpt-post-content-wrapper:not(.shortlist-page-wrapper) > ul.masonry, .taxonomy-content-wrapper > ul, .kaya-post-content-wrapper:not(.shortlist-page-wrapper) > ul, .single-page-meta-content-wrapper .gallery,  .user_tabs_section_wrapper .gallery, .ajax-search-results-page > ul, .cpt-post-content-wrapper > ul' );
        	setTimeout(function(){ $container.masonry() }, 1);
            $("ul.tabs_content_wrapper li").removeClass("tab-active");
            $(this).addClass("tab-active");
            $(".single-page-meta-content-wrapper, .author-page-meta-content-wrapper").stop(true, true).fadeOut(0);
            var activeTab = $(this).find("a").attr("href");
            $(activeTab).stop(true, true).fadeIn(800);
            return false;
        });
    });


 	// Advance Search Panel
 
   $('.toggle_search_icon').click(function(){
    $('.toggle_search_wrapper').css({'display':'block'}).animate({  'opacity':'1','right':'0%'}, 400, 'swing', function(){
    });
  });
  $('.toggle_search_wrapper').each(function(){
    $(this).find('span.search_close').click(function(){
       $(this).parent().parent('.toggle_search_wrapper').css({'display':'none'}).animate({'opacity':'0','right':'0%'}, 400, 'swing', function(){           
        });
    });
  });

   /**
    * Woo commerce Related Produts
    */
    $(".related-product-slider").owlCarousel({
        navigation : false,
        autoplay : true,
        stopOnHover : true,
        pagination  : false,
        margin:10,
         items:4,
         responsive: {
          0:{
              items:1,
              },
             400:{
                  items:1,
              },
              480:{
              	 items:2,
              },
              768:{
                  items:3,
                  loop : false,
              },
              1366:{
                  items:4,
                  loop : true,
              },
        }, 
     });

    /**
    * Woo commerce Upsales Produts
    */
   	$(".upsells-product-slider").owlCarousel({
                navigation : false,
                autoplay : true,
                stopOnHover : true,
                pagination  : false,
                responsive: {
				0:{
		        	items:1,
		        },
		        768:{
		            items:2,
		        },
		        1024:{
		            items:3
		        },
			},
              });

} )( jQuery );
