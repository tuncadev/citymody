<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $hide_company_fields;
$image_max_file_size = civi_get_option('civi_image_max_file_size', '1000kb');
civi_get_gallery_enqueue();
?>
<div class="company-fields-media civi-fields-gallery">
    <label><?php esc_html_e('Image', 'civi-framework'); ?></label>
    <div class="company-fields company-fields-file company-gallery-image">
        <div class="field-media-warpper">
            <div class="media-gallery">
                <div id="civi_gallery_thumbs"></div>
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