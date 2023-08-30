<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $hide_jobs_fields, $current_user;
$jobs_id = get_the_ID();
$jobs_days_closing = civi_get_option('jobs_number_days', true);
$user_id = $current_user->ID;
$jobs_user_post_title = get_user_meta($user_id, CIVI_METABOX_PREFIX . 'jobs_user_post_title', true);
$jobs_user_post_des = get_user_meta($user_id, CIVI_METABOX_PREFIX . 'jobs_user_post_des', true);
$jobs_user_quantity = get_user_meta($user_id, CIVI_METABOX_PREFIX . 'jobs_user_quantity', true);
$jobs_user_gender = get_user_meta($user_id, CIVI_METABOX_PREFIX . 'jobs_user_gender', true);
$jobs_user_days_closing = get_user_meta($user_id, CIVI_METABOX_PREFIX . 'jobs_user_days_closing', true);
$taxonomy_name = "jobs-skills";
?>
<div class="row">
    <?php if (!in_array('fields_jobs_name', $hide_jobs_fields)) : ?>
        <div class="form-group col-md-12">
            <label for="jobs_title"><?php esc_html_e('Job title', 'civi-framework') ?> <sup>*</sup></label>
            <input type="text" id="jobs_title" name="jobs_title"
                   placeholder="<?php esc_attr_e('Name', 'civi-framework') ?>"
                   value="<?php echo $jobs_user_post_title ?>">
        </div>
    <?php endif; ?>
    <?php if (!in_array('fields_jobs_category', $hide_jobs_fields)) : ?>
        <div class="form-group col-lg-6">
            <label><?php esc_html_e('Jobs Categories', 'civi-framework') ?> <sup>*</sup></label>
            <div class="select2-field">
				<select name="jobs_categories" class="civi-select2">
					<?php civi_get_taxonomy('jobs-categories', false, true); ?>
				</select>
			</div>
        </div>
    <?php endif; ?>
    <?php if (!in_array('fields_jobs_type', $hide_jobs_fields)) : ?>
        <div class="form-group col-lg-6">
            <label><?php esc_html_e('Job type', 'civi-framework') ?></label>
            <div class="form-select">
                <div class="select2-field select2-multiple">
					<select data-placeholder="<?php esc_attr_e('Select an option', 'civi-framework'); ?>"
							multiple="multiple" class="civi-select2" name="jobs_type">
						<?php civi_get_taxonomy('jobs-type', false, false); ?>
					</select>
				</div>
                <i class="fas fa-angle-down"></i>
            </div>
        </div>
    <?php endif; ?>
    <?php if (!in_array('fields_jobs_skills', $hide_jobs_fields)) : ?>
        <div class="form-group col-md-12">
            <label><?php esc_html_e('Skills', 'civi-framework') ?> <sup>*</sup></label>
            <div class="form-select">
				<div class="select2-field select2-multiple">
					<select data-placeholder="<?php esc_attr_e('Select skills', 'civi-framework'); ?>" multiple="multiple"
							class="civi-select2" name="jobs_skills">123
						<?php list_skill_options($user_id, $taxonomy_name); ?>
						<?php // civi_get_taxonomy('jobs-skills', false, false); ?>
					</select>
				</div>
                <i class="fas fa-angle-down"></i>
            </div>
        </div>
    <?php endif; ?>
    <?php if (!in_array('fields_jobs_des', $hide_jobs_fields)) : ?>
        <div class="form-group col-md-12">
            <label class="label-des-jobs"><?php esc_html_e('Description', 'civi-framework'); ?> <sup>*</sup></label>
            <?php
            if ($jobs_user_post_des) {
                $content = "$jobs_user_post_des";
            } else {
                $content = '';
            }
            $editor_id = 'jobs_des';
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
            wp_editor(html_entity_decode(stripcslashes($content)), $editor_id, $settings); ?>
        </div>
    <?php endif; ?>
    <?php if (!in_array('fields_jobs_career', $hide_jobs_fields)) : ?>
        <div class="form-group col-lg-6">
            <label><?php esc_html_e('Career level', 'civi-framework') ?></label>
            <div class="select2-field">
				<select name="jobs_career" class="civi-select2">
					<?php civi_get_taxonomy('jobs-career', false, true); ?>
				</select>
			</div>
        </div>
    <?php endif; ?>
    <?php if (!in_array('fields_jobs_experience', $hide_jobs_fields)) : ?>
        <div class="form-group col-lg-6">
            <label><?php esc_html_e('Experience', 'civi-framework') ?></label>
            <div class="select2-field">
				<select name="jobs_experience" class="civi-select2">
					<?php civi_get_taxonomy('jobs-experience', false, true); ?>
				</select>
			</div>
        </div>
    <?php endif; ?>
    <?php if (!in_array('fields_jobs_qualification', $hide_jobs_fields)) : ?>
        <div class="form-group col-lg-6">
            <label><?php esc_html_e('Qualification', 'civi-framework') ?></label>
            <div class="form-select">
				<div class="select2-field select2-multiple">
					<select data-placeholder="<?php esc_attr_e('Select an option', 'civi-framework'); ?>"
							multiple="multiple" class="civi-select2" name="jobs_qualification">
						<?php civi_get_taxonomy('jobs-qualification', false, false); ?>
					</select>
				</div>
                <i class="fas fa-angle-down"></i>
            </div>
        </div>
    <?php endif; ?>
    <?php if (!in_array('fields_jobs_quantity', $hide_jobs_fields)) : ?>
        <div class="form-group col-lg-6">
            <label><?php esc_html_e('Quantity to be recruited', 'civi-framework') ?></label>
            <div class="select2-field">
				<select name="jobs_quantity" class="civi-select2">
					<?php for ($quantity = 0; $quantity <= 10; $quantity++) {
						if ($quantity == 0) { ?>
							<option selected value=""><?php esc_attr_e('Select an option', 'civi-framework'); ?></option>
							<?php
						} else {
							?>
							<option <?php if ($jobs_user_quantity == $quantity) {
								echo 'selected';
							} ?> value="<?php echo $quantity; ?>">
								<?php if ($quantity == 10) {
									echo $quantity . '+';
								} else {
									echo $quantity;
								} ?>
							</option>
						<?php }
					} ?>
				</select>
			</div>
        </div>
    <?php endif; ?>
    <?php if (!in_array('fields_jobs_gender', $hide_jobs_fields)) : ?>
        <div class="form-group col-lg-6">
            <label><?php esc_html_e('Gender', 'civi-framework'); ?></label>
            <div class="select2-field">
				<select name="jobs_gender" class="civi-select2">
					<option <?php if ($jobs_user_gender == "") {
						echo 'selected';
					} ?> value=""><?php esc_attr_e('Select an option', 'civi-framework'); ?></option>
					<option <?php if ($jobs_user_gender == "both") {
						echo 'selected';
					} ?> value="both"><?php esc_html_e('Both', 'civi-framework'); ?></option>
					<option <?php if ($jobs_user_gender == "female") {
						echo 'selected';
					} ?> value="female"><?php esc_html_e('Female', 'civi-framework'); ?></option>
					<option <?php if ($jobs_user_gender == "male") {
						echo 'selected';
					} ?> value="male"><?php esc_html_e('Male', 'civi-framework'); ?></option>
				</select>
			</div>
        </div>
    <?php endif; ?>
    <?php if (!in_array('fields_closing_days', $hide_jobs_fields)) : ?>
        <div class="form-group col-lg-6">
            <label for="jobs_days_closing"><?php esc_html_e('Closing days', 'civi-framework'); ?></label>
            <input type="text" id="jobs_days_closing" name="jobs_days_closing"
                   placeholder="<?php echo $jobs_days_closing; ?>" value="<?php echo $jobs_user_days_closing; ?>">
        </div>
    <?php endif; ?>
</div>
