<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $candidate_data;
$candidate_video_list = get_post_meta($candidate_data->ID, CIVI_METABOX_PREFIX . 'candidate_video_list', false);
$candidate_video_list = !empty($candidate_video_list) ? $candidate_video_list[0] : '';
$candidate_video_quantity = !empty($candidate_video_list) ? count($candidate_video_list) : '';
?>

    <div class="awards-info block-from">
        <h5><?php esc_html_e('Videos', 'civi-framework') ?></h5>

        <div class="sub-head"><?php esc_html_e('We recommend at least one video entry', 'civi-framework') ?></div>

        <div class="civi-candidate-warpper">
            <?php if (!empty($candidate_video_list)) {
                foreach ($candidate_video_list as $index => $candidate_video) : ?>
                    <div class="row">
                        <div class="group-title col-md-12">
                            <i class="delete-group fas fa-times"></i>
                            <h6 class="project">
                                <?php esc_html_e('Videos', 'civi-framework') ?>
                                <span><?php echo $index + 1 ?></span>
                            </h6>
                            <i class="fas fa-angle-up"></i>
                        </div>
                        <div class="form-group col-md-6">
                            <label><?php esc_html_e('Title', 'civi-framework') ?></label>
                            <input type="text" name="candidate_video_title[]" placeholder="<?php esc_attr_e('Video Title', 'civi-framework'); ?>" value="<?php echo esc_attr($candidate_video[CIVI_METABOX_PREFIX . 'candidate_video_title']) ?>">
                        </div>
                        <div class="form-group col-md-12">
                            <label><?php esc_html_e('Video URL', 'civi-framework') ?></label>
                            <input type="text" name="candidate_video_url[]" cols="30" rows="7" placeholder="<?php esc_attr_e('Video URL', 'civi-framework'); ?>" value="<?php echo esc_attr($candidate_video[CIVI_METABOX_PREFIX . 'candidate_video_url']) ?>">
                        </div>
                    </div>
            <?php endforeach; ?>	
			<?php } else {  ?>
					<?php $index = 0; ?>
					 <div class="row">
                        <div class="group-title col-md-12">
                            <i class="delete-group fas fa-times"></i>
                            <h6 class="project">
                                <?php esc_html_e('Videos', 'civi-framework') ?>
                                <span><?php echo $index + 1 ?></span>
                            </h6>
                            <i class="fas fa-angle-up"></i>
                        </div>
                        <div class="form-group col-md-6">
                            <label><?php esc_html_e('Title', 'civi-framework') ?></label>
                            <input type="text" name="candidate_video_title[]" placeholder="<?php esc_attr_e('Video Title', 'civi-framework'); ?>" value="">
                        </div>
                        <div class="form-group col-md-12">
                            <label><?php esc_html_e('Video URL', 'civi-framework') ?></label>
                            <input type="text" name="candidate_video_url[]" cols="30" rows="7" placeholder="<?php esc_attr_e('Video URL', 'civi-framework'); ?>" value="">
                        </div>
                    </div>
			<?php } ?>
            <button type="button" class="btn-more profile-fields"><i class="far fa-angle-down"></i><?php esc_html_e('Add another Video', 'civi-framework') ?></button>

            <template id="template-item-award" data-size="<?php echo esc_attr($candidate_video_quantity) ?>">
                <div class="row">
                    <div class="group-title col-md-12">
                        <i class="delete-group fas fa-times"></i>
                        <h6 class="project">
                            <?php esc_html_e('Video', 'civi-framework') ?>
                            <span></span>
                        </h6>
                        <i class="fas fa-angle-up"></i>
                    </div>
                    <div class="form-group col-md-6">
                        <label><?php esc_html_e('Video Title', 'civi-framework') ?></label>
                        <input type="text" name="candidate_video_title[]" placeholder="<?php esc_attr_e('Video Title', 'civi-framework'); ?>" value="">
                    </div>
                    <div class="form-group col-md-12">
                        <label><?php esc_html_e('Video URL', 'civi-framework') ?></label>
                        <input type="text" name="candidate_video_url[]" cols="30" rows="7" placeholder="<?php esc_attr_e('Video URL', 'civi-framework'); ?>">
                    </div>
                </div>
            </template>
        </div>
    </div>
