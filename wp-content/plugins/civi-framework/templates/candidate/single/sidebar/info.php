<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$candidate_id              = get_the_ID();
$candidate_salary          = !empty(get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_offer_salary')) ? get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_offer_salary')[0] : '';
$candidate_yoe             = get_the_terms($candidate_id, 'candidate_yoe');
$candidate_languages       = get_the_terms($candidate_id, 'candidate_languages');
$candidate_location        = get_the_terms($candidate_id, 'candidate_locations');
$candidate_gender          = get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_gender', true);
$candidate_qualification   = get_the_terms($candidate_id, 'candidate_qualification');
$candidate_ages            = get_the_terms($candidate_id, 'candidate_ages');
$candidate_phone           = get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_phone', true);
$candidate_email           = get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_email', true);
$candidate_twitter         = get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_twitter', true);
$candidate_facebook        = get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_facebook', true);
$candidate_instagram       = get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_instagram', true);
$candidate_linkedin        = get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_linkedin', true);

$enable_social_twitter     = civi_get_option('enable_social_twitter', '1');
$enable_social_linkedin    = civi_get_option('enable_social_linkedin', '1');
$enable_social_facebook    = civi_get_option('enable_social_facebook', '1');
$enable_social_instagram   = civi_get_option('enable_social_instagram', '1');

$option_list_gender = array(
    'both' => esc_html__('Both', 'civi-framework'),
    'female' => esc_html__('Female', 'civi-framework'),
    'male' => esc_html__('Male', 'civi-framework'),
);

$classes = array();
$enable_sticky_sidebar_type = civi_get_option('enable_sticky_candidate_sidebar_type', 1);
if ($enable_sticky_sidebar_type) {
    $classes[] = 'has-sticky';
};
?>
<div class="candidate-sidebar block-archive-sidebar candidate-sidebar <?php echo implode(" ", $classes); ?>">
    <h3 class="title-candidate"><?php esc_html_e('Information', 'civi-framework'); ?></h3>
		<hr>
    <?php if (!empty($candidate_salary)) : ?>
        <div class="info">
            <p class="title-info"><?php esc_html_e('Offered Salary', 'civi-framework'); ?></p>
            <div class="details-info salary">
                <?php civi_get_salary_candidate($candidate_id); ?>
            </div>
        </div>
    <?php endif; ?>
    <?php if (is_array($candidate_yoe)) : ?>
        <div class="info">
            <p class="title-info"><?php esc_html_e('Experience time', 'civi-framework'); ?></p>
            <div class="list-cate">
                <?php foreach ($candidate_yoe as $yoe) {
                    $yoe_link = get_term_link($yoe, 'candidate_yoe'); ?>
                    <a href="<?php echo esc_url($yoe_link); ?>">
                        <?php esc_attr_e($yoe->name); ?>
                    </a>
                <?php } ?>
            </div>
        </div>
    <?php endif; ?>
    <?php if (is_array($candidate_languages)) : ?>
        <div class="info">
            <p class="title-info"><?php esc_html_e('Native Language', 'civi-framework'); ?></p>
            <div class="list-cate">
                <?php foreach ($candidate_languages as $language) {
                    esc_attr_e($language->name);
                } ?>
            </div>
        </div>
    <?php endif; ?>
    <?php if (!empty($candidate_gender)) : ?>
        <div class="info">
            <p class="title-info"><?php esc_html_e('Gender', 'civi-framework'); ?></p>
            <p class="details-info"><?php esc_attr_e($option_list_gender[$candidate_gender]) ?></p>
        </div>
    <?php endif; ?>
    <?php if (is_array($candidate_qualification)) : ?>
        <div class="info">
            <p class="title-info"><?php esc_html_e('Qualification', 'civi-framework'); ?></p>
            <div class="list-cate">
                <?php foreach ($candidate_qualification as $qualification) {
                    echo esc_attr_e($qualification->name);
                } ?>
            </div>
        </div>
    <?php endif; ?>
    <?php if (is_array($candidate_ages)) : ?>
        <div class="info">
            <p class="title-info"><?php esc_html_e('Age', 'civi-framework'); ?></p>
            <div class="list-cate">
                <?php foreach ($candidate_ages as $ages) {
                    echo esc_attr_e($ages->name);
                } ?>
            </div>
        </div>
    <?php endif; ?>
	<!-- No Info 
    <?php // if ($candidate_phone) : ?>
        <div class="info">
            <p class="title-info"><?php esc_html_e('Phone', 'civi-framework'); ?></p>
            <p class="details-info"><a href="tel:<?php esc_attr_e($candidate_phone); ?>"><?php esc_attr_e($candidate_phone); ?></a></p>
        </div>
    <?php // endif; ?>
    <?php // if ($candidate_email) : ?>
        <div class="info">
            <p class="title-info"><?php esc_html_e('Email', 'civi-framework'); ?></p>
            <p class="details-info email"><a href="mailto:<?php esc_attr_e($candidate_email) ?>"><?php esc_attr_e($candidate_email); ?></a></p>
        </div>
    <?php // endif; ?>
	-->
	<!-- 
    <ul class="list-social">
        <?php // if (!empty($candidate_facebook[0]) && $enable_social_facebook == 1) : ?>
            <li><a href="<?php //  echo $candidate_facebook[0]; ?>"><i class="fab fa-facebook-f"></i></a></li>
        <?php //  endif; ?>
        <?php //  if (!empty($candidate_twitter[0]) && $enable_social_twitter == 1) : ?>
            <li><a href="<?php //  echo $candidate_twitter[0]; ?>"><i class="fab fa-twitter"></i></a></li>
        <?php //  endif; ?>
        <?php //  if (!empty($candidate_linkedin[0]) && $enable_social_linkedin == 1) : ?>
            <li><a href="<?php //  echo $candidate_linkedin[0]; ?>"><i class="fab fa-linkedin"></i></a></li>
        <?php //  endif; ?>
        <?php //  if (!empty($candidate_instagram[0]) && $enable_social_instagram == 1) : ?>
            <li><a href="<?php //  echo $candidate_instagram[0]; ?>"><i class="fab fa-instagram"></i></a></li>
        <?php //  endif; ?>
        <?php //  civi_get_social_network($candidate_id,'candidate'); ?>
    </ul>
	-->
</div>