/**
 * File Custom.js.
 *
 * Theme custom jquery functionality
 */

( function( $ ) {
  "use strict";

    $(".preload").fadeOut(2000, function() {
        $(".um-viewing .um-profile").fadeIn(1000);        
    });


$(".um-form .um-header").addClass("no-cover");
$(".um-viewing .compcard").addClass("landscape");
$('.um-viewing .compcard').attr('id', 'compcard');

$('.um-viewing .compcard .um-photo img').attr("src","");

//$( ".um-viewing .compcard" ).prev().css("display", "none");

$('a.cc_print_button').click(function(){
           window.print();
           return false;
});

$("div.kaya-um-tabs > div").wrap("<article class='kaya-um-tabs-content'> </article>");

jQuery(document).ready(function() {
    setTimeout(function() {
      $('.um-viewing .compcard .um-photo a[data-src]').each(function(){ // and for each image with a data-src attribute     
     $(this).find( "img" ).attr('src', $(this).data('src')) // copy it's contents into the src attribute
   //  alert("load ready ");
    })
    }, 3000);
});

// THe section deal with profile tabs

  // kay um row outer box generator
  $('.kaya-um-tabs-on .um-profile-body').each(function() {
    if ($(this).find('div.kaya-um-tabs').length !== 0) {
      $(this).addClass('kaya-um-tabs-wrap');

  }else{
    //$(this).addClass('kaya-um-row-wrap');
  }
  });
  


// THe section deal with profile tabs
  $(".um-viewing .kaya-um-tabs-wrap .um-row-heading:first").addClass("kaya_pt_active");
  $(".um-viewing .kaya-um-tabs-wrap .kaya-um-tabs:not(:first)").hide();

  $(".um-viewing .kaya-um-tabs-wrap .um-row-heading").click(function(){
   // $(this).next(".kaya-um-row").slideToggle("high").siblings(".kaya-um-row:visible").slideUp("slow");
     $(this).next(".um-viewing .kaya-um-tabs-wrap .kaya-um-tabs").slideDown("slow").siblings(".um-viewing .kaya-um-tabs-wrap .kaya-um-tabs:visible").slideUp("high");
    $(this).addClass("kaya_pt_active").siblings(".um-viewing  .kaya-um-tabs-wrap .um-row-heading").removeClass("kaya_pt_active");
   // $(this).siblings(".um-viewing  .um-row-heading").removeClass("kaya_pt_active");
 $('html, body').animate({
                    scrollTop: $(".site-content").offset().top
                }, 500);
  });

// profile tabs end
// section for hiding the empty heading and its hiding content section while profile form editing
$(function() {
    $('.um-editing .um-row').each(function() {
        var $this = $(this);
        if($this.height() < 49) {
            $this.addClass('hide-um-row-heading');
            $(this).closest('.um-row').prev('.um-row-heading').addClass('hide-kaya-um-tabs');

        }
    });
});


//$(".um-directory").removeClass("uimob9601");

} )( jQuery );
