<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
wp_enqueue_style('lity');
wp_enqueue_script('lity');
$company_id = get_the_ID();
$company_meta_data = get_post_custom($company_id);

$company_video_url   = isset($company_meta_data[CIVI_METABOX_PREFIX . 'company_video_url']) ? $company_meta_data[CIVI_METABOX_PREFIX . 'company_video_url'][0] : '';
$company_video_image = isset($company_meta_data[CIVI_METABOX_PREFIX . 'company_video_image']) ? $company_meta_data[CIVI_METABOX_PREFIX . 'company_video_image'][0] : '';
?>
<?php if (!empty($company_video_url)) : ?>
    <div class="block-archive-inner company-video-details">
        <h4 class="title-company"><?php esc_html_e('Video', 'civi-framework') ?></h4>
        <div class="entry-company-element">
            <div class="entry-thumb-wrap">
                <?php if (wp_oembed_get($company_video_url)) : ?>
                    <?php
                    $image_src = civi_image_resize_id($company_video_image, 870, 420, true);
                    $width = '870';
                    $height = '420';
                    if (!empty($image_src)) : ?>
                        <div class="entry-thumbnail">
                            <img class="img-responsive" src="<?php echo esc_url($image_src); ?>" width="<?php echo esc_attr($width) ?>" height="<?php echo esc_attr($height) ?>" alt="<?php the_title_attribute(); ?>" />
                            <a class="view-video" href="<?php echo esc_url($company_video_url); ?>" data-lity><i class="far fa-play-circle icon-large"></i></a>
                        </div>
                    <?php else : ?>
                        <div class="embed-responsive embed-responsive-16by9 embed-responsive-full">
                            <?php echo wp_oembed_get($company_video_url, array('wmode' => 'transparent')); ?>
                        </div>
                    <?php endif; ?>
                <?php else : ?>
                    <div class="embed-responsive embed-responsive-16by9 embed-responsive-full">
                        <?php echo wp_kses_post($company_video_url); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?>