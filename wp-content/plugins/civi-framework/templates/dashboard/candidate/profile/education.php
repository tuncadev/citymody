<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $candidate_data;
$candidate_education_list = get_post_meta($candidate_data->ID, CIVI_METABOX_PREFIX . 'candidate_education_list', false);
$candidate_education_list = !empty($candidate_education_list) ? $candidate_education_list[0] : '';
$candidate_education_quantity = !empty($candidate_education_list) ?  count($candidate_education_list) : '';
?>

<div id="tab-education" class="tab-info">
    <div class="education-info block-from">
        <h5 class="education"><?php esc_html_e('Education', 'civi-framework') ?></h5>

        <div class="sub-head"><?php esc_html_e('We recommend at least one education entry.', 'civi-framework') ?></div>

        <div class="civi-candidate-warpper">
            <?php if (!empty($candidate_education_list)) :

                foreach ($candidate_education_list as $index => $candidate_education) :
            ?><?php echo "silence is golder"; ?>
                    <div class="row">
                        <div class="group-title col-md-12">
                            <i class="delete-group fas fa-times"></i>
                            <h6 class="education">
                                <?php echo esc_html_e('Education', 'civi-framework') ?>
                                <span><?php echo $index + 1 ?></span>
                            </h6>
                            <i class="fas fa-angle-up"></i>
                        </div>
                        <div class="form-group col-md-6">
                            <label><?php esc_html_e('Title', 'civi-framework') ?></label>
                            <input type="text" name="candidate_education_title[]" placeholder="<?php esc_attr_e('Enter Title', 'civi-framework'); ?>" value="<?php echo esc_attr($candidate_education[CIVI_METABOX_PREFIX . 'candidate_education_title']) ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label><?php esc_html_e('Level of Education', 'civi-framework') ?></label>
                            <input type="text" name="candidate_education_level[]" placeholder="<?php esc_attr_e('Enter Level', 'civi-framework'); ?>" value="<?php echo esc_attr($candidate_education[CIVI_METABOX_PREFIX . 'candidate_education_level']) ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label><?php esc_html_e('From', 'civi-framework') ?></label>
                            <input type="date" name="candidate_education_from[]" value="<?php echo esc_attr($candidate_education[CIVI_METABOX_PREFIX . 'candidate_education_from']) ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label><?php esc_html_e('To', 'civi-framework') ?></label>
                            <input type="date" name="candidate_education_to[]" value="<?php echo esc_attr($candidate_education[CIVI_METABOX_PREFIX . 'candidate_education_to']) ?>">
                        </div>

                        <div class="form-group col-md-12">
                            <label><?php esc_html_e('Description', 'civi-framework') ?></label>
                            <textarea name="candidate_education_description[]" cols="30" placeholder="<?php esc_attr_e('Short description', 'civi-framework'); ?>" rows="7"><?php echo esc_attr($candidate_education[CIVI_METABOX_PREFIX . 'candidate_education_description']) ?></textarea>
                        </div>
                    </div>
            <?php endforeach;
            endif;
            ?>
            <button type="button" class="btn-more profile-fields"><i class="far fa-angle-down"></i><?php esc_html_e('Add another education', 'civi-framework') ?></button>
            <template id="template-item-education" data-size="<?php echo esc_attr($candidate_education_quantity) ?>">
                <div class="row">
                    <div class="group-title col-md-12">
                        <i class="delete-group fas fa-times"></i>
                        <h6 class="education">
                            <?php echo esc_html_e('Education', 'civi-framework') ?>
                            <span></span>
                        </h6>
                        <i class="fas fa-angle-up"></i>
                    </div>
                    <div class="form-group col-md-6">
                        <label><?php esc_html_e('Title', 'civi-framework') ?></label>
                        <input type="text" name="candidate_education_title[]" value="" placeholder="<?php esc_attr_e('Enter Title', 'civi-framework'); ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label><?php esc_html_e('Level of Education', 'civi-framework') ?></label>
                        <input type="text" name="candidate_education_level[]" value="" placeholder="<?php esc_attr_e('Enter Level', 'civi-framework'); ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label><?php esc_html_e('From', 'civi-framework') ?></label>
                        <input type="date" name="candidate_education_from[]" value="">
                    </div>
                    <div class="form-group col-md-6">
                        <label><?php esc_html_e('To', 'civi-framework') ?></label>
                        <input type="date" name="candidate_education_to[]" value="">
                    </div>

                    <div class="form-group col-md-12">
                        <label><?php esc_html_e('Description', 'civi-framework') ?></label>
                        <textarea name="candidate_education_description[]" cols="30" rows="7" placeholder="<?php esc_attr_e('Short description', 'civi-framework'); ?>"></textarea>
                    </div>
                </div>
            </template>
        </div>
    </div>
    <?php civi_custom_field_candidate('education'); ?>
</div>