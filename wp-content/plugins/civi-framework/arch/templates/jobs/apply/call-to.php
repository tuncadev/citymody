<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
$jobs_id = get_the_ID();
$jobs_apply_call_to = !empty(get_post_meta($jobs_id, CIVI_METABOX_PREFIX . 'jobs_apply_call_to')) ? get_post_meta($jobs_id, CIVI_METABOX_PREFIX . 'jobs_apply_call_to')[0] : '';
?>
<form action="#" method="post" class="form-popup form-popup-apply form-call-to" id="civi_form_apply_jobs" enctype="multipart/form-data">
    <div class="bg-overlay"></div>
    <div class="apply-popup custom-scrollbar">
        <a href="#" class="btn-close"><i class="far fa-times"></i></a>
        <h5><?php esc_html_e('Call employer', 'civi-framework') ?></h5>
        <a href="tel:<?php echo esc_url($jobs_apply_call_to) ?>" class="phone-apply">
            <i class="fal fa-phone-alt"></i>
            <span><?php esc_html_e($jobs_apply_call_to) ?></span>
        </a>
    </div>
</form>