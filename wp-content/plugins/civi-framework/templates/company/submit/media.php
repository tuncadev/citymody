<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $hide_company_fields, $current_user;
$image_max_file_size = civi_get_option('civi_image_max_file_size', '1000kb');
civi_get_thumbnail_enqueue();
civi_get_avatar_enqueue();
?>
<div class="company-fields-warpper">
    <div class="company-fields-avatar civi-fields-avatar">
        <label><?php esc_html_e('Logo', 'civi-framework'); ?></label>
        <div class="form-field">
            <div id="civi_avatar_errors" class="errors-log"></div>
            <div id="civi_avatar_container" class="file-upload-block preview">
                <div id="civi_avatar_view"></div>
                <div id="civi_add_avatar">
                    <i class="far fa-arrow-from-bottom large"></i>
                    <p id="civi_drop_avatar">
                        <button type="button" id="civi_select_avatar"><?php esc_html_e('Upload', 'civi-framework') ?></button>
                    </p>
                </div>
                <input type="hidden" class="avatar_url form-control" name="company_avatar_url" value="" id="avatar_url">
                <input type="hidden" class="avatar_id" name="company_avatar_id" value="" id="avatar_id" />
            </div>
        </div>
        <div class="field-note"><?php echo sprintf(__('Maximum file size: %s.', 'civi-framework'), $image_max_file_size); ?></div>
    </div>
    <div class="company-fields-thumbnail civi-fields-thumbnail">
        <label><?php esc_html_e('Cover image', 'civi-framework'); ?></label>
        <div class="form-field">
            <div id="civi_thumbnail_errors" class="errors-log"></div>
            <div id="civi_thumbnail_container" class="file-upload-block preview">
                <div id="civi_thumbnail_view"></div>
                <div id="civi_add_thumbnail">
                    <i class="far fa-arrow-from-bottom large"></i>
                    <p id="civi_drop_thumbnail">
                        <button type="button" id="civi_select_thumbnail"><?php esc_html_e('Click here', 'civi-framework') ?></button>
                        <?php esc_html_e(' or drop files to upload', 'civi-framework') ?>
                    </p>
                </div>
                <input type="hidden" class="thumbnail_url form-control" name="company_thumbnail_url" value="" id="thumbnail_url">
                <input type="hidden" class="thumbnail_id" name="company_thumbnail_id" value="" id="thumbnail_id" />
            </div>
        </div>
        <p class="civi-thumbnail-size"><?php esc_html_e('The cover image size should be max 1920 x 400px', 'civi-framework') ?></p>
    </div>
</div>