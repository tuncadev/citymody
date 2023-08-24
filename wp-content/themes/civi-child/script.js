var seconds = 5; // seconds for HTML
var foo; // variable for clearInterval() function


function updateSecs() {
    document.getElementById("seconds").innerHTML = seconds;
    seconds--;
    if (seconds == -1) {
        clearInterval(foo);
        location.reload();
    }
}

function countdownTimer() {
    foo = setInterval(function () {
        updateSecs()
    }, 1000);
}

function runCountDown() {
	console.log("Runs");
	countdownTimer();
}
	
jQuery('#copied_wrapper').hide();

	function copy(text) {
		jQuery("body").css("overflow", "visible");
		navigator.clipboard.writeText(text);
		jQuery('#copied_wrapper').removeClass('make_absolute');
		jQuery('#copied_wrapper').fadeIn('slow');
		
		jQuery('#copied_wrapper').delay(2000).fadeOut();
		
}