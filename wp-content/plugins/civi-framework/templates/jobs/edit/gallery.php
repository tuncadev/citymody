<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $jobs_data, $jobs_meta_data, $hide_jobs_fields;
$image_max_file_size = civi_get_option('civi_image_max_file_size', '1000kb');
civi_get_gallery_enqueue();
?>
<div class="jobs-fields-media civi-fields-gallery">
    <label><?php esc_html_e('Image', 'civi-framework'); ?></label>
    <div class="jobs-fields jobs-fields-file jobs-gallery-image">
        <div class="field-media-warpper">
            <div class="media-gallery">
                <div id="civi_gallery_thumbs">
                    <?php
                    $jobs_img_arg = get_post_meta($jobs_data->ID, CIVI_METABOX_PREFIX . 'jobs_images', false);
                    $jobs_images  = (isset($jobs_img_arg) && is_array($jobs_img_arg) && count($jobs_img_arg) > 0) ? $jobs_img_arg[0] : '';
                    $jobs_images  = explode('|', $jobs_images);
                    $jobs_images  = array_unique($jobs_images);
                    if (!empty($jobs_images[0])) {
                        foreach ($jobs_images as $attach_id) {
                            echo '<div class="media-thumb-wrap">';
                            echo '<figure class="media-thumb">';
                            echo wp_get_attachment_image($attach_id, 'thumbnail');
                            echo '<div class="media-item-actions">';
                            echo '<a class="icon icon-gallery-delete" data-attachment-id="' . intval($attach_id) . '" href="javascript:void(0)">';
                            echo '<i class="far fa-trash-alt large"></i>';
                            echo '</a>';
                            echo '</a>';
                            echo '<input type="hidden" class="civi_gallery_ids" name="civi_gallery_ids[]" value="' . intval($attach_id) . '">';
                            echo '<span style="display: none;" class="icon icon-loader">';
                            echo '<i class="fal fa-spinner fa-spin large"></i>';
                            echo '</span>';
                            echo '</div>';
                            echo '</figure>';
                            echo '</div>';
                        }
                    }
                    ?>
                </div>
            </div>
            <div id="civi_gallery_errors" class="errors-log"></div>
            <div class="civi-gallery-warpper">
                <div class="civi-gallery-inner">
                    <div id="civi_gallery_container">
                        <button type="button" id="civi_select_gallery" class="btn btn-primary">
                            <i class="far fa-arrow-from-bottom large"></i>
                            <?php esc_html_e('Upload ', 'civi-framework'); ?>
                        </button>
                    </div>
                </div>
                <div class="field-note"><?php echo sprintf(__('Maximum file size: %s.', 'civi-framework'), $image_max_file_size); ?></div>
            </div>
        </div>
    </div>
</div>