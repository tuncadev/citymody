<?php get_header(); ?>
<?php 
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$taxonomyName = 'candidate_skills';
$taxonomy_terms = get_categories(
	array(
		'taxonomy' => 'candidate_skills',
		'orderby' => 'name',
		'order' => 'ASC',
		'hide_empty' => false,
		'parent' => 0
	)
);

$today = date("d/m/Y");


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	if( ( $_POST["namelast"] ) && 
	   ( $_POST["email"] )  && 
	   ( $_POST["telephone"] ) && 
	   ( $_POST["company_name"] ) && 
	   ( $_POST["project_title"] ) && 
	   ( $_POST["short_desc"] ) && 
	   ( $_POST["cat"] ) && 
	   ( $_POST["skills"] ) && 
	   ( $_POST["il"] ) && 
	   ( $_POST["ilce"] ) && 
	   ( $_POST["gender"] ) && 
	   ( $_POST["age_range"] ) && 
	   ( $_POST["expiration"] ) ) {
		
		 $namelast = strip_tags($_POST["namelast"]);
		 $email = strip_tags($_POST["email"]);
		 $telephone = preg_replace('/[^0-9]/', '',  $_POST["telephone"]);
		 $company_name = strip_tags($_POST["company_name"]);
		 $project_title = strip_tags($_POST["project_title"]);
		 $short_desc = strip_tags($_POST["short_desc"]);
		 $cat = $_POST["cat"];
		 $skills = $_POST["skills"];
		 $il = $_POST["il"];
		 $ilce = $_POST["ilce"];
		 $gender = $_POST["gender"];
		 $age_range = $_POST["age_range"];
		 $expiration = $_POST["expiration"];

		$post_data = array(
			'post_title'    => $namelast . " - " . $today,
			'post_type'     => 'project',
			'post_status'   => 'publish'
		);
		$post_id = wp_insert_post( $post_data );
		$form_id = $post_id . " - " . $namelast;
		// Save a basic text value.
		update_field( "form_id", $post_id, $post_id );
		update_field( "namelast", $namelast, $post_id );
		update_field( "email", $email, $post_id );
		update_field( "telephone", $telephone, $post_id );
		update_field( "company_name", $company_name, $post_id );
		update_field( "project_title", $project_title, $post_id );
		update_field( "short_desc", $short_desc, $post_id );
		update_field( "cat", $cat, $post_id );
		update_field( "skills", $skills, $post_id );
		update_field( "il", $il, $post_id );
		update_field( "ilce", $ilce, $post_id );
		update_field( "gender", $gender, $post_id );
		update_field( "age_range", $age_range, $post_id );
		update_field( "expiration", $expiration, $post_id );	
		/*send admin mail */
		$to = 'projects@citymody.com, bugrahan.kitapci@citymody.com, tunca.development@gmail.com';
		$subject = 'New Project Request';
		$body = '
		<p>
		<b>From:</b> ' . $namelast . ' <br />
		<b>E-Mail:</b>' .  $email . ' <br />
		<b>Phone:</b> ' .  $telephone . ' <br />
		<b>Company Name:</b> ' .  $company_name . ' <br />
		</p>
		<p>
		<b>Project Title:</b> ' .  $project_title . ' <br />
		<b>Short Description:</b> ' .  $short_desc . ' <br />
		</p>
		<p>
		<b>Category:</b> ' .  $cat . ' <br />
		<b>Skill:</b> ' .  $skills . ' <br />
		<b>City:</b> ' .  $il . ' <br />
		<b>Provience:</b> ' .  $ilce . ' <br />
		<b>Gender:</b> ' .  $gender . ' <br />
		<b>Age Range:</b> ' .  $age_range . ' <br />
		<b>Expiration:</b> ' .  $expiration . ' <br />
		</p>
		';
		$headers = array('Content-Type: text/html; charset=UTF-8');

		wp_mail( $to, $subject, $body, $headers );
		header("Location:https://www.citymody.com/new-project-submit-success/");
	} else {
		echo "<span>There was an error sending the form. Please try again. </span>";
	}
}
?>
<div class="container post_projects"> 
	<div class="row">
		<div class="post_projects-header">
			<img src="https://www.citymody.com/wp-content/uploads/2023/06/free.svg" alt="Free Project Post">
			<h1 class="p_header"><?php echo __("Post a project to find the best talent for your next project.", "civichild"); ?></h1>
		</div>
		<p><?php echo __("Posting a project announcement is absolutely free. We do not charge any commissions or fees.", "civichild"); ?></p>
	</div>`
	<div class="project_form_row">
		<form action="" class="new_project" id="new_project" name="new_project" method="post">
			<div class="row">
				<div class="form-group col-md-6">
					 <label for="namelast"><?php echo __("Full Name", "civichild"); ?></label>
					<input type="text" name="namelast" id="namelast" class="point-mark point-active valid" required>
				</div>
				<div class="form-group col-md-6">
					 <label for="email"><?php echo __("Company E-mail", "civichild"); ?></label>
					<input type="email" name="email" id="user_email" class="point-mark point-active valid" required>
				</div>	
				<div class="form-group col-md-6">
					 <label for="telephone"><?php echo __("Phone Number", "civichild"); ?></label>
					<input type="tel" name="telephone" id="input-telephone" class="form-control point-mark point-active valid" value="" data-mask="(0999) 999 99 99" required placeholder="Örn: (0232) 555 55 55">
				</div>	
				<div class="form-group col-md-6">
					 <label for="company_name"><?php echo __("Company Name", "civichild"); ?></label>
					<input type="text" name="company_name" id="company_name" class="point-mark point-active valid" required>
				</div>	
				<div class="form-group col-md-12">
					 <label for="project_title"><?php echo __("Project Title", "civichild"); ?></label>
					<input name="project_title" type="text" id="project_title" class="point-mark point-active valid" placeholder="<?php echo __("Example: Looking for a female hostess for the TV program.", "civichild"); ?>" required>
				</div>
				<div class="form-group col-md-12">
					 <label for="short_desc"><?php echo __("Short Description", "civichild"); ?></label>
					<textarea name="short_desc" id="short_desc" class="short_desc" style="height: 260px; line-height: 27px; padding: 12px 20px;" type="text" placeholder="<?php echo __("Project Day - Requirements - Event Details","civichild"); ?>" required></textarea>
				</div>
				<div class="form-group col-md-6">
			 		<label for="cat"><?php echo __("Project Category", "civichild"); ?></label>
					<select name="cat" id="cat" required>
						<option value=""><?php echo __("Please Select Category", "civichild"); ?></option>
						<?php
							foreach ($taxonomy_terms as $term) {			
								echo '<option id="' . $term->term_id . '"  value="' . $term->name . '">' .  $term->name . '</option>';				
							}
						 ?>
					</select>
				</div>
				<div class="form-group col-md-6">
					<label for="skills"><?php echo __("Skill Category", "civichild"); ?></label>
					<select name="skills" id="skills" required style="display:none;">			
						<option value=""><?php echo __("Please Select Category First", "civichild"); ?></option>
					<?php
						$terms = get_terms( $taxonomyName, array( 'orderby' => 'slug', 'hide_empty' => false ) );
						foreach ($terms as $child_term) {			
							echo '<option  value="' . $child_term->name . '" class="'. $child_term->parent .'">' .  $child_term->name . '</option>';			
						}
					?>
					</select>
				</div>
	
				<div class="form-group col-md-6">
					<label for="il"><?php echo __("City", "civichild"); ?></label>
					<select name="il" id="il" class="form-control" required>
						<option value=""><?php echo __("Please Select City", "civichild"); ?></option>
					</select>
            	</div>

				<div class="form-group col-md-6">
					<label for="ilce"><?php echo __("Provience", "civichild"); ?></label>
					<select name="ilce" id="ilce" class="form-control" disabled="disabled" required>
						<option value=""><?php echo __("Please Select Provience", "civichild"); ?></option>
					</select>
				</div>
				<div class="form-group col-md-6">
					<label for="gender"><?php echo __("Gender", "civichild"); ?></label>
					<select name="gender" id="gender" class="form-control" required>
						<option value=""><?php echo __("Please Select Gender", "civichild"); ?></option>
						<option value="female"><?php echo __("Female", "civichild"); ?></option>
						<option value="male"><?php echo __("Male", "civichild"); ?></option>
						<option value="male"><?php echo __("Does not matter", "civichild"); ?></option>
					</select>
				</div>
				<div class="form-group col-md-3">
					<label for="age_range"><?php echo __("Age Range", "civichild"); ?></label>
					<select name="age_range" id="age_range" class="form-control" required>
						<option value=""><?php echo __("Please Select Age Range", "civichild"); ?></option>
						<option value="18-25"><?php echo __("18-25", "civichild"); ?></option>
						<option value="25-30"><?php echo __("25-30", "civichild"); ?></option>
						<option value="30-35"><?php echo __("30-35", "civichild"); ?></option>
						<option value="35-40"><?php echo __("35-40", "civichild"); ?></option>
						<option value="40plus"><?php echo __("40+", "civichild"); ?></option>
					</select>
				</div>
				<div class="form-group col-md-3"></div>
				<div class="form-group col-md-6">
					<label for="expiration"><?php echo __("Expiration Date of Project", "civichild"); ?></label>
					<input type="date" name="expiration" id="expiration">
				</div>	
			
				<div class="form-group col-md-9"></div>
				<div class="form-group col-md-3 civi-mailchimp-form-style-01">
					<input type="submit" value="<?php echo __("Send", "civichild"); ?>">
				</div>
			</div>	
		</form>
	</div>
</div>
<script src="//cdnjs.cloudflare.com/ajax/libs/jasny-bootstrap/3.1.3/js/jasny-bootstrap.min.js"></script>
<script>
var skills = jQuery("[name=skills] option").detach()
jQuery("[name=cat]").change(function() {
	jQuery("#skills").show()
  var val = jQuery(this).children(":selected").attr("id");
  jQuery("[name=skills] option").detach()
  skills.filter("." + val).clone().appendTo("[name=skills]")}).change();	
</script>
<script src="<?php echo trailingslashit( get_stylesheet_directory_uri() ) . "/js/getCitiesAjax.js"; ?>"></script>
<?php get_footer(); ?>