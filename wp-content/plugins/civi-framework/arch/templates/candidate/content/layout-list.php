<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $wpdb;

$id = get_the_ID();
if (!empty($candidate_id)) {
    $id = $candidate_id;
}

$author_id = get_post_field('post_author', $candidate_id);
$candidate_avatar = get_the_author_meta('author_avatar_image_url', $author_id);
$candidate_featured = get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_featured', true);
$candidate_location   = get_the_terms($candidate_id, 'candidate_locations');
$candidate_categories = get_the_terms($candidate_id, 'candidate_categories');
$candidate_skills     = get_the_terms($candidate_id, 'candidate_skills');

$candidate_item_class[] = 'civi-candidates-item';

if (!empty($layout)) {
    $candidate_item_class[] = $layout;
}
$candidate_item_class[] = 'candidate-' . $id;
$enable_candidate_des = civi_get_option('enable_candidate_show_des');
?>
<div class="<?php echo join(' ', $candidate_item_class); ?>">
    <div class="candidate-header">
        <div class="candidates-header-right">
            <a class="candidate-img" href="<?php echo get_the_permalink($candidate_id); ?>">
                <?php if (!empty($candidate_avatar)) : ?>
                    <img class="candidate-avatar" src="<?php echo esc_attr($candidate_avatar) ?>" alt="" />
                <?php else : ?>
                    <div class="candidate-avatar"><i class="far fa-camera"></i></div>
                <?php endif; ?>
            </a>
            <div class="candidate-info">
                <?php if (!empty(get_the_title($candidate_id))) : ?>
                    <h2 class="candidates-title">
                        <a href="<?php echo get_the_permalink($candidate_id); ?>"><?php echo get_the_title($candidate_id); ?></a>
                        <?php if ($candidate_featured == 1) : ?>
                            <span class="tooltip" data-title="<?php echo esc_attr('Featured', 'civi-framework') ?>"><i class="fas fa-check"></i></span>
                        <?php endif; ?>
                    </h2>
                <?php endif; ?>
                <div class="candidate-inner">
                    <?php if (is_array($candidate_categories)) { ?>
                        <div class="candidate-category">
                            <?php foreach ($candidate_categories as $category) {
                                $category_link = get_term_link($category, 'candidate_categories'); ?>
                                <a href="<?php echo esc_url($category_link); ?>" class="cate civi-link-bottom">
                                    <?php esc_html_e($category->name); ?>
                                </a>
                            <?php } ?>
                        </div>
                    <?php } ?>
                    <?php if (is_array($candidate_location)) { ?>
                        <div class="candidate-location">
                            <?php foreach ($candidate_location as $location) {
                                $location_link = get_term_link($location, 'candidate-size'); ?>
                                <a href="<?php echo esc_url($location_link); ?>" class="cate civi-link-bottom">
                                    <i class="fas fa-map-marker-alt"></i><?php esc_html_e($location->name); ?>
                                </a>
                            <?php } ?>
                        </div>
                    <?php } ?>
                    <?php echo civi_get_total_rating('candidate', $candidate_id); ?>
                </div>
            </div>
        </div>
        <div class="candidate-header-left">
            <div class="candidate-status-inner">
                <?php civi_get_template('candidate/follow.php', array(
                    'candidate_id' => $candidate_id,
                )); ?>
            </div>
        </div>
    </div>
    <?php if (!empty(get_the_content($candidate_id)) && $enable_candidate_des) : ?>
        <div class="des-candidate">
            <?php echo wp_trim_words(get_the_content($candidate_id), 25); ?>
        </div>
    <?php endif; ?>
    <div class="candidate-bottom">
        <?php if (is_array($candidate_skills)) { ?>
            <div class="candidate-skills">
                <?php foreach ($candidate_skills as $skill) {
                    $skill_link = get_term_link($skill, 'candidate_skills'); ?>
                    <a href="<?php echo esc_url($skill_link); ?>" class="label label-skills">
                        <?php esc_html_e($skill->name); ?>
                    </a>
                <?php } ?>
            </div>
        <?php } ?>
        <?php civi_get_salary_candidate($candidate_id); ?>
    </div>
    <a class="civi-link-item" href="<?php echo get_post_permalink($candidate_id) ?>"></a>
</div>