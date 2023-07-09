<?php get_header(); ?>
<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
<style>
.site-content {
	z-index: auto;
}
	
</style>
<div class="bg-overlay-success">
	<div class="container post_projects"> 
		<div class="row">
			<div class="cntr-msg">
				<h2>
					<?php echo _e("Great! We have received your submission", "civichild"); ?>
				</h2>
				<a class="close" href="https://www.citymody.com/">×</a>
				<span><strong>
					<?php echo _e("We will contact you as soon as our moderators review your submission", "civichild"); ?>
					</strong>
				</span>
				<p>
					<?php echo _e("You will now be redirected to CityMody Home page in:", "civichild"); ?>
					<br />
					<span id="seconds"></span>
				</p>
			</div>		
		</div>
	</div>
</div>
		

<script>

var seconds = 5; // seconds for HTML
var foo; // variable for clearInterval() function

function redirect() {
	console.log("fuuncton redirect");
		window.location.replace("http://www.w3schools.com");

}

function updateSecs() {
    document.getElementById("seconds").innerHTML = seconds;
    seconds--;
    if (seconds == -1) {
        clearInterval(foo);
				console.log("updateSecs");
        redirect();
    }
}

function countdownTimer() {
    foo = setInterval(function () {
        updateSecs()
    }, 1000);
}

countdownTimer();
</script>

<?php get_footer(); ?>