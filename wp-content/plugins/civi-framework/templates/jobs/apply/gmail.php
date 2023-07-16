<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
} ?>
<?php
wp_enqueue_script('jquery-validate');

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

$candidate_phone = $candidate_email = '';
if (!empty($candidate_id)) {
	$candidate_email = !empty(get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_email')) ? get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_email')[0] : '';
	$candidate_phone = !empty(get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_phone')) ? get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_phone')[0] : '';
}
?>
<script>
	var $btn_submit = jQuery("#" + jQuery(".btn-submit-apply-jobs").attr("id"));
	function clickMe(){
jQuery('#btn-apply-jobs-<?php echo $jobs_id ?>').trigger('click');
	}
</script>
<?php  
if ( is_user_logged_in() ) {
	$showhide = "visibility: hidden;";
?>

<?php } else {
$showhide = "visibility: visible;";
} ?>
<form action="#" method="post" class="form-popup form-popup-apply" id="civi_form_apply_jobs" name="civi_form_apply_jobs" enctype="multipart/form-data">
	<div class="bg-overlay"></div>
	<div class="apply-popup custom-scrollbar"  style="<?php echo $showhide; ?>">
		<a href="#" class="btn-close"><i class="far fa-times"></i></a>
		<h5><?php esc_html_e('Apply for this job', 'civi-framework') ?></h5>
		<div class="row">
			<div class="form-group col-md-12">
				<label for="apply_email"><?php esc_html_e('Email address', 'civi-framework') ?></label>
				<input type="email" id="apply_email" name="apply_emaill" placeholder="Enter email" value="<?php echo esc_attr($candidate_email) ?>">
			</div>
			<div class="form-group col-md-12">
				<label for="apply_phone"><?php esc_html_e('Phone', 'civi-framework') ?></label>
				<input type="number" id="apply_phone" name="apply_phone" placeholder="Enter phone" value="<?php echo esc_attr($candidate_phone) ?>">
			</div>
			<div class="form-group col-md-12 civi-upload-cv">
				<div class="form-field">
					<div id="cv_errors_log" class="errors-log"></div>
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
