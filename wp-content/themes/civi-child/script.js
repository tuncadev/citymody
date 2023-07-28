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
	
