<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $company_data, $company_meta_data, $hide_company_fields;
$image_max_file_size = civi_get_option('civi_image_max_file_size', '1000kb');
civi_get_gallery_enqueue();
?>
<div class="company-fields-media civi-fields-gallery">
    <label><?php esc_html_e('Image', 'civi-framework'); ?></label>
    <div class="company-fields company-fields-file company-gallery-image">
        <div class="field-media-warpper">
            <div class="media-gallery">
                <div id="civi_gallery_thumbs">
                    <?php
                    $company_img_arg = get_post_meta($company_data->ID, CIVI_METABOX_PREFIX . 'company_images', false);
                    $company_images  = (isset($company_img_arg) && is_array($company_img_arg) && count($company_img_arg) > 0) ? $company_img_arg[0] : '';
                    $company_images  = explode('|', $company_images);
                    $company_images  = array_unique($company_images);
                    if (!empty($company_images[0])) {
                        foreach ($company_images as $attach_id) {
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