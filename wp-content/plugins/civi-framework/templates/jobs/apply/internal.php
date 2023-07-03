<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
$cv_file = civi_get_option('civi-cv-type');
$cv_max_file_size = civi_get_option('cv_file_size', '1000kb');
$text = '<i class="far fa-arrow-from-bottom large"></i> ' . esc_attr(sprintf(esc_html__('Upload CV (%s)', 'civi-framework'), $cv_file));
$upload_nonce = wp_create_nonce('civi_thumbnail_allow_upload');
$url = CIVI_AJAX_URL .  '?action=civi_thumbnail_upload_ajax&nonce=' . esc_attr($upload_nonce);

wp_enqueue_script('plupload');
wp_enqueue_script('jquery-validate');
wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'upload-cv');
wp_localize_script(
	CIVI_PLUGIN_PREFIX . 'upload-cv',
	'civi_upload_cv_vars',
	array(
		'ajax_url'    => CIVI_AJAX_URL,
		'title'   => esc_html__('Valid file formats', 'civi-framework'),
		'cv_file' => $cv_file,
		'cv_max_file_size' => $cv_max_file_size,
		'upload_nonce' => $upload_nonce,
		'url' => $url,
		'text' => $text,
	)
);

global $current_user;
$user_id = $current_user->ID;
$candidate_id =  $fileUrl = '';
if (in_array('civi_user_candidate', (array)$current_user->roles)) {
	$args_candidate = array(
		'post_type' => 'candidate',
		'author' => $user_id,
	);
	$query = new WP_Query($args_candidate);
	$candidate_id = $query->post->ID;
}

$jobs_id = get_the_ID();
$jobs_select_apply = !empty(get_post_meta($jobs_id, CIVI_METABOX_PREFIX . 'jobs_select_apply')) ? get_post_meta($jobs_id, CIVI_METABOX_PREFIX . 'jobs_select_apply')[0] : '';
$candidate_resume = !empty($candidate_id) ? get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_resume_id_list', false) : '';
$candidate_resume = !empty($candidate_resume) ? $candidate_resume[0] : '';
$fileName = basename(get_attached_file($candidate_resume));
if (!empty(wp_get_attachment_url($candidate_resume))) {
	$fileUrl = wp_get_attachment_url($candidate_resume);
}
?>
<form action="#" method="post" class="form-popup form-popup-apply" id="civi_form_apply_jobs" enctype="multipart/form-data">
	<div class="bg-overlay"></div>
	<div class="apply-popup custom-scrollbar">
		<a href="#" class="btn-close"><i class="far fa-times"></i></a>
		<h5><?php esc_html_e('Apply for this job', 'civi-framework') ?></h5>
		<div class="row">
			<div class="form-group col-md-12">
				<label for="apply_message"><?php esc_html_e('Message', 'civi-framework') ?></label>
				<textarea id="apply_message" name="apply_message" rows="4" cols="50"></textarea>
			</div>
			<div class="form-group col-md-12 civi-upload-cv">
				<div class="form-field">
					<div id="cv_errors_log" class="errors-log"></div>
					<div id="civi_cv_plupload_container" class="file-upload-block preview">
						<div class="civi_cv_file civi_add-cv">
							<p id="civi_drop_cv">
								<?php if (!empty($fileName)) { ?>
									<button type="button" id="civi_select_cv">
										<i class="far fa-arrow-from-bottom large"></i>
										<?php esc_html_e($fileName); ?>
									</button>
								<?php } else { ?>
									<button type="button" id="civi_select_cv">
										<i class="far fa-arrow-from-bottom large"></i>
										<?php echo esc_attr(sprintf(esc_html__('Upload CV (%s)', 'civi-framework'), $cv_file)); ?>
									</button>
								<?php } ?>
							</p>
						</div>
						<input type="hidden" class="cv_url form-control" name="jobs_cv_url" value="" id="cv_url">
						<input type="hidden" class="type_apply form-control" name="type_apply" value="<?php esc_html_e($jobs_select_apply); ?>" id="type_apply">
					</div>
				</div>
			</div>
		</div>
		<div class="message_error"></div>
		<div class="button-warpper">
			<a href="#" class="civi-button button-outline button-block button-cancel"><?php esc_html_e('Cancel', 'civi-framework'); ?></a>
			<button type="submit" class="civi-button button-block btn-submit-apply-jobs" id="btn-apply-jobs-<?php echo $jobs_id ?>" data-jobs_id="<?php echo $jobs_id ?>" data-candidate_id="<?php echo $candidate_id ?>">
				<?php esc_html_e('Apply Jobs', 'civi-framework'); ?>
				<span class="btn-loading"><i class="fal fa-spinner fa-spin large"></i></span>
			</button>
		</div>
	</div>
</form>