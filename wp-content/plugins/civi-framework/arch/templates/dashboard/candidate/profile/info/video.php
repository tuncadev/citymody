<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly 
}

global $candidate_data, $candidate_meta_data;
$candidate_video_url = isset($candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_video_url']) ? $candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_video_url'][0] : '';
?>
<div class="candidate-video block-from">
    <h6><?php esc_html_e('Video', 'civi-framework') ?></h6>

    <div class="row">
        <div class="form-group col-md-12">
            <label for="candidate-video-url"><?php esc_html_e('Video', 'civi-framework') ?></label>
            <input class="point-mark" type="url" id="candidate-video-url" name="candidate_video_url"
                   placeholder="<?php esc_html_e('Enter url video', 'civi-framework') ?>"
                   value="<?php echo esc_attr($candidate_video_url); ?>">
        </div>
    </div>

</div>