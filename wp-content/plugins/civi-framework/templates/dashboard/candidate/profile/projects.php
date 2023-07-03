<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $candidate_data;
$candidate_project_list = get_post_meta($candidate_data->ID, CIVI_METABOX_PREFIX . 'candidate_project_list', false);
$candidate_project_list = !empty($candidate_project_list) ? $candidate_project_list[0] : '';
$candidate_project_quantity = !empty($candidate_project_list) ? count($candidate_project_list) : '';

$candidate_id = $candidate_data->ID;
$ajax_url = admin_url('admin-ajax.php');
$upload_nonce = wp_create_nonce('candidate_allow_upload');
$image_max_file_size = civi_get_option('civi_image_max_file_size');
$image_type = civi_get_option('civi_image_type');
$data_uploaded = '<i class="far fa-arrow-from-bottom large"></i><span>' . esc_html__('Upload', 'civi-framework') . '</span>';
$data_upload = esc_html__('Upload A Screenshot', 'civi-framework');
$image_title = esc_html__('Valid file formats', 'civi-framework');
?>

<div id="tab-projects" class="tab-info projects" data-file-size="<?php esc_attr_e($image_max_file_size) ?>" data-title="<?php esc_attr_e($image_title); ?>" data-nonce="<?php esc_attr_e($upload_nonce) ?>" data-uploaded="<?php esc_attr_e($data_uploaded) ?>" data-upload="<?php esc_attr_e($data_upload) ?>" data-type="<?php esc_attr_e($image_type); ?>">

    <div class="project-info block-from">
        <h5><?php esc_html_e('Projects', 'civi-framework') ?></h5>

        <div class="sub-head"><?php esc_html_e('We recommend at least one project entry', 'civi-framework') ?></div>

        <div class="civi-candidate-warpper">
            <?php
            if (!empty($candidate_project_list)) :
                foreach ($candidate_project_list as $index => $candidate_project) : ?>
                    <div class="row">
                        <div class="group-title col-md-12">
                            <i class="delete-group fas fa-times"></i>
                            <h6 class="project">
                                <?php esc_html_e('Project', 'civi-framework') ?>
                                <span><?php echo $index + 1 ?></span>
                            </h6>
                            <i class="fas fa-angle-up"></i>
                        </div>
                        <div class="form-group col-md-12">
                            <div class="uploaded-container" id="uploaded-container_<?php echo $index + 1 ?>" hidden>
                                <button class="uploaded-main" type="button" hidden id="uploader-main_<?php echo $index + 1 ?>"></button>
                            </div>
                            <?php
                            $project_image = $candidate_project[CIVI_METABOX_PREFIX . 'candidate_project_image_id'];
                            $attach_id = !empty($project_image) ? $project_image['id'] : '';
                            $image_url = !empty($project_image) ? $project_image['url'] : '';
                            ?>

                            <?php if (empty($image_url)) : ?>
                                <div id="project-uploader_<?php echo $index + 1 ?>" class="browse project-upload">
                                    <i class="far fa-arrow-from-bottom large"></i>
                                    <span><?php esc_html_e('Upload', 'civi-framework'); ?></span>
                                </div>
                            <?php endif; ?>
                            <ul id="project-uploaded-list_<?php echo $index + 1 ?>" class="project-uploaded-list card-text col-md-7">
                                <?php if (!empty($attach_id)) :
                                ?>
                                    <li class="card-preview-item">
                                        <figure class="media-thumb media-thumb-wrap">
                                            <?php echo wp_get_attachment_image($attach_id, 'thumbnail') ?>
                                            <div class="media-item-actions">
                                                <a class="icon icon-project-delete" data-candidate-id="<?php echo esc_attr($candidate_id) ?>" data-attachment-id="<?php esc_attr_e($attach_id) ?>" href="javascript:void(0)">
                                                    <i class="far fa-trash-alt large"></i>
                                                </a>
                                                <span style="display: none;" class="icon icon-loader"><i class="fal fa-spinner fa-spin large"></i></span>
                                            </div>
                                        </figure>
                                    </li>
                                <?php endif; ?>
                                <input type="hidden" class="candidate_project_image_id" name="candidate_project_image_id[]" value="<?php esc_attr_e($attach_id); ?>" />
                                <input type="hidden" class="candidate_project_image_url" name="candidate_project_image_url[]" value="<?php esc_attr_e($image_url); ?>">
                            </ul>
                            <div id="civi_project_errors_log_<?php echo $index + 1 ?>" class="errors-log"></div>
                            <div class="field-note"><?php echo sprintf(__('Upload file: %s', 'civi-framework'), $image_type); ?></div>
                        </div>
                        <div class="form-group col-md-6">
                            <label><?php esc_html_e('Title', 'civi-framework') ?></label>
                            <input type="text" name="candidate_project_title[]" placeholder="<?php esc_attr_e('Name of project', 'civi-framework'); ?>" value="<?php echo esc_attr($candidate_project[CIVI_METABOX_PREFIX . 'candidate_project_title']) ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label><?php esc_html_e('Link', 'civi-framework') ?></label>
                            <input type="url" name="candidate_project_link[]" placeholder="<?php esc_attr_e('https://yourlink', 'civi-framework'); ?>" value="<?php echo esc_attr($candidate_project[CIVI_METABOX_PREFIX . 'candidate_project_link']) ?>">
                        </div>
                        <div class="form-group col-md-12">
                            <label><?php esc_html_e('Description', 'civi-framework') ?></label>
                            <textarea name="candidate_project_description[]" cols="30" placeholder="<?php esc_attr_e('Short description', 'civi-framework'); ?>" rows="7"><?php echo esc_attr($candidate_project[CIVI_METABOX_PREFIX . 'candidate_project_description']) ?></textarea>
                        </div>
                    </div>
            <?php endforeach;
            endif;
            ?>

            <button type="button" class="btn-more profile-fields"><i class="far fa-angle-down"></i><?php esc_html_e('Add another project', 'civi-framework') ?></button>

            <template id="template-item-project" data-size="<?php echo esc_attr($candidate_project_quantity) ?>">
                <div class="row">
                    <div class="group-title col-md-12">
                        <i class="delete-group fas fa-times"></i>
                        <h6 class="project">
                            <?php esc_html_e('Project', 'civi-framework') ?>
                            <span></span>
                        </h6>
                        <i class="fas fa-angle-up"></i>
                    </div>
                    <div class="form-group col-md-12">
                        <div class="uploaded-container" id="uploaded-container_<?php echo $candidate_project_quantity ?>" hidden>
                            <button class="uploaded-main" type="button" hidden id="uploader-main_<?php echo $candidate_project_quantity ?>"></button>
                        </div>
                        <div id="project-uploader_<?php echo $candidate_project_quantity ?>" class="browse project-upload">
                            <i class="far fa-arrow-from-bottom large"></i>
                            <span><?php esc_html_e('Upload', 'civi-framework'); ?></span>
                        </div>
                        <ul id="project-uploaded-list_<?php echo $candidate_project_quantity ?>" class="project-uploaded-list card-text col-md-7">
                            <input type="hidden" class="candidate_project_image_id" name="candidate_project_image_id[]" value="" />
                            <input type="hidden" class="candidate_project_image_url" name="candidate_project_image_url[]" value="">
                        </ul>
                        <div id="civi_project_errors_log_<?php echo $candidate_project_quantity ?>" class="errors-log"></div>
                        <div class="field-note"><?php echo sprintf(__('Upload file: %s', 'civi-framework'), $image_type); ?></div>
                    </div>
                    <div class="form-group col-md-6">
                        <label><?php esc_html_e('Title', 'civi-framework') ?></label>
                        <input type="text" name="candidate_project_title[]" value="" placeholder="<?php esc_attr_e('Name of project', 'civi-framework'); ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label><?php esc_html_e('Link', 'civi-framework') ?></label>
                        <input type="url" name="candidate_project_link[]" value="" placeholder="<?php esc_attr_e('https://yourlink', 'civi-framework'); ?>">
                    </div>
                    <div class="form-group col-md-12">
                        <label><?php esc_html_e('Description', 'civi-framework') ?></label>
                        <textarea name="candidate_project_description[]" cols="30" rows="7" placeholder="<?php esc_attr_e('Short description', 'civi-framework'); ?>"></textarea>
                    </div>
                </div>
            </template>
            <template id="project-single-image">
                <figure class="media-thumb media-thumb-wrap">
                    <img src="" />
                    <div class="media-item-actions">
                        <a class="icon icon-project-delete" data-candidate-id="<?php echo esc_attr($candidate_id) ?>" data-attachment-id="" href="javascript:void(0)">
                            <i class="far fa-trash-alt large"></i>
                        </a>
                        <span style="display: none;" class="icon icon-loader"><i class="fal fa-spinner fa-spin large"></i></span>
                    </div>
                </figure>
            </template>
        </div>
    </div>
    <?php civi_custom_field_candidate('projects'); ?>
</div>