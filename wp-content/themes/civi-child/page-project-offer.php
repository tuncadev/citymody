<?php get_header(); ?>
<?php 
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if ( is_user_logged_in() && in_array('civi_user_employer', (array)$current_user->roles))  { 
global $candidate_data, $candidate_meta_data;
$candidate_id = isset($_GET['offer']) ? $_GET['offer'] : "";
$candidate_location = get_the_terms($candidate_id, 'candidate_locations');
$candidate_name = get_the_terms($candidate_id, 'candidate_first_name');
$candidate_categories = get_the_terms($candidate_id, 'candidate_categories');
$candidate_resume = wp_get_attachment_url(get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_resume_id_list', true));
$candidate_featured = get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_featured', true);
$candidate_current_position = get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_current_position', true);
$author_id = get_post_field('post_author', $candidate_id);
$candidate_avatar = get_the_author_meta('author_avatar_image_url', $author_id);
$candidate_website = get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_website', true);
$offer_salary = !empty(get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_offer_salary')) ? get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_offer_salary')[0] : '';
$candidate_first_name = get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_first_name', true);
$candidate_last_name = get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_last_name', true);
$candidate_link = get_permalink($candidate_id);
$candidate_skills = get_the_terms($candidate_id, 'candidate_skills');
$today = date("d/m/Y");


	if ($_SERVER['REQUEST_METHOD'] == 'POST') { 
		if( ( $_POST["full_name"] ) && 
				( $_POST["company_name"] ) && 
				( $_POST["company_email"] )  && 
				( $_POST["company_phone_number"] ) && 
				( $_POST["company_web_site"] ) && 
				( $_POST["project_title"] ) && 
			 	( $_POST["project_description"] ) && 
				( $_POST["project_city"] ) && 
				( $_POST["project_start_date"] ) && 
				( $_POST["project_end_date"] ) ) { 
		
		$candidate_name = get_the_terms($candidate_id, 'candidate_first_name');
		$candidate_link = get_permalink($candidate_id);
		
	 	$full_name = strip_tags($_POST["full_name"]);
		$company_name = strip_tags($_POST["company_name"]);
		$company_email = strip_tags($_POST["company_email"]);
		$company_phone_number = preg_replace('/[^0-9]/', '',  $_POST["company_phone_number"]);
		$company_web_site = strip_tags($_POST["company_web_site"]); 
		$project_title = strip_tags($_POST["project_title"]);
		$link_to_project = strip_tags($_POST["link_to_project"]);
		$project_city = strip_tags($_POST["project_city"]);
		$project_description = strip_tags($_POST["project_description"]);
		$hourly_budget = $_POST["hourly_budget_f"];
		$daily_budget = $_POST["daily_budget_f"];
		$project_based_payment = $_POST["project_based_payment_f"];
		$project_start_date = $_POST["project_start_date"];
		$project_end_date = $_POST["project_end_date"];
		$notes = $_POST["notes"];
		
		$post_data = array(
			'post_title'    => $full_name . " - " . $today,
			'post_type'     => 'job-offer',
			'post_status'   => 'publish'
		);
		$post_id = wp_insert_post( $post_data );

		
		update_field( "candidate_id", $candidate_id, $post_id );
		update_field( "full_name", $full_name, $post_id );
		update_field( "company_name", $company_name, $post_id );
		update_field( "company_email", $company_email, $post_id );
		update_field( "company_phone_number", $company_phone_number, $post_id );
		update_field( "company_web_site", $company_web_site, $post_id );	
		update_field( "project_title", $project_title, $post_id );
		update_field( "link_to_project", $link_to_project, $post_id );
		update_field( "project_city", $project_city, $post_id );
		update_field( "project_description", $project_description, $post_id );		
		update_field( "hourly_budget", $hourly_budget, $post_id );
		update_field( "daily_budget", $daily_budget, $post_id );
		update_field( "project_based_payment", $project_based_payment, $post_id );
		update_field( "project_start_date", $project_start_date, $post_id );
		update_field( "project_end_date", $project_end_date, $post_id );
		update_field( "notes", $notes, $post_id );
		/*send admin mail */		
		$to = 'projects@citymody.com, bugrahan.kitapci@citymody.com, tunca.development@gmail.com';
		$subject = 'New Project Offer';
		$body = '
		<p>
			<b>From:</b> ' . $full_name . ' <br />
			<b>Company Name:</b>' .  $company_name . ' <br />
			<b>Company E-Mail:</b>' .  $company_email . ' <br />
			<b>Company Phone:</b> ' .  $company_phone_number . ' <br />
			<b>Company Web Site:</b> ' .  $company_web_site . ' <br />
		</p>
		<p>
			<b>Project Title:</b> ' .  $project_title . ' <br />
			<b>Project City:</b> ' .  $project_city . ' <br />
			<b>Project Link:</b> ' .  $link_to_project . ' <br />
			<b>Description:</b> ' .  $project_description . ' <br />
		</p>
		<p>
			<b>Offered Hourly Rate:</b> '  .  $hourly_budget . ' <br />
			<b>Offered Daily Rate:</b> '  .  $daily_budget . ' <br />
			<b>Offered Project Rate:</b> '  .  $project_based_payment . ' <br />

			<b>Project Start Date:</b> ' .  $project_start_date . ' <br />
			<b>Project End Date:</b> ' .  $project_end_date . ' <br />
		</p>
		<p>
			<b>Notes:</b> ' .  $notes . ' <br />
		</p>
		<p>
			<b>Candidate Name:</b> ' .  $candidate_first_name . ' ' . $candidate_last_name . '<br />
			<b>Candidate Page:</b> ' .  $candidate_link . ' <br />
		</p>
		';
		$headers = array('Content-Type: text/html; charset=UTF-8');

		wp_mail( $to, $subject, $body, $headers );
		echo "<span>Success </span>";
	} else {
		echo "<span>There was an error sending the form. Please try again. </span>";
	}
}
?>
<div class="container post_projects"> 
	<div class="row">
		<div class="post_projects-header" style="flex-direction: column;align-items: center; text-align: center">
			<div>
			<svg xmlns="http://www.w3.org/2000/svg" height="100px" viewBox="0 0 640 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><style>svg{fill:#2786bb}</style><path d="M272.2 64.6l-51.1 51.1c-15.3 4.2-29.5 11.9-41.5 22.5L153 161.9C142.8 171 129.5 176 115.8 176H96V304c20.4 .6 39.8 8.9 54.3 23.4l35.6 35.6 7 7 0 0L219.9 397c6.2 6.2 16.4 6.2 22.6 0c1.7-1.7 3-3.7 3.7-5.8c2.8-7.7 9.3-13.5 17.3-15.3s16.4 .6 22.2 6.5L296.5 393c11.6 11.6 30.4 11.6 41.9 0c5.4-5.4 8.3-12.3 8.6-19.4c.4-8.8 5.6-16.6 13.6-20.4s17.3-3 24.4 2.1c9.4 6.7 22.5 5.8 30.9-2.6c9.4-9.4 9.4-24.6 0-33.9L340.1 243l-35.8 33c-27.3 25.2-69.2 25.6-97 .9c-31.7-28.2-32.4-77.4-1.6-106.5l70.1-66.2C303.2 78.4 339.4 64 377.1 64c36.1 0 71 13.3 97.9 37.2L505.1 128H544h40 40c8.8 0 16 7.2 16 16V352c0 17.7-14.3 32-32 32H576c-11.8 0-22.2-6.4-27.7-16H463.4c-3.4 6.7-7.9 13.1-13.5 18.7c-17.1 17.1-40.8 23.8-63 20.1c-3.6 7.3-8.5 14.1-14.6 20.2c-27.3 27.3-70 30-100.4 8.1c-25.1 20.8-62.5 19.5-86-4.1L159 404l-7-7-35.6-35.6c-5.5-5.5-12.7-8.7-20.4-9.3C96 369.7 81.6 384 64 384H32c-17.7 0-32-14.3-32-32V144c0-8.8 7.2-16 16-16H56 96h19.8c2 0 3.9-.7 5.3-2l26.5-23.6C175.5 77.7 211.4 64 248.7 64H259c4.4 0 8.9 .2 13.2 .6zM544 320V176H496c-5.9 0-11.6-2.2-15.9-6.1l-36.9-32.8c-18.2-16.2-41.7-25.1-66.1-25.1c-25.4 0-49.8 9.7-68.3 27.1l-70.1 66.2c-10.3 9.8-10.1 26.3 .5 35.7c9.3 8.3 23.4 8.1 32.5-.3l71.9-66.4c9.7-9 24.9-8.4 33.9 1.4s8.4 24.9-1.4 33.9l-.8 .8 74.4 74.4c10 10 16.5 22.3 19.4 35.1H544zM64 336a16 16 0 1 0 -32 0 16 16 0 1 0 32 0zm528 16a16 16 0 1 0 0-32 16 16 0 1 0 0 32z"/></svg>
			</div>
			<div>
			<h1 class="p_header" style="text-align: center; padding-bottom: 5px;"><?php echo __("Make an offer for your project", "civichild"); ?></h1>
			</div>
		</div>
		<p style="margin-bottom: 0px;"><?php echo __("You can send us information about the project via the form below. Our managers will contact you shortly.", "civichild"); ?></p>
	</div>
	<div class="row" style="max-width: 900px; margin: auto">
	<div class="block-archive-inner candidate-head-details" style="width: 100%;border: 1px solid #2786bb66;border-radius: 32px;">
		<div>
			<h2 style="font-size: 18 px;line-height: 15px;margin-bottom: 31px;font-weight: 900;color: #2786BB;text-align: center;"><?php echo __("Project Offer For Candidate:", "civichild"); ?></h2>
			<hr>
		</div>
	<div class="civi-candidate-header-top">
        <?php if (!empty($candidate_avatar)) : ?>
            <img class="image-candidates" src="<?php echo esc_attr($candidate_avatar) ?>" alt=""/>
        <?php else : ?>
            <div class="image-candidates"><i class="far fa-camera"></i></div>
        <?php endif; ?>
        <div class="info">
            <div class="title-wapper">
                <?php if (!empty(get_the_title())) : ?>
									
										<h1><?php echo $candidate_first_name . " "; ?></h1>&nbsp;
										
                    <?php if ($candidate_featured == 1) : ?>
                        <span class="tooltip" data-title="<?php echo esc_attr('Featured', 'civi-framework') ?>"><i
                                    class="fas fa-check"></i></span>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <div class="candidate-info">
                <?php if (!empty($candidate_current_position)) { ?>
                    <div class="candidate-current-position">
                        <?php esc_html_e($candidate_current_position); ?>
                    </div>
                <?php } ?>
                <?php if (is_array($candidate_location)) { ?>
                    <div class="candidate-warpper">
                        <svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 384 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><style>svg{fill:#2786bb}</style><path d="M215.7 499.2C267 435 384 279.4 384 192C384 86 298 0 192 0S0 86 0 192c0 87.4 117 243 168.3 307.2c12.3 15.3 35.1 15.3 47.4 0zM192 128a64 64 0 1 1 0 128 64 64 0 1 1 0-128z"/></svg>
                        <?php foreach ($candidate_location as $location) {
                            $cate_link = get_term_link($location, 'candidate_locations'); ?>
                            <div class="cate-warpper">
                                <a href="<?php echo esc_url($cate_link); ?>" class="cate civi-link-bottom">
                                    <?php echo $location->name; ?>
                                </a>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
                <?php if (!empty($offer_salary)) { ?>
                    <div class="candidate-warpper salary">
                        <i class="fal fa-usd-circle"></i>
                        <?php civi_get_salary_candidate($candidate_id); ?>
                    </div>
                <?php } ?>
                <?php // echo civi_get_total_rating('candidate', $candidate_id); ?>
            </div>
        </div>
    </div>
</div>
	</div>
	<div class="project_form_row" style="border: 1px solid #2786bb66;margin-top: 15px;">
		<form action="" class="new_project_offer" id="new_project_offer" name="new_project_offer" method="post">
			<div class="row">
				<div class="form-group col-md-12">
					<h3><?php echo __("Company Details", "civichild"); ?></h3>
				</div>
				<div class="form-group col-md-6">
					 <label for="full_name"><?php echo __("Full Name", "civichild"); ?></label>
					<input type="text" name="full_name" id="full_name" class="point-mark point-active valid" required>
				</div>
				<div class="form-group col-md-6">
				 	<label for="company_name"><?php echo __("Company Name", "civichild"); ?></label>
					<input type="text" name="company_name" id="company_name" class="point-mark point-active valid" required>
				</div>	
				<div class="form-group col-md-6">
					 <label for="company_email"><?php echo __("Company E-mail", "civichild"); ?></label>
					<input type="email" name="company_email" id="company_email" class="point-mark point-active valid" required>
				</div>	
				<div class="form-group col-md-6">
					 <label for="company_phone_number"><?php echo __("Phone Number", "civichild"); ?></label>
					<input type="tel" name="company_phone_number" id="input-telephone" class="form-control point-mark point-active valid" value="" data-mask="(0999) 999 99 99" required placeholder="Örn: (0232) 555 55 55">
				</div>	
				<div class="form-group col-md-6">
					 <label for="company_web_site"><?php echo __("Company Web Site", "civichild"); ?></label>
					<input type="text" name="company_web_site" id="company_web_site" class="point-mark point-active valid" required>
				</div>	
				<div class="form-group col-md-12">
					<hr style="border-top:1px solid #2786BB;">
					<h3><?php echo __("Project Details", "civichild"); ?></h3>
				</div>	
				<div class="form-group col-md-6">
					 <label for="project_title"><?php echo __("Project Title", "civichild"); ?></label>
					<input name="project_title" type="text" id="project_title" class="point-mark point-active valid" placeholder="<?php echo __("Example: Looking for a female hostess for the TV program.", "civichild"); ?>" required>
				</div>
				<div class="form-group col-md-6">
					 <label for="link_to_project"><?php echo __("Link to project (if exists)", "civichild"); ?></label>
					<input name="link_to_project" type="text" id="link_to_project" class="point-mark point-active valid">
				</div>
				<div class="form-group col-md-12">
					 <label for="project_description"><?php echo __("Project Description", "civichild"); ?></label>
					<textarea name="project_description" id="project_description" class="project_description" style="height: 260px; line-height: 27px; padding: 12px 20px;" type="text" placeholder="<?php echo __("Project Day - Requirements - Event Details, Project Rate","civichild"); ?>" required></textarea>
				</div>
				
				<div class="form-group col-md-6">
					<label for="project_city"><?php echo __("City", "civichild"); ?></label>
					<select name="project_city" id="project_city" class="form-control" required>
						<option value=""><?php echo __("Please Select City", "civichild"); ?></option>
					</select>
            	</div>
				<div class="form-group col-md-6">
            	</div>
				<div class="form-group col-md-4" style="margin-top: 15px;">
					<label for="hourly_budget" style="margin-bottom: 0px;"><?php echo __("Hourly Budget", "civichild"); ?></label>
					<input name="hourly_budget" style="margin-bottom: 0px;" type="checkbox" id="hourly_budget" class="point-mark point-active valid">
					<div style="display: none" id="box_hourly">
						<label for="hourly_budget_f" style="font-size: 12px;margin-bottom: 0px;color: #dd0000;"><?php echo __("Please Enter Your Hourly Budget", "civichild"); ?></label>
						<input name="hourly_budget_f" style="margin-bottom: 15px;" type="number" id="hourly_budget_f" class="point-mark point-active valid" placeholder="0" />
					</div>
				</div>
				<div class="form-group col-md-4" style="margin-top: 15px;">
					<label for="daily_budget" style="margin-bottom: 0px;"><?php echo __("Daily Budget", "civichild"); ?></label>
					<input type="checkbox" style="margin-bottom: 0px;" id="daily_budget" name="daily_budget" />
					<div style="display: none" id="box_daily">
						<label for="daily_budget_f" style="font-size: 12px;margin-bottom: 0px;color: #dd0000;"><?php echo __("Please Enter Your Daily Budget", "civichild"); ?></label>
						<input type="number" style="margin-bottom: 15px;" id="daily_budget_f" name="daily_budget_f" placeholder="0" />
					</div> 
				</div> 
				<div class="form-group col-md-4" style="margin-top: 15px;">
					<label for="project_based_payment" style="margin-bottom: 0px;"><?php echo __("Project Based Payment", "civichild"); ?></label>
					<input type="checkbox" style="margin-bottom: 0px;" id="project_based_payment" name="project_based_payment" />
					<div style="display: none" id="box_project">
						<label for="project_based_payment_f" style="font-size: 12px;margin-bottom: 0px;color: #dd0000;"><?php echo __("Please Enter Your Budget for the Project", "civichild"); ?></label>
						<input type="number" style="margin-bottom: 15px;" id="project_based_payment_f" name="project_based_payment_f" placeholder="0" />
					</div> 
				</div> 

	

				<div class="form-group col-md-12" id="hideme" style="margin-bottom: 25px">
				</div>	
				<div class="form-group col-md-6">
					<label for="project_start_date"><?php echo __("Project Start Date", "civichild"); ?></label>
					<input type="date" name="project_start_date" id="project_start_date" required>
				</div>	
				<div class="form-group col-md-6">
					<label for="project_end_date"><?php echo __("Project End Date", "civichild"); ?></label>
					<input type="date" name="project_end_date" id="project_end_date" required>
				</div>	
				<div class="form-group col-md-12">
					 <label for="notes"><?php echo __("Additional Notes", "civichild"); ?></label>
					<textarea name="notes" id="notes" class="notes" style="height: 160px; line-height: 27px; padding: 12px 20px;" type="text" placeholder="<?php echo __("Please add  here if you have any additional notes","civichild"); ?>"></textarea>
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
<script src="<?php echo trailingslashit( get_stylesheet_directory_uri() ) . "/js/getCitiesAjax.js"; ?>"></script>
<script>
	
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
</script>
<?php } else { ?>
<?php wp_redirect( home_url() ); exit; ?>
<?php } ?>
<?php get_footer(); ?>