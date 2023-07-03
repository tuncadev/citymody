<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $company_meta_data;
?>
<div class="form-group col-md-12">
    <label for="company_video_url"><?php esc_html_e('Introduction video Url', 'civi-framework') ?></label>
    <input type="url" id="company_video_url" name="company_video_url" value="<?php if (isset($company_meta_data[CIVI_METABOX_PREFIX . 'company_video_url'][0])) {
                                                                                    echo $company_meta_data[CIVI_METABOX_PREFIX . 'company_video_url'][0];
                                                                                } ?>" placeholder="<?php esc_attr_e('Enter url video', 'civi-framework') ?>">
</div>