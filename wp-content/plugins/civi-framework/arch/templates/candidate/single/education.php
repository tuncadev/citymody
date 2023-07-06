<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$candidate_id = get_the_ID();
$candidate_educations = get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_education_list', false);
$candidate_educations = !empty($candidate_educations) ? $candidate_educations[0] : '';
if (empty($candidate_educations[0][CIVI_METABOX_PREFIX . 'candidate_education_title'])) {
    return;
}
?>

<div class="block-archive-inner candidate-single-field">
    <h4 class="title-candidate"><?php esc_html_e('Education', 'civi-framework') ?></h4>
    <?php foreach ($candidate_educations as $education) : ?>
        <?php if (!empty($education[CIVI_METABOX_PREFIX . 'candidate_education_title'])) : ?>
            <div class="single candidate-education">
                <?php if (!empty($education[CIVI_METABOX_PREFIX . 'candidate_education_title'])) : ?>
                    <div class="education-title time-dot">
                        <?php echo $education[CIVI_METABOX_PREFIX . 'candidate_education_title']; ?>
                    </div>
                <?php endif; ?>
                <div class="education-details time-line">
                    <?php if (!empty($education[CIVI_METABOX_PREFIX . 'candidate_education_level'])) : ?>
                        <span><?php echo $education[CIVI_METABOX_PREFIX . 'candidate_education_level']; ?></span>
                    <?php endif; ?>
                    <?php if (!empty($education[CIVI_METABOX_PREFIX . 'candidate_education_from'])) : ?>
                        <span><?php echo $education[CIVI_METABOX_PREFIX . 'candidate_education_from']; ?></span>
                    <?php endif; ?>
                    <span>-</span>
                    <?php if (!empty($education[CIVI_METABOX_PREFIX . 'candidate_education_to'])) : ?>
                        <span><?php echo $education[CIVI_METABOX_PREFIX . 'candidate_education_to']; ?></span>
                    <?php endif; ?>
                    <?php if (!empty($education[CIVI_METABOX_PREFIX . 'candidate_education_description'])) : ?>
                        <span><?php echo $education[CIVI_METABOX_PREFIX . 'candidate_education_description']; ?></span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
    <?php civi_custom_field_single_candidate('education'); ?>
</div>