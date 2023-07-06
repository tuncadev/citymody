<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

wp_enqueue_script(CIVI_PLUGIN_PREFIX . 'social-network');

global $candidate_data, $candidate_meta_data;
$candidate_twitter = isset($candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_twitter']) ? $candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_twitter'][0] : '';
$candidate_linkedin = isset($candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_linkedin']) ? $candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_linkedin'][0] : '';
$candidate_facebook = isset($candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_facebook']) ? $candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_facebook'][0] : '';
$candidate_instagram = isset($candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_instagram']) ? $candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_instagram'][0] : '';
$enable_social_twitter = civi_get_option('enable_social_twitter', '1');
$enable_social_linkedin = civi_get_option('enable_social_linkedin', '1');
$enable_social_facebook = civi_get_option('enable_social_facebook', '1');
$enable_social_instagram = civi_get_option('enable_social_instagram', '1');
?>

<div class="social-network block-from" id="candidate-submit-social">
    <h6><?php esc_html_e('Social Network', 'civi-framework') ?></h6>
    <div class="row civi-social-network">
        <?php if ($enable_social_twitter == 1) : ?>
            <div class="form-group col-12 col-sm-6">
                <label><?php esc_html_e('Twitter', 'civi-framework') ?></label>
                <input type="url" name="candidate_twitter" id="candidate_twitter"
                       class="point-mark"
                       value="<?php echo esc_attr($candidate_twitter) ?>"
                       placeholder="<?php esc_attr_e('twitter.com/candidate', 'civi-framework') ?>">
            </div>
        <?php endif; ?>
        <?php if ($enable_social_linkedin == 1) : ?>
            <div class="form-group col-12 col-sm-6">
                <label><?php esc_html_e('Linkedin', 'civi-framework') ?></label>
                <input type="url" name="candidate_linkedin" id="candidate_linkedin"
                       class="point-mark"
                       value="<?php echo esc_attr($candidate_linkedin) ?>"
                       placeholder="<?php esc_attr_e('linkedin.com/candidate', 'civi-framework') ?>">
            </div>
        <?php endif; ?>
        <?php if ($enable_social_facebook == 1) : ?>
            <div class="form-group col-12 col-sm-6">
                <label><?php esc_html_e('Facebook', 'civi-framework') ?></label>
                <input type="url" name="candidate_facebook" id="candidate_facebook"
                       class="point-mark"
                       value="<?php echo esc_attr($candidate_facebook) ?>"
                       placeholder="<?php esc_attr_e('facebook.com/candidate', 'civi-framework') ?>">
            </div>
        <?php endif; ?>
        <?php if ($enable_social_instagram == 1) : ?>
            <div class="form-group col-12 col-sm-6">
                <label><?php esc_html_e('Instagram', 'civi-framework') ?></label>
                <input type="url" name="candidate_instagram" id="candidate_instagram"
                       class="point-mark"
                       value="<?php echo esc_attr($candidate_instagram) ?>"
                       placeholder="<?php esc_attr_e('instagram.com/candidate', 'civi-framework') ?>">
            </div>
        <?php endif; ?>
    </div>

    <div class="field-social-clone">
        <div class="clone-wrap">
            <div class="soical-remove-inner">
                <a href="#" class="remove-social"><i class="fas fa-times"></i></a>
                <span><?php esc_html_e('Network', 'civi-framework') ?><span class="number-network"></span></span>
            </div>
            <div class="row field-wrap">
                <div class="form-group col-12 col-sm-6">
                    <label><?php esc_html_e('Name', 'civi-framework') ?></label>
					<input type="text" name="candidate_social_name[]"
                           placeholder="<?php esc_attr_e('Candidate', 'civi-framework') ?>">
                </div>
                <div class="form-group col-12 col-sm-6">
                    <label><?php esc_html_e('Url', 'civi-framework') ?></label>
                    <input type="url" name="candidate_social_url[]"
                           placeholder="<?php esc_attr_e('url.com/candidate', 'civi-framework') ?>">
                </div>
            </div>
        </div>
    </div>

    <div class="add-social-list">
        <?php
        $candidate_social_tab = get_post_meta($candidate_data->ID, CIVI_METABOX_PREFIX . 'candidate_social_tabs', false);
        $i = 0;
        if (is_array($candidate_social_tab)) {
            foreach ($candidate_social_tab as $social) {
                if (is_array($social)) {
                    foreach ($social as $k1 => $social_v1) {
                        ?>

                        <div class="clone-wrap">
                            <div class="soical-remove-inner">
                                <a href="#" class="remove-social"><i class="fas fa-times"></i></a>
                                <span><?php esc_html_e('Network', 'civi-framework') ?><span
                                            class="number-network"></span></span>
                            </div>
                            <div class="row field-wrap">
                                <div class="col col-md-6">
                                    <label><?php esc_html_e('Name', 'civi-framework') ?></label>
									<input type="text" name="candidate_social_name[]"
										value="<?php echo $social_v1[CIVI_METABOX_PREFIX . 'candidate_social_name']; ?>"
                           				placeholder="<?php esc_attr_e('Candidate', 'civi-framework') ?>">
                                </div>
                                <div class="col col-md-6">
                                    <label><?php esc_html_e('Url', 'civi-framework') ?></label>
                                    <input type="url" name="candidate_social_url[]"
                                           value="<?php echo $social_v1[CIVI_METABOX_PREFIX . 'candidate_social_url']; ?>"
                                           placeholder="<?php esc_attr_e('url.com/candidate', 'civi-framework') ?>">
                                </div>
                            </div>
                        </div>
                    <?php }
                    $i++;
                }
            }
        } ?>
    </div>
    <a class="civi-button button-link add-social" href="#addsocial">
        <span class="civi-button-icon"><i class="far fa-chevron-down"></i></span>
        <?php esc_html_e('Add more', 'civi-framework') ?>
    </a>
</div>
