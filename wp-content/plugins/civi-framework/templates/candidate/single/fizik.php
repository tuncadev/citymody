<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$candidate_id = get_the_ID();
$candidate_fiziks = get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_fizik_list', false);
$candidate_fiziks = !empty($candidate_fizik) ? $candidate_fizik[0] : '';
if (empty($candidate_fiziks[0][CIVI_METABOX_PREFIX . 'candidate_fizik_title'])) {
    return;
}
?>

<div class="block-archive-inner candidate-single-field">
    <h4 class="title-candidate"><?php esc_html_e('Honors & awards', 'civi-framework') ?></h4>
    <?php foreach ($candidate_fiziks as $fizik) : ?>
        <?php if (!empty($fizik[CIVI_METABOX_PREFIX . 'candidate_fizik_title'])) : ?>
            <div class="single candidate-fizik">
                <?php if (!empty($fizik[CIVI_METABOX_PREFIX . 'candidate_fizik_title'])) : ?>
                    <div class="fizik-title time-dot">
                        <?php echo $fizik[CIVI_METABOX_PREFIX . 'candidate_fizik_title']; ?>
                    </div>
                <?php endif; ?>
                <div class="fizik-details time-line">
                    <?php if (!empty($fizik[CIVI_METABOX_PREFIX . 'candidate_fizik_date'])) : ?>
                        <span><?php echo $fizik[CIVI_METABOX_PREFIX . 'candidate_fizik_date']; ?></span>
                    <?php endif; ?>
                    <?php if (!empty($fizik[CIVI_METABOX_PREFIX . 'candidate_fizik_description'])) : ?>
                        <span><?php echo $fizik[CIVI_METABOX_PREFIX . 'candidate_fizik_description']; ?></span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
    <?php civi_custom_field_single_candidate('fizik'); ?>
</div>