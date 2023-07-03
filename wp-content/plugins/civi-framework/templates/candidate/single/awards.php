<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$candidate_id = get_the_ID();
$candidate_awards = get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_award_list', false);
$candidate_awards = !empty($candidate_awards) ? $candidate_awards[0] : '';
if (empty($candidate_awards[0][CIVI_METABOX_PREFIX . 'candidate_award_title'])) {
    return;
}
?>

<div class="block-archive-inner candidate-single-field">
    <h4 class="title-candidate"><?php esc_html_e('Honors & awards', 'civi-framework') ?></h4>
    <?php foreach ($candidate_awards as $award) : ?>
        <?php if (!empty($award[CIVI_METABOX_PREFIX . 'candidate_award_title'])) : ?>
            <div class="single candidate-award">
                <?php if (!empty($award[CIVI_METABOX_PREFIX . 'candidate_award_title'])) : ?>
                    <div class="award-title time-dot">
                        <?php echo $award[CIVI_METABOX_PREFIX . 'candidate_award_title']; ?>
                    </div>
                <?php endif; ?>
                <div class="award-details time-line">
                    <?php if (!empty($award[CIVI_METABOX_PREFIX . 'candidate_award_date'])) : ?>
                        <span><?php echo $award[CIVI_METABOX_PREFIX . 'candidate_award_date']; ?></span>
                    <?php endif; ?>
                    <?php if (!empty($award[CIVI_METABOX_PREFIX . 'candidate_award_description'])) : ?>
                        <span><?php echo $award[CIVI_METABOX_PREFIX . 'candidate_award_description']; ?></span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
    <?php civi_custom_field_single_candidate('awards'); ?>
</div>