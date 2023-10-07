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

/* header */
$author_id = get_post_field('post_author', $candidate_id);
$candidate_skills = get_the_terms($candidate_id, 'candidate_skills');
$candidate_current_position = get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_current_position', true);
$candidate_avatar = get_the_author_meta('author_avatar_image_url', $author_id);
$candidate_first_name = get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_first_name', true);
$candidate_last_name = get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_last_name', true);
$candidate_featured = get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_featured', true);
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
<div class="candidate-sidebar <?php echo implode(" ", $classes); ?>">
	<div class="block-archive-inner candidate-head-details candidate-single-field sidebaravatar">
    <div class="civi-candidate-header-top">
			<?php if (!empty($candidate_avatar)) : ?>
				<img class="image-candidates" src="<?php echo esc_attr($candidate_avatar) ?>" alt=""/>
			<?php else : ?>
			<div class="image-candidates"><i class="far fa-camera"></i></div>
			<?php endif; ?>
			<?php if (!empty(get_the_title())) : ?>					
				<h1><?php echo $candidate_first_name . " " .$candidate_last_name ; ?></h1>
				<?php if ($candidate_featured == 1) : ?>
					<span class="tooltip" data-title="<?php echo esc_attr('Featured', 'civi-framework') ?>">
						<i class="fas fa-check"></i>
					</span>
				<?php endif; ?>
			<?php endif; ?>
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
			</div>
			<div class="wishlist_wrapper">
				<?php civi_get_template('candidate/follow.php', array('candidate_id' => $candidate_id,)); ?>
			</div>
		</div>
	</div>
	<?php 
		if ($candidate_skills == false || is_wp_error($candidate_skills)) {
    	return;
		}
	?>
	<div class="block-archive-inner candidate-single-field skills-aboutme sidebarskills">
    <h4 class="title-candidate"><?php esc_html_e('Skills', 'civi-framework') ?></h4>
    <div class="candidate-skills">
			<?php foreach ($candidate_skills as $skill) {
				$skill_link = get_term_link($skill, 'candidate_skills'); ?>
				<a href="<?php echo esc_url($skill_link); ?>" class="label label-skills">
					<?php esc_html_e($skill->name); ?>
				</a>
			<?php } ?>
    </div>
    <?php civi_custom_field_single_candidate('skills'); ?>
	</div>
	<!-- No Info 
    <?php // if ($candidate_phone) : ?>
        <div class="info">
            <p class="title-info"><?php // esc_html_e('Phone', 'civi-framework'); ?></p>
            <p class="details-info"><a href="tel:<?php // esc_attr_e($candidate_phone); ?>"><?php // esc_attr_e($candidate_phone); ?></a></p>
        </div>
    <?php // endif; ?>
    <?php // if ($candidate_email) : ?>
        <div class="info">
            <p class="title-info"><?php //esc_html_e('Email', 'civi-framework'); ?></p>
            <p class="details-info email"><a href="mailto:<?php // esc_attr_e($candidate_email) ?>"><?php // esc_attr_e($candidate_email); ?></a></p>
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