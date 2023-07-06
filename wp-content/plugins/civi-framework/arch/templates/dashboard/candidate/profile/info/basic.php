<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $hide_candidate_fields, $candidate_data, $candidate_meta_data, $current_user;
$user_id = $current_user->ID;

$candidate_id = civi_get_post_id_candidate();
$candidate_des = $candidate_data->post_content;
$candidate_first_name = isset($candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_first_name']) ? $candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_first_name'][0] : '';
$candidate_last_name = isset($candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_last_name']) ? $candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_last_name'][0] : '';
$candidate_email = isset($candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_email']) ? $candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_email'][0] : '';
$candidate_phone = isset($candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_phone']) ? $candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_phone'][0] : '';
$candidate_current_position = isset($candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_current_position']) ? $candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_current_position'][0] : '';
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
?>

<div class="candidate basic-info block-from">

    <h6><?php esc_html_e('Basic Information', 'civi-framework'); ?></h6>

    <input type="hidden" name="candidate_id" value="<?php echo esc_attr($candidate_id) ?>">

    <div class="civi-avatar-candidate">

        <?php if (!in_array('fields_candidate_avatar', $hide_candidate_fields)) : ?>
            <div class="candidate-fields-avatar civi-fields-avatar">
                <label><?php esc_html_e('Your photo', 'civi-framework'); ?></label>
                <div class="form-field">
                    <div id="civi_avatar_errors" class="errors-log"></div>
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
                <input class="point-mark" type="text" id="user_firstname" name="candidate_first_name" placeholder="<?php esc_attr_e('First name', 'civi-framework') ?>" value="<?php echo esc_attr($candidate_first_name); ?>">
            </div>
        <?php endif; ?>
        <?php if (!in_array('fields_candidate_last_name', $hide_candidate_fields)) : ?>
            <div class="form-group col-md-6">
                <label for="candidate_last_name"><?php esc_html_e('Last name', 'civi-framework') ?></label>
                <input class="point-mark" type="text" id="user_lastname" name="candidate_last_name" placeholder="<?php esc_attr_e('Last name', 'civi-framework') ?>" value="<?php echo esc_attr($candidate_last_name); ?>">
            </div>
        <?php endif; ?>
        <?php if (!in_array('fields_candidate_email_address', $hide_candidate_fields)) : ?>
            <div class="form-group col-md-6">
                <label for="candidate_email"><?php esc_html_e('Email address', 'civi-framework') ?></label>
                <input class="point-mark" type="email" id="user_email" name="candidate_email" placeholder="<?php esc_attr_e('Email', 'civi-framework') ?>" value="<?php echo esc_attr($candidate_email); ?>">
            </div>
        <?php endif; ?>
        <?php if (!in_array('fields_candidate_phone_number', $hide_candidate_fields)) : ?>
            <div class="form-group col-md-6">
                <label for="candidate_phone"><?php esc_html_e('Phone number', 'civi-framework') ?></label>
                <input class="point-mark" type="number" id="author_mobile_number" name="candidate_phone" value="<?php echo esc_attr($candidate_phone); ?>" placeholder="<?php esc_attr_e('Phone', 'civi-framework'); ?>">
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
					<select class="point-mark civi-select2" name="candidate_categories" id="candidate_categories">
						<?php civi_get_taxonomy_by_post_id($candidate_id, 'candidate_categories', true); ?>
					</select>
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
						<option <?php if ($candidate_gender == "both") {
									echo 'selected';
								} ?> value="both"><?php esc_html_e('Both', 'civi-framework'); ?></option>
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
                <label for="candidate_languages"><?php esc_html_e('Languages', 'civi-framework') ?></label>
                <div class="select2-field">
					<select class="civi-select2 point-mark" name="candidate_languages" id="candidate_languages">
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
