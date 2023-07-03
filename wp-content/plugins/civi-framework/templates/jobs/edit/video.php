<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $jobs_meta_data;
?>
<div class="form-group col-md-12">
    <label for="jobs_video_url"><?php esc_html_e('Introduction video Url', 'civi-framework') ?></label>
    <input type="url" id="jobs_video_url" name="jobs_video_url" placeholder="<?php esc_attr_e('youtube.com/asdh843s', 'civi-framework') ?>" value="<?php if (isset($jobs_meta_data[CIVI_METABOX_PREFIX . 'jobs_video_url'][0])) {
                                                                                                                                                        echo $jobs_meta_data[CIVI_METABOX_PREFIX . 'jobs_video_url'][0];
                                                                                                                                                    } ?>">
</div>