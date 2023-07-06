<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $candidate_data, $candidate_meta_data;
$candidate_resume = isset($candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_resume_id_list']) ? $candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_resume_id_list'][0] : '';
$filename = basename(get_attached_file($candidate_resume));
$ajax_url = admin_url('admin-ajax.php');
$cv_file = civi_get_option('civi-cv-type');
$cv_max_file_size = civi_get_option('cv_file_size', '1000kb');

$upload_nonce = wp_create_nonce('civi_thumbnail_allow_upload');
$url = CIVI_AJAX_URL . '?action=civi_thumbnail_upload_ajax&nonce=' . esc_attr($upload_nonce);
$text = '<i class="far fa-arrow-from-bottom large"></i> ' . esc_html__('Browse', 'civi-framework');


wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'upload-cv');
wp_localize_script(
    CIVI_PLUGIN_PREFIX . 'upload-cv',
    'civi_upload_cv_vars',
    array(
        'ajax_url' => $ajax_url,
        'title' => esc_html__('Valid file formats', 'civi-framework'),
        'cv_file' => $cv_file,
        'cv_max_file_size' => $cv_max_file_size,
        'upload_nonce' => $upload_nonce,
        'url' => $url,
        'text' => $text,
    )
);
$cv_file = civi_get_option('civi-cv-type');
?>

<div class="resume block-from">
    <h6><?php esc_html_e('Resume', 'civi-framework') ?></h6>
    <div class="candidate-resume">
        <div class="form-group col-md-12 civi-upload-cv">
            <label><?php esc_html_e('CV Attachment', 'civi-framework'); ?></label>
            <div class="form-field">
                <div id="cv_errors_log" class="errors-log"></div>
                <div id="civi_cv_plupload_container" class="file-upload-block preview">
                    <div class="civi_cv_file civi_add-cv">
                        <p id="civi_drop_cv" data-attachment-id="<?php esc_attr_e($candidate_resume) ?>">
                            <button class="civi-button" type="button" id="civi_select_cv">
                                <i class="far fa-arrow-from-bottom large"></i>
                                <?php if (!empty($candidate_resume)) { ?>
                                    <span><?php esc_html_e($filename); ?></span>
                                <?php } else { ?>
                                    <span><?php esc_html_e('Browse', 'civi-framework'); ?></span>
                                <?php } ?>
                            </button>
                            <?php if (!empty($candidate_resume)) { ?>
                                <a class="icon cv-icon-delete" data-attachment-id="<?php esc_attr_e($candidate_resume) ?>" href="#"><i class="far fa-trash-alt large"></i></a>
                            <?php } ?>
                        </p>
                    </div>
                    <span class="file-type"><?php echo esc_attr(sprintf(esc_html__('Upload file: %s', 'civi-framework'), $cv_file)); ?></span>
                </div>
            </div>
        </div>

    </div>
</div>