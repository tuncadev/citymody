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
	
	const checkbox_hourly = document.getElementById('hourly_budget');
	const checkbox_daily = document.getElementById('daily_budget');
	const checkbox_project = document.getElementById('project_based_payment');
	
	const box_hourly = document.getElementById('box_hourly');
	const box_daily = document.getElementById('box_daily');
	const box_project = document.getElementById('box_project');
	
	checkbox_hourly.addEventListener('click', function handleClick() {
		if (checkbox_hourly.checked) {
			box_hourly.style.display = 'block';
		} else {
			box_hourly.style.display = 'none';
		}
	});
	checkbox_daily.addEventListener('click', function handleClick() {
		if (checkbox_daily.checked) {
			box_daily.style.display = 'block';
		} else {
			box_daily.style.display = 'none';
		}
	});
	checkbox_project.addEventListener('click', function handleClick() {
		if (checkbox_project.checked) {
			box_project.style.display = 'block';
		} else {
			box_project.style.display = 'none';
		}
	});