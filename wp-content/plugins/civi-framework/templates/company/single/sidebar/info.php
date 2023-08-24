<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$company_id = get_the_ID();
$company_logo   = get_post_meta($company_id, CIVI_METABOX_PREFIX . 'company_logo');
$company_categories =  get_the_terms($company_id, 'company-categories');
$company_size =  get_the_terms($company_id,  'company-size');
$company_location =  get_the_terms($company_id, 'company-location');
$enable_social_twitter = civi_get_option('enable_social_twitter', '1');
$enable_social_linkedin = civi_get_option('enable_social_linkedin', '1');
$enable_social_facebook = civi_get_option('enable_social_facebook', '1');
$enable_social_instagram = civi_get_option('enable_social_instagram', '1');
$company_founded =  get_post_meta($company_id, CIVI_METABOX_PREFIX . 'company_founded');
$company_phone =  get_post_meta($company_id, CIVI_METABOX_PREFIX . 'company_phone');
$company_email =  get_post_meta($company_id, CIVI_METABOX_PREFIX . 'company_email');
$company_website =  get_post_meta($company_id, CIVI_METABOX_PREFIX . 'company_website');
$company_twitter   = get_post_meta($company_id, CIVI_METABOX_PREFIX . 'company_twitter');
$company_facebook   = get_post_meta($company_id, CIVI_METABOX_PREFIX . 'company_facebook');
$company_instagram   = get_post_meta($company_id, CIVI_METABOX_PREFIX . 'company_instagram');
$company_linkedin   = get_post_meta($company_id, CIVI_METABOX_PREFIX . 'company_linkedin');

$classes = array();
$enable_sticky_sidebar_type = civi_get_option('enable_sticky_company_sidebar_type', 1);
if ($enable_sticky_sidebar_type) {
    $classes[] = 'has-sticky';
};

?>
<?php if ($company_id !== '') : ?>
    <div class="jobs-company-sidebar block-archive-sidebar company-sidebar <?php echo implode(" ", $classes); ?>">
        <h3 class="title-company"><?php esc_html_e('Information', 'civi-framework'); ?></h3>
				<hr>
        <?php if (is_array($company_categories)) : ?>
            <div class="info">
                <p class="title-info"><?php esc_html_e('Categories', 'civi-framework'); ?></p>
                <div class="list-cate">
                    <?php foreach ($company_categories as $categories) {
                        $cate_link = get_term_link($categories, 'jobs-categories'); ?>
                        <a href="<?php echo esc_url($cate_link); ?>" class="cate civi-link-bottom">
                            <?php echo $categories->name; ?>
                        </a>
                    <?php } ?>
                </div>
            </div>
        <?php endif; ?>
        <?php if (is_array($company_size)) : ?>
            <div class="info">
                <p class="title-info"><?php esc_html_e('Company size', 'civi-framework'); ?></p>
                <div class="list-cate">
                    <?php foreach ($company_size as $size) {
                        echo $size->name;
                    } ?>
                </div>
            </div>
        <?php endif; ?>
        <?php if (!empty($company_founded[0])) : ?>
            <div class="info">
                <p class="title-info"><?php esc_html_e('Founded in', 'civi-framework'); ?></p>
                <p class="details-info"><?php echo $company_founded[0]; ?></p>
            </div>
        <?php endif; ?>
        <?php if (is_array($company_location)) : ?>
            <div class="info">
                <p class="title-info"><?php esc_html_e('Location', 'civi-framework'); ?></p>
                <p class="details-info">
                    <?php foreach ($company_location as $location) { ?>
                        <span><?php echo $location->name; ?></span>
                    <?php } ?>
                </p>
            </div>
        <?php endif; ?>
        <?php if (!empty($company_phone[0])) : ?>
            <div class="info">
                <p class="title-info"><?php esc_html_e('Phone', 'civi-framework'); ?></p>
                <p class="details-info"><a href="tel:<?php echo $company_phone[0]; ?>"><?php echo $company_phone[0]; ?></a></p>
            </div>
        <?php endif; ?>
        <?php if (!empty($company_email[0])) : ?>
            <div class="info">
                <p class="title-info"><?php esc_html_e('Email', 'civi-framework'); ?></p>
                <p class="details-info email"><a href="mailto:<?php echo $company_email[0]; ?>"><?php echo $company_email[0]; ?></a></p>
            </div>
        <?php endif; ?>
        <ul class="list-social">
            <?php if (!empty($company_facebook[0]) && $enable_social_facebook == 1) : ?>
                <li><a href="<?php echo $company_facebook[0]; ?>"><i class="fab fa-facebook-f"></i></a></li>
            <?php endif; ?>
            <?php if (!empty($company_twitter[0]) && $enable_social_twitter == 1) : ?>
                <li><a href="<?php echo $company_twitter[0]; ?>"><i class="fab fa-twitter"></i></a></li>
            <?php endif; ?>
            <?php if (!empty($company_linkedin[0]) && $enable_social_linkedin == 1) : ?>
                <li><a href="<?php echo $company_linkedin[0]; ?>"><i class="fab fa-linkedin"></i></a></li>
            <?php endif; ?>
            <?php if (!empty($company_instagram[0]) && $enable_social_instagram == 1) : ?>
                <li><a href="<?php echo $company_instagram[0]; ?>"><i class="fab fa-instagram"></i></a></li>
            <?php endif; ?>
            <?php civi_get_social_network($company_id,'company'); ?>
        </ul>
    </div>
<?php endif; ?>