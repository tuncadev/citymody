/**
 * File Custom.js.
 *
 * Theme custom jquery functionality
 */

( function( $ ) {
	"use strict";
  
$(".um-form .um-header").addClass("no-cover");
$(".um-viewing .compcard").addClass("landscape");
$('.um-viewing .compcard').attr('id', 'compcard');

$('.um-viewing .compcard .um-photo img').attr("src","");
/*
  $('.cc_print_button').click(function(){ // before registering the event handler 
    $('.compcard .um-photo a[data-src]').each(function(){ // and for each image with a data-src attribute    	
      $(this).find( "img" ).attr('src', $(this).data('src')) // copy it's contents into the src attribute
    })
  })
*/
var acc = document.getElementsByClassName("um-row-heading");
var i;

for (i = 0; i < acc.length; i++) {
  acc[i].addEventListener("click", function() {
    this.classList.toggle("active");
    var panel = this.nextElementSibling;
    if (panel.style.display === "block") {
      panel.style.display = "none";
    } else {
      panel.style.display = "block";
    }
  });
}


jQuery(document).ready(function() {
    setTimeout(function() {
      $('.um-viewing .compcard .um-photo a[data-src]').each(function(){ // and for each image with a data-src attribute    	
     $(this).find( "img" ).attr('src', $(this).data('src')) // copy it's contents into the src attribute
   //  alert("load ready ");
    })
    }, 3000);
});

setTimeout(function() {
  if ($('.compcard').length > 0) {
    // exists

    $("#campcard_wrap").css("display", "block");

}
}, 5000);

//$(".um-directory").removeClass("uimob9601");

} )( jQuery );
