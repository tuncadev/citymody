<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $current_user;
$user_id = $current_user->ID;
$jobs_user_select_apply = get_user_meta($user_id, CIVI_METABOX_PREFIX . 'jobs_user_select_apply', true);
$jobs_user_apply_email = get_user_meta($user_id, CIVI_METABOX_PREFIX . 'jobs_user_apply_email', true);
$jobs_user_apply_external = get_user_meta($user_id, CIVI_METABOX_PREFIX . 'jobs_user_apply_external', true);
$jobs_user_apply_call_to = get_user_meta($user_id, CIVI_METABOX_PREFIX . 'jobs_user_apply_call_to', true);
?>
<div class="row">
    <div class="form-group col-md-6">
        <label><?php esc_html_e('Select type', 'civi-framework') ?></label>
        <div class="select2-field">
			<select id="select-apply-type" name="jobs_select_apply" class="civi-select2">
				<option <?php if ($jobs_user_select_apply == "email") {
					echo 'selected';
				} ?> value="email"><?php esc_html_e('By email', 'civi-framework') ?></option>
				<option <?php if ($jobs_user_select_apply == "external") {
					echo 'selected';
				} ?> value="external"><?php esc_html_e('External Apply', 'civi-framework') ?></option>
				<option <?php if ($jobs_user_select_apply == "internal") {
					echo 'selected';
				} ?> value="internal"><?php esc_html_e('Internal Apply', 'civi-framework') ?></option>
				<option <?php if ($jobs_user_select_apply == "call-to") {
					echo 'selected';
				} ?> value="call-to"><?php esc_html_e('Call To Apply', 'civi-framework') ?></option>
			</select>
		</div>
    </div>
    <div class="civi-section-apply-select form-group col-md-6" id="email">
        <label for="jobs_apply_email"><?php esc_html_e('Job apply email', 'civi-framework') ?></label>
        <input type="email" id="jobs_apply_email" name="jobs_apply_email"
               value="<?php echo esc_attr($jobs_user_apply_email) ?>"
               placeholder="<?php esc_attr_e('Enter email', 'civi-framework') ?>">
    </div>
    <div class="civi-section-apply-select form-group col-md-6" id="external">
        <label for="jobs_apply_external"><?php esc_html_e('Job apply external', 'civi-framework') ?></label>
        <input type="url" id="jobs_apply_external" name="jobs_apply_external"
               value="<?php echo esc_attr($jobs_user_apply_external) ?>"
               placeholder="<?php esc_attr_e('Enter url', 'civi-framework') ?>">
    </div>
    <div class="civi-section-apply-select form-group col-md-6" id="call-to">
        <label for="jobs_apply_call_to"><?php esc_html_e('Call to apply', 'civi-framework') ?></label>
        <input type="tel" id="jobs_apply_call_to" name="jobs_apply_call_to"
               value="<?php echo esc_attr($jobs_user_apply_call_to) ?>"
               placeholder="<?php esc_attr_e('Enter phone', 'civi-framework') ?>">
    </div>
</div>
