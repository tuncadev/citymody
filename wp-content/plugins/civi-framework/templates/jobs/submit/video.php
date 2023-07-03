<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $current_user;
$user_id  = $current_user->ID;
$jobs_user_video_url = get_user_meta($user_id, CIVI_METABOX_PREFIX . 'jobs_user_video_url', true);
?>
<div class="form-group col-md-12">
    <label for="jobs_video_url"><?php esc_html_e('Introduction video Url', 'civi-framework') ?></label>
    <input type="url" id="jobs_video_url" name="jobs_video_url" placeholder="<?php esc_attr_e('youtube.com/asdh843s', 'civi-framework') ?>" value="<?php echo $jobs_user_video_url ?>">
</div>