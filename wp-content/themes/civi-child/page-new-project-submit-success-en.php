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
					<?php echo _e("Great! We have received your submission", "civichild"); ?>
				</h2>
				<a class="close" href="https://www.citymody.com/">×</a>
				<span><strong>
					<?php echo _e("We will contact you as soon as our moderators review your submission", "civichild"); ?>
					</strong>
				</span>
					<a class="closeme" href="https://www.citymody.com/" ><?php echo _e("Close", "civichild"); ?></a>
			</div>		
		</div>
	</div>
</div>
		

<script>


</script>

<?php get_footer(); ?>