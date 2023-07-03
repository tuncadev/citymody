<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$candidate_id = get_the_ID();
$candidate_experiences = get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_experience_list', false);
$candidate_experiences = !empty($candidate_experiences) ? $candidate_experiences[0] : '';
if (empty($candidate_experiences[0][CIVI_METABOX_PREFIX . 'candidate_experience_job'])) {
    return;
}
?>
<div class="block-archive-inner candidate-single-field">
    <h4 class="title-candidate"><?php esc_html_e('Work Experience', 'civi-framework') ?></h4>
    <?php foreach ($candidate_experiences as $experience) : ?>
        <?php if (!empty($experience[CIVI_METABOX_PREFIX . 'candidate_experience_job'])) : ?>
            <div class="single candidate-experience">
                <?php if (!empty($experience[CIVI_METABOX_PREFIX . 'candidate_experience_job'])) : ?>
                    <div class="experience-title time-dot">
                        <?php echo $experience[CIVI_METABOX_PREFIX . 'candidate_experience_job']; ?>
                    </div>
                <?php endif; ?>
                <div class="experience-details time-line">
                    <?php if (!empty($experience[CIVI_METABOX_PREFIX . 'candidate_experience_company'])) : ?>
                        <span><?php echo $experience[CIVI_METABOX_PREFIX . 'candidate_experience_company']; ?></span>
                    <?php endif; ?>
                    <?php if (!empty($experience[CIVI_METABOX_PREFIX . 'candidate_experience_from'])) : ?>
                        <span><?php echo $experience[CIVI_METABOX_PREFIX . 'candidate_experience_from']; ?></span>
                    <?php endif; ?>
                    <span>-</span>
                    <?php if (!empty($experience[CIVI_METABOX_PREFIX . 'candidate_experience_to'])) : ?>
                        <span><?php echo $experience[CIVI_METABOX_PREFIX . 'candidate_experience_to']; ?></span>
                    <?php endif; ?>
                    <?php if (!empty($experience[CIVI_METABOX_PREFIX . 'candidate_experience_description'])) : ?>
                        <span><?php echo $experience[CIVI_METABOX_PREFIX . 'candidate_experience_description']; ?></span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
    <?php civi_custom_field_single_candidate('experience'); ?>
</div>