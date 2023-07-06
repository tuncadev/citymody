<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$candidate_id = get_the_ID();
$candidate_project = get_post_meta($candidate_id, CIVI_METABOX_PREFIX . 'candidate_project_list', false);
$candidate_project = !empty($candidate_project) ? $candidate_project[0] : '';
if (empty($candidate_project[0][CIVI_METABOX_PREFIX . 'candidate_project_image_id']['url'])) {
    return;
}
$show = 2;
?>

<div class="block-archive-inner candidate-project-details">
    <h4 class="title-candidate"><?php esc_html_e('Projects', 'civi-framework') ?></h4>
    <div class="entry-candidate-element">
        <div class="single-candidate-thumbs enable">
            <?php
            $slick_attributes = array(
                '"slidesToShow": ' . $show,
                '"slidesToScroll": 1',
                '"dots": true',
                '"autoplay": false',
                '"autoplaySpeed": 5000',
                '"responsive": [{ "breakpoint": 479, "settings": {"slidesToShow": 1} },{ "breakpoint": 768, "settings": {"slidesToShow": 2}} ]'
            );
            $wrapper_attributes[] = "data-slick='{" . implode(', ', $slick_attributes) . "}'";
            ?>
            <div class="civi-slick-carousel slick-nav" <?php echo implode(' ', $wrapper_attributes); ?>>
                <?php
                foreach ($candidate_project as $project) :
                    $thumb_src = $project[CIVI_METABOX_PREFIX . 'candidate_project_image_id']['url'];
                    if (!empty($project[CIVI_METABOX_PREFIX . 'candidate_project_link'])) {
                        $project_link = $project[CIVI_METABOX_PREFIX . 'candidate_project_link'];
                    } else {
                        $project_link = '#';
                    }
                    ?>
                    <?php if (!empty($project[CIVI_METABOX_PREFIX . 'candidate_project_image_id']['url'])) : ?>
                    <figure>
                        <a href="<?php echo esc_url($project_link); ?>" target="_blank" class="project">
                            <img src="<?php echo esc_url($thumb_src); ?>" alt="<?php the_title_attribute(); ?>"
                                 title="<?php the_title_attribute(); ?>">
                            <div class="content-project">
                                <?php if (!empty($project[CIVI_METABOX_PREFIX . 'candidate_project_title'])) : ?>
                                    <h4><?php echo $project[CIVI_METABOX_PREFIX . 'candidate_project_title']; ?></h4>
                                <?php endif; ?>
                                <div class="project-inner">
                                    <?php if (!empty($project[CIVI_METABOX_PREFIX . 'candidate_project_description'])) : ?>
                                        <p><?php echo $project[CIVI_METABOX_PREFIX . 'candidate_project_description']; ?></p>
                                    <?php endif; ?>
                                    <?php if (!empty($project[CIVI_METABOX_PREFIX . 'candidate_project_title'])) : ?>
                                        <span class="civi-button button-border-bottom"><?php esc_html_e('View Project', 'civi-framework') ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                    </figure>
                <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php civi_custom_field_single_candidate('projects'); ?>
</div>