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
		navigator.clipboard.writeText(text);
		jQuery('#copied_wrapper').removeClass('make_absolute');
		jQuery('#copied_wrapper').fadeIn('slow');
		jQuery('#copied_wrapper').delay(2000).fadeOut();
}

function pop_up(url){
	window.open(url,'win2','status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=1076,height=768,directories=no,location=no') 
	}
	
	

	const civi_user_candidate = document.getElementById('civi_user_candidate');
	const civi_user_employer = document.getElementById('civi_user_employer');
	const reg_warn_lbl = document.getElementById('reg_warn_lbl');

	civi_user_candidate.addEventListener('click', function handleClick() { 
		
		if (civi_user_candidate.checked) {
			reg_warn_lbl.style.display = 'block';
		} else {
			reg_warn_lbl.style.display = 'none';
		}
	});

	civi_user_employer.addEventListener('click', function handleClick() { 
		
		if (civi_user_employer.checked) {
			reg_warn_lbl.style.display = 'none';
		} else {
			reg_warn_lbl.style.display = 'block';
		}
	});


