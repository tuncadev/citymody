<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
wp_enqueue_style('lity');
wp_enqueue_script('lity');
$candidate_id = get_the_ID();

$candidate_meta_data = get_post_custom($candidate_id);

$candidate_video_url   = isset($candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_video_url']) ? $candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_video_url'][0] : '';
$candidate_video_image = isset($candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_video_image']) ? $candidate_meta_data[CIVI_METABOX_PREFIX . 'candidate_video_image'][0] : '';
?>
<?php if (!empty($candidate_video_url)) : ?>
    <div class="block-archive-inner candidate-video-details">
        <h4 class="title-candidate"><?php esc_html_e('Video', 'civi-framework') ?></h4>
        <div class="entry-candidate-element">
            <div class="entry-thumb-wrap">
                <?php if (wp_oembed_get($candidate_video_url)) : ?>
                    <?php
                    $image_src = civi_image_resize_id($candidate_video_image, 870, 420, true);
                    $width = '870';
                    $height = '420';
                    if (!empty($image_src)) : ?>
                        <div class="entry-thumbnail">
                            <img class="img-responsive" src="<?php echo esc_url($image_src); ?>" width="<?php echo esc_attr($width) ?>" height="<?php echo esc_attr($height) ?>" alt="<?php the_title_attribute(); ?>" />
                            <a class="view-video" href="<?php echo esc_url($candidate_video_url); ?>" data-lity><i class="far fa-play-circle icon-large"></i></a>
                        </div>
                    <?php else : ?>
                        <div class="embed-responsive embed-responsive-16by9 embed-responsive-full">
                            <?php echo wp_oembed_get($candidate_video_url, array('wmode' => 'transparent')); ?>
                        </div>
                    <?php endif; ?>
                <?php else : ?>
                    <div class="embed-responsive embed-responsive-16by9 embed-responsive-full">
                        <?php echo wp_kses_post($candidate_video_url); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?>