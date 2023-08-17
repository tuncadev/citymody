<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $hide_candidate_fields, $current_user, $candidate_data;
$user_id = $current_user->ID;
$image_max_file_size = civi_get_option('civi_image_max_file_size', '1000kb');
civi_get_gallery_enqueue();
?>
<div class="civi-candidate-galleries civi-upload-gallery block-from">
    <h6><?php esc_html_e('Gallery', 'civi-framework') ?></h6>
	<h4 class="gal_warning"><?php esc_html_e('* In order to be discovered by companies for thier projects, please upload at least 4 photos', 'civi-framework'); ?></h4>
    <div class="candidate-fields-media civi-fields-gallery">
        <label><?php esc_html_e('Image', 'civi-framework'); ?></label>
        <div class="candidate-fields candidate-fields-file candidate-gallery-image">
            <div class="field-media-warpper">
                <div class="media-gallery">
                    <div id="civi_gallery_thumbs">
                        <?php
                        $candidate_img_arg = get_post_meta($candidate_data->ID, CIVI_METABOX_PREFIX . 'candidate_galleries', false);
                        $candidate_images = (isset($candidate_img_arg) && is_array($candidate_img_arg) && count($candidate_img_arg) > 0) ? $candidate_img_arg[0] : '';
                        $candidate_images = explode('|', $candidate_images);
                        $candidate_images = array_unique($candidate_images);
                        if (!empty($candidate_images[0])) {
                            foreach ($candidate_images as $attach_id) {
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
</div>