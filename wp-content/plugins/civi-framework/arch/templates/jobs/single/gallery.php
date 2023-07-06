<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
wp_enqueue_style('lightgallery');
wp_enqueue_script('lightgallery');

$id = get_the_ID();
$jobs_gallery     = get_post_meta(get_the_ID(), CIVI_METABOX_PREFIX . 'jobs_images', true);
$attach_id         = get_post_thumbnail_id();
$show = 3;

?>
<?php if (!empty($jobs_gallery)) : ?>
    <div class="block-archive-inner jobs-gallery-details">
        <h4 class="title-jobs"><?php esc_html_e('Photos', 'civi-framework') ?></h4>
        <div class="entry-jobs-element">
            <div class="single-jobs-thumbs enable">
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
                    $obj_jobs_gallery = explode('|', $jobs_gallery);
                    $count = count($obj_jobs_gallery);
                    foreach ($obj_jobs_gallery as $key => $image) :
                        if ($image) {
                            $image_full_src = wp_get_attachment_image_src($image, 'full');
                            if (isset($image_full_src[0])) {
                                $thumb_src      = $image_full_src[0];
                            }
                        }

                        if (!empty($thumb_src)) {
                    ?>
                            <figure>
                                <a href="<?php echo esc_url($thumb_src); ?>" data-elementor-open-lightbox="yes" data-elementor-lightbox-slideshow="lgslider" class="lgbox">
                                    <img src="<?php echo esc_url($thumb_src); ?>" alt="<?php the_title_attribute(); ?>" title="<?php the_title_attribute(); ?>">
                                </a>
                            </figure>
                        <?php } ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>