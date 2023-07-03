<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $jobs_data, $jobs_meta_data, $hide_jobs_fields;
$jobs_thumbnail_id = get_post_thumbnail_id($jobs_data->ID);
$jobs_image_url = get_the_post_thumbnail_url($jobs_data->ID, 'full');
civi_get_thumbnail_enqueue();
?>
<div class="jobs-fields-thumbnail civi-fields-thumbnail">
    <label><?php esc_html_e('Cover image', 'civi-framework'); ?></label>
    <div class="form-field">
        <div id="civi_thumbnail_errors" class="errors-log"></div>
        <div id="civi_thumbnail_container" class="file-upload-block preview">
            <div id="civi_thumbnail_view" data-image-id="<?php echo $jobs_thumbnail_id; ?>" data-image-url="<?php if (!empty($jobs_image_url)) {
                                                                                                                echo $jobs_image_url;
                                                                                                            } ?>"></div>
            <div id="civi_add_thumbnail">
                <i class="far fa-arrow-from-bottom large"></i>
                <p id="civi_drop_thumbnail">
                    <button type="button" id="civi_select_thumbnail"><?php esc_html_e('Click here', 'civi-framework') ?></button>
                    <?php esc_html_e(' or drop files to upload', 'civi-framework') ?>
                </p>
            </div>
            <input type="hidden" class="thumbnail_url form-control" name="jobs_thumbnail_url" value="" id="thumbnail_url">
            <input type="hidden" class="thumbnail_id" name="jobs_thumbnail_id" value="" id="thumbnail_id" />
        </div>
    </div>
</div>
<p class="civi-thumbnail-size"><?php esc_html_e('The cover image size should be max 1920 x 400px', 'civi-framework') ?></p>