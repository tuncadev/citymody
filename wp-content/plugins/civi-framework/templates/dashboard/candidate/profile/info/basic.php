<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $hide_candidate_fields, $candidate_data, $candidate_meta_data, $current_user;
$user_id = $current_user->ID;

$current_user = wp_get_current_user();
$package_id = get_the_author_meta(CIVI_METABOX_PREFIX . 'package_id', $user_id);
$package_unlimited_job = get_post_meta($package_id, CIVI_METABOX_PREFIX . 'package_unlimited_job', true);
$package_unlimited_featured_job = get_post_meta($package_id, CIVI_METABOX_PREFIX . 'package_unlimited_job_featured', true);
$package_num_job = get_user_meta($user_id, CIVI_METABOX_PREFIX . 'package_number_job', true);
$package_num_featured_job = get_user_meta($user_id, CIVI_METABOX_PREFIX . 'package_number_featured', true);
$package_activate_date = get_user_meta($user_id, CIVI_METABOX_PREFIX . 'package_activate_date', true);
$package_time_unit = get_post_meta($package_id, CIVI_METABOX_PREFIX . 'package_time_unit', true);
$package_period = get_post_meta($package_id, CIVI_METABOX_PREFIX . 'package_period', true);
$package_unlimited_time = get_post_meta($package_id, CIVI_METABOX_PREFIX . 'package_unlimited_time', true);
$package_name = get_the_title($package_id);
$user_info = get_userdata($user_id);
$civi_package = new Civi_Package();
$expired_date = $civi_package->get_expired_date($package_id, $user_id);
$paid_submission_type = civi_get_option('paid_submission_type', 'no');

$current_date = date('Y-m-d');
if ($current_date < $expired_date) {
    $seconds = strtotime($expired_date) - strtotime($current_date);
    $dtF = new \DateTime('@0');
    $dtT = new \DateTime("@$seconds");
    $expired_jobs = $dtF->diff($dtT)->format('%a');
} else {
    $expired_jobs = 0;
}




$candidate_id = civi_get_post_id_candidate();
$candidate_des = $candidate_data->post_content;
$candidate_first_name = isset($candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_first_name']) ? $candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_first_name'][0] : '';
$candidate_last_name = isset($candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_last_name']) ? $candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_last_name'][0] : '';
$candidate_email = isset($candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_email']) ? $candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_email'][0] : '';
$candidate_phone = isset($candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_phone']) ? $candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_phone'][0] : '';
$candidate_current_position = isset($candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_current_position']) ? $candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_current_position'][0] : '';

/* Physical */

$candidate_height = isset($candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_height']) ? $candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_height'][0] : '';
$candidate_weight = isset($candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_weight']) ? $candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_weight'][0] : '';
$candidate_haircolor = isset($candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_hair-color']) ? $candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_haircolor'][0] : '';
$candidate_hairtype = isset($candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_hair-type']) ? $candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_hairtype'][0] : '';
$candidate_eyecolor = isset($candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_eye-color']) ? $candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_eyecolor'][0] : '';
$candidate_skincolor = isset($candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_skin-color']) ? $candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_skincolor'][0] : '';
$candidate_chestsize = isset($candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_chest-size']) ? $candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_chestsize'][0] : '';
$candidate_waistsize = isset($candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_waist-size']) ? $candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_waistsize'][0] : '';
$candidate_hipsize = isset($candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_hip-size']) ? $candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_hip-size'][0] : '';
$candidate_bodytype = isset($candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_body-type']) ? $candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_bodytype'][0] : '';
$candidate_footsize = isset($candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_footsize']) ? $candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_footsize'][0] : '';

/********* */
$candidate_categories = isset($candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_categories']) ? $candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_categories'][0] : '';
$candidate_dob = isset($candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_dob']) ? $candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_dob'][0] : '';
$candidate_age = isset($candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_age']) ? $candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_age'][0] : '';
$candidate_gender = isset($candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_gender']) ? $candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_gender'][0] : '';
$candidate_languages = isset($candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_languages']) ? $candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_languages'][0] : '';
$candidate_qualification = isset($candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_qualification']) ? $candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_qualification'][0] : '';
$candidate_yoe = isset($candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_yoe']) ? $candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_yoe'][0] : '';
$candidate_salary_type = isset($candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_salary_type']) ? $candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_salary_type'][0] : '';
$candidate_offer_salary = isset($candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_offer_salary']) ? $candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_offer_salary'][0] : '';
$candidate_show_my_profile = isset($candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_show_my_profile']) ? $candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_show_my_profile'][0] : '';
/****************** */
$candidate_skills = get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_skills', false);
$candidate_skills = !empty($candidate_skills) ?  $candidate_skills[0] : '';
$taxonomyName = "candidate_skills";

/************************** */
$candidate_avatar_id = $user_id;
$candidate_avatar_url = get_the_author_meta('author_avatar_image_url', $user_id);
$candidate_cover_image_id = get_post_thumbnail_id($candidate_data->ID);
$candidate_cover_image_url = get_the_post_thumbnail_url($candidate_data->ID, 'full');
$image_max_file_size = civi_get_option('civi_image_max_file_size', '1000kb');
civi_get_thumbnail_enqueue();
civi_get_avatar_enqueue();

$google_gmail = get_user_meta($user_id, CIVI_METABOX_PREFIX . 'user-google-email', true);
if (!empty($google_gmail)) {
    $candidate_email = $google_gmail;
} else {
    $candidate_email = isset($candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_email']) ? $candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_email'][0] : '';
}
	$avatar_bg = $candidate_avatar_url == "" ? "avatar_bg" : "";
?>

<div class="candidate basic-info block-from">


    <h6><?php esc_html_e('Basic Information', 'civi-framework'); ?></h6>

    <input type="hidden" name="candidate_id" value="<?php echo esc_attr($candidate_id) ?>">

    <div class="civi-avatar-candidate">

        <?php if (!in_array('fields_candidate_avatar', $hide_candidate_fields)) : ?>
            <div class="candidate-fields-avatar civi-fields-avatar">
                <label><?php esc_html_e('Your photo', 'civi-framework'); ?></label>
							<div class="no_selfie-wrapper">
				<div class="no_selfie">
					<span><?php esc_html_e("Please avoid using bad quailty selfie pictures for your profile photo. This may result in being removed from Talent List", "civi-framework"); ?></span>
				</div>
			</div>
                <div class="form-field">
                    <div id="civi_avatar_errors" class="errors-log"></div>
					<div class="avatar-container">
						<div id="civi_avatar_container" class="file-upload-block preview">
							<div id="civi_avatar_view" data-image-id="<?php echo $candidate_avatar_id; ?>" data-image-url="<?php if (!empty($candidate_avatar_url)) {
																																echo $candidate_avatar_url;
																															} ?>"></div>
							<div id="civi_add_avatar">
								<i class="far fa-arrow-from-bottom large"></i>
								<p id="civi_drop_avatar">
									<button type="button" id="civi_select_avatar"><?php esc_html_e('Upload', 'civi-framework') ?></button>
								</p>
							</div>
							<input type="hidden" class="avatar_url form-control" name="author_avatar_image_url" value="<?php echo $candidate_avatar_url; ?>" id="avatar_url">
							<input type="hidden" class="avatar_id" name="author_avatar_image_id" value="<?php echo $candidate_avatar_id; ?>" id="avatar_id" />
						</div>
						<div class="no_selfie-container <?php echo $avatar_bg; ?>" id="no-selfie">

						</div>
					</div>
                </div>
                <div class="field-note"><?php echo sprintf(__('Maximum file size: %s.', 'civi-framework'), $image_max_file_size); ?></div>
            </div>

			
        <?php endif; ?>

        <?php if (!in_array('fields_candidate_thumbnail', $hide_candidate_fields)) : ?>
            <div class="candidate-fields-thumbnail civi-fields-thumbnail">
                <label><?php esc_html_e('Cover image', 'civi-framework'); ?></label>
                <div class="form-field">
                    <div id="civi_thumbnail_errors" class="errors-log"></div>
                    <div id="civi_thumbnail_container" class="file-upload-block preview">
                        <div id="civi_thumbnail_view" data-image-id="<?php echo $candidate_cover_image_id; ?>" data-image-url="<?php if (!empty($candidate_cover_image_url)) {
                                                                                                                                    echo $candidate_cover_image_url;
                                                                                                                                } ?>"></div>
                        <div id="civi_add_thumbnail">
                            <i class="far fa-arrow-from-bottom large"></i>
                            <p id="civi_drop_thumbnail">
                                <button type="button" id="civi_select_thumbnail"><?php esc_html_e('Click here', 'civi-framework') ?></button>
                                <?php esc_html_e(' or drop files to upload', 'civi-framework') ?>
                            </p>
                        </div>
                        <input type="hidden" class="thumbnail_url form-control" name="candidate_cover_image_url" value="<?php echo $candidate_cover_image_url; ?>" id="thumbnail_url">
                        <input type="hidden" class="thumbnail_id" name="candidate_cover_image_id" value="<?php echo $candidate_cover_image_id; ?>" id="thumbnail_id" />
                    </div>
                </div>
                <p class="civi-thumbnail-size"><?php esc_html_e('The cover image size should be max 1920 x 400px', 'civi-framework') ?></p>
            </div>
        <?php endif; ?>
    </div>


    <div class="row">
        <?php if (!in_array('fields_candidate_first_name', $hide_candidate_fields)) : ?>
            <div class="form-group col-md-6">
                <label for="candidate_first_name"><?php esc_html_e('First name', 'civi-framework') ?></label>
                <input class="point-mark" type="text" id="user_firstname" name="candidate_first_name" placeholder="<?php esc_attr_e('First name', 'civi-framework') ?>" value="<?php echo esc_attr($candidate_first_name); ?>" required>
            </div>
        <?php endif; ?>
        <?php if (!in_array('fields_candidate_last_name', $hide_candidate_fields)) : ?>
            <div class="form-group col-md-6">
                <label for="candidate_last_name"><?php esc_html_e('Last name', 'civi-framework') ?></label>
                <input class="point-mark" type="text" id="user_lastname" name="candidate_last_name" placeholder="<?php esc_attr_e('Last name', 'civi-framework') ?>" value="<?php echo esc_attr($candidate_last_name); ?>" required>
            </div>
        <?php endif; ?>
        <?php if (!in_array('fields_candidate_email_address', $hide_candidate_fields)) : ?>
            <div class="form-group col-md-6">
                <label for="candidate_email"><?php esc_html_e('Email address', 'civi-framework') ?></label>
                <input class="point-mark" type="email" id="user_email" name="candidate_email" placeholder="<?php esc_attr_e('Email', 'civi-framework') ?>" value="<?php echo esc_attr($candidate_email); ?>" required>
            </div>
        <?php endif; ?>
        <?php if (!in_array('fields_candidate_phone_number', $hide_candidate_fields)) : ?>
            <div class="form-group col-md-6">
                <label for="candidate_phone"><?php esc_html_e('Phone number', 'civi-framework') ?></label>
                <input class="point-mark" type="number" id="author_mobile_number" name="candidate_phone" value="<?php echo esc_attr($candidate_phone); ?>" placeholder="<?php esc_attr_e('Phone', 'civi-framework'); ?>" required>
            </div>
        <?php endif; ?>
        <?php if (!in_array('fields_candidate_current_position', $hide_candidate_fields)) : ?>
            <div class="form-group col-md-6">
                <label for="candidate_current_position"><?php esc_html_e('Current Position', 'civi-framework') ?></label>
                <input class="point-mark" type="text" id="candidate_current_position" name="candidate_current_position" value="<?php echo esc_attr($candidate_current_position); ?>" placeholder="<?php esc_attr_e('Ex: UI/UX Designer', 'civi-framework'); ?>">
            </div>
        <?php endif; ?>
        <?php if (!in_array('fields_candidate_categories', $hide_candidate_fields)) : ?>
            <div class="form-group col-md-6">
							<label for="candidate_categories"><?php esc_html_e('Categories', 'civi-framework') ?></label>
							<div class="select2-field">
								<select class="point-mark civi-select2" name="candidate_categories" id="candidate_categories" required onChange="ShowAudioDiv()">
									<?php civi_get_taxonomy_by_post_id($candidate_id, 'candidate_categories', true); ?>
								</select>
							</div>
            </div>
						<div class="form-group col-md-12">

								<div class="skills-info block-from">
									<h5><?php esc_html_e('Skills', 'civi-framework') ?></h5>
									<div class="sub-head"><?php esc_html_e('We recommend at least one skill entry', 'civi-framework') ?></div>
									<div class="row">
									<div class="form-group col-md-12">
											<label for="candidate_skills"><?php esc_html_e('Select Skills', 'civi-framework') ?></label>
											<select class="civi-select2 point-mark" name="candidate_skills" id="candidate_skills" multiple required	>
												<?php list_skill_options($candidate_id, $taxonomyName); ?>
											</select>
											</div>
									</div>
								</div>

            </div>
        <?php endif; ?>
        <?php if (!in_array('fields_candidate_description', $hide_candidate_fields)) : ?>
            <div class="form-group col-md-12">
                <label for="candidate_des"><?php esc_html_e('Description', 'civi-framework') ?></label>
                <?php
                $content = $candidate_des;
                $editor_id = 'candidate_des';
                $settings = array(
                    'wpautop' => true,
                    'media_buttons' => false,
                    'textarea_name' => $editor_id,
                    'textarea_rows' => get_option('default_post_edit_rows', 8),
                    'tabindex' => '',
                    'editor_css' => '',
                    'editor_class' => '',
                    'teeny' => false,
                    'dfw' => false,
                    'tinymce' => true,
                    'quicktags' => true
                );
                wp_editor(html_entity_decode(stripcslashes($content)), $editor_id, $settings);
                ?>
            </div>
        <?php endif; ?>
        <?php if (!in_array('fields_candidate_date_of_birth', $hide_candidate_fields)) : ?>
            <div class="form-group col-md-6">
                <label for="candidate_dob"><?php esc_html_e('Date of Birth', 'civi-framework') ?></label>
                <input class="point-mark" type="date" id="candidate_dob" name="candidate_dob" value="<?php echo esc_attr($candidate_dob); ?>">
            </div>
        <?php endif; ?>
        <?php if (!in_array('fields_candidate_age', $hide_candidate_fields)) : ?>
            <div class="form-group col-md-6">
                <label for="candidate_age"><?php esc_html_e('Age', 'civi-framework') ?></label>
                <div class="select2-field">
					<select class="point-mark civi-select2" name="candidate_age" id="candidate_age">
						<?php civi_get_taxonomy_by_post_id($candidate_id, 'candidate_ages', true); ?>
					</select>
				</div>
            </div>
        <?php endif; ?>
        <?php if (!in_array('fields_candidate_gender', $hide_candidate_fields)) : ?>
            <div class="form-group col-md-6">
                <label for="candidate_gender"><?php esc_html_e('Gender', 'civi-framework') ?></label>
                <div class="select2-field">
					<select class="point-mark civi-select2" name="candidate_gender" id="candidate_gender">
						<option <?php if ($candidate_gender == "") {
									echo 'selected';
								} ?> value=""><?php esc_attr_e('Select an option', 'civi-framework'); ?></option>
						<option <?php if ($candidate_gender == "female") {
									echo 'selected';
								} ?> value="female"><?php esc_html_e('Female', 'civi-framework'); ?></option>
						<option <?php if ($candidate_gender == "male") {
									echo 'selected';
								} ?> value="male"><?php esc_html_e('Male', 'civi-framework'); ?></option>
					</select>
				</div>
            </div>
        <?php endif; ?>
        <?php if (!in_array('fields_closing_languages', $hide_candidate_fields)) : ?>
            <div class="form-group col-md-6">
                <label for="candidate_languages"><?php esc_html_e('Native Language', 'civi-framework') ?></label>
                <div class="select2-field">
					<select class="civi-select2 point-mark" name="candidate_languages" id="candidate_languages"  >
						<?php civi_get_taxonomy_by_post_id($candidate_id, 'candidate_languages', true); ?>
					</select>
				</div>
            </div>
        <?php endif; ?>
        <?php if (!in_array('fields_candidate_qualification', $hide_candidate_fields)) : ?>
            <div class="form-group col-md-6">
                <label for="candidate_qualification"><?php esc_html_e('Qualification', 'civi-framework') ?></label>
                <div class="select2-field">
					<select class="point-mark civi-select2" name="candidate_qualification" id="candidate_qualification">
						<?php civi_get_taxonomy_by_post_id($candidate_id, 'candidate_qualification', true); ?>
					</select>
				</div>
            </div>
        <?php endif; ?>
        <?php if (!in_array('fields_candidate_experience', $hide_candidate_fields)) : ?>
            <div class="form-group col-md-6">
                <label for="candidate_yoe"><?php esc_html_e('Years of Experience', 'civi-framework') ?></label>
                <div class="select2-field">
					<select class="point-mark civi-select2" name="candidate_yoe" id="candidate_yoe">
						<?php civi_get_taxonomy_by_post_id($candidate_id, 'candidate_yoe', true); ?>
					</select>
				</div>
            </div>
        <?php endif; ?>
		
		
		<?php /* Physical Atttr */ ?>
		
			<div class="form-group col-md-12" style="margin-bottom: 0px; border-top: 1px solid #ccc; padding-top: 24px;">
				<h6><?php esc_html_e('Physical Attributes', 'civi-framework') ?></h6>
			</div>		
		<?php if (!in_array('fields_candidate_height', $hide_candidate_fields)) : ?>
			<div class="form-group col-md-6">
					  <label for="candidate_height"><?php esc_html_e('Height (m)', 'civi-framework') ?></label>
				<div class="select2-field">
					<select class="point-mark civi-select2" name="candidate_height" id="candidate_height">
						<?php civi_get_taxonomy_by_post_id($candidate_id, 'candidate_height', true); ?>
					</select>
				</div>
			</div>
		<?php endif; ?>
		<?php if (!in_array('fields_candidate_weight', $hide_candidate_fields)) : ?>
			<div class="form-group col-md-6">
					  <label for="candidate_weight"><?php esc_html_e('Weight (kg)', 'civi-framework') ?></label>
				<div class="select2-field">
					<select class="point-mark civi-select2" name="candidate_weight" id="candidate_weight">
						<?php civi_get_taxonomy_by_post_id($candidate_id, 'candidate_weight', true); ?>
					</select>
				</div>
			</div>
		<?php endif; ?>
		<?php if (!in_array('fields_candidate_footsize', $hide_candidate_fields)) : ?>
			<div class="form-group col-md-6">
					  <label for="candidate_footsize"><?php esc_html_e('Foot Size (EU)', 'civi-framework') ?></label>
				<div class="select2-field">
					<select class="point-mark civi-select2" name="candidate_footsize" id="candidate_footsize">
						<?php civi_get_taxonomy_by_post_id($candidate_id, 'candidate_footsize', true); ?>
					</select>
				</div>
			</div>
		<?php endif; ?>
		
		<?php if (!in_array('fields_candidate_haircolor', $hide_candidate_fields)) : ?>
			<div class="form-group col-md-6">
					  <label for="candidate_haircolor"><?php esc_html_e('Hair Color', 'civi-framework') ?></label>
				<div class="select2-field">
					<select class="point-mark civi-select2" name="candidate_haircolor" id="candidate_haircolor">
						<?php civi_get_taxonomy_by_post_id($candidate_id, 'candidate_hair-color', true); ?>
					</select>
				</div>
			</div>
		<?php endif; ?>
		<?php if (!in_array('fields_candidate_hairtype', $hide_candidate_fields)) : ?>
			<div class="form-group col-md-6">
					  <label for="candidate_hairtype"><?php esc_html_e('Hair Type', 'civi-framework') ?></label>
				<div class="select2-field">
					<select class="point-mark civi-select2" name="candidate_hairtype" id="candidate_hairtype">
						<?php civi_get_taxonomy_by_post_id($candidate_id, 'candidate_hair-type', true); ?>
					</select>
				</div>
			</div>
		<?php endif; ?>
		<?php if (!in_array('fields_candidate_eyecolor', $hide_candidate_fields)) : ?>
			<div class="form-group col-md-6">
					  <label for="candidate_eyecolor"><?php esc_html_e('Eye Color', 'civi-framework') ?></label>
				<div class="select2-field">
					<select class="point-mark civi-select2" name="candidate_eyecolor" id="candidate_eyecolor">
						<?php civi_get_taxonomy_by_post_id($candidate_id, 'candidate_eye-color', true); ?>
					</select>
				</div>
			</div>
		<?php endif; ?>
		<?php if (!in_array('fields_candidate_skincolor', $hide_candidate_fields)) : ?>
			<div class="form-group col-md-6">
					  <label for="candidate_skincolor"><?php esc_html_e('Skin Color', 'civi-framework') ?></label>
				<div class="select2-field">
					<select class="point-mark civi-select2" name="candidate_skincolor" id="candidate_skincolor">
						<?php civi_get_taxonomy_by_post_id($candidate_id, 'candidate_skin-color', true); ?>
					</select>
				</div>
			</div>

		<?php endif; ?>
		<?php if (!in_array('fields_candidate_chestsize', $hide_candidate_fields)) : ?>
			<div class="form-group col-md-6">
					  <label for="candidate_chestsize"><?php esc_html_e('Chest Size (cm)', 'civi-framework') ?></label>
				<div class="select2-field">
					<select class="point-mark civi-select2" name="candidate_chestsize" id="candidate_chestsize">
						<?php civi_get_taxonomy_by_post_id($candidate_id, 'candidate_chest-size', true); ?>
					</select>
				</div>
			</div>
		<?php endif; ?>
		<?php if (!in_array('fields_candidate_waistsize', $hide_candidate_fields)) : ?>
			<div class="form-group col-md-6">
					  <label for="candidate_waistsize"><?php esc_html_e('Waist Size (cm)', 'civi-framework') ?></label>
				<div class="select2-field">
					<select class="point-mark civi-select2" name="candidate_waistsize" id="candidate_waistsize">
						<?php civi_get_taxonomy_by_post_id($candidate_id, 'candidate_waist-size', true); ?>
					</select>
				</div>
			</div>
		<?php endif; ?>
		<?php if (!in_array('fields_candidate_hipsize', $hide_candidate_fields)) : ?>
			<div class="form-group col-md-6">
					  <label for="candidate_hipsize"><?php esc_html_e('Hip Size (cm)', 'civi-framework') ?></label>
				<div class="select2-field">
					<select class="point-mark civi-select2" name="candidate_hipsize" id="candidate_hipsize">
						<?php civi_get_taxonomy_by_post_id($candidate_id, 'candidate_hip-size', true); ?>
					</select>
				</div>
			</div>
		<?php endif; ?>
		<?php if (!in_array('fields_candidate_bodytype', $hide_candidate_fields)) : ?>
			<div class="form-group col-md-6">
					  <label for="candidate_bodytype"><?php esc_html_e('Body Type', 'civi-framework') ?></label>
				<div class="select2-field">
					<select class="point-mark civi-select2" name="candidate_bodytype" id="candidate_bodytype">
						<?php civi_get_taxonomy_by_post_id($candidate_id, 'candidate_body-type', true); ?>
					</select>
				</div>
			</div>
		<?php endif; ?>	

		<?php /* *********** */ ?>
		
		
        <?php if (!in_array('fields_candidate_salary', $hide_candidate_fields)) : ?>
            <div class="form-group col-md-6">
                <label for="candidate_offer_salary"><?php esc_html_e('Offer Salary', 'civi-framework') ?></label>
                <input class="point-mark" type="number" id="candidate_offer_salary" name="candidate_offer_salary" value="<?php echo esc_attr($candidate_offer_salary); ?>" placeholder="<?php esc_html_e('Ex: 100', 'civi-framework') ?>">
            </div>
        <?php endif; ?>
        <?php if (!in_array('fields_candidate_salary', $hide_candidate_fields)) : ?>
            <div class="form-group col-md-6">
                <label><?php esc_html_e('Salary type', 'civi-framework'); ?></label>
                <div class="select2-field">
					<select name="candidate_salary_type" class="civi-select2 point-mark">
						<option <?php if ($candidate_salary_type == '') {
									echo 'selected';
								} ?> value=""><?php esc_html_e('None', 'civi-framework'); ?></option>
						<option <?php if ($candidate_salary_type == 'hr') {
									echo 'selected';
								} ?> value="hr"><?php esc_html_e('Hourly', 'civi-framework'); ?></option>
						<option <?php if ($candidate_salary_type == 'day') {
									echo 'selected';
								} ?> value="day"><?php esc_html_e('Daily', 'civi-framework'); ?></option>
						<option <?php if ($candidate_salary_type == 'month') {
									echo 'selected';
								} ?> value="month"><?php esc_html_e('Monthly', 'civi-framework'); ?></option>
						<option <?php if ($candidate_salary_type == 'year') {
									echo 'selected';
								} ?> value="year"><?php esc_html_e('Yearly', 'civi-framework'); ?></option>
					</select>
				</div>
            </div>
        <?php endif; ?>
        <?php if (!in_array('fields_candidate_salary', $hide_candidate_fields)) : ?>
            <div class="form-group col-md-6">
                <label><?php esc_html_e('Currency Type', 'civi-framework'); ?></label>
                <div class="select2-field">
					<select name="candidate_currency_type" class="civi-select2">
						<?php civi_get_select_currency_type(true); ?>
					</select>
				</div>
            </div>
        <?php endif; ?>
    </div>
</div>
