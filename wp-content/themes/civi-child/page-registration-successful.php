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
<?php 
$id=9480; 
$post = get_post($id); 
$content = apply_filters('the_content', $post->post_content); 
echo $content;  
?>
<div class="bg-overlay-success">
	<div class="container post_projects"> 
		<div class="row">
			<div class="cntr-msg">
				<h2>
					<?php echo _e("Great! You’ve registered! Thank you for registration.", "civichild"); ?>
				</h2>
				<a class="close" href="https://www.citymody.com/candidate-profile/">×</a>
				<span><strong>
					<?php echo _e("Please keep your profile filled and updated.", "civichild"); ?>
					</strong>
				</span>
				<p>
					<?php echo _e("You will now be redirected to your profile page in:", "civichild"); ?>
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
    document.location.href = 'https://www.citymody.com/candidate-profile/';
}

function updateSecs() {
    document.getElementById("seconds").innerHTML = seconds;
    seconds--;
    if (seconds == -1) {
        clearInterval(foo);
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