<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $current_user;

$candidate_id = get_the_ID();
$candidate_location = get_the_terms($candidate_id, 'candidate_locations');
$candidate_categories = get_the_terms($candidate_id, 'candidate_categories');
$candidate_resume = wp_get_attachment_url(get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_resume_id_list', true));
$candidate_featured = get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_featured', true);
$candidate_current_position = get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_current_position', true);
$author_id = get_post_field('post_author', $candidate_id);
$candidate_avatar = get_the_author_meta('author_avatar_image_url', $author_id);
$candidate_website = get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_website', true);
$offer_salary = !empty(get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_offer_salary')) ? get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_offer_salary')[0] : '';
?>
<div class="block-archive-inner candidate-head-details">
    <div class="civi-candidate-header-top">
        <?php if (!empty($candidate_avatar)) : ?>
            <img class="image-candidates" src="<?php echo esc_attr($candidate_avatar) ?>" alt=""/>
        <?php else : ?>
            <div class="image-candidates"><i class="far fa-camera"></i></div>
        <?php endif; ?>
        <div class="info">
            <div class="title-wapper">
                <?php if (!empty(get_the_title())) : ?>
                    <h1><?php echo get_the_title(); ?></h1>
                    <?php if ($candidate_featured == 1) : ?>
                        <span class="tooltip" data-title="<?php echo esc_attr('Featured', 'civi-framework') ?>"><i
                                    class="fas fa-check"></i></span>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <div class="candidate-info">
                <?php if (!empty($candidate_current_position)) { ?>
                    <div class="candidate-current-position">
                        <?php esc_html_e($candidate_current_position); ?>
                    </div>
                <?php } ?>
                <?php if (is_array($candidate_location)) { ?>
                    <div class="candidate-warpper">
                        <i class="fas fa-map-marker-alt"></i>
                        <?php foreach ($candidate_location as $location) {
                            $cate_link = get_term_link($location, 'candidate_locations'); ?>
                            <div class="cate-warpper">
                                <a href="<?php echo esc_url($cate_link); ?>" class="cate civi-link-bottom">
                                    <?php echo $location->name; ?>
                                </a>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
                <?php if (!empty($offer_salary)) { ?>
                    <div class="candidate-warpper salary">
                        <i class="fal fa-usd-circle"></i>
                        <?php civi_get_salary_candidate($candidate_id); ?>
                    </div>
                <?php } ?>
                <?php echo civi_get_total_rating('candidate', $candidate_id); ?>
            </div>
        </div>
    </div>
    <div class="civi-candidate-header-bottom">
        <?php civi_get_template('candidate/follow.php', array(
            'candidate_id' => $candidate_id,
        )); ?>
        <?php if (!empty($candidate_resume) && is_user_logged_in()) { ?>
            <a href="<?php echo $candidate_resume ?>" class="civi-button button-outline">
                <i class="fas fa-download"></i>
                <?php esc_html_e('Download CV', 'civi-framework') ?>
            </a>
        <?php } ?>
        <?php if (is_user_logged_in() && in_array('civi_user_employer', (array)$current_user->roles)) { ?>
            <a href="#" class="civi-button"
               id="btn-invite-candidate"><?php esc_html_e('Invite', 'civi-framework') ?></a>
        <?php } else { ?>
            <div class="logged-out">
                <a href="#popup-form" class="civi-button btn-login notice-employer"
                   data-notice="<?php esc_attr_e('Please login role Employer to view', 'civi-framework') ?>">
                    <?php esc_html_e('Invite', 'civi-framework') ?>
                </a>
            </div>
        <?php } ?>
        <?php civi_get_template('candidate/messages.php', array(
            'candidate_id' => $candidate_id,
        )); ?>
    </div>
    <?php civi_custom_field_single_candidate('info'); ?>
</div>
