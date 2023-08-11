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
	

		document.querySelectorAll(".acc-more").forEach(el=>{
	const hidden= el.parentElement.querySelectorAll(".hidden");
	el.addEventListener("click", ()=>{
	 hidden.forEach(h=> h.classList.toggle("hidden")) 
	 if (hidden[0].classList.contains("hidden")) {
	 		el.classList.remove("up");
	 		el.classList.add("down");
			el.innerHTML = "<?php echo __("Show more" , "civi-framework"); ?>"; 
		}
		else { 
			el.classList.remove("down");
			el.classList.add("up");
			el.innerHTML = "<?php echo __("Show less" , "civi-framework"); ?>"; 
		}
	});
 });

	